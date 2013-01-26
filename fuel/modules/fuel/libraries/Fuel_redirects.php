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
 * FUEL redirects object
 *
 * Looks at the redirects configuration for a possible match to do a HTTP redirect.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_redirects
 */

// --------------------------------------------------------------------

class Fuel_redirects extends Fuel_base_library {
	
	public $http_code = 301; // The HTTP response code to return... 301 = permanent redirect
	public $redirects = array(); // The pages to redirect to
	public $case_sensitive = TRUE; // Determines whether the pattern matching for the redirects is case sensitive

	/**
	 * Constructor
	 *
	 */
	function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds to the redirects list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @param	string	The page to redirect to (optional)
	 * @return	array	
	 */	
	function add($uri, $redirect = '')
	{
		if (is_array($uri))
		{
			$this->redirects = array_merge($this->redirects, $uri);
		}
		else if (is_string($uri))
		{
			$this->redirects[$uri] = $redirect;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Remove from the redirects list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @return	array	
	 */	
	function remove($uri)
	{
		if (isset($this->redirects[$uri]))
		{
			unset($this->redirects[$uri]);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of all the redirects
	 *
	 * @access	public
	 * @return	array	
	 */	
	function config()
	{
		include(APPPATH.'config/redirects.php');
		
		if (isset($config['http_code']))
		{
			$this->http_code = $config['http_code'];
		}

		if (isset($config['case_sensitive']))
		{
			$this->case_sensitive = $config['case_sensitive'];
		}

		if (isset($config['redirects']))
		{
			$this->redirects = $config['redirects'];
		}

		// do this if the array doesn't exist and instead they use $config['redirects']
		if (!isset($redirect))
		{
			$redirect = array();
		}

		$redirect = array_merge($redirect, $this->redirects);
		return $redirect; 
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loops through the redirects config to find a possible match to redirect a page to
	 *
	 * @access	public
	 * @param	boolean	Determines whether to show a 404 page if the page doesn't exist
	 * @return	void	
	 */	
	function execute($show_404 = TRUE)
	{
		$redirects = $this->config();

		$uri = implode('/', $this->CI->uri->segments);
		$query_string = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
		
		if (!empty($query_string)) 
		{
			$uri = $uri.'?'.$query_string;
		}
		if (!empty($redirects))
		{

			// Is there a literal match?  If so we're done
			if (isset($redirects[$uri]))
			{
				$info = $this->_get_redirect_info($redirects[$uri]);
				$url = $info['url'];
				$http_code = $info['http_code'];

				$url = site_url($url);
				redirect($url, 'location', $http_code);
			}

			foreach ($redirects as $key => $val)
			{

				$info = $this->_get_redirect_info($val);

				$value = $info['url'];
				$case_sensitive = $info['case_sensitive'];
				$http_code = $info['http_code'];

				$key = trim($key, '/');
				$value = trim($value, '/');
				
				// Convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// Does the RegEx match?
				$pattern = '#^'.$key.'$#';
				if (!$case_sensitive)
				{
					$pattern .= 'i';
				}
				if (preg_match($pattern, $uri))
				{

					// Do we have a back-reference?
					if (strpos($value, '$') !== FALSE AND strpos($key, '(') !== FALSE)
					{
						$value = preg_replace('#^'.$key.'$#', $value, $uri);
					}
					$url = site_url($value);

					// call any pre redirect hooks
					$hook_params = array('url' => $key, 'redirect' => $value, 'uri' => $uri);
					$GLOBALS['EXT']->_call_hook('pre_redirect', $hook_params);

					redirect($url, 'location', $http_code);
				}
			}
		}
		
		if ($show_404 === TRUE)
		{
			// call any pre 404 hooks
			$hook_params = array('uri' => $uri);
			$GLOBALS['EXT']->_call_hook('pre_404', $hook_params);

			// check CMS first if set to AUTO or views
			if ($this->fuel->pages->mode() != 'views')
			{
				$error_404 = $this->fuel->pages->render('404_error', array(), array('render_mode' => 'cms'), TRUE); 
			}

			// then views
			if (empty($error_404) AND file_exists(APPPATH.'views/404_error'.EXT))
			{
				$error_404 = $this->fuel->pages->render('404_error', array(), array('render_mode' => 'views'), TRUE); 
			}

			if (empty($error_404))
			{
				$error_404 = $this->fuel->pages->render('404_error', array(), array('render_mode' => 'views'), TRUE);
			}
			if (!empty($error_404))
			{
				echo $error_404;
				exit();
			}
			else
			{
				show_404();
			}
		}
	}

	protected function _get_redirect_info($val)
	{
		$return = array(
				'url' => NULL,
				'case_sensitive' => $this->case_sensitive,
				'http_code' => $this->http_code,
			);
		if (is_array($val))
		{
			// set defaults based on array index for default
			$return['url'] = current($val);
			$next_val = next($val);
			if ($next_val)
			{
				$return['case_sensitive'] = $next_val;	
			}
			$next_val = next($val);
			if ($next_val)
			{
				$return['http_code'] = $next_val;	
			}
			
			// if keys specified, then you can use those too 
			if (isset($val['url']))
			{
				$return['url'] = $val['url'];
			}

			if (isset($val['case_sensitive']))
			{
				$return['case_sensitive'] = (bool) $val['case_sensitive'];
			}
			
			if (isset($val['http_code']))
			{
				$return['http_code'] = $val['http_code'];
			}
		}
		else
		{
			$return['url'] = $val;
		}
		return $return;
	}

}

/* End of file Fuel_redirects.php */
/* Location: ./modules/fuel/libraries/Fuel_redirects.php */