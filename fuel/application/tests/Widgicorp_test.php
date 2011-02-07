<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Widgicorp_test extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setup()
	{
		$this->load_sql('widgicorp_test.sql', NULL);
	}
	
	// TEST 1 test general page errors
	public function test_no_general_page_errors()
	{
		$pages = $this->_get_pages();

		foreach($pages as $page)
		{
			$this->load_page($page);
			$test = $this->_check_errors();
			$expected = FALSE;
			$this->run($test, $expected, 'Test for general page errors on page: '.$page);
		}
	}

	// TEST 2 test home page
	public function test_home()
	{
		$this->load_page('home');
		$test = pq("title")->text();
		$expected = 'WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test homepage title');
	}

	// TEST 3 test about page
	public function test_about()
	{
		$this->load_page('about');
		$test = pq("title")->text();
		$expected = 'About : WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test About title');
	}
	
	// TEST 4 test services page
	public function test_services()
	{
		$page = $this->load_page('about/services');
		
		$test = pq("title")->text();
		$expected = 'Services : About : WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test Services title');
	}

	// TEST 5 test team page
	public function test_team()
	{
		$this->load_page('about/team');
		$test = pq("title")->text();
		$expected = 'Team : About : WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test Team title');
	}
	
	// TEST 6 test what they say page
	public function test_what_they_say()
	{
		$this->load_page('about/what-they-say');
		$test = pq("title")->text();
		$expected = 'What They Say : About : WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test What They Say title');
	}

	// TEST 7 test showcase
	public function test_showcase()
	{
		$this->load_page('showcase');
		$test = pq("title")->text();
		$expected = 'Showcase : WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test Showcase title');
		
		$page = $this->load_page('showcase/project/nuts-over-bolts');
		$test = (strpos($page, '<li><strong>Client:</strong> Yoda</li>') !== FALSE);
		$expected = TRUE;
		$this->run($test, $expected, 'Test Showcase project page content');
		
	}
	
	// TEST 8 test showcase
	public function test_blog()
	{
		$this->load_page('blog');
		$test = pq("title")->text();
		$expected = 'WidgiCorp Blog';
		$this->run($test, $expected, 'Test Blog title');
		
		// test blog post exists
		$blog = $this->load_page('blog');
		$test = pq("h2")->text();
		$expected = 'A long, long time ago, in a galaxy far, far away';
		$this->run($test, $expected, 'Test Blog post title');
	}

	// TEST 9 test contact
	public function test_contact()
	{
		$this->load_page('contact');
		$test = pq("title")->text();
		$expected = 'Contact : WidgiCorp - Fine Makers of Widgets';
		$this->run($test, $expected, 'Test Contact title');
		
		// test contact email
		$post['first_name'] = 'Darth';
		$post['last_name'] = 'Vader';
		$post['email'] = 'darth@deathstar.com';
		$post['question'] = 'Do I look good in black?';
		
		// must have email set in applicaton/config/MY_config.php
		$contact = $this->load_page('contact', $post, TRUE);
		$test = pq(".success")->size();
		$expected = 1;
		$this->run($test, $expected, 'Test Contact email... if failed make sure you have an admin email address set in applicaton/config/MY_config.php');
		
		// and check your email now
	}

	private function _get_pages()
	{
		$this->CI->load->module_model(FUEL_FOLDER, 'pages_model');
		$pages = $this->CI->pages_model->all_pages_including_views(TRUE, FALSE);

		// remove project page and add dynamic ones
		unset($pages['showcase/project']);
		$this->CI->load->model('projects_model');
		$projects = $this->CI->projects_model->find_all(); // won't filter on published because they all should be'
		
		// add project pages
		foreach($projects as $project)
		{
			$key = 'showcase/project/'.$project->slug;
			$pages[$key] = $key;
		}
		return $pages;
	}
	
	private function _check_errors()
	{
		$error = ($this->_has_404_error() OR $this->_has_db_error() OR $this->_has_general_error() OR $this->_has_php_error());
		return $error;
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
