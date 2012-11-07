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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * An extension of the Model class to map data operations to a table
 * Depends upon the Validator library, date helper and the string helper
 * 
 * Inspired from this post here Developer13:
 * http://codeigniter.com/forums/viewthread/88769/
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/my_model
 */


require_once(APPPATH.'libraries/Validator.php');

class MY_Model extends CI_Model {
	
	public $auto_validate = TRUE; // use auto-validation before saving
	public $return_method = 'auto'; // object, array, query, auto
	public $auto_validate_fields = array(
		'email|email_address' => 'valid_email',
		'phone|phone_number' => 'valid_phone'
		); // fields to auto validate
	public $required = array(); // required fields
	public $default_required_message = "Please fill out the required field '%1s'"; // the default required validator message
	public $auto_date_add = array('date_added', 'entry_date'); // field names to automatically set the date when the value is NULL
	public $auto_date_update = array('last_modified', 'last_updated'); // field names to automatically set the date on updates
	public $date_use_gmt = FALSE; // datetime method
	public $default_date = 0; // default date value that get's passed to the model on save. Using 0000-00-00 will not work if it is a required field since it is not seen as an empty value
	public $auto_trim = TRUE; // will trim on clean
	public $auto_encode_entities = TRUE; // automatically encode html entities 
	public $xss_clean = FALSE; // automatically run the xss_clean
	public $readonly = FALSE; // sets the model to readonly mode where you can't save or delete data'
	public $hidden_fields = array(); // fields to hide when creating a form
	public $unique_fields = array(); // fields that are not IDs but are unique
	public $linked_fields = array(); // fields that are are linked. Key is the field, value is a function name to transform it
	public $foreign_keys = array(); // map foreign keys to table models
	
	protected $db; // CI database object
	protected $table_name; // the table name to associate the model with
	protected $key_field = 'id'; // usually the tables primary key(s)... can be an array if compound key
	protected $normalized_save_data = NULL; // the saved data before it is cleaned
	protected $cleaned_data = NULL; // data after it is cleaned
	protected $dsn = ''; // the DSN string to connect to the database... if blank it will pull in from database config file
	protected $has_auto_increment = TRUE; // does the table have auto_increment?
	protected $suffix = '_model'; // the suffix used for the data record class
	protected $record_class = ''; // the name of the record class (if it can't be determined)
	protected $rules = array(); // validation rules
	protected $fields = array(); // fields in the table
	protected $use_common_query = TRUE; // include the _common_query method for each query
	protected $validator = NULL; // the validator object
	
