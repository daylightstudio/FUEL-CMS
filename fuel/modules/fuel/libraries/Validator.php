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
 * An alternative to the CI Validation class
 *
 * This class is used in MY_Model and the Form class. Does not require
 * post data and is a little more generic then the CI Validation class.
 * The <a href="[user_guide_url]helpers/validator_helper">validator_helper</a> 
 * that contains many helpful rule functions is automatically loaded.
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/validator
 */

class Validator {
	
	public $field_error_delimiter = "\n"; // delimiter for rendering multiple errors for a field
	public $stack_field_errors = FALSE; // stack multiple field errors if any or just replace with the newest
	public $register_to_global_errors = TRUE; // will add to the globals error array
	public $load_helpers = TRUE; // will automatically load the validator helpers
	protected $_fields = array(); // fields to validate
	protected $_errors = array(); // errors after running validation
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct ($params = array()) {
		if (!defined('GLOBAL_ERRORS'))
		{
			define('GLOBAL_ERRORS', '__ERRORS__');
		}
		if (!isset($GLOBALS[GLOBAL_ERRORS])) $GLOBALS[GLOBAL_ERRORS] = array();

		if (function_exists('get_instance') && $this->load_helpers)
		{
			$CI =& get_instance();
			$CI->load->helper('validator');
		}
	
		if (count($params) > 0)
		{
			$this->initialize($params);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($params = array())
	{
		$this->reset();
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function set_params($params)
	{
		if (is_array($params) AND count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}		
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add processing rule (function) to an input variable. 
	 * The <a href="[user_guide_url]helpers/validator_helper">validator_helper</a> contains many helpful rule functions you can use
	 *
	 * @access public
	 * @param string key in processing array to assign to rule. Often times its the same name as the field input
	 * @param string error message
	 * @param string function for processing
	 * @param mixed function arguments with the first usually being the posted value. If multiple arguments need to be passed, then you can use an array.
	 * @return void
	 */
	public function add_rule($field, $func, $msg, $params = array())
	{
		if (empty($fields[$field])) $fields[$field] = array();
		settype($params, 'array');
		
		// if params are emtpy then we will look in the $_POST
		if (empty($params))
		{
			if (!empty($_POST[$field])) $params = $_POST[$field];
		}
		$rule = new Validator_Rule($func, $msg, $params);
		$this->_fields[$field][] = $rule;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Removes rule from validation
	 * 
	 * @access public
	 * @param string field to remove
	 * @param string key for rule (can have more then one rule for a field) (optional)
	 * @return void
	 */
	public function remove_rule($field, $func = NULL)
	{
		if (!empty($func))
		{
			foreach($this->_fields[$field] as $key => $rule)
			{
				if ($rule->func == $func)
				{
					unset($this->_fields[$field][$key]);
				}
			}
		}
		else
		{
			// remove all rules
			unset($this->_fields[$field]);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Runs through validation
	 * 
	 * @access public
	 * @param array assoc array of values to validate (optional)
	 * @param boolean exit on first error? (optional)
	 * @param boolean reset validation errors (optional)
	 * @return boolean
	 */
	public function validate($values = array(), $stop_on_first = FALSE, $reset = TRUE)
	{
		// reset errors to start with a fresh validation
		if ($reset) $this->_errors = array();
		
		//if (empty($values)) $values = $_POST;
		if (empty($values))
		{
			$values = array_keys($this->_fields);
		}
		else if (!array_key_exists(0, $values)) // detect if it is an associative array and if so just use keys
		{
			 $values = array_keys($values);
		}

		foreach($values as $key)
		{
			if (!empty($this->_fields[$key]))
			{
				$rules = $this->_fields[$key];
				foreach($rules as $key2 => $val2)
				{
					$ok = $val2->run();
					if (!$ok)
					{
						$this->catch_error($val2->get_message(), $key);
						if (!empty($stop_on_first)) return FALSE;
					}
				}
			}
		}
		return $this->is_valid();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Checks to see if it validates
	 *
	 * @access public
	 * @return boolean
	 */
	public function is_valid()
	{
		return (count($this->_errors) <= 0);
	}

	// --------------------------------------------------------------------

	/**
	 * Catches error into the global array
	 *
	 * @access public
	 * @param string msg error message
	 * @param mixed key to identify error message
	 * @return string key of variable input
	 */
	public function catch_error($msg, $key = NULL)
	{
	    if (empty($key)) $key = count($this->_errors);
		if ($this->stack_field_errors)
		{
			$this->_errors[$key] = (!empty($this->_errors[$key])) ? $this->_errors[$key] = $this->_errors[$key].$this->field_error_delimiter.$msg : $msg;
		}
		else
		{
			$this->_errors[$key] = $msg;
		}
		if ($this->register_to_global_errors)
		{
			$GLOBALS[GLOBAL_ERRORS][$key] = $this->_errors[$key];
		}
		return $key;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Catches multiple errors
	 *
	 * @access public
	 * @param array of error messages
	 * @param key of error message if a single message
	 * @return string key of variable input
	 */
	public function catch_errors($errors, $key = NULL)
	{
	    if (is_array($errors))
		{
			foreach($errors as $key => $val)
			{
				if (is_int($key))
				{
					$this->catch_error($val);
				}
				else
				{
					$this->catch_error($val, $key);
				}
			}
		}
		else
		{
			return $this->catch_error($errors, $key);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Retrieve errors
	 *
	 * @access public
	 * @return assoc array of errors and messages
	 */
	public function get_errors()
	{
		return $this->_errors;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Retrieves a single error
	 *
	 * @access public
	 * @param mixed key to error message
	 * @return string error message
	 */
	public function get_error($key)
	{
		if (!empty($this->_errors[$key]))
		{
			return $this->_errors[$key];
		}
		else
		{
			return NULL;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Retrieves the last error message
	 *
	 * @access public
	 * @return string error message
	 */
	public function get_last_error()
	{
		if (!empty($this->_errors))
		{
			return end($this->_errors);
		}
		else
		{
			return NULL;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the fields with rules
	 * @access public
	 * @return array
	 */
	public function fields()
	{
		return $this->_fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Same as reset
	 * 
	 * @access	public
	 * @return	void
	 */
	public function clear()
	{
		$this->reset();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Resets rules and errors
	 * @access public
	 * @return void
	 */
	public function reset($remove_fields = TRUE)
	{
		$this->_errors = array();
		if ($remove_fields)
		{
			$this->_fields = array();
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Validation rule object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Validator_Rule {
	public $func; // function to execute that will return TRUE/FALSE
	public $msg; // message to be display on error
	public $args; // arguments to pass to the function
	
	/**
	 * Validator rule constructor
	 * 
	 * @param string function to execute that will return TRUE/FALSE
	 * @param string message to be display on error
	 * @param mixed arguments to pass to the function
	 */
	public function __construct($func, $msg, $args)
	{
		$this->func = $func;
		$this->msg = $msg;
		
		if (!is_array($args))
		{
			$this->args[] = $args;
		}
		else if (empty($args))
		{ // create first argument
			
			$this->args[] = '';
		}
		else
		{
			$this->args = $args;
		}
	}
	
	/**
	 * Runs the rules function
	 *
	 * @access public
	 * @return boolean (should return TRUE/FALSE but it depends on the function of course)
	 */
	public function run()
	{
		return call_user_func_array($this->func, $this->args);
	}
	
	/**
	 * Retrieve errors
	 *
	 * @access public
	 * @return string error message
	 */
	public function get_message()
	{
		return $this->msg;
	}
}
/* End of file Validator.php */
/* Location: ./modules/fuel/libraries/Validator.php */