<?php 
/*
|--------------------------------------------------------------------------
| Alaternative database DSN. This file is included in 
| application/database.php if in TESTING mode
|--------------------------------------------------------------------------
*/
include('tester'.EXT);

$db['test'] = $db['default'];
$db['test']['database'] = $config['tester']['db_name'];
if (defined('TESTING_DSN'))
{
	$active_group = TESTING_DSN;
}