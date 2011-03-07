<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/tester'] = lang('module_tester');



/*
|--------------------------------------------------------------------------
| TOOL SETTING: Tester
|--------------------------------------------------------------------------
*/

// dsn for database connection. If not supplied, it will assume a test'group name is created in the database.php config
// set this to 'test' if you want load_page method to load pages from the test database to test
$config['tester']['dsn_group'] = 'test';

// the name of the test database... this will be used regardless of what is set in your DSN value
$config['tester']['db_name'] = 'fuel_test';

// the cookie jar file path used for CURL sessions
// more info http://www.php.net/manual/en/function.curl-setopt.php
$config['tester']['session_cookiejar_file'] = APPPATH.'cache/cookiefile.txt';

// servers allowed to run tests
$config['tester']['valid_testing_server_names'] = array('localhost', '192\.168\.:any');
