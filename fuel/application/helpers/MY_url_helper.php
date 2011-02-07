<?php
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL URL Helper
 *
 * Extends CI's URL helper
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
	$CI =& get_instance();
	return $CI->config->site_url($uri);
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
 * @param	boolean	whether to use the rerouted uri
 * @return	string
 */
function uri_segment($n, $rerouted = TRUE)
{
	$CI =& get_instance();

	if ($rerouted)
	{
		return $CI->uri->segment($n);
	}
	else
	{
		return $CI->uri->rsegment($n);

	}
}

// --------------------------------------------------------------------

/**
 * Helper function to determine if it is a local path
 *
 * @access	public
 * @param	boolean	use the rerouted URI string?
 * @param	boolean	the start index to build the uri path
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

/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */