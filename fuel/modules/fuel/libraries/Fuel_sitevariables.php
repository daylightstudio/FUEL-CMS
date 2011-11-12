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
 * FUEL dashboard object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_sitevariables
 */

// --------------------------------------------------------------------

class Fuel_sitevariables extends Fuel_base_library {
	
	
	function get($location = NULL)
	{
		if (is_null($location))
		{
			$location = uri_path();
		}
		
		$this->fuel->load_model('sitevariables_model');
		
		$site_vars = $this->CI->sitevariables_model->find_all_array(array('active' => 'yes'));
		
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

/* End of file Fuel_sitevariables.php */
/* Location: ./modules/fuel/libraries/Fuel_sitevariables.php */