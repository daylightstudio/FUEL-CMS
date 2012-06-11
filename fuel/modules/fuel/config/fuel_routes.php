<?php 
/*
|--------------------------------------------------------------------------
| FUEL ROUTES
|--------------------------------------------------------------------------
|
| The following are routes used by FUEL specifically
|
*/
$route[substr(FUEL_ROUTE, 0, -1)] = "fuel/dashboard";

$module_folder = MODULES_PATH.'/';

// config isn't loaded yet so do it manually'
include(FUEL_PATH.'config/fuel.php');

// load any public routes for advanced modules
foreach ($config['modules_allowed'] as $module)
{
	$routes_path = $module_folder.$module.'/config/'.$module.'_routes.php';
	
	if (file_exists($routes_path))
	{
		include($routes_path);
	}
}

// to prevent the overhead of this on every request, we do a quick check of the path... USE_FUEL_ROUTES is defined in fuel_constants
if (USE_FUEL_ROUTES)
{

	$route[FUEL_ROUTE.'login|'.FUEL_ROUTE.'login/:any'] = "fuel/login"; // so we can pass forward param
	
	$module_folder = MODULES_PATH;

	include(FUEL_PATH.'config/fuel_modules.php');
	@include(APPPATH.'/config/MY_fuel_modules.php');
	
	$modules = array_keys($config['modules']);
	$modules = array_merge($config['modules_allowed'], $modules);

	foreach($modules as $module)
	{
		// check FUEL folder for controller first... if not there then we use the default module to map to
		if (!file_exists($module_folder.FUEL_FOLDER.'/controllers/'.$module.EXT)
				AND !file_exists($module_folder.$module.'/controllers/'.$module.'_module'.EXT) 
				)
		{
			$route[FUEL_ROUTE.$module] = FUEL_FOLDER.'/module';
			$route[FUEL_ROUTE.$module.'/(.*)'] = FUEL_FOLDER.'/module/$1';
		}
		
		// check if controller does exist in FUEL folder and if so, create the proper ROUTE if it does not equal the FUEL_FOLDER
		else if (file_exists($module_folder.FUEL_FOLDER.'/controllers/'.$module.EXT)) 
		{
			$route[FUEL_ROUTE.$module] = FUEL_FOLDER.'/'.$module;
			$route[FUEL_ROUTE.$module.'/(.*)'] = FUEL_FOLDER.'/'.$module.'/$1';
		}

		// check module specific folder next
		else if (file_exists($module_folder.$module.'/controllers/'.$module.'_module'.EXT))
		{
			$route[FUEL_ROUTE.$module] = $module.'/'.$module.'_module';
			$route[FUEL_ROUTE.$module.'/(.*)'] = $module.'/'.$module.'_module/$1';
		}
	}
	// catch all
	$route[FUEL_ROUTE.'(:any)'] = FUEL_FOLDER."/$1";
}


/* End of file fuel_routes.php */
/* Location: ./modules/fuel/config/fuel_routes.php */