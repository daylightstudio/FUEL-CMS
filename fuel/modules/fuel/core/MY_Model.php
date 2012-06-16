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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * MY_Model class
 * 
 * An extension of the Model class to map data operations to a table.
 * Depends upon the Validator library, date helper and the string helper.
 * 
 * Inspired from this post here Developer13:
 * http://codeigniter.com/forums/viewthread/88769/
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/my_model
 * @prefix		$this->example_model->
 */


require_once(FUEL_PATH.'libraries/Validator.php');

class MY_Model extends CI_Model {
	
	public $auto_validate = TRUE; // use auto-validation before saving
	public $return_method = 'auto'; // object, array, query, auto
	
	 // fields to auto validate
	public $auto_validate_fields = array(
		'email|email_address' => 'valid_email',
		'phone|phone_number' => 'valid_phone'
		);
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
	public $serialized_fields = array(); // fields that are contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $default_serialization_method = 'json'; // the default serialization method. Options are 'json' and 'serialize'
	public $foreign_keys = array(); // map foreign keys to table models
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $suffix = '_model'; // the suffix used for the data record class
	
// !@todo add docs for $has_many
	public $has_many = array(); // keys are model => key => ', module, relationships_model, foreign_key, candidate_key 
	public $belongs_to = array();
	
