<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2010, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Scraper Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/scraper_helper
 */


// --------------------------------------------------------------------

/**
 * Uses <a href="[user_guide_url]libraries/curl">CURL</a> to scrape the contents of a URL
 *
 * @access	public
 * @param	string	URL of page to scrape contents
 * @param	array	POST parameters to pass along in the request
 * @param	array	An additional set of CURL options
 * @return	string
 */	
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

// --------------------------------------------------------------------

/**
 * Returns a DOM object of a page or a result object if an XPath query was passed
 *
 * @access	public
 * @param	string	URL of page to scrape contents
 * @param	string	an XPath query to pass
 * @return	object
 */	
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

// --------------------------------------------------------------------

/**
 * Uses <a href="[user_guide_url]libraries/curl">CURL</a> to determine if a page exists or not
 *
 * @access	public
 * @param	string	URL of page to check
 * @return	boolean
 */	
function is_valid_page($url)
{
	$CI =& get_instance();
	$CI->load->library('curl');
	return $CI->curl->is_valid($url);
}

/* End of file scraper_helper.php */
/* Location: ./modules/fuel/helpers/scraper_helper.php */