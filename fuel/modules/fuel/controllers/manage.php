<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Manage extends Fuel_base_controller {
	
	public $nav_selected = 'manage';
	public $module_uri = 'manage/activity';
	
	function __construct()
	{
		parent::__construct();
		$this->js_controller = 'fuel.controller.ManageController';
	}
	
	function index()
	{
		$this->_validate_user('manage');
		$crumbs = array(lang('section_manage'));
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->render('manage');
	}
	
	function cache()
	{
		$this->_validate_user('cache');
		
		$this->fuel->admin->set_nav_selected('manage/cache');
		
		if ($post = $this->input->post('action'))
		{
			$this->fuel->cache->clear();
			
			$msg = lang('cache_cleared');
			$this->fuel->logs->write($msg);
			$this->fuel->admin->set_notification(lang('cache_cleared'), Fuel_admin::NOTIFICATION_SUCCESS);
			
			redirect('fuel/manage/cache');
		}
		else 
		{
			$crumbs = array('manage' => lang('section_manage'), lang('module_manage_cache'));
			$this->fuel->admin->set_titlebar($crumbs, 'ico_manage_cache');
			$this->fuel->admin->render('manage/cache');
		}
	}

}