	protected $db; // CI database object
	protected $table_name; // the table name to associate the model with
	protected $key_field = 'id'; // usually the tables primary key(s)... can be an array if compound key
	protected $normalized_save_data = NULL; // the saved data before it is cleaned
	protected $cleaned_data = NULL; // data after it is cleaned
	protected $dsn = ''; // the DSN string to connect to the database... if blank it will pull in from database config file
	protected $has_auto_increment = TRUE; // does the table have auto_increment?
	protected $record_class = ''; // the name of the record class (if it can't be determined)
	protected $rules = array(); // validation rules
	protected $fields = array(); // fields in the table
	protected $use_common_query = TRUE; // include the _common_query method for each query
	protected $validator = NULL; // the validator object
	protected $_tables = array(); // an array of table names with the key being the alias and the value being the actual table
	
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
		$this->load->module_language(FUEL_FOLDER, 'model');
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
	 <code>
	$db = $this->examples_model->db(); 
	</code>
	 *
	 * @access	public
	 * @return	array
	 */	
	public function &db()
	{
 		//$this->_check_readonly();
		return $this->db;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Gets the short name minus the suffix
	 *
	 <code>
	echo $this->examples_model->short_name(TRUE); 
	// examples
	</code>
	 *
	 * @access	public
	 * @param	boolean	lower case the name (optional)
	 * @param	boolean return the record clas name (optional)
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
	 <code>
	echo $this->examples_model->table_name(); 
	// examples
	</code>
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
	 * Sets the aliases to table(s) that you can use in your queries
	 *
	 <code>
	$my_tables = array('mytable' => 'my_table');
	$this->examples_model->set_tables($my_tables); 
	</code>
	 *
	 * @access	public
	 * @param	array	an array of tables
	 * @return	void
	 */	
	function set_tables($tables)
	{
		$this->_tables = array_merge($this->_tables, $tables);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Gets the table(s) name based on the configuration
	 *
	 <code>
	$table_name = $this->examples_model->tables('my_table'); 
	</code>
	 *
	 * @access	public
	 * @param	string	the table name (optional)
	 * @return	string
	 */	
	function tables($table = NULL)
	{
		if (!empty($table))
		{
			if (isset($this->_tables[$table]))
			{
				return $this->_tables[$table];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return $this->_tables;
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the key field(s)
	 *
	 <code>
	$fields = $this->examples_model->fields(); 
	foreach($fields as $field)
	{
		echo $field; // field name
	}
	</code>
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
	 <code>
	</code>
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
	 <code>
	$rows = $this->examples_model->get(TRUE, 'object', FALSE); 
	foreach($rows->result() as $row)
	{
	    echo $row->name;
	}
	
	// The third parameter is the column name to be used as the array key value (if <dfn>$force_array</dfn> is set to <dfn>TRUE</dfn>)
	$rows = $this->examples_model->get(TRUE, 'object', 'id'); 
	foreach($rows->result() as $id => $row)
	{
	    echo $id;
	}
	</code>
	 *
	 * @access	public
	 * @param	boolean	return multiple records (optional)
	 * @param	string	method return type (object, array, query, auto) (optional)
	 * @param	string	the column to use for an associative key array (optional)
	 * @param	boolean	determine whether to use the _common_query method in the query (optional)
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
	 <code>
	...
	$query = $this->db->query('SELECT * FROM USERS');
	$users = $this->examples_model->map_query_records($query, 'id');
	foreach($users as $id => $user)
	{
	    echo $user->name;
	}
	</code>
	 *
	 * @access	public
	 * @param	object	the query object
	 * @param	string	the field name to be used the key value (optional)
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
	 <code>
	$my_user['id'] = 1;
	$my_user['name'] = 'Darth Vader';
	$my_user['email'] = 'darth@deathstar.com';
	$my_custom_record = $this->examples_model->map_to_record_class($my_user); 
	echo $my_custom_record->name;
	</code>
	 *
	 * @access	public
	 * @param	array	field values
	 * @param	array	all the fields available for the object (optional)
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
	 * Get the results of the query
	 *
	 <code>
	$examples = $this->examples_model->find_all(array('published' => 'yes'), 'date_added desc'); 
	</code>
	 *
	 * @access	public
	 * @param	string	the type of find to perform. Options are "key", "one", "options", "all" and find_"{your_method}". By default it will perform a find_all (optional)
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query (optional)
	 * @param	int		the number of records to limit in the results (optional)
	 * @param	int		the offset value for the results (optional)
	 * @param	string	return type (object, array, query, auto) (optional)
	 * @param	string	the column to use for an associative key array (optional)
	 * @return	array
	 */	
	function find($find = 'all', $where = NULL, $order = NULL, $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		// allows for just a single parameter of arrays to be passed
		if (is_array($find))
		{
			extract($find);
		}

		$data = array();
		if ($find === 'key')
		{
			$data = $this->find_by_key($where, $return_method);
		}
		else if ($find === 'one')
		{
			$data = $this->find_one($where, $order, $return_method);
		}
		else if ($find === 'options')
		{
			$data = $this->options_list(NULL, NULL, $where, $order);
		}
		else
		{
			if (empty($find) OR $find == 'all')
			{
				$data = $this->find_all($where, $order, $limit, $offset, $return_method, $assoc_key);
			}
			else
			{
				$method = 'find_'.$find;
				if (is_callable(array($this, $method)))
				{
					if (!empty($where)) $this->db->where($where);
					if (!empty($order)) $this->db->order_by($order);
					if (!empty($limit)) $this->db->offset($limit);
					if (!empty($offset)) $this->db->offset($offset);
					
					$args = array_shift(func_get_args());
					$data = call_user_func(array($this, $method), $args);
				}
				else
				{
					return FALSE;
				}

			}
		}
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get one record result based on the key value
	 *
	 <code>
	$id = 1;
	$example = $this->examples_model->find_by_key($id, 'object'); 
	</code>
	 *
	 * @access	public
	 * @param	string	the key value to find a single record
	 * @param	mixed	return type (object, array, query, auto) (optional)
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
	 <code>
	$example = $this->examples_model->find_one(array('published' => 'yes'), ''asc'); 
	</code>
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query (optional)
	 * @param	string	return type (object, array, query, auto) (optional)
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
		
		$data = $query->result();
		
		// unserialize any data
		if ($return_method == 'array')
		{
			$data = $this->unserialize_field_values($data);
		}
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get one record result as an array
	 *
	 <code>
	$examples = $this->examples_model->find_one_array(array('published' => 'yes'), 'date_added desc'); 
	</code>
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query
	 * @param	string	the order by of the query (optional)
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
	 <code>
	$examples = $this->examples_model->find_all(array('published' => 'yes'), 'date_added desc'); 
	</code>
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query (optional)
	 * @param	int		the number of records to limit in the results (optional)
	 * @param	int		the offset value for the results (optional)
	 * @param	string	return type (object, array, query, auto) (optional)
	 * @param	string	the column to use for an associative key array (optional)
	 * @return	array
	 */	
	public function find_all($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$where = $this->_safe_where($where);
		
		if (!empty($where)) 
		{
			if (is_array($where))
			{
				foreach($where as $key => $val)
				{
					// check for nested array values to use for wherein
					$method = (!empty($val) AND is_array($val)) ? 'where_in' : 'where';
					$this->db->$method($key, $val);
				}
			}
			else
			{
				$this->db->where($where);
			}
		}
		
		$params = array('order_by', 'limit', 'offset');
		foreach($params as $method)
		{
			if (!empty($$method)) $this->db->$method($$method);
		}
		$query = $this->get(TRUE, $return_method, $assoc_key);
		if ($return_method == 'query') return $query;
		
		$data = $query->result();

		// unserialize any data if the return method is an array. If it is a custom object, then we let the object take care of it
		if ($return_method == 'array')
		{
			$data = $this->unserialize_field_values($data);
		}
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the results of the query as an array
	 *
	 <code>
	$examples = $this->examples_model->find_all_array(array('published' => 'yes'), 'date_added desc'); 
	</code>
	 *
	 * @access	public
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query (optional)
	 * @param	int		the number of records to limit in the results (optional)
	 * @param	int		the offset value for the results (optional)
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
	 <code>
	$examples = $this->examples_model->find_all_assoc(array('published' => 'yes'), 'date_added desc'); 
	</code>
	 *
	 * @access	public
	 * @param	string	the column to use for an associative key array (optional)
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query (optional)
	 * @param	int		the number of records to limit in the results (optional)
	 * @param	int		the offset value for the results (optional)
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
	 <code>
	$examples = $this->examples_model->find_all_assoc(array('published' => 'yes'), 'date_added desc'); 
	</code>
	 *
	 * @access	public
	 * @param	string	the column to use for an associative key array (optional)
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query (optional)
	 * @param	int		the number of records to limit in the results (optional)
	 * @param	int		the offset value for the results (optional)
	 * @return	array
	 */	
	public function find_all_array_assoc($assoc_key = 'id', $where = array(), $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->find_all($where, $order_by, $limit, $offset, 'array', $assoc_key);
	}

	// --------------------------------------------------------------------
	
	/**
	 * This method takes an associative array with the key values that map to CodeIgniter active record methods and returns a query result object.
	 * 
	 * For more advanced, use CI Active Record. Below are the key values you can pass:
	<ul>
		<li><strong>select</strong></li>
		<li><strong>from</strong></li>
		<li><strong>join</strong></li>
		<li><strong>where</strong></li>
		<li><strong>or_where</strong></li>
		<li><strong>where_in</strong></li>
		<li><strong>or_where_in</strong></li>
		<li><strong>where_not_in</strong></li>
		<li><strong>or_where_not_in</strong></li>
		<li><strong>like</strong></li>
		<li><strong>or_like</strong></li>
		<li><strong>not_like</strong></li>
		<li><strong>or_not_like</strong></li>
		<li><strong>group_by</strong></li>
		<li><strong>order_by</strong></li>
		<li><strong>limit</strong></li>
		<li><strong>offset</strong></li>
	</ul>
	
	 *
	 <code>
	$where['select'] = 'id, name, published';
	$where['where'] = array('published' => 'yes');
	$where['order_by'] = 'name asc';
	$where['limit'] = 10;

	$query = $this->examples_model->query($where); 
	$results = $query->result(); 
	</code>
	 *
	 * @access	public
	 * @param	array	an array of parameters to create a query (optional)
	 * @return	array
	 */	
	public function query($params = array())
	{
		if (is_array($params))
		{
			$defaults = array(
				'select'          => $this->table_name.'.*',
				'from'            => $this->table_name,
				'join'            => array(),
				'where'           => array(),
				'or_where'        => array(),
				'where_in'        => array(),
				'or_where_in'     => array(),
				'where_not_in'    => array(),
				'or_where_not_in' => array(),
				'like'            => array(),
				'or_like'         => array(),
				'not_like'        => array(),
				'or_not_like'     => array(),
				'group_by'        => NULL,
				'order_by'        => NULL,
				'limit'           => NULL,
				'offset'          => NULL
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
					foreach($params['join'] as $join)
					{
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
			
			if ( ! empty($params['limit'])) {
				$this->db->limit($params['limit']);
			}
			if ( ! empty($params['offset'])) {
				$this->db->offset($params['offset']);
			}
			$results = $this->get();
		} 
		else 
		{
			$results = $this->get();
		}
		return $results;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the results of a query as an associative array... good for form option lists
	 *
	 <code>
	$where['published'] = 'yes'; 
	$order = 'name, desc'; 
	$examples_list = $this->examples_model->options_list('id', 'name', $where, $order); 
	</code>
	 *
	 * @access	public
	 * @param	string	the column to use for the value (optional)
	 * @param	string	the column to use for the label (optional)
	 * @param	mixed	an array or string containg the where paramters of a query (optional)
	 * @param	string	the order by of the query. defaults to $val asc (optional)
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
			$val = $fields[1];
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
		
		if (!empty($where))
		{
			$this->db->where($where);
		}
		
		$query = $this->db->get($this->table_name);
		
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
	 <code>
	$where['type'] = 'A'; 
	if ($this->examples_model->record_exists($where))
	{
		echo 'record exists';
	} 
	</code>
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
	 <code>
	$example = $this->examples_model->create($_POST); // Be sure to always clean your $_POST variables before using them
	</code>
	 *
	 * @access	public
	 * @param	mixed	the record oject associated with this class (optional)
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
			throw new Exception(lang('error_could_not_find_record_class', get_class($this)));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clean the data before saving
	 *
	 <code>
	$cleaned_data = $this->examples_model->clean($_POST); // Be sure to always clean your $_POST variables before using them
	</code>
	 *
	 * @access	public
	 * @param	mixed	an array of values to be saved (optional)
	 * @param	boolean	run on_before_clean hook or not (optional)
	 * @return	array
	 */	
	public function clean($values = array(), $run_hook = FALSE)
	{
		$CI =& get_instance();
		if (empty($values)) $values = $CI->input->post();

		// run clean hook
		if ($run_hook)
		{
			$values = $this->on_before_clean($values);
		}
		
		// get table information to clean against
		$fields = $this->table_info();

		$clean = array();
		
		foreach($fields as $key => $val)
		{
			if (isset($values[$key]))
			{
				$values[$key] = ($this->auto_trim AND is_string($values[$key])) ? trim($values[$key]) : $values[$key];
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
				if (empty($values[$key]) OR (int)$values[$key] == 0)
				{
					
					$values[$key] = $this->default_date;
					$has_time = FALSE;
				}
				else
				{
					$has_time = (count(explode(' ', $values[$key])) > 1);
				}
				
				// test if there is an hour field AND that there is NO time values first before looking at other values
				if (isset($values[$key.'_hour']) AND !$has_time)
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
				if (is_string($values[$key]))
				{
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
	 <code>
	$cleaned_data = $this->examples_model->cleaned_data();
	</code>
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
	 <code>
	$where['published'] = 'yes'; 
	echo $this->examples_model->record_count($where); // dislays the number of records
	</code>
	 *
	 * @access	public
	 * @param	mixed	where condition (optional)
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
	 <code>
	$total_count = $this->examples_model->total_record_count();
	</code>
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
	 * Returns a an array of records based on the limit (<strong>$per_page</strong>) and <var>'offset'</var> parameters of the method.
	 *
	 <code>
	$data = $this->examples_model->paginate(20, 0);
	</code>
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
	 * Saves record object, or array of data to the database
	 *
	 <code>
	$this->examples_model->save($_POST, TRUE, TRUE); // Be sure to always clean your $_POST variables before using them
	</code>
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
		$CI =& get_instance();
		if (!isset($record)) $record = $CI->input->post();
		if ($this->_is_nested_array($record))
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
			
			// clean the data before saving. on_before_clean hook now runs in the clean() method
			$values = $this->on_before_clean($values);
			$values = $this->clean($values);
			$values = $this->on_before_validate($values);
			

			// now validate. on_before_validate hook now runs inside validate() method
			$validated = ($validate) ? $this->validate($values) : TRUE;

			if ($validated AND !empty($values))
			{
				// now clean the data to be ready for database saving
				$this->db->set($values);
				
				if ($ignore_on_insert)
				{
					// execute on_before_insert/update hook methods
					$values = $this->on_before_save($values);
					
					// process serialized values
					$values = $this->serialize_field_values($values);
					
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
					if (!$this->_has_key_field_value($values) AND $this->db->insert_id())
					{
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
						if (!$this->_has_key_field_value($values) AND $this->db->insert_id())
						{
							$record->on_after_insert($values);
						}
						else
						{
							$record->on_after_update($values);
						}
					}
				}
				else if (!$this->_has_key_field_value($values))
				{           	
					// execute on_before_insert/update hook methods
					$values = $this->on_before_save($values);
					$values = $this->on_before_insert($values);
					
					// process serialized values
					$values = $this->serialize_field_values($values);
					
					$this->db->insert($this->table_name, $values);
					if (is_string($this->key_field))
					{
						$values[$this->key_field] = $this->db->insert_id();
					}
					$this->on_after_insert($values);
					if (is_a($record, 'Data_record'))
					{
						$record->on_after_insert($values);
					}
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
					
					// process serialized values
					$values = $this->serialize_field_values($values);
					
					$this->db->update($this->table_name, $values);
					$this->on_after_update($values);
					if (is_a($record, 'Data_record'))
					{
						$record->on_after_update();
					}
				}
			} 
			else
			{
				return FALSE;
			}
			
			// returns the key value of the record upon save
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
			
			// check for errors here in case some are thrown in the hooks
			if ($this->has_error())
			{
				return FALSE;
			}
			return $return;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Save related data to a many to many table. To be used in on_after_save hook
	 *
	 <code>
	$this->examples_model->save_related('examples_to_categories', array('example_id' => $obj->id), array('categories_id' => $_POST['categories']));
	</code>
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
	 * Handles grabbing of the related data's keys
	 *
	 <code>
	</code>
	 *
	 * @access public
	 * @param array $values
	 * @param string $related_model
	 * @param string $mode, has_many or belongs_to (optional)
	 * @return array
	 */
	public function get_related_keys($values, $related_model, $mode = 'has_many', $rel_config = '')
	{
		$CI =& get_instance();
		$use_rel_tbl = $this->is_using_relationship_table($rel_config);
		$fields = $this->relationship_field_names($mode);
		
		if (is_array($related_model))
		{
			$related_model = $this->load_related_model($related_model);
		}
		
		if ($use_rel_tbl == FALSE)
		{
			$assoc_where = array($rel_config['foreign_key'] => $values['id']);
			$related_keys = array_keys($CI->$related_model->find_all_array_assoc($CI->$related_model->key_field(), $assoc_where));
		}
		else
		{
			$relationships_model = $this->load_model($fields['relationships_model']);
			
			if ($mode == 'belongs_to')
			{
				$assoc_where = array($fields['candidate_table'] => $CI->$related_model->table_name, $fields['foreign_table'] => $this->table_name());
				if ( ! empty($values) AND array_key_exists('id', $values))
				{
					$assoc_where[$fields['foreign_key']] = $values['id'];
				}
				$related_keys = array_keys($CI->$relationships_model->find_all_array_assoc($fields['candidate_key'], $assoc_where));
			}
			else
			{
				$assoc_where = array($fields['candidate_table'] => $this->table_name(), $fields['foreign_table'] => $CI->$related_model->table_name);
				if ( ! empty($values) AND array_key_exists('id', $values))
				{
					$assoc_where[$fields['candidate_key']] = $values['id'];
				}
				$related_keys = array_keys($CI->$relationships_model->find_all_array_assoc($fields['foreign_key'], $assoc_where));
			}
		}
		
		return $related_keys;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Performs a simple insert to the database record. 
	 *
	 <code>
	$values['name'] = 'Darth Vader'; 
	$values['email'] = 'dvader@deathstar.com'; 
	$this->examples_model->insert($values);
	</code>
	 *
	 * @access	public
	 * @param	mixed	an array or object to save to the database
	 * @return	boolean
	 */	
	public function insert($values)
	{
		$this->_check_readonly();
		$values = $this->on_before_insert($values);
		$values = $this->serialize_field_values($values);
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
	 * Performs a simple update to the database record based on the <dfn>$where</dfn> condition passed. 
	 *
	 <code>
	$where['id'] = 1;
	$this->examples_model->update($_POST, $where); // Be sure to always clean your $_POST variables before using them
	</code>
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
		$values = $this->serialize_field_values($values);
		$this->db->where($where);
		$return = $this->db->update($this->table_name, $values);
		$this->on_after_update($values);
		return $return;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Deletes database record(s) based on the <dfn>$where</dfn> condition passed. Does execute <dfn>before</dfn> and <dfn>after</dfn> delete hooks.
	 *
	 <code>
	$where['id'] = 1; 
	$this->examples_model->delete($where);
	</code>
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
	 * Delete related data. To be used in on_before/on_after delete hooks.
	 *
	 <code>
	$obj = $this->examples_model->create();
	$obj->name = 'Darth Vader';
	$obj->save();
	$this->examples_model->delete_related('examples_to_categories', 'example_id', $obj);
	</code>
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
	 * Truncates the data in the table
	 *
	 <code>
	$this->examples_model->truncate();
	</code>
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
	 * Creates a where condition and queries the model's table to see if the column (<dfn>$key</dfn>) already contains the <dfn>$val</dfn> value.
	 * Usually used for validation to check if a unique key already exists.
	 *
	 <code>
	$this->examples_model->is_new('location', 'id');
	</code>
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
	 * 	Creates a where condition and queries the model's table to see if the column (<dfn>$key</dfn>) already contains the <dfn>$val</dfn> value but compares it to the tables key column with the <dfn>$id</dfn> value.
	 * Usually used for validation. The example below uses this method to validate on the model before saving. The <dfn>is_new</dfn> and <dfn>is_editable</dfn> methods are usually used during the models validation process as shown in the example.
	 *
	 <code>
	function on_before_validate($values) 
	{ 
	    if (!empty($values['id'])) 
	    { 
	        $this->add_validation('location', array(&$this, 'is_editable'), 'The location value already exists.' , array('location', $values['id'])); 
	    } 
	    else 
	    { 
	        $this->add_validation('location', array(&$this, 'is_new'), 'The location value already exists.', 'location'); 
	    } 
	    return $values; 
	} 
	</code>
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
	 * Validate the data before saving. Can pass either a custom record object or an associative array of table column values.
	 * Will run all validation rules, including required and autovalidation (if auto_validation is set on the model)
	 * and return <dfn>TRUE</dfn> if it passes validation and <dfn>FALSE</dfn> if it fails. The <dfn>run_hook</dfn> parameter
	 * will run the model's on_before_validate hook if set to TRUE (default is FALSE);
	 *
	 <code>
	// Using an array of values
	$values['name'] = 'Mr. Jones';
	$values['email'] = 'jones@example.com';
	$values['active'] = 'yes';
	$this->examples_model->validate($values);
	
	// Using a custom record
	$example = $this->examples_model->create();
	$example->name = 'Mr. Jones';
	$example->email = 'jones@example.com';
	$example->active = 'yes';
	$this->examples_model->validate($example);
	
	// you can also just call the validate method on the custom record object
	$example->validate();
	</code>
	 *
	 * @access	public
	 * @param	mixed	object or array of values
	 * @param	boolean	run on_before_validate hook or not (optional)
	 * @return	array
	 */	
	public function validate($record, $run_hook = FALSE)
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
		
		if ($run_hook)
		{
			$values = $this->on_before_validate($values);
		}
		
		// if any errors are generated in the previous hooks then we return FALSE
		if ($this->get_errors())
		{
			return FALSE;
		}
		
		$required = array_merge($this->unique_fields, $this->required);

		// convert required fields to rules
		foreach($required as $key => $val)
		{
			if (is_int($key))
			{
				$field = $val;
				if (empty($this->default_required_message))
				{
					$this->default_required_message = lang('error_required_fields');
				}
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
	 * 	Returns a field type of <dfn>string</dfn>, <dfn>number</dfn>, <dfn>date</dfn>, <dfn>datetime</dfn>, <dfn>time</dfn>, <dfn>blob</dfn>, <dfn>enum</dfn>
	 *
	 <code>
	$type = $this->examples_model->field_type('email');
	echo $type; // string
	</code>
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
	 * Returns a boolean value if all validation rules that have been run have passed.
	 *
	 <code>
	$values['name'] = 'Mr. Jones';
	$values['email'] = 'jones@example.com';
	$values['active'] = 'yes';
	$this->examples_model->validate($values);
	if ($this->examples_model->is_valid($values))
	{
	    echo 'We are valid!';
	}
	</code>
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
	 <code>
	$this->examples_model->add_validation('email', 'valid_email', 'The email is invalid.', 'jones@example.com'); 
	</code>
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
	 <code>
	$this->examples_model->add_error('There was an error in processing the data.', 'my_key'); 
	</code>
	 *
	 * @access	public
	 * @param	string	error message
	 * @param	string	key value of error message (optional)
	 * @return	void
	 */	
	public function add_error($msg, $key = NULL)
	{
		$this->validator->catch_errors($msg, $key);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns a <dfn>TRUE</dfn> if the model has any errors associated with it and <dfn>FALSE</dfn> otherwise.
	 *
	 <code>
	if ($this->examples_model->has_error())
	{
		return FALSE;
	}
	</code>
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function has_error()
	{
		return (count($this->validator->get_errors()) > 1);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Remove a validation rule from the validator object
	 *
	 <code>
	$this->examples_model->remove_validation('my_field', 'my_func');
	</code>
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	string	function name (optional)
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
	 <code>
	$this->examples_model->add_required('my_field'); 
	</code>
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
	 * Gets the validation object
	 *
	 <code>
	$validation = $this->examples_model->get_validation(); 
	if ($validation->is_valid())
	{
	    echo 'YEAH!'; 
	}
	</code>
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
	 <code>
	$this->examples_model->register_to_global_errors(TRUE); 

	...// code goes here

	$errors = get_errors(); // validator_helper function to get global errors
	foreach($errors as $error) 
	{ 
	    echo $error; 
	    // There was an error in processing your data 
	} 
	</code>
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
	 * Returns the errors found in the validator object
	 *
	 <code>
	$errors = $this->examples_model->get_errors(); 

	foreach($errors as $error) 
	{ 
	    echo $error; 
	    // There was an error in processing your data 
	} 
	</code>
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
	 <code>
	$this->examples_model->remove_all_validation();
	</code>
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
	 * Returns an associative array of a specific field's meta information which includes the following:
	 *
	<ul>
		<li><strong>name</strong> - the name of the field</li>
		<li><strong>type</strong> - the type of field (e.g. int, varchar, datetime... etc)</li>
		<li><strong>default</strong> - the default value of the field</li>
		<li><strong>options/max_length</strong> - if it is an enum field, then the enum options will be displayed. Otherwise, it will show the max length of the field which may not be relevant for some field types.</li>
		<li><strong>primary_key</strong> - the primary key column</li>
		<li><strong>comment</strong> - the comment</li>
		<li><strong>collation</strong> - the collation method</li>
		<li><strong>extra</strong> - extra field meta information like auto_increment</li>
		<li><strong>null</strong> - a boolean value if the field is <dfn>NULL</dfn> or not </li>
	</ul>

	 <code>
	$field_meta = $this->examples_model->field_info('email'); 

	echo $field_meta['name']; // email 
	echo $field_meta['type']; // varchar 
	</code>
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
	 * Returns an associative array of table field meta information with each key representing a table field.
	 *
	 <code>
	$table_meta = $this->examples_model->table_info(); 

	echo $table_meta['id']['type']; // int 
	echo $table_meta['id']['primary_key']; // 1 (TRUE) 
	echo $table_meta['email']['type']; // varchar 
	echo $table_meta['first_name']['type']; // varchar 
	echo $table_meta['description']['type']; // text 
	echo $table_meta['active']['type']; // enum 
	print_r($table_meta['active']['options']); // array('yes', 'no') 
	</code>
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
	 * Returns an array of information that can be used for building a form (e.g. Form_builder). 
	 *
	 * Somewhat similar to the table_info method with difference being that the returned array has information for creating a form.
	 * The related parameter is used to conveniently map other model information with this form to create a many to many multi-select form element.
	 * This method is usally used with the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class.
	 *
	 <code>
	$form_info = $this->examples_model->form_fields(); 

	echo $form_fields['id']['type']; // hidden 
	echo $table_meta['email']['type']; // text 
	echo $table_meta['email']['required']; // 1 (TRUE) 
	echo $table_meta['first_name']['type']; // text 
	echo $table_meta['description']['type']; // textfield 
	echo $table_meta['active']['type']; // select or enum 
	echo $table_meta['date_added']['type']; // datetime (a special field type in the form_builder class) 
	</code>
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
			
			// create boolean checkboxes
			if(in_array($val['name'], $this->boolean_fields) AND ($val['type'] === 'tinyint'))
			{
				$fields[$key]['type'] = 'checkbox';
				$fields[$key]['value'] = 1;
			}
			
			// set password fields
			if ($key == 'password' OR $key == 'pwd' OR $key == 'pass')
			{
				$fields[$key]['type'] = 'password';
			}
			
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
				$where = array();
				$model = $this->load_model($val);
				if (is_array($val) AND !empty($val['where']))
				{
					$where = $val['where'];
					unset($val['where']);
				}
				$fields[$key]['type'] = 'select';
				$fields[$key]['options'] = $CI->$model->options_list(NULL, NULL, $where);
				$fields[$key]['first_option'] = 'Select...';
				$fields[$key]['label'] = ucfirst(str_replace('_', ' ', $CI->$model->short_name(TRUE, TRUE)));
				$fields[$key]['module'] = $CI->$model->short_name(TRUE, FALSE);
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
					$related_model = $this->load_model($key.$this->suffix);
					$related_model_name = $related_name.$this->suffix;
					
					$lookup_name = end(explode('/', $val));
					$lookup_model = $this->load_model($val);

					$options = $CI->$related_model_name->options_list();
					
					// important to sort by id ascending order in case a field type uses the saving order as how it should be returned (e.g. a sortable multi-select)
					$field_values = (!empty($values['id'])) ? array_keys($CI->$lookup_name->find_all_array_assoc($CI->$related_model_name->short_name(TRUE, TRUE).'_id', array($this->short_name(TRUE, TRUE).'_id' => $values[$key_field]), 'id asc')) : array();
					$fields[$key] = array('label' => ucfirst($related_name), 'type' => 'array', 'module' => $key, 'options' => $options, 'value' => $field_values, 'mode' => 'multi');
				}
			}
		}

		// attach relationship fields if they exist
		if ( ! empty($this->has_many))
		{
			foreach ($this->has_many as $related_field => $rel_config)
			{
				$related_model = $this->load_related_model($rel_config);
				$where = array();
				if (is_array($rel_config) AND !empty($rel_config['where']))
				{
					$where = $rel_config['where'];
				}
				$related_options = $CI->$related_model->options_list(NULL, NULL, $where);
				$related_vals = ( ! empty($values['id'])) ? $this->get_related_keys($values, $related_model, 'has_many', $rel_config) : array();
				$fields[$related_field] = array('label' => humanize($related_field), 'type' => 'multi', 'options' => $related_options, 'value' => $related_vals, 'mode' => 'multi', 'module' => $CI->$related_model->short_name(TRUE, FALSE));
			}
		}

		if ( ! empty($this->belongs_to))
		{
			foreach ($this->belongs_to as $related_field => $rel_config)
			{
				$where = array();
				if (is_array($rel_config) AND !empty($rel_config['where']))
				{
					$where = $rel_config['where'];
				}

				$related_model = $this->load_related_model($rel_config);
				$related_options = $CI->$related_model->options_list(NULL, NULL, $where);
				$related_vals = ( ! empty($values['id'])) ? $this->get_related_keys($values, $related_model, 'belongs_to', $rel_config) : array();
				$fields[$related_field] = array('label' => lang('label_belongs_to').'<br />' . humanize($related_field), 'type' => 'multi', 'options' => $related_options, 'value' => $related_vals, 'mode' => 'multi', 'module' => $CI->$related_model->short_name(TRUE, FALSE));
			}
		}

// !@todo Finish creating has_one relationship
/*
		if ( ! empty($this->has_one))
		{
			foreach ($this->has_one as $related_field => $rel_config)
			{
				$related_model = $this->load_related_model($rel_config);
				$related_options = $this->$related_model->options_list();
				$related_vals = ( ! empty($values['id'])) ? $this->get_related_keys($values, $related_model, 'has_many', $rel_config) : array();
				$fields[$related_field] = array('label' => humanize($related_field), 'type' => 'select', 'options' => $related_options, 'value' => $related_vals, 'first_option' => lang('label_select_one'));
			}
		}
*/


		// set auto dates to display only be default
		$hidden_fields = array_merge($this->auto_date_add, $this->auto_date_update, $this->hidden_fields);
		foreach($hidden_fields as $f)
		{
			if (isset($fields[$f])) $fields[$f]['type'] = 'hidden';
		}
		
		return $fields;
	}
	
	/**
	 * Outputs the data passed to in into a comma separated value (CSV)
	 *
	 <code>
	$items = $this->find_all();
	$data = $this->csv($items);
	</code>
	 *
	 * @access	public
	 * @param	boolean	Display headers?
	 * @param	string	The delimiter - comma by default
	 * @param	string	The newline character - \n by default
	 * @param	string	The enclosure - double quote by default
	 * @return	string
	 */	
	public function csv($data = NULL, $display_headers = TRUE, $delim = ",", $newline = "\n", $enclosure = '"')
	{
		// borrowed from CI DBUtil class
		$out = '';
		
		if (is_null($data))
		{
			$data = $this->find_all_array();
		}

		// First generate the headings from the table column names
		if ($display_headers !== FALSE)
		{
			// check if it is a query object first
			if (is_object($data) AND method_exists($data, 'list_fields'))
			{
				$headers = $query->list_fields();
				$data = $query->result_array();
			}

			// then check to see if it is just a data array
			else if ($this->_is_nested_array($data))
			{
				$record = current($data);
				$headers = array_keys($this->normalize_data($record));
			}
		
			foreach ($headers as $name)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
			}
		}

		$out = rtrim($out);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($data as $row)
		{
			// normalize the row data
			$row = $this->normalize_data($row);

			foreach ($row as $item)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
		
	}
	// --------------------------------------------------------------------

	/**
	 * 	Returns the custom record class name if it exists. 
	 * 
	 * If a name does not exist, it will try to intelligently find a class with a singular version 
	 * of the parent table model's name (e.g. <dfn>examples_model</dfn> = <dfn>example_model</dfn>)
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
			$record_class = substr(ucfirst($class_name), 0, -1);
			

			// common change
			$record_class = preg_replace('#ie$#', 'y', $record_class);
			$record_class .= $this->suffix;
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
	 * Set's the default return method for methods like <dfn>get()</dfn>, <dfn>find_one</dfn> and <dfn>find_all</dfn>.
	 * 
	 * Values can be <dfn>object</dfn>, <dfn>array</dfn>, <dfn>query</dfn>, <dfn>auto</dfn>
	 *
	 <code>
	$this->examples_model->set_return_method('object'); 
	$examples = $this->examples_model->find_all(); // an array of custom objects (if a custom object is defined. If not, a standard object will be used) 

	$this->examples_model->set_return_method('array'); 
	$examples = $this->examples_model->find_all(); // an array of associative arrays is returned and will ignore any custom object
	</code>
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
	 * Returns the return method used for querying (<dfn>object</dfn>, <dfn>array</dfn>, <dfn>query</dfn>, <dfn>auto</dfn>)
	 *
	 <code>
	$return_method = $this->examples_model->get_return_method(); // object 
	</code>
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
	 * Prints out to the screen the last query results ran by the model.
	 *
	 <code>
	$this->examples_model->debug_data(); // prints out an array of information to the screen
	</code>
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
	 * Prints out to the screen the last query SQL ran by the model.
	 *
	 <code>
	$this->examples_model->debug_query(); // prints out the last query run by the model
	</code>
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
	 * Normalize the data to be saved so that it becomes an array and can be referenced by the 'normalized_save_data' property
	 *
	 <code>
	$record = $this->examples_model->create(); 
	$record->name = 'John Smith'; 

	$values = $this->examples_model->normalize_save_values($record); 
	echo $values['name'] = 'John Smith';
	</code>
	 *
	 * @access	public
	 * @param	mixed	array of values to be saved
	 * @return	array
	 */	
	public function normalize_save_values($record)
	{
		$CI =& get_instance();
		if (!isset($record)) $record = $CI->input->post();
		$values = $this->normalize_data($record);
		$this->normalized_save_data = $values;
		return $values;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Normailzes the data passed to it so that it becomes an array (used by the normalize_save_values)
	 *
	 <code>
	$record = $this->examples_model->create(); 
	$record->name = 'John Smith'; 

	$values = $this->examples_model->normalize_data($record); 
	echo $values['name'] = 'John Smith';
	</code>
	 *
	 * @access	public
	 * @param	mixed	array of values
	 * @return	array
	 */	
	public function normalize_data($data)
	{
		if (is_object($data))
		{
			if (is_a($data, 'Data_record'))
			{
				$values = $data->values();
			}
			else
			{
				$values = get_object_vars($data);
			}
		}
		else
		{
			$values = (array) $data;
		}
		return $values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Processes $linked_fields and will convert any empty values with their corresponding linked field function
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
						$params = array($values[$val]);
						
						if (is_array($func))
						{
							$f = array_shift($func);
							$params = array_merge($params, $func);
							$func = $f;
						}
						if (function_exists($func))
						{
							$values[$field] = call_user_func_array($func, $params);
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
	 * Process relationships
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function process_relationships($id)
	{
		$CI =& get_instance();
		
		// handle has_many relationships
		if ( ! empty($this->has_many))
		{
			$fields = $this->relationship_field_names('has_many');
			$relationships_model = $this->load_model($fields['relationships_model']);
			
			// first delete in case there are multiple saves to the same relationship table
			foreach ($this->has_many as $related_field => $related_model)
			{
				// remove pre-existing relationships
				$CI->$relationships_model->delete(array($fields['candidate_table'] => $this->table_name, $fields['candidate_key'] => $id));
			}

			// then save
			foreach ($this->has_many as $related_field => $related_model)
			{
				if ( ! empty($this->normalized_save_data[$related_field]))
				{
					$related_model = $this->load_related_model($related_model);
					
					// create relationships
					foreach ($this->normalized_save_data[$related_field] as $foreign_id)
					{
						$CI->$relationships_model->save(array($fields['candidate_table'] => $this->table_name, $fields['candidate_key'] => $id, $fields['foreign_table'] => $CI->$related_model->table_name, $fields['foreign_key'] => $foreign_id));
					}
				}
			}
		}
		
		// handle belongs_to relationships
		if ( ! empty($this->belongs_to))
		{
			$fields = $this->relationship_field_names('belongs_to');
			$relationships_model = $this->load_model($fields['relationships_model']);
			$related_models = array();

			// first delete in case there are multiple saves to the same relationship table
			foreach ($this->belongs_to as $related_field => $related_model)
			{
				// cache the loaded models here for reference below
				$related_models[$related_field] =& $this->load_related_model($related_model);

				// remove pre-existing relationships
				$CI->$relationships_model->delete(array($fields['candidate_table'] => $CI->$related_model->table_name, $fields['foreign_table'] => $this->table_name, $fields['foreign_key'] => $id));

			}

			// then save
			foreach ($this->belongs_to as $related_field => $related_model)
			{
				if ( ! empty($this->normalized_save_data[$related_field]))
				{
					$related_model = $related_models[$related_field];
					
					// create relationships
					foreach ($this->normalized_save_data[$related_field] as $candidate_id)
					{
						$CI->$relationships_model->save(array($fields['foreign_table'] => $this->table_name, $fields['foreign_key'] => $id, $fields['candidate_table'] => $CI->$related_model->table_name, $fields['candidate_key'] => $candidate_id));
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Process relationships before delete
	 *
	 * @access	public
	 * @param	array	where condition for deleting
	 * @return	void
	 */	
	public function process_relationships_delete($id)
	{
		$CI =& get_instance();
		
		// clear out any relationships
		if ( ! empty($this->has_many))
		{
			$fields = $this->relationship_field_names('has_many');

			foreach ($this->has_many as $related_field => $related_model)
			{
				$relationships_model = $this->load_model($fields['relationships_model']);
				$CI->$relationships_model->delete(array($fields['candidate_table'] => $this->table_name, $fields['candidate_key'] => $id));
			}
		}
		if ( ! empty($this->belongs_to))
		{
			$fields = $this->relationship_field_names('belongs_to');
			foreach ($this->belongs_to as $related_field => $related_model)
			{
				$related_model = $this->load_related_model($related_model);
				$relationships_model = $this->load_model($fields['relationships_model']);
				$CI->$relationships_model->delete(array($fields['candidate_table'] => $CI->$related_model->table_name, $fields['foreign_table'] => $this->table_name, $fields['foreign_key'] => $id));
			}
		}
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
	 * Hook - right before saving of data
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
	 * Hook - right after saving of data
	 *
	 * @access	public
	 * @param	array	values to be saved
	 * @return	array
	 */	
	public function on_after_save($values)
	{
		// process relationship values
		$id = $this->_determine_key_field_value($values);
		$this->process_relationships($id);
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
		$id = $this->_determine_key_field_value($where);
		$this->process_relationships_delete($id);
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
	 <code>
	// load model from application directory
	$this->load_model('my_model');
	
	// load model from another module
	$this->load_model(array('my_module' => 'my_model'));
	</code>
	 *
	 * @access	public
	 * @param	mixed	the name of the model. If an array, the key is the module and the name is the model
	 * @return	string
	 */	
	public function load_model($model)
	{
		$CI =& get_instance();
		if (is_array($model))
		{
			$module = key($model);
			$m = current($model);

			// TODO .... DECIDE IF WE SHOULD PASS THROUGH to format_model_name... the suffix may be different if configured
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
	 * Load a related model
	 *
	 * @access	public
	 * @param	string	the name of the model
	 * @return	boolean
	 */	
	public function load_related_model($related_model)
	{
		// rearrange some params if model is an array
		if (is_array($related_model))
		{

			if (isset($related_model['model']))
			{
				if (is_array($related_model['model']))
				{
					$module_model = $related_model['model'];
					$related_model = array(
						'module' => key($module_model),
						'model'  => current($module_model),
						);
				}
				else
				{
					$related_model = $related_model['model'];
				}
			}
			else
			{
				$module = key($related_model);
				$model = current($related_model);
				$related_model = array(
						'model'  => $model,
						'module' => $module,
						);
			}
		}
		
		if (is_array($related_model))
		{
			if (is_array($related_model['model']))
			{
				$related_model = $this->load_model($related_model['model']);
			}
			else if (isset($related_model['module'], $related_model['model']))
			{
				$related_model = $this->load_model(array($related_model['module'] => $this->format_model_name($related_model['model'])));
			}
		}
		else
		{
			$related_model = $this->load_model($this->format_model_name($related_model));
		}
		return $related_model;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array of the relationship field names to be used
	 *
	 * @access	public
	 * @param	string	relationship type
	 * @return	array
	 */	
	public function relationship_field_names($relationship_type)
	{
		$valid_rel_types = array('has_many', 'belongs_to');
		if ( ! in_array($relationship_type, $valid_rel_types))
		{
			return FALSE;
		}
		
		if (empty($this->$relationship_type))
		{
			return FALSE;
		}
		
		$rel_config = $this->$relationship_type;
		$fields = array(
			'candidate_table'	=> 'candidate_table',
			'foreign_table'		=> 'foreign_table',
			'foreign_key'		=> 'foreign_key',
			'candidate_key'		=> 'candidate_key',
			'relationships_model'=> array(FUEL_FOLDER => 'relationships_model'),
			);
			
		if (is_array($rel_config))
		{
			// loop
			foreach($fields as $key => $val)
			{
				if (isset($rel_config[$key]))
				{
					$fields[$key] = $rel_config[$key];
				}
			}
		}
		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Format a model's name
	 *
	 * @access	public
	 * @param	string	the name of the model
	 * @return	boolean
	 */	
	public function format_model_name($model)
	{
		$model_name = $model;
		if (substr($model, -strlen($this->suffix)) != $this->suffix)
		{
			$model_name .= $this->suffix;
		}
		return $model_name;
	}
	

	/**
	 * Returns whether the relationship is using a pivot table
	 */
	public function is_using_relationship_table($rel_config)
	{
		if (is_array($rel_config) AND array_key_exists('relationships_model', $rel_config) AND ($rel_config['relationships_model'] == FALSE)
				AND array_key_exists('foreign_key', $rel_config) AND ! empty($rel_config['foreign_key'])
				)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Serializes field values specified in the $serialized_fields property
	 *
	 * @access	public
	 * @param	string	the field name to unserialize
	 * @return	array
	 */	
	public function serialize_field_values($data)
	{
		// serialize any data
		if (!empty($this->serialized_fields) AND is_array($data))
		{
			if ($this->_is_nested_array($data))
			{
				foreach($data as $key => $val)
				{
					$data[$key] = $this->serialize_field_values($val);
				}
			}
			
			$method = NULL;
			foreach($this->serialized_fields as $method => $field)
			{
				if (!is_numeric($method))
				{
					$method = ($method != 'json') ? 'serialize' : 'json';
				}

				if (isset($data[$field]))
				{
					$data[$field] = $this->serialize_value($data[$field], $method);
				}
			}
		}
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Serialize a value to be saved
	 *
	 * @access	public
	 * @param	string	the array value to unserialize
	 * @param	string	the unserialization method. If none is specified it will default to the default setting of the model which is 'json' 
	 * @return	array
	 */	
	public function serialize_value($val, $method = NULL)
	{
		if (empty($method))
		{
			$method = $this->default_serialization_method;
		}
		
		if (is_array($val))
		{
			if ($method == 'serialize')
			{
				$val = serialize($val);
			}
			else
			{
				$val = json_encode($val);
			}
		}
		return $val;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Unserialize a field's value
	 *
	 * @access	public
	 * @param	mixed	the data to unserialize. Can be an array of arrays too
	 * @return	array
	 */	
	public function unserialize_field_values($data)
	{
		// unserialize any data
		if (!empty($this->serialized_fields))
		{
			if ($this->_is_nested_array($data))
			{
				foreach($data as $key => $val)
				{
					$data[$key] = $this->unserialize_field_values($val);
				}
			}
			else
			{
				$method = NULL;
				foreach($this->serialized_fields as $method => $field)
				{
					if (!is_numeric($method))
					{
						$method = ($method != 'json') ? 'serialize' : 'json';
					}

					if (isset($data[$field]))
					{
						$data[$field] = $this->unserialize_value($data[$field], $method);
					}
				}
			}
		}
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Unserialize a saved value
	 *
	 * @access	public
	 * @param	string	the array value to unserialize
	 * @param	string	the unserialization method. If none is specified it will default to the default setting of the model which is 'json' 
	 * @return	array
	 */	
	public function unserialize_value($val, $method = NULL)
	{
		// if not a valid field name, then we look for a key value with that name in the serialized_fields array
		$invalid_field_names = array('json', 'serialize');
		if (!in_array($method, $invalid_field_names))
		{
			foreach($this->serialized_fields as $m => $field)
			{
				if ($field == $method)
				{
					$method = $m;
					break;
				}
			}
		}

		if (empty($method))
		{
			$method = $this->default_serialization_method;
		}

		
		if ($method == 'serialize')
		{
			if (is_serialized_str($val))
			{
				$val = unserialize($val);
			}
		}
		else
		{
			if (is_json_str($val))
			{
				$val = json_decode($val, TRUE);
			}
		}
		return $val;
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
			throw new Exception(lang('error_in_readonly_mode', get_class($this)));
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
	 * Determins if the value is an array of arrays
	 *
	 * @access	protected
	 * @param	mixed
	 * @return	boolean
	 */	
	protected function _is_nested_array($record)
	{
		return (is_array($record) AND (is_int(key($record)) AND is_array(current($record))));
	}

	// --------------------------------------------------------------------
	
	/**
	 * What to print when echoing out this object
	 *
	 <code>
	</code>
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
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * This class is a wrapper around the query results returned by the MY_Model class
 * 
 * This class is instantiated by the Table Class (MY_Model) when a result set is needed.
 * The Data_set class has a few methods to retrieve information about the data set.
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/my_model
 * @prefix		$data_set->
 */

class Data_set {
	
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
	 <code>
	$single_result_set = $this->examples_model->get(FALSE); 
	$example = $single_result_set->result(); 
	echo $example->name; 
	
	// If multiple result sets are needed, then the result method will return multiple result objects/arrays like so:
	$multiple_result_set = $this->examples_model->get(TRUE); 
	foreach($multiple_result_set as $example) 
	{ 
	    echo $example->name; 
	} 
	</code>
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
	 <code>
	$multiple_result_set = $this->examples_model->get(TRUE); 
	echo $multiple_result_set->num_records();
	</code>
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
	 <code>
	$multiple_result_set = $this->examples_model->get(TRUE); 
	$multiple_result_set->debug();
	</code>
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
	 <code>
	</code>
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
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * This class can be extended to return custom record objects for MY_Model
 * 
 * The Data_record class is used to create custom record objects for a Table class (MY_Model). 
 * Data_record objects provides a greater level of flexibility with your models by allowing you to create not only
 * methods on your model to retreive records from your datasource, but also the ability to create
 * derived attributes and lazy load other objects with each record returned.
 * This class is <strong>optional</strong>. If it it doesn't exist, then the Table Class parent model
 * will use either a standard generic class or an array depending on the return method specified.
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/my_model
 * @prefix		$record->
 */

class Data_record {

	protected $_CI = NULL; // global CI object
	protected $_db = NULL; // database object
	protected $_fields = array(); // fields of the record
	protected $_objs = array(); // nested objects
	protected $_parent_model = NULL; // the name of the parent model
	protected $_inited = FALSE; // Returns whether the object has been initiated or not
	protected $_date_format = ''; // datetime method format... will first look in config and then will default to m/d/Y
	protected $_time_format = 'h:i:s a'; // datetime method format
	protected $_format_suffix = '_formatted'; // datetime method format
	
	/**
	 * Constructor - requires a result set from MY_Model. 
	 * @param	object	parent object
	 */
	public function __construct(&$parent = NULL)
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
		
		$this->_parent_model = $parent;
		$this->_db = $this->_parent_model->db();
		
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
	 * This method returns either <dfn>TRUE</dfn> or <dfn>FALSE</dfn> depending on if the record class has been properly intialized.
	 *
	 <code>
	$record = $this->examples_model->create(); 
	$record->is_initialized(); // Returns TRUE
	</code>
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
	 <code>
	$fields = array('id', 'name', 'email');
	$record = $this->examples_model->set_fields($fields); 
	</code>
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
	 <code>
	$record->id(); // Returns id
	</code>
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
	 <code>
	$record = $this->examples_model->create(); 
	$record->fill($_POST);  // Be sure to always clean your $_POST variables before using them
	</code>
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
	 * Returns an array of the record's values. 
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$values = $record->values()
	echo $values['email']; // vader@deathstar.com 
	</code>
	 *
	 * @access	public
	 * @param	boolean Determins whether to include derived attributes (those starting with get_)
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
	 * Duplicates the record with all it's current values
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$duplicate_record = $record->duplicate()
	echo $duplicate_record->email; // vader@deathstar.com 
	</code>
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
	 * Saves all the properties of the object. 
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->email = 'hsolo@milleniumfalcon.com';
	$record->save();
	</code>
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
	 * Validates the values of the object to makes sure they are valid to be saved.
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->email = 'hsolomilleniumfalcon.com'; // note the invalid email address
	if ($record->validate()) 
	{ 
	    echo 'VALID'; 
	} 
	else 
	{ 
	    echo 'Please fill out a valid email address'; 
	} 
	</code>
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
	 * Returns <dfn>TRUE</dfn> or <dfn>FALSE</dfn> depending on if validation has been run and is valid.
	 *
 	<p class="important">The validate <strong>method</strong> must be called before calling <strong>is_valid</strong>.</p>
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->email = 'hsolomilleniumfalcon.com'; // note the invalid email address
	$record->validate();

	... other code ...

	if ($record->is_valid()) 
	{ 
	    echo 'VALID'; 
	} 
	else 
	{ 
	    echo 'Please fill out a valid email address'; 
	} 
	</code>
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
	 * Returns an array of error messages if there were any found after validating. 
	 * This is commonly called after saving because validation will occur automatically on save.
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->email = 'hsolomilleniumfalcon.com'; // note the invalid email address
	if (!$record->save())
	{ 
	    foreach($record->errors as $error)
	    {
	        echo $error;	
	    }
	} 
	</code>
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
	 * Deletes the record. Similar to the save method, it will call the parent model's delete method passing itself as the where condition to delete
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->delete(); // note the invalid email address
	</code>
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
	 * Refreshes the object from the data source.
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->email = 'hsolo@milleniumfalcon.com';
	$record->refresh();
	echo $record->email; // dvader@deathstar.com
	</code>
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
	 * Will load another model's record object and is often used in custom derived attributes.
	 *
	 <code>
	function get_spaceship()
	{
	    $ship = $this->lazy_load(array('email' => 'hsolo@milleniumfalcon.com'), 'spacehips_model', FALSE);
	    return $ship;
	}
	</code>
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
	 * Returns the parent model object
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 

	// Same as above
	$record->parent_model()->find_one(array('email' => 'dvader@deathstar.com');
	</code>
	 *
	 * @access	public
	 * @return	object
	 */	
	public function parent_model()
	{
		return $this->_parent_model;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Tests whether a property exists on the record
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	if ($record->prop_exists('email')))
	{
	    echo $record->email;
	}
	</code>
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
	 * Prints to the screen the last query run by the parent model. An alias to the parent model's debug_query method.
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->debug_query()))
	</code>
	 *
	 * @access	public
	 * @param	object	parent model object
	 * @param	array	field names
	 * @return	array
	 */	
	public function debug_query()
	{
		$this->_parent_model->db()->debug_query();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Prints to the screen the property values of the of the object.
	 *
	 <code>
	$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
	$record->debug_data()))
	</code>
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
			else if ($this->_is_relationship_property($found[1], 'has_many'))
			{
				$return_object = (isset($args[0])) ? $args[0] : FALSE;
				return $this->_get_relationship($found[1], $return_object,'has_many');
			}
			else if ($this->_is_relationship_property($found[1], 'belongs_to'))
			{
				$return_object = (isset($args[0])) ? $args[0] : FALSE;
				return $this->_get_relationship($found[1], $return_object,'belongs_to');
			}
		}
		else if (preg_match("/is_(.*)/", $method, $found))
		{
			if (array_key_exists($found[1], $this->_fields))
			{
				$field = $this->_parent_model->field_info($found[1]);
				if (!empty($field) AND (($field['type'] == 'enum' AND count($field['options']) == 2) OR in_array($found[1], $this->_parent_model->boolean_fields)))
				{
					return is_true_val($this->_fields[$found[1]]);
				}
			}
		}
		else if (preg_match("/has_(.*)/", $method, $found))
		{
			if (array_key_exists($found[1], $this->_fields))
			{
				$field = $this->_parent_model->field_info($found[1]);
				if (!empty($field))
				{
					return !empty($this->_fields[$found[1]]);
				}
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
		else if ($this->_is_relationship_property($var, 'has_many'))
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
	public function __get($var)
	{
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
		// check if field is for related data via has_many
		else if ($this->_is_relationship_property($var, 'has_many'))
		{
			$output = $this->_get_relationship($var, FALSE, 'has_many');
		}
		// check if field is for related data via belongs_to
		else if ($this->_is_relationship_property($var, 'belongs_to'))
		{
			$output = $this->_get_relationship($var, FALSE, 'belongs_to');
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
		
		// unserialize any data
		if (!empty($this->_parent_model->serialized_fields))
		{
			$output = $this->_parent_model->unserialize_value($output, $var);
		}
		
		$output = $this->after_get($output, $var);
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of relationship data
	 *
	 * @access	protected
	 * @param	string	field name
	 * @param	boolean	whether return the related object or just the data (optional)
	 * @param	string	options are 'has_many' and 'belongs_to'(optional)
	 * @return	array
	 */	
	protected function _get_relationship($var, $return_object = FALSE, $relationship_type = 'has_many')
	{
		$valid_rel_types = array('has_many', 'belongs_to');
		if ( ! in_array($relationship_type, $valid_rel_types))
		{
			return FALSE;
		}
		
		$rel = $this->_parent_model->$relationship_type;
		$fields = $this->_parent_model->relationship_field_names($relationship_type);
		$id_field = '';
		
		$rel_config = $rel[$var];

		$use_rel_tbl = $this->_parent_model->is_using_relationship_table($rel_config);
		
		$output = array();
		
		// load the necessary models
		$foreign_model = $this->_parent_model->load_related_model($rel_config);
		if ($use_rel_tbl)
		{
			$relationships_model = $this->_parent_model->load_model($fields['relationships_model']);
			$id_field = $this->_parent_model->key_field();
			$related_table_name = $this->_CI->$foreign_model->table_name();
		}
		
		// check that the id field is not an array
		if (!is_string($id_field))
		{
			return FALSE;
		}
		
		if ($use_rel_tbl == FALSE)
		{
			// Using alternative relationship table
			$this->_CI->$foreign_model->db()->where($rel_config['foreign_key'], $this->id);
		}
		else
		{
			// Using relationship pivot table
			if (strtolower($relationship_type) == 'belongs_to')
			{
				$rel_where = array(
					$fields['candidate_table'] => $related_table_name,
					$fields['foreign_table']   => $this->_parent_model->table_name(),
					$fields['foreign_key']     => $this->$id_field,
					);
				$rel_ids = array_keys($this->_CI->$relationships_model->find_all_array_assoc('candidate_key', $rel_where));
			}
			else
			{
				$rel_where = array(
					$fields['candidate_table'] => $this->_parent_model->table_name(),
					$fields['candidate_key']   => $this->$id_field,
					$fields['foreign_table']   => $related_table_name,
					);
				$rel_ids = array_keys($this->_CI->$relationships_model->find_all_array_assoc('foreign_key', $rel_where));
			}
			if ( ! empty($rel_ids))
			{
				// construct the method name
				$this->_CI->$foreign_model->db()->where_in("{$related_table_name}.".$id_field, $rel_ids);
			}
			else
			{
				if ($return_object)
				{
					return FALSE;
				}
				else
				{
					return array();
				}
			}
		}

		// if return object is set to TRUE, then do just that  with the where_in already applied

		if ($return_object)
		{
			return $this->_CI->$foreign_model;
		}
		
		// other wise we do a find all with the where_in already applied
		else
		{
			$foreign_data = $this->_CI->$foreign_model->find_all();
		}
		if ( ! empty($foreign_data))
		{
			$output = $foreign_data;
		}
		
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the property name represents a relationship property
	 *
	 * @access	protected
	 * @param	string	field name
	 * @param	string	relationship type
	 * @return	boolean
	 */	
	protected function _is_relationship_property($var, $type = 'has_many')
	{
		if ( ! empty($this->_parent_model->$type) AND array_key_exists($var, $this->_parent_model->$type))
		{
			$rel = $this->_parent_model->$type;
			if ( ! empty($rel[$var])) {
				return TRUE;
			}
		}
		return FALSE;
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
				if (empty($this->_date_format))
				{
					$this->_date_format = $this->_CI->config->item('date_format');
				}
				if (empty($this->_date_format))
				{
					$this->_date_format = 'm/d/Y';
				}
				$output = date($this->_date_format.' '.$this->_time_format, strtotime($this->_fields[$field]));
				break;
			case 'date':
				if (empty($this->_date_format))
				{
					$this->_date_format = $this->_CI->config->item('date_format');
				}
				if (empty($this->_date_format))
				{
					$this->_date_format = 'm/d/Y';
				}
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
/* Location: ./modules/fuel/core/MY_Model.php */
