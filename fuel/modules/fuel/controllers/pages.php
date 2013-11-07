<?php
require_once('module.php');

class Pages extends Module {
	
	private $_importing = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->module_model(FUEL_FOLDER, 'fuel_pagevariables_model');
	}

	public function create($field = NULL, $redirect = TRUE)
	{

		// check that the action even exists and if not, show a 404
		if (!$this->fuel->auth->module_has_action('save'))
		{
			show_404();
		}
		
		// check permissions
		if (!$this->fuel->auth->has_permission($this->module_obj->permission, 'create'))
		{
			show_error(lang('error_no_permissions'));
		}
		
		if (isset($_POST['id'])) // check for dupes
		{
			$posted = $this->_process();
			
			// set publish status to no if you do not have the ability to publish
			if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
			{
				$posted['published'] = 'no';
			}
			
			// reset dup id
			if ($_POST['id'] == 'dup')
			{
				$_POST['id'] = '';
				$_POST['location'] = $_POST['location'].'_copy';
			}
			else
			{
				// run before_create hook
				$this->_run_hook('before_create', $posted);

				// run before_save hook
				$this->_run_hook('before_save', $posted);

				if ($id = $this->model->save($posted))
				{

					if (empty($id) OR $this->model->get_errors())
					{
						show_error(lang('error_saving'));
					}
				
					$this->_process_uploads();
				
					if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
					{
						unset($_POST['published']);
					}
				
					if ($this->_save_page_vars($id, $posted))
					{
						$data = $this->model->find_one_array(array($this->model->table_name().'.id' => $id));
				
						// run after_create hook
						$this->_run_hook('after_create', $data);

						// run after_save hook
						$this->_run_hook('after_save', $data);

						if (!empty($data))
						{
							$msg = lang('module_created', $this->module_name, $data[$this->display_field]);
							$url = fuel_uri('pages/edit/'.$id);

							// save any tab states
							$this->_save_tab_state($id);

							if ($this->input->post('language'))
							{
								$url .= '?lang='.$this->input->post('language');
							}
							redirect($url);
						}
					}
					
				}
			}
			
		}
		$vars = $this->_form();
		$crumbs = array($this->module_uri => $this->module_name, '' => 'Create');
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->render('pages/page_create_edit', $vars);
	}

	public function edit($id = NULL, $field = NULL, $redirect = TRUE)
	{
		if (!$this->fuel->auth->module_has_action('save'))
		{
			show_404();
		}

		// check permissions
		if (!$this->fuel->auth->has_permission($this->module_obj->permission, 'edit') AND !$this->fuel->auth->has_permission($this->module_obj->permission, 'create'))
		{
			show_error(lang('error_no_permissions'));
		}

		if ($this->input->post('id'))
		{
			$posted = $this->_process();
			
			if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
			{
				unset($_POST['published']);
			}
			
			// run before_edit hook
			$this->_run_hook('before_edit', $posted);

			// run before_save hook
			$this->_run_hook('before_save', $posted);

			if ($this->model->save($posted))
			{
				$this->_process_uploads();
				
				if ($this->_save_page_vars($id, $posted))
				{
					$data = $this->model->find_one_array(array($this->model->table_name().'.id' => $id));
				
					// run after_edit hook
					$this->_run_hook('after_edit', $data);

					// run after_save hook
					$this->_run_hook('after_save', $data);

					
					$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
					$this->fuel->logs->write($msg);
					$url = fuel_uri('pages/edit/'.$id);
					if ($this->input->post('language'))
					{
						$url .= '?lang='.$this->input->post('language');
					}
					redirect($url);
				}
			}
		}
		
		$vars = $this->_form($id);
		$this->fuel->admin->render('pages/page_create_edit', $vars);
	}
	
	public function _form($id = NULL)
	{
		$this->load->library('form_builder');

		$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');
		
		$this->fuel->load_model('fuel_navigation');
		
		$this->load->helper('file');
		$this->js_controller_params['method'] = 'add_edit';

		// get saved data
		$saved = array();
		if (!empty($id))
		{
			$saved = $this->model->find_one_array(array($this->model->table_name().'.id' => $id));
			if (empty($saved))
			{
				show_404();
			}
		}

		if ($this->input->get('lang'))
		{
			$saved['language'] = $this->input->get('lang', TRUE);
		}
		//$this->model->add_required('location');
		
		// create fields... start with the table info and go from there
		$fields = $this->model->form_fields($saved);
		$common_fields = $this->_common_fields($saved);
		$fields = array_merge($fields, $common_fields);

		if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
		{
			unset($fields['published']);
		}
		
		// layout name tweaks
		if ($this->input->post('layout'))
		{
			$layout = $this->input->post('layout', TRUE);
		} 
		else if (!empty($saved['layout']))
		{
			$layout =  $saved['layout'];
		}
		else 
		{
			$layout = $this->fuel->layouts->default_layout;
		}
		
		// num uri params
		$fields['cache']['class'] = 'advanced';
		
		
		$field_values = (!empty($_POST)) ? $_POST : $saved;
		$field_values['layout'] = $layout;
		
		if (!empty($field_values['location'])) $this->preview_path = $field_values['location'];
		
		$sort_arr = (empty($fields['navigation_label'])) ? array('location', 'layout', 'published', 'cache') : array('location', 'layout', 'navigation_label', 'published', 'cache');
		
		// create page form fields
		$this->form_builder->set_validator($this->model->get_validation());
		$this->form_builder->question_keys = array();
		$this->form_builder->submit_value = NULL;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_field_order($sort_arr);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->show_required = FALSE;
		
		// set this one to FALSE because the layout selection will execute the js again
		$this->form_builder->auto_execute_js = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->set_field_values($field_values);

		$vars['form'] = $this->form_builder->render();

		// clear the values hear to prevent issues in subsequent calls
		$this->form_builder->clear();


		$this->form_builder->submit_value = lang('btn_save');
		$this->form_builder->cancel_value = lang('btn_cancel');
		
		// page variables
		$layout =  $this->fuel->layouts->get($layout);
		if (!empty($layout))
		{
			$fields = $layout->fields();
			$import_field = $layout->import_field();
		}

		if (!empty($import_field))
		{
			$this->js_controller_params['import_field'] = $import_field;
		}

		/*****************************************************************************
		// check for twin view file, controller and extra routing to generate warnings
		******************************************************************************/
		$view_twin = NULL;
		$import_view = FALSE;
		$routes = array();
		$uses_controller = FALSE;
		if (!empty($field_values['location'])) {
			$view_twin = APPPATH.'views/'.$field_values['location'].EXT;
			$import_view = FALSE;
			if (file_exists($view_twin))
			{
				$view_twin_info = get_file_info($view_twin);
				if (!empty($saved)) {
					$tz = date('T');
					if ($view_twin_info['date'] > strtotime($saved['last_modified'].' '.$tz) OR
						$saved['last_modified'] == $saved['date_added'])
					{
						$import_view = TRUE;
					}
				}
			}
			
			// check if there is routing for this page and display warning
			require(APPPATH.'config/routes.php');
			
			$page_vars = array();
			foreach ($route as $key => $val)
			{
				// Convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// Does the RegEx match?
				if (preg_match('#^'.$key.'$#', $field_values['location']))
				{
					$routes[] = $key;
				}
			}

			// check if a controller and method already exists
			$segments = explode('/', $field_values['location']);
			
			if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
			{
				require_once(APPPATH.'controllers/'.$segments[0].EXT);
				$controller = $segments[0];
				$method = (!empty($segments[1])) ? $segments[1] : 'index';
				$class_methods = get_class_methods($segments[0]);
				if (in_array($method, $class_methods)) $uses_controller = TRUE;
				
			}
			
			if (file_exists(APPPATH.'controllers/'.$segments[0]))
			{
				// Is the controller in a sub-folder?
				if (is_dir(APPPATH.'controllers/'.$segments[0]))
				{		
					// Set the directory and remove it from the segment array
					if (count($segments) > 1)
					{
						// Does the requested controller exist in the sub-folder?
						if ( file_exists(APPPATH.'controllers/'.$segments[0].'/'.$segments[1].EXT))
						{
							require_once(APPPATH.'controllers/'.$segments[0].'/'.$segments[1].EXT);
							$controller = $segments[1];
							$method = (!empty($segments[2])) ? $segments[2] : 'index';
							$class_methods = get_class_methods($segments[1]);
							if (in_array($method, $class_methods)) $uses_controller = TRUE;
						}
					}
					else
					{
						if (file_exists(APPPATH.'controllers/'.$segments[0].$route['default_controller'].EXT))
						{
							require_once(APPPATH.'controllers/'.$segments[0].$route['default_controller'].EXT);
							$class_methods = get_class_methods($route['default_controller']);
							if (in_array('index', $class_methods)) $uses_controller = TRUE;
						}
					}
				}
			}
		}
		
		$this->form_builder->id = 'layout_fields';
		$this->form_builder->name_prefix = 'vars';
		$this->form_builder->set_fields($fields);
		
		$page_vars = array();
		if (!empty($id))
		{
			$page_vars = $this->fuel_pagevariables_model->find_all_by_page_id($id);
		}
		else if (!empty($_POST))
		{
			$page_vars = array();
			foreach($_POST as $key => $val)
			{
				$key = end(explode('--', $key));
				$page_vars[$key] = $val;
			}
		}
		
		$this->form_builder->set_field_values($page_vars);
		
		$conflict = $this->_has_conflict($fields);

		if (!empty($conflict))
		{
			$vars['layout_fields'] = $conflict;
		}
		else if (empty($id))
		{
			$vars['layout_fields'] = $this->form_builder->render();
		}
		else
		{
			$vars['layout_fields'] = '';
		}

		// other variables
		$vars['id'] = $id;
		$vars['data'] = $saved;

		$vars['action'] =  (!empty($saved['id'])) ? 'edit' : 'create';
		$action_uri = $vars['action'].'/'.$id.'/';
		$vars['form_action'] = ($this->fuel->admin->is_inline()) ? $this->module_uri.'/inline_'.$action_uri : $this->module_uri.'/'.$action_uri;
		$vars['versions'] = $this->fuel_archives_model->options_list($id, $this->model->table_name());
		
		$vars['publish'] = (!empty($saved['published']) && is_true_val($saved['published'])) ? 'Unpublish' : 'Publish';
		$vars['import_view'] = $import_view;
		$vars['view_twin'] = $view_twin;
		$vars['routes'] = $routes;
		$vars['uses_controller'] = $uses_controller;
		$vars['others'] = $this->model->get_others('location', $id);
		if (!empty($saved['location'])) 
		{
			$related = $saved;
			$related['page_vars'] = $page_vars;
			$vars['related_items'] = $this->model->related_items($related);
		}

		$actions = $this->load->view('_blocks/module_create_edit_actions', $vars, TRUE);
		$vars['actions'] = $actions;
		$vars['error'] = $this->model->get_errors();
		
		if (!empty($saved['last_modified']))
		{
			$vars['last_updated'] = lang('pages_last_updated_by', english_date($vars['data']['last_modified'], true), $vars['data']['email']);
		}
		
		$notifications = $this->load->view('_blocks/notifications', $vars, TRUE);
		$vars['notifications'] = $notifications;
		
		if ($vars['action'] == 'edit')
		{
			$crumbs = array($this->module_uri => $this->module_name, '' => character_limiter(strip_tags($vars['data'][$this->display_field]), 50));
		}
		else
		{
			$crumbs = array($this->module_uri => $this->module_name, '' => lang('action_create'));
		}
		$this->fuel->admin->set_titlebar($crumbs);

		// do this after rendering so it doesn't render current page'
		if (!empty($vars['data'][$this->display_field]))
		{
			$this->fuel->admin->add_recent_page($this->uri->uri_string(), $vars['data'][$this->display_field], $this->module);
		}
		return $vars;
	}
	
	public function _save_page_vars($id, $posted)
	{
		//$vars = $this->input->post('vars');
		$vars = array();

		// process post vars... can't use an array because of file upload complications'
		foreach($posted as $key => $val)
		{
			if (strncmp('vars--', $key, 6) === 0)
			{
				$new_key = end(explode('--', $key));
				$vars[$new_key] = $val;
			}
		}
		
		if (!empty($vars) && is_array($vars))
		{

			// run any form field post processing hooks

			$layout = $this->fuel->layouts->get($this->input->post('layout', TRUE));
			$fields = $layout->fields();

			// add in block fields
			foreach($fields as $key => $val)
			{
				if (isset($val['type']) AND $val['type'] == 'block' AND isset($posted[$key]['block_name']))
				{
					$block_layout = $this->fuel->layouts->get($posted[$key]['block_name'], 'block');
					if ($block_layout)
					{
						$block_fields = $block_layout->fields();
						$fields = array_merge($fields, $block_fields);
					}
				}
			}

			$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');
			
			$this->form_builder->set_fields($fields);
			$this->form_builder->set_field_values($vars);
			
			$vars = $this->form_builder->post_process_field_values($vars);// manipulates the $_POST values directly

			$save = array();
			
			$lang = $this->input->post('language', TRUE);
			
			// clear out all other variables
			$delete = array('page_id' => $id);
			if ($this->input->post('language'))
			{
				$delete['language'] = $this->input->post('language', TRUE);
			}
			
			$this->fuel_pagevariables_model->delete($delete);
			$pagevariable_table = $this->db->table_info($this->fuel_pagevariables_model->table_name());
			$var_types = $pagevariable_table['type']['options'];
			$page_variables_archive = array();
			
			// field types that shouldn't be saved
			$non_recordable_fields = array('section', 'copy', 'fieldset');
			
			foreach($fields as $key => $val)
			{
				if (!isset($val['type']))
				{
					$val['type'] = 'string';
				}

				if (!in_array($val['type'], $non_recordable_fields))
				{
					$value = (!empty($vars[$key])) ? $vars[$key] : NULL;
					if (is_array($value) OR $val['type'] == 'array' OR $val['type'] == 'multi')
					{
						//$value = array_map('zap_gremlins', $value);
						//$value = serialize($value);
						$val['type'] = 'array'; // force the type to be an array
					}

					if (!in_array($val['type'], $var_types)) $val['type'] = 'string';
					
					$save = array('page_id' => $id, 'name' => $key, 'value' => $value, 'type' => $val['type']);
					$where = array('page_id' => $id, 'name' => $key, 'language' => $lang);
					if ($lang)
					{
						$save['language'] = $lang;
						$where['language'] = $lang;
					}
					$where = (!empty($id)) ? $where : array();

					if (!$layout->validate($vars))
					{
						add_errors($layout->errors());
						return FALSE;
					}

					if (!$this->fuel_pagevariables_model->save($save, $where))
					{
						add_error(lang('error_saving'));
						return FALSE;
					}

				}
			}

			$page_variables_archive = $this->fuel_pagevariables_model->find_all_array(array('page_id' => $id));

			// archive
			$archive = $this->model->cleaned_data();
			$archive[$this->model->key_field()] = $id;
			$archive['variables'] = $page_variables_archive;
			
			$this->model->archive($id, $archive);
			
			// save to navigation if config allows it
			if ($this->input->post('navigation_label')) {
					
				$this->fuel->load_model('fuel_navigation');
				
				$save = array();
				$save['label'] = $this->input->post('navigation_label', TRUE);
				$save['location'] = $this->input->post('location', TRUE);
				$save['group_id'] = $this->fuel->config('auto_page_navigation_group_id');
				$save['parent_id'] = 0;
				$save['published'] = $this->input->post('published', TRUE);
				if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
				{
				     $save['published'] = 'no';
				}
				// reset $where and create where clause to try and find an existing navigation item
				$where = array();
				$where['location'] = $save['location'];
				$where['group_id'] = $save['group_id'];
				$where['parent_id'] = $save['parent_id'];
				$does_it_exist_already = $this->fuel_navigation_model->record_exists($where);
				if (!$does_it_exist_already)
				{
					// determine parent based off of location
					$location_arr = explode('/', $this->input->post('location', TRUE));
					$parent_location = implode('/', array_slice($location_arr, 0, (count($location_arr) -1)));
				
					if (!empty($parent_location)) $parent = $this->fuel_navigation_model->find_by_location($parent_location);
					if (!empty($parent)) {
						$save['parent_id'] = $parent['id'];
					}
					$this->fuel_navigation_model->add_validation('parent_id', array(&$this->fuel_navigation_model, 'no_location_and_parent_match'), lang('error_location_parents_match'), '{location}');
					$this->fuel_navigation_model->save($save, array('location' => $this->input->post('location', TRUE), 'group_id' => $save['group_id']));
				}
			}
		}
		$this->fuel->admin->set_notification(lang('data_saved'), Fuel_admin::NOTIFICATION_SUCCESS);
		
		// reset cache for that page only
		if ($this->input->post('location'))
		{
			$this->fuel->cache->clear_page($this->input->post('location', TRUE));
		}
		return TRUE;
	}

	public function layout_fields($layout_name, $id = NULL, $lang = NULL, $vars = array())
	{
		// check to make sure there is no conflict between page columns and layout vars
		$layout = $this->fuel->layouts->get($layout_name);
		if (!$layout)
		{
			return;
		}
		$fields = $layout->fields();

		$fields['__layout__'] = array('type' => 'hidden', 'value' => $layout_name);

		$conflict = $this->_has_conflict($fields);
		if (!empty($conflict))
		{
			$this->output->set_output($conflict);
			return;
		}
		$this->load->library('form_builder');
		$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');
		
		$this->form_builder->question_keys = array();
		$this->form_builder->submit_value = lang('btn_save');
		$this->form_builder->cancel_value = lang('btn_cancel');
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->name_prefix = 'vars';
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		
		if (!empty($id))
		{
			$pagevars = $this->fuel_pagevariables_model->find_all_by_page_id($id, $lang);

			// the following will pre-populate fields of a different language to the default values
			if (empty($pagevars) AND $this->fuel->language->has_multiple() AND $lang != $this->fuel->language->default_option())
			{
				$pagevars = $this->fuel_pagevariables_model->find_all_by_page_id($id, $this->fuel->language->default_option());
			}

			$pagevars = array_merge($pagevars, $vars);

			$this->form_builder->set_field_values($pagevars);
		}
		
		$form = $this->form_builder->render();
		$this->output->set_output($form);
	}
	
	public function _has_conflict($fields)
	{
		$page_columns = $this->model->form_fields();
		unset($page_columns['id']);
		$reserved_cols = array_keys($page_columns);
		$page_variable_cols = array_keys($fields);

		foreach($page_variable_cols as $val)
		{
			if (in_array($val, $reserved_cols))
			{
				return '<div class="notification"><p class="notification warning ico_warn">'.lang('error_page_layout_variable_conflict', implode(', ', $reserved_cols)).'</p></div>';
			}
		}
		return FALSE;
	}

	public function import_view()
	{
		if (!empty($_POST['id']))
		{
			$id = $this->input->post('id', TRUE);
			$pagevars = $this->fuel->pages->import($this->input->post('id', TRUE), $this->sanitize_input);
			$layout = $pagevars['layout'];
			unset($pagevars['layout']);
			$this->layout_fields($layout, $id, NULL, $pagevars);
			return;
		}
		$out = 'error';
		$this->output->set_output($out);
	}
	
	public function import_view_cancel()
	{
		if ($this->input->post('id')){

			// don't need to pass anything because it will automatically update last_modified'
			$save['id'] = $this->input->post('id', TRUE);
			$save['location'] = $this->input->post('location', TRUE);
			$save['last_modified'] = datetime_now();

			$where['id'] = $save['id'];
			if ($this->model->update($save, $where))
			{
				$this->output->set_output('success');
				return;
			}
		}
		$this->output->set_output('error');
	}
	
	public function ajax_page_edit()
	{
		if (is_ajax())
		{
			if (!empty($_POST))
			{
				$save = $this->_process();
				if ($this->model->save($save))
				{
					$this->output->set_output('success');
					return;
				}

			}
		}
		$this->output->set_output('error');
	}

	public function select()
	{

		$value = $this->input->get_post('selected', TRUE);
		$filter = rawurldecode($this->input->get_post('filter', TRUE));

		// Convert wild-cards to RegEx
		$filter = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $filter));
		$this->js_controller_params['method'] = 'select';
	
		$this->load->helper('array');
		$this->load->helper('form');
		$this->load->library('form_builder');
		$pages = $this->fuel->pages->options_list();
		$pdfs = $this->fuel->assets->dir_files('pdf', TRUE);
 
		if (!empty($pdfs) AND !empty($_GET['pdfs']))
		{
			$options[lang('page_select_pages')] = array_combine($pages, $pages);
			$options[lang('page_select_pdfs')] = array_combine($pdfs, $pdfs);
		}
		else
		{
			$options = array_combine($pages, $pages);
		}

		// apply filter
		if (!empty($filter))
		{
			$filter_callback = create_function('$a', 'return preg_match(\'#^'.$filter.'$#\', $a);');
			$options = array_filter($options, $filter_callback);
		}
		
		// just return the options as json
		$fields['General'] = array('type' => 'fieldset', 'class' => 'tab');
		if (isset($_GET['options']))
		{
			if (isset($_GET['format']) AND strtolower($_GET['format']) == 'json')
			{
				json_headers();
				echo json_encode($options);
				return;
			}
			else
			{
				$str = '';
				if (isset($_GET['first_option']))
				{
					$first_option = $this->input->get('first_option', TRUE);
					$str .= "<option value=\"\" label=\"".Form::prep($first_option, FALSE)."\">".Form::prep($first_option, FALSE)."</option>\n";
				}
				foreach($options as $key => $val)
				{
					$str .= "<option value=\"".Form::prep($key, FALSE)."\" label=\"".Form::prep($val, FALSE)."\">".Form::prep($val, FALSE)."</option>\n";
				}
				echo $str;
				return;
				
			}
		}

		$select_label = lang('form_label_page');
		$display_label_select = FALSE;
		if (isset($_GET['input']))
		{
			$fields['input'] = array('value' => $this->input->get_post('input', TRUE), 'label' => lang('form_label_url'), 'size' => 100);	
			$select_label = lang('form_label_or_select');
			$display_label_select = TRUE;
		}

		$fields['url_select'] = array('value' => $this->input->get_post('url_select', TRUE), 'label' => $select_label, 'type' => 'select', 'options' => $options, 'first_option' => lang('label_select_one'), 'display_label' => $display_label_select);

		$fields['Advanced'] = array('type' => 'fieldset', 'class' => 'tab');
		if (isset($_GET['target']))
		{
			$target_options = array(
				''        => '', 
				'_blank'  => '_blank',
				'_parent' => '_parent',
				'_top'    => '_top',
				);
			$fields['target'] = array('value' => $this->input->get_post('target', TRUE), 'label' => lang('form_label_target'), 'type' => 'select', 'options' => array('' => '', '_blank' => '_blank'));	
			$fields['url_select']['display_label'] = TRUE;
		}
		
		if (isset($_GET['title']))
		{
			$fields['title'] = array('value' => $this->input->get_post('title', TRUE), 'label' => lang('form_label_title'));
			$fields['url_select']['display_label'] = TRUE;
		}

		if (isset($_GET['class']))
		{
			$fields['class'] = array('value' => $this->input->get_post('class', TRUE), 'label' => lang('form_label_class'));
			$fields['url_select']['display_label'] = TRUE;
		}

		$fields['selected'] = array('type' => 'hidden', 'value' => $this->input->get_post('selected', TRUE));

		$this->form_builder->submit_value = NULL;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$vars['form'] = $this->form_builder->render();
		$this->fuel->admin->set_inline(TRUE);

		$crumbs = array('' => $this->module_name, lang('pages_select_action'));
		$this->fuel->admin->set_panel_display('notification', FALSE);
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->render('modal_select', $vars);
	}

	public function upload()
	{
		$this->load->helper('file');
		$this->load->helper('security');
		$this->load->library('form_builder');
		$this->load->library('upload');
		$this->js_controller_params['method'] = 'upload';

		if (!empty($_POST) AND !empty($_FILES))
		{
			$params['upload_path'] = sys_get_temp_dir();
			$params['allowed_types'] = 'php|html|txt';

			// to ensure we check the proper mime types
			$this->upload->initialize($params);

			// Hackery to ensure that a proper php mimetype is set. 
			// Would set in mimes.php config but that may be updated with the next version of CI which does not include the text/plain
			$this->upload->mimes['php'] =  array(
				'application/x-httpd-php', 
				'application/php', 
				'application/x-php', 
				'text/php', 
				'text/x-php', 
				'application/x-httpd-php-source', 
				'text/plain');

			if ($this->upload->do_upload('file'))
			{
				$upload_data = $this->upload->data();
				$error = FALSE;
				
				// read in the file so we can filter it
				$file = read_file($upload_data['full_path']);
				
				// sanitize the file before saving
				$id = $this->input->post('id', TRUE);
				$pagevars = $this->fuel->pages->import($id);

				if (!empty($pagevars))
				{
					$layout = $this->fuel->layouts->get($pagevars['layout']);
					unset($pagevars['layout']);

					foreach($pagevars as $key => $val)
					{
						$where['page_id'] = $id;
						$where['name'] = $key;
						$page_var = $this->fuel_pagevariables_model->find_one_array($where);
						$save['id'] = (empty($page_var['id'])) ? NULL : $page_var['id'];
						$save['name'] = $key;
						$save['page_id'] = $id;
						$save['value'] = $val;
						if (!$this->fuel_pagevariables_model->save($save))
						{
							add_error(lang('error_upload'));
						}
					}

					// resave to prevent import popup on next page
					$page = $this->fuel_pages_model->find_by_key($id, 'array');
					$page['last_modified'] = date('Y-m-d H:i:s', (time() + 1)); // to prevent window from popping up after upload
					$this->model->save($page);

					if (!has_errors())
					{
						// change list view page state to show the selected group id
						$this->fuel->admin->set_notification(lang('pages_success_upload'), Fuel_admin::NOTIFICATION_SUCCESS);
						
						redirect(fuel_url('pages/edit/'.$id));
					}

				}
				else
				{
					add_error(lang('error_upload'));
				}
			}
			else
			{
				$error_msg = $this->upload->display_errors('', '');
				add_error($error_msg);
			}
		}
		
		$fields = array();
		$pages = $this->model->options_list('id', 'location', array('published' => 'yes'), 'location');
		
		$fields['id'] = array('label' => lang('form_label_name'), 'type' => 'select', 'options' => $pages, 'class' => 'add_edit pages');
		$fields['file'] = array('type' => 'file', 'accept' => '');
		$this->form_builder->hidden = array();
		$this->form_builder->set_fields($fields);
		$this->form_builder->set_field_values($_POST);
		$this->form_builder->submit_value = '';
		$this->form_builder->use_form_tag = FALSE;
		$vars['instructions'] = lang('pages_upload_instructions');
		$vars['form'] = $this->form_builder->render();
		$vars['back_action'] = ($this->fuel->admin->last_page() AND $this->fuel->admin->is_inline()) ? $this->fuel->admin->last_page() : fuel_uri($this->module_uri);
		//$vars['back_action'] = fuel_uri($this->module_uri);
		
		$crumbs = array($this->module_uri => $this->module_name, '' => lang('action_upload'));
		$this->fuel->admin->set_titlebar($crumbs);
		
		$this->fuel->admin->render('upload', $vars);
	}
	
	public function refresh_field()
	{

		if (is_ajax() AND (!empty($_POST) OR !empty($_GET)))
		{
			$layout =  $this->input->get_post('layout', TRUE);
			$values = $this->input->get_post('values', TRUE);
			if (empty($layout)) return;

			$layout_obj = $this->fuel->layouts->get($layout);
			$fields = $layout_obj->fields();
			$field = $this->input->get_post('field', TRUE);

			$field_key = end(explode('vars--', $field));

			if (!isset($fields[$field_key])) return;

			$field_id = $this->input->post('field_id', TRUE);
			$selected = $this->input->post('selected', TRUE);
			
			$this->load->library('form_builder');
			$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');

			// for multi select
			if (is_array($values))
			{
				$selected = (array) $selected;
				$selected = array_merge($values, $selected);
			}
			
			$output = '';

			// if template/nested field types, then we need to look at the sub field
			if ($fields[$field_key]['type'] == 'template')
			{
				//$fields['return_fields'] = TRUE;
				require_once(FUEL_PATH.'libraries/Fuel_custom_fields.php');
				$fuel_cf = new Fuel_custom_fields();
				$index = $this->input->get_post('index', TRUE);
				$key = $this->input->get_post('key', TRUE);
				$field_name = $this->input->get_post('field_name', TRUE);
				$params = $fields[$field_key];
				$params['index'] = $index;
				$params['name'] = $field_name;
				$params['key'] = $field_name;
				$params['value'] = array();
				$params['value'][0] = $selected;
				$this->form_builder->name_prefix = 'vars';
				$this->form_builder->name_array = $field_name;
				//$fb->set_field_values();
				$params['instance'] =& $this->form_builder;
				$sub_fields = $fuel_cf->template($params, TRUE);
				if (!empty($sub_fields[0][$key]))
				{
					$output = $sub_fields[0][$key];
				}
				
			}
			else
			{
				if (!empty($selected)) $fields[$field_key]['value'] = $selected;
				$fields[$field_key]['name'] = $field_id;
				
				// if the field is an ID, then we will do a select instead of a text field
				if (isset($fields[$this->model->key_field()]))
				{
					$fields['id']['type'] = 'select';
					$fields['id']['options'] = $this->model->options_list();
				}
				$output = $this->form_builder->create_field($fields[$field_key]);	
			}
			
			$this->output->set_output($output);
		}
	}
	
}