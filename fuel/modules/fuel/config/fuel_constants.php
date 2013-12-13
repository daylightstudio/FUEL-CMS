<?php 
// INSTALL_ROOT is defined in the index.php bootstrap file
define('FUEL_VERSION', '1.0');
define('MODULES_FOLDER', '../modules');
define('FUEL_FOLDER', 'fuel');
define('MODULES_PATH', APPPATH.MODULES_FOLDER.'/');
define('MODULES_FROM_APPCONTROLLERS', '../'.MODULES_FOLDER.'/');
define('FUEL_PATH', MODULES_PATH.FUEL_FOLDER.'/');
define('WEB_ROOT', str_replace('\\', '/', realpath(dirname(SELF)).DIRECTORY_SEPARATOR)); // replace \ with / for windows

// needed to take care of some server environments
$_SERVER['SCRIPT_NAME'] = preg_replace('#^/(.+)\.php/(.*)#', '/$1.php', $_SERVER['SCRIPT_NAME']);
define('WEB_PATH', str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));

// change slashes for some Windows platforms
$_FUEL_SEGS = explode('/', str_replace("\\", '/', $_SERVER['SCRIPT_FILENAME']));

define('WEB_FOLDER', (count($_FUEL_SEGS) > 1) ? $_FUEL_SEGS[count($_FUEL_SEGS)-2] : '/');
define('MODULES_WEB_PATH', FUEL_FOLDER.'/modules/');


// must include language helper if you want to use lang function
include(APPPATH.'helpers/MY_language_helper.php');
include(FUEL_PATH.'config/fuel.php');

// used for CLI... must create $_SERVER['REQUEST_URI]
if (defined('STDIN'))
{
	$args = array_slice($_SERVER['argv'], 1);
	$_SERVER['REQUEST_URI'] = $args ? implode('/', $args) : '';
}



define('FUEL_ROUTE', $config['fuel_path']);


// DETECT URI PATH
if (isset($_SERVER['REQUEST_URI']))
{
	$_URI_PATH = $_SERVER['REQUEST_URI'];
}
else if (isset($_SERVER['PATH_INFO']))
{
	$_URI_PATH = $_SERVER['PATH_INFO'];
}
else if (isset($_SERVER['ORIG_PATH_INFO']))
{
	$_URI_PATH = $_SERVER['ORIG_PATH_INFO'];
}

define('USE_FUEL_ROUTES', (strpos($_URI_PATH, '/'.$config['fuel_path']) !== FALSE));

foreach($config['modules_allowed'] as $module)
{
	$constants_path = MODULES_PATH . $module . '/config/' . $module . '_constants.php';
	if (file_exists($constants_path))
	{
		require_once($constants_path);
	}
}


if (!defined('BASE_URL'))
{
	$_base_path = $_SERVER['HTTP_HOST'].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

	if (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) !== 'off' OR $_SERVER['SERVER_PORT'] == 443)
	{
		define('BASE_URL', "https://".$_base_path);
	}
	else
	{
		define('BASE_URL', "http://".$_base_path);
	}
}

/* End of file fuel_constants.php */
/* Location: ./modules/fuel/config/fuel_constants.php */