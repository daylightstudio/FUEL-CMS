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
 * FUEL pages 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_backup
 */

// --------------------------------------------------------------------

// include base library class to extend
require_once('Fuel_base_library.php');

class Fuel_backup extends Fuel_base_library {
	
	function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	function initialize($params)
	{
		parent::initialize($params);
	}
	
}



/* End of file Fuel_backup.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_backup.php */