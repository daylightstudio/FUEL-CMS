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
 * Curl library
 * Some code borrowed from:
 * http://semlabs.co.uk/journal/object-oriented-curl-class-with-multi-threading
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/curl
 */
// --------------------------------------------------------------------

class Curl {
	
	public $user_agent = ''; // The user agent CURL should use
	public $timeout = 10; // The maximum number of seconds to allow cURL functions to execute.
	public $connect_timeout = 10; // The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
	public $dns_cache_timeout = 3600; // The number of seconds to keep DNS entries in memory. This option is set to 120 (2 minutes) by default.
	public $cookie_file = 'my_cookie.txt'; // The name to be used for the cookie file
	public $block_size = 5; // The number of CURL sessions to executed simultaneously
	
	protected $CI; // Reference to the CI super object
	protected $_sessions = array(); // CURL sessions
	protected $_info; // CURL session information
	protected $_error; // CURL Session errors
	protected $_output; // Executed CURL output
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		if (!$this->is_enabled())
		{
			show_error(lang('error_no_curl_lib')); // found in fuel_lang
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
	public function initialize($params = array())
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
	public function set_params($params)
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

	
	// --------------------------------------------------------------------
	
	/**
	 * Adds a CURL session.
	 *
	 * @access	public
	 * @param	string	The URL to use for the CURL session
	 * @param	array	An array of CURL options (http://www.php.net/manual/en/function.curl-setopt.php). Can be short syntax of 'get', 'post', 'head', 'none 'cookie' (optional)
	 * @param	array	An array of additional options you can pass to your request. In particular $_POST or $_COOKIE parameters (optional)
	 * @return	void
	 */	
	public function add_session($url, $opts = array(), $opt_params = array())
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds a CURL option to a particular session.
	 *
	 * @access	public
	 * @param	string	The CURL option (http://www.php.net/manual/en/function.curl-setopt.php)
	 * @param	mixed	The CURL value for the option
	 * @param	int	The key value of a CURL session (optional)
	 * @return	void
	 */	
	public function set_option($opt, $val, $key = 0)
	{
		curl_setopt($this->_sessions[$key], $opt, $val);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds CURL options to a particular session.
	 *
	 * @access	public
	 * @param	array	An array of CURL options (http://www.php.net/manual/en/function.curl-setopt.php).
	 * @param	int	The key value of a CURL session (optional)
	 * @return	void
	 */	
	public function set_options($opts, $key = 0)
	{
		curl_setopt_array($this->_sessions[$key], $opts);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Executes either a single or multiple CURL session
	 *
	 * @access	public
	 * @param	string	The key value of a CURL session. If none provided, it will execute all sessions in the stack (optional)
	 * @param	boolean	Wether to clear out the sessions after execution. Default is TRUE (optional)
	 * @return	string
	 */	
	public function exec($key = FALSE, $clear = TRUE)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Executes a single
	 *
	 * @access	public
	 * @param	int	The key value of a CURL session. If none provided, it will execute all sessions in the stack (optional)
	 * @param	boolean	Wether to clear out the sessions after execution. Default is TRUE (optional)
	 * @return	string
	 */	
	public function exec_single($key = 0, $clear = TRUE)
	{
		// in case it' set to FALSE
		if (empty($key))
		{
			$key = 0;
		}

		if ($clear)
		{
			$this->_error = array();
			$this->_info = array();
		}

		$this->_output = curl_exec($this->_sessions[$key]);
		$curl_error = curl_error($this->_sessions[$key]);
		if (!empty($curl_error))
		{
			$this->_error[$key] = $curl_error;
		}
		$this->_info[$key] = curl_getinfo($this->_sessions[$key]);

		// clear and close all sessions
		if ($clear)
		{
			$this->clear();
		}
		return $this->_output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Executes multiple sessions at once. 
	 *
	 * Can be more efficient when doing a lot of CURL requests at once.
	 *
	 * @access	public
	 * @param	boolean	Wether to clear out the sessions after execution. Default is TRUE (optional)
	 * @param	int	The number to execute simultaneously. Default is 5 (optional)
	 * @return	string
	 */	
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
				
				// -- run the block
				$running = NULL;
				do {
					// track the previous loop's number of handles still running so we can tell if it changes
					$running_before = $running;

					// run the block or check on the running block and get the number of sites still running in $running
					$mrc = curl_multi_exec($mh, $running);

				} while ($running > 0);

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
					if ($info['http_code'] < 400 AND $this->info('http_code') != 0)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines if their are multiple sessions in the stack to execute.
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_multi()
	{
		$cnt = count($this->_sessions);
		return $cnt > 1;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns information of an executed session.
	 *
	 * @access	public
	 * @param	string	The name of the parameter to return. If no value is proviced, then all values will be returned (optional)
	 * @param	mixed	The key value of a CURL session. If set to TRUE, then it will return all the infos from a mult-session (optional)
	 * @return	mixed
	 */	
	public function info($opt = NULL, $key = 0)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of CURL resource objects.
	 *
	 * @access	public
	 * @param	int	The key value of a CURL session (optional)
	 * @return	array
	 */	
	public function sessions($key = FALSE)
	{
		if ($key === TRUE)
		{
			return $this->_sessions[$key];
		}
		return $this->_sessions;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the output of an executed CURL session.
	 *
	 * If multiple sessions are executed, the output will be in an array
	 *
	 * @access	public
	 * @param	int	The key value of a CURL session (optional)
	 * @return	mixed
	 */	
	public function output($key = FALSE)
	{
		if ($key === TRUE AND is_array($this->_output))
		{
			return $this->_output[$key];
		}
		return $this->_output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Closes CURL session(s).
	 *
	 * If multiple sessions were executed, and no key is provided, all sessions will be closed.
	 *
	 * @access	public
	 * @param	int	The key value of a CURL session (optional)
	 * @return	void
	 */	
	public function close($key = FALSE)
	{
		if($key === FALSE)
		{
			foreach($this->_sessions as $session)
			{
				curl_close($session);
			}
		}
		else if (isset($this->_session[$key]))
		{
			curl_close($this->_session[$key]);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clears CURL session(s) from the stack.
	 *
	 * If multiple sessions were executed, and no key is provided, all sessions will be cleared.
	 *
	 * @access	public
	 * @param	int	The key value of a CURL session (optional)
	 * @return	void
	 */	
	public function clear($key = FALSE)
	{
		if($key === FALSE)
		{
			foreach($this->_sessions as $session)
			{
				curl_close($session);
			}
			$this->_sessions = array();
		}
		else if (isset($this->_session[$key]))
		{
			curl_close($this->_session[$key]);
			unset($this->_session[$key]);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Shorthand for a simple GET CURL request
	 *
	 * @access	public
	 * @param	string	The URL to use for the CURL session
	 * @return	string
	 */	
	public function get($url)
	{
		$opts = $this->_opts('get');
		$this->add_session($url, $opts);
		return $this->exec();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Shorthand for a simple POST CURL request
	 *
	 * @access	public
	 * @param	string	The URL to use for the CURL session
	 * @param	array	An array of $_POST parameter to pass to the URL (optional)
	 * @return	string
	 */	
	public function post($url, $post = array())
	{
		// NOTE TO SELF: to add a file to upload, you can do the following as a value:
		//'@path/to/file.txt;type=text/html';
		$opts = $this->_opts('post', $post);
		$this->add_session($url, $opts);
		return $this->exec_single();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Shorthand for a simple HEAD CURL request
	 *
	 * @access	public
	 * @param	string	The URL to use for the CURL session
	 * @return	string
	 */	
	public function head($url)
	{
		$opts = $this->_opts('head');
		$this->add_session($url, $opts);
		return $this->exec_single();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Shorthand for a simple POST CURL request
	 *
	 * @access	public
	 * @param	string	The URL to use for the CURL session
	 * @param	array	An array of parameter to set on the cookie
	 * @param	boolean	Whether to cleanup the cookie crumbs left behind (optional)
	 * @return	string
	 */	
	public function cookie($url, $cookie, $cleanup = TRUE)
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

	// --------------------------------------------------------------------
	
	/**
	 * Returns error(s) from the CURL request
	 *
	 * @access	public
	 * @param	string	An index value of an error message (optional)
	 * @return	mixed
	 */	
	public function error($key = FALSE)
	{
		if ($key === FALSE)
		{
			return $this->_error;
		}
		return $this->_error[$key];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns TRUE if there are errors detected and FALSE if not
	 *
	 * @access	public
	 * @param	string	An index value of an error message (optional)
	 * @return	boolean
	 */	
	public function has_error($key = FALSE)
	{
		if ($key === FALSE)
		{
			return !empty($this->_error);
		}
		return !empty($this->_error[$key]);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Shorthand for a simple POST CURL request
	 *
	 * @access	public
	 * @param	string	The URL to use for the CURL session
	 * @return	void
	 */	
	public function is_valid($url)
	{
		$opts = $this->_opts('head');
		
		$key = 0;
		$this->add_session($url, $opts);
		$result = $this->exec_single();
		$retval = $this->info('http_code') < 400 AND $this->info('http_code') != 0;// check if HTTP OK
		
		return $retval;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Convenience method that returns an array of CURL options provided a specific "type" parameter
	 *
	 * @access	protected
	 * @param	string	A type of CURL request. (optional)
	 * @param	array	Additional options to pass to the CURL option (e.g. $_POST or cookie parameters)  (optional)
	 * @return	array
	 */	
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

	// --------------------------------------------------------------------
	
	/**
	 * Determines whether or not CURL can run based on the system's settings
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_enabled()
	{
		return function_exists('curl_init');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Convenience method to normalize a URL path
	 *
	 * @access	protected
	 * @param	string	The URL to use for the CURL session
	 * @return	string
	 */	
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
/* Location: ./modules/fuel/libraries/curl.php */