<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Controller_test extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setup()
	{
		$this->load_sql('test_fuel_schema.sql');
	}
	
	public function test_goto_page()
	{
		//http://code.google.com/p/phpquery/wiki/Manual
		$post['test']= 'home';
		$home = $this->load_page('home', $post);

		$test = pq("#main")->size();
		$expected = 1;
		$this->run($test, $expected, 'Test for content node');

		$test = pq("#logo")->size();
		$expected = 1;
		$this->run($test, $expected, 'Test for logo node');
	}
	

}
