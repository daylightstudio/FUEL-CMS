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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
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

if (!function_exists('url_to'))
{
	/**
	 * This is just an alias to the normal CI site_url function but was added mainly to prevent conflicts with other systems like Wordpress (yes... sometimes the two need to be integrated)
	 * Added simple return if the url begins with http
	 *
	 * @access	public
	 * @param	string	the URI string
	 * @param	boolean	sets or removes "https" from the URL. Must be set to TRUE or FALSE for it to explicitly work
	 * @param	boolean	sets the language parameter on the URL based on the "language_mode" setting in the FUEL configuration
	 * @return	string
	 */
	function url_to($uri = '', $https = NULL, $language = NULL)
	{
		if (is_http_path($uri)) return $uri;
		if ($uri == '#' OR (strncmp('mailto:', $uri, 7) === 0) OR (strncmp('javascript:', $uri, 11) === 0) OR (strncmp('tel:', $uri, 4) === 0))
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
}

// --------------------------------------------------------------------

if (!function_exists('site_url'))
{
	/**
	 * Site URL which is an alias to the url_to function
	 *
	 * @access	public
	 * @param	string	the URI string
	 * @param	boolean	sets or removes "https" from the URL. Must be set to TRUE or FALSE for it to explicitly work
	 * @param	boolean	sets the language parameter on the URL based on the "language_mode" setting in the FUEL configuration
	 * @return	string
	 */
	function site_url($uri = '', $https = NULL, $language = NULL)
	{
		return url_to($uri, $https, $language);
	}
}

// --------------------------------------------------------------------

if (!function_exists('current_url'))
{
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
}

// --------------------------------------------------------------------

if (!function_exists('uri_path'))
{
	/**
	 * Returns the uri path normalized
	 *
	 * @access	public
	 * @param	boolean	use the rerouted URI string?
	 * @param	int		the start index to build the uri path
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
}

// --------------------------------------------------------------------

if (!function_exists('uri_segment'))
{
	/**
	 * Returns the uri segment
	 *
	 * @access	public
	 * @param	int		the segment number
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
}

// --------------------------------------------------------------------

if (!function_exists('is_http_path'))
{
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
}

// --------------------------------------------------------------------

if (!function_exists('is_home'))
{
	/**
	 * Determines if the page is the homepage or not
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	function is_home($uri_path = NULL)
	{
		if (is_null($uri_path))
		{
			$uri_path = uri_path(FALSE);	
		}
		
		$CI =& get_instance();
		$segs = explode('/', $CI->router->routes['404_override']);
		return ($uri_path == 'home' OR $uri_path == '' OR $uri_path == end($segs));
	}
}

// --------------------------------------------------------------------

if (!function_exists('is_404'))
{
	/**
	 * Determines if the page is 404
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_404()
	{
		return (http_response_code() == 404);
	}
}

// --------------------------------------------------------------------

if (!function_exists('last_url'))
{
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
}

// --------------------------------------------------------------------

if (!function_exists('link_target'))
{
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

		// get the extension to check
		if (is_string($exts))
		{
			$exts = array($exts);
		}
		$link_parts = explode('.', $link);
		$ext = end($link_parts);

		if (isset($url_parts['host']))
		{
			if ($url_parts['host'] == $test_domain AND !in_array($ext, $exts))
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


		// check if an http path and that it is from a different domain
		if (is_http_path($link) AND $test_domain != $domain OR (!empty($exts) AND in_array($ext, $exts)))
		{
			return ' target="_blank"';
		}
		return '';
	}
}

// --------------------------------------------------------------------

if (!function_exists('redirect_404'))
{
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
}

// ------------------------------------------------------------------------

if (!function_exists('redirect'))
{
	/**
	 * Header Redirect (Overwritten to account for adding HTTPS and adding language path in site_url function)
	 *
	 * Header redirect in two flavors
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 *
	 * @access	public
	 * @param	string	the URL
	 * @param	string	the method: location or redirect
	 * @param	int		the http response code
	 * @param	string	whether to force or not https
	 * @param	boolean	whether add the language to the URI
	 * @return	string
	 */
	function redirect($uri = '', $method = 'location', $http_response_code = 302, $https = NULL,  $language = FALSE)
	{
		if ( ! preg_match('#^https?://#i', $uri))
		{
			if (is_null($https))
			{
				$https = is_https();
			} 
			$uri = url_to($uri, $https, $language);
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
}
// ------------------------------------------------------------------------

// used function exists to future proof it https://github.com/IT-Can/CodeIgniter/commit/98bc5d985b7119ff71b9f50a1b226559f647797a
if ( ! function_exists('is_https'))
{
	/**
	 * Returns whether the current page is using SSL (https)
	 *
	 * @access	public
	 * @param	string	the URL
	 * @param	string	the method: location or redirect
	 * @return	string
	 */
	function is_https()
	{
		return ((!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) !== 'off'));
	}
}

// ------------------------------------------------------------------------

if (!function_exists('query_str'))
{
	/**
	 * Returns a query string formatted
	 *
	 * @access	public
	 * @param	array	an array of query string parameters to exclude
	 * @param	boolean	determines whether to include posted variables in the query string
	 * @param	boolean	determines whether to include the question mark
	 * @return	string
	 */
	function query_str($exclude = array(), $include_post = FALSE, $include_q = TRUE)
	{
		$CI =& get_instance();
		$query_str = '';
		if ($include_post)
		{
			$get_array = $CI->input->get_post(NULL, FALSE);
		}
		else
		{
			$get_array = $CI->input->get(NULL, FALSE);
		}
		
		if (!empty($get_array))
		{
			if (!empty($exclude))
			{
				foreach($exclude as $e)
				{
					if (isset($get_array[$e]))
					{
						unset($get_array[$e]);
					}
				}
			}
			$query_str = http_build_query($get_array);
			if (!empty($query_str) AND $include_q)
			{
				$query_str = '?'.$query_str;
			}
		}
		return $query_str;
	}
}

/* End of file MY_url_helper.php */
/* Location: ./modules/fuel/helpers/MY_url_helper.php */
