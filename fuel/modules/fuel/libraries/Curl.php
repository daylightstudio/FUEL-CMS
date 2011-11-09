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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Curl library
 * Some code borrowed from:
 * http://semlabs.co.uk/journal/object-oriented-curl-class-with-multi-threading
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/curl
 */


// --------------------------------------------------------------------

class Curl {
	
	public $user_agent = '';
	public $timeout = 10;
	public $connect_timeout = 10;
	public $dns_cache_timeout = 3600;
	public $cookie_file = 'my_cookie.txt';
	
	protected $CI; // reference to the CI super object
	protected $_sessions = array();
	protected $_info;
	protected $_error;
	protected $_output;
	
	/**
	 * Constructor - Sets Fuel_base_library preferences and to any children
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		if (!$this->is_enabled())
		{
			$this->_add_error('The cURL extension does not exist');
		}
		$this->CI = & get_instance();
		$this->CI->load->library('user_agent');
		$this->user_agent = $this->CI->agent->agent_string();
		$this->cookie_file = $this->CI->config->item('cache_path').$this->cookie_file;
		$this->initialize();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params = array())
	{
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */
	function set_params($params)
	{
		if (!is_array($params)) return;

		foreach ($params as $key => $val)
		{
			if (isset($this->$key) AND substr($key, 0, 1) != '_')
			{
				$this->$key = $val;
			}
		}
	}

	
	function add_session($url, $opts = array())
	{
		// normalize the URL
		$url = $this->_normalize_url($url);
		
		$this->_sessions[] = curl_init($url);
		
		$key = count($this->_sessions) - 1;
		
		// set default options
		$this->set_defaults($key);
		
		if (!empty($opts))
		{
			
			// make it easy to pass options
			$o = array();
			foreach($opts as $key => $val)
			{
				if (is_string($key) && !is_numeric($key))
				{
					$c = 'CURLOPT_' . strtoupper($key);
					if (defined($c))
					{
						$constant = constant($c);
						$o[$constant] = $val;
					}
				}
				else
				{
					$o[$key] = $val;
				}
			}
			
			$this->set_options($key, $o);
		}
	}
	
	function set_defaults($key = 0)
	{
		$default_opts = array(
						CURLOPT_HEADER => 0,
						CURLOPT_RETURNTRANSFER => TRUE,
						CURLOPT_TIMEOUT => $this->timeout,
						CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
						CURLOPT_DNS_CACHE_TIMEOUT => $this->dns_cache_timeout,
						CURLOPT_BINARYTRANSFER => TRUE,
						CURLOPT_USERAGENT => $this->user_agent,
					);
		$this->set_options($default_opts, $key);
	}
	
	function set_options($opts = array(), $key = 0)
	{
		curl_setopt_array($this->_sessions[$key], $opts);
	}
	
	function exec($key = 0, $clear = TRUE)
	{
		if (empty($this->_sessions))
		{
			return FALSE;
		}
		
		if ($this->is_multi())
		{
			if ($key === FALSE)
			{
				$this->exec_multi($clear);
			}
			else
			{
				$this->exec_single($key, $clear);
			}
		}
		else
		{
			$this->exec_single($clear);
		}

		
		// set by exec_single/exec_multi
		return $this->_output;
	}
	
	function exec_single($key = 0, $clear = TRUE)
	{
		$this->_output = curl_exec($this->_sessions[$key]);
		$this->_error[$key]  = curl_error($this->_sessions[$key]);
		$this->_info[$key] = curl_getinfo($this->_sessions[$key]);

		// clear and close all sessions
		if ($clear)
		{
			$this->clear();
		}
		return $this->_output;
	}

