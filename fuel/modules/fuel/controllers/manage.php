<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Manage extends Fuel_base_controller {
	
	public $nav_selected = 'manage/cache';
	public $module_uri = 'manage/activity';
	
	function __construct()
	{
		parent::__construct();
		$this->js_controller = 'fuel.controller.ManageController';
	}
	
	function index()
	{
		$this->_validate_user('manage');
		$vars['notifications'] = '';
		
		$vars['xtras'] = $this->config->item('xtra', 'fuel');
		$this->_render('manage', $vars);
	}
	
	function cache(){
		$this->_validate_user('manage/cache');
		$this->nav_selected = 'manage/cache';
		if ($post = $this->input->post('action'))
		{
			$this->load->library('cache');
			$cache_group = $this->config->item('page_cache_group', 'fuel');
			$this->cache->remove_group($cache_group);
			
			
			// also delete DWOO compiled files
			$this->load->helper('file');
			$dwoo_path = APPPATH.'cache/dwoo/compiled/';
			if (is_dir($dwoo_path) AND is_writable($dwoo_path))
			{
				@delete_files($dwoo_path);
			}
			
			// remove asset cache files if exist
			$modules = $this->config->item('modules_allowed', 'fuel');
			$modules[] = FUEL_FOLDER; // fuel
			$modules[] = ''; // main application assets
			foreach($modules as $module)
			{
				// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
				$cache_folder = assets_server_path($this->asset->assets_cache_folder, 'cache', $module);
				if (is_dir($cache_folder) AND is_writable($cache_folder))
				{
					@delete_files($cache_folder);
				}
			}
			
			$msg = lang('cache_cleared');
			$this->logs_model->logit($msg);
			$this->session->set_flashdata('success', 'The cache has been cleared.');
			redirect('fuel/manage/cache');
		}
		else 
		{
			$vars['notifications'] = $this->load->view('_blocks/notifications', array(), TRUE);
			$this->_render('manage/cache', $vars);
		}
	}
	

	function activity(){
		$this->_validate_user('manage/activity');
		$this->load->module_model(FUEL_FOLDER, 'logs_model');
		$this->load->library('pagination');
		$this->load->library('data_table');
		$this->load->helper('convert');
		
		$this->nav_selected = 'manage/activity';
		
		$page_state = $this->_get_page_state();
		
		/* PROCESS PARAMS BEGIN */
		$filters = array();

		$defaults = array();
		$defaults['col'] = 'entry_date';
		$defaults['order'] = 'asc';
		$defaults['offset'] = 0;
		$defaults['limit'] = 25;
		$defaults['search_term'] = '';
		$defaults['precedence'] = NULL;
		$uri_params = uri_safe_batch_decode(fuel_uri_segment(4), '|', TRUE);
		$uri_params = array();
		if (fuel_uri_segment(4)) $uri_params['offset'] = (int) fuel_uri_segment(4);

		$posted = array();
		if (!empty($_POST)){
			if ($this->input->post('col')) $posted['col'] = $this->input->post('col');
			if ($this->input->post('order')) $posted['order'] = $this->input->post('order');
			if ($this->input->post('limit')) $posted['limit'] = $this->input->post('limit');
			if ($this->input->post('limit')) $posted['offset'] = (int) $this->input->post('offset');
			$posted['search_term'] = $this->input->post('search_term');
		}
		
		//$params = array_merge($defaults, $uri_params, $posted);
		$params = array_merge($defaults, $page_state, $uri_params, $posted);
		
		if ($params['search_term'] == lang('label_search')) $params['search_term'] = NULL;
		/* PROCESS PARAMS END */
		
		$seg_params = $params;
		unset($seg_params['offset']);
		
		$seg_params = uri_safe_batch_encode($seg_params, '|', TRUE);

		// if (!is_ajax() AND !empty($_POST))
		// {
		// 	$uri = fuel_url('manage/activity/offset/'.$params['offset']);
		// 	redirect($uri);
		// }
		
		$filters['first_name'] = $params['search_term'];
		$filters['last_name'] = $params['search_term'];
		$filters['message'] = $params['search_term'];
		$filters['entry_date'] = $params['search_term'];
		
		$this->logs_model->add_filters($filters);
		
		// pagination
		$config['base_url'] = fuel_url('manage/activity/offset/');
		$config['total_rows'] = $this->logs_model->list_items_total();
		$config['uri_segment'] = fuel_uri_index(4);
		$config['per_page'] = $params['limit'];
		$config['page_query_string'] = FALSE;
		$config['num_links'] = 5;
		$config['prev_link'] = lang('pagination_prev_page');
		$config['next_link'] = lang('pagination_next_page');
		$config['first_link'] = lang('pagination_first_link');
		$config['last_link'] = lang('pagination_last_link');;
		
		$this->pagination->initialize($config);
		
		$this->_save_page_state($params);
		
		// data table
		$vars['params'] = $params;
		
		$vars['table'] = '';
		
		if (is_ajax())
		{
			$items = $this->logs_model->list_items($params['limit'], $params['offset'], $params['col'], $params['order']);
			$this->data_table->row_alt_class = 'alt';
			$this->data_table->id = 'activity_data_table'; // change this so that it doesn't have clickable rows'
			$this->data_table->only_data_cols = array('id');
			$this->data_table->set_sorting($params['col'], $params['order']);
			$this->data_table->auto_sort = TRUE;
			$this->data_table->sort_js_func = 'page.sortList';
			$headers = array('entry_date' => lang('form_label_entry_date'), 'name' => lang('form_label_name'), 'message' => lang('form_label_message'));
			$this->data_table->assign_data($items, $headers);
			$vars['table'] = $this->data_table->render();
			$this->load->view('_blocks/module_list_table', $vars);
			return;
		}
		else
		{
			$this->load->library('form_builder');
			$this->js_controller_params['method'] = 'activity';
			$vars['table'] = $this->load->view('_blocks/module_list_table', $vars, TRUE);
			$vars['pagination'] = $this->pagination->create_links();

			// for extra module filters
			$field_values = array();
			$this->_render('manage/activity', $vars);
		}
	}
	
}