<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class My_modules extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$vars['modules'] = $this->fuel->modules->advanced();
		$crumbs = array(lang('section_my_modules'));
		$this->fuel->admin->set_titlebar($crumbs);
		
		$this->fuel->admin->render('manage/my_modules', $vars);
	}
	
	function install($module = NULL)
	{
		
		$module = 'test';
		//$this->fuel->modules->install($module);
		$this->fuel->install->activate('backup');
	}
	
	function uninstall($module = NULL)
	{
		$this->fuel->set_module($module);
		//$this->fuel->install->deactivate();
		$this->fuel->$module->deactivate();
	}

	
}