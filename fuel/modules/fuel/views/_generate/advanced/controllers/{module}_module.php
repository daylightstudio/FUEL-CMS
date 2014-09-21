<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class {model_name}_module extends Fuel_base_controller {
	
	public $nav_selected = '{module}|{module}/:any';
	

	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('module_{module}')), FALSE);
		$crumbs = array('tools' => lang('section_tools'), lang('module_{module}'));
		$this->fuel->admin->set_titlebar($crumbs, 'ico_{module}');
		$this->fuel->admin->render('_admin/{module}', $vars);

	}
	
}