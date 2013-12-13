<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
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
 */

// ------------------------------------------------------------------------

/**
 * Extends Base_module_model
 *
 * <strong>Fuel_navigation_groups_model</strong> is used for managing FUEL users in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_navigation_groups_model
 */

require_once('base_module_model.php');

class Fuel_navigation_groups_model extends Base_module_model {
	
	public $unique_fields = array('name'); // The name field is unique
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		$CI =& get_instance();
		$tables = $CI->config->item('tables', 'fuel');
		parent::__construct($tables['fuel_navigation_groups']);
		$this->add_validation('name', array(&$this, 'valid_name'), lang('error_requires_string_value'));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validation callback function. Navigation group names must not be numeric.
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */	
	public function valid_name($name)
	{
		return (!is_numeric($name));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Cleanup navigation items if group is deleted
	 *
	 * @access	public
	 * @param	mixed The where condition for the delete
	 * @return	void
	 */	
	 public function on_after_delete($where)
	 {
		$this->delete_related(array(FUEL_FOLDER => 'fuel_navigation_model'), 'group_id', $where);
	 }
}

class Fuel_navigation_group_model extends Base_module_record {
}
