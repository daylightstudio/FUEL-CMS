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
 * FUEL sitevariables object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_sitevars
 */

// --------------------------------------------------------------------

class Fuel_sitevars extends Fuel_module {
	
	protected $module = 'sitevariables';
		
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of site variables pertaining to a given URI path
	 *
	 * @access	public
	 * @param	string	A URI path. If left blank, the current URI path will be used (optional)
	 * @return	array
	 */	
	public function get($location = NULL)
	{
		if (is_null($location))
		{
			$location = uri_path();
		}
		
		$this->fuel->load_model('fuel_sitevariables');
		
		$site_vars = $this->CI->fuel_sitevariables_model->find_all_array(array('active' => 'yes'));
		
		$vars = array();
		
		// Loop through the pages array looking for wild-cards
		foreach ($site_vars as $site_var){
			
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $site_var['scope']));

			// Does the RegEx match?
			if (empty($key) OR preg_match('#^'.$key.'$#', $location))
			{
				$vars[$site_var['name']] = $site_var['value'];
			}
		}
		return $vars;
	}
	
}

/* End of file Fuel_sitevars.php */
/* Location: ./modules/fuel/libraries/Fuel_sitevars.php */