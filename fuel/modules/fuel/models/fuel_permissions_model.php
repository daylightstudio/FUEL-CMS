<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Fuel_permissions_model extends Base_module_model {
	
	public $required = array('name', 'description');
	public $unique_fields = array('name');
	public $belongs_to = array('users' => array('model' => array(FUEL_FOLDER => 'fuel_users_model'), 'where' => array('super_admin' => 'no')));	
	
	function __construct()
	{
		parent::__construct('fuel_permissions');
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, description, active');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		return $fields;
	}
	
}


class Fuel_permission_model extends Data_record {
}