<?php 

// relative to the web root. Can also be absolute to a different server for CDN
$config['assets_path'] = 'assets/';

// path structure to use for modules relative to web root
$config['assets_module_path'] = 'fuel/modules/{module}/assets/';

// file path to assets folder
$config['assets_server_path'] = WEB_ROOT.'assets'.DIRECTORY_SEPARATOR;

// leave blank to set the default context to the main asset_path
$config['assets_module'] = '';

// relative to web_root.assets_path
$config['assets_folders'] = array(
	'images' => 'images/',
	'css' => 'css/',
	'js' => 'js/',
	'pdf' => 'pdf/',
	'swf' => 'swf/',
	'media' => 'media/',
	'captchas' => 'captchas/',
	'docs' => 'docs/'
	);

// makes paths to assets absolute
$config['assets_absolute_path'] = FALSE;

// used for caching
$config['assets_last_updated'] = '00/00/0000 00:00:00';

// appends timestamp of last updated after file name
$config['asset_append_cache_timestamp'] = array('js', 'css');

/*
|--------------------------------------------------------------------------
turn on asset optimization which can combine multiple files into one, strips 
whitespace from js and css files, and gzip. Used with the js/css functions

Known issue with jquery library due to regex comments stripping stuff out.
You can still use this, but just turn of optimizing in the js function call that has jquery

YOU MUST use the assets_last_updated to refresh the cache file

options: 
* FALSE - no optimation
* TRUE - will combine files, strip whitespace, and gzip
* "inline" - will render the files inline
* "gzip" - will combine files (if multiple) and gzip without stripping whitespace
* "whitespace" - will combine files (if multiple) and strip out whitespace without gzipping
* "combine" - will combine files (if multiple) but will not strip out whitespace or gzip

There is not an option for not combining files. To do that, you just call the
js/css function without multiple file in the first parameter
*/

$config['assets_output'] = FALSE;

// force assets to recompile on each load
$config['force_assets_recompile'] = FALSE;

// cache folder relative to the web root folder... must be writable directory (default is the application/assets/cache folder)
$config['assets_cache_folder'] = 'cache/';

// time limit on gzip cache file in seconds
$config['assets_gzip_cache_expiration'] = 3600;

/* End of file asset.php */
/* Location: ./application/config/asset.php */