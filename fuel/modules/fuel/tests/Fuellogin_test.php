<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Fueltest_base.php');

class Fuellogin_test extends Fueltest_base {
	
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

}
