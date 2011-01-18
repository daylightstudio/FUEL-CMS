<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Site_docs extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->_validate_user('site_docs');
	}
	
	function _remap()
	{
		$this->load->module_library(FUEL_FOLDER, 'fuel_pagevars');
		
		if ($this->_has_module('user_guide'))
		{
			$this->load->helper(USER_GUIDE_FOLDER, 'user_guide');
		}
		
		$this->load->helper('text');
		$page = uri_path(TRUE, 1);

		if (empty($page)) $page = 'index';
		$this->fuel_pagevars->vars_path = APPPATH.'views/_variables/';
		$vars = $this->fuel_pagevars->view_variables($page, 'site_docs');
		$vars['body'] = 'index';

		// render page
		if (file_exists(APPPATH.'/views/_docs/'.$page.'.php'))
		{
			// use app module which is the application directory
			$vars['body'] = $this->load->module_view('app', '_docs/'.$page, $vars, TRUE);
			
			// get layout page
			if (file_exists(APPPATH.'views/_layouts/documentation.php'))
			{
				$this->load->module_view(NULL, '_layouts/documentation', $vars);
			}
			else if ($this->_has_module('user_guide'))
			{
				$vars['page_title'] = $this->config->item('site_name', 'fuel');
				$this->load->view('_layouts/documentation', $vars);
			}
			else
			{
				$this->output->set_output($vars['body']);
			}
		}
		else
		{
			show_404();
		}
		
		
	}
}