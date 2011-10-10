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
 * FUEL cache object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_console
 */

// --------------------------------------------------------------------

class Fuel_base_library {
	
	protected $CI;
	protected $fuel;
	
	function __construct($params = array())
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
		
		if (count($params) > 0)
		{
			$this->initialize($params);
		}
		
	}
	
	function initialize($params = array())
	{
		$this->set_params($params);
	}
	
	function set_params($params)
	{
		foreach ($params as $key => $val)
		{
			if (isset($this->$key) AND substr($key, 0, 1) != '_')
			{
				$this->$key = $val;
			}
		}
	}
}

/* End of file Fuel_base_library.php */
/* Location: ./modules/fuel/libraries/Fuel_base_library.php */