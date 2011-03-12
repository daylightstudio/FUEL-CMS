<?php
// IMPORTANT: for a complete list of fuel configurations, go to the modules/fuel/config/fuel.php file

// path to the fuel admin from the web base directory... MUST HAVE TRAILING SLASH!
$config['fuel_path'] = 'fuel/';

// the name to be displayed on the top left of the admin
$config['site_name'] = 'My Website';

// options are cms, views, auto... cms pulls views and variables from the database,
// views mode pulls views from the views folder and variables from the _variables folder.
// The auto option will first check the database for a page and if it doesn't exist or is not published, it will then check for a corresponding view file.
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

// will auto search view files. 
// If the URI is about/history and the about/history view does not exist but about does, it will render the about page
$config['auto_search_views'] = FALSE;

// max upload files size for assets
$config['assets_upload_max_size']	= 5000;

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

// text editor settings... options are markitup or ckeditor
$config['text_editor'] = 'markitup';

// ck editor specific settings
$config['ck_editor_settings'] = array(
	'toolbar' => array(
			//array('Source'),
			array('Bold','Italic','Strike'),
			array('Format'),
			array('Image','HorizontalRule'),
			array('NumberedList','BulletedList'),
			array('Link','Unlink'),
			array('Undo','Redo','RemoveFormat'),
			array('Preview'),
			array('Maximize'),
		),
	'contentsCss' => WEB_PATH.'assets/css/main.css',
	'htmlEncodeOutput' => FALSE,
	'entities' => FALSE,
	'bodyClass' => 'ckeditor',
	'toolbarCanCollapse' => FALSE,
);


/* End of file MY_fuel.php */
/* Location: ./application/config/MY_fuel.php */