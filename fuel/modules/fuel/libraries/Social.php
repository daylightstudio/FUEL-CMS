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
 */

// ------------------------------------------------------------------------

/**
 * FUEL Social library
 *
 * There is a <dfn>fuel/application/config/social.php</dfn> configuration file for the preferred share URLs.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/share
 */

// --------------------------------------------------------------------

class Social {

	public $share_urls = array(); // an array of URLs
	public $og = array(); // open graph tags
	protected $CI; // Reference to the CI super object


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
		$this->CI = & get_instance();
		$this->initialize($params);
	}

		// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	Config preferences
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
	 * @param	array	Config preferences
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
	 * Set a share URL
	 *
	 * @access	public
	 * @param	string	The key (e.g. twitter, facebook, linkedin..etc)
	 * @param	string	The url which should include placeholders to swap out with passed data
	 * @return	object	The Social object instance itself to make it chainable
	 */
	public function add_share_url($key, $url)
	{
		$this->share_urls[$key] = $url;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a share URL
	 *
	 * @access	public
	 * @param	string	The type (e.g. twitter, facebook, linkedin..etc)
	 * @param	mixed	An array or object of values
	 * @return	string	The translated URL
	 */
	public function share($type, $values = NULL)
	{
		// first check to see if there is a url set on the object with the specified type
		if (empty($this->share_urls[$type]))
		{
			return FALSE;
		}

		if (is_array($type))
		{
			$return = array();

			foreach($type as $t)
			{
				$return[$t] = $this->share($t, $values);
			}
			return $return;
		}
		else
		{

			// normalize the values into a usable array
			$values = $this->normalize_values($values);

			// grab the correct URL 
			$url = $this->share_urls[$type];
			preg_match_all('#\{(.+)\}#U', $url, $matches);

			if (!empty($matches[1]))	
			{
				$placeholders = $matches[1];
				foreach($placeholders as $placeholder)
				{
					$fields = explode('|', $placeholder);
					$default_arr = explode(':', $placeholder);

					// set default value if it exists
					$default = (count($default_arr) > 1) ? end($default_arr) : '';

					foreach($fields as $field)
					{
						if (isset($values[$field]))
						{
							$url = str_replace('{'.$placeholder.'}', rawurlencode($values[$field]), $url);
						}
					}

					// set default if no value exists which can be found after the ":" in the placeholder (e.g. {title|headline:DEFAULT HEADLINE})
					if (!isset($values[$field]))
					{
						$url = str_replace('{'.$placeholder.'}', rawurlencode($default), $url);
					}
				}
			}
			
			return $url;

		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Normalizes the value data to be an array
	 *
	 * @access	protected
	 * @param	mixed	An array or object of values
	 * @return	array	An array of values
	 */
	protected function normalize_values($values)
	{
		if (is_object($values))
		{
			if ($values instanceof Data_record)
			{
				$values = $values->values(TRUE);
			}
			else
			{
				$values = get_object_vars($values);
			}
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates open graph meta tags
	 *
	 * @access	protected
	 * @param	mixed	An array or object of values with keys being title, url, description, image, site_name, or type
	 * @return	string	An array of values
	 */
	public function og($values = array())
	{
		$CI =& get_instance();

		// normalize the values
		$values = $this->normalize_values($values);

		$str = '';

		$defaults = array('url' => current_url(), 'title' => $CI->load->get_var('page_title'), 'description' => $CI->load->get_var('meta_description'), 'source' => $CI->fuel->config('site_name'));

		// If a post object exists, then we can auto create the information if they have the proper fields
		if ($values instanceof Base_post_item_model)
		{
			$values = array();
			$values['url'] = $values->url;
			$values['title'] = $values->title;
			$values['url'] = $values->url;
			$values['description'] = $values->get_excerpt(80, '...');
		}

		$values = array_merge($defaults, $values);

		$valid_types = array('title', 'url', 'description', 'image', 'site_name', 'type');
		
		foreach($values as $key => $val)
		{
			if (!empty($val) AND in_array($key, $valid_types))
			{
				// format image path
				if ($key == 'image' AND !is_http_path($val))
				{
					$val = img_path($val, NULL, TRUE);
				}
				elseif ($key == 'url')
				{
					$val = site_url($val);
				}
				$str .= "\t<meta property=\"og:".$key."\" content=\"".$values[$key]."\">\n";
			}
		}
		return $str;
	}
}


/* End of file Social.php */
/* Location: ./modules/fuel/libraries/Social.php */