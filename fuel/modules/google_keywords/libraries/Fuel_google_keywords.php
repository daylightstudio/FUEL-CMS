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
 * Page analysis 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/page_analysis
 */

// --------------------------------------------------------------------

class Fuel_google_keywords extends Fuel_advanced_module {
	
	public $domain = '';
	public $keywords = '';
	public $num_results = 100;
	public $additional_params = '';
	
	/**
	 * Constructor - Sets Fuel_backup preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct($params);

		if (!extension_loaded('curl')) 
		{
			$this->_add_error(lang('error_no_curl_lib'));
		}
		
		// initialize object if any parameters
		if (!empty($params))
		{
			$this->initialize($params);
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the backup object
	 *
	 * Accepts an associative array as input, containing backup preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		$this->set_params($this->_config);
		
	}
	
	// returns an array with the keywords being the key and the value being a comma separated value of the rankings
	function results($params)
	{
		$this->set_params($params);
		
		// normalize keywords into an array
		if (is_string($this->keywords))
		{
			$this->keywords = explode(',', $this->keywords);
		}
		
		// normalize domain
		if (empty($this->domain))
		{
			$this->domain = $_SERVER['SERVER_NAME'];
		}
		$this->domain = str_replace(array('http://', 'www'), '', $this->domain);
		
		// start CURL and loop through the keywords to test against the domain
		$ch = curl_init();
		$found = array();
		foreach($this->keywords as $keyword)
		{
			$keyword = trim($keyword);
			
			$uri = 'http://www.google.com/search?q='.rawurlencode($keyword).'&num='.$this->num_results.'&'.http_build_query($this->additional_params);

			// scrape html from page running on localhost
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $uri);
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$google_page = curl_exec($ch);				
			preg_match_all('|<h3 class=(["\'])?r\\1?><a.+href="(.+)".+</h3>|Umis', $google_page, $matches);

			// echo "<pre style=\"text-align: left;\">";
			// print_r($matches);
			// echo "</pre>";
			// exit();
			
			$results = array();
			if (!empty($matches[2]))
			{
				$results = $matches[2];
			}

			$num = 1;
			foreach($results as $uri)
			{
				if (strpos($uri, $this->domain) !== FALSE)
				{
					if (!isset($found[$keyword]))
					{
						$found[$keyword] = array();
					}
					$found[$keyword][] = $num;
				}
				$num++;
			}
			$found[$keyword] = implode(', ', $found[$keyword]);
		}
		curl_close($ch);
		return $found;
	}

}

/* End of file Fuel_page_analysis.php */
/* Location: ./modules/fuel/libraries/Fuel_page_analysis.php */