<?php
/*
|---------------------------------------------------------------------------------------------------
| IMPORTANT: for a complete list of fuel configurations, go to the modules/fuel/config/fuel.php file
|---------------------------------------------------------------------------------------------------
*/

// path to the fuel admin from the web base directory... MUST HAVE TRAILING SLASH!
$config['fuel_path'] = 'fuel/';

// the name to be displayed on the top left of the admin
$config['site_name'] = 'My Website';

// whether the admin backend is enabled or not
$config['admin_enabled'] = FALSE;

// options are cms, views, and auto. 
// cms pulls views and variables from the database,
// views mode pulls views from the views folder and variables from the _variables folder,
// and the auto option will first check the database for a page and if it doesn't exist or is 
// not published, it will then check for the corresponding view file.
$config['fuel_mode'] = 'views';

// specifies which modules are allowed to be used in the fuel admin
$config['modules_allowed'] = array(
	'user_guide',
);

// used for system emails
$config['domain'] = '';

// shows an alert in the admin backend if this is the admin password
$config['default_pwd'] = 'admin';

// maximum number of paramters that can be passed to the page. Used to cut down on queries to the db.
// If it is an array, then it will loop through the array using the keys to match against a regular expression:
// $config['max_page_params'] = array('about/news/' => 1);
$config['max_page_params'] = 0;

// will auto search view files. 
// If the URI is about/history and the about/history view 
// does not exist but about does, it will render the about page
$config['auto_search_views'] = FALSE;

// max upload files size for assets
$config['assets_upload_max_size']	= 5000;

// max width for asset images beign uploaded
$config['assets_upload_max_width']  = 1024;

// max height for asset images beign uploaded
$config['assets_upload_max_height']  = 768;


// text editor settings  (options are markitup or ckeditor)
// markitup: allows you to visualize the code in its raw format - not wysiwyg (http://markitup.jaysalvat.com/)
// ckeditor: suitable for clients; shows what the output will look like in the page (http://ckeditor.com/)
$config['text_editor'] = 'markitup';


// The parsing engine to use for FUEL. Options are dwoo, ci and now 'twig'!
$config['parser_engine'] = 'dwoo';

// The directory to put the parsed compiled files
$config['parser_compile_dir'] = APPPATH.'cache/dwoo/compiled/';

// The delimiters used by the parsing engine
$config['parser_delimiters'] = array(
				'tag_comment'   => array('{#', '#}'), // Twig only
				'tag_block'     => array('{%', '%}'), // Twig only
				'tag_variable'  => array('{', '}'), // Used by Twig, Dwoo and CI. Default for twig is '{{', '}}' and Dwoo is '{', '}'
				'interpolation' => array('#{', '}'), // Twig only
			);


/* Uncomment if you want to control FUEL settings in the CMS. Below are a couple examples of ones you can configure
$config['settings'] = array();
$config['settings']['site_name'] = array();
if (!empty($config['modules_allowed']))
{
	$config['settings']['modules_allowed'] = array('type' => 'multi', 'options' => array_combine($config['modules_allowed'], $config['modules_allowed']));
}
*/



/* End of file MY_fuel.php */
/* Location: ./application/config/MY_fuel.php */