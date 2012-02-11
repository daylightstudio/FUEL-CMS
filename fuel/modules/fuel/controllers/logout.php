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
		$this->load->helper('convert');
		$this->fuel->auth->logout();
		$config = array(
			'name' => $this->fuel->auth->get_fuel_trigger_cookie_name(),
			'path' => WEB_PATH
		);
		delete_cookie($config);
		
		$redirect = $this->fuel->config('logout_redirect');
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