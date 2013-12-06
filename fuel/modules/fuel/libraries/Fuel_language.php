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
 * FUEL language object
 *
 * This class is used for setting and retrieving the different language options
 * Some code inspired by https://github.com/EllisLab/CodeIgniter/wiki/Language-Selection-2
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_language
 */

// --------------------------------------------------------------------

class Fuel_language extends Fuel_base_library {
	
	public $options = array(); // The language options available. Specified in the main FUEL config
	public $selected = ''; // The currently selected language. The default is the language specified in the CI config
	public $query_str_param = 'lang'; // The name of the query string parameter to use for setting the language
	public $cookie_name = ''; // The name of the cookie to hold the currently selected language
	public $cookie_exp = '63072000'; // default is 2 years
	public $use_cookies = TRUE; // use cookies to remember a selected language
	public $detect_user_agent = 'auto'; // will check the user agent during language detection
	public $default_option = NULL; // the default language to use 
	
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
		parent::__construct();
		$this->CI->load->helper('cookie');
		$this->CI->load->library('user_agent');
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
		// first set the default to the values in the FUEL config
		$_fuel_config = array('query_str_param', 'cookie_name', 'cookie_exp', 'use_cookies', 'detect_user_agent', 'default_option');
		foreach($_fuel_config as $p)
		{
			$config = $this->fuel->config('language_'.$p);
			if (!is_null($config))
			{
				$this->$p = $config;	
			}
		}

