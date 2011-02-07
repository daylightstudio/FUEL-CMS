<?php 
define('TESTER_VERSION', '0.9.2');
define('TESTER_FOLDER', 'tester');
define('TESTER_PATH', MODULES_PATH.TESTER_FOLDER.'/');

// used for testing... basically triggers it to check for if it is a valid server name for us to run tests
if (!empty($_COOKIE['tester_dsn']))
{
	// just having a cookie isn't enough... so we check the valid server names before setting it to testing mode
	// this is needed for CURLing pages with the Tester_base::load_page() method
	include('tester'.EXT);
	
	if (!empty($config['tester']['valid_testing_server_names']))
	{
		$valid = FALSE;
		$servers = (array) $config['tester']['valid_testing_server_names'];
		foreach($servers as $server)
		{
			$server = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $server));
			if (preg_match('#^'.$server.'$#', $_SERVER['SERVER_NAME'])) $valid = TRUE;
		}
		
		// if the SERVER_NAME is found in the config, then we set it to testing
		if ($valid)
		{
			define('TESTING', TRUE);
			define('TESTING_DSN', $_COOKIE['tester_dsn']);
		}
	}
}