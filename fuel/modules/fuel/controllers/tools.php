<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Tools extends Fuel_base_controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->_validate_user('tools');
		
		$vars['page_title'] = $this->fuel->admin->page_title(lang('section_tools'), FALSE);
		$this->fuel->admin->set_titlebar(lang('module_tools'), 'ico_tools');
		
		$this->fuel->admin->render('tools', $vars,  Fuel_admin::DISPLAY_NO_ACTION);
	}

}