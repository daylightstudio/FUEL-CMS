<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_test_base extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
		
		// load needed helpers
		$this->CI->load->helper('convert');
	}

	public function setup()
	{
		$this->load_sql('test_fuel_schema.sql', 'fuel');
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
	
	
	protected function _is_success()
	{
		return pq(".success")->size();
	}

	protected function _has_errors()
	{
		$error = ($this->_has_page_error() OR $this->_has_404_error() OR $this->_has_db_error() OR $this->_has_general_error() OR $this->_has_php_error());
		return $error;
	}

	//for more on pq function 
	//http://code.google.com/p/phpquery/wiki/Manual
	protected function _has_page_error()
	{
		return $this->page_contains(".error");
	}

	//for more on pq function 
	//http://code.google.com/p/phpquery/wiki/Manual
	protected function _has_404_error()
	{
		return $this->page_contains("#error_404");
	}

	protected function _has_db_error()
	{
		return $this->page_contains("#error_db");
	}

	protected function _has_general_error()
	{
		return $this->page_contains("#error_general");
	}

	protected function _has_php_error()
	{
		return $this->page_contains("#error_php");
	}
	

}
