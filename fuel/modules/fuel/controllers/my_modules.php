<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class My_modules extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$vars = array();
		$this->_render('modules', $vars);
	}
	
}