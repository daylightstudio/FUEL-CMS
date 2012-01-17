<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/search'] = lang('module_search');


/*
|--------------------------------------------------------------------------
| TOOL SETTING: Search
|--------------------------------------------------------------------------
*/
$config['tables']['search'] = 'fuel_search';


$config['search'] = array();

// whether to enable search indexing
$config['search']['indexing_enabled'] = TRUE;

// the user agent used when indexing
$config['user_agent'] = 'FUEL';

// value can be either "like" which will do a %word% query OR "match" which will use the MATCH / AGAINST syntax
// use "like" if you have a small number of records
$config['search']['query_type'] = 'like';

// search page content delimiters. used for scraping page content. Can be an HTML node or xpath syntax (e.g. //div[@id="main"])
$config['search']['delimiters'] = array(
	'<div id="main_inner">', 
);

// the URI locations of pages to exclude from the index. 
// You can also add them to the "robots.txt" file for your site
$config['search']['exclude'] = array();

// can be AUTO, "crawl" or "sitemap"
// crawl, will scan the site for local links to index
// sitemap will use the sitemap if it exists
// AUTO will first check the sitemap (because it's faster), then will default to the crawl
$config['search']['index_method'] = 'AUTO';

// whether to automatically index modules that have a preview_path specified. 
// Default is TRUE and will automatically do it for all modules. If an array
// is specified, then it will only index those in the array
$config['search']['auto_index'] = TRUE;

