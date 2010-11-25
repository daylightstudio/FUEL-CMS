<?php 
// INSTALL_ROOT is defined in the index.php bootstrap file
define('FUEL_VERSION', '0.91');
define('MODULES_FOLDER', '../modules');
define('FUEL_FOLDER', 'fuel');
define('FUEL_PATH', APPPATH.MODULES_FOLDER.'/'.FUEL_FOLDER.'/');
define('IN_FUEL_ADMIN', (strpos($_SERVER['REQUEST_URI'], '/'.FUEL_FOLDER.'/') !== FALSE));
define('WEB_ROOT', str_replace('\\', '/', realpath(dirname(SELF)).DIRECTORY_SEPARATOR)); // replace \ with / for windows
$_SERVER['SCRIPT_NAME'] = preg_replace('#^/(.+)\.php/(.*)#', '/$1.php', $_SERVER['SCRIPT_NAME']);
define('WEB_PATH', str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
$segs = explode('/', FCPATH);
define('WEB_FOLDER', $segs[count($segs)-2]);
define('MODULES_WEB_PATH', FUEL_FOLDER.'/modules/');
if ($_SERVER['SERVER_PORT'] != '80')
{
	define('BASE_URL', "http://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
}
else
{
	define('BASE_URL', "http://".$_SERVER['SERVER_NAME'].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
}	

// must include string helper if you want to use lang function
include(APPPATH.'helpers/MY_string_helper.php');
include(APPPATH.MODULES_FOLDER.'/fuel/config/fuel.php');
foreach($config['modules_allowed'] as $module)
{
	$constants_path = APPPATH.MODULES_FOLDER.'/' . $module . '/config/' . $module . '_constants.php';
	if (file_exists($constants_path))
	{
		require_once($constants_path);
	}
}

/* End of file fuel_constants.php */
/* Location: ./application/modules/fuel/config/fuel_constants.php */