<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Pagevariables_model extends MY_Model {

	public $page_id;
	private $_tables;
	
	function __construct()
	{
		$CI =& get_instance();
		$CI->config->module_load(FUEL_FOLDER, 'fuel', TRUE);
		$this->_tables = $CI->config->item('tables', 'fuel');
		parent::__construct($this->_tables['pagevars']);
	}
	
	function find_one_by_location($location, $name)
	{
		$data = $this->find_all_array(array($this->_tables['pages'].'.location' => $location, 'name' => $name));
		return  $this->_process_casting($data);
	}
	
	function find_all_by_location($location)
	{
		$data = $this->find_all_array(array($this->_tables['pages'].'.location' => $location));
		return $this->_process_casting($data);
	}
	
	function find_one_by_page_id($page_id, $name)
	{
		$this->page_id = $page_id;
		$data = $this->find_one_array(array('page_id' => $page_id, 'name' => $name));
		return $this->_process_casting($data);
	}
	
	function find_all_by_page_id($page_id)
	{
		$this->page_id = $page_id;
		$data = $this->find_all_array(array('page_id' => $page_id));
		return $this->_process_casting($data);
	}
	
	function _process_casting($data)
	{
		if (is_array(current($data)))
		{
			$return = array();
			foreach ($data as $val)
			{
				$return[$val['name']] = $this->cast($val['value'], $val['type']);
			}
			return $return;
		}
		else if (!empty($data))
		{
			return $this->cast($data['value'], $data['type']);
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
				$return = (is_serialized_str($val)) ? unserialize($val) : array();
				break;
			default:
				$return = $val;
		}
		return $return;
	}
	
	function _common_query()
	{
		$this->db->join($this->_tables['pages'], $this->_tables['pages'].'.id = '.$this->_tables['pagevars'].'.page_id', 'left');
	}

}


class Pagevariable_model extends Data_record {
}
