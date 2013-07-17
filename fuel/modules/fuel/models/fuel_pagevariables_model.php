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
 * <strong>Fuel_pagevariables_model</strong> is used for storing page variable data
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_pagevariables_model
 */

require_once('base_module_model.php');

class Fuel_pagevariables_model extends Base_module_model {

	public $page_id; // The page ID of the most recently queried page
	public $honor_page_status = FALSE; // Will look at the pages published status as well
	public $serialized_fields = array('value'); // The "value" field is serialized using JSON

	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_pagevars');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the page variables (not displayed in the CMS)
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data (optional)
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data (optional)
	 */	
	public function list_items($limit = NULL, $offset = NULL, $col = 'location', $order = 'desc', $just_count = FALSE)
	{
		$this->db->select($this->_tables['fuel_pagevars'].'.*, '.$this->_tables['fuel_pages'].'.layout, '.$this->_tables['fuel_pages'].'.location, '.$this->_tables['fuel_pages'].'.published AS page_published');
		$this->db->join($this->_tables['fuel_pages'], $this->_tables['fuel_pages'].'.id = '.$this->_tables['fuel_pagevars'].'.page_id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwrite: returns a single page variable record in an array format
	 *
	 * @access	public
	 * @param	mixed The where condition for the query
	 * @param	string The field name to order by (optional)
	 * @return	array
	 */	
	public function find_one_array($where, $order_by = NULL)
	{
		$data = parent::find_one_array($where, $order_by);
		if (!empty($data))
		{
			$data['value'] = $this->_process_casting($data);	
		}
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a single page variable record in an array format based on the page location value
	 *
	 * @access	public
	 * @param	string The page location
	 * @param	string The variable name
	 * @param	string The language associated with the variable (optional)
	 * @return	array
	 */	
	public function find_one_by_location($location, $name, $lang = NULL)
	{
		$where = array($this->_tables['fuel_pages'].'.location' => $location, 'name' => $name);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		$data = $this->find_one_array($where);
		return  $this->_process_casting($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a all page variable records in an array format based on the page location value
	 *
	 * @access	public
	 * @param	string The page location
	 * @param	string The language associated with the variable (optional)
	 * @param	boolean Determines whether to include the $pagevar object which includes all the page variables
	 * @return	array
	 */	
	public function find_all_by_location($location, $lang = NULL, $include_pagevars_object = FALSE)
	{
		$where = array($this->_tables['fuel_pages'].'.location' => $location);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}

		$data = array();
		if ($include_pagevars_object)
		{
			$objs = $this->find_all_assoc('name',$where);
			if (!empty($objs))
			{
				$data['pagevar'] = new Fuel_pagevar_helper();
				foreach($objs as $name => $obj)
				{
					$data[$name] = $this->_process_casting($obj);
					$data['pagevar']->$name = $obj;
				}
			}
		}
		else
		{
			$data = $this->find_all_array($where);
			$data = $this->_process_casting($data);
		}
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a single page variable record in an array format based on the pages ID value
	 *
	 * @access	public
	 * @param	string The page location
	 * @param	string The language associated with the variable (optional)
	 * @param	boolean Determines whether to include the $pagevar object which includes all the page variables
	 * @return	array
	 */	
	public function find_one_by_page_id($page_id, $name, $lang = NULL)
	{
		$this->page_id = $page_id;
		$where = array('page_id' => $page_id, 'name' => $name);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		
		$data = $this->find_one_array(array('page_id' => $page_id, 'name' => $name));
		return $this->_process_casting($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a all page variable records in an array format based on the page ID value
	 *
	 * @access	public
	 * @param	string The page location
	 * @param	string The language associated with the variable (optional)
	 * @param	boolean Determines whether to include the $pagevar object which includes all the page variables
	 * @return	array
	 */	
	public function find_all_by_page_id($page_id, $lang = NULL)
	{
		$this->page_id = $page_id;
		$where = array('page_id' => $page_id);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		
		$data = $this->find_all_array($where);
		return $this->_process_casting($data);;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Casts one or multiple page variables to their proper type
	 *
	 * @access	protected
	 * @param	mixed The data to be cast
	 * @return	mixed
	 */	
	protected function _process_casting($data)
	{
		if (is_array(current($data)))
		{
			$return = array();
			foreach ($data as $val)
			{
				if (is_object($data))
				{
					$value = $val->value;
					$type = $val->type;
				}
				else
				{
					$value = $val['value'];
					$type = $val['type'];
				}
				$return[$val['name']] = $this->cast($value, $type);
			}
			return $return;
		}
		else if (!empty($data))
		{
			if (is_object($data))
			{
				$value = $data->value;
				$type = $data->type;
			}
			else
			{
				$value = $data['value'];
				$type = $data['type'];
			}
			return $this->cast($value, $type);
		}
		else
		{
			return array();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Casts and unserializes if necessary a single variable to it's proper type (int, boolean, array)
	 *
	 * @access	protected
	 * @param	mixed The data to be cast
	 * @return	mixed
	 */	
	public function cast($val, $type)
	{
		$return = '';
		switch ($type){
			case 'int':
				$return = (int) $val;
				break;
			case 'boolean':
				$return = is_true_val($val);
				break;
			case 'array': case 'multi':
				if (is_string($val))
				{
					// for legacy versions
					if (is_serialized_str($val))
					{
						$return = unserialize($val);
					}
					else if ($json = json_decode($val, TRUE))
					{
						$return = $json;
					}
				}
				else if (is_array($val))
				{
					$return = $val;
				}
				if (empty($return))
				{
					$return = array();
				}
				break;
			default:
				$return = $val;
		}
		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add FUEL specific changes to the form_fields method
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array())
	{
		$CI =& get_instance();
		$fields = parent::form_fields($values, $related);
		
		//$fields['value']['value'] = (!empty($values['value'])) ? $this->cast($values['value'], $values['type']) : '';
		if (isset($values['page_id']))
		{
			$page = $CI->fuel->pages->find($values['page_id']);
			if (isset($page->id))
			{
				$layout = $this->fuel->layouts->get($page->layout);
				$layout_fields = $layout->fields();
				if (isset($layout_fields[$values['name']]))
				{
					$fields['value'] = $layout_fields[$values['name']];
					$fields['value']['name'] = 'value';
				}
			}
		}
		// not needed due to on_before_clean
		unset($fields['type']);
		return $fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook right before the data is cleaned. Determines type of variable
	 *
	 * @access	public
	 * @param	array The values to be saved right the clean method is run
	 * @return	array Returns the values to be cleaned
	 */	
	public function on_before_clean($values)
	{
		if (isset($values['value']))
		{
			$values['type'] = $this->determine_type($values['value']);
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Used by on_before_clean model hook to determine the type of variable (int, string, array)
	 *
	 * @access	public
	 * @param	array The values to be determined
	 * @return	string
	 */	
	public function determine_type($value)
	{
		if (is_array($value) OR is_serialized_str($value))
		{
			return 'array';
		}
		return 'string';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Common query that joins user page information with the page variable data
	 *
	 * @access	public
	 * @param mixed parameter to pass to common query (optional)
	 * @return	void
	 */	
	public function _common_query($params = NULL)
	{
		$CI =& get_instance();
		$lang_options = $CI->fuel->config('languages');
		
		$this->db->select($this->_tables['fuel_pagevars'].'.*, '.$this->_tables['fuel_pages'].'.layout, '.$this->_tables['fuel_pages'].'.location, '.$this->_tables['fuel_pages'].'.published AS page_published');
		$this->db->join($this->_tables['fuel_pages'], $this->_tables['fuel_pages'].'.id = '.$this->_tables['fuel_pagevars'].'.page_id', 'left');
		$this->db->where(array($this->_tables['fuel_pagevars'].'.active' => 'yes'));
		if ($this->honor_page_status AND !defined('FUEL_ADMIN'))
		{
			$this->db->where(array($this->_tables['fuel_pages'].'.published' => 'yes'));
		}
	}
}

// ------------------------------------------------------------------------

/**
 * FUEL page variable record object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @prefix		$var->
 */
class Fuel_pagevariable_model extends Data_record {

	// --------------------------------------------------------------------
	
	/**
	 * Magic method that returns the value data if the object is echoed (e.g. $pagevar->h1 == $pagevar=>h1->value)
	 *
	 * @access	public
	 * @param	object	method name
	 * @param	array	arguments
	 * @return	array
	 */	
	public function __toString()
	{
		return $this->value;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the value of the page variable
	 *
	 * @access	public
	 * @param	object	method name
	 * @param	array	arguments
	 * @return	array
	 */	
	public function get_value()
	{
		return $this->_parent_model->cast($this->_fields['value'], $this->type);
	}	
}

// ------------------------------------------------------------------------

/**
 * The Fuel_pagevar_helper object.
 * 
 * Class used for accessing page variable field values easier
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @prefix		$var->
 */
// --------------------------------------------------------------------
class Fuel_pagevar_helper {

	protected $_vars = array();
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method for capturing method calls on the record object that don't exist.
	 *
	 * @access	public
	 * @param	object	method name
	 * @param	array	arguments
	 * @return	array
	 */	
	public function __call($method, $args)
	{
		// // take the field name plus a '_' to get the suffix
		$suffix = substr(strrchr($method, '_'), 1);

		// get the core field name without the suffix (+1 because of underscore)
		$field = substr($method, 0, - (strlen($suffix) + 1));
		return $this->_vars[$field]->format('value', $suffix, $args);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method to set variable object
	 * @access	public
	 * @param	string	field name
	 * @param	mixed	
	 * @return	void
	 */	
	public function __set($var, $val)
	{
		$this->_vars[$var] = $val;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method to return variable object
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function __get($var)
	{
		$output = NULL;
		
		// finally check values from the database
		if (array_key_exists($var, $this->_vars))
		{
			$output = $this->_vars[$var];
		}
		else
		{
			// take the field name plus a '_' to get the suffix
			$suffix = substr(strrchr($var, '_'), 1);

			// get the core field name without the suffix (+1 because of underscore)
			$field = substr($var, 0, - (strlen($suffix) + 1));

			// apply formatting to the value
			$output = $this->_vars[$field]->format('value', $suffix);
		}
		
		return $output;
	}
}
