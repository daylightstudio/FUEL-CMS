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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
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
 * @link		http://docs.getfuelcms.com/installation/configuration
 */

/*
|--------------------------------------------------------------------------
| General Settings
|--------------------------------------------------------------------------
*/

// The name of the site to be displayed at the top. Also used to generate your session key
$config['site_name'] = 'MyWebsite';

// Path to the fuel admin from the web base directory... MUST HAVE TRAILING SLASH!
$config['fuel_path'] = FUEL_FOLDER.'/';

// Options are cms, views, auto... cms pulls views and variables from the database,
// Views mode pulls views from the views folder and variables from the _variables folder.
// The auto option will first check the database for a page and if it doesn't exist or is not published, it will then check for a corresponding view file.
// Additionally, you can create an array value specifying the keys, 'pages', 'blocks' and 'navigation' to specify how those modules work individually 
// (e.g.  array('pages' => 'AUTO', 'blocks' => 'views', 'navigation' => 'cms');)
$config['fuel_mode'] = 'views';

// Used for system emails. Can be overwritten by MY_fuel.php
$config['domain'] = $_SERVER['SERVER_NAME'];

// The page to redirect to AFTER logging in
$config['login_redirect'] = $config['fuel_path'].'dashboard';

// The page to redirect to AFTER logging out. . Use the special value :last to redirect to the last page you were on.
//$config['logout_redirect'] = $config['fuel_path'].'login'; // to take you back to the login page instead of the last page you were on
$config['logout_redirect'] = ':last';

// Used for system emails
$config['from_email'] = 'admin@'.$config['domain'];

// Allow for login link to allow forgotten passwords
$config['allow_forgotten_password'] = TRUE;

// Archiving
$config['max_number_archived'] = 5;

// Warn if a form field has changed before leaving page
$config['warn_if_modified'] = TRUE;

// Max number of recent pages to display
$config['max_recent_pages'] = 5;

// The maximum number of pages that page state will be saved before dumping the last one saved.
// This is used on the list pages in the admin to save sorting and filtering. Used to save on space needed for session.
$config['saved_page_state_max'] = 0;

// Provide a cookie path... different from the CI config if you need it (default is same as CI config)
$config['fuel_cookie_path'] = WEB_PATH;

// External css file for additional styles possibly needed for 3rd party integration and customizing.
// Must exist in the assets/css file and not the fuel/assets/css folder
$config['xtra_css'] = '';

// The main layout file to be used for the interface.
// By default, it will pull from the fuel module folder however, if an array is specified, the key will be the module and the value will be the view file.
$config['main_layout'] = 'admin_main';

// Keyboard shortcuts
$config['keyboard_shortcuts'] = array(
	'toggle_view' => 'Ctrl+Shift+m', 
	'save' => 'Ctrl+Shift+s', 
	'view' => 'Ctrl+Shift+p'
);

// Dashboard modules to include
$config['dashboards'] = array('fuel');

// Dashboard rss
$config['dashboard_rss'] = 'https://www.getfuelcms.com/blog/feed/rss';

// text editor settings  (options are markitup or ckeditor)
// markitup: allows you to visualize the code in its raw format - not wysiwyg (http://markitup.jaysalvat.com/)
// ckeditor: suitable for clients; shows what the output will look like in the page (http://ckeditor.com/)
// for more editor settings, look at the config/editors.php configuration file
$config['text_editor'] = 'markitup';

// An associative array of objects to attach to the fuel object
$config['attach'] = array();


/*
|--------------------------------------------------------------------------
| Language settings 
|--------------------------------------------------------------------------
*/

// Languages for pages. The key is saved to the page variables
$config['languages'] = array(
	'english' => 'English'
);

// Specifies the method in which to look for pages with different languages.
// Values can be "domain", "segment", "query_string" or "both" (which means both "segment" and "query_string")
$config['language_mode'] = 'domain';

// Append the current language value to the site URL automatically
$config['add_language_to_site_url'] = FALSE;

// force the default language segment, or query string on any URLs created with site_url
$config['language_force_default_to_site_url'] = FALSE;

// The name of the query string parameter to use for setting the language
$config['language_query_str_param'] = 'lang';

// The name of the cookie to hold the currently selected language. One will be generated if left blank
$config['language_cookie_name'] = '';

// Default is 2 years
$config['language_cookie_exp'] = '63072000';

// Use cookies to remember a selected language
$config['language_use_cookies'] = TRUE;

