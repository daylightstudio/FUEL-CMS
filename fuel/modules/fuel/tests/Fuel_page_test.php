<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Fuel_test_base.php');

class Fuel_page_test extends Fuel_test_base {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setup()
	{
		parent::setup();
		$this->_login();
	}
	
	public function test_save_new_page()
	{
		
		// create new page
		$post = array();
		$post['id'] = NULL;
		$post['location'] = 'test';
		$post['layout'] = 'none'; // blank page
		$post['navigation_label'] = '';
		$post['published'] = 'yes';
		$post['cache'] = 'yes';
		$post['date_added'] = NULL;
		$post['last_modified'] = NULL;
		$post['last_modified_by'] = NULL;
		$post['vars--page_title'] = 'test';
		$post['vars--meta_description'] = 'test';
		$post['vars--meta_keywords'] = 'test';
		$post['vars--body'] = 'This is a test';
		$post['vars--body_class'] = NULL;
		
		$page = $this->load_page('fuel/pages/create', $post);
		$test = $this->_is_success();
		$expected = TRUE;
		$this->run($test, $expected, 'Test for successful save of new page');
	}
	
	public function test_check_new_page()
	{
		$page = $this->load_page('test');
		$test = $this->page_contains('This is a test', FALSE);
		$expected = TRUE;
		$this->run($test, $expected, 'Test for contents on page');
	}

	public function test_edit_page()
	{
		$post = array();
		$post['id'] = 1;
		$post['location'] = 'test';
		$post['layout'] = 'none'; // blank page
		$post['navigation_label'] = '';
		$post['published'] = 'yes';
		$post['cache'] = 'yes';
		$post['date_added'] = NULL;
		$post['last_modified'] = NULL;
		$post['last_modified_by'] = NULL;
		$post['vars--page_title'] = 'test';
		$post['vars--meta_description'] = 'test';
		$post['vars--meta_keywords'] = 'test';
		$post['vars--body'] = 'This is a the second test';
		$post['vars--body_class'] = NULL;
		
		$page = $this->load_page('fuel/pages/edit/1', $post);
		$test = $this->_is_success();
		$expected = TRUE;
		$this->run($test, $expected, 'Test for successful edit of page');

		// check if page contains archived values
		$test = $this->page_contains('#fuel_restore_version option[value="1"]', TRUE);
		$expected = TRUE;
		$this->run($test, $expected, 'Test for archived version of page');
	}
	
	public function test_check_edited_page()
	{
		$page = $this->load_page('test');
		$test = $this->page_contains('This is a the second test', FALSE);
		$expected = TRUE;
		$this->run($test, $expected, 'Test for contents on page');
		
		
	}
	
	

}
