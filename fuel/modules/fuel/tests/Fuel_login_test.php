<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Fuel_test_base.php');

class Fuel_login_test extends Fuel_test_base {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function test_login()
	{
		$page = 'Dashboard';
		
		// test for invalid login
		$this->_login('admin', 'XXX');
		$test = $this->_has_errors();
		$expected = TRUE;
		$this->run($test, $expected, 'Test for invalid login');
		
		// test valid login
		$this->_login('admin', 'admin');
		$test = $this->_has_errors();
		$expected = FALSE;
		$this->run($test, $expected, 'Test for valid login');
		
		// test the proper left menu items are there
		$test = $this->_check_leftmenu();
		$expected = TRUE;
		$this->run($test, $expected, 'Test for left menu items: '.$page);

	}
	
	public function _check_leftmenu($exclude = array())
	{
		$leftmenu_classes = array(
			'ico_dashboard' => 'Dashboard',
			'ico_pages' => 'Pages',
			'ico_blocks' => 'Blocks',
			'ico_navigation' => 'Navigation',
			'ico_assets' => 'Assets',
			'ico_sitevariables' => 'Site Variables',
			'ico_users' => 'Users',
			'ico_permissions' => 'Permissions',
			'ico_manage_cache' => 'Page Cache',
			'ico_logs' => 'Activity Log',
			'ico_settings' => 'Settings',
		);
		
		$menu = $leftmenu_classes;
		
		// exclude if either the key or value is in the exclude array
		if (!empty($exclude))
		{
			$menu = array();
			foreach($leftmenu_classes as $key => $val)
			{
				if (!in_array($val, $exclude) AND !in_array($key, $exclude))
				{
					$menu[$key] = $val;
				}
			}
		}
		$menu = (!empty($exclude)) ? $leftmenu_classes : array_diff($leftmenu_classes, $exclude);
		
		$valid = TRUE;
		foreach($menu as $key => $val)
		{
			if (pq(".".$key)->size() === 0)
			{
				return FALSE;
			}
		}
		
		return TRUE;

	}

}
