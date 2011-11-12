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
	public $block_size = 5;
	
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
			$this->_add_error(lang('error_no_curl_lib')); // found in fuel_lang
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

	
	function add_session($url, $opts = array(), $opt_params = NULL)
	{
		// normalize the URL
		$url = $this->_normalize_url($url);
		
		$this->_sessions[] = curl_init($url);
		
		$key = count($this->_sessions) - 1;
		
		// set default options
		$default_opts = $this->_opts();
		$this->set_options($default_opts, $key);
		
		if (!empty($opts))
		{
			$o = array();
			
			if (is_array($opts))
			{
				// make it easy to pass options
				$o = array();
				foreach($opts as $k => $val)
				{
					if (is_string($k) && !is_numeric($k))
					{
						$c = 'CURLOPT_' . strtoupper($k);
						if (defined($c))
						{
							$constant = constant($c);
							$o[$constant] = $val;
						}
					}
					else
					{
						$o[$k] = $val;
					}
				}
			}
			else if (is_string($opts))
			{
				// shortcuts
				switch($opts)
				{
					case 'get':
						$o = $this->_opts('get');
						break;
					case 'post':
						$o = $this->_opts('post', $opt_params);
						break;
					case 'head':
						$o = $this->_opts('head');
						break;
					case 'none':
						$o = $this->_opts('none');
						break;
					case 'cookie':
						$o = $this->_opts('cookie', $opt_params);
						break;
				}
			}

			$this->set_options($o, $key);
		}
	}
	
	function set_options($opts = array(), $key = 0)
	{
		curl_setopt_array($this->_sessions[$key], $opts);
	}
	
	function exec($key = FALSE, $clear = TRUE)
	{
		if (empty($this->_sessions))
		{
			return FALSE;
		}
		
		if ($this->is_multi())
		{
			if ($key === FALSE)
			{
				$this->exec_multi($clear, $this->block_size);
			}
			else
			{
				$this->exec_single($key, $clear);
			}
		}
		else
		{
			$this->exec_single($key, $clear);
		}
		
		// set by exec_single/exec_multi
		return $this->_output;
	}
	
	function exec_single($key = 0, $clear = TRUE)
	{
		$this->_output = curl_exec($this->_sessions[$key]);
		$this->_error[$key] = curl_error($this->_sessions[$key]);
		$this->_info[$key] = curl_getinfo($this->_sessions[$key]);

		// clear and close all sessions
		if ($clear)
		{
			$this->clear();
		}
		return $this->_output;
	}

	public function exec_multi($clear = TRUE, $block_size = NULL)
	{
		$mh = curl_multi_init();
		
		if (empty($block_size)) $block_size = $this->block_size;
		
		// partially from http://stackoverflow.com/questions/2874845/whats-the-fastest-way-to-scrape-a-lot-of-pages-in-php/2874903#2874903
		
		$i = 0;  // count where we are in the list so we can break up the runs into smaller blocks
		$block = array(); // to accumulate the curl_handles for each group we'll run simultaneously
	
		// reset arrays
		$this->_output = array(); 
		$this->_error = array();
		$this->_info = array();

		

		foreach ($this->_sessions as $key => $session)
		{
			$i++; // increment the position-counter

		    // add the handle to the curl_multi_handle and to our tracking "block"
			curl_multi_add_handle($mh, $session);
			$block[] = $session;

			// -- check to see if we've got a "full block" to run or if we're at the end of out list of handles
			if (($i % $block_size == 0) or ($i == count($this->_sessions)))
			{
				
				$this->CI->benchmark->mark('a');
				
				
				// -- run the block
				$running = NULL;
				do {
					// track the previous loop's number of handles still running so we can tell if it changes
					$running_before = $running;

					// run the block or check on the running block and get the number of sites still running in $running
					$mrc = curl_multi_exec($mh, $running);
					
					// if the number of sites still running changed, print out a message with the number of sites that are still running.
					// if ($running != $running_before)
					// {
					// 	echo "<pre style=\"text-align: left;\">";
					// 	print_r("Waiting for $running sites to finish...\n");
					// 	echo "</pre>";
					// }
				} while ($running > 0);
					$this->CI->benchmark->mark('b');
					
					echo "<pre style=\"text-align: left;\">x";
					print_r($this->CI->benchmark->elapsed_time('a', 'b'));
					echo "x</pre>";
					
				if ($mrc != CURLM_OK)
				{
					return FALSE;
				}
				
				// get content foreach session, retry if applied
				foreach ($block as $b)
				{
					$this->_error[]  = curl_error($b);
					
					$info = curl_getinfo($b);
					$this->_info[] = $info;

					// check for valid http_code
					if ($info['http_code'] < 400)
					{
						$result = curl_multi_getcontent($b);
						$this->_output[] = $result;
					}
					else
					{
						$this->_output[] = FALSE;
					}
					curl_multi_remove_handle($mh, $b);
				}
				$block = array();
			}
		}
	
		
		
		// clear and close all sessions
		if ($clear)
		{
			curl_multi_close($mh);
			$this->clear();
		}
		
		return $this->_output;
	}
	
	function is_multi()
	{
		$cnt = count($this->_sessions);
		return $cnt > 1;
	}
	
	
	function info($opt = NULL, $key = 0)
	{
		$info = array();
		if ($key === TRUE)
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
		else if (isset($this->_info[$key]))
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
	
	function sessions()
	{
		return $this->_sessions;
	}

	function output()
	{
		return $this->_output;
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
	function clear()
	{
		foreach($this->_sessions as $session)
		{
			curl_close($session);
		}
		$this->_sessions = array();
	}
	

	function get($url)
	{
		$opts = $this->_opts('get');
		$this->add_session($url, $opts);
		return $this->exec();
	}
	
	function post($url, $post = array())
	{
		// NOTE TO SELF: to add a file to upload, you can do the following as a value:
		//'@path/to/file.txt;type=text/html';
		$opts = $this->_opts('post', $post);
		$this->add_session($url, $opts);
		return $this->exec_single();
	}
	
	function head($url)
	{
		$opts = $this->_opts('head');
		$this->add_session($url, $opts);
		return $this->exec_single();
	}
	
	function cookie($url, $cookie, $cleanup = TRUE)
	{
		if (is_array($cookie))
		{
			$cookie = http_build_query($params, NULL, '&amp;');
		}
		$opts = $this->_opts('cookie', $cookie);
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
		$opts = $this->_opts('head');
		
		$key = 0;
		$this->add_session($url, $opts);
		$result = $this->exec_single();
		$retval = $this->info('http_code') < 400;// check if HTTP OK
		
		return $retval;
	}
	
	protected function _opts($type = NULL, $opt_params = NULL)
	{
		switch($type)
		{
			case 'get':
				$opts = array(
								CURLOPT_NOBODY => FALSE,
								CURLOPT_HTTPGET => TRUE,
								CURLOPT_FOLLOWLOCATION => TRUE, // follow redirects
							);
				break;
				
			case 'post':
				$opts = array(
								CURLOPT_POST => TRUE,
								CURLOPT_POSTFIELDS => $opt_params,
								CURLOPT_HTTPGET => FALSE,
							);
				break;
				
			case 'head':
				$opts = array(
								CURLOPT_HEADER => TRUE, // just http header
								CURLOPT_NOBODY => TRUE, // no return of body
								CURLOPT_FOLLOWLOCATION => TRUE, // follow redirects
							);
				break;
				
			case 'none':
				$opts = array(
								CURLOPT_HEADER => FALSE, // just http header
								CURLOPT_NOBODY => TRUE, // no return of body
								CURLOPT_FOLLOWLOCATION => TRUE, // follow redirects
							);
				break;
				
			case 'cookie':
				$opts = array(
								CURLOPT_COOKIEFILE => $this->cookie_file, // no http header
								CURLOPT_COOKIEJAR => $this->cookie_file, // no return of body
								CURLOPT_COOKIE => $opt_params,
							);
				break;
				
			default:
				$opts = array(
								CURLOPT_HEADER => 0,
								CURLOPT_RETURNTRANSFER => TRUE,
								CURLOPT_TIMEOUT => $this->timeout,
								CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
								CURLOPT_DNS_CACHE_TIMEOUT => $this->dns_cache_timeout,
								CURLOPT_BINARYTRANSFER => TRUE,
								CURLOPT_USERAGENT => $this->user_agent,
								
							);
		}
		return $opts;
		
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