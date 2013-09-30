<?php

class Logout extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->config->load('fuel', TRUE);
		if (!$this->config->item('admin_enabled', 'fuel')) show_404();
	}
	
	public function _remap($segment)
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
			
			$redirect = uri_safe_decode($segment);
		}
		if ($segment == 'page_router' OR $redirect == 'page_router')
		{
			$redirect = $this->fuel->config('default_home_view');
		}
		redirect($redirect, 'location', 302, FALSE);
	}
}