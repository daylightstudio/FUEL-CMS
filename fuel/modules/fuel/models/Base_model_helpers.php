<?php 

// ------------------------------------------------------------------------

/**
 * Abstract base model helper class
 *
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 */
abstract class Abstract_base_model_helper {

	protected $values = array();
	protected $parent_model = NULL;
	protected $record = NULL;
	protected $CI = NULL;
	protected $fuel = NULL;

	public function __construct($record = array(), $parent_model = NULL)
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the parent model.
	 *
	 * @access	public
	 * @param	object 	A reference to the parent model
	 * @return	object
	 */	
	public function set_parent_model($model)
	{
		$this->parent_model = $model;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the parent model.
	 *
	 * @access	public
	 * @return	object 	A reference to the parent models
	 */	
	public function get_parent_model()
	{
		return $this->parent_model;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the values initially.
	 *
	 * @access	public
	 * @param	array 	The values to set
	 * @return	object
	 */	
	public function set_values($values)
	{
		$this->values = $this->get_parent_model()->normalize_data($values);
		return $this;
	}

	/**
	 * Appends to the values instead of overwrites all the values.
	 *
	 * @access	public
	 * @param	array 	The values to set
	 * @return	object
	 */	
	public function append_values($values)
	{
		$values = array_merge($this->get_values(), $values);
		$this->set_values($values);
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the values.
	 *
	 * @access	public
	 * @return	array
	 */	
	public function get_values()
	{
		return $this->values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the values.
	 *
	 * @access  public
	 * @param	string 	A field name
	 * @return	mixed 	The value
	 */
	public function get_value($key)
	{
		if (array_key_exists($key, $this->values))
		{
			return $this->values[$key];
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the record object.
	 *
	 * @access	public
	 * @return	object
	 */	
	public function record()
	{
		if (!isset($this->record))
		{
			$key_field = $this->get_parent_model()->key_field();
			$id = $this->get_value($key_field);
			$this->record = (!empty($id)) ? $this->get_parent_model()->find_by_key($id) : FALSE;	
		}
		return $this->record;
	}

}


// ------------------------------------------------------------------------

/**
 * Base model fields class
 *
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 */
class Base_model_fields extends Abstract_base_model_helper implements ArrayAccess, Countable, IteratorAggregate {

	protected $fields = array();

	public function __construct($fields = array(), $values = array(), $parent_model = NULL)
	{
		parent::__construct();
		$this->set_parent_model($parent_model);
		$this->set_fields($fields);
		$this->set_values($values);
		$this->initialize($this->get_fields(), $this->get_values(), $this->get_parent_model());
	}

	// --------------------------------------------------------------------
	
	/**
	 * A placeholder for initialization of field data. This method will most likely be overwritten
	 *
	 * @access	public
	 * @return	void
	 */	
	public function initialize($fields, $values, $parent_model)
	{
		// put in your own fields initialization code
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the fields initially.
	 *
	 * @access	public
	 * @param	array 	The fields to set
	 * @param	boolean Determines whether or not to remove the order values set for the fields
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function set_fields($fields, $remove_order = TRUE)
	{
		if ($fields instanceof Base_model_fields)
		{
			$fields = $fields->get_fields();
		}

		$this->fields = $fields;

		if ($remove_order)
		{
			foreach($this->fields as $key => $field)
			{
				if (array_key_exists('order', $this->fields[$key]))
				{
					unset($this->fields[$key]['order']);
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the fields.
	 *
	 * @access	public
	 * @return	array
	 */	
	public function get_fields()
	{
		return $this->fields;
	}

	/**
	 * Returns the field parameters.
	 *
	 * @access  public
	 * @param	string 	A field name
	 * @return	mixed 	The field
	 */
	public function get_field($key)
	{
		if (array_key_exists($key, $this->fields))
		{
			return $this->fields[$key];
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a field's parameter value.
	 *
	 * @access	public
	 * @param	string 	A field name
	 * @param	string 	The parameter of the field to set
	 * @param	mixed 	The value of the parameter to set
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function set($field, $param, $value = NULL)
	{
		if (is_string($field) AND is_string($param))
		{
			$this->fields[$field][$param] = $value;
		}
		elseif(is_array($field))
		{
			foreach($field as $key => $val)
			{
				if (isset($value) AND is_int($key))
				{
					
					$this->set($val, $param, $value);
				}
				else
				{
					$this->set($key, $param, $val);
				}
			}
		}
		else
		{
			if ($value === TRUE AND isset($this->fields[$field]))
			{
				$this->fields[$field] = array_merge($this->fields[$field], $param);	
			}
			else
			{
				$this->fields[$field] = $param;
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Gets a field's parameter value.
	 *
	 * @access	public
	 * @param	string 	A field name
	 * @param	string 	The parameter of the field to set
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function get($field, $param = NULL)
	{
		if (array_key_exists($field, $this->fields))
		{
			if (!empty($param))
			{
				if (array_key_exists($param, $this->fields[$field]))
				{
					return $this->fields[$field][$param];	
				}
				else
				{
					return NULL;
				}
				
			}
			else
			{
				return $this->fields[$field];
			}
		}
		else
		{
			return NULL;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Removes fields
	 *
	 * @access	public
	 * @param	array 	An array of fields to remove
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function remove($fields)
	{
		if (is_string($fields))
		{
			$fields = preg_split('#\s*,\s*#', $fields);
		}
		$fields = (array) $fields;
		foreach($fields as $field)
		{
			if (isset($this->fields[$field]))
			{
				unset($this->fields[$field]);	
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Clears the fields and values.
	 *
	 * @access	public
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function clear()
	{
		$this->fields = array();
		$this->values = array();
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Groups fields together to create a tab
	 *
	 * @access	public
	 * @param	string 	The label of the tab
	 * @param	array 	The fields to put under the tab (optional)
	 * @param	array 	The order of the fields (optional)
	 * @param	string 	The name of additional class to append to the fieldset (tab container) (optional)
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function tab($label, $fields = array(), $order_start = NULL, $other_class = '')
	{
		$other_class = (!empty($other_class)) ? ' '.$other_class : '';
		$this->fields[$label] = array('type' => 'fieldset', 'class' => 'tab'.$other_class);
		$i = 1;
		foreach($fields as $key => $field)
		{
			// if a string is passed in the array, then we'll assume it's the key to the field
			if (is_int($key))
			{
				$key = $field;
				$field = (isset($this->fields[$field])) ? $this->fields[$field] : NULL;
			}

			if (!empty($field))
			{
				// if the field already exists, then we unset it to give it a natural order
				if (isset($this->fields[$key]))
				{
					unset($this->fields[$key]);
				}

				if (!is_null($order_start))
				{
					$field['order'] = $order_start + $i;	
				}
				
				$this->fields[$key] = $field;
				$i++;
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Reorder the fields
	 *
	 * @access	public
	 * @param	array 	The order of the fields
	 * @return	object 	Instance of Base_model_fields
	 */	
	public function reorder($order)
	{
		foreach($order as $key => $val)
		{
			$this->fields[$val]['order'] = $key + 1;
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get an iterator for the items.
	 *
	 * @access	public
	 * @return ArrayIterator
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new ArrayIterator($this->fields);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Count the number of items in the collection.
	 *
	 * @access	public
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->fields);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Determine if an item exists at an offset.
	 *
	 * @access	public
	 * @param  mixed  $key
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->fields);
	}

	// --------------------------------------------------------------------

	/**
	 * Get an item at a given offset.
	 *
	 * @access	public
	 * @param  mixed  $key
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($key)
	{
		$ref =& $this->fields[$key];
		return $ref;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set the item at a given offset.
	 *
	 * @access	public
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($key, $value)
	{
		if (is_null($key))
		{
			$this->fields[] = $value;
		}
		else
		{
			$this->fields[$key] = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Unset the item at a given offset.
	 *
	 * @access	public
	 * @param  string  $key
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($key)
	{
		unset($this->fields[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Magic method that will allow for mass assignment of a parameter across multiple fields.
	 *
	 * @access	public
	 * @param  string 	Method name
	 * @param  array 	The arguments to pass to the method   
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		if (preg_match( "/^set_(.*)/", $method, $found))
		{
			$arg1 = $args[0];
			$arg2 = $found[1];
			$arg3 = (isset($args[1])) ? $args[1] : NULL;
			$this->set($arg1, $arg2, $arg3);
			return $this;
		}
		else
		{
			throw new Exception("Invalid method call '$method' on Base_model_fields");
		}
		
	}
}


// ------------------------------------------------------------------------

/**
 * Base model validation class
 *
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 */
class Base_model_validation extends Abstract_base_model_helper  {

	protected $validator = NULL;

	public function __construct($record = array(), $parent_model = NULL)
	{
		parent::__construct();
		$this->set_parent_model($parent_model);
		$this->set_values($record);
		$this->validator =& $parent_model->get_validation();
		$this->initialize($this->get_values(), $this->get_parent_model());
	}

	// --------------------------------------------------------------------
	
	/**
	 * A placeholder for initialization of validation. This method will most likely be overwritten
	 *
	 * @access	public
	 * @return	void
	 */	
	public function initialize($values, $parent_model)
	{

	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the validator object initially.
	 *
	 * @access	public
	 * @param	array 	The validator to set
	 * @return	object 	Instance of Base_model_validation
	 */	
	public function set_validator($validator)
	{
		$this->validator = $validator;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the validator object.
	 *
	 * @access	public
	 * @return	object
	 */	
	public function get_validator()
	{
		return $this->validator;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds an error to the parent models validator object
	 *
	 * @access	public
	 * @return	object
	 */	
	public function add_error($msg, $key = NULL)
	{
		return $this->parent_model->add_error($msg, $key);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a validation rule
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	mixed	function name OR array($object_instance, $method)
	 * @param	string	error message to display
	 * @return	object 	Instance of Base_model_validation
	 */	
	public function add($field, $rule, $msg = '', $params = array())
	{
		$value = $this->get_value($field);
		if (is_array($rule) AND empty($msg))
		{
			foreach($rule as $r => $msg)
			{
				$args = $this->extract_args($field, $r, $params);
				$this->get_validator()->add_rule($field, $this->normalize_rule_func($r), $msg, $args);
			}
		}
		else
		{
			$args = $this->extract_args($field, $rule, $params);
			$this->get_validator()->add_rule($field, $this->normalize_rule_func($rule), $msg, $args);
		}
		
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Normalizes the the validation callback function
	 *
	 * @access	public
	 * @param	string	function name
	 * @return	mixed
	 */	
	protected function normalize_rule_func($rule)
	{
		if (method_exists($this, $rule))
		{
			return array($this, $rule);
		}
		if (is_array($rule))
		{
			$rule_parts = explode(':', $rule[1]);
			$rule[1] = current($rule_parts);
		}
		else
		{
			$rule_parts = explode(':', $rule);
			$rule = current($rule_parts);
		}
		return $rule;
	}

	// --------------------------------------------------------------------

	/**
	 * Remove a validation rule from the validator object
	 *
	 * @access	public
	 * @param	string	function name
	 * @return	array
	 */	
	protected function extract_args($field, $rule, $params = array())
	{
		if (is_string($params))
		{
			$params = array($params);
		}

		if (is_array($rule) AND !empty($rule[1]))
		{
			$args = explode(':', $rule[1]);
		}
		else
		{
			$args = explode(':', $rule);
		}
		array_shift($args);
		$value = $this->get_value($field);

		$args = array_merge(array($value), $args, $params);
		return $args;
	}

	// --------------------------------------------------------------------

	/**
	 * Remove a validation rule from the validator object
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	string	function name (optional)
	 * @return	array
	 */	
	public function remove($field, $rule = NULL)
	{
		$this->validator->remove_rule($field, $rule);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Run validation
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function validate()
	{
		$values = $this->get_values();
		return $this->validator->validate(array_keys($values));		
	}

	// --------------------------------------------------------------------

	/**
	 * Magic method that will allow for mass assignment of a parameter across multiple fields.
	 *
	 * @access	public
	 * @param  string 	Method name
	 * @param  array 	The arguments to pass to the method   
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		if (preg_match( "/^add_(.*)/", $method, $found))
		{
			$arg1 = $args[0];
			$arg2 = $found[1];
			$arg3 = (isset($args[1])) ? $args[1] : NULL;
			$this->add($arg1, $arg2, $arg3);
			return $this;
		}
		else
		{
			throw new Exception("Invalid method call '$method' on Base_model_validation");
		}
		
	}

}


// ------------------------------------------------------------------------

/**
 * Base related items class
 *
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 */
class Base_model_related_items extends Abstract_base_model_helper  {

	protected $record = NULL;
	protected $vars = array();
	protected $output = '';
	protected $display_if_new = TRUE;

	public function __construct($record = array(), $parent_model = NULL)
	{
		parent::__construct();
		$this->set_parent_model($parent_model);
		$this->set_values($record);
		$this->output = '';
		$this->initialize($this->get_values(), $this->get_parent_model());
	}

	// --------------------------------------------------------------------
	
	/**
	 * A placeholder for initialization of validation. This method will most likely be overwritten
	 *
	 * @access	public
	 * @return	void
	 */	
	public function initialize($values, $parent_model)
	{
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the variables for the view.
	 *
	 * @access	public
	 * @param	array	An array of variables to pass to a view (optional)
	 * @return	array
	 */	
	public function vars($vars = array())
	{
		$vars['values'] = $this->get_values();
		$vars['rec'] = $this->record();
		$vars['model'] = $this->get_parent_model();
		$vars['CI'] =& $this->CI;
		$vars['fuel'] =& $this->fuel;
		$vars['class'] = get_class($this->get_parent_model());
		$vars['ref'] =& $this;
		return $vars;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the view.
	 *
	 * @access	public
	 * @param	string	A view file. If inside an advanced module, use an array with the key being the module and the value being the view.
	 * @param	array	An array of variables to pass to a view (optional)
	 * @return	string
	 */	
	public function view($view, $vars = array())
	{
		$module = 'app';
		if (is_array($view))
		{
			$module = key($view);
			$view = current($view);
		}
		$vars = $this->vars($vars);
		$this->output = $this->CI->load->module_view($module, $view, $vars, TRUE);
		return $this->output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the view.
	 *
	 * @access	public
	 * @return	object 	Instance of Base_model_validation
	 */	
	public function set_output($output)
	{
		$this->output .= $output;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the view.
	 *
	 * @access	public
	 * @return	string
	 */	
	public function get_output()
	{
		return $this->output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders the output
	 *
	 * @access	public
	 * @param	string	A view file. If inside an advanced module, use an array with the key being the module and the value being the view.
	 * @param	array	An array of variables to pass to a view
	 * @return	string
	 */	
	public function render($view = '', $vars = array())
	{
		if (empty($this->display_if_new) AND !isset($vars['rec']->id))
		{
			return FALSE;
		}
		if (!empty($view))
		{
			$this->view($view, $vars);
		}
		return $this->output;
	}

}


// ------------------------------------------------------------------------

/**
 * Base related items class
 *
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 */
class Base_model_list_items extends Abstract_base_model_helper  {

	public $limit = NULL;
	public $offset = 0;
	public $col = 'id';
	public $order = 'asc';

	protected $display_type = '';
	protected $filters = NULL;
	protected $select = NULL;
	protected $joins = NULL;
	protected $search_field = NULL;
	protected $filter_join = NULL;
	protected $fields = NULL;
	protected $db;

	public function __construct($parent_model = NULL)
	{
		parent::__construct();
		$this->set_parent_model($parent_model);
		$this->fields = new Base_model_fields(array(), $this->get_values(), $this->get_parent_model());
		$this->db =& $this->get_parent_model()->db();
		if (!empty($this->filters)) $this->get_parent_model()->filters = $this->filters;
		if (!empty($this->display_type)) $this->CI->advanced_search = $this->display_type;
		if (!empty($this->search_field)) $this->CI->search_field = $this->search_field;
		if (!empty($this->filter_join)) $this->get_parent_model()->filter_join = $this->filter_join;

		$this->initialize($this->get_parent_model());
	}

	// --------------------------------------------------------------------
	
	/**
	 * A placeholder for initialization of validation. This method will most likely be overwritten
	 *
	 * @access	public
	 * @return	void
	 */	
	public function initialize($parent_model)
	{
		
	}

	public function fields($values = array())
	{
		return array();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds a filter for searching
	 *
	 * @access	public
	 * @param	string The name of the field to filter on
	 * @param	string A key to associate with the filter(optional)
	 * @return	void
	 */	
	public function add_filter($filter, $key = NULL)
	{
		$this->get_parent_model()->add_filter($filter, $key);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds multiple filters for searching
	 *
	 * @access	public
	 * @param	array An array of fields to filter on
	 * @return	void
	 */	
	public function add_filters($filters)
	{
		$this->get_parent_model()->add_filter($filters);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds a filter join such as "and" or "or" to a particular field
	 *
	 * @access	public
	 * @param	string The name of the field to filter on
	 * @param	string "and" or "or" (optional)
	 * @return	void
	 */	
	public function add_filter_join($field, $join_type = 'or')
	{
		$this->get_parent_model()->add_filter_join($field, $join_type);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds select values to the active record used for the list view
	 *
	 * @access	public
	 * @return	void
	 */	
	public function select()
	{

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds join values to the active record used for the list view
	 *
	 * @access	public
	 * @return	void
	 */	
	public function join()
	{

	}

	// --------------------------------------------------------------------
	
	/**
	 * Displays friendly text for what is being filtered on the list view
	 *
	 * @access	public
	 * @param	array The values applied to the filters
	 * @return	string The friendly text string
	 */	
	public function friendly_info($values)
	{
		$this->set_values($values);
	}

	// --------------------------------------------------------------------
	
	/**
	 * A method that can be used to do further manipulation on the data
	 *
	 * @access	public
	 * @param	array The array of data
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @return	array The data to return
	 */	
	public function process($data)
	{
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Runs the select and join methods
	 *
	 * @access	public
	 * @return	string
	 */	
	public function run()
	{
		$this->select();
		$this->join();
	}

}