// Will check the user agent during language detection
$config['language_detect_user_agent'] = '';

// The default language to use
$config['language_default_option'] = NULL;


/*
|--------------------------------------------------------------------------
| Asset settings 
|--------------------------------------------------------------------------
*/

// Paths specific to FUEL... relative to the WEB_ROOT
$config['fuel_assets_path'] = 'fuel/modules/{module}/assets/';

// Excludes certain folders from being viewed
$config['assets_excluded_dirs'] = array('js', 'css', 'cache', 'swf', 'captchas');

// Allow subfolders to be created in the assets folder if they don't exist'
$config['assets_allow_subfolder_creation'] = TRUE;

// Specifies what filetype extensions can be included in the folders
$config['editable_asset_filetypes'] = array(
	'images' => 'jpg|jpeg|jpe|gif|png|zip|svg',
	'pdf' => 'pdf|zip',
	'media' => 'mov|mp3|aiff|mpeg|zip',
	'assets' => 'jpg|jpeg|jpe|png|gif|mov|mpeg|mp3|wav|aiff|pdf|css|zip|svg'
);

// Max upload files size for assets
$config['assets_upload_max_size']	= '1000';

// Max width for asset images being uploaded
$config['assets_upload_max_width']  = '1024';

// Max height for asset images being uploaded
$config['assets_upload_max_height']  = '768';

// Javascript files (mostly jquery plugins) to be included other then the controller js files
$config['fuel_javascript'] = array(
	
	'fuel' => array(
	'jquery/plugins/jquery-ui-1.8.17.custom.min',
	'jquery/plugins/jquery.easing',
	'jquery/plugins/jquery.bgiframe',
	'jquery/plugins/jquery.tooltip',
	'jquery/plugins/jquery.scrollTo-min',
	'jquery/plugins/jqModal',
	'jquery/plugins/jquery.checksave',
	'jquery/plugins/jquery.form',
	'jquery/plugins/jquery.treeview',
	'jquery/plugins/jquery.serialize',
	'jquery/plugins/jquery.cookie',
	'jquery/plugins/jquery.supercookie',
	'jquery/plugins/jquery.hotkeys',
	'jquery/plugins/jquery.cookie',
	'jquery/plugins/jquery.simpletab.js',
	'jquery/plugins/jquery.tablednd.js',
	'jquery/plugins/jquery.placeholder',
	'jquery/plugins/jquery.selso',
	'jquery/plugins/jquery.disable.text.select.pack',
	'jquery/plugins/jquery.supercomboselect',
	'jquery/plugins/jquery.MultiFile',
	'fuel/linked_field_formatters',
	'jquery/plugins/jquery.numeric',
	'jquery/plugins/jquery.repeatable',

	// NASTY Chrome JS bug...
	// http://stackoverflow.com/questions/10314992/chrome-sometimes-calls-incorrect-constructor
	// http://stackoverflow.com/questions/10251272/what-could-cause-this-randomly-appearing-error-inside-jquery-itself
	'jquery/plugins/chrome_pushstack_fix.js',
	'jqx/plugins/util.js',
	'fuel/global')
	
	// This is actually slower it appears now???
	//'fuel' => 'fuel/fuel.min'
);

// CSS other then the fuel.css file which automatically gets loaded
$config['fuel_css'] = array();

// Allow for asset optimization. Requires that all module folders have a writable assets/cache folder
// Values can be TRUE, FALSE, inline, gzip, whitespace, or combine. See the config/asset.php file for more info
$config['fuel_assets_output'] = FALSE;

// Set this value to the file permission that will be applied with chmod() after a file gets uploaded
// Value should be a unix file permission integer (i.e. 0755)
$config['set_upload_file_perms'] = FALSE;


/*
|--------------------------------------------------------------------------
| Security settings 
|--------------------------------------------------------------------------
*/

// Sets the site to offline mode. Uses "offline" view file to render page
$config['offline'] = FALSE;

// Sets the site password min length. An empty (e.g. NULL/0) value will not require a minimum length
$config['password_min_length'] = NULL;

// Sets the site password max length. An empty (e.g. NULL/0) value will not require a maximum length
$config['password_max_length'] = NULL;

// Sets specific patterns that the password must match (e.g. 'upper|lower|numbers|symbols')
$config['password_pattern_match'] = NULL;

// List of uri allowed to access when site switched to offline mode.
// Eg.:
// To allow "www.example.com/UAT"
// $config['offline_allowed_uri'] = array('UAT');
$config['offline_allowed_uri'] = array();

