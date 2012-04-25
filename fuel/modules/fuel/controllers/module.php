<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Module extends Fuel_base_controller {
	
	public $module = '';

	// array of data about all (if any) uploaded files
	public $upload_data = array();

	function __construct()
	{
		parent::__construct();

		$this->load->module_model(FUEL_FOLDER, 'archives_model');
		$this->module = fuel_uri_segment(1);

		if (empty($this->module))
		{
			show_error(lang('cannot_determine_module'));
		}
		
		$params = $this->fuel_modules->info($this->module);

		if (empty($params))
		{
			// if it is a module with multiple controllers, then we'll check first and second FUEL segment with a hyphen'
			$this->module = $this->module.'_'.fuel_uri_segment(2);
			$params = $this->fuel_modules->info($this->module);
			if ($params === FALSE) $params = array();
		}

		foreach($params as $key => $val)
		{
			$this->$key = $val;
		}
		
		// load any configuration
		if (!empty($this->configuration)) 
		{
			if (is_array($this->configuration))
			{
				$config_module = key($this->configuration);
				$config_file = current($this->configuration);

				$this->config->module_load($config_module, $config_file);
			}
			else
			{
				$this->config->load($this->configuration);
			}
		}
		
		// load any language
		if (!empty($this->language)) 
		{
			if (is_array($this->language))
			{
				$lang_module = key($this->language);
				$lang_file = current($this->language);

				// now check to see if we need to load the language file or not... 
				// we load the main language file automatically with the Fuel_base_controller.php
				$this->load->module_language($lang_module, $lang_file);
			}
			else
			{
				$this->config->load($this->language);
			}
		}
		
		// load the model
		if (!empty($this->model_location))
		{
			$this->load->module_model($this->model_location, $this->model_name);
		}
		else
		{
			$this->load->model($this->model_name);
		}
		
		if (empty($this->display_field))
		{
			$model = $this->model_name;
			$fields = $this->$model->fields();
			
			// loop through the fields and find the first column that doesn't have id or _id at the end of it
			for ($i = 1; $i < count($fields); $i++)
			{
				if (substr($fields[$i], -3) != '_id')
				{
					$this->display_field = $fields[$i];
					break;
				}
			}
			if (empty($this->display_field)) $this->display_field = $fields[1]; // usually the second field is the display_field... first is the id
		}
		
		// set the module_uri
		if (empty($this->module_uri)) $this->module_uri = $this->module;
		
		$this->js_controller_params['module'] = $this->module_uri;
		
		// 
		$model = $this->model_name;
		if (!empty($model))
		{
			$this->model =& $this->$model;
		}
		else
		{
			show_error(lang('incorrect_route_to_module'));
		}
		
		// global variables
		$vars = array();
		if (!empty($params['js']))
		{
			if (is_string($params['js']))
			{
				$params['js'] = preg_split("/,\s*/", $params['js']);
			}
			$vars['js'] = $params['js'];
		}
		if (!empty($this->nav_selected)) $vars['nav_selected'] = $this->nav_selected;
		$this->load->vars($vars);
		
		$this->_load_js_localized($params['js_localized']);

		if (!empty($this->permission)) $this->_validate_user($this->permission);
		
		
	}
	
	function index()
	{
		$this->items();
	}
	
	function items()
	{
		
		$this->load->library('data_table');
		$params = $this->_list_process();
		
		// save in case we need to pass more variables in the URI
		// $seg_params = $params;
		// unset($seg_params['offset']);
		// $seg_params = uri_safe_batch_encode($seg_params, '|', TRUE);
		
		// save page state
		$this->_save_page_state($params);
		
		if (!is_ajax() AND !empty($_POST))
		{
			//$uri = $this->config->item('fuel_path', 'fuel').$this->module.'/items/params/'.$seg_params.'/offset/'.$params['offset'];
			$uri = $this->config->item('fuel_path', 'fuel').$this->module_uri.'/items/offset/'.$params['offset'];
			redirect($uri);
		}
		
		
		// create search filter
		$filters[$this->display_field] = $params['search_term'];
		
		//$filters = array();
		
		// sort of hacky here... to make it easy for the model to just filter on the search term (like the users model)
		$this->model->filter_value = $params['search_term'];
			
		foreach($this->filters as $key => $val)
		{
			$filters[$key] = $params[$key];
		}
		
		// set model filters before pagination and setting table data
		if (method_exists($this->model, 'add_filters'))
		{
			$this->model->add_filters($filters);
		}
		$this->config->set_item('enable_query_strings', FALSE);
		
		// pagination
		$config['base_url'] = fuel_url($this->module_uri).'/items/offset/';
		$uri_segment = 4 + (count(explode('/', $this->module_uri)) - 1);
		$config['total_rows'] = $this->model->list_items_total();
		$config['uri_segment'] = fuel_uri_index($uri_segment);
		$config['per_page'] = (int) $params['limit'];
		$config['page_query_string'] = FALSE;
		$config['num_links'] = 5;
		$config['prev_link'] = lang('pagination_prev_page');
		$config['next_link'] = lang('pagination_next_page');
		$config['first_link'] = lang('pagination_first_link');
		$config['last_link'] = lang('pagination_last_link');;
		
		$this->pagination->initialize($config);

		if (method_exists($this->model, 'tree'))
		{
			//$vars['tree'] = "Loading...\n<ul></ul>\n";
			$vars['tree'] = "\n<ul></ul>\n";
		}
		
		// set vars
		$vars['params'] = $params;
		
		$vars['table'] = '';
		
		// reload table
		if (is_ajax())
		{
			// data table items... check col value to know if we want to send sorting parameter
			if (empty($params['col']) OR empty($params['order']))
			{
				$items = $this->model->list_items($params['limit'], $params['offset']);
			}
			else
			{
				$items = $this->model->list_items($params['limit'], $params['offset'], $params['col'], $params['order']);
				$this->data_table->set_sorting($params['col'], $params['order']);
			}
			
			// set data table actions... look first for item_actions set in the fuel_modules
			foreach($this->table_actions as $key => $val)
			{
				if (!is_int($key)) 
				{
					$action_type = 'url';
					$action_val = $this->table_actions[$key];
					if (is_array($val))
					{
						$action_type = key($val);
						$action_val = current($val);
					}
					$this->data_table->add_action($key, $action_val, $action_type);
				}
				else if (strtoupper($val) == 'DELETE')
				{
					$delete_func = '
					$CI =& get_instance();
					$link = "";
					if ($CI->fuel_auth->has_permission($CI->permission, "delete") AND isset($cols[$CI->model->key_field()]))
					{
						$url = site_url("/".$CI->config->item("fuel_path", "fuel").$CI->module_uri."/delete/".$cols[$CI->model->key_field()]);
						$link = "<a href=\"".$url."\">".lang("table_action_delete")."</a>";
						$link .= " <input type=\"checkbox\" name=\"delete[".$cols[$CI->model->key_field()]."]\" value=\"1\" id=\"delete_".$cols[$CI->model->key_field()]."\" class=\"multi_delete\"/>";
					}
					return $link;';
					
					$delete_func = create_function('$cols', $delete_func);
					$this->data_table->add_action($val, $delete_func, 'func');
				}
				else
				{
					if (strtoupper($val) != 'VIEW' OR (!empty($this->preview_path) AND strtoupper($val) == 'VIEW'))
					{
						$action_name = lang('table_action_'.strtolower($val));
						if (empty($action_name)) $action_name = $val;
						$this->data_table->add_action($action_name, site_url('/'.$this->config->item('fuel_path', 'fuel').$this->module_uri.'/'.strtolower($val).'/{'.$this->model->key_field().'}'), 'url');
					}
				}
			}
			
			
			if (!$this->rows_selectable)
			{
				$this->data_table->id = 'data_table_noselect';
				$this->data_table->row_action = FALSE;
			}
			else
			{
				$this->data_table->row_action = TRUE;
			}
			$this->data_table->row_alt_class = 'alt';
			$this->data_table->only_data_fields = array($this->model->key_field());
			$this->data_table->auto_sort = TRUE;
			$this->data_table->actions_field = 'last';
			$this->data_table->no_data_str = lang('no_data');
			$this->data_table->lang_prefix = 'form_label_';
			$_unpub_func = '
			$CI =& get_instance();
			$can_publish = $CI->fuel_auth->has_permission($CI->permission, "publish");
			$is_publish = (isset($cols[\'published\'])) ? TRUE : FALSE;
			$no = lang("form_enum_option_no");
			$yes = lang("form_enum_option_yes");
			if ((isset($cols[\'published\']) AND $cols[\'published\'] == "no") OR (isset($cols[\'active\']) AND $cols[\'active\'] == "no")) 
			{ 
				$text_class = ($can_publish) ? "publish_text unpublished toggle_publish": "unpublished";
				$action_class = ($can_publish) ? "publish_action unpublished hidden": "unpublished hidden";
				$col_txt = ($is_publish) ? \'click to publish\' : \'click to activate\';
				return "<span class=\"publish_hover\"><span class=\"".$text_class."\" id=\"row_published_".$cols["'.$this->model->key_field().'"]."\">".$no."</span><span class=\"".$action_class."\">".$col_txt."</span></span>";
			}
			else
			{ 
				$text_class = ($can_publish) ? "publish_text published toggle_unpublish": "published";
				$action_class = ($can_publish) ? "publish_action published hidden": "published hidden";
				$col_txt = ($is_publish) ? \'click to unpublish\' : \'click to deactivate\';
				return "<span class=\"publish_hover\"><span class=\"".$text_class."\" id=\"row_published_".$cols["'.$this->model->key_field().'"]."\">".$yes."</span><span class=\"".$action_class."\">".$col_txt."</span></span>";
			}';
				
			$_unpublished = create_function('$cols', $_unpub_func);

			$this->data_table->add_field_formatter('published', $_unpublished);
			$this->data_table->add_field_formatter('active', $_unpublished);
			$this->data_table->auto_sort = TRUE;
			$this->data_table->sort_js_func = 'page.sortList';
			
			$this->data_table->assign_data($items, $this->table_headers);

			$vars['table'] = $this->data_table->render();
			if (!empty($items[0]) AND (!empty($this->precedence_col) AND isset($items[0][$this->precedence_col])))
			{
				$vars['params']['precedence'] = 1;
			}
			$this->load->module_view(FUEL_FOLDER, '_blocks/module_list_table', $vars);
			return;
		}
		
		else
		{
			$this->load->library('form_builder');
			$this->js_controller_params['method'] = 'items';
			$this->js_controller_params['precedence_col'] = $this->precedence_col;
			
			
			$vars['table'] = $this->load->module_view(FUEL_FOLDER, '_blocks/module_list_table', $vars, TRUE);
			$vars['pagination'] = $this->pagination->create_links();

			// for extra module 'filters'
			$field_values = array();
			foreach($this->filters as $key => $val)
			{
				$field_values[$key] = $params[$key];
			}
			$this->form_builder->question_keys = array();
			//$this->form_builder->hidden = (array) $this->model->key_field();
			$this->form_builder->label_layout = 'left';
			$this->form_builder->form->validator = &$this->model->get_validation();
			$this->form_builder->submit_value = null;
			$this->form_builder->use_form_tag = FALSE;
			$this->form_builder->set_fields($this->filters);
			$this->form_builder->display_errors = FALSE;
			$this->form_builder->css_class = 'more_filters';
			$this->form_builder->set_field_values($field_values);
			
			// keycheck is already put in place by $this->form->close() in module_list layout
			$this->form_builder->key_check = FALSE; 
			$vars['more_filters'] = $this->form_builder->render_divs();

			$this->_render($this->views['list'], $vars);
		}
	}

	protected function _list_process()
	{
		$this->load->library('pagination');
		$this->load->helper('convert');
		$this->load->helper('cookie');

		/* PROCESS PARAMS BEGIN */
		$filters = array();
		
		$page_state = $this->_get_page_state($this->module_uri);
		
		$defaults = array();
		$defaults['col'] = (!empty($this->default_col)) ? $this->default_col : $this->display_field;
		$defaults['order'] = (!empty($this->default_order)) ? $this->default_order : 'asc';
		$defaults['offset'] = 0;
		$defaults['limit'] = 25;
		$defaults['search_term'] = '';
		$defaults['view_type'] = 'list';
		$defaults['extra_filters'] = array();
		$defaults['precedence'] = 0;
		
		// custom module filters defaults
		foreach($this->filters as $key => $val)
		{
			$defaults[$key] = (isset($val['default'])) ? $val['default'] : NULL;
		}
		
		$mod_segs = explode('/',$this->module_uri);
		$mod_offset_index = count($mod_segs) + 3;
		$uri_params = uri_safe_batch_decode(fuel_uri_segment($mod_offset_index), '|', TRUE);
		$uri_params['offset'] = (fuel_uri_segment($mod_offset_index)) ? (int) fuel_uri_segment($mod_offset_index) : 0;
		
		$posted = array();
		if (!empty($_POST))
		{

			$posted['search_term'] = $this->input->post('search_term');
			$posted_vars = array('col', 'order', 'limit', 'offset', 'precedence', 'view_type');
			foreach($posted_vars as $val)
			{
				if ($this->input->post($val)) $posted[$val] = $this->input->post($val, TRUE);
			}
			
			// custom module filters
			$extra_filters = array();
			
			foreach($this->filters as $key => $val)
			{
				if (isset($_POST[$key]))
				{
					$posted[$key] = $this->input->post($key, TRUE);
					$this->filters[$key]['value'] = $posted[$key];
					$extra_filters[$key] = $posted[$key];
				}
			}
			$posted['extra_filters'] = $extra_filters;
			
		}
		
		//$params = array_merge($defaults, $page_state, $uri_params, $posted);
		$params = array_merge($defaults, $page_state, $uri_params, $posted);
		
		// reset offset if you apply a filter (via POST and not ajax)
		if (!empty($_POST) and !is_ajax())
		{
			$params['offset'] = 0;
		}
		
		if ($params['search_term'] == lang('label_search')) $params['search_term'] = NULL;
		/* PROCESS PARAMS END */
		
		return $params;
	}
	
	function items_tree()
	{
		// tree
		if (method_exists($this->model, 'tree') AND is_ajax())
		{
			$params = $this->_list_process();
			
			$this->load->library('menu');
			$this->menu->depth = NULL; // as deep as it goes
			$this->menu->use_titles = FALSE;
			$this->menu->root_value = 0;
			$this->model->add_filters($params['extra_filters']);
			$menu_items = $this->model->tree();
			if (!empty($menu_items))
			{
				$output = $this->menu->render($menu_items);
			}
			else
			{
				$output = '<div style="text-align: center">'.lang('no_data').'</div>';
			}
			$this->output->set_output($output);
		}
		
	}
	
	function items_precedence()
	{
		if (is_ajax() AND !empty($_POST['data_table']) AND !empty($this->precedence_col))
		{
			if (is_array($_POST['data_table']))
			{
				$i = 0;
				foreach($_POST['data_table'] as $row)
				{
					if (!empty($row))
					{
						$values = array($this->precedence_col => $i);
						$where = array('id' => $row);
						$this->model->update($values, $where);
					}
					$i++;
				}
			}
		}
	}
	
	function create($redirect = TRUE)
	{
		$id = NULL;
		if (!$this->fuel_auth->module_has_action('create')) show_404();
		
		if (isset($_POST[$this->model->key_field()])) // check for dupes
		{
			$this->model->on_before_post();
		
			$posted = $this->_process();

			// set publish status to no if you do not have the ability to publish
			if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
			{
				$posted['published'] = 'no';
				$posted['active'] = 'no';
			}
			
			$model = $this->model;
			
			// run before_create hook
			$this->_run_hook('before_create', $posted);
			
			// run before_save hook
			$this->_run_hook('before_save', $posted);
			
			// reset dup id
			if ($_POST[$this->model->key_field()] == 'dup')
			{
				$_POST[$this->model->key_field()] = '';
			}
			else if ($id = $this->model->save($posted))
			{
				if (empty($id))
				{
					show_error('Not a valid ID returned to save template variables');
				}
				
				// process $_FILES
				if (!$this->_process_uploads($posted))
				{
					$this->session->set_flashdata('error', get_error());
					redirect(fuel_uri($this->module_uri.'/edit/'.$id));
				}
				
				// add id value to the posted array
				if (!is_array($this->model->key_field()))
				{
					$posted[$this->model->key_field()] = $id;
				}
				
				$this->model->on_after_post($posted);
				
				if (!$this->model->is_valid())
				{
					add_errors($this->model->get_errors());
				}
				else
				{
					// archive data
					$archive_data = $this->model->cleaned_data();
					$archive_data[$this->model->key_field()] = $id;
					if ($this->archivable) $this->model->archive($id, $archive_data);
					$data = $this->model->find_one_array(array($this->model->table_name().'.'.$this->model->key_field() => $id));

					// run after_create hook
					$this->_run_hook('after_create', $data);
					
					// run after_save hook
					$this->_run_hook('after_save', $posted);
					
					if (!empty($data))
					{
						
						$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
						$this->logs_model->logit($msg);
						$this->_clear_cache();
						$url = 'fuel/'.$this->module_uri.'/edit/'.$id;
						if ($redirect === TRUE)
						{
							$this->session->set_flashdata('success', lang('data_saved'));
							redirect(fuel_uri($this->module_uri.'/edit/'.$id));
						}
					}
				}
			}
		}
		$vars = $this->_form();
		$this->_render($this->views['create_edit'], $vars);
		return $id;
	}

	function edit($id = NULL, $redirect = TRUE)
	{
		if (empty($id) OR !$this->fuel_auth->module_has_action('save')) show_404();

		if ($this->input->post($this->model->key_field()))
		{
			$this->model->on_before_post();
			
			$posted = $this->_process();
			
			// run before_edit hook
			$this->_run_hook('before_edit', $posted);
			
			// run before_save hook
			$this->_run_hook('before_save', $posted);
			
			if ($this->model->save($posted))
			{
				// process $_FILES
				if (!$this->_process_uploads($posted))
				{
					$this->session->set_flashdata('error', get_error());
					redirect(fuel_uri($this->module_uri.'/edit/'.$id));
				}
				
				$this->model->on_after_post($posted);
				
				if (!$this->model->is_valid())
				{
					add_errors($this->model->get_errors());
				}
				else
				{
					// archive data
					$archive_data = $this->model->cleaned_data();
					if ($this->archivable) $this->model->archive($id, $archive_data);
					$data = $this->model->find_one_array(array($this->model->table_name().'.'.$this->model->key_field() => $id));
					
					// run after_edit hook
					$this->_run_hook('after_edit', $data);

					// run after_save hook
					$this->_run_hook('after_save', $data);

					$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
					$this->logs_model->logit($msg);
					$this->_clear_cache();
					if ($redirect === TRUE)
					{
						$this->session->set_flashdata('success', lang('data_saved'));
						redirect(fuel_uri($this->module_uri.'/edit/'.$id));
					}
				}
			}
		}
		$vars = $this->_form($id);
		$this->_render($this->views['create_edit'], $vars);
	}
	
	protected function _process()
	{
		$this->load->helper('security');

		// filter placeholder $_POST values 
		$callback = create_function('$matches', '
			if (isset($_POST[$matches["2"]]))
			{
				$str = $matches[1].$_POST[$matches["2"]].$matches[3];
			}
			else
			{
				$str = $matches[0];
			}
			return $str;
		');
		
		// first loop through and create simple non-namespaced $_POST values if they don't exist for convenience'
		foreach($_POST as $key => $val)
		{
			$tmp_key = end(explode('--', $key));
			$_POST[$tmp_key] = $val;
		}
		
		// now loop through and do any substitution
		foreach($_POST as $key => $val)
		{
			if (is_string($val))
			{
				$_POST[$key] = preg_replace_callback('#(.*){(.+)\}(.*)#U', $callback, $val);
			}
		}
		
		if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
		{
			unset($_POST['published']);
		}
		
		// set key_field if it is not id
		if (!empty($_POST['id']) AND $this->model->key_field() != 'id')
		{
			$_POST[$this->model->key_field()] = $_POST['id'];
		}
		
		// sanitize input if set in module configuration
		$posted = $this->_sanitize($_POST);
		
		// loop through uploaded files
		if (!empty($_FILES))
		{
			foreach ($_FILES as $file => $file_info)
			{
				if ($file_info['error'] == 0)
				{
					$posted[$file] = $file_info['name'];
					
					$file_tmp = current(explode('___', $file));
					$field_name = $file_tmp;

					// if there is a field with the suffix of _upload, then we will overwrite that posted value with this value
					if (substr($file_tmp, ($file_tmp - 7)) == '_upload') {
						$field_name = substr($file_tmp, 0, ($file_tmp - 7));
					}

					if (isset($posted[$file_tmp.'_filename']))
					{
						// get file extension
						$path_info = pathinfo($file_info['name']);
						$field_value = $posted[$file_tmp.'_filename'].'.'.$path_info['extension'];
					}
					else
					{
						$field_value = $file_info['name'];
					}
					// FIX ME....
					// foreach($_POST as $key => $val)
					// {
					// 	$tmp_key = end(explode('--', $key));
					// 	$_POST[$tmp_key] = preg_replace('#(.*){(.+)\}(.*)#e', "'\\1'.\$_POST['\\2'].'\\3'", $val);
					// }
					
					if (strpos($field_value, '{') !== FALSE )
					{
						$field_value = preg_replace('#(.*){(.+)\}(.*)#e', "'\\1'.\$posted['\\2'].'\\3'", $field_value);
					}

					// set both values for the namespaced and non-namespaced... make them underscored and lower cased
					$tmp_field_name = end(explode('--', $field_name));
					$posted[$tmp_field_name] = url_title($field_value, 'underscore', TRUE);
					$posted[$field_name] = url_title($field_value, 'underscore', TRUE);
				}
			}
		}
		return $posted;
	}
	
	protected function _sanitize($data)
	{
		$posted = $data;
		
		if (!empty($this->sanitize_input))
		{
			// functions that are valid for sanitizing
			$valid_funcs = $this->config->item('module_sanitize_funcs', 'fuel');
			
			if ($this->sanitize_input === TRUE)
			{
				$posted = xss_clean($data);
			}
			else
			{
				// force to array to normalize
				$sanitize_input = (array) $this->sanitize_input;
				
				if (is_array($data))
				{
					foreach($data as $key => $post)
					{
						if (is_array($post))
						{
							$posted[$key] = $this->_sanitize($data[$key]);
						}
						else
						{
							// loop through sanitzation functions 
							foreach($sanitize_input as $func)
							{
								$func = (isset($valid_funcs[$func])) ? $valid_funcs[$func] : FALSE;
								if ($func)
								{
									$posted[$key] = $func($posted[$key]);
								}
							}
						}
					}
				}
				else
				{
					// loop through sanitzation functions 
					foreach($sanitize_input as $key => $val)
					{
						$func = (isset($valid_funcs[$val])) ? $valid_funcs[$val] : FALSE;
						if ($func)
						{
							$posted = $func($posted);
						}
					}
				}
			}
		}

		return $posted;
	}
	
	// seperated to make it easier in subclasses to use the form without rendering the page
	protected function _form($id = NULL, $fields = NULL, $log_to_recent = TRUE, $display_normal_submit_cancel = TRUE)
	{
		$this->load->library('form_builder');
//		$this->model->set_return_method('array');

		$model = $this->model;
		$this->js_controller_params['method'] = 'add_edit';
		$this->js_controller_params['linked_fields'] = $this->model->linked_fields;
		
		// get saved data
		$saved = array();
		
		if (!empty($id) AND $id != 'create') 
		{
			
			$edit_method = $this->edit_method;
			if ($edit_method != 'find_one_array')
			{
				$saved = $this->model->$edit_method($id);
			}
			else
			{
				$saved = $this->model->$edit_method(array($this->model->table_name().'.'.$this->model->key_field() => $id));
			}
			if (empty($saved)) show_404();
		}
		
		// create fields... start with the table info and go from there
		if (empty($fields)) 
		{
			$fields = (!empty($saved)) ? $this->model->form_fields($saved) : $this->model->form_fields($_POST);

			// set published/active to hidden since setting this is an buttton/action instead of a form field
			//$fields['published']['type'] = 'hidden';
			if (is_array($fields))
			{
				if (!empty($saved['published'])) $fields['published']['value'] = $saved['published'];

				if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
				{
					unset($fields['published']);
				}
			}
		}
		
		if (is_array($fields))
		{
			$field_values = (!empty($_POST)) ? $_POST : $saved;

			$this->form_builder->form->validator = &$this->model->get_validation();
			
			// not inline edited
			if ($display_normal_submit_cancel)
			{
				$this->form_builder->submit_value = lang('btn_save');
				$this->form_builder->cancel_value = lang('btn_cancel');
			}
			
			// inline editied
			else
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
			$vars['form'] = $this->form_builder->render();
		}
		else
		{
			$vars['form'] = $fields;
		}

		// other variables
		$vars['id'] = $id;
		$vars['data'] = $saved;
		$vars['action'] =  (!empty($saved[$this->model->key_field()])) ? 'edit' : 'create';
		$vars['versions'] = $this->archives_model->options_list($id, $this->model->table_name());
		$vars['others'] = $this->model->get_others($this->display_field, $id);

		preg_match_all('#\{(.+)\}+#U', $this->preview_path, $matches);
		if (!empty($matches[1]))
		{
			foreach($matches[1] as $match)
			{
				if (!empty($vars['data'][$match]))
				{
					$this->preview_path = str_replace('{'.$match.'}', $vars['data'][$match], $this->preview_path);
				}
			}
		}
		
		// active or publish fields
		if (isset($saved['published']))
		{
			$vars['publish'] = (!empty($saved['published']) AND is_true_val($saved['published'])) ? 'unpublish' : 'publish';
		}
		
		if (isset($saved['active']))
		{
			$vars['activate'] = (!empty($saved['active']) AND is_true_val($saved['active'])) ? 'deactivate' : 'activate';
		}
		$vars['module'] = $this->module;

		$actions = $this->load->module_view(FUEL_FOLDER, '_blocks/module_create_edit_actions', $vars, TRUE);
		$vars['actions'] = $actions;
		
		$vars['error'] = $this->model->get_errors();
		$notifications = $this->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
		$vars['notifications'] = $notifications;
		
		// do this after rendering so it doesn't render current page'
		if (!empty($vars['data'][$this->display_field]) AND $log_to_recent) 
		{
			$this->_recent_pages($this->uri->uri_string(), $this->module_name.': '.$vars['data'][$this->display_field], $this->module);
		}
		return $vars;
	}
	
	function delete($id = NULL)
	{
		if (!$this->fuel_auth->has_permission($this->permission, 'delete')) 
		{
			show_error(lang('error_no_permissions'));
		}
		
		if (!empty($_POST['id']))
		{
			$posted = explode('|', $this->input->post('id'));
			
			
			// run before_delete hook
			$this->_run_hook('before_delete', $posted);
			
			// Flags
			$any_success = $any_failure = FALSE;
			foreach ($posted as $id)
			{
				if ($this->model->delete(array($this->model->key_field() => $id)))
				{
					$any_success = TRUE;
				}
				else
				{
					$any_failure = TRUE;
				}
			}
			// set a success delete message
			if ($any_success)
			{
				$this->session->set_flashdata('success', lang('data_deleted'));
			}
			
			// set an error delete message
			if ($any_failure)
			{
				// first try to get an error added in model by $this->add_error('...')
				$msg = $this->model->get_validation()->get_last_error();
				
				// if there is none like that, lets use default message
				if (is_null($msg))
				{
					$msg = lang('data_not_deleted');
				}

				$this->session->set_flashdata('error', $msg);
			}
			
			// run after_delete hook
			$this->_run_hook('after_delete', $posted);
			
			$this->_clear_cache();
			$this->logs_model->logit('Multiple module '.$this->module.' data deleted');
			redirect(fuel_uri($this->module_uri));
		}
		else
		{
			$this->js_controller_params['method'] = 'deleteItem';
			
			$vars = array();
			if (!empty($_POST['delete']) AND is_array($_POST['delete'])) 
			{
				$data = array();
				foreach($this->input->post('delete') as $key => $val)
				{
					$d = $this->model->find_by_key($key, 'array');
					if (!empty($d)) $data[] = $d[$this->display_field];
				}
				$vars['id'] = implode('|', array_keys($_POST['delete']));
				$vars['title'] = implode(', ', $data);
			}
			else
			{
				$data = $this->model->find_by_key($id, 'array');
				$vars['id'] = $id;
				if (isset($data[$this->display_field])) $vars['title'] = $data[$this->display_field];
			}
			if (empty($data)) show_404();
			$vars['error'] = $this->model->get_errors();
			$vars['notifications'] = $this->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
			$this->_render($this->views['delete'], $vars);
		}
	}
	
	function restore()
	{
		if ($this->input->post('version') AND $this->input->post('ref_id'))
		{
			if (!$this->model->restore($this->input->post('ref_id'), $this->input->post('version')))
			{
				$msg = lang('module_restored', $this->module_name);
				$this->logs_model->logit($msg);
				
				$this->session->set_flashdata('error', $this->model->get_validation()->get_last_error());
			}
			else
			{
				$this->session->set_flashdata('success', lang('module_restored_success'));
			}
			redirect(fuel_uri($this->module_uri.'/edit/'.$this->input->post('ref_id')));
		}
		else
		{
			show_404();
		}
	}
	
	function view($id = null)
	{
		if (!empty($this->preview_path))
		{
			$data = $this->model->find_one_array(array($this->model->table_name().'.'.$this->model->key_field() => $id));

			$url = $this->preview_path;
		
			// get the keys from the preview path
			preg_match_all('#\{(.+)\}#U', $this->preview_path, $matches, PREG_PATTERN_ORDER);
			if (!empty($matches[1]))
			{
				foreach($matches[1] as $match)
				{
					$url = str_replace('{'.$match.'}', $data[$match], $url);
				}
			}

			// change the last page to be the referrer
			$last_page = substr($_SERVER['HTTP_REFERER'], strlen(site_url()));
			$this->_last_page($last_page);
			redirect($url);
		}
		else
		{
			show_error(lang('no_preview_path'));
		}
	}

	// used in list view to quickly unpublish (if they have permisison)
	function unpublish($id = null)
	{
		$this->_publish_unpublish($id, 'unpublish');
	}

	// used in list view to quickly publish (if they have permisison)
	function publish($id = null)
	{
		$this->_publish_unpublish($id, 'publish');
	}
	
	// reduce code by creating this shortcut function for the unpublish/publish
	protected function _publish_unpublish($id, $pub_unpub)
	{
		if (!$this->fuel_auth->module_has_action('save')) return false;
		if (empty($id)) $id = $this->input->post($this->model->key_field());
		
		if ($id)
		{
			//$this->model->set_return_method('array');
			$save = $this->model->find_by_key($id, 'array');
			if ($this->fuel_auth->has_permission($this->permission, 'publish') AND !empty($save))
			{
				if ($pub_unpub == 'publish')
				{
					$save['published'] = 'yes';
					$save['active'] = 'yes';
				}
				else
				{
					$save['published'] = 'no';
					$save['active'] = 'no';
				}
				
				if ($this->model->save($save))
				{
					// log it
					$data = $this->model->find_by_key($id, 'array');
					$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
					$this->logs_model->logit($msg);
				}
				else
				{
					$this->output->set_output(lang('error_saving'));
				}
			}
		}
		
		if (is_ajax())
		{
			$this->output->set_output($pub_unpub.'ed');
		}
		else
		{
			$this->items();
		}
	}
	
	function inline_edit($id, $column = null)
	{
		if (!$this->fuel_auth->module_has_action('save') OR !$this->fuel_auth->module_has_action('create')) return false;
		
		if (!empty($_POST))
		{
			$posted = $this->_process();
			
			if (!empty($posted['__fuel_inline_action__']) AND $posted['__fuel_inline_action__'] == 'delete' AND !empty($posted['id']))
			{
				$this->model->delete(array($this->model->key_field() => $posted['id']));
				$this->_clear_cache();
				$str = (is_ajax()) ? '' : '<script type="text/javascript">parent.location.reload(true);</script>';
				$this->output->set_output($str);
				return;
			}
			else
			{
				$after_post = $posted;
				if ($id === 'create')
				{
					unset($posted['id']);
				}
				if (!$this->fuel_auth->has_permission($this->permission, 'publish'))
				{
					unset($values['published']);
				}
				
				// run hook
				if ($id === 'create')
				{
					$this->_run_hook('before_create', $posted);
				}
				else
				{
					$this->_run_hook('before_edit', $posted);
				}
				
				$saved_id = $this->model->save($posted);

				if (!$this->_process_uploads())
				{
					$this->model->add_error($this->session->flashdata('error'));
				}
				
				$after_post[$this->model->key_field()] = $saved_id;
				$this->model->on_after_post($after_post);
				
				if ($saved_id && $this->model->is_valid())
				{
					
					// archive data
					$archive_data = $this->model->cleaned_data();
					$archive_data[$this->model->key_field()] = $saved_id;
					if ($this->archivable) $this->model->archive($id, $archive_data);
					
					// run hook
					if ($id === 'create')
					{
						$this->_run_hook('after_create', $archive_data);
					}
					else
					{
						$this->_run_hook('after_edit', $archive_data);
					}
					
					
					$this->_clear_cache();
					$str = (is_ajax()) ? $saved_id : '<script type="text/javascript">parent.location.reload(true);</script>';
					$this->output->set_output($str);
				}
				else
				{
					//$this->output->set_output('<error>'.lang('error_saving').'</error>');
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
			$fields = $this->model->form_fields(array());
			
			if ($id === 'create')
			{
				$id = NULL;
			}
			
			if (!is_int($column) AND isset($fields[$column]))
			{
				// load it here again so that we can set the use_label
				$this->load->library('form_builder');
				//$this->form_builder->hidden = (array) $this->model->key_field();
				$this->form_builder->label_colons = FALSE;
				$this->form_builder->question_keys = array();
				$this->form_builder->css_class = 'inline_form';
				$single_field = array();
				$single_field[$column] = $fields[$column];
				$single_field[$column]['label'] = ' ';
				$single_field['id'] = array('type' => 'hidden', 'value' => $id);
				$vars = $this->_form($id, $single_field, TRUE, FALSE);
			}
			else
			{
				$vars = $this->_form($id, NULL, TRUE, FALSE);
			}
			
			$vars['linked_fields'] = $this->model->linked_fields;
			
			$this->load->module_view(FUEL_FOLDER, '_layouts/module_inline_edit', $vars);
		}
	}
	
	function refresh_field()
	{
		if (is_ajax() AND !empty($_POST))
		{
			$fields = $this->model->form_fields();
			$field = $this->input->post('field', TRUE);
			if (!isset($fields[$field])) return;
			
			$field_id = $this->input->post('field_id', TRUE);
			$values = $this->input->post('values', TRUE);
			$selected = $this->input->post('selected', TRUE);
			
			$this->load->library('form_builder');
			
			// for multi select
			if (is_array($values))
			{
				$selected = (array) $selected;
				$selected = array_merge($values, $selected);
			}
			
			if (!empty($selected)) $fields[$field]['value'] = $selected;
			$fields[$field]['name'] = $field_id;
			
			// if the field is an ID, then we will do a select instead of a text field
			if (isset($fields[$this->model->key_field()]))
			{
				$fields['id']['type'] = 'select';
				$fields['id']['options'] = $this->model->options_list();
			}

			$output = $this->form_builder->create_field($fields[$field]);
			$this->output->set_output($output);
		}
	}
	
	function process_linked()
	{
		if (!empty($_POST))
		{
			$master_field = $this->input->post('master_field', FALSE);
			$master_value = $this->input->post('master_value', FALSE);
			$slave_field = $this->input->post('slave_field', FALSE);
			$values = array(
				$master_field => $master_value,
				$slave_field => '' // blank so we can process
			);
			$processed = $this->model->process_linked($values);
			if (!empty($processed[$slave_field]))
			{
				$this->output->set_output($processed[$slave_field]);
			}
		}
		
	}
	
	function ajax($method = NULL)
	{
		// must not be empty and must start with find_ (... don't want to access methods like delete)
		if (is_ajax())
		{
			// append ajax to the method name... to prevent any conflicts with default methods
			$method = 'ajax_'.$method;
			$this->uri->init_get_params();
			$params = $_GET;
			
			if (!method_exists($this->model, $method))
			{
				show_error(lang('error_invalid_method'));
			}
			
			$results = $this->model->$method($params);
			
			if (is_string($results))
			{
				$this->output->set_output($results);
			}
			else
			{
				$this->output->set_header('Cache-Control: no-cache, must-revalidate');
				$this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				$this->output->set_header('Last-Modified: '. gmdate('D, d M Y H:i:s').'GMT');
				$this->output->set_header('Content-type: application/json');
				$output = json_encode($results);
				print($output);
			}
			
		}
	}
	
	protected function _clear_cache()
	{
		// reset cache for that page only
		if ($this->clear_cache_on_save) 
		{
			$CI =& get_instance();
			$this->load->library('cache');
			$cache_group = $this->config->item('page_cache_group', 'fuel');
			$this->cache->remove_group($cache_group);
		}
	}
	
	protected function _allow_action($action)
	{
		return in_array($action, $this->item_actions);
	}

	protected function _process_uploads($posted = NULL)
	{
		if (empty($posted)) $posted = $_POST;

		$this->lang->load('upload');
		
		$errors = FALSE;
		if (!empty($_FILES))
		{
			$this->load->module_model(FUEL_FOLDER, 'assets_model');
			$this->load->library('upload');
			$this->load->helper('directory');
						
			$config['max_size']	= $this->config->item('assets_upload_max_size', 'fuel');
			$config['max_width']  = $this->config->item('assets_upload_max_width', 'fuel');
			$config['max_height']  = $this->config->item('assets_upload_max_height', 'fuel');
			
			// loop through all the uploaded files
			foreach($_FILES as $file => $file_info)
			{
				if ($file_info['error'] == 0)
				{
					// continue processing
					$filename = $file_info['name'];
					$filename_arr = explode('.', $filename);
					$filename_no_ext = $filename_arr[0];
					$ext = end($filename_arr);
					$test_multi = explode('___', $file);
					$is_multi = (count($test_multi) > 1);
					$multi_root = $test_multi[0];
					
					// loop through all the allowed file types that are accepted for the asset directory
					foreach($this->assets_model->get_dir_filetypes() as $key => $val)
					{
						$file_types = explode('|', strtolower($val));
						if (in_array(strtolower($ext), $file_types))
						{
							$asset_dir = $key;
							break;
						}
					}
					if (!empty($asset_dir))
					{
						// upload path
						if (!empty($posted[$file.'_path']))
						{
							$config['upload_path'] = $posted[$file.'_path'];
						}
						else if (!empty($posted[$multi_root.'_path']))
						{
							$config['upload_path'] = $posted[$multi_root.'_path'];
						}
						else
						{
							$config['upload_path'] = (isset($upload_path)) ? $upload_path : assets_server_path().$asset_dir.'/';
						}
						
						if (!is_dir($config['upload_path']) AND $this->config->item('assets_allow_subfolder_creation', 'fuel'))
						{
							// will recursively create folder
							//$old = umask(0)
							@mkdir($config['upload_path'], 0777, TRUE);
							if (!file_exists($config['upload_path']))
							{
								$errors = TRUE;
								add_error(lang('upload_not_writable'));
								$this->session->set_flashdata('error', lang('upload_not_writable'));
							}
							else
							{
								chmodr($config['upload_path'], 0777);
							}
							//umask($old);
						} 
						
						// overwrite
						if (isset($posted[$file.'_overwrite']))
						{
							$config['overwrite'] = (is_true_val($posted[$file.'_overwrite']));
						}
						else if (isset($posted[$multi_root.'_overwrite']))
						{
							$config['overwrite'] = (is_true_val($posted[$multi_root.'_overwrite']));
						}
						else
						{
							$config['overwrite'] = TRUE;
						}
						
						// filename... lower case it for consistency
						$config['file_name'] = url_title($filename, 'underscore', TRUE);
						if (!empty($posted[$file.'_filename']))
						{
							$config['file_name'] = $posted[$file.'_filename'].'.'.$ext;
						}
						else if (!empty($posted[$multi_root.'_filename']))
						{
							$config['file_name'] = $posted[$multi_root.'_filename'].'.'.$ext;
						}
						
						$config['allowed_types'] = ($this->assets_model->get_dir_filetype($asset_dir)) ? $this->assets_model->get_dir_filetype($asset_dir) : 'jpg|jpeg|png|gif';
						$config['remove_spaces'] = TRUE;

						//$config['xss_clean'] = TRUE; // causes problem with image if true... so we use the below method
						$tmp_file = file_get_contents($file_info['tmp_name']);
						if ($this->sanitize_images AND is_image_file($config['file_name']) AND xss_clean($tmp_file, TRUE) === FALSE)
						{
							$errors = TRUE;
							add_error(lang('upload_invalid_filetype'));
							$this->session->set_flashdata('error', lang('upload_invalid_filetype'));
						}
						
						if (!$errors)
						{
							$this->upload->initialize($config);
							if (!$this->upload->do_upload($file))
							{
								$errors = TRUE;
								add_error($this->upload->display_errors('', ''));
								$this->session->set_flashdata('error', $this->upload->display_errors('', ''));
							}
							else
							{
								// saves data about successfully uploaded file
								$this->upload_data[] = $this->upload->data();
							}
						}
					}
					else
					{
						$errors = TRUE;
						add_error(lang('upload_invalid_filetype'));
					}
				}
			}
		}
		
		// transfers data about successfully uploaded file to the model
		if (isset($this->model->upload_data))
		{
			$this->model->upload_data = $this->upload_data;
		}
		return !$errors;
	}
	
	protected function _run_hook($hook, $params = array())
	{
		// call hook
		$hook_name = $hook.'_'.$this->module;
		return $GLOBALS['EXT']->_call_hook($hook_name, $params);
	}
}