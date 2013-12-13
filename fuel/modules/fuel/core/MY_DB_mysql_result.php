<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Extends the MySQL result object to allow for associative arrays
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/my_db_mysql_result
 */

require_once(BASEPATH.'database/drivers/mysql/mysql_result'.EXT);

class MY_DB_mysql_result extends CI_DB_mysql_result {
	
	protected $result_assoc_array	= array();
	protected $result_assoc	= array();
	
	// --------------------------------------------------------------------

	/**
	 * Returns an associative array with an array for rows
	 *
	 * @access	public
	 * @param	string	field name to use as associative key
	 * @return	void
	 */
	public function result_assoc_array($key)
	{
		if (count($this->result_assoc_array) > 0)
		{
			return $this->result_assoc_array;
		}

		// In the event that query caching is on the result_id variable 
		// will return FALSE since there isn't a valid SQL resource so 
		// we'll simply return an empty array.
		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->_data_seek(0);
		
		while ($row = $this->_fetch_assoc())
		{
			if (count($row) == 1)
			{
				$this->result_assoc_array[$row[$key]] = $row[$key];
			} 
			else if (count($row) == 2)
			{
				$this->result_assoc_array[$row[$key]] = next($row);
			}
			else
			{
				$this->result_assoc_array[$row[$key]] = $row;
			}
		}
		return $this->result_assoc_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an associative array with objects for rows
	 *
	 * @access	public
	 * @param	string	field name to use as associative key
	 * @return	void
	 */
	public function result_assoc($key)
	{
		if (count($this->result_assoc) > 0)
		{
			return $this->result_assoc;
		}

		// In the event that query caching is on the result_id variable 
		// will return FALSE since there isn't a valid SQL resource so 
		// we'll simply return an empty array.
		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->_data_seek(0);
		while ($row = $this->_fetch_object())
		{
			$row_arr = get_object_vars($row);
			if (count($row_arr) == 1)
			{
				$this->result_assoc[$row->$key] = $row->$key;
			} 
			else if (count($row_arr) == 2)
			{
				$this->result_assoc[$row->$key] = next($row_arr);
			}
			else
			{
				$this->result_assoc[$row->$key] = $row;
			}
		}
		return $this->result_assoc;
	}
}
/* End of file MY_DB_mysql_result.php */
/* Location: ./application/libraries/MY_DB_mysql_result.php */