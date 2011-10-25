<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/google_keywords'] = lang('module_google_keywords');


/*
|--------------------------------------------------------------------------
| TOOL SETTING: Google keyword search
|--------------------------------------------------------------------------
*/

$config['google_keywords'] = array();

// the default domain to search for when using keywords
$config['google_keywords']['default_domain'] = '';

// the number of results google will return
$config['google_keywords']['num_results'] = 100;

// the default keywords to search for
$config['google_keywords']['default_keywords'] = '';

// additional parameters to include in the search string
$config['google_keywords']['additional_params'] = array();

