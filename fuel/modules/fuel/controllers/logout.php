<?php

class Logout extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->config->load('fuel', TRUE);
		if (!$this->config->item('admin_enabled', 'fuel')) show_404();
	}
	
	function _remap($segment)
	{
		$this->load->library('session');
		$this->session->sess_destroy();
		$this->load->module_library(FUEL_FOLDER, 'fuel_auth');
		$this->load->helper('cookie');
		$this->fuel_auth->logout();
		$config = array(
			'name' => $this->fuel_auth->get_fuel_trigger_cookie_name(),
			'path' => WEB_PATH
		);
		delete_cookie($config);
		
		$redirect = $this->config->item('logout_redirect', 'fuel');
		if ($redirect == ':last')
		{
			$this->load->helper('convert');
			
			// if ($segment == 'index')
			// {
			// 	$redirect = fuel_uri('login');
			// }
			// else
			// {
				$redirect = uri_safe_decode($segment);
			//}
		}
		redirect($redirect);
	}
}