<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/validate'] = lang('module_validate');


/*
|--------------------------------------------------------------------------
| TOOL SETTING: Validation settings
|--------------------------------------------------------------------------
*/
$config['validate'] = array();

$config['validate']['toolbar'] = array(
									'toolbar' => 'Validate HTML',
									'toolbar/links' => 'Validate Links',
									'toolbar/weight' => 'Page Weight',
							);

// validator url
$config['validate']['validator_url'] = 'http://validator.w3.org/check';

// a list of valid internal domains. Can contain regular expression or :any, :num wildcards
$config['validate']['valid_internal_server_names'] = array('localhost', '192\.168\.:any');

// sets the warning limit of the filesize of a resource in KB
$config['validate']['size_report_warn_limit'] = 100;

// default value for page input field... must be delimited by \n
$config['validate']['default_page_input'] = '';

// number of seconds before the curl request times out
$config['validate']['curl_timeout'] = 10;
