<?php
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL Configuration
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/general/configuration
 */

/*
|--------------------------------------------------------------------------
| General Settings
|--------------------------------------------------------------------------
*/

// the name of the site to be displayed at the top. Also used to generate your session key
$config['site_name'] = 'MyWebsite';

// path to the fuel admin from the web base directory... MUST HAVE TRAILING SLASH!
$config['fuel_path'] = FUEL_FOLDER.'/';

// options are cms, views, auto... cms pulls views and variables from the database,
// views mode pulls views from the views folder and variables from the _variables folder.
// The auto option will first check the database for a page and if it doesn't exist or is not published, it will then check for a corresponding view file.
$config['fuel_mode'] = 'views';

// used for system emails. Can be overwritten by MY_fuel.php
$config['domain'] = $_SERVER['SERVER_NAME'];

// the page to redirect to AFTER logging in
$config['login_redirect'] = $config['fuel_path'].'dashboard';

// the page to redirect to AFTER logging out. . Use the special value :last to redirect to the last page you were on.
//$config['logout_redirect'] = $config['fuel_path'].'login'; // to take you back to the login page instead of the last page you were on
$config['logout_redirect'] = ':last';

// used for system emails
$config['from_email'] = 'admin@'.$config['domain'];

// allow for login link to allow forgotten passwords
$config['allow_forgotten_password'] = TRUE;

// archiving
$config['max_number_archived'] = 5;

// warn if a form field has changed before leaving page
$config['warn_if_modified'] = TRUE;

// max number of recent pages to display
$config['max_recent_pages'] = 5;

// the maximum number of pages that page state will be saved before dumping the last one saved. 
// This is used on the list pages in the admin to save sorting and filtering. Used to save on space needed for session.
$config['saved_page_state_max'] = 5;

// provide a cookie path... different from the CI config if you need it (default is same as CI config)
$config['fuel_cookie_path'] = '/';

// external css file for additional styles possibly needed for 3rd party integration and customizing.
// must exist in the assets/css file and not the fuel/assets/css folder
$config['xtra_css'] = '';

// keyboard shortcuts
$config['keyboard_shortcuts'] = array(
	'toggle_view' => 'Ctrl+Shift+m', 
	'save' => 'Ctrl+Shift+s', 
	'view' => 'Ctrl+Shift+p');

// dashboard modules to include
$config['dashboards'] = array('fuel', 'backup');

// dashboard rss
$config['dashboard_rss'] = 'http://www.getfuelcms.com/blog/feed/rss';

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


/*
|--------------------------------------------------------------------------
| Asset settings 
|--------------------------------------------------------------------------
*/

// paths specific to FUEL... relative to the WEB_ROOT
$config['fuel_assets_path'] = 'fuel/modules/{module}/assets/';

// excludes certain folders from being viewed
$config['assets_excluded_dirs'] = array('js', 'css', 'cache', 'swf', 'captchas');

// allow subfolders to be created in the assets folder if they don't exist'
$config['assets_allow_subfolder_creation'] = TRUE;

// specifies what filetype extensions can be included in the folders
$config['editable_asset_filetypes'] = array('images' => 'jpg|jpeg|jpe|gif|png', 'pdf' => 'pdf', 'media' => 'jpg|jpeg|jpe|png|gif|mov|mp3|aiff|pdf|css');

// max upload files size for assets
$config['assets_upload_max_size']	= '1000';

// max width for asset images being uploaded
$config['assets_upload_max_width']  = '1024';

// max height for asset images being uploaded
$config['assets_upload_max_height']  = '768';

// javascript files (mostly jquery plugins) to be included other then the controller js files
$config['fuel_javascript'] = array(
	'jquery/plugins/date',
	'jquery/plugins/jquery.datePicker',
	'jquery/plugins/jquery.fillin',
	'jquery/plugins/jquery.easing',
	'jquery/plugins/jquery.bgiframe',
	'jquery/plugins/jquery.tooltip',
	'jquery/plugins/jquery.scrollTo-min',
	'jquery/plugins/jqModal',
	'jquery/plugins/jquery.checksave',
	'jquery/plugins/jquery.form',
	'jquery/plugins/jquery.treeview.min',
	'jquery/plugins/jquery.hotkeys',
	'jquery/plugins/jquery.cookie',
	'jquery/plugins/jquery.fillin',
	'jquery/plugins/jquery.selso',
	'jquery/plugins/jquery-ui-1.8.4.custom.min',
	'jquery/plugins/jquery.disable.text.select.pack',
	'jquery/plugins/jquery.supercomboselect',
	'jquery/plugins/jquery.MultiFile',
	'jquery/plugins/jquery.tablednd.js',
	'editors/markitup/jquery.markitup.pack',
	'editors/markitup/jquery.markitup.set',
	'editors/ckeditor/ckeditor.js',
	'fuel/linked_field_formatters.js',
);

