<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/seo'] = lang('module_page_analysis');
$config['nav']['tools']['tools/seo/google_keywords'] = lang('module_google_keywords');


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

// additional parameters to include in the search string
$config['seo']['keyword_google_additional_params'] = array();

