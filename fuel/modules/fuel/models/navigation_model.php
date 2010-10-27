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
		$this->add_validation('parent_id', array(&$this, 'no_location_and_parent_match'), lang('error_location_parents_match'));
		$this->required['group_id'] = lang('error_create_nav_group');
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'location', $order = 'asc')
	{
		$this->db->select($this->_tables['navigation'].'.id, label, nav_key,'.$this->_tables['navigation'].'.published');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function find_by_location($location, $group_id = 1)
	{
		$this->db->select($this->_tables['navigation'].'.*, '.$this->_tables['navigation_groups'].'.name AS group_name');
		$this->db->join($this->_tables['navigation_groups'], $this->_tables['navigation_groups'].'.id = fuel_navigation.group_id', 'left');
		
		if (!empty($group_id))
		{
			$data = $this->find_one_array(array($this->_tables['navigation'].'.location' => $location, 'group_id' => $group_id));
		}
		else
		{
			$data = $this->find_all_array(array($this->_tables['navigation'].'.location' => $location));
		}
		return $data;
	}
	
	function tree($just_published = false)
	{
		$CI =& get_instance();
		$CI->load->helper('array');
		
		$data = array();

		$where = array();
		$where['group_id'] = $this->group_id;
		if ($just_published) $where['published'] =  'yes';
		$all_nav = $this->find_all_array_assoc('id', $where);

		$where = array();
		if (!empty($parent))
		{
			$parent = $this->find_one_array(array('location' => $parent));
			$where = array('group_id' => $this->group_id, 'parent_id' => $parent['id']);
		}
		else
		{
			$where = array('group_id' => $this->group_id);
		}
		$data = $this->find_all_array($where, 'precedence desc');
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
		$return = array_sorter($return, 'label', 'asc');
		return $return;
	}
	
	function children($parent, $group_id = 1)
	{
		$parent = $this->find_one_array(array('location' => $parent));
		
		$data = array();
		if (!empty($parent)){
			$data = $this->find_all_array(array('group_id' => $group_id, 'parent_id' => $parent['id'], 'published' => 'yes'));
		}
		return $data;
	}
	
	function root($group_id = 1)
	{
		$data = $this->find_all_array(array('group_id' => $group_id, 'parent_id' => 0, 'published' => 'yes'));
		return $data;
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
		'label' => 'Navigation Group',
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
		$parent_options = $this->options_list('id', 'nav_key');
		$fields['parent_id']['label'] = 'Parent';
		$fields['parent_id']['type'] = 'select';
		$fields['parent_id']['options'] = $parent_options;
		$fields['parent_id']['first_option'] = array('0' => 'None');
		$fields['published']['label_layout'] = 'left';
		
		return $fields;
	}
	
	
	// validation method
	function no_location_and_parent_match($parent_id)
	{
		$data = $this->find_one_array(array('fuel_navigation.id' => $parent_id));
		if (!empty($data)){
			if ($data['id'] == $data['parent_id']) return FALSE;
		}
		return TRUE;
	}
	
	// validation method
	function is_editable_navigation($location, $group_id, $parent_id, $id)
	{
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	function on_before_clean($values)
	{
		if (empty($values['nav_key'])) $values['nav_key'] = $values['location'];

		return $values;
	}
	
	function on_before_validate($values)
	{
		if (!empty($values['id']))
		{
			$this->add_validation('location', array(&$this, 'is_editable'), lang('error_val_empty_or_already_exists', 'location'), array('location', $values['id']));
			$this->add_validation('nav_key', array(&$this, 'is_editable'), lang('error_val_empty_or_already_exists', 'nav_key'), array('nav_key', $values['id']));
		}
		else
		{
			$this->add_validation('nav_key', array(&$this, 'is_new'), lang('error_val_empty_or_already_exists', 'nav_key'), 'nav_key');
		}
		return $values;
	}
	
	function _common_query()
	{
		$this->db->order_by('precedence, location asc');
	}
}

class Navigation_item_model extends Base_module_record {
}
