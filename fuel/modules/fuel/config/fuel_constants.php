<?php 
// INSTALL_ROOT is defined in the index.php bootstrap file
define('FUEL_VERSION', '0.9.3');
define('MODULES_FOLDER', '../modules');
define('FUEL_FOLDER', 'fuel');
define('MODULES_PATH', APPPATH.MODULES_FOLDER.'/');
define('FUEL_PATH', MODULES_PATH.FUEL_FOLDER.'/');
define('WEB_ROOT', str_replace('\\', '/', realpath(dirname(SELF)).DIRECTORY_SEPARATOR)); // replace \ with / for windows

// needed to take care of some server environments
$_SERVER['SCRIPT_NAME'] = preg_replace('#^/(.+)\.php/(.*)#', '/$1.php', $_SERVER['SCRIPT_NAME']);
define('WEB_PATH', str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));

// change slashes for some Windows platforms
$_FUEL_SEGS = explode('/', str_replace("\\", '/', $_SERVER['SCRIPT_FILENAME']));

define('WEB_FOLDER', $_FUEL_SEGS[count($_FUEL_SEGS)-2]);
define('MODULES_WEB_PATH', FUEL_FOLDER.'/modules/');

if ($_SERVER['SERVER_PORT'] == '443' OR $_SERVER['SERVER_PORT'] == '80')
{
	$_base_path = $_SERVER['SERVER_NAME'].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	
	if ($_SERVER['SERVER_PORT'] == '443')
	{
		define('BASE_URL', "https://".$_base_path);
	}
	else
	{
		define('BASE_URL', "http://".$_base_path);
	}
}
else
{
	define('BASE_URL', "http://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
}	

// must include language helper if you want to use lang function
include(APPPATH.'helpers/MY_language_helper.php');
include(FUEL_PATH.'config/fuel.php');

// used for CLI... must create $_SERVER['REQUEST_URI]
if (defined('STDIN'))
{
	$args = array_slice($_SERVER['argv'], 1);
	$_SERVER['REQUEST_URI'] = $args ? implode('/', $args) : '';
}

define('IN_FUEL_ADMIN', (strpos($_SERVER['REQUEST_URI'], '/'.$config['fuel_path']) !== FALSE));
define('FUEL_ROUTE', $config['fuel_path']);

foreach($config['modules_allowed'] as $module)
{
	$constants_path = MODULES_PATH . $module . '/config/' . $module . '_constants.php';
	if (file_exists($constants_path))
	{
		require_once($constants_path);
	}
}

/* End of file fuel_constants.php */
/* Location: ./modules/fuel/config/fuel_constants.php */