// css other then the fuel.css file which automatically gets loaded
$config['fuel_css'] = array();

// allow for asset optimization. Requires that all module folders have a writable assets/cache folder
// values can be TRUE, FALSE, inline, gzip, whitespace, or combine. See the config/asset.php file for more info
$config['fuel_assets_output'] = FALSE;


/*
|--------------------------------------------------------------------------
| Security settings 
|--------------------------------------------------------------------------
*/

// restrict fuel to only certain ip addresses (array only value so can include multiple)
$config['restrict_to_remote_ip'] = array();

// default password to alert against
$config['default_pwd'] = 'admin';

// enable the FUEL admin or not?
$config['admin_enabled'] = FALSE;

// the number of times someone can attempt to login before they are locked out for 1 minute
$config['num_logins_before_lock'] = 3;

// the number of seconds to lock out a person upon reaching the max number failed login attempts
$config['seconds_to_unlock'] = 60;

// If you set a dev password, the site will require a password to view
$config['dev_password'] = '';

// will auto search view files. 
// If the URI is about/history and the about/history view does not exist but about does, it will render the about page
$config['auto_search_views'] = FALSE;

// functions that can be used for the sanitize_input value on a basic module. 
// The key of the array is what should be used when configuring your module
$config['module_sanitize_funcs'] = array(
	'xss' => 'xss_clean', 
	'php' => 'encode_php_tags', 
	'template' => 'php_to_template_syntax', 
	'entities' => 'htmlentities',
);

/*
|--------------------------------------------------------------------------
| Module settings
|--------------------------------------------------------------------------
*/

// specifies which modules are allowed to be used in the FUEL admin
$config['modules_allowed'] = array('blog', 'tools');

// site... Dashboard will always be there
$config['nav']['site'] = array(
	'dashboard' => lang('module_dashboard'),
	'pages' => lang('module_pages'),
	'blocks' => lang('module_blocks'),
	'navigation' => lang('module_navigation'),
	'assets' => lang('module_assets'),
	'sitevariables' => lang('module_sitevariables')
	);

// my modules... if set to auto, then it will automatically include all in MY_fuel_modules.php
$config['nav']['shop'] = array();

// blog placeholder if it exists
$config['nav']['blog'] = array();

// my modules... if set to auto, then it will automatically include all in MY_fuel_modules.php
$config['nav']['modules'] = 'AUTO';

// tools
$config['nav']['tools'] = array();

// manage
$config['nav']['manage'] = array(
	'users' => lang('module_users'), 
	'permissions' => lang('module_permissions'),
	'manage/cache' => lang('module_manage_cache'), 
	'manage/activity' => lang('module_manage_activity')
	);

/*
|--------------------------------------------------------------------------
| Fuel Router settings
|--------------------------------------------------------------------------
*/

// the default view for home
$config['default_home_view'] = 'home';

// turn on cache. Can be TRUE/FALSE or cms
$config['use_page_cache'] = 'cms';

// how long to cache the page. A value of 0 means forever until the page or other modules have been updated
$config['page_cache_ttl'] = 0;

// the name of the group the cache is associated with (so you can just remove the group)
$config['page_cache_group'] = 'pages';

// maximum number of paramters that can be passed to the page. Used to cut down on queries to the db
$config['max_page_params'] = 0;


/*
|--------------------------------------------------------------------------
| DB Table settings
|--------------------------------------------------------------------------
*/

// the FUEL specific database tables
$config['tables'] = array(
	'archives' => 'fuel_archives',
	'logs' => 'fuel_logs',
	'navigation' => 'fuel_navigation',
	'navigation_groups' => 'fuel_navigation_groups',
	'pagevars' => 'fuel_page_variables',
	'pages' => 'fuel_pages',
	'blocks' => 'fuel_blocks',
	'permissions' => 'fuel_permissions',
	'user_to_permissions' => 'fuel_user_to_permissions',
	'users' => 'fuel_users'
);

/*
|--------------------------------------------------------------------------
| Page settings
|--------------------------------------------------------------------------
*/

// the group to associate with the auto-created navigation item
$config['auto_page_navigation_group_id'] = 1;

// automatically removes the following path from the location
$config['page_uri_prefix'] = '';


@include(APPPATH.'config/MY_fuel.php');

/* End of file fuel.php */
/* Location: ./modules/fuel/config/fuel.php */