		parent::initialize($params);
		$this->options = $this->fuel->config('languages');
		$this->selected = $this->CI->config->item('language');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the language options
	 *
	 * @access	public
	 * @param	array	Array of language options with the key being the language and the value being a friendly label
	 * @return	void
	 */	
	public function set_options($options)
	{
		$this->options = (array)$options;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the language options
	 *
	 * @access	public
	 * @return	array
	 */	
	public function options()
	{
		return (array)$this->options;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns boolean value if there are more than one language option
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function has_multiple()
	{
		return count($this->options) > 1;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines whether a language option exists
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function has_language($lang)
	{
		return (!empty($lang) AND isset($this->options[$lang]));
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Sets the selected language
	 *
	 * @access	public
	 * @param	string	The selected language 
	 * @param	boolean	Set the config language value (optional)
	 * @param	boolean	Set the query string lang value (optional)
	 * @return	boolean
	 */	
	public function set_selected($selected, $set_config = FALSE, $set_query = FALSE)
	{
		if ($this->has_language($selected) AND $this->is_valid($selected))
		{
			$this->set_cookie($selected);
			$this->selected = $selected;

			if ($set_query)
			{
				$this->set_query_str($selected);	
			}
			
			if ($set_config)
			{
				$this->CI->config->set_item('language', $selected);
			}
			return TRUE;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the selected language
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function selected()
	{
		return $this->selected;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the selected languages label value
	 *
	 * @access	public
	 * @return	string
	 */	
	public function selected_label()
	{
		if (isset($this->options[$this->selected]))
		{
			return $this->options[$this->selected];
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the default language option
	 *
	 * @access	public
	 * @param	string language key value to set as the default
	 * @return	string
	 */	
	public function set_default_option($lang)
	{
		return $this->default_option = $lang;
	}

	// --------------------------------------------------------------------
	
	/**
	 * The default language option
	 *
	 * @access	public
	 * @return	string
	 */	
	public function default_option()
	{
		if (!empty($this->default_option))
		{
			return $this->default_option;
		}

		if (is_array($this->options))
		{
			reset($this->options);
			return key($this->options);
		}
		else
		{
			return (string)$this->options;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the language is the default language
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_default($lang)
	{
		if (!$this->has_multiple())
		{
			return TRUE;
		}
		// the default is the first option of the list
		$default = key($this->options);
		return $lang == $default;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the language is a valid selection
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_valid($lang)
	{
		return isset($this->options[$lang]);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Detects which language should be used 
	 *
	 * @access	public
	 * @param	boolean	Whether to set the selected value to the detected or not (optional)
	 * @return	string
	 */	
	public function detect($set_config = FALSE)
	{
		$language = FALSE;

		// obtain language code from query string if available
		if ($this->is_mode('query_string') OR ($this->is_mode('both')))
		{
			$language = $this->query_str_value();
		}

		// get the preferred mode for obtaining the language if no language value is set yet from query string (if allowed)
		if ($this->is_mode('segment') OR $this->is_mode('both') AND empty($language))
		{
			$language = $this->lang_segment();
			if (!$language)
			{
				$language = $this->default_option();
			}
		}

		// if that language doesn't exist, then we'll check the cookie value
		if (!$this->has_language($language))
		{
			// if a language cookie is available get its info
			$language = $this->cookie_value();
			
			// again... if that language doesn't exist in the query string or cookie, then we'll check the HTTP_ACCEPT_LANGUAGE value
			// this will only be used if you are using the query_string mode and have detect_user_agent set to TRUE (which it is by default)
			if (!$this->has_language($language) AND 
				($this->detect_user_agent === TRUE OR 
				(strtolower($this->detect_user_agent) == 'auto' AND $this->is_mode('query_string'))))
			{
				$language = $this->user_agent();
			}
		}

		// if language is still not legit, we'll use the default language
		if ($language === FALSE OR !$this->is_valid($language))
		{
			$language = $this->default_option();
		}

		// only set the selected if there are multiple languages
		if ($this->has_multiple())
		{
			$this->set_selected($language, $set_config);	
		}
		
		return $language;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Retrieves the language set by the user agent
	 *
	 * @access	public
	 * @return	string
	 */	
	public function user_agent()
	{
		// check all of them
		foreach ($this->CI->agent->languages() as $lang)
		{
			if ($this->has_language($lang))
			{
				$language = $lang;
				return $lang;
			}
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the language cookie
	 *
	 * @access	public
	 * @param	string	The selected language 
	 * @return	void
	 */	
	public function set_cookie($lang)
	{
		if (!$this->use_cookies) return;

		if (!$this->has_language($lang))
		{
			return FALSE;
		}
		$config = array(
			'name' => $this->cookie_name(), 
			'value' => $lang,
			'expire' => $this->cookie_exp,
			'path' => $this->CI->config->item('cookie_path'),
		);
		set_cookie($config);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the language cookie value
	 *
	 * @access	public
	 * @param	string	The selected language 
	 * @return	string
	 */	
	public function cookie_value()
	{
		if (!$this->use_cookies) return FALSE;
		return get_cookie($this->cookie_name());
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the name of the cookie
	 *
	 * @access	public
	 * @return	string
	 */	
	public function cookie_name()
	{
		if (!empty($this->cookie_name))
		{
			return $this->cookie_name;
		}
		else
		{
			return 'fuel_lang_'.substr($this->fuel->auth->get_session_namespace(), 5);
		}
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the query string to the selected language from the query string
	 *
	 * @access	public
	 * @param	string	The selected language 
	 * @return	void
	 */	
	public function set_query_str($lang)
	{
		if (!$this->has_language($lang))
		{
			return FALSE;
		}
		$_GET[$this->query_str_param] = $lang;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the selected language from the query string
	 *
	 * @access	public
	 * @param	string	The selected language 
	 * @return	boolean
	 */	
	public function query_str_value()
	{
		if ($this->CI->input->get($this->query_str_param))
		{
			return $this->CI->input->get($this->query_str_param);
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the method to use for setting language. Value is either 'segment', 'query_string' or both
	 *
	 * @access	public
	 * @return	boolean
	 */
	function mode()
	{
		return strtolower($this->fuel->config('language_mode'));
	}

	// --------------------------------------------------------------------
	
	/**
	 *
	 * @access	public
	 * @return	boolean
	 */
	function set_mode($mode)
	{
		return $this->fuel->set_config('language_mode', $mode);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean value based on the mode value you pass
	 *
	 * @access	public
	 * @param	string The name of the mode to test (e.g. 'segment', 'query_string')
	 * @return	boolean
	 */
	function is_mode($mode)
	{
		return $this->mode() == strtolower($mode);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean value depending on if the passed segment value equals the current language segment
	 *
	 * @access	public
	 * @param	string The language segment value
	 * @return	boolean
	 */
	function is_current_lang_segment($segment)
	{
		return $this->lang_segment() == $segment;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean value depending on if the passed segment value is a valid language segment
	 *
	 * @access	public
	 * @param	string The language segment value
	 * @return	boolean
	 */
	function is_lang_segment($lang)
	{
		if (!empty($lang) AND in_array($lang, array_keys($this->options())))
		{
			return $lang;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the language segment value or FALSE if none exists or the mode is set to "query_string"
	 *
	 * @access	public
	 * @param	string The URI string to check and extract the language segment from (optional)
	 * @param	mixed Determines whether to use a routed (TRUE), non-routed (FALSE), or both (default) when looking at the URI segment if one is not provided in first argument (optional)
	 * @return	string
	 */
	function lang_segment($uri = NULL, $routed = 'both')
	{
		// immediately return false if the mode is query_string
		if ($this->is_mode('query_string'))
		{
			return FALSE;
		}

		if (is_null($uri))
		{
			if ($routed == 'both')
			{

				// check the normal segment array
				$segs = $this->CI->uri->segment_array();
				$lang = array_shift($segs);
				if ($this->is_lang_segment($lang))
				{
					return $lang;
				}

				// check the routed segment array
				$segs = $this->CI->uri->rsegment_array();
				$lang = array_shift($segs);
				if ($this->is_lang_segment($lang))
				{
					return $lang;
				}

			}
			else
			{
				if ($routed === FALSE)
				{
					$segs = $this->CI->uri->segment_array();
				}
				else
				{
					$segs = $this->CI->uri->rsegment_array();
				}
				$lang = array_shift($segs);
				return $this->is_lang_segment($lang);
			}
		}
		else if (is_string($uri))
		{
			$lang = current(explode('/', $uri));
			return $this->is_lang_segment($lang);
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Cleans a URI string value of any language segements
	 *
	 * @access	public
	 * @param	string The URI string to check. If none provided, will use the regment_array or rsegment_array on the URI object (optional)
	 * @param	boolean Determines whether to use a routed (TRUE), non-routed (FALSE) when looking at the URI segment if one is not provided in first argument (optional)
	 * @return	string
	 */
	function cleaned_uri($uri = NULL, $routed = FALSE)
	{
		$segs = $this->cleaned_uri_segments($uri, $routed);
		return implode($segs, '/');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Cleans a URI segments of any language segements
	 *
	 * @access	public
	 * @param	string The URI string to check. If none provided, will use the regment_array or rsegment_array on the URI object (optional)
	 * @param	boolean Determines whether to use a routed (TRUE), non-routed (FALSE) when looking at the URI segment if one is not provided in first argument (optional)
	 * @return	string
	 */
	function cleaned_uri_segments($uri = NULL, $routed = FALSE)
	{
		if (is_null($uri))
		{
			$segs = ($routed) ? $this->CI->uri->rsegment_array() : $this->CI->uri->segment_array();

			// reset index to 0
			$segs = array_values($segs);
		}
		else
		{
			$uri = trim($uri, '/');
			$segs = explode('/', $uri);
		}
		if (isset($segs[0]) AND in_array($segs[0], array_keys($this->options())))
		{
			array_shift($segs);	
		}

		array_unshift($segs, NULL);
		unset($segs[0]);
		return $segs;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Will redirect you to a language specific URL if the passed or detected language value does not match the current language
	 *
	 * @access	public
	 * @param	string The language to check and redirect to. If no value is provided, it will detect (optional)
	 * @return	void
	 */
	function redirect_to_lang($lang = NULL)
	{
		if ($this->lang_segment($lang) AND !$this->is_current_lang_segment($lang))
		{
			if (empty($lang))
			{
				$lang = $this->detect();
			}

			$uri = (!is_home()) ? $this->cleaned_uri() : '';

			$uri = $this->uri($uri, $lang);

			// must run site_url function on it first or else 
			// passing simply the "$uri" value will result in a redirect loop
			$url = site_url($uri, NULL, $lang);
			redirect($url);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the URI path appending any necessary language information to the path
	 *
	 * @access	public
	 * @param	string The URI path (optional)
	 * @param	string The language version you want the current URL to represent (optional)
	 * @return	string
	 */
	function uri($uri = '', $lang = NULL)
	{
		$use_detect_lang = ($lang === TRUE OR (is_null($lang) AND (isset($this->CI->fuel) AND $this->CI->fuel->config('add_language_to_site_url'))));
		if (((is_string($lang) AND $lang != '') OR $use_detect_lang) AND !defined('FUEL_ADMIN') AND USE_FUEL_ROUTES === FALSE)
		{

			// set static variables to speed up subsequent calls
			static $detect_lang;
			static $lang_seg;
			static $no_lang;
	
			// set static $detct_lang to speed up subsequent calls
			if ($use_detect_lang)
			{
				if (is_null($detect_lang))
				{
					$detect_lang =  $this->detect();
				}
				$lang = $detect_lang;
			}

			if (is_null($lang_seg))
			{
				$lang_seg = $this->lang_segment();
			}

			if (is_null($no_lang))
			{
				$no_lang[] = FUEL_FOLDER;
				$fuel_path = trim($this->CI->fuel->config('fuel_path'), '/');
				if (FUEL_FOLDER != $fuel_path)
				{
					$no_lang[] = $fuel_path;
				}
			}

			$uri = $this->cleaned_uri($uri);
			
			// if $lang is set then we will check to see if it is a legit language and use it
			if (!empty($lang) AND $lang != $this->is_default($lang) AND !in_array($lang_seg, $no_lang))
			{
				if (!$this->is_mode('query_string'))
				{
					$uri = $lang.'/'.trim($uri, '/');
				}
				else
				{
					$uri_append = (strpos($uri, '?') !== FALSE) ? '&' : '?';
					$uri = $uri.$uri_append.'lang='.$lang;
				}
			}
		}
		return $uri;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the URL appending any necessary language information to the path. 
	 * This is an alias to the site_url function without the second http parameter. 
	 * The <dfn>site_lang_url</dfn> function in the MY_language_helper calls this method.
	 *
	 * @access	public
	 * @param	string The URI path (optional)
	 * @param	string The language version you want the current URL to represent (optional)
	 * @return	string
	 */
	function url($uri = '', $lang = NULL)
	{
		return site_url($uri, NULL, $lang);
	}


}
/* End of file fuel_language.php */
/* Location: ./modules/fuel/libraries/fuel_language.php */