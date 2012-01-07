<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/user_guide'] = lang('module_user_guide');



/*
|--------------------------------------------------------------------------
| User Guide specific parameters
|--------------------------------------------------------------------------
*/
$config['user_guide'] = array();

// user guide requires user authentication to view
$config['user_guide']['authenticate'] = TRUE;

// the URI path to the user guide
$config['user_guide']['root_url'] = FUEL_ROUTE.'/tools/'.USER_GUIDE_FOLDER.'/';
