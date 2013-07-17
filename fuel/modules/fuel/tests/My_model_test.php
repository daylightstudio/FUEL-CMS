<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class My_model_test extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setup()
	{
		$this->load_sql('test_fuel_schema.sql', 'fuel');
		$this->load_sql('test_generic_schema.sql', 'fuel');

		// load a basic models to test
		require_once('test_users_model.php');
	}
	
	public function test_json()
	{
		$test_custom_records_model = new Test_users_model();

		// test find_one
		$record = $test_custom_records_model->find_one(array('email' => 'dave@thedaylightstudio.com'));

		$test = $record->to_json();
		$expected = '{"id":"2","user_name":"dave","password":"21232f297a57a5a743894a0e4a801fc3","email":"dave@thedaylightstudio.com","first_name":"Dave","last_name":"McReynolds","bio":"Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.","role_id":"2","attributes":"","active":"yes","date_added":"2012-01-01 00:00:00","full_name":"Dave McReynolds"}';
		$this->run($test, $expected, 'to_json custom record object test');
	}

	public function test_formatters()
	{
		$test_custom_records_model = new Test_users_model();
		$user = $test_custom_records_model->find_one(array('user_name' => 'admin'));

		// load helper first
		$this->CI->load->helper('typography');
		
		$test_custom_records_model->add_formatter('string', 'auto_typography', 'formatted');
		$test = $user->bio_formatted;
		$expected = '<p>This is my bio.</p>';
		$this->run($test, $expected, 'formatter test formatted #1');

		$test_custom_records_model->remove_formatter('string', 'formatted');
		$test = $user->bio_formatted;
		$expected = 'This is my bio.';
		$this->run($test, $expected, 'formatter test formatted removed #2');

		$test_custom_records_model->add_formatter('datetime', array('date_formatter', 'm-Y'), 'formatted');
		$test = $user->date_added_formatted;
		$expected = '01-2012';
		$this->run($test, $expected, 'formatter test of date formatting with configuration argments set #3');

		$test_custom_records_model->remove_formatter('datetime', 'formatted');
		$test_custom_records_model->add_formatter('datetime', 'date_formatter', 'formatted');
		$test = $user->date_added_formatted('Y-m-d');
		$expected = '2012-01-02';
		$this->run($test, $expected, 'formatter test of date formatting using function call #4');

		$test_custom_records_model->remove_formatter('datetime', 'formatted');
		$test_custom_records_model->add_formatter('string', 'camelize');
		$test = $user->first_name_camelize;
		$expected = 'darth';
		$this->run($test, $expected, 'formatter test of camelize attached to string type #5');

		$test_custom_records_model->remove_formatter('string', 'camelize');
		$test = $user->first_name_camelize;
		$expected = 'Darth';
		$this->run($test, $expected, 'formatter test removal of camelize worked #6');

		$test_custom_records_model->add_formatter('first_name', 'camelize');
		$test = $user->first_name_camelize;
		$expected = 'darth';
		$this->run($test, $expected, 'formatter test using a field name #7');
	}

	public function test_foreign_key_relationship()
	{
		$test_custom_records_model = new Test_users_model();

		// set the foreign key
		$test_custom_records_model->foreign_keys = array('role_id' => array(FUEL_FOLDER => 'fuel_categories_model'));
		
		// grab a user
		$user = $test_custom_records_model->find_by_key(1);

		// setup category data
		$categories_model = $this->fuel->categories->model();
		$test_category1 = array('id' => 1, 'name' => 'Jedi', 'slug' => 'jedi', 'context' => 'jedi', 'active' => 1);
		$test_category2 = array('id' => 2, 'name' => 'Droid', 'slug' => 'droid', 'context' => 'droid', 'active' => 1);
		$test_category3 = array('id' => 3, 'name' => 'Ewok', 'slug' => 'droid', 'context' => 'ewok', 'active' => 1);
		
		// save category data
		$categories_model->save($test_category1);
		$categories_model->save($test_category2);


		$test = is_object($user->role) AND $user->role->name == 'Jedi';
		$expected = TRUE;
		$this->run($test, $expected, 'foreign key record object test #1');

		// now change category
		$user->role_id = 2;
		$user->save();

		$test = is_object($user->role) AND $user->role->name == 'Droid';
		$expected = TRUE;
		$this->run($test, $expected, 'foreign key record object test #2');

		// now try saving it by passing an object
		$role_obj = $categories_model->create($test_category3);
		$user->role = $role_obj;
		$user->save();

		$test = is_object($user->role) AND $user->role->name == 'Ewok';
		$expected = TRUE;
		$this->run($test, $expected, 'foreign key record object test by #3');

		$categories_model->truncate();
	}
	
	public function test_has_many_relationship()
	{
		$test_custom_records_model = new Test_users_model();

		// set the has_many key
		$test_custom_records_model->has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags'));

		$user = $test_custom_records_model->find_by_key(1);

		// setup category data
		$categories_model = $this->fuel->categories->model();
		$test_categories[] = array('id' => 1, 'name' => 'Jedi', 'slug' => 'jedi', 'context' => 'jedi', 'active' => 1);
		$test_categories[] = array('id' => 2, 'name' => 'Droid', 'slug' => 'droid', 'context' => 'droid', 'active' => 1);
		$test_categories[] = array('id' => 3, 'name' => 'Ewok', 'slug' => 'droid', 'context' => 'ewok', 'active' => 1);
		$categories_model->save($test_categories);

		// setup tags
		$tags_model = $this->fuel->tags->model();
		$test_tags[] = array('id' => 1, 'name' => 'Yoda', 'slug' => 'yoda', 'category_id' => 1, 'active' => 1);
		$test_tags[] = array('id' => 2, 'name' => 'Darth Vader', 'slug' => 'darth-vader', 'category_id' => 1, 'active' => 1);
		$test_tags[] = array('id' => 3, 'name' => 'C3P0', 'slug' => 'c3p0', 'category_id' => 2, 'active' => 1);
		
		// save tags
		$tags_model->save($test_tags);

		// test #1
		$test = $tags_model->has_error();
		$expected = FALSE;
		$this->run($test, $expected, 'has_many tags save test');

		// test #2
		$user->tags = array(1, 2, 3);
		$user->save();

		$test = count($user->tags);
		$expected = 3;
		$this->run($test, $expected, 'has_many tag record test');

		// test #3
		$test_custom_records_model->has_many = array('tags' => array(
																	'model' => array(FUEL_FOLDER => 'fuel_tags'), 
																	'where' => array('context' => 'jedi')
																	)
													);

		$test = count($user->tags);
		$expected = 2;
		$this->run($test, $expected, 'has_many test #3');

		// test #4
		$user_model = $user->get_tags(TRUE);
		$test = is_a($user_model, 'Fuel_tags_model');
		$expected = TRUE;
		$this->run($test, $expected, 'has_many model object returned test');

		$users = $user_model->db()->where(array('fuel_tags.name' => 'Yoda'));
		$test = count($users);
		$expected = 1;
		$this->run($test, $expected, 'has_many filter with active record test');

	}

	public function test_serialized()
	{
		$test_custom_records_model = new Test_users_model();
		$test_custom_records_model->serialized_fields = array('attributes');
		$user = $test_custom_records_model->find_by_key(1);

		$attributes = array('gender' => 'male', 'hair' => 'brown', 'sign' => 'capricorn');
		$user->attributes = $attributes;
		$user->save();

		$user = $test_custom_records_model->find_by_key(1);
		$test = $attributes;
		$expected = $user->attributes;
		$this->run($test, $expected, 'serialized field test');
	}

	public function test_linked_fields()
	{
		$test_custom_records_model = new Test_users_model();
		$test_custom_records_model->linked_fields = array('user_name' => array('email' => 'mirrored'));

		$user = $test_custom_records_model->create();
		$user->email = 'han@milleniumfalcon.com';
		$user->first_name = 'Han';
		$user->last_name = 'Solo';
		$user->save();

		$test = $user->errors();
		$expected = array();
		$this->run($test, $expected, 'linked field saved without errors test');

		$user->refresh();
		$test = $user->user_name;
		$expected = $user->email;
		$this->run($test, $expected, 'linked field mirrored test');

		// now delete to cleanup
		$user->delete();
	}

	public function test_short_name()
	{
		$test_custom_records_model = new Test_users_model();
		$test = $test_custom_records_model->short_name();
		$expected = 'Test_users';
		$this->run($test, $expected, 'short_name test');

		$test = $test_custom_records_model->short_name(TRUE);
		$expected = 'test_users';
		$this->run($test, $expected, 'short_name lowercase test');

		$test = $test_custom_records_model->short_name(FALSE, TRUE);
		$expected = 'Test_user';
		$this->run($test, $expected, 'short_name record test');

		$test = $test_custom_records_model->short_name(TRUE, TRUE);
		$expected = 'test_user';
		$this->run($test, $expected, 'short_name lowercase record test');
	}

	public function test_friendly_names()
	{
		$test_custom_records_model = new Test_users_model();
		$test = $test_custom_records_model->friendly_name();
		$expected = 'Users';
		$this->run($test, $expected, 'friendly name test');

		$test = $test_custom_records_model->friendly_name(TRUE);
		$expected = 'users';
		$this->run($test, $expected, 'friendly name lower case test');

		$test = $test_custom_records_model->singular_name();
		$expected = 'User';
		$this->run($test, $expected, 'singular_name');

		$test = $test_custom_records_model->singular_name(TRUE);
		$expected = 'user';
		$this->run($test, $expected, 'singular_name lower case test');
	}


	public function test_table_name()
	{
		$test_custom_records_model = new Test_users_model();
		$test = $test_custom_records_model->table_name();
		$expected = 'users';
		$this->run($test, $expected, 'table_name test');
	}

	public function test_tables()
	{
		$test_custom_records_model = new Test_users_model();
		$tables = $this->CI->fuel->config('tables');
	
		$expected = array(
		    'fuel_archives' => 'fuel_archives',
		    'fuel_blocks' => 'fuel_blocks',
		    'fuel_categories' => 'fuel_categories',
		    'fuel_logs' => 'fuel_logs',
		    'fuel_navigation' => 'fuel_navigation',
		    'fuel_navigation_groups' => 'fuel_navigation_groups',
		    'fuel_pages' => 'fuel_pages',
		    'fuel_pagevars' => 'fuel_page_variables',
		    'fuel_permissions' => 'fuel_permissions',
		    'fuel_relationships' => 'fuel_relationships',
		    'fuel_settings' => 'fuel_settings',
		    'fuel_tags' => 'fuel_tags',
		    'fuel_users' => 'fuel_users'
		);

		$test_custom_records_model->set_tables($tables);
		$test = $test_custom_records_model->tables();
		$this->run($test, $expected, 'table tests');
	}

	public function test_key_field()
	{
		$test_custom_records_model = new Test_users_model();
		$test = $test_custom_records_model->key_field();
		$expected = 'id';
		$this->run($test, $expected, 'key_field test');
	}

	public function test_fields()
	{
		$test_custom_records_model = new Test_users_model();
		$test = $test_custom_records_model->fields();
		$expected = array(
  				'id',
  				'user_name',
  				'password',
  				'email',
  				'first_name',
  				'last_name',
  				'bio',
  				'role_id',
  				'attributes',
  				'active',
  				'date_added'
			);
		$this->run($test, $expected, 'fields test');
	}

	public function test_find_by_key()
	{
		$test_custom_records_model = new Test_users_model();

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
		$test_custom_records_model = new Test_users_model();
	
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
		$test_custom_records_model = new Test_users_model();

		// test find_all
		$results = $test_custom_records_model->find_all(array('active' => 'yes'));
		$test = count($results);
		$expected = 2;
		$this->run($test, $expected, 'find_all custom record object test');
	}

	public function test_record_count()
	{
		$test_custom_records_model = new Test_users_model();

		$test = $test_custom_records_model->record_count(array('user_name' => 'admin'));
		$expected = 1;
		$this->run($test, $expected, 'record count test');

		$test = $test_custom_records_model->total_record_count();
		$expected = 3;
		$this->run($test, $expected, 'total record count test');
	}

	public function test_save()
	{

		$test_custom_records_model = new Test_users_model();

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
