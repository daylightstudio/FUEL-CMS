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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL URL Helper
 *
 * Contains functions to be used with urls. Extends CI's url helper and is autoloaded.
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_url_helper
 */

// --------------------------------------------------------------------

/**
 * Site URL
 * Added simple return if the url begins with http
 *
 * @access	public
 * @param	string	the URI string
 * @param	boolean	sets or removes "https" from the URL. Must be set to TRUE or FALSE for it to explicitly work
 * @param	boolean	sets the language parameter on the URL based on the "language_mode" setting in the FUEL configuration
 * @return	string
 */
function site_url($uri = '', $https = NULL, $language = NULL)
{
	if (is_http_path($uri)) return $uri;
	if ($uri == '#' OR (strncmp('mailto', $uri, 6) === 0) OR (strncmp('javascript:', $uri, 11) === 0))
	{
		return $uri;
	}
	else
	{

		$CI =& get_instance();
		
		// append any language stuff to the URL if configured
		if (isset($CI->fuel))
		{
			$uri  = $CI->fuel->language->uri($uri, $language);	
		}

		$url = $CI->config->site_url($uri);

		if ($https === TRUE)
		{
			$url = preg_replace('#^http:(.+)#', 'https:$1', $url);
		}
		else if ($https === FALSE)
		{
			$url = preg_replace('#^https:(.+)#', 'http:$1', $url);
		}
		return $url;
	}
}

// --------------------------------------------------------------------

/**
 * Current URL
 * Added show_query_str parameter
 *
 * @access	public
 * @param	boolean	determines whether to include query string parameters
 * @param	boolean	determines whether to change the language value
 * @return	string
 */
function current_url($show_query_str = FALSE, $lang = NULL)
{
    $CI =& get_instance();

    $url = site_url($CI->uri->uri_string(), NULL, $lang);
    if ($show_query_str AND !empty($_SERVER['QUERY_STRING']))
    {
    	$url = $url.'?'.$_SERVER['QUERY_STRING'];
    }
    return $url;
}

// --------------------------------------------------------------------

/**
 * Returns the uri path normalized
 *
 * @access	public
 * @param	boolean	use the rerouted URI string?
 * @param	boolean	the start index to build the uri path
 * @param	boolean	determines whether to strip any language segments
 * @return	string
 */
function uri_path($rerouted = TRUE, $start_index = 0, $strip_lang = TRUE)
{
	$CI =& get_instance();

	if ($strip_lang AND isset($CI->fuel) AND $CI->fuel->language->has_multiple())
	{
		$segments = $CI->fuel->language->cleaned_uri_segments(NULL, $rerouted);
	}
	else
	{
		$segments = ($rerouted) ? $CI->uri->rsegment_array() : $CI->uri->segment_array();
	}
	if (!empty($segments) && $segments[count($segments)] == 'index')
	{
		array_pop($segments);
	}
	if (!empty($start_index))
	{
		$segments = array_slice($segments, $start_index);
	}
	$location = implode('/', $segments);


	return $location;
}

// --------------------------------------------------------------------

/**
 * Returns the uri segment
 *
 * @access	public
 * @param	int	the segment number
 * @param	string	the default value if the segment doesn't exist
 * @param	boolean	whether to use the rerouted uri
 * @param	boolean	determines whether to strip any language segments
 * @return	string
 */
function uri_segment($n, $default = FALSE, $rerouted = TRUE, $strip_lang = TRUE)
{
	$CI =& get_instance();
	if ($strip_lang AND isset($CI->fuel) AND $CI->fuel->language->has_multiple())
	{
		$segments = $CI->fuel->language->cleaned_uri_segments(NULL, !$rerouted);
		$seg =  (isset($segments[$n])) ? $segments[$n] : $default;
	}
	else
	{
		if ($rerouted)
		{
			$seg = $CI->uri->segment($n, $default);
		}
		else
		{
			$seg = $CI->uri->rsegment($n, $default);
		}
	}
	return $seg;
}

// --------------------------------------------------------------------

/**
 * Helper function to determine if it is a local path
 *
 * @access	public
 * @param	string	URL
 * @return	string
 */
