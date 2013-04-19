<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Fuel_navigation_model extends Base_module_model {
	
	public $group_id = 1;
	public $required = array('label', 'group_id' => 'Please create a Navigation Group');
	public $filter_join = array('label' => 'or', 'location' => 'or', 'group_id' => 'and');
	public $record_class = 'Fuel_navigation_item';
	public $ignore_replacement = array('nav_key');
	public $filters = array('label', 'location');
	public $linked_fields = array('nav_key' => array('location' => 'mirror'));
	public $boolean_fields = array('hidden');

	function __construct()
	{
		parent::__construct('fuel_navigation');
		$this->required['group_id'] = lang('error_create_nav_group');
	}

	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'nav_key', $order = 'desc')
	{
		$CI =& get_instance();
		if ($CI->fuel->language->has_multiple())
		{
			$this->db->select('id, label, if (nav_key != "", nav_key, location) AS location, precedence, language, hidden, published', FALSE);
		}
		else
		{
			$this->db->select('id, label, if (nav_key != "", nav_key, location) AS location, precedence, hidden, published', FALSE);	
		}
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function find_by_location($location, $group_id = 1, $lang = NULL)
	{
		$where[$this->_tables['fuel_navigation'].'.location'] = $location;
		return $this->_find_by_array($where, $group_id, $lang);
	}

	function find_by_nav_key($nav_key, $group_id = 1, $lang = NULL)
	{
		$where[$this->_tables['fuel_navigation'].'.nav_key'] = $nav_key;
		return $this->_find_by_array($where, $group_id, $lang);
	}
	
	protected function _find_by_array($where, $group_id = 1, $lang = NULL)
	{
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		if (!empty($group_id))
		{
			if (is_string($group_id))
			{
				$where[$this->_tables['fuel_navigation_groups'].'.name'] = $group_id;
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
	
	function find_all_by_group($group_id = 1, $lang = NULL, $assoc_key = NULL)
	{
		$where = (is_string($group_id)) ? array($this->_tables['fuel_navigation_groups'].'.name' => $group_id) : array($this->_tables['fuel_navigation'].'.group_id' => $group_id);
		if (!empty($lang))
		{
			$where['language'] = $lang;
		}
		if (!empty($assoc_key))
		{
			$data = $this->find_all_array_assoc($assoc_key, $where);	
		}
		else
		{
			$data = $this->find_all_array($where);
		}
		
		return $data;
	}

	function max_id()
	{
		$this->db->select_max('id');
		$query = $this->db->get($this->_tables['fuel_navigation']);
		$data = $query->row_array();
		return $data['id'];
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		$CI =& get_instance();
		// navigation group
		if (empty($CI->fuel_navigation_groups_model)){
			$CI->load->module_model(FUEL_FOLDER, 'fuel_navigation_groups_model');
		}
		$CI->load->helper('array');
		
		$group_options = $CI->fuel_navigation_groups_model->options_list();
		$group_values = array_keys($group_options);
		$group_value = (!empty($group_values)) ? $group_values[0] : 1;
		
		$fields['group_id'] = array(
			'type' => 'inline_edit', 
			'module' => 'navigation_group',
			'options' => $group_options,
			'type' => 'select',
	//		'class' => 'add_edit navigation_group', 
			'comment' => 'The grouping of items you want to associate this navigation item to'
		);
		
		if (count($group_options) == 0)
		{
			$fields['group_id']['displayonly'] = TRUE;
		}

		if (empty($CI->fuel_pages_model))
		{
			$CI->load->module_model(FUEL_FOLDER, 'fuel_pages_model');
		}
		
		$this->load->helper('array');
		
		$parent_group = (!empty($values['group_id'])) ? $values['group_id'] : $group_value;
		$where['group_id'] = $parent_group;
		if (!empty($values['id']))
		{
			$where['id !='] = $values['id'];
			$where['parent_id !='] = $values['id'];
		}
		$parent_options = $this->options_list('id', 'nav_key', $where, TRUE, FALSE);
		$fields['parent_id']['label'] = lang('navigation_model_parent_id');
		$fields['parent_id']['type'] = 'select';
		$fields['parent_id']['options'] = $parent_options;
		$fields['parent_id']['first_option'] = array('0' => 'None');
		
		$yes = lang('form_enum_option_yes');
		$no = lang('form_enum_option_no');
		$fields['hidden']['options'] = array('yes' => $yes, 'no' => $no);
		
		// set language field
		if ($CI->fuel->language->has_multiple())
		{
			$fields['language'] = array('type' => 'select', 'options' => $CI->fuel->language->options());
		}
		else
		{
			$fields['language'] = array('type' => 'hidden', 'value' => $CI->fuel->language->default_option());
		}

		$fields['nav_key']['type'] = 'linked';
		$fields['nav_key']['linked_to'] = array('location' => 'mirror');

		// set order
		$fields['general_tab'] = array('type' => 'fieldset', 'label' => 'General', 'class' => 'tab', 'order' => 1);
		$fields['advanced_tab'] = array('type' => 'fieldset', 'label' => 'Advanced', 'class' => 'tab', 'order' => 5);

		$order = array(	'general_tab', 
						'group_id', 
						'label', 
						'location', 
						'nav_key', 
						'parent_id', 
						'published', 
						'language',
						'advanced_tab', 
						'precedence', 
						'attributes', 
						'selected', 
						'hidden'
						);
		foreach($order as $key => $val)
		{
			$fields[$val]['order'] = $key + 1;
		}
		
		return $fields;
	}
	
	
	// validation method
	function no_location_and_parent_match($parent_id)
	{
		$data = $this->find_one_array(array($this->_tables['fuel_navigation'].'.id' => $parent_id));
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
	function is_new_navigation($nav_key, $group_id, $lang)
	{
		if (empty($group_id)) return FALSE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'nav_key' => $nav_key, 'language' => $lang));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// validation method
	function is_editable_navigation($nav_key, $group_id, $id, $lang)
	{
		$data = $this->find_one_array(array('group_id' => $group_id, 'nav_key' => $nav_key, 'language' => $lang));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	// validation method
	function is_new_location($location, $group_id, $parent_id, $lang)
	{
		if (empty($group_id)) return FALSE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id, 'language' => $lang));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// validation method
	function is_editable_location($location, $group_id, $parent_id, $id, $lang)
	{
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id, 'language' => $lang));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	function on_before_clean($values)
	{
		//if (empty($values['nav_key'])) $values['nav_key'] = $values['location'];
		
		// if the path is local, then we clean it
		if (!is_http_path($values['location']))
		{
			$values['location'] = str_replace(array('/', '#'), array('____', '___'), $values['location']);
			$values['location'] = url_title($values['location']);
			$values['location'] = str_replace(array('____', '___'), array('/', '#'), $values['location']);
		}

		if (empty($values['language']))
		{
			$CI =& get_instance();
			$values['language'] = $CI->fuel->language->default_option();
		}
		return $values;
	}
		
	function on_before_validate($values)
	{
		$this->add_validation('parent_id', array(&$this, 'no_location_and_parent_match'), lang('error_location_parents_match'));
	//	$this->add_validation('id', array(&$this, 'no_id_and_parent_match'), lang('error_location_parents_match'), $values['parent_id']);
		
		if (!empty($values['id']))
		{
			$this->add_validation('nav_key', array(&$this, 'is_editable_navigation'), lang('error_val_empty_or_already_exists', lang('form_label_nav_key')), array($values['group_id'], $values['id'], $values['language']));
			$this->add_validation('location', array(&$this, 'is_editable_location'), lang('error_val_empty_or_already_exists', lang('form_label_location')), array($values['group_id'], $values['parent_id'], $values['id'], $values['language']));
		}
		else
		{
			$this->add_validation('nav_key', array(&$this, 'is_new_navigation'), lang('error_val_empty_or_already_exists', lang('form_label_nav_key')), array($values['group_id'], $values['language']));
			$this->add_validation('location', array(&$this, 'is_new_location'), lang('error_val_empty_or_already_exists', lang('form_label_location')), array($values['group_id'], $values['parent_id'], $values['language']));
		}
		return $values;
	}
	
	function _common_query()
	{
		parent::_common_query();
		$this->db->select($this->_tables['fuel_navigation'].'.*, '.$this->_tables['fuel_navigation_groups'].'.id group_id, '.$this->_tables['fuel_navigation_groups'].'.name group_name');
		$this->db->join($this->_tables['fuel_navigation_groups'], $this->_tables['fuel_navigation_groups'].'.id='.$this->_tables['fuel_navigation'].'.group_id', 'left');
		$this->db->order_by('precedence, location asc');
	}
	
	
	// overwritten so we can group items
	function options_list($key = 'id', $val = 'label', $where = array(), $order = TRUE, $group = TRUE)
	{
		if (!empty($order) AND is_bool($order))
		{
			$this->db->order_by($val, 'asc');
		} 
		else if (!empty($order) AND is_string($order))
		{
			if (strpos($order, ' ') === FALSE) $order .= ' asc';
			$this->db->order_by($order);
		}

		if ($group)
		{
			// need to turn this off to get the proper ordering
			$data = $this->find_all_array_assoc($key, $where);
	
			return $this->_group_options($data, $key, $val);
		}
		else
		{
			return parent::options_list($key, $val, $where, $order);
		}

	}
	
	
	// used to get nested groups
	function get_others($display_field, $id, $val_field = NULL)
	{
		$others = $this->find_all_array_assoc('id');

		// COMMENTED OUT BECAUSE WE DISABLE IT IN THE DROPDOWN INSTEAD
		//if (isset($others[$id])) unset($others[$id]);
		$others = $this->_group_options($others);
		//if (isset($others[$id])) unset($others[$id]);
		return $others;
	}
	
	// group the options together
	protected function _group_options($data, $key = 'id', $val = 'label')
	{
		$options = array();
		foreach($data as $d)
		{
			if (!isset($options[$d['group_name']])) $options[$d['group_name']] = array();
			if ($val == 'label')
			{
				$options[$d['group_name']][$d[$key]] = $d['nav_key'].' ('. $d[$val].')';
			}
			else
			{
				$options[$d['group_name']][$d[$key]] = $d[$val].'';
			}
			
		}
		unset($data);
		return $options;
	}
}

class Fuel_navigation_item_model extends Base_module_record {
}
