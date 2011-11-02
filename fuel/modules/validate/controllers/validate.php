<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');

class Validate extends Fuel_base_controller {
	
	public $nav_selected = 'tools/validate|tools/validate/:any';
	public $view_location = 'validate';
	
	function __construct()
	{
		parent::__construct();
		
		// check for valid usr
		$this->_validate_user('tools/validate');

		// get localized js
		$js_localized = json_lang('validate/validate_js', FALSE);
		$this->fuel->admin->load_js_localized($js_localized);

		// set pages input to blank if it is default value
		if (!empty($_POST['pages_input']))
		{
			if ($_POST['pages_input'] == lang('validate_pages_input'))
			{
				$_POST['pages_input'] = FALSE;
			}
		}
		
		// set jqx javascript parameters
		$this->js_controller = 'ValidateController';
		$this->js_controller_path = js_path('', VALIDATE_FOLDER);
		$this->js_controller_params['method'] = 'validate';
		$this->js_controller_params['module'] = 'tools';
	}
	
	function index()
	{
		$this->load->module_model(FUEL_FOLDER, 'pages_model');
		
		// TODO.... NEED TO FIX THIS METHOD OF GETTING ALL PAGES
		$pages = $this->fuel->pages->all_pages_including_views(TRUE);

		$vars['default_page_input'] = $this->fuel->validate->config('default_page_input');
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['validation_type'] = lang('validate_type_html');
		$vars['pages_select'] = $pages;
		$this->js_controller_params['method'] = 'validate';
		
		$crumbs = array('tools' => lang('section_tools'), lang('module_validate'));
		$this->fuel->admin->set_breadcrumb($crumbs, 'ico_tools_validate');
		$this->fuel->admin->render('validate', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

	function html()
	{
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));
		
		$validate_config = $this->config->item('validate');
		
		if ($this->input->post('uri'))
		{
			$uri = $this->input->post('uri');
			$results = $this->fuel->validate->html2($uri, TRUE);
			exit();
			$results = $this->fuel->validate->html($uri, TRUE);
			$this->output->set_output($results);
			return;
		} 
		else if (!$this->input->post('pages') AND !$this->input->post('pages_input') AND !$this->input->post('pages_serialized'))
		{
			$this->session->set_flashdata('error', lang('error_no_pages_selected'));
			redirect(fuel_uri('tools/validate'));
		}
		
		// check if the cache folder is writable in case we need to use it to create the HTML file for validation
		if (!is_writable($this->config->item('cache_path')))
		{
			$vars['error'] = lang('error_cache_folder_not_writable', $this->config->item('cache_path'));
		}
		
		// set the javascript jqx method
		$this->js_controller_params['method'] = 'html';
		
		// get all the pages to iterate over
		$pages = $this->_get_pages();
		$this->js_controller_params['pages'] = $pages;
		
		// serialize pages for reload value
		$vars['pages_serialized'] = base64_encode(serialize($pages));
		
		$vars['validation_type'] = lang('validate_type_html');
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('section_tools'), lang('module_validate'), lang('validate_type_html')), FALSE);
		
		// set breadcrumb
		$crumbs = array('tools' => lang('section_tools'), 'tools/validate' => lang('module_validate'), lang('validate_type_html'));
		$this->fuel->admin->set_breadcrumb($crumbs, 'ico_tools_validate');
		
		// render page
		$this->fuel->admin->render('run', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

	function links()
	{
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));

		if ($this->input->post('uri'))
		{
			$uri = $this->input->post('uri');
			$links = $this->fuel->validate->links($uri);

			$this->load->module_model(FUEL_FOLDER, 'pages_model');
			$page_data = $this->pages_model->find_by_location($uri, FALSE);
			
			$vars['valid'] = $links['valid'];
			$vars['invalid'] = $links['invalid'];
			$vars['total'] = count($links['invalid']) + count($links['valid']);

			$vars['link'] = $uri;
			$vars['edit_url'] = (!empty($page_data['id'])) ? fuel_url('pages/edit/'.$page_data['id']) : '';
			$output = $this->load->view('links_output', $vars, TRUE);
			
			$this->output->set_output($output);
			return;
		} 
		else if (!$this->input->post('pages') AND !$this->input->post('pages_input') AND !$this->input->post('pages_serialized'))
		{
			$this->session->set_flashdata('error', lang('error_no_pages_selected'));
			redirect(fuel_uri('tools/validate'));
		}
		
		// set the javascript jqx method
		$this->js_controller_params['method'] = 'links';
		
		// get all the pages to iterate over
		$pages = $this->_get_pages();
		$this->js_controller_params['pages'] = $pages;
		
		// serialize pages for reload value
		$vars['pages_serialized'] = base64_encode(serialize($pages));
		$vars['validation_type'] =  lang('validate_type_links');
		
		// set breadcrumb
		$crumbs = array('tools' => lang('section_tools'), 'tools/validate' => lang('module_validate'), lang('validate_type_links'));
		$this->fuel->admin->set_breadcrumb($crumbs, 'ico_tools_validate');
	
		// render page
		$this->fuel->admin->render('run', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

	function size_report()
	{
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));
		$this->load->helper('number');
		
		$validate_config = $this->config->item('validate');
		
		if ($this->input->post('uri'))
		{
			$uri = $this->input->post('uri');
			$vars = $this->fuel->validate->size_report($uri);
			$output = $this->load->view('size_report_output', $vars, TRUE);
			
			$this->output->set_output($output);
			return;
		} 
		else if (!$this->input->post('pages') AND !$this->input->post('pages_input') AND !$this->input->post('pages_serialized'))
		{
			$this->session->set_flashdata('error', lang('error_no_pages_selected'));
			redirect(fuel_uri('tools/validate'));
		}
		
		// set the javascript jqx method
		$this->js_controller_params['method'] = 'size';

		// get all the pages to iterate over
		$pages = $this->_get_pages();
		$this->js_controller_params['pages'] = $pages;
		
		// serialize pages for reload value
		$vars['pages_serialized'] = base64_encode(serialize($pages));
		$vars['validation_type'] = lang('validate_type_size_report');
		
		// set breadcrumb
		$crumbs = array('tools' => lang('section_tools'), 'tools/validate' => lang('module_validate'), lang('validate_type_size_report'));
		$this->fuel->admin->set_breadcrumb($crumbs, 'ico_tools_validate');
	
		// render page
		$this->fuel->admin->render('run', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}
	
	function _get_pages()
	{
		$pages_input = $this->input->post('pages_input', TRUE);
		$extra_pages = array();
		if (!empty($pages_input) AND $pages_input != lang('validate_pages_input'))
		{
			$extra_pages = explode("\n", $pages_input);
			foreach($extra_pages as $key => $page)
			{
				$extra_pages[$key] = site_url(trim($page));
			}
		}
		$post_pages = (!empty($_POST['pages'])) ? $this->input->post('pages', TRUE) : array();
		$pages = array_merge($post_pages, $extra_pages);
		
		if (empty($pages) )
		{
			$pages = $this->input->post('pages_serialized');
			if (empty($pages))
			{
				redirect(fuel_uri('tools/validate'));
			}
			else
			{
				$pages = unserialize(base64_decode($pages));
			}
		}
		
		return $pages;
	}

}
/* End of file validate.php */
/* Location: ./fuel/modules/validate/controllers/validate.php */