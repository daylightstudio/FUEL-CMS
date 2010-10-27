<?php 
/*
|--------------------------------------------------------------------------
| FUEL ROUTES
|--------------------------------------------------------------------------
|
| The following are routes used by fuel specifically
|
*/

$route['fuel'] = "fuel/dashboard";
$route['fuel/login/:any'] = "fuel/login"; // so we can pass forward param

// to prevent the overhead of this on every request, we do a quick check of the path... IN_FUEL_ADMIN is defined in a presystem hook
if (IN_FUEL_ADMIN) {
	
	$module_folder = APPPATH.MODULES_FOLDER.'/';

	// config isn't loaded yet so do it manually'
	include($module_folder.FUEL_FOLDER.'/config/fuel.php');
	include($module_folder.FUEL_FOLDER.'/config/fuel_modules.php');

	$modules = array_keys($config['modules']);
	$modules = array_merge($config['modules_allowed'], $modules);

	foreach($modules as $module){
		
		// grab any routes in the module specific folder
		$routes_path = $module_folder . $module . '/config/' . $module . '_routes.php';
		
		if (file_exists($routes_path))
		{
			include($routes_path);
		}
		// check FUEL folder for controller first
		else if (!file_exists($module_folder.FUEL_FOLDER.'/controllers/'.$module.EXT)
				&& !file_exists($module_folder.$module.'/controllers/'.$module.'_module'.EXT))
		{
			$route[FUEL_FOLDER.'/'.$module] = FUEL_FOLDER.'/module';
			//$route[FUEL_FOLDER.'/'.$module.'/:num'] = FUEL_FOLDER.'/module/items';
			$route[FUEL_FOLDER.'/'.$module.'/(.*)'] = FUEL_FOLDER.'/module/$1';
		}

		// check module specific folder next
		else if (file_exists($module_folder.$module.'/controllers/'.$module.'_module'.EXT))
		{
			$route[FUEL_FOLDER.'/'.$module] = $module.'/'.$module.'_module';
		//	$route[FUEL_FOLDER.'/'.$module.'/:num'] = $module.'/'.$module.'_module/items';
			$route[FUEL_FOLDER.'/'.$module.'/(.*)'] = $module.'/'.$module.'_module/$1';
		}

		// default to just using the FUEL module
		else
		{
			//$route['fuel/'.$module.'/:num'] = FUEL_FOLDER.'/'.$module."/items";
		}

	}
}

/* End of file fuel_routes.php */
/* Location: ./codeigniter/application/modules/fuel/config/fuel_routes.php */