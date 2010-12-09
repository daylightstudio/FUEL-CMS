<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/user_guide'] = 'User Guide';



/*
|--------------------------------------------------------------------------
| User Guide specific parameters
|--------------------------------------------------------------------------
*/

// user guide requires user authentication to view
$config['user_guide_authenticate'] = TRUE;

// the URI path to the user guide
$config['user_guide_root_url'] = FUEL_ROUTE.'tools/'.USER_GUIDE_FOLDER.'/';
