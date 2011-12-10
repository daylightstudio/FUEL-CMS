<?php
require_once('module.php');

class Pages extends Module {
	
	private $_importing = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->config->load('fuel', TRUE);
		$this->load->module_library(FUEL_FOLDER, 'fuel_layouts');
		$this->load->module_model(FUEL_FOLDER, 'pagevariables_model');
	}

	function create()
	{
		if (isset($_POST['id'])) // check for dupes
		{
			$posted = $this->_process();
			
			// set publish status to no if you do not have the ability to publish
			if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
			{
				$posted['published'] = 'no';
			}
			
			// reset dup id
			if ($_POST['id'] == 'dup')
			{
				$_POST['id'] = '';
				$_POST['location'] = '';
			}
			else if ($id = $this->model->save($posted))
			{
				if (empty($id))
				{
					show_error('Not a valid ID returned to save layout variables');
				}
				
				$this->_process_uploads();
				
				if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
				{
					unset($_POST['published']);
				}
				
				$this->_save_page_vars($id, $posted);
				$data = $this->model->find_one_array(array($this->model->table_name().'.id' => $id));
				
				// run hook
				$this->_run_hook('create', $data);
				
				if (!empty($data))
				{
					$msg = lang('module_created', $this->module_name, $data[$this->display_field]);
					redirect(fuel_uri('pages/edit/'.$id));
				}
			}
			
		}
		$vars = $this->_form();
		$this->_render('pages/page_create_edit', $vars);
	}

	function edit($id = NULL)
	{
		if (empty($id)) show_404();

		$posted = $this->_process();

		if ($this->input->post('id'))
		{
			if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
			{
				unset($_POST['published']);
			}
			
			if ($this->model->save($posted))
			{
				$this->_process_uploads();
				
				$this->_save_page_vars($id, $posted);
				$data = $this->model->find_one_array(array($this->model->table_name().'.id' => $id));
				
				// run hook
				$this->_run_hook('edit', $data);
				
				$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
				$this->logs_model->logit($msg);
				redirect(fuel_uri('pages/edit/'.$id));
			}
		}
		$vars = $this->_form($id);
		$this->_render('pages/page_create_edit', $vars);
	}
	
	function _form($id = NULL, $fields = NULL, $log_to_recent = TRUE, $display_normal_submit_cancel = TRUE)
	{
		
		$this->load->library('form_builder');
		$this->load->module_model(FUEL_FOLDER, 'navigation_model');
		
		$this->load->helper('file');
		$this->js_controller_params['method'] = 'add_edit';

		// get saved data
		$saved = array();
		if (!empty($id)) {
			$saved = $this->model->find_one_array(array($this->model->table_name().'.id' => $id));
			if (empty($saved)) show_404();
		}
		
		$this->model->add_required('location');
		
		// create fields... start with the table info and go from there
		$fields = $this->model->form_fields();
		if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
		{
			unset($fields['published']);
		}
		
		// layout name tweaks
		if ($this->input->post('layout'))
		{
			$layout = $this->input->post('layout');
		} 
		else if (!empty($saved['layout']))
		{
			$layout =  $saved['layout'];
		}
		else 
		{
			$layout = $this->fuel_layouts->default_layout;
		}
		
		$fields['layout']['type'] = 'select';
		$fields['layout']['options'] = $this->fuel_layouts->layouts_list();
		$fields['layout']['value'] = $layout;
		
		// num uri params
		$fields['cache']['class'] = 'advanced';
		
		// easy add for navigation
		if (empty($id)) $fields['navigation_label'] = array('comment' => 'This field lets you quickly add a navigation item for this page. 
		It only allows you to create a navigation item during page creation. To edit the navigation item, you must click on the
		\'Navigation\' link on the left, find the navigation item you want to change and click on the edit link.');
		
		$field_values = (!empty($_POST)) ? $_POST : $saved;
		
		if (!empty($field_values['location'])) $this->preview_path = $field_values['location'];
		
		$sort_arr = (empty($fields['navigation_label'])) ? array('location', 'layout', 'published', 'cache') : array('location', 'layout', 'navigation_label', 'published', 'cache');
		
		// not inline edited
		if (!$display_normal_submit_cancel)
		{
			$this->form_builder->submit_value = NULL;
			$this->form_builder->cancel_value = NULL;
			//$this->form_builder->name_array = '__fuel_field__'.$this->module_name.'_'.$id;
			$fields['__fuel_inline_action__'] = array('type' => 'hidden');
			$fields['__fuel_inline_action__']['class'] = '__fuel_inline_action__';
			$fields['__fuel_inline_action__']['value'] = (empty($id)) ? 'create' : 'edit';

			$fields['__fuel_module__'] = array('type' => 'hidden');
			$fields['__fuel_module__']['value'] = $this->module;
			$fields['__fuel_module__']['class'] = '__fuel_module__';
			$this->form_builder->name_prefix = '__fuel_field__'.$this->module.'_'.(empty($id) ? 'create' : $id);
			$this->form_builder->css_class = 'inline_form';
			
		}
		//$this->form_builder->hidden = (array) $this->model->key_field();
		$this->form_builder->question_keys = array();
		$this->form_builder->use_form_tag = FALSE;
		
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->set_field_values($field_values);
		$this->form_builder->displayonly = $this->displayonly;
		// create page form fields
		$this->form_builder->form->validator = &$this->page->validator;
		$this->form_builder->submit_value = NULL;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->set_field_order($sort_arr);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->show_required = FALSE;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();

		$this->form_builder->submit_value = lang('btn_save');
		$this->form_builder->cancel_value = lang('btn_cancel');
		
		// page variables
		$fields = $this->fuel_layouts->fields($layout, empty($id));
		
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
		
		$this->form_builder->name_prefix = 'vars';
		$this->form_builder->set_fields($fields);
		$page_vars = array();
		if (!empty($id))
		{
			$page_vars = $this->pagevariables_model->find_all_by_page_id($id);
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
		$vars['layout_fields'] = (!empty($conflict)) ? $conflict : $this->form_builder->render();

		// other variables
		$vars['id'] = $id;
		$vars['data'] = $saved;
		
		$vars['action'] =  (!empty($saved['id'])) ? 'edit' : 'create';
		$vars['versions'] = $this->archives_model->options_list($id, $this->model->table_name());
		
		$vars['publish'] = (!empty($saved['published']) && is_true_val($saved['published'])) ? 'Unpublish' : 'Publish';
		$vars['import_view'] = $import_view;
		$vars['view_twin'] = $view_twin;
		$vars['routes'] = $routes;
		$vars['uses_controller'] = $uses_controller;
		$vars['others'] = $this->model->get_others('location', $id);
		if (!empty($saved['location'])) $vars['page_navs'] = $this->navigation_model->find_by_location($saved['location'], FALSE);
		
		$actions = $this->load->view('_blocks/module_create_edit_actions', $vars, TRUE);
		$vars['actions'] = $actions;
		$vars['error'] = $this->model->get_errors();
		$notifications = $this->load->view('_blocks/notifications', $vars, TRUE);
		$vars['notifications'] = $notifications;

		// do this after rendering so it doesn't render current page'
		if (!empty($vars['data'][$this->display_field])) $this->_recent_pages($this->uri->uri_string(), $vars['data'][$this->display_field], $this->module);
		return $vars;
	}
	
	function _save_page_vars($id, $posted)
	{
		//$vars = $this->input->post('vars');
		$vars = array();

		// process post vars... can't use an array because of file upload complications'
		foreach($posted as $key => $val)
		{
			if (strncmp('vars--', $key, 4) === 0)
			{
				$new_key = end(explode('--', $key));
				$vars[$new_key] = $val;
			}
		}

		if (!empty($vars) && is_array($vars))
		{
			$fields = $this->fuel_layouts->fields($this->input->post('layout'));
			$save = array();
			
			// clear out all other variables
			$this->pagevariables_model->delete(array('page_id' => $id));
			$pagevariable_table = $this->db->table_info($this->pagevariables_model->table_name());
			$var_types = $pagevariable_table['type']['options'];
			$page_variables_archive = array();

			foreach($fields as $key => $val)
			{
				$value = (!empty($vars[$key]) ) ? $vars[$key] : NULL;

				if ($val['type'] == 'array' OR $val['type'] == 'multi')
				{
					$value = serialize($value);
					$val['type'] = 'array'; // force the type to be an array
				}
				
				if (!in_array($val['type'], $var_types)) $val['type'] = 'string';
				$save = array('page_id' => $id, 'name' => $key, 'value' => $value, 'type' => $val['type']);
				$where = (!empty($id)) ? array('page_id' => $id, 'name' => $key) : array();

				if ($this->pagevariables_model->save($save, $where))
				{
					$page_variables_archive[$key] = $this->pagevariables_model->cleaned_data();
				}
			}
			// archive
			$archive = $this->model->cleaned_data();
			$archive[$this->model->key_field()] = $id;
			$archive['variables'] = $page_variables_archive;
			
			$this->model->archive($id, $archive);
			
			// save to navigation if config allows it
			if ($this->input->post('navigation_label')) {
					
				$this->load->module_model(FUEL_FOLDER, 'navigation_model');
				$save = array();
				$save['label'] = $this->input->post('navigation_label');
				$save['location'] = $this->input->post('location');
				$save['group_id'] = $this->config->item('auto_page_navigation_group_id', 'fuel');
				$save['parent_id'] = 0;
				
				// reset $where and create where clause to try and find an existing navigation item
				$where = array();
				$where['location'] = $save['location'];
				$where['group_id'] = $save['group_id'];
				$where['parent_id'] = $save['parent_id'];
				$does_it_exist_already = $this->navigation_model->record_exists($where);
				if (!$does_it_exist_already)
				{
					// determine parent based off of location
					$location_arr = explode('/', $this->input->post('location'));
					$parent_location = implode('/', array_slice($location_arr, 0, (count($location_arr) -1)));
				
					if (!empty($parent_location)) $parent = $this->navigation_model->find_by_location($parent_location);
					if (!empty($parent)) {
						$save['parent_id'] = $parent['id'];
					}
					$this->navigation_model->add_validation('parent_id', array(&$this->navigation_model, 'no_location_and_parent_match'), lang('error_location_parents_match'), '{location}');
					$this->navigation_model->save($save, array('location' => $this->input->post('location'), 'group_id' => $save['group_id']));
				}
			}
		}
		$this->session->set_flashdata('success', lang('data_saved'));
		
		// reset cache for that page only
		if ($this->input->post('location')){
			$this->load->library('cache');
			$cache_group = $this->config->item('page_cache_group', 'fuel');
			$this->cache->remove(fuel_cache_id($this->input->post('location')), $cache_group);
		}
	}

	function layout_fields($layout, $id = NULL)
	{
		
		// check to make sure there is no conflict between page columns and layout vars
		$fields = $this->fuel_layouts->fields($layout);
		$conflict = $this->_has_conflict($fields);
		if (!empty($conflict))
		{
			$this->output->set_output($conflict);
			return;
		}
		$this->load->library('form_builder');
		$this->form_builder->form->validator = &$this->pagevariables_model->get_validation();
		$this->form_builder->question_keys = array();
		$this->form_builder->submit_value = lang('btn_save');
		$this->form_builder->cancel_value = lang('btn_cancel');
		$this->form_builder->use_form_tag = FALSE;
		//$this->form_builder->name_array = 'vars';
		$this->form_builder->name_prefix = 'vars';
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		if (!empty($id)) {
			$page_vars = $this->pagevariables_model->find_all_by_page_id($id);
			$this->form_builder->set_field_values($page_vars);
		}
		$this->output->set_output($this->form_builder->render());
	}
	
	function _has_conflict($fields)
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
	
	function import_view()
	{
		$out = 'error';
		if ($this->input->post('id')){
			$page_data = $this->model->find_by_key($this->input->post('id'), 'array');
			$this->load->helper('file');
			$view_twin = APPPATH.'views/'.$page_data['location'].EXT;
			
			if (file_exists($view_twin))
			{
				$view_twin_info = get_file_info($view_twin);
				$tz = date('T');
				if ($view_twin_info['date'] > strtotime($page_data['last_modified'].' '.$tz) OR
					$page_data['last_modified'] == $page_data['date_added'])
				{
					// must have content in order to not return error
					$out = file_get_contents($view_twin);
					
					// replace PHP tags with template tags... comments are replaced because of xss_clean()
					if ($this->sanitize_input)
					{
						$out = php_to_template_syntax($out);
					}
				}
			}
		}
		$this->output->set_output($out);
	}
	
	function import_view_cancel()
	{
		if ($this->input->post('id')){

			// don't need to pass anything because it will automatically update last_modified'
			$save['id'] = $this->input->post('id');
			$save['location'] = $this->input->post('location');
			$save['last_modified'] = datetime_now();

			if ($this->model->save($save))
			{
				$this->output->set_output('success');
				return;
			}
		}
		$this->output->set_output('error');
	}
	
	function ajax_page_edit()
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
	
	function inline_edit($field, $page_id = NULL)
	{
		// if field is empty then we'll assume it is really the page ID'
		if (empty($page_id))
		{
			$this->_importing = TRUE;
			parent::inline_edit($field);
			return;
		}
		else
		{
			if (is_ajax())
			{
				if (!empty($_POST)) {
					if (!empty($_POST['model']))
					{
						$this->load->module_model(FUEL_FOLDER, 'pagevariables_model', 'editor_model');

						$this->_process();

						$save = array();
						$var_id = $this->input->post('var_id', TRUE);
						$page_vars = $this->editor_model->find_by_key($var_id, 'array');



						$save['id'] = $this->input->post('var_id');
						$save_val = $this->_sanitize($_POST['__fuel_field__pagevar'][$field]);

						if (is_array($save_val))
						{
							// serialize to normalize
							$save_val = serialize($save_val);
						}
						else if ($page_vars['type'] == 'array')
						{
							// it may be an encoded string in which case we need to unencode and convert to an array
							$save_val = json_decode(rawurldecode($save_val));

							// serialize to normalize
							$save_val = serialize($save_val);
						}

						$save['value'] = $save_val;

						if ($this->editor_model->save($save))
						{
							$this->_process_uploads();
							
							// run hook
							$this->_run_hook('inline', $save);
							
						}
						else
						{
							$vars['error'] = $this->model->get_errors();
							$notification = $this->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
							if (empty($notification))
							{
								$notification = lang('error_saving');
							}
							$this->output->set_output('<error>'.$notification.'</error>');

						}
					}
				}
				else
				{
					$page = $this->model->find_one_array(array('fuel_pages.id' => $page_id));
					if (!empty($page))
					{
						$layout_fields = $this->fuel_layouts->fields($page['layout']);
						$page_var = $this->pagevariables_model->find_one_array(array('page_id' => $page_id, 'name' => $field));

						$fields = array();
						$fields[$field] = (isset($layout_fields[$field])) ? $layout_fields[$field] : '';
						$fields[$field]['label'] = ' ';
						$fields[$field]['value'] = (!empty($page_var)) ? $this->pagevariables_model->cast($page_var['value'], $page_var['type']) : '';

						$this->load->library('form_builder');
						$this->form_builder->form->validator = &$this->pagevariables_model->get_validation();
						$this->form_builder->question_keys = array();
						$this->form_builder->submit_value = NULL;
						$this->form_builder->use_form_tag = FALSE;
						$this->form_builder->label_colons = FALSE;
						$this->form_builder->name_array = '__fuel_field__pagevar';
						$this->form_builder->set_fields($fields);
						$this->form_builder->css_class = 'inline_form';
						$this->form_builder->display_errors = FALSE;

						$vars['form'] = $this->form_builder->render();
						$vars['description'] = '';
						$vars['field'] = $field;
						$vars['page_id'] = $page_id;
						$vars['var_id'] = (isset($page_var['id'])) ? $page_var['id'] : NULL;
						if (!empty($fields[$field]['comment']))
						{
							$vars['description'] = $fields[$field]['comment'];
						}
						else if (!empty($fields[$field]['description']))
						{
							$vars['description'] = $fields[$field]['description'];
						}

						$this->load->view('pages/inline_edit', $vars);
					}
					else
					{
						$this->output->set_output(lang('error_inline_page_edit'));
					}
				}
			}
		}
	}
	
	function upload()
	{
		$this->load->helper('file');
		$this->load->helper('security');
		$this->load->library('form_builder');
		$this->js_controller_params['method'] = 'upload';

		if (!empty($_POST))
		{
			if (!empty($_FILES['file']['name']))
			{
				
				$error = FALSE;
				$file_info = $_FILES['file'];
				
				// read in the file so we can filter it
				$file = read_file($file_info['tmp_name']);
				
				// sanitize the file before saving
				$id = $this->input->post('id', TRUE);
				$field = end(explode('--', $this->js_controller_params['import_view_key']));
				$where['page_id'] = $id;
				$where['name'] = $field;
				$page_var = $this->pagevariables_model->find_one_array($where);

				$file = $this->_sanitize($file);
				$save['id'] = (empty($page_var)) ? NULL : $page_var['id'];
				$save['name'] = $field;
				$save['page_id'] = $id;
				$save['value'] = $file;
				
				if (!$this->pagevariables_model->save($save))
				{
					add_error(lang('error_upload'));
				}


				if (!has_errors())
				{
					// change list view page state to show the selected group id
					$this->session->set_flashdata('success', lang('pages_success_upload'));
					redirect(fuel_url('pages/edit/'.$id));
				}
				
			}
			else if (!empty($_FILES['file']['error']))
			{
				add_error(lang('error_upload'));
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
		$this->_render('upload', $vars);
	}
	
}