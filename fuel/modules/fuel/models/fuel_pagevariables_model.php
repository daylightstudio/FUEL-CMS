<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
require_once('base_module_model.php');

class Fuel_pagevariables_model extends Base_module_model {

	public $page_id;
	public $honor_page_status = FALSE; // will look at the pages published status as well
	public $serialized_fields = array('value');
	function __construct()
	{
		parent::__construct('fuel_pagevars');
	}
	
	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'location', $order = 'desc')
	{
		$this->db->select($this->_tables['fuel_pagevars'].'.*, '.$this->_tables['fuel_pages'].'.layout, '.$this->_tables['fuel_pages'].'.location, '.$this->_tables['fuel_pages'].'.published AS page_published');
		$this->db->join($this->_tables['fuel_pages'], $this->_tables['fuel_pages'].'.id = '.$this->_tables['fuel_pagevars'].'.page_id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	/* OVERWRITE */
	function find_one_array($where, $order_by = NULL)
	{
		$data = parent::find_one_array($where, $order_by);
		if (!empty($data))
		{
			$data['value'] = $this->_process_casting($data);	
		}
		return $data;
	}

	function find_one_by_location($location, $name, $lang = NULL)
	{
		$where = array($this->_tables['fuel_pages'].'.location' => $location, 'name' => $name);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		$data = $this->find_one_array($where);
		return  $this->_process_casting($data);
	}
	
	function find_all_by_location($location, $lang = NULL, $include_pagevars_object = FALSE)
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
	
	function find_one_by_page_id($page_id, $name, $lang = NULL)
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
	
	function find_all_by_page_id($page_id, $lang = NULL)
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
	
	function _process_casting($data)
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
	
	function cast($val, $type)
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
	
	function form_fields($values = array(), $related = array())
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
	
	function on_before_clean($values)
	{
		if (isset($values['value']))
		{
			$values['type'] = $this->determine_type($values['value']);
		}
		return $values;
	}

	function determine_type($value)
	{
		if (is_array($value) OR is_serialized_str($value))
		{
			return 'array';
		}
		return 'string';
	}

	function _common_query()
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


class Fuel_pagevariable_model extends Data_record {

	function __toString()
	{
		return $this->value;
	}

	function get_value()
	{
		return $this->_parent_model->cast($this->_fields['value'], $this->type);
	}	
}


// --------------------------------------------------------------------
	
/**
 * Class used for accessing field values easier
 *
 */	
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
