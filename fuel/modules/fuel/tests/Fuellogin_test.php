<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuellogin_test extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setup()
	{
		$this->load_sql('test_fuel_schema.sql', 'tester');
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
	
	public function _login($user_name = 'admin', $password = 'admin')
	{
		$post = array(
			'user_name' => $user_name,
			'password' => $password
		);
		
		$page = 'fuel/login';
		$dashboard = $this->load_page('fuel/login', $post);
		return $dashboard;
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
			'ico_blog_posts' => 'Blog Posts',
			'ico_blog_categories' => 'Blog Categories',
			'ico_blog_comments' => 'Blog Comments',
			'ico_blog_links' => 'Blog Links',
			'ico_blog_users' => 'Blog Authors',
			'ico_blog_settings' => 'Blog Settings',
			'ico_tools_user_guide' => 'User Guide',
			'ico_tools_backup' => 'Backup',
			'ico_tools_seo' => 'Page Analysis',
			'ico_tools_seo_google_keywords' => 'Google Keywords',
			'ico_tools_validate' => 'Validate',
			'ico_tools_tester' => 'Tester',
			'ico_tools_cronjobs' => 'Cronjobs',
			'ico_users' => 'Users',
			'ico_dashboard' => 'Dashboard',
			'ico_permissions' => 'Permissions',
			'ico_manage_cache' => 'Page Cache',
			'ico_manage_activity' => 'Activity Log',
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
			if (pq(".".$key)->size() !== 1)
			{
				return FALSE;
			}
		}
		
		return TRUE;

	}
	
	
	private function _has_errors()
	{
		$error = ($this->_has_page_error() OR $this->_has_404_error() OR $this->_has_db_error() OR $this->_has_general_error() OR $this->_has_php_error());
		return $error;
	}

	//for more on pq function 
	//http://code.google.com/p/phpquery/wiki/Manual
	private function _has_page_error()
	{
		return pq(".error")->size();
	}

	//for more on pq function 
	//http://code.google.com/p/phpquery/wiki/Manual
	private function _has_404_error()
	{
		return pq("#error_404")->size();
	}

	private function _has_db_error()
	{
		return pq("#error_db")->size();
	}

	private function _has_general_error()
	{
		return pq("#error_general")->size();
	}

	private function _has_php_error()
	{
		return pq("#error_php")->size();
	}
	

}
