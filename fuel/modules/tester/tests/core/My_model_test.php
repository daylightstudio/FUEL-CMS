<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class My_model_test extends Tester_base {
	
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
	
	public function test_find_by_key()
	{
		$test_custom_records_model = new Test_custom_records_model();

		// test find_by_key
		$record = $test_custom_records_model->find_by_key(1);
		$test = $record->full_name;
		$expected = 'Darth Vader';
		$this->run($test, $expected, 'find_by_key custom record object property test');
	
		// test get_full_name() method version
		$test = $record->get_full_name();
		$this->run($test, $expected, 'find_one custom record object method test');
	}
	
	public function test_find_one()
	{
		$test_custom_records_model = new Test_custom_records_model();
	
		// test find_one
		$record = $test_custom_records_model->find_one(array('email' => 'dave@thedaylightstudio.com'));
		$test = $record->full_name;
		$expected = 'Dave McReynolds';
		$this->run($test, $expected, 'find_one custom record object property test');
		
		// test get_full_name() method version
		$test = $record->get_full_name();
		$this->run($test, $expected, 'find_one custom record object method test');

		// test find_one_array
		$record = $test_custom_records_model->find_one_array(array('email' => 'dvader@deathstar.com'));
		$test = $record['full_name'];
		$expected = 'Darth Vader';
		$this->run($test, $expected, 'find_one custom record object test');
	}
	
	public function test_find_all()
	{
		$test_custom_records_model = new Test_custom_records_model();

		// test find_all
		$results = $test_custom_records_model->find_all(array('active' => 'yes'));
		$test = count($results);
		$expected = 2;
		$this->run($test, $expected, 'find_one custom record object test');
	}
		
	public function test_save()
	{

		$test_custom_records_model = new Test_custom_records_model();

		// test save without email to get required error
		$record = $test_custom_records_model->create();
		$record->first_name = 'John';
		$record->last_name = 'Smith';
		$record->save();
		$test = $record->is_valid();
		$expected = FALSE;
		$this->run($test, $expected, 'test save without email to get required error');

		// test save with invalid email
		$record = $test_custom_records_model->create();
		$record->first_name = 'John';
		$record->last_name = 'Smith';
		$record->email = 'jsmithXX.com';
		$record->save();
		$test = $record->is_valid();
		$expected = FALSE;
		$this->run($test, $expected, 'test save with invalid email');

		// test save with valid email
		$record = $test_custom_records_model->create();
		$record->first_name = 'John';
		$record->last_name = 'Smith';
		$record->email = 'jsmith@test.com';
		$record->save();
		$test = $record->is_valid();
		$expected = TRUE;
		$this->run($test, $expected, 'test save with valid email');

	}

}
