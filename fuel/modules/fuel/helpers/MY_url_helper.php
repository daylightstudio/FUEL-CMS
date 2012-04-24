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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
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
 * @link		http://www.getfuelcms.com/user_guide/helpers/url_helper
 */

// --------------------------------------------------------------------

/**
 * Site URL
 * Added simple return if the url begins with http
 *
 * @access	public
 * @param	string	the URI string
 * @return	string
 */
function site_url($uri = '')
{
	if (is_http_path($uri)) return $uri;
	if ($uri == '#' OR (strncmp('mailto', $uri, 6) === 0) OR (strncmp('javascript:', $uri, 11) === 0))
	{
		return $uri;
	}
	else
	{
		$CI =& get_instance();
		return $CI->config->site_url($uri);
	}
}

// --------------------------------------------------------------------

/**
 * Creates https URLs or removes the https from the site_url
 *
 * @access	public
 * @param	string	the URI string
 * @param	boolean	changes the https to http
 * @return	string
 */
function https_site_url($uri = '', $remove_https = FALSE)
{
	$CI =& get_instance();
	if (is_array($uri))
	{
		$uri = implode('/', $uri);
	}
	$base_url = $CI->config->slash_item('base_url');
	if ($remove_https)
	{
		$base_url = str_replace('https://', 'http://', $base_url);
	}
	else
	{
		$base_url = str_replace('http://', 'https://', $base_url);
	}

	if ($uri == '')
	{
		return $base_url.$CI->config->item('index_page');
	}
	else
	{
		$suffix = ($CI->config->item('url_suffix') == FALSE) ? '' : $CI->config->item('url_suffix');
		return $base_url.$CI->config->slash_item('index_page').preg_replace("|^/*(.+?)/*$|", "\\1", $uri).$suffix;
	}
}

// --------------------------------------------------------------------

/**
 * Returns the uri path normalized
 *
 * @access	public
 * @param	boolean	use the rerouted URI string?
 * @param	boolean	the start index to build the uri path
 * @return	string
 */
function uri_path($rerouted = TRUE, $start_index = 0)
{
	$CI =& get_instance();
	$segments = ($rerouted) ? $CI->uri->rsegment_array() : $CI->uri->segment_array();
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
 * @return	string
 */
function uri_segment($n, $default = NULL, $rerouted = TRUE)
{
	$CI =& get_instance();

	if ($rerouted)
	{
		return $CI->uri->segment($n, $default);
	}
	else
	{
		return $CI->uri->rsegment($n, $default);

	}
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
 * @return	boolean
 */
function link_target($link)
{
	$url_parts = parse_url($link);
	
	$test_domain = $_SERVER['SERVER_NAME'];
	$domain = '';
	if (isset($url_parts['host']))
	{
		$host_parts = explode('.', $url_parts['host']);
		$index = count($host_parts) - 1;
		
		if (isset($host_parts[$index]))
		{
			$domain = $host_parts[$index - 1];
			$domain .='.';
			$domain .= $host_parts[$index];
		}
	}
	
	// check if an http path and that it is from a different domain
	if (is_http_path($link) AND $test_domain != $domain)
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

/* End of file MY_url_helper.php */
/* Location: ./modules/fuel/helpers/MY_url_helper.php */