// Restrict fuel to only certain ip addresses (can be string or an array of IP addresses)
$config['restrict_to_remote_ip'] = array();

// Restrict fuel webhooks like the migrate functionality to only certain IP address (can be string or an array of IP addresses)
$config['webhook_remote_ip'] = array();

// Sefault password to alert against
$config['default_pwd'] = 'admin';

// Enable the FUEL admin or not?
$config['admin_enabled'] = FALSE;

// The number of times someone can attempt to login before they are locked out for 1 minute
$config['num_logins_before_lock'] = 3;

// The number of seconds to lock out a person upon reaching the max number failed login attempts
$config['seconds_to_unlock'] = 60;

// If you set a dev password, the site will require a password to view
$config['dev_password'] = '';

// Will auto search view files. The max_page_params config can also be used for this as well
// If the URI is about/history and the about/history view does not exist but about does, it will render the about page
$config['auto_search_views'] = FALSE;

// Functions that can be used for the sanitize_input value on a basic module.
// The key of the array is what should be used when configuring your module
$config['module_sanitize_funcs'] = array(
	'xss' => 'xss_clean', 
	'php' => 'encode_php_tags', 
	'template' => 'php_to_template_syntax', 
	'entities' => 'htmlentities',
	'purify' => 'html_purifier',
);

// Allow the evaluation of PHP code
$config['allow_eval_php_code'] = FALSE;

/*
|--------------------------------------------------------------------------
| Module settings
|--------------------------------------------------------------------------
*/

// Specifies which modules are allowed to be used in the FUEL admin (e.g. 'user_guide', 'blog', 'backup'...)
$config['modules_allowed'] = array();

// Site... Dashboard will always be there
$config['nav']['site'] = array(
	'dashboard' => lang('module_dashboard'),
	'pages' => lang('module_pages'),
	'blocks' => lang('module_blocks'),
	'navigation' => lang('module_navigation'),
	'tags' => lang('module_tags'),
	'categories' => lang('module_categories'),
	'assets' => lang('module_assets'),
	'sitevariables' => lang('module_sitevariables')
);

// My modules... if set to empty array, then it will automatically include all in MY_fuel_modules.php
$config['nav']['modules'] = array();

// Tools
$config['nav']['tools'] = array();

// Manage
$config['nav']['manage'] = array(
	'users' => lang('module_users'),
	'permissions' => lang('module_permissions'),
	'manage/cache' => lang('module_manage_cache'),
	'logs' => lang('module_manage_activity'),
	'settings' => lang('module_manage_settings'),
);

// Will auto arrange the navigation into the normal order
$config['nav_auto_arrange'] = TRUE;

// Determines wether to display the action buttons when using inline modal window
$config['allow_inline_actions'] = FALSE;

/*
|--------------------------------------------------------------------------
| Fuel Router settings
|--------------------------------------------------------------------------
*/

// The default view for home
$config['default_home_view'] = 'home';

// Turn on cache. Can be TRUE/FALSE or cms
$config['use_page_cache'] = 'cms';

// How long to cache the page. A value of 0 means forever until the page or other modules have been updated
$config['page_cache_ttl'] = 0;

// The name of the group the cache is associated with (so you can just remove the group)
$config['page_cache_group'] = 'pages';

// Maximum number of parameters that can be passed to the page. Used to cut down on queries to the db.
// If it is an array, then it will loop through the array using the keys to match against a regular expression:
// $config['max_page_params'] = array('about/news/' => 1);
$config['max_page_params'] = 0;

// A list of URI paths that will always pull from the view folder... can use :any, like routes
// good to use if you are passing page parameters to your pages controlled in the admin and
// you have a page you always want to pull from a view file 
// (e.g. URI = company/press and you have a page of "company" in the admin with max page params set to 1 or more 
// it would normally pull the company page if no company/press page existed in the admin and in this case 
// we want to pull the view file of company/press)
$config['uri_view_overwrites'] = array();


/*
|--------------------------------------------------------------------------
| DB Table settings
|--------------------------------------------------------------------------
*/

