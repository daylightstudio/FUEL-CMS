<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Tools extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('fuel_auth');
	}
	
	function index()
	{
		$this->_validate_user('tools');
		$this->_render('tools');
	}

}