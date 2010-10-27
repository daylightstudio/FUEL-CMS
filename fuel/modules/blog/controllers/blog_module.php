<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');
class Blog_module extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->config->module_load('blog', 'blog');
		$this->view_location = 'blog';
	}
	
	function dashboard()
	{
		$vars = array();
		$this->_render('dashboard', $vars);
	}

}