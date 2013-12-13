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
 */

// ------------------------------------------------------------------------

/**
 * Extends Base_module_model
 *
 * <strong>Fuel_logs_model</strong> is used for logging action in FUEL
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_logs_model
 */

require_once('base_module_model.php');

class Fuel_logs_model extends Base_module_model {

	private $_logs_table;

	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct($logs_table = 'fuel_logs')
	{
		parent::__construct($logs_table);
		$this->_logs_table = ($logs_table == 'fuel_logs') ? $this->_tables[$logs_table] : $logs_table;
		$this->filters = array('entry_date', $this->_tables['fuel_users'].'.first_name', $this->_tables['fuel_users'].'.last_name', 'message');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the log items
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data (optional)
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data (optional)
	 */	
	public function list_items($limit = NULL, $offset = NULL, $col = 'entry_date', $order = 'desc', $just_count = FALSE)
	{
		$this->db->select($this->_logs_table.'.id, entry_date, CONCAT('.$this->_tables['fuel_users'].'.first_name, " ", '.$this->_tables['fuel_users'].'.last_name) as name, message, type', FALSE);
		$this->db->join($this->_tables['fuel_users'], $this->_logs_table.'.user_id = '.$this->_tables['fuel_users'].'.id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns just the latest activity in the log
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @return	array of data
	 */	
	public function latest_activity($limit = NULL)
	{
		$this->db->where('type', 'info');
		return $this->list_items($limit);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Saves to the logging table
	 *
	 * @access	public
	 * @param	string The message to associate with the log
	 * @param	string The type of log (optional)
	 * @param	int The user ID associated with the log (optional)
	 * @return	boolean TRUE if saved correctly. FALSE otherwise
	 */	
	public function logit($msg, $type = NULL, $user_id = NULL)
	{
		$CI =& get_instance();
		if (!isset($user_id))
		{
			$user = $CI->fuel->auth->user_data();
			if (isset($user['id']))
			{
				$user_id = $user['id'];
			}
		}

		$save['message'] = $msg;
		$save['type'] = $type;
		$save['user_id'] = $user_id;
		$save['entry_date'] = datetime_now();
		return $this->save($save);
	}

}

class Fuel_log_model extends Base_module_record {
}