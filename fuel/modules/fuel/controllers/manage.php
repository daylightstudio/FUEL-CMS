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
		$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_NO_ACTION);
		$this->fuel->admin->render('manage');
	}
	
	function cache(){
		$this->_validate_user('manage/cache');
		
		$this->nav_selected = 'manage/cache';
		
		if ($post = $this->input->post('action'))
		{
			$this->fuel->cache->clear();
			
			$msg = lang('cache_cleared');
			$this->logs_model->write($msg);
			$this->session->set_flashdata('success', 'The cache has been cleared.');
			redirect('fuel/manage/cache');
		}
		else 
		{
			$crumbs = array('manage' => lang('section_manage'), lang('module_manage_cache'));
			$this->fuel->admin->set_titlebar($crumbs);
			$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_NO_ACTION);
			
			$this->fuel->admin->render('manage/cache');
		}
	}

}