<?php
/*
|--------------------------------------------------------------------------
| Redirects
|--------------------------------------------------------------------------
|
| Add redirects here instead of your .htaccess file with the key being
| Do NOT include beginning or trailing slashes for BOTH keys and values
|
|	$config['passive_redirects'] = array(
|							'mypage' => 'http://www.mysite.com/my_newpage'),
|							'mypage2' => array('url' => 'http://www.mysite.com/my_newpage', 'case_sensitive' => FALSE, 'http_code' => 302),
|							);
|
| You may also use the following setting to force SSL on specific URI paths
| The keys should match the value in your environments config
|
|	$config['ssl'] = array(
|					'production' => array('mysecurepage$|mysecurepage/:any'),
|					);
*/

// Default HTTP response code to return... 301 = permanent redirect
$config['http_code'] = 301; 

// Determines whether the pattern matching for the redirects is case sensitive
$config['case_sensitive'] = TRUE; 

// The paths to force SSL with the key being the environment it belongs to
$config['ssl'] = array('development' => array()); 

// The host name to enforce (e.g. mysite.com vs www.mysite.com) with the key being the environment it belongs to
$config['host'] = array('production' => ''); 

// The pages to redirect to regardless if it's found by FUEL. 
// WARNING: Run on every request.
$config['aggressive_redirects'] =  array();

// The pages to redirect to only AFTER no page is found by FUEL
$config['passive_redirects'] = array();

// The max number of times to redirect before showing a 404
$config['max_redirects'] = 2;

// DEPRECATED BUT STILL WILL WORK. THIS MAPS TO $config['passive_redirects']
//$redirect = array(); 


/* End of file redirects.php */
/* Location: ./application/config/redirects.php */