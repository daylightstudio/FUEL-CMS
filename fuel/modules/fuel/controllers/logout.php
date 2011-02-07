<?php

class Logout extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->config->load('fuel', TRUE);
		if (!$this->config->item('admin_enabled', 'fuel')) show_404();
	}
	
	function index()
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
		redirect(fuel_uri('login'));
	}
}