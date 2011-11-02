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
	protected $_curl;
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
		if (!extension_loaded('curl')) 
		{
			if ( ! $this->is_enabled())
			{
				log_message('error', 'cURL Class - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.');
			}
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
		$this->_curl = curl_init();
		$default_opts = array(
						CURLOPT_HEADER => 0,
						CURLOPT_RETURNTRANSFER => TRUE,
						CURLOPT_TIMEOUT => $this->timeout,
						CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
						CURLOPT_DNS_CACHE_TIMEOUT => $this->dns_cache_timeout,
						CURLOPT_BINARYTRANSFER => TRUE,
						CURLOPT_USERAGENT => $this->user_agent,
					);
		curl_setopt_array($this->_curl, $default_opts);
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

	
	function execute($url, $opts = array())
	{
		// normalize the URL
		$url = $this->_normalize_url($url);
		
		// make it easy to pass options
		$o = array();
		foreach($opts as $key => $val)
		{
			if (is_string($key) && !is_numeric($key))
			{
				$constant = constant('CURLOPT_' . strtoupper($key));
				$o[$constant] = $val;
			}
			else
			{
				$o[$key] = $val;
			}
		}

		// set url 
		curl_setopt($this->_curl, CURLOPT_URL, $url);
		
		// merge in any other options
		curl_setopt_array($this->_curl, $o);
		$output = curl_exec($this->_curl);
		
		$this->_output = curl_exec($this->_curl); 
		$this->_info = curl_getinfo($this->_curl);
		$this->_error  = curl_error($this->_curl);
		curl_close($this->_curl);
		
		return $this->_output;
	}
	
	function get($url)
	{
		$opts = array(
			CURLOPT_NOBODY => FALSE,
			CURLOPT_HTTPGET => TRUE,
			);
		return $this->execute($url, $opts);
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
		
		$result = $this->execute($url, $opts);
		return $result;
	}
	
	function head($url)
	{
		$opts = array(
			CURLOPT_HEADER => TRUE, // no http header
			CURLOPT_NOBODY => TRUE, // no return of body
			);
		$result = $this->execute($url, $opts);
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
		$result = $this->execute($url);
		
		// remove the cookie file
		if (file_exists($this->cookie_file) AND $cleanup)
		{
			@unlink($this->cookie_file);
		}
		return $result;
	}
	
	function info($opt = NULL)
	{
		$info = curl_getinfo($this->_curl, $opt);
		return $info;
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
		$this->execute($url, $opts);
		$retval = $this->info(CURLINFO_HTTP_CODE) == 200;// check if HTTP OK
		return $retval;
	}
	
	function query($url, $post = array())
	{
		$output = $this->post($url, $post);
		
		require_once(TESTER_PATH.'libraries/phpQuery.php');
		
		phpQuery::newDocumentHTML($output, strtolower($this->CI->config->item('charset')));
		$this->loaded_page = $output;
		return $output;
		
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