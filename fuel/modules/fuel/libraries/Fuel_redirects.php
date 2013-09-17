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
 * FUEL redirects object
 *
 * Looks at the redirects configuration for a possible match to do a HTTP redirect.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_redirects
 */

// --------------------------------------------------------------------

class Fuel_redirects extends Fuel_base_library {
	
	public $http_code = 301; // The HTTP response code to return... 301 = permanent redirect
	public $case_sensitive = TRUE; // Determines whether the pattern matching for the redirects is case sensitive
	public $ssl = array(); // The paths to force SSL with
	public $aggressive_redirects = array(); // The pages to redirect to regardless if it's found by FUEL. WARNING: Run on every request.
	public $passive_redirects = array(); // The pages to redirect to only AFTER no page is found by FUEL
	public $max_redirects = 2; // Sets the number of times the page can redirect before giving nup and displaying a 404

	protected $has_session = FALSE; // used to determine if there currently is a native session being used

	const REDIRECT_COUNT = '__FUEL_REDIRECT_COUNT__';
	const REDIRECT_ORIG = '__FUEL_REDIRECT_ORIG__';

	/**
	 * Constructor
	 *
	 */
	public function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 *
	 * @access	public
	 * @param	array	Array of initalization parameters  (optional)
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds to the redirects list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @param	string	The page to redirect to or the type of redirect if the first parameter is an array (optional)
	 * @param	boolean	Determines whether it is a passive redirect or not. Default is TRUE(optional)
	 * @return	array	
	 */	
	public function add($uri, $redirect = '', $passive = TRUE)
	{
		if (is_array($uri))
		{
			if (!$redirect)
			{
				$this->aggressive_redirects = array_merge($this->aggressive_redirects, $uri);
			}
			else
			{
				$this->passive_redirects = array_merge($this->passive_redirects, $uri);	
			}
		}
		else if (is_string($uri))
		{
			if (!$passive)
			{
				$this->aggressive_redirects[$uri] = $redirect;
			}
			else
			{
				$this->passive_redirects[$uri] = $redirect;
			}
			
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Remove from the redirects list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @param	boolean	Determines whether it is a passive redirect or not. Default is TRUE(optional)
	 * @return	array	
	 */	
	public function remove($uri, $passive = TRUE)
	{
		if (!$passive AND isset($this->aggressive_redirects[$uri]))
		{
			unset($this->aggressive_redirects[$uri]);
		}
		else if ($passive AND isset($this->passive_redirects[$uri]))
		{
			unset($this->passive_redirects[$uri]);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds to the ssl redirect list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @param	string	The page to redirect to or the name of the environment if the first parameter is an array(optional)
	 * @param	string	The name of the environment key that the redirect applies to (optional)
	 * @return	array	
	 */	
	public function add_ssl($uri, $redirect = '')
	{
		if (is_array($uri))
		{
			if (!isset($this->ssl[$redirect]))
			{
				$this->ssl[$redirect] = array();
			}
			$this->ssl[$redirect] = array_merge($this->ssl[$redirect], $uri);
		}
		else if (is_string($uri))
		{
			$this->ssl[$environment][$uri] = $redirect;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Remove from the ssl redirect list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @param	string	The name of the environment key that the redirect applies to (optional)
	 * @return	array	
	 */	
	public function remove_ssl($uri, $environment = 'production')
	{
		if (isset($this->ssl[$environment][$uri]))
		{
			unset($this->ssl[$environment][$uri]);
		}
	}

	/**
	 * Returns the $config array
	 *
	 * @access	public
	 * @return	array
	 */
	public function config()
	{
		static $config;
		if (!isset($config))
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

			if (isset($config['max_redirects']))
			{
				$this->max_redirects = $config['max_redirects'];
			}
			// do this if the array doesn't exist and instead they use $config['redirects']
			if (!isset($redirect))
			{
				$redirect = array();
			}
			$config['passive_redirects'] = (isset($config['passive_redirects'])) ? array_merge($redirect, $config['passive_redirects']) : $redirect;

			// used for testing purposes
			if (defined('TESTING') AND !empty($_POST['config']))
			{
				$config = array_merge($config, json_decode($_POST['config'], TRUE));
			}
		}
		return $config;		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the list of redirects
	 *
	 * @access	public
	 * @param	boolean	Determines whether only redirect those pages that are deemed "passive"
	 * @return	array	
	 */	
	public function redirects($only_passive = TRUE)
	{
		$config = $this->config();

		if (isset($config['passive_redirects']) AND $only_passive)
		{
			// merge variable $redirects with passive redirects for compatibility with older version
			$this->add($config['passive_redirects'], TRUE);
			return $this->passive_redirects;
		}
		else if (isset($config['aggressive_redirects']) AND !$only_passive)
		{
			$this->add($config['aggressive_redirects'], FALSE);
			return $this->aggressive_redirects;
		}

	}

	// --------------------------------------------------------------------
	
	/**
	 * Loops through the redirects config to find a possible match to redirect a page to
	 *
	 * @access	public
	 * @param	boolean	Determines whether to show a 404 page if the page doesn't exist
	 * @param	boolean	Determines whether only redirect those pages that are deemed "passive"
	 * @return	void	
	 */	
	public function execute($show_404 = TRUE, $only_passive = TRUE)
	{
		$redirects = $this->redirects($only_passive);
		$uri = $this->_get_uri();
		
		if (!empty($redirects))
		{

			// Is there a literal match?  If so we're done
			if (isset($redirects[$uri]))
			{
				$info = $this->_get_redirect_info($redirects[$uri]);

				$url = $info['url'];
				$http_code = $info['http_code'];
				$max_redirects = $info['max_redirects'];
				$url = site_url($url);

				redirect($url, 'location', $http_code);
			}

			$check_num_redirects = !empty($this->max_redirects);

			// set the original URI value so we can redirect back to it and 404 if the redirects exceed the number of max redirects
			if ($check_num_redirects)
			{
				$this->_session_init($uri);
			}

			foreach ($redirects as $key => $val)
			{
				$info = $this->_get_redirect_info($val);

				$value = $info['url'];
				$case_sensitive = $info['case_sensitive'];
				$http_code = $info['http_code'];
				$max_redirects = $info['max_redirects'];

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
					$url = site_url($value, FALSE, FALSE);

					// call any pre redirect hooks
					$hook_params = array('url' => $key, 'redirect' => $value, 'uri' => $uri);
					$GLOBALS['EXT']->_call_hook('pre_redirect', $hook_params);

					if ($check_num_redirects)
					{

						// grab the current number of redirects performed
						$cnt = $_SESSION[self::REDIRECT_COUNT];
												
						// increment the number of redirects and make sure we don't go into a redirect loop
						$cnt = (int)$cnt + 1;

						// set the session number of redirects to the new number
						$_SESSION[self::REDIRECT_COUNT] = $cnt;

						// if the current count of redirects is greater then the max_directs value, 
						// we will set the URL to the original and display a 404 error
						if ($cnt > $max_redirects)
						{
							$url = $_SESSION[self::REDIRECT_ORIG];

							// cleanup session stuff
							$this->_session_cleanup();
						}
						
					}

					// now redirect
					if (!$check_num_redirects OR ($check_num_redirects AND $cnt <= $max_redirects))
					{
						redirect($url, 'location', $http_code);	
					}
					else
					{
						break;
					}
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

	/**
	 * Loops through the ssl config to find a possible match to redirect to an SSL uri
	 *
	 * @access	public
	 * @return	void
	 */
	public function ssl()
	{
		$config = $this->config();
		$is_https = is_https();

		if (!isset($config['ssl']) OR $is_https)
		{
			return;
		}

		$ssl = $config['ssl'];

		if ( ! empty($ssl[ENVIRONMENT]))
		{
			$ssl_redirects = $ssl[ENVIRONMENT];

			$uri = $this->_get_uri();

			// Is there a literal match?  If so we're done
			if (isset($ssl_redirects[$uri]) AND !$is_https)
			{
				redirect( site_url($uri, TRUE) );
			}

			foreach ($ssl_redirects as $val)
			{
				// Convert wild-cards to RegEx
				$val = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $val));

				// Does the RegEx match?
				$pattern = '#^'.$val.'$#';
				
				if (preg_match($pattern, $uri) AND !$is_https)
				{
					redirect( site_url($uri, TRUE), 'location', 301);
				}
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Will redirect the site based on the host (e.g. mysite.com vs www.mysite.com)
	 *
	 * @access	public
	 * @return	void	
	 */	
	public function enforce_host()
	{
		$config = $this->config();

		if (!isset($config['host']))
		{
			return;
		}

		$host = $config['host'];

		if ( ! empty($host[ENVIRONMENT]))
		{
			if ($_SERVER['HTTP_HOST'] != $host[ENVIRONMENT])
			{
				$url = $host[ENVIRONMENT].$_SERVER['REQUEST_URI'];

				$prefix = (is_https()) ? 'https://' : 'http://';
				$url = $prefix.$url;
				header("Location: ".$url, TRUE, 301);
				exit();
			}
		}
	}
	
	/**
	 * Gets redirect info
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function _get_redirect_info($val)
	{
		$return = array(
				'url' => NULL,
				'case_sensitive' => $this->case_sensitive,
				'http_code' => $this->http_code,
				'max_redirects' => $this->max_redirects
			);
		if (is_array($val))
		{
			// set defaults based on array index for default
			$return['url'] = current($val);
			$next_val = next($val);
			if (isset($next_val))
			{
				$return['case_sensitive'] = $next_val;
			}
			$next_val = next($val);
			if ($next_val)
			{
				$return['http_code'] = $next_val;	
			}
			
			$next_val = next($val);
			if ($next_val)
			{
				$return['max_redirects'] = $next_val;	
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

			if (isset($val['max_redirects']))
			{
				$return['max_redirects'] = $val['max_redirects'];
			}
		}
		else
		{
			$return['url'] = $val;
		}
		return $return;
	}

	/**
	 * Tests redirects and returns an array of valid URL and errors
	 *
	 * @access	public
	 * @param	mixed an array or string of URLs to test. If none are provided, it will pull from the config
	 * @return	array
	 */
	public function test($urls = array())
	{
		$this->CI->load->library('curl');

		if (empty($redirects))
		{
			$config = $this->config();
			$urls = array_keys(array_merge($config['passive_redirects'], $config['aggressive_redirects']));
		}
		if (is_string($urls))
		{
			$urls = preg_split('#\s*(,|\s)\s*#', $urls);
		}

		foreach($urls as $url)
		{
			$url = site_url($url);
			$this->CI->curl->add_session($url, array(CURLOPT_FOLLOWLOCATION => TRUE, CURLOPT_MAXREDIRS => $this->max_redirects));
		}
		$this->CI->curl->exec_multi();
		$infos = $this->CI->curl->info(NULL, TRUE);

		$return = array(
			'valid' => array(),
			'errors' => array(),
			);
		foreach($infos as $key => $info)
		{
			//echo $info['http_code'] .'<br />';
			if ((int) $info['http_code'] >= 400 OR $this->CI->curl->error($key))
			{
				$return['errors'][] = $urls[$key];
			}
			else
			{
				$return['valid'][] = $urls[$key];
			}
		}
		return $return;
	}

	/**
	 * Gets the current uri path with query string parameters
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_uri()
	{
		$uri = implode('/', $this->CI->uri->segments);
		$query_string = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
		
		if (!empty($query_string)) 
		{
			$uri = $uri.'?'.$query_string;
		}
		return $uri;
	}

	/**
	 * Initialize $_SESSION variables for tracking the number of redirects
	 *
	 * @access	protected
	 * @param   string the original URL to redirect to
	 * @return	void
	 */
	protected function _session_init($uri)
	{
		$this->has_session = session_id();

		if (!$this->has_session)
		{
			// set the session cookie for 1 second which should be more then enough time to do the redirects
			session_set_cookie_params(1);

			// set a unique session ID
			// set to time() because we expect the session to be done in a second
			session_id(time());

			// use native sessions because it seems to work better and less chance for session conflict
			session_start();
		}

		// set the original URI value so we can redirect back to it and 404 if the redirects exceed the number of max redirects
		if (!isset($_SESSION[self::REDIRECT_ORIG]))
		{
			$_SESSION[self::REDIRECT_ORIG] = $uri;
		}

		if (!isset($_SESSION[self::REDIRECT_COUNT]))
		{
			$_SESSION[self::REDIRECT_COUNT] = 0;
		}

	}

	/**
	 * Cleans up $_SESSION variables and the PHP session itself which is used to prevent infinite redirects
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _session_cleanup()
	{
		// cleanup
		unset($_SESSION[self::REDIRECT_COUNT]);
		unset($_SESSION[self::REDIRECT_ORIG]);

		if (empty($_SESSION) AND !$this->has_session)
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
			session_destroy();
		}
	}

}

/* End of file Fuel_redirects.php */
/* Location: ./modules/fuel/libraries/Fuel_redirects.php */