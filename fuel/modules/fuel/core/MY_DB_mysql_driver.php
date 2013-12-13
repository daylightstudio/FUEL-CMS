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
 * Extends the MySQL driver to add some extra goodness
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/my_db_mysql_driver
 */

class MY_DB_mysql_driver extends CI_DB_mysql_driver {
	
	protected $_table_info_cache = array();

	// --------------------------------------------------------------------

	/**
	 * Echos out the last query ran to the screen
	 *
	 * @access	public
	 * @param	boolean	will hide the echoed output in a comment
	 * @param	boolean	will exit the script
	 * @return	void
	 */
	public function debug_query($hidden = FALSE, $exit = FALSE)
	{
		if (!empty($hidden)) echo '<!--';
		echo $this->last_query()." \n";
		if (!empty($hidden)) echo '-->';
		if (!empty($exit)) exit;
	}

	// --------------------------------------------------------------------

	/**
	 * Load the result drivers. Overrides the CI_DB_mysql_driver driver
	 *
	 * @access	public
	 * @return	object
	 */
	public function load_rdriver()
	{
		$driver = 'MY_DB_mysql_result';

		if ( ! class_exists($driver))
		{
			include_once(BASEPATH.'database/DB_result'.EXT);
			include_once(APPPATH.'core/MY_DB_mysql_result'.EXT);
		}
		
		return $driver;
	}

	// --------------------------------------------------------------------

