<?php 

function scrape_html($url, $post = array(), $opts = array())
{
	$CI =& get_instance();
	$CI->load->library('curl');
	$CI->curl->initialize();
	if (!empty($post))
	{
		$opts[CURLOPT_POST] = TRUE;
		$opts[CURLOPT_POSTFIELDS] = $post;
		$opts[CURLOPT_HTTPGET] = FALSE;
	}
	if (is_array($url))
	{
		foreach($url as $u)
		{
			$CI->curl->add_session($u, $opts);
		}
	}
	else
	{
		$CI->curl->add_session($url, $opts);
	}
	return $CI->curl->exec();
}

function scrape_regex($url, $regex)
{
	$html = scrape_html();
	if ($regex == 'links')
	{
		$regex = '/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is';
	}
//	preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $html, $matches);
}

function scrape_dom($url, $xpath_query = NULL)
{
	if (is_http_path($url))
	{
		$url = site_url($url);
	}
	
	// turn off the warnings for bad html
	$old_setting = libxml_use_internal_errors(TRUE); 
	libxml_clear_errors(); 
	$dom = new DOMDocument(); 
	
	if (!@$dom->loadHTMLFile($url))
	{
		return FALSE;
	}
	
	if ($xpath_query)
	{
		$xpath = new DOMXPath($dom); 
		$results = $xpath->query($xpath_query);
	}

	// change errors back to original settings
	libxml_clear_errors(); 
	libxml_use_internal_errors($old_setting); 
	
	if (isset($results))
	{
		return $results;
	}
	return $dom;
}

function is_valid_page($url)
{
	$CI =& get_instance();
	$CI->load->library('curl');
	return $CI->curl->is_valid($url);
}