<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
require_once('base_module_model.php');

class Pagevariables_model extends Base_module_model {

	public $page_id;
	public $honor_page_status = FALSE; // will look at the pages published status as well
	
	function __construct()
	{
		parent::__construct('pagevars');
	}
	
	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'location', $order = 'desc')
	{
		$this->db->select($this->_tables['pagevars'].'.*, '.$this->_tables['pages'].'.layout, '.$this->_tables['pages'].'.location, '.$this->_tables['pages'].'.published AS page_published');
		$this->db->join($this->_tables['pages'], $this->_tables['pages'].'.id = '.$this->_tables['pagevars'].'.page_id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	/* OVERWRITE */
	function find_one_array($where, $order_by = NULL)
	{
		$data = parent::find_one_array($where, $order_by);
		$data['value'] = $this->_process_casting($data);
		return $data;
	}

	function find_one_by_location($location, $name, $lang = NULL)
	{
		$where = array($this->_tables['pages'].'.location' => $location, 'name' => $name);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		$data = $this->find_one_array($where);
		return  $this->_process_casting($data);
	}
	
	function find_all_by_location($location, $lang = NULL)
	{
		$where = array($this->_tables['pages'].'.location' => $location);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		$data = $this->find_all_array($where);
		return $this->_process_casting($data);
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
		
		$fields['value']['value'] = (!empty($values['value'])) ? $this->cast($values['value'], $values['type']) : '';
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
				}
			}
		}
		return $fields;
	}
	
	function on_before_clean($values)
	{
		if (isset($values['value']))
		{
			if (is_array($values['value']))
			{
				//$values['value'] = serialize($values['value']);
				$values['value'] = json_encode($values['value']);
				$values['type'] = 'array';
			}
			else if (is_serialized_str($values['value']))
			{
				$values['type'] = 'array';
			}
		}
		return $values;
	}
	
	function _common_query()
	{
		$CI =& get_instance();
		$lang_options = $CI->fuel->config('languages');
		
		$this->db->select($this->_tables['pagevars'].'.*, '.$this->_tables['pages'].'.layout, '.$this->_tables['pages'].'.location, '.$this->_tables['pages'].'.published AS page_published');
		$this->db->join($this->_tables['pages'], $this->_tables['pages'].'.id = '.$this->_tables['pagevars'].'.page_id', 'left');
		$this->db->where(array($this->_tables['pagevars'].'.active' => 'yes'));
		if ($this->honor_page_status AND !defined('FUEL_ADMIN'))
		{
			$this->db->where(array($this->_tables['pages'].'.published' => 'yes'));
		}
		
		
	}

}


class Pagevariable_model extends Data_record {
}