	/**
	 * Constructor - Sets MY_Model preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($table = NULL, $params = array())
	{
		parent::__construct();
		
		// load helpers here 
		$this->load->helper('string');
		$this->load->helper('date');
		$this->load->helper('security');
		$this->load->helper('language');

		$this->initialize($table, $params);
    }

	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($table = NULL, $params = array())
	{
		if (!empty($table))
		{
			$this->table_name = $table;
		} 
		else 
		{
	        $this->table_name = strtolower(get_class($this));
		}
		
		if (!empty($params))
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
		
		// if a DSN property is set,then we will load that database in
		if (!empty($this->dsn))
		{
			$this->db = $this->load->database($this->dsn, TRUE, TRUE);
		}
		else
		{
			// else we use the database set on the CI object
			if (empty($this->db))
			{
				$this->load->database($this->dsn);
			}
			$CI =& get_instance();
			if (isset($CI->db))
			{
				// create a copy of the DB object to prevent cross model interference
				unset($this->db);
				$this->db = clone $CI->db;
			}
			else
			{
				$CI->load->language('db');
				show_error(lang('db_unable_to_connect'));
			}
		}
		$this->validator = new Validator();
		$this->validator->register_to_global_errors = FALSE;

	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the database object
	 *
	 * @access	public
	 * @return	array
	 */	
	public function &db()
	{
// 		$this->_check_readonly();
		return $this->db;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Gets the short name minus the suffix
	 *
	 * @access	public
	 * @return	array
	 */	
	public function short_name($lower = FALSE, $record_class = FALSE)
	{
		$class_name = ($record_class) ? $this->record_class_name() : get_class($this);
		$end_index = strlen($class_name) - strlen($this->suffix);
		$short_name = substr($class_name, 0, $end_index);
		if ($lower)
		{
			return strtolower($short_name);
		}
		else
		{
			return $short_name;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the table name
	 *
	 * @access	public
	 * @return	array
	 */	
	public function table_name()
	{
		return $this->table_name;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the key field(s)
	 *
	 * @access	public
	 * @return	array
	 */	
	public function key_field()
	{
		return $this->key_field;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the fields of the table
	 *
	 * @access	public
	 * @return	array
	 */	
	public function fields()
	{
		if (empty($this->fields)) $this->fields = $this->db->list_fields($this->table_name);
		return $this->fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the results of the query
	 *
	 * @access	public
	 * @param	boolean	return multiple records
	 * @param	string	method return type (object, array, query, auto)
	 * @param	string	the column to use for an associative key array
	 * @return	array
	 */	
	public function get($force_array = TRUE, $return_method = NULL, $assoc_key = NULL, $use_common_query = NULL){

		if (!empty($this->return_method) AND empty($return_method)) $return_method = $this->return_method;
		//$this->fields();
		
		if (!isset($use_common_query)) $use_common_query =  $this->use_common_query;
		
		// common query if exists
		if (method_exists($this, '_common_query') AND $use_common_query)
		{
			$this->_common_query();
		}
		
		if (empty($this->db->ar_select))
		{
			$this->db->select($this->table_name.'.*'); // make select table specific
		}
		
		//Get the data out of the database
		$query = $this->db->get($this->table_name);
		
		if (empty($query)) $query = new MY_DB_mysql_result();
		
		if ($this->return_method == 'query') 
		{
			return $query;
		}
		
		if ($return_method == 'array' OR !class_exists($this->record_class_name()))
		{
			if ($return_method == 'object')
			{
				$result_objects = (!empty($assoc_key)) ? $query->result_assoc($assoc_key) : $query->result() ;
			}
			else
			{
				$result_objects = (!empty($assoc_key)) ? $query->result_assoc_array($assoc_key) : $query->result_array();
			}
			$query->free_result();
			return new Data_set($result_objects, $force_array);
		}
		
		//This array holds all result data
		$result_objects = $this->map_query_records($query, $assoc_key);

		$query->free_result();
		$this->last_data_set = new Data_set($result_objects, $force_array);
		return $this->last_data_set;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Maps a query result object to an array of record objects
	 *
	 * @access	public
	 * @param	object	the query object
	 * @param	string	the field name to be used the key value
	 * @return	array
	 */	
	function map_query_records($query, $assoc_key = NULL)
	{
		$result_objects = array();
		
		$fields = $query->list_fields();
		foreach ($query->result_array() as $row) 
		{
			$record = $this->map_to_record_class($row, $fields);
			if (!empty($assoc_key))
			{
				$result_objects[$row[$assoc_key]] = $record;
			}
			else
			{
				$result_objects[] = $record;
			}
		}
		return $result_objects;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Maps an associative record array to a record object
	 *
	 * @access	public
	 * @param	array	field values
	 * @param	array	all the fields available for the object
	 * @return	array
	 */	
	function map_to_record_class($row, $fields = NULL)
	{
		if (empty($fields))
		{
			$fields = array_keys($row);
		}
		$record_class = $this->record_class_name();
		$record = new $record_class();
		$record->initialize($this, $fields);
		$record->fill($row);
		return $record;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get one record result
	 *
	 * @access	public
	 * @param	string	the key value to find a single record
	 * @param	mixed	return type (object, array, query, auto)
	 * @return	array
	 */	
	public function find_by_key($key_val, $return_method = NULL)
	{
		$where = array();
		if (is_array($key_val))
		{
			$key_field = (array) $this->key_field;
			foreach($key_field as $val)
			{
				if (is_array($key_val))
				{
					foreach($key_val as $key2 => $val2)
					{
						if ($key2 == $val)
						{
							$where[$val] = $val2;
						}
					}
				}
			}
		}
		else
		{
			$where[$this->table_name.'.'.$this->key_field] = $key_val;
		}
		return $this->find_one($where, NULL, $return_method);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get one record result
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query
	 * @param	string	return type (object, array, query, auto)
	 * @return	array
	 */	
	public function find_one($where = array(), $order_by = NULL, $return_method = NULL)
	{
		$where = $this->_safe_where($where);
		if (!empty($where)) $this->db->where($where);
		if (!empty($order_by)) $this->db->order_by($order_by);
		$this->db->limit(1);
		$query = $this->get(FALSE, $return_method);
		if ($return_method == 'query') return $query;
		return $query->result();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get one record result as an array
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query
	 * @return	array
	 */	
	public function find_one_array($where, $order_by = NULL)
	{
		return $this->find_one($where, $order_by, 'array');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the results of the query
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query
	 * @param	int		the number of records to limit in the results
	 * @param	int		the offset value for the results
	 * @param	string	return type (object, array, query, auto)
	 * @param	string	the column to use for an associative key array
	 * @return	array
	 */	
	public function find_all($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$where = $this->_safe_where($where);
		$params = array('where', 'order_by', 'limit', 'offset');
		foreach($params as $method)
		{
			if (!empty($$method)) $this->db->$method($$method);
		}
		$query = $this->get(TRUE, $return_method, $assoc_key);
		if ($return_method == 'query') return $query;
		return $query->result();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the results of the query as an array
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query
	 * @param	int		the number of records to limit in the results
	 * @param	int		the offset value for the results
	 * @return	array
	 */	
	public function find_all_array($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->find_all($where, $order_by, $limit, $offset, 'array');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the results of the query returned as a keyed array of objects
	 *
	 * @access	public
	 * @param	string	the column to use for an associative key array
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query
	 * @param	int		the number of records to limit in the results
	 * @param	int		the offset value for the results
	 * @return	array
	 */	
	public function find_all_assoc($assoc_key = 'id', $where = array(), $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->find_all($where, $order_by, $limit, $offset, 'object', $assoc_key);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the results of the query returned as a keyed array of arrays
	 *
	 * @access	public
	 * @param	string	the column to use for an associative key array
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query
	 * @param	int		the number of records to limit in the results
	 * @param	int		the offset value for the results
	 * @return	array
	 */	
	public function find_all_array_assoc($assoc_key = 'id', $where = array(), $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->find_all($where, $order_by, $limit, $offset, 'array', $assoc_key);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Basic query method. For more advanced, use CI Active Record
	 *
	 * @access	public
	 * @param	array	an array of parameters to create a query
	 * @return	array
	 */	
	public function query($params = array())
	{
		if (is_array($params)){

			$defaults = array(
				'select' => $this->table_name.'.*',
				//'select' => '*',
				'from' => $this->table_name,
				'join' => array(),
				'where' => array(),
				'or_where' => array(),
				'where_in' => array(),
				'or_where_in' => array(),
				'where_not_in' => array(),
				'or_where_not_in' => array(),
				'like' => array(),
				'or_like' => array(),
				'not_like' => array(),
				'or_not_like' => array(),
				'group_by' => NULL,
				'order_by' => NULL,
				'limit' => NULL,
				'offset' => NULL
			);

			$defaults2 = array(
				'join',
				'from',
				'where',
				'or_where',
				'where_in',
				'or_where_in',
				'where_not_in',
				'or_where_not_in',
				'like',
				'or_like',
				'not_like',
				'or_not_like'
				);

			// merge params with defaults
			$params = array_merge($defaults, $params);
			
			// add joins
			if (!empty($params['join'][0]))
			{
				$join_select = '';
				if (is_array($params['join'][0]))
				{
					foreach($params['join'] as $join){
						$this->db->join($join[0], $join[1], $join[2]);
						if ($join[3]) $join_select .= ', '.$this->db->safe_select($join[0]);
					}
				} 
				else 
				{
					$this->db->join($params['join'][0], $params['join'][1], $params['join'][2]);
					if ($params['join'][3]) $join_select .= ', '.$this->db->safe_select($params['join'][0]);
				}

				//if (empty($params['select'])) $params['select'] = $join_select;
				$params['select'] = $params['select'].$join_select;
			}

			// select
			if (!empty($params['select'])) $this->db->select($params['select'], FALSE);
			if (!empty($params['join_select'])) $this->db->select($join_select, FALSE);

			// from
			if ($params['from'] != $this->table_name)
			{
				$this->db->from($params['from']);
			}

			// loop through list above to set params
			foreach($defaults2 as $val)
			{
				if ($val == 'where_in' OR $val == 'or_where_in' OR $val == 'where_not_in' OR $val == 'or_where_not_in')
				{
					foreach($params[$val] as $key => $val2)
					{
						$this->db->$val($key, $val2);
					}
				}
				else if ($val != 'join' AND $val != 'from' AND $val != 'order_by')
				{
					if (!empty($params[$val])) 
					{
						if (is_array($params[$val]))
						{
							$this->db->$val($params[$val]);
						}
						else
						{
							$this->db->$val($params[$val]);
						}
					}
				}
			}

			// group by
			if (!empty($params['group_by'])) $this->db->group_by($params['group_by']);

			//order by
			if (!empty($params['order_by'])) 
			{
				if (is_array($params['order_by'])) 
				{
					foreach($params['order_by'] as $val) 
					{
						$order_by = explode(' ', trim($val));
						$this->db->order_by($order_by[0], $order_by[1]);
					}
				} 
				else 
				{
					$this->db->order_by($params['order_by']);
				}
			}
			if (method_exists($this, '_common_query'))
			{
				$this->_common_query();
			}
			
			$this->db->limit($params['limit']);
			$this->db->offset($params['offset']);
			$results = $this->get();
		} 
		else 
		{
			if (method_exists($this, '_common_query'))
			{
				$this->_common_query();
			}
			$results = $this->get();
		}
		return $results;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the results of a query as an associative array... good for form option lists
	 *
	 * @access	public
	 * @param	string	the column to use for the value
	 * @param	string	the column to use for the label
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query. defaults to $val asc
	 * @return	array
	 */	
	public function options_list($key = NULL, $val = NULL, $where = array(), $order = TRUE)
	{
		if (empty($key))
		{
			if (!is_array($this->key_field))
			{
				$key = $this->key_field;
			}
		}
		if (empty($val))
		{
			$fields = $this->fields();
			$val =$fields[1];
		}
		
		// don't need extra model sql stuff so just use normal active record'
		if (!empty($order) AND is_bool($order))
		{
			$this->db->order_by($val, 'asc');
		} 
		else if (!empty($order) AND is_string($order))
		{
			if (strpos($order, ' ') === FALSE) $order .= ' asc';
			$this->db->order_by($order);
		}
		$this->db->select($key.', '.$val, FALSE);
		$query = $this->db->get_where($this->table_name, $where);
		
		$key_arr = explode('.', $key);
		$clean_key = $key_arr[(count($key_arr) - 1)];
		if (!empty($query))
		{
			$results = $query->result_assoc_array($clean_key);
			return $results;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determine if a record exists in the database
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @return	boolean
	 */	
	public function record_exists($where)
	{
		$query = $this->db->get_where($this->table_name, $where);
		return ($query->num_rows() != 0);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create a new record if a custom record object exists
	 *
	 * @access	public
	 * @param	mixed	the record oject associated with this class
	 * @return	boolean
	 */	
	public function create($values = array())
	{
		$record_class = $this->record_class_name();
		if (class_exists($record_class))
		{
			$record = new $record_class();
			$record->initialize($this, $this->table_info());
			if (!empty($values)) $record->fill($values);
			return $record;
		}
		else
		{
			return NULL;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clean the data before saving
	 *
	 * @access	public
	 * @param	mixed	an array of values to be saved
	 * @return	array
	 */	
	public function clean($values = array())
	{
		if (empty($values)) $values = $_POST;
		// get table information to clean against
		$fields = $this->table_info();

		$clean = array();
		
		foreach($fields as $key => $val)
		{
			if (isset($values[$key]))
			{
				$values[$key] = ($this->auto_trim) ? trim($values[$key]) : $values[$key];
			}
		}

		// process linked fields
		$values = $this->process_linked($values);
		
		foreach ($fields as $key => $field)
		{
			if  ($field['type'] == 'time')
			{
				if (isset($values[$key.'_hour']) AND is_numeric($values[$key.'_hour']))
				{
					if (empty($values[$key]) OR (int)$values[$key] == 0) $values[$key] = $date_func('H:i:s');
					//the js seem like only supply minute field, assign 00 for sec now
					if (empty($values[$key.'_sec']))$values[$key.'_sec'] = '00';
					$values[$key] = date("H:i:s", strtotime(@$values[$key.'_hour'].':'.@$values[$key.'_min'].':'.@$values[$key.'_sec'].' '.@$values[$key.'_am_pm']));
				}
			}
			// make it easier for dates
			else if ($field['type'] == 'datetime')
			{
				
				if (empty($values[$key]) OR (int)$values[$key] == 0) $values[$key] = $this->default_date;
				if (isset($values[$key.'_hour']))
				{
					if (!empty($values[$key]))
					{
						$values[$key] = english_date_to_db_format($values[$key], @$values[$key.'_hour'], @$values[$key.'_min'], @$values[$key.'_sec'], @$values[$key.'_am_pm']);
					}
					
				}
			}
			else if ($field['type'] == 'date')
			{
				if (empty($values[$key]) OR (int)$values[$key] == 0) $values[$key] = $this->default_date;
				if (!empty($values[$key]) AND !is_date_db_format($values[$key])) $values[$key] = english_date_to_db_format($values[$key]);
			}
			
			$date_func = ($this->date_use_gmt) ? 'gmdate' : 'date';

			// create dates for date added and last updated fields automatically
			if (($field['type'] == 'datetime' OR $field['type'] == 'timestamp' OR $field['type'] == 'date') AND in_array($key, $this->auto_date_add))
			{
				
				$test_date = (isset($values[$key])) ? (int) $values[$key] : 0;

				// if no key field then we assume it is a new save and so we add the date if it's empty'
				if (!$this->_has_key_field_value($values) AND empty($test_date))
				{
					$values[$key] = ($field['type'] == 'date') ? $date_func('Y-m-d') : $date_func('Y-m-d H:i:s');
				}
			} 
			else if (($field['type'] == 'datetime' OR $field['type'] == 'timestamp' OR $field['type'] == 'date') AND in_array($key, $this->auto_date_update))
			{
				$values[$key] = ($field['type'] == 'date') ? $date_func('Y-m-d') : $date_func('Y-m-d H:i:s');
			} 
			if (isset($values[$key]))
			{
				
				// format dates
				if (!in_array($key, $this->auto_date_add))
				{	
					if ($field['type'] == 'datetime' OR $field['type'] == 'timestamp' OR $field['type'] == 'date')
					{
						if (isset($values[$key]) AND strncmp($values[$key], '0000', 4) !== 0)
						{
							if ($field['type'] == 'date')
							{
								$values[$key] = ($values[$key] != 'invalid') ? $date_func('Y-m-d', strtotime($values[$key])) : $this->default_date;
							}
							else
							{
								$values[$key] = ($values[$key] != 'invalid') ? $date_func('Y-m-d H:i:s', strtotime($values[$key])) : $this->default_date;
							}
						}
					} 
				}
				
				// safe_htmlspecialchars is buggy for unserialize so we use the cleanup_ms_word
				if ($this->auto_encode_entities)
				{
					if ((is_array($this->auto_encode_entities) AND in_array($key, $this->auto_encode_entities))
						OR (is_string($this->auto_encode_entities) AND $key == $this->auto_encode_entities)
						OR ($this->auto_encode_entities === TRUE)
					)
					{
						$values[$key] = safe_htmlentities($values[$key]);
					}
				}
				
				if ($this->xss_clean)
				{
					if ((is_array($this->xss_clean) AND in_array($key, $this->xss_clean))
						OR (is_string($this->xss_clean) AND $key == $this->xss_clean)
						OR ($this->xss_clean === TRUE)
					)
					{
						$values[$key] = xss_clean(($values[$key]));
					}
				}

				$clean[$key] = $values[$key];
			}
		}

		$this->cleaned_data = $clean;
		return $clean;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the cleaned data 
	 *
	 * @access	public
	 * @return	array
	 */	
	public function cleaned_data()
	{
		return $this->cleaned_data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns number of query results
	 *
	 * @access	public
	 * @param	mixed	where condition
	 * @return	int
	 */	
	public function record_count($where = array())
	{
		$this->db->where($where);
		$query = $this->db->get($this->table_name);
		return $query->num_rows();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns number of records in the table
	 *
	 * @access	public
	 * @return	int
	 */	
	public function total_record_count()
	{
		return $this->db->count_all($this->table_name);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns query results
	 *
	 * @access	public
	 * @param	int	limit part of query
	 * @param	int	offset part of query
	 * @return	array
	 */	
	public function paginate($per_page, $offset)
	{
		$this->db->limit($per_page);
		$this->db->offset($offset);
		return $this->find_all_array();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Save the data
	 *
	 * @access	public
	 * @param	mixed	an array or object to save to the database
	 * @param	boolean	validate the data before saving
	 * @param	boolean	ignore duplicate records on insert
	 * @return	mixed
	 */	
	public function save($record = NULL, $validate = TRUE, $ignore_on_insert = TRUE)
	{
		$this->_check_readonly();
		if (!isset($record)) $record = $_POST;
		if (is_array($record) AND is_array(current($record)))
		{
			$saved = TRUE;
			foreach($record as $rec)
			{
				if(!$this->save($rec))
				{
					$saved = FALSE;
				}
			}
			return $saved;
		}
		else
		{
			
			$fields = array();
			$values = $this->normalize_save_values($record);
			
			// reset validator here so that all validation set with hooks will not be lost
			$this->validator->reset();
			
			// run any prevalidation formatting... things you want to do to manipulate values before being validated
			$values = $this->on_before_clean($values);
			$values = $this->clean($values);
			$values = $this->on_before_validate($values);

			// if any errors are generated in the previous hooks then we return FALSE
			if ($this->get_errors())
			{
				return FALSE;
			}

			$validated = ($validate) ? $this->validate($values) : TRUE;
			
			if ($validated AND !empty($values))
			{
				// now clean the data to be ready for database saving
				$this->db->set($values);
				
				if ($ignore_on_insert)
				{
					// execute on_before_insert/update hook methods
					$values = $this->on_before_save($values);
					if (!$this->_has_key_field_value($values))
					{
						$values = $this->on_before_insert($values);
					}
					else
					{
						$values = $this->on_before_update($values);
					}
					
					$insert_key = ($this->has_auto_increment) ? $this->key_field : NULL;
					$this->db->insert_ignore($this->table_name, $values, $insert_key);

					// execute on_insert/update hook methods
					$no_key = FALSE;
					if (!$this->_has_key_field_value($values) AND $this->db->insert_id())
					{
						$no_key = TRUE;
						if (is_string($this->key_field))
						{
							$values[$this->key_field] = $this->db->insert_id();
						}
						$this->on_after_insert($values);
					}
					else
					{
						$this->on_after_update($values);
					}

					// execute on_insert/update hook methods on the Date_record model if exists
					if (is_object($record) AND is_a($record, 'Data_record'))
					{
						if ($no_key)
						{
							$record->on_after_insert($values);
						}
						else
						{
							$record->on_after_update($values);
						}
					}
				}
				else if(!$this->_has_key_field_value($values))
				{           	
					// execute on_before_insert/update hook methods
					$values = $this->on_before_save($values);
					$values = $this->on_before_insert($values);
					$this->db->insert($this->table_name, $values);
					if (is_string($this->key_field))
					{
						$values[$this->key_field] = $this->db->insert_id();
					}
					$this->on_after_insert($values);
					if (is_a($record, 'Data_record')) $record->on_after_insert($values);
				}
				else
				{
					$key_field = (array) $this->key_field;
					foreach($key_field as $key)
					{
						$this->db->where($key, $values[$key]);
					}
					
					$values = $this->on_before_save($values);
					$values = $this->on_before_update($values);
					$this->db->update($this->table_name, $values);
					$this->on_after_update($values);
					if (is_a($record, 'Data_record')) $record->on_after_update();
				}
			} 
			else
			{
				return FALSE;
			}
			
			if ($this->db->insert_id())
			{
				$return = $this->db->insert_id();
			}
			else
			{
				if (is_a($record, 'Data_record')) 
				{
					$key_field = $this->key_field;
					if (is_string($this->key_field))
					{
						$return = $record->$key_field;
					}
					else
					{
						$return = array();
						foreach($key_field as $key)
						{
							$return[$key] = $record->$key;
						}
					}
				} 
				else if (is_string($this->key_field) AND !empty($values[$this->key_field]))
				{
					$return = $values[$this->key_field];
				}
				else if (is_array($this->key_field))
				{
					$return = array();
					foreach($key_field as $key)
					{
						$return[$key] = $values[$key_field];
					}
				}
				else
				{
					$return = TRUE;
					// not valid test because a save could happen and no data is changed
					//return (bool)($this->db->affected_rows()) ? TRUE : FALSE;
				}
			}

			$this->on_after_save($values);
			return $return;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Save related data to a many to many table
	 *
	 * @access	public
	 * @param	mixed	an array or object to save to the database
	 * @param	array	key is the column name, and value is the value to save
	 * @param	array	key is the column name, and the array of data to iterate over and save
	 * @return	boolean
	 */	
	public function save_related($model, $key_field, $data)
	{
		$this->_check_readonly();
		
		$CI =& get_instance();
		$model = $this->load_model($model);

		$id = current($key_field);
		$key_field = key($key_field);

		$other_field = key($data);
		$data = current($data);
		
		// first remove all the articles
		$CI->$model->delete(array($key_field => $id));
		
		// then readd them
		$return = TRUE;
		foreach($data as $val)
		{
			$d = $CI->$model->create();
			$d->$key_field = $id;
			$d->$other_field = $val;
			if ($d->save())
			{
				$return = FALSE;
			}
		}
		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Insert data
	 *
	 * @access	public
	 * @param	mixed	an array or object to save to the database
	 * @return	boolean
	 */	
	public function insert($values)
	{
		$this->_check_readonly();
		$values = $this->on_before_insert($values);
		$return = $this->db->insert($this->table_name, $values);
		if (is_string($this->key_field))
		{
			$values[$this->key_field] = $this->db->insert_id();
		}
		$this->on_after_insert($values);
		return $return;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Update data
	 *
	 * @access	public
	 * @param	mixed	an array or object to save to the database
	 * @param	mixed	where condition
	 * @return	boolean
	 */	
	public function update($values, $where)
	{
		$this->_check_readonly();
		$values = $this->on_before_update($values);
		$this->db->where($where);
		$return = $this->db->update($this->table_name, $values);
		$this->on_after_update($values);
		return $return;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete data
	 *
	 * @access	public
	 * @param	mixed	where condition
	 * @return	void
	 */	
	public function delete($where)
	{
		$this->_check_readonly();
		
		$this->on_before_delete($where);

		if (is_object($where) AND $where instanceof Data_record) 
		{
			$obj = $where;
			$key_field = (array) $this->key_field;
			$where = array();
			foreach($key_field as $key)
			{
				$where[$key] = $obj->$key;
			}
			unset($obj);
		}
		$return = FALSE;
		$this->db->where($where);
		if ($this->is_valid())
		{
        	$return = $this->db->delete($this->table_name);
		}
		$this->on_after_delete($where);
		return $return;
    }

	// --------------------------------------------------------------------
	
	/**
	 * Delete related data... to be used in on_before/on_after delete hooks
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	mixed
	 * @return	boolean
	 */	
	public function delete_related($model, $key_field, $where)
	{
		$this->_check_readonly();
		
		// delete all has and belongs to many
		$CI =& get_instance();
		$id = $this->_determine_key_field_value($where);
		if (!empty($id))
		{
			$model = $this->load_model($model);
			return $CI->$model->delete(array($key_field => $id));
		}
		return FALSE;
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Truncates the table
	 *
	 * @access	public
	 * @return	void
	 */	
	public function truncate()
	{
		$this->_check_readonly();
		$this->db->truncate($this->table_name);
    }

	// --------------------------------------------------------------------
	
	/**
	 * Checks if a value is actually new
	 *
	 * @access	public
	 * @param	string	value to be checked
	 * @param	string	column name to check
	 * @return	array
	 */	
	function is_new($val, $key)
	{
		if (!isset($val)) return FALSE;
		$data = $this->find_one_array(array($key => $val));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks if a value is editable
	 *
	 * @access	public
	 * @param	string	value to be checked
	 * @param	string	column name to check
	 * @param	mixed	the key field value to check againsts
	 * @return	array
	 */	
	function is_editable($val, $key, $id)
	{
		if (!isset($val)) return FALSE;
		$data = $this->find_one_array(array($key => $val));
		
		// if no data then we are new and good
		if (empty($data)) return TRUE;

		$key_field = $this->key_field();
		
		// we are going to ignore multiple keys
		if (!empty($data) AND $data[$key_field] == $id) return TRUE;
		return FALSE;
	}
    
	// --------------------------------------------------------------------

	/**
	 * Validate the data before saving
	 *
	 * @access	public
	 * @param	mixed	object or array of values
	 * @return	array
	 */	
	public function validate($record)
	{
		$values = array();
		if (is_array($record))
		{
			$values = $record;
		} 
		else if (is_object($record)) 
		{
			if ($record instanceof Data_record)
			{
				$values = $record->values();
			}
			else
			{
				$values = get_object_vars($record);
			}
		}
		
		$required = array_merge($this->unique_fields, $this->required);

		// convert required fields to rules
		foreach($required as $key => $val)
		{
			if (is_int($key))
			{
				$field = $val;
				$msg = sprintf($this->default_required_message, str_replace('_', ' ', $val));
			}
			else
			{
				$field = $key;
				$msg = $val;
			}
			$this->rules[] = array($field, 'required', $msg);
		}
		
		// check required first
		foreach($this->rules as $rule)
		{
			if ($rule[1] == 'required')
			{
				foreach ($values as $key => $val)
				{
					if ($key == $rule[0])
					{
						// check required fields and make sure there is a value passed
						array_push($rule, $val); // add value to last parameter of rule
						break;
					}
				}
				call_user_func_array(array(&$this->validator, 'add_rule'), $rule);
			}
		}

		// check unique fields and add validation for them
		foreach($this->unique_fields as $field)
		{
			$has_key_field = $this->_has_key_field_value($values);
			$friendly_field = ucwords(str_replace('_', ' ', $field));
			if ($has_key_field)
			{
				$key_field = $this->key_field();
				if (!is_array($key_field))
				{
					$this->add_validation($field, array(&$this, 'is_editable'), lang('error_val_empty_or_already_exists', $friendly_field), array($field, $values[$key_field]));
				}
			}
			else
			{
				$this->add_validation($field, array(&$this, 'is_new'), lang('error_val_empty_or_already_exists', $friendly_field), $field);
			}
		}

		// run other validation in model if exists
		foreach ($values as $key => $val)
		{
			foreach($this->rules as $rule)
			{
				if ($key == $rule[0] AND $rule[1] != 'required')
				{
					$rule_val = (array) $val;
					if (empty($rule[3]))
					{
						$rule[3] = (!empty($values[$key])) ? array($values[$key]) : array();
					} 
					else if (!is_array($rule[3])) 
					{
						$rule[3] = array($rule[3]);
					}
					
					// now replace any placeholders for values
					foreach($rule[3] as $r_key => $r_val) 
					{
						if (strpos($r_val, '{') === 0)
						{
							$val_key = str_replace(array('{', '}'), '', $r_val);
							if (isset($values[$val_key])) $rule[3][$r_key] = $values[$val_key];
						}
					}
					$rule[3] = array_merge($rule_val, $rule[3]);
					call_user_func_array(array(&$this->validator, 'add_rule'), $rule);
				}
			}
		}

		// run the auto validation
		if ($this->auto_validate)
		{
			foreach ($this->fields() as $field)
			{
				if (isset($values[$field])) 
				{
					$this->auto_validate_field($field, $values[$field]);
				}
			}
		}
		$validated = ($this->validator->validate(array_keys($values)));
		
		return $validated;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the generic type of string, number, date, datetime, time, blob, enum
	 *
	 * @access	private
	 * @param	string	field
	 * @return	array
	 */	
	public function field_type($field)
	{
		$field_info = $this->field_info($field);
		
		switch($field_info['type'])
		{
			case 'var' : case 'varchar': case 'string': case 'tinytext': case 'text':  case 'longtext':
				return 'string';
				break;
			case 'int': case 'tinyint': case 'smallint': case 'mediumint': case 'float':  case 'double':  case 'decimal':
				return 'number';
				break;
			case 'datetime': case 'timestamp':
				return 'datetime';
				break;
			case 'date':
				return 'date';
				break;
			case 'year':
				return 'year';
				break;
			case 'time':
				return 'time';
				break;
			case 'blob': case 'mediumblob': case 'longblob':  case 'binary':
				return 'blob';
				break;
			case 'enum':
				return 'enum';
				break;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Automatically validate the data before saving based on the table meta info
	 *
	 * @access	private
	 * @param	string	field name
	 * @param	string	value of field
	 * @return	array
	 */	
	private function auto_validate_field($field, $value)
	{
		$CI =& get_instance();

		// set auto validation field rules
		foreach($this->auto_validate_fields as $key => $val)
		{
			if (!empty($value) AND preg_match("/".$key."/", $field))
			{
				$this->validator->add_rule($field, $val, lang('error_'.$val, $val), $value);
			}
		}
		
		// set auto validation based on field type
		$field_data = $this->db->field_info($this->table_name, $field);
		if (!empty($field_data) AND !empty($value)){
			$field_name = "'".(str_replace('_', ' ', $field))."'";
			$type = $this->field_type($field);
			
			switch($type)
			{
				case 'string':
					if (!empty($field_data['max_length'])) $this->validator->add_rule($field, 'length_max', lang('error_value_exceeds_length', $field_name), array($value, $field_data['max_length']));
					break;
				case 'number':
					$this->validator->add_rule($field, 'is_numeric', lang('error_not_number', $field_name), $value);
					if ($field_data['type'] != 'float') $this->validator->add_rule($field, 'length_max', lang('error_value_exceeds_length', $field_name), array($value, $field_data['max_length']));
					break;
				case 'date':
					if (strncmp($value, '0000', 4) !== 0)
					{
						$this->validator->add_rule($field, 'valid_date', lang('error_invalid_date', $field_name), $value);
						if ($field_data['type'] == 'datetime') $this->validator->add_rule($field, 'valid_time', lang('error_invalid_time', $field_name), $value);
					}
					break;
				case 'year':
					$reg_ex = (strlen(strval($value)) == 2) ? '\d{2}' : '\d{4}';
					$this->validator->add_rule($field, 'regex', lang('error_invalid_year', $field_name), array($value, $reg_ex));
					break;
				case 'enum':
					$options = (!empty($field_data['options'])) ? $field_data['options'] : $field_data['max_length'];
					$this->validator->add_rule($field, 'is_one_of_these', lang('error_invalid_generic', $field_name), array($value, $options)); // options get put into max_length field
			}
		}
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Is the data valid for saving
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_valid()
	{
		return $this->validator->is_valid();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a validation rule
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	mixed	function name OR array($object_instance, $method)
	 * @param	string	error message to display
	 * @return	void
	 */	
	public function add_validation($field, $rule, $msg)
	{
		$key = (is_array($rule)) ? $rule[1]: $rule;
		$this->rules[$field.'-'.$key] = func_get_args();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add an error to the validation to prevent saving
	 *
	 * @access	public
	 * @param	string	error message
	 * @param	string	key value of error message
	 * @return	void
	 */	
	public function add_error($msg, $key = NULL)
	{
		$this->validator->catch_error($msg, $key);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Remove an error of the validation
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	string	function name
	 * @return	array
	 */	
	public function remove_validation($field, $rule = NULL)
	{
		$key = (is_array($rule)) ? $rule[1]: $rule;
		unset($this->rules[$field.'-'.$key]);
		$this->validator->remove_rule($field, $rule);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a required field
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	void
	 */	
	public function add_required($field)
	{
		if (is_array($field))
		{
			foreach($field as $required)
			{
				if (!in_array($required, $this->required)) $this->required[] = $required;
			}
		}
		else
		{
			if (!in_array($field, $this->required)) $this->required[] = $field;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the validation object
	 *
	 * @access	public
	 * @return	object
	 */	
	public function &get_validation()
	{
		return $this->validator;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the validation to register to the global scope
	 *
	 * @access	public
	 * @return	object
	 */	
	public function register_to_global_errors($register)
	{
		$this->validator->register_to_global_errors = $register;
	}

	// --------------------------------------------------------------------

	/**
	 * Return validation errors
	 *
	 * @access	public
	 * @return	array
	 */	
	public function get_errors()
	{
		return $this->validator->get_errors();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Removes all the validation
	 *
	 * @access	public
	 * @return	void
	 */	
	public function remove_all_validation()
	{
		$this->validator->reset(TRUE);
		$this->rules = array();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of information about a field
	 *
	 * @access	public
	 * @return	array
	 */	
	public function field_info($field)
	{
		return $this->db->field_info($this->table_name, $field);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of table information that has column names as keys
	 *
	 * @access	public
	 * @return	array
	 */	
	public function table_info()
	{
		return $this->db->table_info($this->table_name);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of information that can be used for building a form (e.g. Form_builder)
	 *
	 * @access	public
	 * @param	array	array of values to pass to the form fields
	 * @param	array	an array of info about related models that create multiselects
	 * @return	array
	 */	
	public function form_fields($values = array(), $related = array())
	{
		$CI =& get_instance();
		$fields = $this->table_info();
		foreach($fields as $key => $val) 
		{
			if (is_array($this->required))
			{
				$required = array();
				foreach($this->required as $req => $req_val)
				{
					if (is_string($req))
					{
						$required[] = $req;
					}
					else
					{
						$required[] = $req_val;
					}
				}
			}
			$fields[$key]['required'] = (in_array($key, $required)) ? TRUE : FALSE;
			
			// create options for enum values
			if ($val['type'] == 'enum')
			{
				if (is_array($val['options']))
				{
					$fields[$key]['options'] = array_combine($val['options'], $val['options']);
				}
			}
			
			// get lang value if one exists... check first for model specific then look for a generic model lang key
			// no longer needed because Form_builder does this
			// $lang_key = strtolower(get_class($this)).'_'.$key;
			// $ln = ($lang = lang($lang_key)) ? $lang : lang('form_label_'.$key);
			// if ($ln)
			// {
			// 	$fields[$key]['label'] = $ln;
			// }
		}
		
		// lookup unique fields and give them a required parameter
		if (!empty($this->unique_fields))
		{
			foreach($this->unique_fields as $val)
			{
				$fields[$val]['required'] = TRUE;
			}
		}
		
		// lookup foreign keys and make the selects by default
		if (!empty($this->foreign_keys))
		{
			foreach($this->foreign_keys as $key => $val)
			{
				$model = $this->load_model($val);

				$fields[$key]['type'] = 'select';
				$fields[$key]['options'] = $CI->$model->options_list();
				$fields[$key]['first_option'] = 'Select...';
				$fields[$key]['label'] = ucfirst(str_replace('_', ' ', $CI->$model->short_name(TRUE, TRUE)));
			}
		}
		
		// create related
		if (!empty($related))
		{
			$key_field = $this->key_field();
			if (is_string($key_field))
			{
				foreach($related as $key => $val)
				{
					// related  need to be loaded using slash syntax if model belongs in another module (e.g. my_module/my_model)
					$related_name = end(explode('/', $key));
					$related_model = $this->load_model($key.'_model');
					$related_model_name = $related_name.'_model';
					
					$lookup_name = end(explode('/', $val));
					$lookup_model = $this->load_model($val);

					$options = $CI->$related_model_name->options_list();
					$field_values = (!empty($values['id'])) ? array_keys($CI->$lookup_name->find_all_array_assoc($CI->$related_model_name->short_name(TRUE, TRUE).'_id', array($this->short_name(TRUE, TRUE).'_id' => $values[$key_field]))) : array();
					$fields[$key] = array('label' => ucfirst($related_name), 'type' => 'array', 'class' => 'add_edit '.$key, 'options' => $options, 'value' => $field_values, 'mode' => 'multi');
				}
			}
		}

		// set auto dates to display only be default
		$hidden_fields = array_merge($this->auto_date_add, $this->auto_date_update, $this->hidden_fields);
		foreach($hidden_fields as $f)
		{
			if (isset($fields[$f])) $fields[$f]['type'] = 'hidden';
		}
		return $fields;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the record class name
	 *
	 * @access	private
	 * @param	string	the table class name (not the record class)
	 * @return	string
	 */	
 	public function record_class_name()
 	{
 		$record_class = '';
		if (empty($this->record_class))
 		{
			$class_name = $this->short_name();
			$record_class = substr(ucfirst($class_name), 0, -1).$this->suffix;
 		}
 		else
 		{
			$record_class = ucfirst($this->record_class).$this->suffix;
 		}

		if (strtolower($record_class) != strtolower(get_class($this)))
		{
			
			return $record_class;
		}
		else
		{
			return NULL;
		}
 	}

	// --------------------------------------------------------------------

	/**
	 * Set the default return type
	 *
	 * @access	public
	 * @param	string	return type (object, array, query, auto)
	 * @return	void
	 */	
	public function set_return_method($return_method)
	{
		$this->return_method = $return_method;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the default return type
	 *
	 * @access	public
	 * @return	mixed
	 */	
	public function get_return_method()
	{
		return $this->return_method;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Print out the last data set result
	 *
	 * @access	public
	 * @return	mixed
	 */	
	public function debug_data()
	{
		if (!empty($this->last_data_set))
		{
			$this->last_data_set->debug();
		}
		else
		{
			echo 'Data empty';
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Debug the last query
	 *
	 * @access	public
	 * @return	void
	 */	
	public function debug_query()
	{
		$this->db->debug_query();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Normailze the data to be saved so that it becomes an array
	 *
	 * @access	public
	 * @param	mixed	array of values to be saved
	 * @return	array
	 */	
	public function normalize_save_values($record)
	{
		if (!isset($record)) $record = $_POST;
		if (is_object($record))
		{
			if (is_a($record, 'Data_record'))
			{
				$values = $record->values();
			}
			else
			{
				$values = get_object_vars($record);
			}
		}
		else
		{
			$values = (array) $record;
		}
		$this->normalized_save_data = $values;
		return $values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Process linked fields
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function process_linked($values)
	{
		
		// process linked fields
		foreach($this->linked_fields as $field => $func_val)
		{
			if (empty($values[$field]))
			{
				if (is_string($func_val) AND !empty($values[$func_val]))
				{
					// convenience for most common
					$values[$field] = url_title($values[$func_val], 'dash', TRUE);
				}
				else if (is_array($func_val))
				{
					$func = current($func_val);
					$val = key($func_val);
					
					if (!empty($values[$val]))
					{
						if (function_exists($func))
						{
							$values[$field] = call_user_func($func, $values[$val]);
						}
						else
						{
							$values[$field] = $values[$val];
						}
					}
				}
			}
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Placeholder hook - right before cleaning of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_before_clean($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right before validation of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_before_validate($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right before insertion of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_before_insert($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right after insertion of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	void
	 */	
	public function on_after_insert($values)
	{
		
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right before update of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_before_update($values)
	{
		return $values;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right after update of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	void
	 */	
	public function on_after_update($values)
	{
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right before saving of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_before_save($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right after saving of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_after_save($values)
	{
		return $values;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right before delete
	 *
	 * @access	public
	 * @param	array	where condition for deleting
	 * @return	void
	 */	
	public function on_before_delete($where)
	{
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - right after delete
	 *
	 * @access	public
	 * @param	array	where condition for deleting
	 * @return	void
	 */	
	public function on_after_delete($where)
	{
	}
	
	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - for right after before processing a post. To be used outside of 
	 * the saving process and must be called manually from your own code
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	void
	 */	
	public function on_before_post($values = array())
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - for right after posting. To be used outside of 
	 * the saving process and must be called manually from your own code
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	void
	 */	
	public function on_after_post($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Load another model
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function load_model($model)
	{
		$CI =& get_instance();
		if (is_array($model))
		{
			$module = key($model);
			$m = current($model);
			$CI->load->module_model($module, $m);
			return $m;
		}
		else
		{
			$CI->load->model($model);
			return $model;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create safe where query parameters to avoid column name conflicts in queries
	 *
	 * @access	protected
	 * @param	mixed	where condition
	 * @return	mixed
	 */	
	protected function _safe_where($where)
	{
		if (is_array($where))
		{
			$new_where = array();
			foreach($where as $key => $val)
			{
				$table_col = explode('.', $key);
				if (empty($table_col[1])) $key = $this->table_name.'.'.$key;
				$new_where[$key] = $val;
			}
			return $new_where;
		}
		return $where;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create safe where query parameters to avoid column name conflicts in queries
	 *
	 * @access	protected
	 * @param	mixed	where condition
	 * @return	mixed
	 */	
	protected function _check_readonly()
	{
		if ($this->readonly)
		{
			throw new Exception(lang('error_in_readonly_mode', strtolower(get_class($this))));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines if there is a key field value in the array of values
	 *
	 * @access	protected
	 * @param	array	values to be saved
	 * @return	boolean
	 */	
	protected function _has_key_field_value($values)
	{
		$key_field = (array) $this->key_field;
		$return = TRUE;
		foreach($key_field as $key)
		{
			if (empty($values[$key])) $return = FALSE;
		}
		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines the id value based on an array or record
	 *
	 * @access	protected
	 * @param	array	values to be saved
	 * @return	boolean
	 */	
	protected function _determine_key_field_value($where)
	{
		$id = NULL;
		$id_field = $this->key_field();
		if (is_string($id_field))
		{
			if (is_object($where) AND $where instanceof Data_record) 
			{
				$id = $where->$id_field;
			}
			else if (is_array($where) AND isset($where[$id_field]))
			{
				$id = $where[$id_field];
			}
			else if (!empty($id) AND is_int($id))
			{
				$id = $where;
			}
		}
		return $id;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * What to print when echoing out this object
	 *
	 * @access	public
	 * @return	string
	 */	
	public function __toString()
	{
		return $this->table_name;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magically create methods based on the following syntax
	 *
	 * find_all_by_{column1}_and_{column2}, find_one_by_{column1}_or_{column2}
	 *
	 * @access	protected
	 * @param	string	name of the method called
	 * @param	array	arguments sent to the method call
	 * @return	array
	 */	
	public function __call($name, $args)
	{
		$find_how_many = substr($name, 0, 8);
		$find_where = substr($name, 8);

		$find_and_or = preg_split("/_by_|(_and_)|(_or_)/", $find_where, -1, PREG_SPLIT_DELIM_CAPTURE);
		if (!empty($find_and_or) AND strncmp($name, 'find', 4) == 0)
		{
			$arg_index = 0;
			foreach($find_and_or as $key => $find)
			{
				if (empty($find) OR $find == '_and_')
				{
					$this->db->where(array($find_and_or[$key + 1] => $args[$arg_index]));
					$arg_index++;
				}
				else if ($find == '_or_')
				{
					$this->db->or_where(array($find_and_or[$key + 1] => $args[$arg_index]));
					$arg_index++;
				}
			}
			
			$force_array = ($find_how_many == 'find_all') ? TRUE : FALSE;
			$other_args = array_slice($args, count($find_and_or) -1);
			
			if (!empty($other_args[0])) $this->db->order_by($other_args[0]);
			if (!empty($other_args[1])) $this->db->limit($other_args[1]);
			if (!empty($other_args[1])) $this->db->offset($other_args[2]);
			return $this->get($force_array)->result();
		}
		throw new Exception(lang('error_method_does_not_exist', $name));
	}

}



// --------------------------------------------------------------------

/**
 * This class is a wrapper around the query results returned by the MY_Model class
 *
 */	
Class Data_set {
	
	private $results; // the results array
	private $force_array; // return one or many
	
	/**
	 * Constructor - requires a result set from MY_Model. 
	 * @param	array	result set
	 * @param	boolean	returns 1 or many
	 */
	public function __construct($result_array, $force_array = TRUE)
	{
		$this->results = $result_array;	
		$this->force_array = $force_array;	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the results of the query
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function result(){
		if(empty($this->results))
		{
			return array();
		}
		return (!$this->force_array) ? $this->results[0] : $this->results;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines if there is a key field value in the array of values
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	boolean
	 */	
	public function num_records(){
		if(empty($this->results))
		{
			return 0;
		}
		return count($this->results);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Debug data sets
	 *
	 * @access	public
	 * @return	void
	 */	
	public function debug()
	{
		echo '<pre>';
		if (isset($this->results[0]))
		{
			foreach($this->results as $key =>$data)
			{
				echo '['.$key.']';
				echo "\t  ".$data;
			}
		}
		else
		{
			echo $this->results;
			
		}
		echo '</pre>';
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Debug data sets
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __toString(){
		$str = '<pre>';
		$str .= $this->force_array;
		$str .= print_r($this->results, true);
		$str .= '</pre>';
		return $str;
	}
	
}



// --------------------------------------------------------------------

/**
 * This class can be extended to return custom record objects for MY_Model
 *
 */	
Class Data_record {

	protected $_CI; // global CI object
	protected $_db; // database object
	protected $_fields = array(); // fields of the record
	protected $_objs = array(); // nested objects
	protected $_parent_model; // the name of the parent model
	protected $_inited = FALSE;
	protected $_date_format = 'm/d/Y'; // datetime method format
	protected $_time_format = 'h:i:s a'; // datetime method format
	protected $_format_suffix = '_formatted'; // datetime method format
	
	/**
	 * Constructor - requires a result set from MY_Model. 
	 * @param	object	parent object
	 */
	public function __construct(&$parents = NULL)
	{
		if (!empty($parent)) $this->initialize($parent);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initializes the class with the parent model and field names
	 *
	 * @access	public
	 * @param	object	parent model object
	 * @param	array	field names
	 * @return	array
	 */	
	public function initialize(&$parent, $fields = array())
	{
		$this->_CI =& get_instance();
		$this->_CI->load->helper('typography');
		$this->_CI->load->helper('date');
		
		$this->_db = $this->_CI->db;
		$this->_parent_model = $parent;
		
		if (empty($this->_fields))
		{
			// auto create fields based on table
			foreach($fields as $key => $val)
			{
				if (is_array($val))
				{
					$this->_fields[$key] = $val['default'];
				}
				else
				{
					$this->_fields[$val] = NULL;
				}
			}
		}
		$class_vars = get_class_vars(get_class($this));
		$this->set_fields(array_merge($this->_fields, $class_vars));
		$this->on_init();
		$this->_inited = TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is this class initialized yet?
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_initialized()
	{
		return $this->_inited;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the fields of the oject ignoring those fields prefixed with an underscore
	 *
	 * @access	public
	 * @param	array	field names
	 * @return	void
	 */	
	public function set_fields($fields)
	{
		$filtered_fields = array();
		foreach($fields as $key => $val)
		{
			if (strncmp($key, '_', 1) !== 0)
			{
				$filtered_fields[$key] = $val;
			}
		}
		$this->_fields = $filtered_fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the id field name
	 *
	 * @access	public
	 * @param	array	field values
	 * @return	void
	 */	
	public function id()
	{
		return $this->_parent_model->key_field();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the values of the fields
	 *
	 * @access	public
	 * @param	array	field values
	 * @return	void
	 */	
	public function fill($values = array())
	{
		if (!is_array($values)) return FALSE;
		foreach($values as $key => $val)
		{
			if ($this->prop_exists($key)) $this->$key = $val;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the field values
	 *
	 * @access	public
	 * @return	array
	 */	
	public function values($include_derived = FALSE)
	{
		$values = array();
		$class_vars = get_class_vars(get_class($this));
		foreach(get_object_vars($this) as $key => $val)
		{
			if ((array_key_exists($key, $class_vars) AND strncmp($key, '_', 1) !== 0))
			{
				$values[$key] = $val;
			}
		}
		
		if ($include_derived)
		{
			$methods = get_class_methods($this);
			$reflection = new ReflectionClass(get_class($this));
			foreach($methods as $method)
			{
				if (strncmp($method, 'get_', 4) === 0 AND $reflection->getMethod($method)->getNumberOfParameters() == 0)
				{
					$key = substr($method, 4); // remove get_
					$values[$key] = $this->$method();
				}
			}
		}
		$values = array_merge($this->_fields, $values);
		return $values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Duplicates the object
	 *
	 * @access	public
	 * @return	object
	 */	
	public function duplicate()
	{
		$dup = $this->_parent_model->create();
		$dup->fill($this->values());
		
		// NULL out key values so as not to overwrite existing objects
		$key_field = (array) $this->_parent_model->key_field();
		foreach($key_field as $key)
		{
			$dup->$key = NULL;
		}
		return $dup;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the results of the query
	 *
	 * @access	public
	 * @param	boolean	validate before saving?
	 * @param	boolean	ignore on insert
	 * @return	boolean
	 */	
	public function save($validate = TRUE, $ignore_on_insert = TRUE)
	{
		return $this->_parent_model->save($this, $validate, $ignore_on_insert);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validates the values
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function validate()
	{
		return $this->_parent_model->validate($this);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is the data in the fields valid?
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_valid()
	{
		return $this->_parent_model->is_valid();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of errors if they exist
	 *
	 * @access	public
	 * @return	array
	 */	
	public function errors()
	{
		return $this->_parent_model->get_errors();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Deletes this object
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function delete()
	{
		return $this->_parent_model->delete($this);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Refreshes the data in this object from data in the database
	 *
	 * @access	public
	 * @return	void
	 */	
	public function refresh()
	{
		$key_field = (array) $this->_parent_model->key_field();
		foreach($key_field as $key)
		{
			$where[$key] = $this->$key;
		}
		
		$data = $this->_parent_model->find_one($where, NULL, 'array');
		$this->fill($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the results of the query
	 *
	 * @access	public
	 * @param	mixed	where conditions
	 * @param	string	model name
	 * @param	boolean	return 1 or many
	 * @param	string	the key to the _obj property to store the lazy loaded object
	 * @return	array
	 */	
	public function lazy_load($where, $model, $multiple = FALSE, $cache_key = '')
	{
		if ($cache_key == '') 
		{
			if (is_array($where))
			{
				$cache_key = implode('_', array_keys($where));
			}
			else
			{
				$cache_key = str_replace(' ', '_', $where).'_'.$model;
			}
		}
		if (!empty($this->_objs[$cache_key]) AND $cache_key === FALSE) return $this->_objs[$cache_key];
		
		$model = $this->_parent_model->load_model($model);
		
		if (is_array($model))
		{
			$module = key($model);
			$model = current($model);
			$this->_CI->load->module_model($module, $model);
		}
		else
		{
			$this->_CI->load->model($model);
		}
		
		
		// set the readonly to the callers
		$this->_CI->$model->readonly = $this->_parent_model->readonly;
		
		if ($multiple)
		{
			$this->_objs[$cache_key] = $this->_CI->$model->find_all($where);
		}
		else
		{
			if (is_string($where))
			{
				$this->_objs[$cache_key] = $this->_CI->$model->find_by_key($this->$where);
			}
			else
			{
				$this->_objs[$cache_key] = $this->_CI->$model->find_one($where);
			}
			
		}
		
		// create an empty object
		if (empty($this->_objs[$cache_key])) 
		{
			return FALSE;
		}
		return $this->_objs[$cache_key];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Determine if a proprty (field) exists
	 *
	 * @access	public
	 * @param	key	field names
	 * @return	boolean
	 */	
	public function prop_exists($key)
	{
		return (array_key_exists($key, $this->_fields) OR property_exists($this, $key));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Echos out the last query run by the parent model
	 *
	 * @access	public
	 * @param	object	parent model object
	 * @param	array	field names
	 * @return	array
	 */	
	public function debug_query()
	{
		$this->_parent_model->db->debug_query();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Echos out the data of this object
	 *
	 * @access	public
	 * @return	void
	 */	
	public function debug_data()
	{
		echo $this->__toString(); 
	}

	// --------------------------------------------------------------------
	
	/**
	 * Placeholder - executed after insertion of data
	 *
	 * @access	public
	 * @param	array	field values
	 * @return	void
	 */	
	public function on_after_insert($values)
	{
		$key_field = $this->_parent_model->key_field();
		if (is_string($key_field))
		{
			$this->_fields[$key_field] = $this->_db->insert_id();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Placeholder - executed after update of data
	 *
	 * @access	public
	 * @param	array	field values
	 * @return	void
	 */	
	public function on_after_update($values)
	{
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Placeholder - executed after initialization
	 *
	 * @access	public
	 * @return	vpod
	 */	
	public function on_init()
	{
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Placeholder - to execute before a magic method get
	 *
	 * @access	public
	 * @param	string	output from get comand
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function before_set($output, $var)
	{
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Placeholder - to execute after a magic method get
	 *
	 * @access	public
	 * @param	string	output from get comand
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function after_get($output, $var)
	{
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * String value of this object is it's values in an array format
	 *
	 * @access	public
	 * @return	string
	 */	
	public function __toString()
	{
		$str = '<pre>';
		$str .= print_r($this->values(), true);
		$str .= '</pre>';
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the results of the query
	 *
	 * @access	public
	 * @param	object	parent model object
	 * @param	array	field names
	 * @return	array
	 */	
	public function __call($method, $args )
	{
		if (preg_match( "/set_(.*)/", $method, $found))
		{
			if (array_key_exists($found[1], $this->_fields))
			{
				$this->_fields[$found[1]] = $args[0];
				return TRUE;
			}
		}
		else if (preg_match("/get_(.*)/", $method, $found))
		{
			if (array_key_exists($found[1], $this->_fields))
			{
				return $this->_fields[$found[1]];
			}
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method to set first property, method, then field values
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	mixed	
	 * @return	void
	 */	
	public function __set($var, $val)
	{
		$val = $this->before_set($val, $var);
		if (property_exists($this, $var))
		{
			$this->$var = $val;
		} 
		else if (method_exists($this, 'set_'.$var))
		{
			$set_method = "set_".$var;
			$this->$set_method($val);
		}
		else if (array_key_exists($var, $this->_fields))
		{
			$this->_fields[$var] = $val;
		}
		else
		{
			throw new Exception('property '.$var.' does not exist.');
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method to return first property, method, then field values 
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function __get($var){
		$output = NULL;
		$foreign_keys = $this->_parent_model->foreign_keys;
		
		// first class property has precedence
		if (property_exists($this, $var))
		{
			$output = $this->$var;
		} 
		// check a get_{method}
		else if (method_exists($this, "get_".$var))
		{
			$get_method = "get_".$var;
			$output = $this->$get_method();
		}
		// then look in foreign keys and lazy load (add _id from the end in search)
		else if (in_array($var.'_id', array_keys($foreign_keys)))
		{
			$var_key = $var.'_id';
			$model = $foreign_keys[$var_key];
			$output = $this->lazy_load($var_key, $model);
		}

		// finally check values from the database
		else if (array_key_exists($var, $this->_fields))
		{
			$output = $this->_fields[$var];
		}
		// formatted
		else if (substr($var, -10) == $this->_format_suffix)
		{
			$field = substr($var, 0, -10);
			$output = $this->_get_formatted($field);
		}
		$output = $this->after_get($output, $var);
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Method to auto format fields with the suffix $_format_suffix
	 *
	 * @access	private
	 * @param	string	field to check if it is set
	 * @return	string
	 */
	protected function _get_formatted($field)
	{
		$type = $this->_parent_model->field_type($field);
		$output = '';
		switch($type)
		{
			case 'string':
				$output = auto_typography($this->_fields[$field]);
				break;
			case 'datetime':
				$output = date($this->_date_format.' '.$this->_time_format, strtotime($this->_fields[$field]));
				break;
			case 'date':
				$output = date($this->_date_format, strtotime($this->_fields[$field]));
				break;
		}
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method to check if a variable is set
	 *
	 * @access	public
	 * @param	string	field to check if it is set
	 * @return	boolean
	 */	
	public function __isset($key)
	{
		$obj_vars = get_object_vars($this);
		return (isset($this->_fields[$key]) OR isset($obj_vars[$key]) OR method_exists($this, 'get_'.$key));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method for unset
	 *
	 * @access	public
	 * @param	string	field to delete
	 * @return	void
	 */	
	public function __unset($isset)
	{
		$obj_vars = get_object_vars($this);
		if (isset($this->_fields[$key]))
		{
			unset($this->_fields[$key]);
		} 
		else if(isset($obj_vars[$key]))
		{
			unset($this->$key);
		}
	}
	
	
}

/* End of file MY_Model.php */
/* Location: ./application/libraries/MY_Model.php */