// The FUEL specific database tables
$config['tables'] = array(
	'fuel_archives' => 'fuel_archives',
	'fuel_blocks' => 'fuel_blocks',
	'fuel_categories' => 'fuel_categories',
	'fuel_logs' => 'fuel_logs',
	'fuel_navigation' => 'fuel_navigation',
	'fuel_navigation_groups' => 'fuel_navigation_groups',
	'fuel_pages' => 'fuel_pages',
	'fuel_pagevars' => 'fuel_page_variables',
	'fuel_permissions' => 'fuel_permissions',
	'fuel_relationships' => 'fuel_relationships',
	'fuel_settings' => 'fuel_settings',
	'fuel_tags' => 'fuel_tags',
	'fuel_users' => 'fuel_users'
);


/*
|--------------------------------------------------------------------------
| Page settings
|--------------------------------------------------------------------------
*/

// The group to associate with the auto-created navigation item
$config['auto_page_navigation_group_id'] = 1;

// Automatically removes the following path from the location
$config['page_uri_prefix'] = '';

// View the page from the admin in a new window or within a modal window
$config['view_in_new_window'] = TRUE;

/*
|--------------------------------------------------------------------------
| Parsing engine settings
|--------------------------------------------------------------------------
*/

// The parsing engine to use for FUEL. Options are dwoo, ci and now 'twig'!
$config['parser_engine'] = 'dwoo';

// Runs the parsing process twice for pages created in the CMS which allows
// for variables to be set from within blocks and layout fields and can
// bubble up to the layout view file (takes slightly longer to render
// if caching is turned off). Valid values are TRUE, FALSE, or 'AUTO' which
// will be activated if fuel_set_var is called from within a block or page
// layout variable. This can also be set as a property of a layout object
$config['double_parse'] = FALSE;

// The directory to put the parsed compiled files
$config['parser_compile_dir'] = APPPATH.'cache/dwoo/compiled/';

// The folder permissions to put the parsed compiled files
$config['parser_compile_dir_perms'] = DIR_WRITE_MODE;

// The file permissions for the parsed compiled files (Dwoo only)
$config['parser_compile_file_perms'] = FILE_WRITE_MODE;

// The delimiters used by the parsing engine
$config['parser_delimiters'] = array(
				'tag_comment'   => array('{#', '#}'), // Twig only
				'tag_block'     => array('{%', '%}'), // Twig only
				'tag_variable'  => array('{', '}'), // Used by Twig, Dwoo and CI. Default for twig is '{{', '}}'
				'interpolation' => array('#{', '}'), // Twig only
			);

// Functions allowed by the parsing engine
$config['parser_allowed_functions'] = array(
	'strip_tags', 'date', 
	'detect_lang','lang',
	'js', 'css', 'swf', 'img_path', 'css_path', 'js_path', 'swf_path', 'pdf_path', 'media_path', 'cache_path', 'captcha_path', 'assets_path', 'docs_path', // assets specific
	'fuel_block', 'fuel_model', 'fuel_nav', 'fuel_edit', 'fuel_set_var', 'fuel_var', 'fuel_var_append', 'fuel_form', 'fuel_page', // FUEL specific
	'quote', 'safe_mailto', // HTML/URL specific
	'session_flashdata', 'session_userdata', // Session specific
	'prep_url', 'site_url', 'show_404', 'redirect', 'uri_segment', 'auto_typography', 'current_url' // CI specific
);

// Object references passed to the parsing engine
$config['parser_refs'] = array('config', 'load', 'session', 'uri', 'input', 'user_agent');

/*
|--------------------------------------------------------------------------
| Generate settings
|--------------------------------------------------------------------------
*/

// The files/folders to generate with the CLI generate command
$config['generate'] = array(
	'search'   => array('app', 'fuel'),
	'advanced' => array(
		'assets/css/{module}.css',
		'assets/images/ico_cog.png',
		'assets/js/{ModuleName}Controller.js',
		'assets/cache/',
		'config/{module}.php',
		'config/{module}_constants.php',
		'config/{module}_routes.php',
		'controllers/{Module}_module.php',
		'helpers/{module}_helper.php',
		'install/install.php',
		'libraries/Fuel_{module}.php',
		'language/english/{module}_lang.php',
		'models/',
		'tests/sql/',
		'views/_admin/{module}.php',
		'views/_blocks/',
		'views/_docs/index.php',
		'views/_layouts/',
	),
	'simple' => 'MY_fuel_modules.php',
	'model'  => array(
		'{Model_name}_model.php',
		'sql/{table}.sql',
	)
);

@include(APPPATH.'config/MY_fuel.php');


/* End of file fuel.php */
/* Location: ./modules/fuel/config/fuel.php */
