<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');

class Validate extends Fuel_base_controller {
	
	public $nav_selected = 'tools/validate|tools/validate/:any';
	
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
		
		$this->load->module_model(FUEL_FOLDER, 'pages_model');
	}
	
	function index()
	{
		$pages = $this->fuel->pages->options_list('all', TRUE);
		
		$fields['pages'] = array('type' => 'multi', 'options' => $pages, $this->input->post('pages'));
		
		$fields['pages_input'] = array('type' => 'textarea', 'value' => $this->fuel->validate->config('default_page_input'), 'class' => 'no_editor', 'cols' => 5, 'rows' => 100, 'placeholder' => lang('validate_pages_input'));
		$this->load->library('form_builder');
		$this->form_builder->question_keys = array();
		$this->form_builder->submit_value = null;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$form = $this->form_builder->render();
		$vars['form'] = $form;
		
		$vars['form_action'] = 'tools/validate/html';
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['validation_type'] = lang('validate_type_html');
		$this->js_controller_params['method'] = 'validate';
		
		$crumbs = array('tools' => lang('section_tools'), lang('module_validate'));
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_validate');
		$this->fuel->admin->render('_admin/validate', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

	function html()
	{
		$validate_config = $this->config->item('validate');
		
		if ($this->input->post('uri'))
		{
			
			$uri = $this->input->post('uri');
			$page_data = $this->pages_model->find_by_location($uri, FALSE);
			
			$vars = $this->fuel->validate->html($uri);
			$vars['link'] = $uri;
			$vars['edit_url'] = (!empty($page_data['id'])) ? fuel_url('pages/edit/'.$page_data['id']) : '';
			
			$vars['body'] = $this->load->view('_admin/html_output', $vars, TRUE);
			$output = $this->load->view('_layouts/validate_layout', $vars, TRUE);
			
			$this->output->set_output($output);
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
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_validate');
		
		// render page
		$this->fuel->admin->render('_admin/run', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

	function links()
	{
		if ($this->input->post('uri'))
		{
			$uri = $this->input->post('uri');
			$page_data = $this->pages_model->find_by_location($uri, FALSE);

			$vars = $this->fuel->validate->links($uri);
			$vars['link'] = $uri;
			$vars['edit_url'] = (!empty($page_data['id'])) ? fuel_url('pages/edit/'.$page_data['id']) : '';
			$vars['body'] = $this->load->view('_admin/links_output', $vars, TRUE);
			$output = $this->load->view('_layouts/validate_layout', $vars, TRUE);
			
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
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_validate');
	
		// render page
		$this->fuel->admin->render('_admin/run', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

	function size_report()
	{
		$this->load->helper('number');
		
		$validate_config = $this->config->item('validate');
		
		if ($this->input->post('uri'))
		{
			$uri = $this->input->post('uri');
			$page_data = $this->pages_model->find_by_location($uri, FALSE);
			
			$vars = $this->fuel->validate->size_report($uri);
			$vars['edit_url'] = (!empty($page_data['id'])) ? fuel_url('pages/edit/'.$page_data['id']) : '';
			$vars['body'] = $this->load->view('_admin/size_report_output', $vars, TRUE);
			$output = $this->load->view('_layouts/validate_layout', $vars, TRUE);
			
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
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_validate');
	
		// render page
		$this->fuel->admin->render('_admin/run', $vars, Fuel_admin::DISPLAY_NO_ACTION);
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