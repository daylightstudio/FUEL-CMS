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
 * FUEL language object
 *
 * This class is used for setting and retrieving the different language options
 * Some code inspired by http://codeigniter.com/wiki/Language_Selection_2
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/fuel/fuel_language
 */

// --------------------------------------------------------------------

class Fuel_language extends Fuel_base_library {
	
	public $options = array(); // The language options available. Specified in the main FUEL config
	public $selected = ''; // The currently selected language. The default is the language specified in the CI config
	public $query_str_param = 'lang'; // The name of the query string parameter to use for setting the language
	public $cookie_name = 'lang'; // The name of the cookie to hold the currently selected language
	public $cookie_exp = '63072000'; // Default is 2 years
	
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
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->load->helper('cookie');
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
	function initialize($params)
	{
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
	function set_options($options)
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
	function options()
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
	function has_multiple()
	{
		return count($this->options) > 1;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Has language as option
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function has_language($lang)
	{
		return isset($this->options[$lang]);
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Sets the selected language
	 *
	 * @access	public
	 * @param	string	The selected language 
	 * @return	boolean
	 */	
	function set_selected($selected)
	{
		if ($this->has_language($selected))
		{
			//$this->CI->config->set_item('language', $selected);
			$this->set_cookie($selected);
			$this->selected = $selected;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the selected language
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function selected()
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
	function selected_label()
	{
		if (isset($this->options[$this->selected]))
		{
			return $this->options[$this->selected];
		}
		return FALSE;
	}
	
	function default_option()
	{
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
	
	function detect($set_selected = TRUE)
	{
		// obtain language code from query string if available
		$language = $this->query_str_value();

		// if that language doesn't exist, then we'll check the cookie value
		if (!$this->has_language($language))
		{
			// if a language cookie available get its info
			$language = $this->cookie_value();
			
			// again... if that language doesn't exist in the query string or cookie, then we'll check the HTTP_ACCEPT_LANGUAGE value
			if (!$this->has_language($language))
			{
				$accept_langs = $this->CI->input->server('HTTP_ACCEPT_LANGUAGE');
				if ($accept_langs !== FALSE)
				{
					//explode languages into array
					$accept_langs = strtolower($accept_langs);
					$accept_langs = explode(",", $accept_langs);

					// check all of them
					foreach ($accept_langs as $lang)
					{
						// remove all after ';'
						$pos = strpos($lang,';');
						if ($pos !== false)
						{
							$lang = substr($lang, 0, $pos); 
						}
						
						if ($this->has_language($lang))
						{
							$language = $lang;
							break;
						}
					}
				}
			}
		}
		
		// if language is still not legit, we'll use the default language
		if ($language === FALSE)
		{
			$language = $this->default_option();
		}
		if ($set_selected)
		{
			$this->set_selected($language);
		}
		return $language;
	}
	
	function set_cookie($lang)
	{
		if (!$this->has_language($lang))
		{
			return FALSE;
		}
		$config = array(
			'name' => $this->cookie_name, 
			'value' => $lang,
			'expire' => $this->cookie_exp,
			'path' => $this->CI->config->item('cookie_path'),
		);
		set_cookie($config);
	}
	
	function cookie_value()
	{
		return get_cookie($this->cookie_name);
	}
	
	function set_query_str($lang)
	{
		if (!$this->has_language($lang))
		{
			return FALSE;
		}
		$_GET[$this->query_str_param] = $lang;
	}
	
	function query_str_value()
	{
		if ($this->CI->input->get($this->query_str_param))
		{
			return $this->CI->input->get($this->query_str_param);
		}
		return FALSE;
	}
	
}
/* End of file fuel_language.php */
/* Location: ./modules/fuel/libraries/fuel_language.php */