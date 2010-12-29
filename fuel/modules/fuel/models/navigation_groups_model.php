<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Navigation_groups_model extends Base_module_model {
	
	public $unique_fields = array('name');
	
	function __construct()
	{
		$CI =& get_instance();
		$tables = $CI->config->item('tables', 'fuel');
		parent::__construct($tables['navigation_groups']);
		$this->add_validation('name', array(&$this, 'valid_name'), lang('error_requires_string_value'));
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'location', $order = 'asc')
	{
		$this->db->select('id, name, published');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function valid_name($name)
	{
		return (!is_numeric($name));
	}

	 // cleanup navigation items if group is deleted
	 function on_after_delete($where)
	 {
		$this->delete_related(array(FUEL_FOLDER => 'navigation_model'), 'group_id', $where);
	 }
}

class Navigation_group_model extends Base_module_record {
}