function is_http_path($path)
{
	return (preg_match('!^\w+://! i', $path));
}

// --------------------------------------------------------------------

/**
 * Determines if the page is the homepage or not
 *
 * @access	public
 * @return	boolean
 */
function is_home()
{
	$uri_path = uri_path(FALSE);
	return ($uri_path == 'home' OR $uri_path == '');
}

// --------------------------------------------------------------------

/**
 * Returns the last page you visited
 *
 * @access	public
 * @param	string	Default value if no last page exists
 * @param	boolean	Whether to return only the URI part of the the URL
 * @return	boolean
 */
function last_url($default = FALSE, $only_uri = FALSE)
{
	$back_url = (isset($_SERVER['HTTP_REFERER']) AND $_SERVER['HTTP_REFERER'] != current_url()) ? $_SERVER['HTTP_REFERER'] : $default;
	
	// check to make sure the last URL was from the same site
	if (!preg_match('#^'.site_url().'#', $back_url))
	{
		$back_url = $default;
	}
	
	if ($back_url)
	{
		$back_url = site_url($back_url);

		if ($only_uri)
		{
			$back_url = str_replace(site_url(), '', $back_url);
		}
	}
	
	return $back_url;
}

// --------------------------------------------------------------------

/**
 * Will return a target="_blank" if the link is not from the same domain.
 *
 * @access	public
 * @param	string	URL
 * @param	array	An array of extensions to check to force it to target="_blank"
 * @return	boolean
 */
function link_target($link, $exts = array())
{
	$url_parts = parse_url($link);
	
	$test_domain = $_SERVER['SERVER_NAME'];
	$domain = '';
	if (isset($url_parts['host']))
	{
		
		if ($url_parts['host'] == $test_domain)
		{
			return '';
		}

		$host_parts = explode('.', $url_parts['host']);

		$index = count($host_parts) - 1;
		if (isset($host_parts[$index - 1]))
		{
			$domain = $host_parts[$index - 1];
			$domain .='.';
			$domain .= $host_parts[$index];
		} 
		else if (count($host_parts) == 1)
		{
			$domain = $host_parts[0];
		}
	}

	// get the extension to check
	if (is_string($exts))
	{
		$exts = array($exts);
	}
	$ext = end(explode('.', $link));
	
	// check if an http path and that it is from a different domain
	if (is_http_path($link) AND $test_domain != $domain OR (!empty($exts) AND in_array($ext, $exts)))
	{
		return ' target="_blank"';
	}
	return '';
}

// --------------------------------------------------------------------

/**
 * Checks the redirects before showing a 404
 *
 * @access	public
 * @param	boolean	Whether to redirect or not
 * @return	void
 */
function redirect_404($redirect = TRUE)
{
	$CI =& get_instance();
	$CI->fuel->redirects->execute($redirect);
}

// ------------------------------------------------------------------------

/**
 * Header Redirect (Overwritten to account for adding language path in site_url function)
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @param	string	the http response code
 * @param	string	wether to force or not https
 * @return	string
 */
function redirect($uri = '', $method = 'location', $http_response_code = 302, $use_https = NULL)
{
	if ( ! preg_match('#^https?://#i', $uri))
	{
		if (is_null($use_https))
		{
			$use_https = is_https();
		} 
		$uri = site_url($uri, $use_https, FALSE);
	}

	switch($method)
	{
		case 'refresh'	: header("Refresh:0;url=".$uri);
			break;
		default			: header("Location: ".$uri, TRUE, $http_response_code);
			break;
	}
	exit;
}

// ------------------------------------------------------------------------

/**
 * Returns whether the current page is using SSL (https)
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @return	string
 */
// used function exists to future proof it https://github.com/IT-Can/CodeIgniter/commit/98bc5d985b7119ff71b9f50a1b226559f647797a
if ( ! function_exists('is_https'))
{
	function is_https()
	{
		return ((!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) !== 'off'));
	}
}

/* End of file MY_url_helper.php */
/* Location: ./modules/fuel/helpers/MY_url_helper.php */
