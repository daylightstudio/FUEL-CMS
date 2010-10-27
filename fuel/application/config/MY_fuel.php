<?php
// IMPORTANT: for a complete list of fuel configurations, go to the modules/fuel/config/fuel.php file

// the name to be displayed on the top left of the admin
$config['site_name'] = 'My Website';

// options are cms, views, auto... cms pulls views and variables from the database,
// views mode pulls views from the views folder and variables from the _variables folder
$config['fuel_mode'] = 'views';

// used for system emails. Needs to be overwritten by MY_fuel.php
$config['domain'] = '';

// default password to alert against
$config['default_pwd'] = 'admin';

// specifies which modules are allowed to be used in the fuel admin
$config['modules_allowed'] = array(
	'user_guide',
	'blog',
	'backup',
	'seo',
	'validate',
	'tester',
	'cronjobs'
	);

// default password to alert against
$config['admin_enabled'] = FALSE;

// max upload files size for assets
$config['assets_upload_max_size']	= 100000;

// max width for asset images beign uploaded
$config['assets_upload_max_width']  = 1024;

// max height for asset images beign uploaded
$config['assets_upload_max_height']  = 768;

$config['assets_excluded_dirs'] = array(
	'js',
	'css',
	'cache', 
	'swf', 
	);

/* End of file MY_fuel.php */
/* Location: ./application/config/MY_fuel.php */ 