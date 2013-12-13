<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Module extends Fuel_base_controller {
	
	public $module_obj; // the module object
	public $module = ''; // the name of the module
	public $uploaded_data = array(); // reference to the uploaded data

	protected $_orig_post = array(); // used for reference
	
	function __construct($validate = TRUE)
	{
		parent::__construct($validate);

		$this->load->module_model(FUEL_FOLDER, 'fuel_archives_model');
		if (empty($this->module))
		{
			$this->module = fuel_uri_segment(1);
		}

		if (empty($this->module))
		{
			show_error(lang('cannot_determine_module'));
		}
		
		$params = array();
		if ($this->fuel->modules->exists($this->module, FALSE))
		{
			$this->module_obj = $this->fuel->modules->get($this->module, FALSE);
			$params = $this->module_obj->info();
		}
		else if ($this->fuel->modules->exists($this->module.'_'.fuel_uri_segment(2), FALSE))
		{
			// if it is a module with multiple controllers, then we'll check first and second FUEL segment with an underscore'
			$this->module = $this->module.'_'.fuel_uri_segment(2);
			if ($this->fuel->modules->exists($this->module, FALSE))
			{
				$this->module_obj = $this->fuel->modules->get($this->module, FALSE);
				$params = $this->module_obj->info();
			}
		}
		else if ($this->fuel->modules->exists(fuel_uri_segment(2), FALSE))
		{
			$this->module = fuel_uri_segment(2);
			$this->module_obj = $this->fuel->modules->get($this->module, FALSE);

			if ($this->module_obj)
			{
				$mod_name = $this->module_obj->name();	
			}
			
			if (empty($mod_name))
			{
				show_error(lang('error_missing_module', fuel_uri_segment(1)));
			}
			unset($mod_name);
			$params = $this->module_obj->info();
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
				$this->load->module_language($lang_module, $lang_file, $this->fuel->auth->user_lang());
			}
			else
			{
				$this->load->language($this->language);
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
		
		// get the model name
		$model_parts = explode('/', $this->model_name);
		$model = end($model_parts);
		
		
		// set the module_uri
		if (empty($this->module_uri)) $this->module_uri = $this->module;
		
		$this->js_controller_params['module'] = $this->module_uri;
		

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
		
		$this->fuel->admin->load_js_localized($params['js_localized']);

		if (!empty($this->permission) AND $validate)
		{
			$this->_validate_user($this->permission);
		}
		
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays the list (table) view
	 *
	 * @access	public
	 * @return	void
	 */	
	function index()
	{
		$this->items();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays the list (table) view
	 *
	 * @access	public
	 * @return	void
	 */	
	function items()
	{
		$this->load->library('data_table');
	
		// check the model for a filters method as well and merge it with the property value
		// added per http://www.getfuelcms.com/forums/discussion/760/filter-dropdown/#Item_2
		if (method_exists($this->model, 'filters'))
		{
			$this->filters = array_merge($this->filters, $this->model->filters($this->filters));
		}
	
		// set the language dropdown if there is a language column
		if ($this->fuel->language->has_multiple() AND !empty($this->language_col) AND method_exists($this->model, 'get_languages'))
		{
			$languages = $this->model->get_languages($this->language_col);
			$first_option = current($languages);
			if (!empty($languages) AND (is_string($first_option) OR (is_array($first_option)) AND count($first_option) > 1))
			{
				$lang_filter = array('type' => 'select', 'options' => $languages, 'label' => lang('label_language'), 'first_option' => lang('label_select_a_language'));
				$this->filters[$this->language_col.'_equal'] = $lang_filter;
				$this->model->add_filter_join($this->language_col.'_equal', 'and');
			}
		}
		
		$params = $this->_list_process();
		
		// save page state
		$this->fuel->admin->save_page_state($params);
		
		
		// create search filter
		$filters[$this->display_field] = trim($params['search_term']);
		
		// sort of hacky here... to make it easy for the model to just filter on the search term (like the users model)
		$this->model->filter_value = trim($params['search_term']);
		foreach($this->filters as $key => $val)
		{
			$filters[$key] = $params[$key];
			if (!empty($val['filter_join']))
			{
				if (!is_array($this->model->filter_join[$key]))
				{
					settype($this->model->filter_join, 'array');
				}
				$this->model->filter_join[$key] = $val['filter_join'];
			}
		}
		
		// set model filters before pagination and setting table data
		if (method_exists($this->model, 'add_filters'))
		{
			$this->model->add_filters($filters);
		}
	
		// to prevent it from being called unecessarility with ajax
		if (!is_ajax())
		{
			$this->config->set_item('enable_query_strings', FALSE);
		
			// pagination
			$query_str_arr = $this->input->get(NULL, TRUE);
			unset($query_str_arr['offset']);
			$query_str = (!empty($query_str_arr)) ? http_build_query($query_str_arr) : '';
		
			$config['base_url'] = fuel_url($this->module_uri).'/items/?'.$query_str;
			$uri_segment = 4 + (count(explode('/', $this->module_uri)) - 1);
			$config['total_rows'] = $this->model->list_items_total();
			$config['uri_segment'] = fuel_uri_index($uri_segment);
			$config['per_page'] = (int) $params['limit'];
			$config['query_string_segment'] = 'offset';
			$config['page_query_string'] = TRUE;
			$config['num_links'] = 5;
		
			$config['prev_link'] = lang('pagination_prev_page');
			$config['next_link'] = lang('pagination_next_page');
			$config['first_link'] = lang('pagination_first_link');
			$config['last_link'] = lang('pagination_last_link');
		
			// must reset these in case a config file has something different
			$config['full_tag_open'] = NULL;
			$config['full_tag_close'] = NULL;
			$config['full_tag_close'] = NULL;
			$config['num_tag_open'] = '&nbsp;';
			$config['num_tag_close'] = NULL;
			$config['cur_tag_open'] = '&nbsp;<strong>';
			$config['cur_tag_close'] = '</strong>';
			$config['next_tag_open'] = '&nbsp;';
			$config['next_tag_close'] = '&nbsp;';
			$config['prev_tag_open'] = '&nbsp;';
			$config['prev_tag_close'] = NULL;
			$config['first_tag_open'] = '&nbsp;';
			$config['first_tag_close'] = '&nbsp;';
			$config['last_tag_open'] = NULL;
			$config['last_tag_close'] = NULL;
			$this->pagination->initialize($config);

			if (method_exists($this->model, 'tree'))
			{
				//$vars['tree'] = "Loading...\n<ul></ul>\n";
				$vars['tree'] = "\n<ul></ul>\n";
			}
			
			// reset offset if total rows is less then limit
			if ($config['total_rows'] < $params['limit'])
			{
				$params['offset'] = 0;
			}

			
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

			$has_edit_permission = $this->fuel->auth->has_permission($this->permission, "edit") ? '1' : '0';
			$has_delete_permission = $this->fuel->auth->has_permission($this->permission, "delete") ? '1' : '0';
			
			// set data table actions... look first for item_actions set in the fuel_modules
			$edit_func = '
			$CI =& get_instance();
			$link = "";';
			if ($has_edit_permission)
			{
				$edit_func .= 'if (isset($cols[$CI->model->key_field()]))
				{
					$url = fuel_url("'.$this->module_uri.'/edit/".$cols[$CI->model->key_field()]);
					$link = "<a href=\"".$url."\">".lang("table_action_delete")."</a>";
					$link .= " <input type=\"checkbox\" name=\"delete[".$cols[$CI->model->key_field()]."]\" value=\"1\" id=\"delete_".$cols[$CI->model->key_field()]."\" class=\"multi_delete\"/>";
				}';	
			}
			$edit_func .= 'return $link;';
			
			$edit_func = create_function('$cols', $edit_func);
			
			
			// set data table actions... look first for item_actions set in the fuel_modules
			$delete_func = '
			$CI =& get_instance();
			$link = "";';
			if ($has_delete_permission)
			{
				$delete_func .= 'if (isset($cols[$CI->model->key_field()]))
				{
					$url = fuel_url("'.$this->module_uri.'/delete/".$cols[$CI->model->key_field()]);
					$link = "<a href=\"".$url."\">".lang("table_action_delete")."</a>";
					$link .= " <input type=\"checkbox\" name=\"delete[".$cols[$CI->model->key_field()]."]\" value=\"1\" id=\"delete_".$cols[$CI->model->key_field()]."\" class=\"multi_delete\"/>";
				}';				
			}

			$delete_func .= 'return $link;';
			
			$delete_func = create_function('$cols', $delete_func);
			
			foreach($this->table_actions as $key => $val)
			{
				if (!is_int($key)) 
				{
					$action_type = 'url';
					$action_val = $this->table_actions[$key];
					$attrs = array();
					if (is_array($val))
					{
						if (isset($val['url']))
						{
							$action_type = 'url';
							$action_val = $val['url'];
							if (isset($val['attrs']))
							{
								$attrs = $val['attrs'];
							}
						}
						else
						{
							$action_type = key($val);
							$action_val = current($val);
						}
						$attrs = (isset($val['attrs'])) ? $val['attrs'] : array();
					}
					$this->data_table->add_action($key, $action_val, $action_type, $attrs);
				}
				else if (strtoupper($val) == 'EDIT')
				{
					if ($this->fuel->auth->has_permission($this->permission, "edit"))
					{
						$action_url = fuel_url($this->module_uri.'/edit/{'.$this->model->key_field().'}');
						$this->data_table->add_action(lang('table_action_edit'), $action_url, 'url');
					}
				}
				else if (strtoupper($val) == 'DELETE')
				{
					$this->data_table->add_action($val, $delete_func, 'func');
				}
				else
				{
					if (strtoupper($val) != 'VIEW' OR (!empty($this->preview_path) AND strtoupper($val) == 'VIEW'))
					{
						$action_name = lang('table_action_'.strtolower($val));
						if (empty($action_name)) $action_name = $val;
						$action_url = fuel_url($this->module_uri.'/'.strtolower($val).'/{'.$this->model->key_field().'}');
						$this->data_table->add_action($action_name, $action_url, 'url');
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

			// Key and boolean fields are data only
//			$this->data_table->only_data_fields = array_merge(array($this->model->key_field()), $this->model->boolean_fields);

			$this->data_table->auto_sort = TRUE;
			$this->data_table->actions_field = 'last';
			$this->data_table->no_data_str = lang('no_data');
			$this->data_table->lang_prefix = 'form_label_';
			$this->data_table->row_id_key = $this->model->key_field();
			
			$boolean_fields = $this->model->boolean_fields;
			if (!in_array('published', $boolean_fields)) $boolean_fields[] = 'published';
			if (!in_array('active', $boolean_fields)) $boolean_fields[] = 'active';

			$has_publish_permission = ($this->fuel->auth->has_permission($this->permission, 'publish')) ? '1' : '0';
			$has_edit_permission = $this->fuel->auth->has_permission($this->permission, 'edit') ? '1' : '0';

			$no = lang("form_enum_option_no");
			$yes = lang("form_enum_option_yes");
			$col_txt = lang('click_to_toggle');
			$key_field = $this->model->key_field();


			$_publish_toggle_callback = '
			$can_publish = (($heading == "published" OR $heading == "active") AND '.$has_publish_permission.' OR
				(($heading != "published" AND $heading != "active") AND '.$has_edit_permission.'));

			$no = "'.$no.'";
			$yes = "'.$yes.'";
			$col_txt = "'.$col_txt.'";

			// boolean fields
			if (!is_true_val($cols[$heading]))
			{
				$text_class = ($can_publish) ? "publish_text unpublished toggle_on" : "unpublished";
				$action_class = ($can_publish) ? "publish_action unpublished hidden" : "unpublished hidden";
				return \'<span class="publish_hover"><span class="\'.$text_class.\'" id="row_published_\'.$cols["'.$key_field.'"].\'" data-field="\'.$heading.\'">\'.$no.\'</span><span class="\'.$action_class.\'">\'.$col_txt.\'</span></span>\';
			}
			else
			{
				$text_class = ($can_publish) ? "publish_text published toggle_off" : "published";
				$action_class = ($can_publish) ? "publish_action published hidden" : "published hidden";
				return \'<span class="publish_hover"><span class="\'.$text_class.\'" id="row_published_\'.$cols["'.$key_field.'"].\'" data-field="\'.$heading.\'">\'.$yes.\'</span><span class="\'.$action_class.\'">\'.$col_txt.\'</span></span>\';
				
			}';

			$_publish_toggle_callback = create_function('$cols, $heading', $_publish_toggle_callback);

			foreach($boolean_fields as $bool)
			{
				$this->data_table->add_field_formatter($bool, $_publish_toggle_callback);
			}
			
			$this->data_table->auto_sort = TRUE;
			$heading_sort_func = (isset($this->disable_heading_sort) AND $this->disable_heading_sort) ? '' : 'fuel.sortList';
			$this->data_table->sort_js_func = $heading_sort_func;
			
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
			$this->form_builder->set_validator($this->model->get_validation());
			$this->form_builder->submit_value = NULL;
			$this->form_builder->use_form_tag = FALSE;
			$this->form_builder->set_fields($this->filters);
			$this->form_builder->display_errors = FALSE;
			$this->form_builder->css_class = 'more_filters';
			if ($this->config->item('date_format'))
			{
				$this->form_builder->date_format = $this->config->item('date_format');
			}
			$this->form_builder->set_field_values($field_values);

			if (method_exists($this->model, 'friendly_filter_info'))
			{
				$friendly_filter_info = $this->model->friendly_filter_info($field_values);
				if ( ! empty($friendly_filter_info)) {
					$vars['info'] = $friendly_filter_info;
				}
			}

			// keycheck is already put in place by $this->form->close() in module_list layout
			$this->form_builder->key_check = FALSE; 
			$vars['more_filters'] = $this->form_builder->render_divs();
			$vars['actions'] = $this->load->module_view(FUEL_FOLDER, '_blocks/module_list_actions', $vars, TRUE);
			$vars['form_action'] = $this->module_uri.'/items';
			$vars['form_method'] = 'get';
			$vars['query_string'] = $query_str;
			$vars['description'] = $this->description;
			$crumbs = array($this->module_uri => $this->module_name);
			$this->fuel->admin->set_titlebar($crumbs);
			
			$inline = $this->input->get('inline', TRUE);
			$this->fuel->admin->set_inline($inline);
			
			if ($inline === TRUE)
			{
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
			}
			$this->fuel->admin->render($this->views['list'], $vars);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays the list (table) view but inline without the left menu
	 *
	 * @access	public
	 * @return	void
	 */	
	function inline_items()
	{
		$this->items(TRUE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Processes the list view filters and returns an array of parameters
	 *
	 * @access	protected
	 * @return	array
	 */	
	protected function _list_process()
	{
		$this->load->library('pagination');
		$this->load->helper('convert');
		$this->load->helper('cookie');

		/* PROCESS PARAMS BEGIN */
		$filters = array();
		
		$page_state = $this->fuel->admin->get_page_state($this->module_uri);
		unset($page_state['offset']);
		
		$defaults = array();
		$defaults['col'] = (!empty($this->default_col)) ? $this->default_col : $this->display_field;
		$defaults['order'] = (!empty($this->default_order)) ? $this->default_order : 'asc';
		$defaults['offset'] = 0;
		$defaults['limit'] = key($this->limit_options);
		$defaults['search_term'] = '';
		$defaults['view_type'] = 'list';
		$defaults['extra_filters'] = array();
		$defaults['precedence'] = 0;

		//$defaults['language'] = '';
		
		// custom module filters defaults
		foreach($this->filters as $key => $val)
		{
			$defaults[$key] = (isset($val['default'])) ? $val['default'] : NULL;
		}
		
		$posted = array();
		if (!empty($_POST) OR !empty($_GET))
		{

			$posted['search_term'] = $this->input->get_post('search_term', TRUE);
			$posted_vars = array('col', 'order', 'limit', 'offset', 'precedence', 'view_type');
			foreach($posted_vars as $val)
			{
				if ($this->input->get_post($val)) $posted[$val] = $this->input->get_post($val, TRUE);
			}
			
			// custom module filters
			$extra_filters = array();
			
			
			foreach($this->filters as $key => $val)
			{
				if (isset($_POST[$key]) OR isset($_GET[$key]))
				{
					$posted[$key] = $this->input->get_post($key, TRUE);
					$this->filters[$key]['value'] = $posted[$key];
					$extra_filters[$key] = $posted[$key];
				}
			}
			$posted['extra_filters'] = $extra_filters;
		}
		$params = array_merge($defaults, $page_state, $posted);
		//$params = array_merge($defaults, $uri_params, $posted);
		
		if ($params['search_term'] == lang('label_search')) $params['search_term'] = NULL;
		/* PROCESS PARAMS END */
		return $params;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays the tree view
	 *
	 * @access	public
	 * @return	void
	 */	
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
				$output = '<div>'.lang('no_data').'</div>';
			}
			$this->output->set_output($output);
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Saves the precedence of fields
	 *
	 * @access	public
	 * @return	void
	 */	
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
						$where = array($this->model->key_field() => $row);
						$this->model->update($values, $where);
					}
					$i++;
				}
				
				// clear cache
				$this->_clear_cache();
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Displays the fields to create a record (form view)
	 *
	 * @access	public
	 * @param	string	The name of a field, or fields spearated by colon to display in the form (optional)
	 * @param	string	Determines whether to redirect the page after save or not
	 * @return	void
	 */	
	function create($field = NULL, $redirect = TRUE)
	{
		$id = NULL;
		
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
		
		$inline = $this->fuel->admin->is_inline();
		
		if (isset($_POST[$this->model->key_field()])) // check for dupes
		{
			if ($id = $this->_process_create() AND !has_errors())
			{
				if ($inline === TRUE)
				{
					$url = fuel_uri($this->module_uri.'/inline_edit/'.$id);
				}
				else
				{
					$url = fuel_uri($this->module_uri.'/edit/'.$id);
				}

				// save any tab states
				$this->_save_tab_state($id);

				if ($redirect)
				{
					if (!$this->fuel->admin->has_notification(Fuel_admin::NOTIFICATION_SUCCESS))
					{
						$this->fuel->admin->set_notification(lang('data_saved'), Fuel_admin::NOTIFICATION_SUCCESS);
					}
					redirect($url);
				}
			}
		}
		
		$shell_vars = $this->_shell_vars($id);
		
		$passed_init_vars = ($this->input->get(NULL, TRUE)) ? $this->input->get(NULL, TRUE) : array();
		$form_vars = $this->_form_vars($id, $passed_init_vars, FALSE, $inline);
		$vars = array_merge($shell_vars, $form_vars);
		$vars['action'] = 'create';
		
		$crumbs = array($this->module_uri => $this->module_name, lang('action_create'));
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->set_inline($inline);
		
		if ($inline === TRUE)
		{
			$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT);
		}

		$vars['actions'] = $this->load->module_view(FUEL_FOLDER, '_blocks/module_inline_actions', $vars, TRUE);
		$this->fuel->admin->render($this->views['create_edit'], $vars);
		return $id;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The same as the create method but does not show the left menu
	 *
	 * @access	public
	 * @param	string	The name of a field, or fields spearated by colon to display in the form (optional)
	 * @param	string	Determines whether to redirect the page after save or not
	 * @return	void
	 */	
	function inline_create($field = NULL)
	{
		$this->fuel->admin->set_inline(TRUE);
		$this->create($field);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Duplicates a record. Similar to edit but without the record ID attached.
	 *
	 * @access	public
	 * @return	void
	 */	
	function duplicate()
	{
		$_POST[$this->model->key_field()] = 'dup';
		$this->create();
	}

	protected function _process_create()
	{
		// reset dup id
		if ($_POST[$this->model->key_field()] == 'dup')
		{
			$_POST[$this->model->key_field()] = '';

			// alter duplicate information if there is a hook
			$_POST = $this->model->on_duplicate($_POST);

			$this->load->library('form_builder');
			$fb = new Form_builder();
			$fb->load_custom_fields(APPPATH.'config/custom_fields.php');
			$fields = $this->model->form_fields($_POST);
			$fb->set_fields($fields);
			$fb->post_process_field_values();// manipulates the $_POST values directly

		}
		else
		{
			$this->model->on_before_post($this->input->post());

			$posted = $this->_process();
			// set publish status to no if you do not have the ability to publish
			if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
			{
				$posted['published'] = 'no';
				$posted['active'] = 'no';
			}
			$model = $this->model;

			// run before_create hook
			$this->_run_hook('before_create', $posted);

			// run before_save hook
			$this->_run_hook('before_save', $posted);
			
			// save the data
			$id = $this->model->save($posted);
			if (empty($id))
			{
				add_error(lang('error_invalid_id'));
				return FALSE;
			}
			
			// add id value to the posted array
			if (!is_array($this->model->key_field()))
			{
				$posted[$this->model->key_field()] = $id;
			}

			// process $_FILES
			if (!$this->_process_uploads($posted))
			{
				return FALSE;
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
				$this->_run_hook('after_save', $data);

				if (!empty($data))
				{
					$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
					$this->fuel->logs->write($msg);
					$this->_clear_cache();
					return $id;
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays the fields to edit a record (form view)
	 *
	 * @access	public
	 * @param	int		The ID value of the record to edit
	 * @param	string	The name of a field, or fields spearated by colon to display in the form (optional)
	 * @param	string	Determines whether to redirect the page after save or not
	 * @return	void
	 */	
	function edit($id = NULL, $field = NULL, $redirect = TRUE)
	{
		// check that the action even exists and if not, show a 404
		if (!$this->fuel->auth->module_has_action('save'))
		{
			show_404();
		}

		// check permissions
		if (!$this->fuel->auth->has_permission($this->module_obj->permission, 'edit') AND !$this->fuel->auth->has_permission($this->module_obj->permission, 'create'))
		{
			show_error(lang('error_no_permissions'));
		}

		$inline = $this->fuel->admin->is_inline();
		
		if ($this->input->post($this->model->key_field()))
		{
			if ($this->_process_edit($id) AND !has_errors())
			{
				if ($inline === TRUE)
				{
					$url = fuel_uri($this->module_uri.'/inline_edit/'.$id.'/'.$field);
				}
				else
				{
					$url = fuel_uri($this->module_uri.'/edit/'.$id.'/'.$field);
				}
				
				if ($redirect)
				{
					if (!$this->fuel->admin->has_notification(Fuel_admin::NOTIFICATION_SUCCESS))
					{
						$this->fuel->admin->set_notification(lang('data_saved'), Fuel_admin::NOTIFICATION_SUCCESS);
					}
					redirect($url);
				}
			}
		}
		
		//$vars = $this->_form($id);
		$data = $this->_saved_data($id);
		$action = (!empty($data[$this->model->key_field()])) ? 'edit' : 'create';
	
		// substitute data values into preview path
		$this->preview_path = $this->module_obj->url($data);

		$shell_vars = $this->_shell_vars($id, $action);
		$form_vars = $this->_form_vars($id, $data, $field, $inline);
		
		$vars = array_merge($shell_vars, $form_vars);
		$vars['data'] = $data;
		$vars['action'] = $action;
		$vars['related_items'] = $this->model->related_items($data);
		
		
		// active or publish fields
		if (isset($data['published']))
		{
			$vars['publish'] = (!empty($data['published']) AND is_true_val($data['published'])) ? 'unpublish' : 'publish';
		}
		
		if (isset($data['active']))
		{
			$vars['activate'] = (!empty($data['active']) AND is_true_val($data['active'])) ? 'deactivate' : 'activate';
		}
		
		if (!empty($field))
		{
			$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_NO_ACTION);
		}
		else if ($inline === TRUE)
		{
			$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT);
		}

		$crumbs = array($this->module_uri => $this->module_name);
		if (!empty($data[$this->display_field]))
		{
			$crumbs[''] = character_limiter(strip_tags($data[$this->display_field]), 50);
		}
		
		$this->fuel->admin->set_titlebar($crumbs);

		$vars['actions'] = $this->load->module_view(FUEL_FOLDER, '_blocks/module_create_edit_actions', $vars, TRUE);
		$this->fuel->admin->render($this->views['create_edit'], $vars);

		// do this after rendering so it doesn't render current page'
		if (!empty($data[$this->display_field]) AND $inline !== TRUE)
		{
			$this->fuel->admin->add_recent_page($this->uri->uri_string(), $this->module_name.': '.$data[$this->display_field], $this->module);
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The same as the edit method but does not show the left menu
	 *
	 * @access	public
	 * @param	int		The ID value of the record to edit
	 * @param	string	The name of a field, or fields spearated by colon to display in the form (optional)
	 * @return	void
	 */	
	function inline_edit($id = NULL, $field = NULL)
	{
		if (empty($id))
		{
			show_404();
		}
		
		$this->fuel->admin->set_inline(TRUE);
		$this->edit($id, $field);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Processes the form data to save
	 *
	 * @access	protected
	 * @param	int		The ID value of the record to edit
	 * @return	boolean
	 */	
	protected function _process_edit($id)
	{
		$this->model->on_before_post($this->input->post());
		
		$posted = $this->_process();

		// run before_edit hook
		$this->_run_hook('before_edit', $posted);
		
		// run before_save hook
		$this->_run_hook('before_save', $posted);

		if ($this->model->save($posted))
		{
			// process $_FILES...
			if (!$this->_process_uploads($posted))
			{
				return FALSE;
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
				$this->fuel->logs->write($msg);
				$this->_clear_cache();
				return TRUE;
			}
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sanitizes the input based on the module's settings
	 *
	 * @access	protected
	 * @param	array	The array of posted data to sanitize
	 * @return	array
	 */	
	protected function _sanitize($data)
	{
		$posted = $data;
		
		if (!empty($this->sanitize_input))
		{
			// functions that are valid for sanitizing
			$valid_funcs = $this->fuel->config('module_sanitize_funcs');
			
			if ($this->sanitize_input === TRUE)
			{
				foreach($data as $key => $val)
				{
					if (!empty($val))
					{
						$posted[$key] = xss_clean($val);	
					}
					
				}
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of shell variables to apply to the main area of the page
	 *
	 * @access	protected
	 * @param	int		The ID value of the record to edit
	 * @param	string	The name of the action to apply to the main form element
	 * @return	array
	 */	
	protected function _shell_vars($id = NULL, $action = 'create')
	{
		$model = $this->model;
		$this->js_controller_params['method'] = 'add_edit';
		$this->js_controller_params['linked_fields'] = $this->model->linked_fields;
		
		// other variables
		$vars['id'] = $id;
		$vars['versions'] = $this->fuel_archives_model->options_list($id, $this->model->table_name());
		$vars['others'] = $this->model->get_others($this->display_field, $id);
		$vars['action'] =  $action;
		
		$vars['module'] = $this->module;
		$vars['notifications'] = $this->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
		
		return $vars;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of saved data based on the id value passed
	 *
	 * @access	protected
	 * @param	int		The ID value of the record to edit
	 * @return	array
	 */	
	protected function _saved_data($id)
	{
		if (empty($id)) return array();
		
		$edit_method = $this->edit_method;
		if ($edit_method != 'find_one_array')
		{
			$saved = $this->model->$edit_method($id);
		}
		else
		{
			$saved = $this->model->$edit_method(array($this->model->table_name().'.'.$this->model->key_field() => $id));
		}
		return $saved;
	}
	
	// seperated to make it easier in subclasses to use the form without rendering the page
	protected function _form_vars($id = NULL, $values = array(), $field = NULL, $inline = FALSE)
	{
		$this->load->library('form_builder');
		
		// load custom fields
		$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');

		$model = $this->model;
		$this->js_controller_params['method'] = 'add_edit';
		$action = (!empty($values[$this->model->key_field()])) ? 'edit' : 'create';
		
		// create fields... start with the table info and go from there
		$fields = (!empty($values)) ? $this->model->form_fields($values) : $this->model->form_fields($_POST);

		// if field parameter is set, then we just display a single field
		if (!empty($field))
		{
			
			// added per pierlo in Forum (http://www.getfuelcms.com/forums/discussion/673/fuel_helper-fuel_edit-markers)
			$columns = explode(':', $field);
			
			// special case if you use the word required
			if (in_array('required', $columns))
			{
				$columns = array_merge($columns, $this->model->required);
			}
			
			// set them to hidden... just in case model hooks require the values to be passed on save
			foreach($fields as $k => $f)
			{
				if (!in_array($k, $columns))
				{
					$fields[$k]['type'] = 'hidden';
				}
				
				if (count($columns) <= 1)
				{
					$fields[$k]['display_label'] = FALSE;
					$fields[$k]['required'] = FALSE;
				}
			}
		}
		
		// set published/active to hidden since setting this is an buttton/action instead of a form field
		$form = '';
		if (is_array($fields))
		{
			
			$field_values = (!empty($_POST)) ? $_POST : $values;
			
			$published_active = array(
				'publish' => 'published',
				'active' => 'activate'
			);
			foreach($published_active as $k => $v)
			{
				if (!$this->fuel->auth->has_permission($this->permission, $k))
				{
					unset($fields[$v]);
				}

				if (isset($fields[$v]) AND !empty($values[$v]))
				{
					$fields['published']['value'] = $values[$v];
				}
			}

			$this->form_builder->set_validator($this->model->get_validation());

			// add hidden field with the module name for convenience
			$common_fields = $this->_common_fields($field_values);
			$fields = array_merge($fields, $common_fields);

			$fields['__fuel_inline_action__'] = array('type' => 'hidden');
			$fields['__fuel_inline_action__']['class'] = '__fuel_inline_action__';
			$fields['__fuel_inline_action__']['value'] = (empty($id)) ? 'create' : 'edit';
			
			$fields['__fuel_inline__'] = array('type' => 'hidden');
			$fields['__fuel_inline__']['value'] = ($inline) ? 1 : 0;

			$this->form_builder->submit_value = lang('btn_save');
			$this->form_builder->question_keys = array();
			$this->form_builder->use_form_tag = FALSE;
			$this->form_builder->hidden = (array) $this->model->key_field();
			$this->form_builder->set_fields($fields);
			$this->form_builder->display_errors = FALSE;
			$this->form_builder->set_field_values($field_values);

			if ($this->config->item('date_format'))
			{
				$this->form_builder->date_format = $this->config->item('date_format');
			}

			if ($inline)
			{
				$this->form_builder->cancel_value = lang('viewpage_close');
			}
			else
			{
				$this->form_builder->cancel_value = lang('btn_cancel');
			}

			// we will set this in the BaseFuelController.js file so that the jqx page variable is available upon execution of any form field js
			//$this->form_builder->auto_execute_js = FALSE;
			if (!isset($fields['__FORM_BUILDER__'], $fields['__FORM_BUILDER__']['displayonly']))
			{
				$this->form_builder->displayonly = $this->displayonly;
			}
			
			
			$form = $this->form_builder->render();
		}
		$action_uri = $action.'/'.$id.'/'.$field;
		$vars['form_action'] = ($inline) ? $this->module_uri.'/inline_'.$action_uri : $this->module_uri.'/'.$action_uri;
		$vars['form'] = $form;
		$vars['data'] = $values;
		$vars['error'] = $this->model->get_errors();
		$vars['notifications'] = $this->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
		$vars['instructions'] = (empty($field)) ? $this->instructions : '';
		$vars['field'] = (!empty($field));
		return $vars;
	}

	protected function _process()
	{
		$this->load->helper('security');

		$this->_orig_post = $_POST;

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
				$_POST[$key] = preg_replace_callback('#(.*)\{(.+)\}(.*)#U', $callback, $val);
			}
		}

		// set boolean fields 
		if (!empty($this->model->boolean_fields) AND is_array($this->model->boolean_fields))
		{
			foreach($this->model->boolean_fields as $val)
			{
				$_POST[$val] = (isset($_POST[$val])) ? $_POST[$val] : 0;
			}
		}

		// if no permission to publish, then we revoke
		if (!$this->fuel->auth->has_permission($this->permission, 'publish'))
		{
			unset($_POST['published']);
		}
		
		// set key_field if it is not id
		if (!empty($_POST['id']) AND $this->model->key_field() != 'id')
		{
			$_POST[$this->model->key_field()] = $_POST['id'];
		}

		// run any form field post processing hooks
		$this->load->library('form_builder');
		
		// use a new instance to prevent problems when duplicating
		$fb = new Form_builder();
		$fb->load_custom_fields(APPPATH.'config/custom_fields.php');
		$fields = $this->model->form_fields($_POST);
		$fb->set_fields($fields);
		$fb->post_process_field_values();// manipulates the $_POST values directly

		// sanitize input if set in module configuration
		$posted = $this->_sanitize($_POST);

		return $posted;
	}
	
	function form($id = NULL, $field = NULL)
	{
		$saved = $this->_saved_data($id);
		$vars = $this->_form_vars($id, $saved, $field);
		$this->load->module_view(FUEL_FOLDER, '_layouts/module_form', $vars);
	}

	function delete($id = NULL)
	{
		// check that the action even exists and if not, show a 404
		if (!$this->fuel->auth->module_has_action('delete'))
		{
			show_404();
		}
		
		if (!$this->fuel->auth->has_permission($this->permission, 'delete')) 
		{
			show_error(lang('error_no_permissions'));
		}

		$inline = $this->fuel->admin->is_inline();
		if (!empty($_POST['id']))
		{
			$posted = explode('|', $this->input->post('id', TRUE));
			
			
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
			
			// run after_delete hook
			$this->_run_hook('after_delete', $posted);
			
			$this->_clear_cache();
			
			if (count($posted) > 1)
			{
				$this->fuel->logs->write(lang('module_multiple_deleted', $this->module));
			}
			else
			{
				$this->fuel->logs->write(lang('module_deleted', count($posted), $this->module));
			}
			
			if ($inline)
			{
				$vars['title'] = '';
				$vars['id'] = '';
				$vars['back_action'] = '';
				$this->fuel->admin->render('modules/module_close_modal', $vars);
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
				$this->fuel->admin->render($this->views['delete'], $vars);
			}
			else
			{
				// set a success delete message
				if ($any_success)
				{
					if (!$this->session->flashdata('success'))
					{
						$this->fuel->admin->set_notification(lang('data_deleted'), Fuel_admin::NOTIFICATION_SUCCESS);
					}
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
					$this->fuel->admin->set_notification($msg, Fuel_admin::NOTIFICATION_ERROR);
				}
				
				$url = fuel_uri($this->module_uri);
				redirect($url);
			}
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
				if (isset($data[$this->display_field]))
				{
					$vars['title'] = $data[$this->display_field];
				}
			}
			
			if (empty($data))
			{
				show_404();
			}
			
			$vars['error'] = $this->model->get_errors();
			
			$crumbs = array($this->module_uri => $this->module_name);
			$crumbs[''] = character_limiter(strip_tags(lang('action_delete').' '.$vars['title']), 50);
			
			$this->fuel->admin->set_titlebar($crumbs);
			
			if ($inline)
			{
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_NO_ACTION);
				$vars['back_action'] = fuel_url($this->module_uri.'/inline_edit/'.$id);
			}
			else
			{
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_NO_ACTION);
				$vars['back_action'] = fuel_url($this->module_uri.'/');
			}
			$action_uri = 'delete/'.$id;
			$vars['form_action'] = ($inline) ? $this->module_uri.'/inline_'.$action_uri : $this->module_uri.'/'.$action_uri;

			$this->fuel->admin->render($this->views['delete'], $vars);
		}
	}
	
	function inline_delete($id)
	{
		$this->fuel->admin->set_inline(TRUE);
		$this->delete($id);
	}
	
	function restore()
	{
		if (!$this->fuel->auth->has_permission($this->permission, 'edit')) 
		{
			show_error(lang('error_no_permissions'));
		}
		
		if (!empty($_POST['fuel_restore_version']) AND !empty($_POST['fuel_restore_ref_id']))
		{
			if (!$this->model->restore($this->input->post('fuel_restore_ref_id'), $this->input->post('fuel_restore_version')))
			{
				$msg = lang('module_restored', $this->module_name);
				$this->fuel->logs->write($msg);
				
				$this->fuel->admin->set_notification($this->model->get_validation()->get_last_error(), Fuel_admin::NOTIFICATION_ERROR);
				
			}
			else
			{
				if (!$this->session->flashdata('success'))
				{
					$this->fuel->admin->set_notification(lang('module_restored_success'), Fuel_admin::NOTIFICATION_SUCCESS);
				}
				$this->_clear_cache();
			}
			redirect(fuel_uri($this->module_uri.'/edit/'.$this->input->post('fuel_restore_ref_id', TRUE)));
		}
		else
		{
			show_404();
		}
	}
	
	function replace($id = NULL)
	{
		if (empty($id))
		{
			show_404();
		}
		
		if (!$this->fuel->auth->has_permission($this->permission, 'edit') OR !$this->fuel->auth->has_permission($this->permission, 'delete')) 
		{
			show_error(lang('error_no_permissions'));
		}
		
		$success = FALSE;
		if (!empty($_POST))
		{
			if (!empty($_POST['fuel_replace_id']))
			{
				$replace_id = $this->input->post('fuel_replace_id');
				//$delete = is_true_val($this->input->post('fuel_delete_replacement'));
				$delete = TRUE;
				if (!$this->model->replace($replace_id, $id, $delete))
				{
					add_error($this->model->get_validation()->get_last_error());
				}
				else
				{
					$this->fuel->admin->set_notification(lang('module_replaced_success'), Fuel_admin::NOTIFICATION_SUCCESS);
					$success = TRUE;
					$this->_clear_cache();
				}
			}
			else
			{
				add_error(lang('error_select_replacement'));
			}
			//redirect(fuel_uri($this->module_uri.'/edit/'.$id));
		}
		$this->load->library('form_builder');
		
		$fields = array();
		$other_options = $this->model->get_others($this->display_field, $id);
		$fields['fuel_replace_id'] = array('label' => 'Replace record:', 'type' => 'select', 'options' => $other_options, 'first_option' => 'Select record to replace...', 'style' => 'max-width: 400px', 'disabled_options' => array($id));
		//$fields['fuel_delete_replacement'] = array('label' => 'Delete replacement', 'type' => 'checkbox', 'value' => 'yes');
		if ($success)
		{
			$fields['new_fuel_replace_id'] = array('type' => 'hidden', 'value' => $replace_id);
		}
		
		//$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		//$this->form_builder->submit_value = NULL;
		
		$vars['form'] = $this->form_builder->render();
		$this->fuel->admin->set_inline(TRUE);

		$crumbs = array('' => $this->module_name, lang('action_replace'));
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->render('modules/module_replace', $vars);
		
	}
	
	// displays the module's designated view'
	function view($id = NULL)
	{
		if (!empty($this->preview_path) AND !empty($id))
		{
			$data = $this->model->find_one_array(array($this->model->table_name().'.'.$this->model->key_field() => $id));

			$url = $this->module_obj->url($data);

			// change the last page to be the referrer
			$last_page = (isset($_SERVER['HTTP_REFERER'])) ? substr($_SERVER['HTTP_REFERER'], strlen(site_url())) : NULL;
			$this->fuel->admin->set_last_page($last_page);
			redirect($url);
		}
		else
		{
			show_error(lang('no_preview_path'));
		}
	}
	
	// refreshes a single field
	function refresh_field()
	{
		if (!empty($_POST))
		{
			$fields = $this->model->form_fields();
			$field = $this->input->post('field', TRUE);
			if (!isset($fields[$field])) return;
			
			$field_id = $this->input->post('field_id', TRUE);
			$values = $this->input->post('values', TRUE);

			$selected = $this->input->post('selected', TRUE);
			
			$field_key = end(explode('vars--', $field));

			$this->load->library('form_builder');
			$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');
			
			// for multi select
			if (is_array($values))
			{
				$selected = (array) $selected;
				foreach($values as $v)
				{
					if (!in_array($v, $selected))
					{
						$selected[] = $v;
					}
				}
			}
			
			if (!empty($selected)) $fields[$field]['value'] = $selected;
			$fields[$field]['name'] = $field_id;

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
					$fields[$this->model->key_field()]['type'] = 'select';
					$fields[$this->model->key_field()]['options'] = $this->model->options_list();
				}
				$output = $this->form_builder->create_field($fields[$field_key]);
			}
			
			$this->output->set_output($output);
		
		}
	}
	
	// processes linked fields
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
	
	// automatically calls ajax methods on the model
	function ajax($method = NULL)
	{
		// must not be empty and must start with find_ (... don't want to access methods like delete)
		if (is_ajax())
		{
			// append ajax to the method name... to prevent any conflicts with default methods
			$method = 'ajax_'.$method;

			$params = $this->input->get_post(NULL, TRUE);
			
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
	
	// exports data to CSV
	function export()
	{
		if (empty($this->exportable))
		{
			show_404();
		}
		
		if (!$this->fuel->auth->has_permission($this->permission, 'export'))
		{
			show_error(lang('error_no_permissions'));
		}
		if (!empty($_POST))
		{
			// load dbutils for convenience to use in custom methods on model
			$this->load->dbutil();
			$this->load->helper('download');
			
			$filename = $this->module.'_'.date('Y-m-d').'.csv';
			$params = $this->_list_process();
			$data = $this->model->export_data($params);
			force_download($filename, $data);
		}
	}
	
	// used in list view to quickly unpublish (if they have permisison)
	function toggle_on($id = NULL, $field = 'published')
	{
		$this->_toggle($id, $field, 'on');
	}

	// used in list view to quickly publish (if they have permisison)
	function toggle_off($id = NULL, $field = 'published')
	{
		$this->_toggle($id, $field, 'off');
	}
	
	// reduce code by creating this shortcut function for the unpublish/publish
	function _toggle($id, $field, $toggle)
	{
		if (!$this->fuel->auth->module_has_action('save') OR ($field == 'publish' AND !$this->fuel->auth->has_permission($this->permission, 'publish'))) 
		{
			return FALSE;
		}
		
		if (empty($id))
		{
			$id = $this->input->post($this->model->key_field());
		}
		
		if ($id)
		{
			$save = $this->model->find_by_key($id, 'array');
			$field_info = $this->model->field_info($field);

			if (!empty($save))
			{
				if ($toggle == 'on')
				{
					$save[$field] = ($field_info['type'] != 'enum') ? 1 : 'yes';
				}
				else
				{
					$save[$field] = ($field_info['type'] != 'enum') ? 0 : 'no';
				}

				// run before_edit hook
				$this->_run_hook('before_edit', $save);
	
				// run before_save hook
				$this->_run_hook('before_save', $save);


				$save = $this->model->clean($save);
				$where[$this->model->key_field()] = $id;

				// use update instead of save to avoid issue with has_many and belongs_to being removed
				if ($this->model->update($save, $where))
				{
					// clear cache
					$this->_clear_cache();

					// log it
					$data = $this->model->find_by_key($id, 'array');
					
					// run after_edit hook
					$this->_run_hook('after_edit', $data);

					// run after_save hook
					$this->_run_hook('after_save', $data);
					
					$msg = lang('module_edited', $this->module_name, $data[$this->display_field]);
					$this->fuel->logs->write($msg);
				}
				else
				{
					$this->output->set_output(lang('error_saving'));
				}
			}
		}
		
		if (is_ajax())
		{
			$this->output->set_output($toggle);
		}
		else
		{
			$this->items();
		}
	}
	
	protected function _clear_cache()
	{
		// reset cache for that page only
		if ($this->clear_cache_on_save) 
		{
			$this->fuel->cache->clear_pages();
		}
	}
	
	protected function _allow_action($action)
	{
		return in_array($action, $this->item_actions);
	}
	
	protected function _common_fields($values)
	{
		$fields['__fuel_module__'] = array('type' => 'hidden');
		$fields['__fuel_module__']['value'] = $this->module;
		$fields['__fuel_module__']['class'] = '__fuel_module__';

		$fields['__fuel_module_uri__'] = array('type' => 'hidden');
		$fields['__fuel_module_uri__']['value'] = $this->module_uri;
		$fields['__fuel_module_uri__']['class'] = '__fuel_module_uri__';

		$fields['__fuel_id__'] = array('type' => 'hidden');
		$fields['__fuel_id__']['value'] = (!empty($values[$this->model->key_field()])) ? $values[$this->model->key_field()] : '';
		$fields['__fuel_id__']['class'] = '__fuel_id__';
		return $fields;
	}

	protected function _save_tab_state($id)
	{
		// set tab
		if (isset($_POST['__fuel_selected_tab__']))
		{
			if (!empty($_COOKIE['fuel_tabs']))
			{
				$tab_cookie = json_decode(urldecode($_COOKIE['fuel_tabs']), TRUE);
				if (!empty($tab_cookie))
				{
					$tab_cookie[$this->module.'_edit_'.$id] = $_POST['__fuel_selected_tab__'];
					$cookie_val = urlencode(json_encode($tab_cookie));

					// set the cookie for viewing the live site with added FUEL capabilities
					$config = array(
						'name' => 'fuel_tabs', 
						'value' => $cookie_val,
						'expire' => 0,
						//'path' => WEB_PATH
						'path' => $this->fuel->config('fuel_cookie_path')
					);
					set_cookie($config);
				}
			}
		}
	}
	
	protected function _process_uploads($posted = NULL)
	{
		if (empty($posted)) $posted = $_POST;
		$errors = FALSE;
		
		if (!empty($_FILES))
		{

			// loop through uploaded files
			foreach ($_FILES as $file => $file_info)
			{
				if ($file_info['error'] == 0)
				{
					$posted[$file] = $file_info['name'];
					
					$file_tmp = current(explode('___', $file));
					$field_name = $file_tmp;

					// if there is a field with the suffix of _upload, then we will overwrite that posted value with this value
					if (substr($file_tmp, ($file_tmp - 7)) == '_upload') 
					{
						$field_name = substr($file_tmp, 0, ($file_tmp - 7));
					}

					if (isset($posted[$file_tmp.'_file_name']))
					{
						// get file extension
						$path_info = pathinfo($file_info['name']);
						$field_value = $this->_orig_post[$file_tmp.'_file_name'].'.'.$path_info['extension'];
					}
					else
					{
						$field_value = $file_info['name'];
					}
					if (strpos($field_value, '{') !== FALSE )
					{
						//e modifier is deprecated so we have to do this
						$callback = create_function('$match', '
								$return = "";
								if (!empty($match[2]))
								{
									$return = $match[1].$GLOBALS["__tmp_transient_posted__"][$match[2]].$match[3];
								}
								return $return;');

						// hacky but avoids 5.3 funcation syntax (which is nicer but doesn't work with 5.2)
						$GLOBALS['__tmp_transient_posted__'] = $posted;

						$field_value = preg_replace_callback('#^(.*)\{(.+)\}(.*)$#', $callback, $field_value);
					}

					// set both values for the namespaced and non-namespaced... make them underscored and lower cased
					$tmp_field_name = end(explode('--', $field_name));

					$file_name = pathinfo($field_value, PATHINFO_FILENAME);
					$file_ext = pathinfo($field_value, PATHINFO_EXTENSION);
					$file_val = url_title($file_name, 'underscore', FALSE).'.'.$file_ext;
					$posted[$tmp_field_name] = $file_val;
					$posted[$field_name] = $file_val;
					$posted[$file_tmp.'_file_name'] = $file_val;

				}
			}

			// hacky cleanup to avoid using 5.3 syntax
			if (isset($GLOBALS["__tmp_transient_posted__"]))
			{
				unset($GLOBALS["__tmp_transient_posted__"]);
			}

			$params['xss_clean'] = $this->sanitize_files;
			$params['posted'] = $posted;

			// UPLOAD!!!
			if (!$this->fuel->assets->upload($params))
			{
				$errors = TRUE;
				$msg = $this->fuel->assets->last_error();
				add_error($msg);
				$this->fuel->admin->set_notification($msg, Fuel_admin::NOTIFICATION_ERROR);
			}
			else
			{
				
				// do post processing of updating field values if they changed during upload due to overwrite being FALSE
				$uploaded_data = $this->fuel->assets->uploaded_data();

				// transfer uploaded data info to the model
				$this->model->upload_data =& $uploaded_data;

				// transfer uploaded data the controller object as well
				$this->upload_data =& $uploaded_data;

				foreach($uploaded_data as $key => $val)
				{
					$file_tmp = current(explode('___', $key));

					// if there is a field with the suffix of _upload, then we will overwrite that posted value with this value
					if (substr($file_tmp, ($file_tmp - 7)) == '_upload')
					{
						$field_name = substr($file_tmp, 0, ($file_tmp - 7));
					}

					// get the file name field
					// if the file name field exists AND there is no specified hidden filename field to assign to it AND...
					// the model does not have an array key field AND there is a key field value posted
					if (isset($field_name) AND isset($posted[$field_name]) AND !is_array($this->model->key_field()) AND isset($posted[$this->model->key_field()]))
					{
						$id = $posted[$this->model->key_field()];
						$data = $this->model->find_one_array(array($this->model->table_name().'.'.$this->model->key_field() => $id));
						$data[$field_name] = $val['file_name'];
						$this->model->save($data);
					}
				}
				
			}
		}
		return !$errors;
	}
	
	protected function _run_hook($hook, $params = array())
	{
		// call module specific hook
		$hook_name = $hook.'_'.$this->module;
		$GLOBALS['EXT']->_call_hook($hook_name, $params);

		// call global module hook if any
		$hook_name = $hook.'_module';
		$GLOBALS['EXT']->_call_hook($hook_name, $params);
	}
}