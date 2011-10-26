<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Navigation_model extends Base_module_model {
	
	public $group_id = 1;
	public $required = array('label', 'group_id' => 'Please create a Navigation Group');
	public $filter_join = 'and';
	public $record_class = 'Navigation_item';
	
	function __construct()
	{
		parent::__construct('navigation');
		$this->required['group_id'] = lang('error_create_nav_group');
	}

	function find_by_location($location, $group_id = 1)
	{
		$where = array();
		$where[$this->_tables['navigation'].'.location'] = $location;
		if (!empty($group_id))
		{
			if (is_string($group_id))
			{
				$where[$this->_tables['navigation_groups'].'.name'] = $group_id;
			}
			else
			{
				$where['group_id'] = (int)$group_id;
			}
			$data = $this->find_one_array($where);
			
		}
		else
		{
			$data = $this->find_all_array($where);
			
		}
		return $data;
	}
	
	function tree($just_published = false)
	{
		$CI =& get_instance();
		$CI->load->helper('array');
		
		$data = array();

		$where = array();
		$group_id = (!empty($this->filters['group_id'])) ? $this->filters['group_id'] : $this->group_id;
		$where['group_id'] = $group_id;

		if ($just_published) $where['published'] =  'yes';
		$all_nav = $this->find_all_array_assoc('id', $where);

		$where = array();
		if (!empty($parent))
		{
			$parent = $this->find_one_array(array('location' => $parent));
			$where = array('group_id' => $group_id, 'parent_id' => $parent['id']);
		}
		else
		{
			$where = array('group_id' => $group_id);
		}
		$data = $this->find_all_array($where, 'precedence, location asc');
		$return = array();
		$i = 0;
		foreach($data as $key => $val)
		{
			$return[$key] = $val;

			if ($val['parent_id'] != 0) {
				if (empty($all_nav[$val['parent_id']]))
				{
					if (empty($return['_orphans']))
					{
						$return['_orphans'] = array('label' => '_orphans', 'parent_id' => 0, 'location' => null);
					}
					$return[$key]['parent_id'] = '_orphans';
				}
			}
			else
			{
				$return[$key]['parent_id'] = 0;
				
			}

			
			if ($val['published'] == 'no')
			{
				$return[$key]['attributes'] = array('class' => 'unpublished', 'title' => 'unpublished');
			}
			$return[$key]['location'] = fuel_url('navigation/edit/'.$val['id']);
		}
		//$return = array_sorter($return, 'label', 'asc');
		return $return;
	}
	
	function find_all_by_group($group_id = 1)
	{
		$where = (is_string($group_id)) ? array($this->_tables['navigation_groups'].'.name' => $group_id) : array($this->_tables['navigation'].'.group_id' => $group_id);
		$data = $this->find_all_array($where);
		return $data;
	}

	function children($parent, $group_id = 1)
	{
		$parent = $this->find_one_array(array('location' => $parent));
		
		$data = array();
		if (!empty($parent))
		{
			$where = (is_string($group_id)) ? array($this->_tables['navigation_groups'].'.name' => $group_id) : array($this->_tables['navigation'].'.group_id' => $group_id);
			$where['published'] = 'yes';
			$where['parent_id'] = $parent['id'];
			$data = $this->find_all_array($where);
		}
		return $data;
	}
	
	function root($group_id = 1)
	{
		$where = (is_string($group_id)) ? array($this->_tables['navigation_groups'].'.name' => $group_id) : array($this->_tables['navigation'].'.group_id' => $group_id);
		$where['published'] = 'yes';
		$where['parent_id'] = 0;
		$data = $this->find_all_array($where);
		$data = $this->find_all_array($where);
		return $data;
	} 
	
	function max_id()
	{
		$this->db->select_max('id');
		$query = $this->db->get($this->_tables['navigation']);
		$data = $query->row_array();
		return $data['id'];
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		$CI =& get_instance();
		// navigation group
		if (empty($CI->navigation_groups_model)){
			$CI->load->module_model(FUEL_FOLDER, 'navigation_groups_model');
		}
		$CI->load->helper('array');
		
		$group_options = options_list($CI->navigation_groups_model->find_all_array());
		$group_values = array_keys($group_options);
		$group_value = (!empty($group_values)) ? $group_values[0] : 1;

		$fields['group_id'] = array(
		'type' => 'select', 
		'options' => $group_options,
		'class' => 'add_edit navigation_group', 
		'comment' => 'The grouping of items you want to associate this navigation item to'
		);
		
		if (count($group_options) == 0)
		{
			$fields['group_id']['displayonly'] = TRUE;
		}

		if (empty($CI->pages_model))
		{
			$CI->load->module_model(FUEL_FOLDER, 'pages_model');
		}
		
		$this->load->helper('array');
		
		$parent_group = (!empty($values['group_id'])) ? $values['group_id'] : $group_value;
		$where['group_id'] = $parent_group;
		if (!empty($values['id']))
		{
			$where['id !='] = $values['id'];
			$where['parent_id !='] = $values['id'];
		}
		$parent_options = $this->options_list('id', 'nav_key', $where);
		$fields['parent_id']['label'] = lang('navigation_model_parent_id');
		$fields['parent_id']['type'] = 'select';
		$fields['parent_id']['options'] = $parent_options;
		$fields['parent_id']['first_option'] = array('0' => 'None');
		
		$yes = lang('form_enum_option_yes');
		$no = lang('form_enum_option_no');
		$fields['hidden']['options'] = array('yes' => $yes, 'no' => $no);
		
		return $fields;
	}
	
	
	// validation method
	function no_location_and_parent_match($parent_id)
	{
		$data = $this->find_one_array(array('fuel_navigation.id' => $parent_id));
		if (!empty($data))
		{
			if ($data['id'] == $data['parent_id']) return FALSE;
		}
		return TRUE;
	}

	// validation method
	/*function no_id_and_parent_match($id, $parent_id)
	{
		$data = $this->find_one_array(array('fuel_navigation.parent_id' => $id));
		if (!empty($data))
		{
			if ($data['id'] == $parent_id) return FALSE;
		}
		return TRUE;
	}*/
	
	// validation method
	function is_new_navigation($nav_key, $group_id)
	{
		if (empty($group_id)) return FALSE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'nav_key' => $nav_key));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// validation method
	function is_editable_navigation($nav_key, $group_id, $id)
	{
		$data = $this->find_one_array(array('group_id' => $group_id, 'nav_key' => $nav_key));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	// validation method
	function is_new_location($location, $group_id, $parent_id)
	{
		if (empty($group_id)) return FALSE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// validation method
	function is_editable_location($location, $group_id, $parent_id, $id)
	{
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	function on_before_clean($values)
	{
		if (empty($values['nav_key'])) $values['nav_key'] = $values['location'];
		
		// if the path is local, then we clean it
		if (!is_http_path($values['location']))
		{
			$values['location'] = str_replace(array('/', '#'), array('____', '___'), $values['location']);
			$values['location'] = url_title($values['location']);
			$values['location'] = str_replace(array('____', '___'), array('/', '#'), $values['location']);
		}
		return $values;
	}
		
	function on_before_validate($values)
	{
		$this->add_validation('parent_id', array(&$this, 'no_location_and_parent_match'), lang('error_location_parents_match'));
	//	$this->add_validation('id', array(&$this, 'no_id_and_parent_match'), lang('error_location_parents_match'), $values['parent_id']);
		
		if (!empty($values['id']))
		{
			$this->add_validation('nav_key', array(&$this, 'is_editable_navigation'), lang('error_val_empty_or_already_exists', lang('form_label_nav_key')), array($values['group_id'], $values['id']));
			$this->add_validation('location', array(&$this, 'is_editable_location'), lang('error_val_empty_or_already_exists', lang('form_label_location')), array($values['group_id'], $values['parent_id'], $values['id']));
		}
		else
		{
			$this->add_validation('nav_key', array(&$this, 'is_new_navigation'), lang('error_val_empty_or_already_exists', lang('form_label_nav_key')), array($values['group_id']));
			$this->add_validation('location', array(&$this, 'is_new_location'), lang('error_val_empty_or_already_exists', lang('form_label_location')), array($values['group_id'], $values['parent_id']));
		}
		return $values;
	}
	
	function _common_query()
	{
		parent::_common_query();
		$this->db->select($this->_tables['navigation'].'.*, '.$this->_tables['navigation_groups'].'.id group_id, '.$this->_tables['navigation_groups'].'.name group_name');
		$this->db->join($this->_tables['navigation_groups'], $this->_tables['navigation_groups'].'.id='.$this->_tables['navigation'].'.group_id', 'left');
		$this->db->order_by('precedence, location asc');
	}
	
	// used to get nested groups
	function get_others($display_field, $id, $val_field = NULL)
	{
		if (empty($val_field)) $val_field = $this->key_field;
		$data = $this->find_all_array_assoc('id');
		unset($data[$id]);
		$others = array();
		foreach($data as $d)
		{
			if (!isset($others[$d['group_name']])) $others[$d['group_name']] = array();
			$others[$d['group_name']][$d['id']] = $d['label'];
		}
		if (isset($others[$id])) unset($others[$id]);
		return $others;
	}
	
}

class Navigation_item_model extends Base_module_record {
}
