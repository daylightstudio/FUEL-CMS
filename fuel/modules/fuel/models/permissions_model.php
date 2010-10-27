<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Permissions_model extends Base_module_model {
	
	public $required = array('name');
	public $unique_fields = array('name');
	
	function __construct()
	{
		parent::__construct('permissions');
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, description, active');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
}


class Permission_model extends Base_module_record {
}