	/**
	 * Appends the table name to fields in a select that don't have it to prevent ambiguity
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	public function safe_select($table, $fields = NULL, $prefix = NULL)
	{
		if (empty($prefix)) $prefix = $table.'.';
		if (empty($fields)) {
			$fields = $this->field_data($table);
			$new_fields = array();
			foreach($fields as $key => $val)
			{
				$new_fields[$val->name] = get_object_vars($val);
			}
			$fields = $new_fields;
		}
		$select = '';
		if (!empty($fields))
		{
			foreach($fields as $key => $val)
			{
				$select .= $table.'.'.$key.' as \''.$prefix.$key.'\', ';
			}
			$select = substr($select, 0, -2); // remove trailing comma
		}
		return $select;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets an array of information about a particular table's field
	 *
	 * @access	public
	 * @param	string	name of table
	 * @param	string	field name
	 * @return	string
	 */
	public function field_info($table, $field)
	{
		$table_info = $this->table_info($table);
		if (isset($table_info[$field]))
		{
			return $table_info[$field];
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets an array of information about a table. Useful for generating forms
	 *
	 *	Original idea from http://codeigniter.com/forums/viewthread/46418/
	 *
	 * @access	public
	 * @param	string	name of table
	 * @param	string	field name
	 * @return	string
	 */
	public function table_info($table, $set_field_key = TRUE)
	{
		if (!empty($this->_table_info_cache[$table]) AND $set_field_key) return $this->_table_info_cache[$table]; // lazy load
		$sql = "SHOW FULL COLUMNS FROM ". $this->_escape_identifiers($table);
		$query = $this->query($sql);
		$retval = array();
		foreach ($query->result() as $field) 
		{
			/* Explanation of the ugly regex:
			**   match until first non '('
			**   then optionally match numbers '\d' inside brackets '\(', '\)
			*/
			//preg_match('/([^(]+)(\((\d+)\))?/', $field->Type, $matches);
			preg_match('/([^(]+)(\((.+)\))?/', $field->Type, $matches);
			
			$type           = sizeof($matches) > 1 ? $matches[1] : NULL;
			if (!empty($matches[3]) AND strpos($matches[3], ',') > 0){
				$matches[3] = str_replace("','", '|', $matches[3]); // convert enum divider to pipe in case there are commas in the enum values
				$matches[3] = str_replace("''", '^', $matches[3]); // convert single quotes to a different character
				$matches[3] = str_replace("'", '', $matches[3]);
				$enum_vals = str_replace("^", "'", $matches[3]); // convert single quotes back
				
				$enum_vals_arr = explode("|", $enum_vals);
				$max_length = $enum_vals_arr;
			}
			else
			{
				$max_length = sizeof($matches) > 3 ? $matches[3] : NULL;
			}
			$f = array();
			$f['name'] = $field->Field;
			$f['type'] = ($type == 'char' OR $type =='varchar') ? 'string' : $type;
			$f['default']     = $field->Default;
			if ($type == 'enum')
			{
				$f['options']	= $max_length;
				$f['max_length']  = NULL;
			}
			else
			{
				$f['options']		= NULL;
				$f['max_length']  = $max_length;
			}
			$f['primary_key'] = ($field->Key == "PRI") ? TRUE : FALSE;
			$f['comment']     = $field->Comment;
			$f['collation']   = $field->Collation;
			$f['extra']       = $field->Extra;
			$f['null']	 	= ($field->Null == "NO") ? FALSE : TRUE;

			if ($set_field_key)
			{
				$retval[$f['name']] = $f;
			} else {
				$retval[] = $f;
			}
			
		}
		$this->_table_info_cache[$table] = $retval;
		return $retval;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Save's information to the database using INSERT IGNORE syntax
	 *
	 * @access	public
	 * @param	string	name of table
	 * @param	array	values to save
	 * @param	mixed	primary key value(s)
	 * @return	string
	 */
	public function insert_ignore($table, $values, $primary_key = 'id')
	{
		if (empty($values)) return false;
		$sql = "INSERT IGNORE ";
		$sql .= "INTO ".$this->protect_identifiers($table)." (";
		
		foreach($values as $key => $val)
		{
			$sql .= $this->_escape_identifiers($key).", ";
		}
		$sql = substr($sql, 0, -2); // get rid of last comma
		
		$sql .= ") VALUES ";

		// handle multple
		if (is_array(next($values)))
		{
			foreach($values as $key => $val)
			{
				$sql .= '(';
				foreach($val as $key2 => $val2)
				{
					$sql .= $this->escape($val2).", ";
				}
				$sql = substr($sql, 0, -2); // get rid of last comma
				$sql .= '), ';
			}
			$sql = substr($sql, 0, -2); // get rid of last comma
		}
		else
		{
			$sql .= '(';
			foreach($values as $key => $val)
			{
				$sql .= $this->escape($val).", ";
			}
			$sql = substr($sql, 0, -2); // get rid of last comma
			$sql .= ')';
		}
		
		$sql .= ' ON DUPLICATE KEY UPDATE ';
		foreach($values as $key => $val)
		{
			if ((is_string($primary_key) AND $primary_key == $key) OR (is_array($primary_key) AND in_array($key, $primary_key)))
			{
				$sql .=  $this->_escape_identifiers($key).' = LAST_INSERT_ID('.$this->_escape_identifiers($key).'), ';
			}
			else
			{
				$sql .= $this->_escape_identifiers($key).' = VALUES('.$this->_escape_identifiers($key).'), ';
			}
		}
		$sql = substr($sql, 0, -2); // get rid of last comma

		//echo $sql.'<br/><br/>';
		$return = $this->query($sql);
		$this->_reset_write();
		
		$last_insert = $this->insert_id();
		if (!empty($last_insert)) return $last_insert;
		return $return;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Allows you to get the compiled active record string without running the query
	 *
	 * @access	public
	 * @param	boolean	clear the active record
	 * @return	string
	 */
	public function get_query_string($clear = TRUE)
	{
		$sql = $this->_compile_select();
		if ($clear)
		{
			$this->clear_query_string();
		}
		return $sql;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Clears the compiled query string
	 *
	 * @access	public
	 * @return	string
	 */
	public function clear_query_string()
	{
	   $this->_reset_select();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Loads a SQL string and executes it... good for bigger data dumps
	 *
	 * @access	public
	 * @param	string	The path to a SQL file
	 * @param	boolean	If the contents being passed in parameter 1 is a path or a SQL string
	 * @return	void
	 */
	public function load_sql($sql_path, $is_path = TRUE)
	{
		$CI =& get_instance();
		// check first to see if it is a path to a file
		if (file_exists($sql_path) AND $is_path)
		{
			$sql = file_get_contents($sql_path);
		}
		
		// if not, assume it is a string
		else
		{
			$sql = $sql_path;
		}
		
		$sql = preg_replace('#^/\*(.+)\*/$#U', '', $sql);
		$sql = preg_replace('/^#(.+)$/U', '', $sql);
		
		// load database config
		include(APPPATH.'config/database.php');
		$CI->load->database();
	
		// select the database
		$db = $db[$active_group]['database'];
		
		$use_sql = 'USE '.$db;
		
		$CI->db->query($use_sql);
		$sql_arr = explode(";\n", $sql);
		foreach($sql_arr as $s)
		{
			$s = trim($s);
			if (!empty($s))
			{
				$CI->db->query($s);
			}
		}
	}
}
/* End of file MY_DB_mysql_driver.php */
/* Location: ./application/libraries/MY_DB_mysql_driver.php */