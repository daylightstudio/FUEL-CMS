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
 * Extends MY_Model
 *
 * <strong>Fuel_archives_model</strong> is used for archiving saved data that can be rolled back in the admin.
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_archives_model
 */
class Fuel_archives_model extends MY_Model {
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		$CI =& get_instance();
		$CI->config->module_load(FUEL_FOLDER, 'fuel', TRUE);
		$tables = $CI->config->item('tables', 'fuel');
		parent::__construct($tables['fuel_archives']);
	}
	
	/**
	 * Returns an option list of saved archives based on a record ID and table name
	 *
	 * @access	public
	 * @param	string	The record ID
	 * @param	string	The table name
	 * @param	boolean	Determines whether to include the currently active record in the archive list (optional)
	 * @param	boolean	Order by for options list (optional)
	 * @return	array Key/value array with the key being the archive ID value
	 */
	public function options_list($ref_id = NULL, $table_name = NULL, $include_current = array(), $order_by = TRUE)
	{
		if ($order_by === TRUE)
		{
			$order_by = 'version_timestamp desc';
		}
		$CI =& get_instance();
		$CI->load->helper('date');
		$options = $this->find_all_array(array('ref_id' => $ref_id, 'table_name' => $table_name), $order_by);
		$return = array();
		$i = 0;
		foreach($options as $val)
		{
			if ($i == 0 && !empty($include_current))
			{
				$return[$val['version']] = 'Current Version';
			}
			else
			{
				$return[$val['version']] = 'Version '.$val['version'].' - '.english_date($val['version_timestamp'], true);
			}
			$i++;
		}
		return $return;
	}

}

class Fuel_archive_model extends Data_record {
}
