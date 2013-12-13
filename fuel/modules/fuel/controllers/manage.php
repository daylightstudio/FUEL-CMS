<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Manage extends Fuel_base_controller {
	
	public $nav_selected = 'manage';
	public $module_uri = 'manage/activity';
	
	public function __construct()
	{
		parent::__construct(FALSE);
		$this->js_controller = 'fuel.controller.ManageController';
	}
	
	public function index()
	{
		$this->_validate_user('manage');
		$crumbs = array(lang('section_manage'));
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->render('manage');
	}
	
	public function cache()
	{
		$this->_validate_user('manage/cache');	
		
		$this->fuel->admin->set_nav_selected('manage/cache');
		
		if ($this->input->post('action'))
		{
			$msg = $this->clear_cache(TRUE);
			$this->fuel->admin->set_notification($msg, Fuel_admin::NOTIFICATION_SUCCESS);
			redirect('fuel/manage/cache');
		}
		else 
		{
			$crumbs = array('manage' => lang('section_manage'), lang('module_manage_cache'));

			$this->fuel->admin->set_titlebar($crumbs, 'ico_manage_cache');
			$this->fuel->admin->render('manage/cache');
		}
	}

	public function clear_cache($return = FALSE)
	{
		$remote_ips = $this->fuel->config('webhook_remote_ip');
		$is_web_hook = ($this->fuel->auth->check_valid_ip($remote_ips));

		// check if it is CLI or a web hook otherwise we need to validate
		$validate = (php_sapi_name() == 'cli' OR defined('STDIN') OR $is_web_hook) ? FALSE : TRUE;

		if ($validate)
		{
			$this->_validate_user('manage/cache');	
		}

		$this->fuel->cache->clear();
		$msg = lang('cache_cleared');

		$this->fuel->logs->write($msg);
		if ($return)
		{
			return $msg;	
		}
		echo $msg."\n";
	}

}