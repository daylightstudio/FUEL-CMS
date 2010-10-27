<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Controller_test extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setup()
	{
		$this->load_sql('test_generic_schema.sql');

		// load a basic MY_Model to test
		require_once('test_custom_records_model.php');
	}
	
	public function test_goto_page()
	{
		//http://code.google.com/p/phpquery/wiki/Manual
		$post['test']= 'test';
		$home = $this->load_page('home', $post);

		// $test = pq("#content")->size();
		// $expected = 1;
		// $this->run($test, $expected, 'Test for content node');

		$test = pq("#logo")->size();
		$expected = 1;
		$this->run($test, $expected, 'Test for logo node');
	}
	

}