	public function exec_multi($clear = TRUE)
	{
		$mh = curl_multi_init();
		
		// add all sessions to multi handle
		foreach ($this->_sessions as $key => $session)
		{
			curl_multi_add_handle($mh, $this->_sessions[$key]);
		}

		
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			
		
		
		while ($active AND $mrc == CURLM_OK)
		{
			if (curl_multi_select($mh) != -1)
			{
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		if ($mrc != CURLM_OK)
		{
			return FALSE;
		}
			
		
		// get content foreach session, retry if applied
		foreach ($this->_sessions as $key => $session)
		{
			$code = $this->info($key, 'http_code');
			if ($code[0] > 0 && $code[0] < 400)
			{
				$this->_output[$key] = curl_multi_getcontent($this->_sessions[$key]);
				$this->_error[$key]  = curl_error($this->_sessions[$key]);
				$this->_info[$key] = curl_getinfo($this->_sessions[$key]);
			}
			else
			{
				$this->_output[$key] = FALSE;
			}
			curl_multi_remove_handle($mh, $this->_sessions[$i]);
		}
		
		// clear and close all sessions
		if ($clear)
		{
			curl_multi_close($mh);
			$this->clear();
		}
		
		return $result;
	}
	
	function is_multi()
	{
		$cnt = count($this->_sessions);
		return $cnt > 1;
	}
	
	
	function info($key = FALSE, $opt = NULL)
	{
		// if there is only one session, then the key is 0
		if (!$this->is_multi())
		{
			$key = 0;
		}
		
		if ($key === FALSE)
		{
			foreach($this->_info as $key => $i)
			{
				if ($opt)
				{
					$info[$key] = $i[$opt];
				}
				else
				{
					$info[$key] = $i;
				}
			}
		}
		else
		{
			if ($opt)
			{
				$info = $this->_info[$key][$opt];
			}
			else
			{
				$info = $this->_info[$key];
			}
		}
		
		return $info;
	}

	/**
	* Closes cURL sessions
	* @param $key int, optional session to close
	*/
	function close($key = FALSE)
	{
		if($key === FALSE)
		{
			foreach($this->_sessions as $session)
			{
				curl_close($session);
			}
		}
		else
		{
			curl_close( $this->_sessions[$key]);
		}
			
	}
	
	/**
	* Remove all cURL sessions
	*/
	public function clear()
	{
		foreach($this->_sessions as $session)
		{
			curl_close($session);
		}
		$this->_sessions = array();
	}
	

	function get($url)
	{
		$opts = array(
			CURLOPT_NOBODY => FALSE,
			CURLOPT_HTTPGET => TRUE,
			);
		$this->add_session($url, $opts);
		return $this->exec();
	}
	
	function post($url, $post = array())
	{
		// NOTE TO SELF: to add a file to upload, you can do the following as a value:
		//'@path/to/file.txt;type=text/html';
		$opts = array(
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPGET => FALSE,
			);
		$this->add_session($url, $opts);
		return $this->exec_single();
	}
	
	function head($url)
	{
		$opts = array(
			CURLOPT_HEADER => TRUE, // no http header
			CURLOPT_NOBODY => TRUE, // no return of body
			);
		$this->add_session($url, $opts);
		return $this->exec_single();
	}
	
	function cookie($url, $cookie, $cleanup = TRUE)
	{
		if (is_array($cookie))
		{
			$cookie = http_build_query($params, NULL, '&amp;');
		}
		$opts = array(
			CURLOPT_COOKIEFILE => $this->cookie_file, // no http header
			CURLOPT_COOKIEJAR => $this->cookie_file, // no return of body
			CURLOPT_COOKIE => $cookie,
			);
		$this->add_session($url, $opts);
		$result = $this->exec_single();
		
		// remove the cookie file
		if (file_exists($this->cookie_file) AND $cleanup)
		{
			@unlink($this->cookie_file);
		}
		return $result;
	}

	function error()
	{
		return $this->_error;
	}
	
	function is_valid($url)
	{
		$opts = array(
			CURLOPT_NOBODY => TRUE, // do a HEAD request only
			CURLOPT_FOLLOWLOCATION => TRUE, // follow redirects
			);
		$this->add_session($url, $opts);
		$result = $this->exec_single();
		
		$retval = $this->info('http_code') == 200;// check if HTTP OK
		return $retval;
	}
	
	public function is_enabled()
	{
		return function_exists('curl_init');
	}
	
	protected function _normalize_url($url)
	{
		if (is_http_path($url))
		{
			$url = site_url($url);
		}
		return $url;
	}

}

/* End of file Curl.php */
/* Location: ./application/libraries/curl.php */