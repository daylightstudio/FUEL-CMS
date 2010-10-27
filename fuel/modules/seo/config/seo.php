<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/seo'] = 'Page Analysis';
$config['nav']['tools']['tools/seo/google_keywords'] = 'Google Keywords';


/*
|--------------------------------------------------------------------------
| TOOL SETTING: Google keyword search
|--------------------------------------------------------------------------
*/

$config['seo'] = array();

// the default domain to search for when using keywords
$config['seo']['keyword_search_default_domain'] = '';

// the number of results google will return
$config['seo']['keyword_google_num_results'] = 100;

// the default keywords to search for
$config['seo']['keyword_google_default_keywords'] = '';

// the centerpoint of the search used by google... NOT CURRENTLY USED
$config['seo']['keyword_google_search_centerpoint'] = 'Portland, OR';

