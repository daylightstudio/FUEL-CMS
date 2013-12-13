<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * <strong>Fuel_navigation_model</strong> is used for managing FUEL users in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_navigation_model
 */

require_once('base_module_model.php');

class Fuel_navigation_model extends Base_module_model {
	
	public $group_id = 1; // The default navigation group ID
	public $required = array('label', 'group_id' => 'Please create a Navigation Group'); // The label and group_id are required fields
	public $filters = array('label', 'location'); // The label and location 
	public $filter_join = array('label' => 'or', 'location' => 'or', 'group_id' => 'and'); // The search filters will look in label OR location from within a specified group_id
	public $record_class = 'Fuel_navigation_item'; // The name of the record class
	public $ignore_replacement = array('nav_key'); // The "nav_key" will be ignored upon replacement
	public $linked_fields = array('nav_key' => array('location' => 'mirror')); // nav_key and location value will mirror each other by default
	public $boolean_fields = array('hidden'); // The hidden field is considered a boolean field which allows us to toggle it between "yes" and "no" in the list view of the admin

	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_navigation');
		$this->required['group_id'] = lang('error_create_nav_group');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Lists the module's items
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data (optional)
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data (optional)
	 */	
	public function list_items($limit = NULL, $offset = NULL, $col = 'nav_key', $order = 'desc', $just_count = FALSE)
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
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of page information based on the location
	 *
	 * @access	public
	 * @param	string The location value of the menu item
	 * @param	mixed The group that the menu item belongs to. Can be a string (name) or an int (ID)  (optional)
	 * @param	string The language of the navigation item (optional)
	 * @return	array
	 */	
	public function find_by_location($location, $group_id = 1, $lang = NULL)
	{
		$where[$this->_tables['fuel_navigation'].'.location'] = $location;
		return $this->_find_by_array($where, $group_id, $lang);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of page information based on the location
	 *
	 * @access	public
	 * @param	string The nav_key value of the menu item
	 * @param	mixed The navigation group name (string) or ID (int) value (optional)
	 * @param	string The language of the navigation item (optional)
	 * @return	array
	 */	
	public function find_by_nav_key($nav_key, $group_id = 1, $lang = NULL)
	{
		$where[$this->_tables['fuel_navigation'].'.nav_key'] = $nav_key;
		return $this->_find_by_array($where, $group_id, $lang);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of page information based on the location
	 *
	 * @access	protected
	 * @param	array The where condition for the query
	 * @param	mixed The navigation group name (string) or ID (int) value (optional)
	 * @param	string The language of the navigation item (optional)
	 * @return	array
	 */	
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Tree view that puts navigation items in a hierarchy based on their location value
	 *
	 * @access	public
	 * @param	boolean Determines whether to return just published navigation items or not (optional... and ignored in the admin)
	 * @return	array An array that can be used by the Menu class to create a hierachical structure
	 */	
	public function tree($just_published = FALSE)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Finds all menu items based on a group value
	 *
	 * @access	public
	 * @param	mixed The navigation group name (string) or ID (int) value (optional)
	 * @param	string The language of the navigation item (optional)
	 * @param	string The column name to be used as the key value
	 * @return	array
	 */	
	public function find_all_by_group($group_id = 1, $lang = NULL, $assoc_key = NULL)
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

	// --------------------------------------------------------------------
	
	/**
	 * Finds the max menu ID value. Used when importing menu items
	 *
	 * @access	public
	 * @return	int
	 */	
	public function max_id()
	{
		$this->db->select_max('id');
		$query = $this->db->get($this->_tables['fuel_navigation']);
		$data = $query->row_array();
		return $data['id'];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Navigation form fields
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Validation callback method to make sure that the location and parent ID's don't match (to prevent infinite recursive loopiness)
	 *
	 * @access	public
	 * @param	array The parent ID of the navigation item
	 * @return	boolean
	 */	
	public function no_location_and_parent_match($parent_id)
	{
		$data = $this->find_one_array(array($this->_tables['fuel_navigation'].'.id' => $parent_id));
		if (!empty($data))
		{
			if ($data['id'] == $data['parent_id']) return FALSE;
		}
		return TRUE;
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Validation callback method to make sure that new navigation item doesn't already exist with the same nav_key, group_id and language values
	 *
	 * @access	public
	 * @param	string The nav_key value of the menu itemparent ID of the navigation item
	 * @param	mixed The navigation group name (string) or ID (int) value
	 * @param	string The language of the navigation item
	 * @return	boolean
	 */	
	public function is_new_navigation($nav_key, $group_id, $lang)
	{
		if (empty($group_id)) return FALSE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'nav_key' => $nav_key, 'language' => $lang));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validation callback method to make sure that existing navigation item doesn't change values to already existing navigation with the same nav_key, group_id and language values
	 *
	 * @access	public
	 * @param	string The nav_key value of the menu itemparent ID of the navigation item
	 * @param	mixed The navigation group name (string) or ID (int) value
	 * @param	int The navigation's ID value
	 * @param	string The language of the navigation item
	 * @return	boolean
	 */	
	public function is_editable_navigation($nav_key, $group_id, $id, $lang)
	{
		$data = $this->find_one_array(array('group_id' => $group_id, 'nav_key' => $nav_key, 'language' => $lang));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validation callback method to make sure that new navigation item doesn't already exist with the same location, group_id and language values
	 *
	 * @access	public
	 * @param	string The location value of the menu itemparent ID of the navigation item
	 * @param	mixed The navigation group name (string) or ID (int) value
	 * @param	string The language of the navigation item
	 * @return	boolean
	 */	
	public function is_new_location($location, $group_id, $parent_id, $lang)
	{
		if (empty($location)) return TRUE;
		if (empty($group_id)) return FALSE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id, 'language' => $lang));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validation callback method to make sure that existing navigation item doesn't change values to already existing navigation with the same location, group_id and language values
	 *
	 * @access	public
	 * @param	string The location value of the menu itemparent ID of the navigation item
	 * @param	mixed The navigation group name (string) or ID (int) value
	 * @param	int The navigation's ID value
	 * @param	string The language of the navigation item
	 * @return	boolean
	 */
	public function is_editable_location($location, $group_id, $parent_id, $id, $lang)
	{
		if (empty($location)) return TRUE;
		$data = $this->find_one_array(array('group_id' => $group_id, 'location' => $location, 'parent_id' => $parent_id, 'language' => $lang));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook right before the data is cleaned. Cleans up location value if needed
	 *
	 * @access	public
	 * @param	array The values to be saved right the clean method is run
	 * @return	array Returns the values to be cleaned
	 */	
	public function on_before_clean($values)
	{
		//if (empty($values['nav_key'])) $values['nav_key'] = $values['location'];
		
		// if the path is local, then we clean it
		if (!is_http_path($values['location']))
		{
			$values['location'] = str_replace(array('/', '#', '.'), array('____', '___', '_X_'), $values['location']);
			$values['location'] = url_title($values['location']);
			$values['location'] = str_replace(array('____', '___', '_X_'), array('/', '#', '.'), $values['location']);
		}

		if (empty($values['language']))
		{
			$CI =& get_instance();
			$values['language'] = $CI->fuel->language->default_option();
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right before validation is run to add additional navigation validation
	 *
	 * @access	public
	 * @param	array The values to be saved right before validation
	 * @return	array Returns the values to be validated right before saving
	 */		
	public function on_before_validate($values)
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

	// --------------------------------------------------------------------
	
	/**
	 * Common query that joins fuel_navigation_groups table info
	 *
	 * @access	public
	 * @param mixed parameter to pass to common query (optional)
	 * @return	void
	 */	
	public function _common_query($params = NULL)
	{
		parent::_common_query();
		$this->db->select($this->_tables['fuel_navigation'].'.*, '.$this->_tables['fuel_navigation_groups'].'.id group_id, '.$this->_tables['fuel_navigation_groups'].'.name group_name');
		$this->db->join($this->_tables['fuel_navigation_groups'], $this->_tables['fuel_navigation_groups'].'.id='.$this->_tables['fuel_navigation'].'.group_id', 'left');
		$this->db->order_by('precedence, location asc');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwritten: Allows for grouping of menu items with last paramter
	 *
	 * @access	public
	 * @param	string	The column to use for the value (optional)
	 * @param	string	The column to use for the label (optional)
	 * @param	mixed	An array or string containg the where paramters of a query (optional)
	 * @param	string	The order by of the query. defaults to $val asc (optional)
	 * @param	boolean	Determines whether it will group the options together based on the menu troup (optional)
	 * @return	array	 */	
	public function options_list($key = 'id', $val = 'label', $where = array(), $order = TRUE, $group = TRUE)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwritten: Allows for grouping of menu items with last paramter
	 *
	 * @access	public
	 * @param	string The field name used as the label
	 * @param	int The current value... and actually deprecated (optional)
	 * @param	string The value field (optional)
	 * @return	array Key/value array
	 */	
	public function get_others($display_field, $id = NULL, $val_field = NULL)
	{
		$others = $this->find_all_array_assoc('id');

		// COMMENTED OUT BECAUSE WE DISABLE IT IN THE DROPDOWN INSTEAD
		//if (isset($others[$id])) unset($others[$id]);
		$others = $this->_group_options($others);
		//if (isset($others[$id])) unset($others[$id]);
		return $others;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Groups the options together
	 *
	 * @access	protected
	 * @param	array The data to group together
	 * @param	int The current value... and actually deprecated (optional)
	 * @param	string The value field (optional)
	 * @return	array Key/value array
	 */	
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
