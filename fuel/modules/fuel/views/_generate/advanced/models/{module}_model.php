<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class {module_name}s_model extends Base_module_model {

	public $filters = array();
	public $required = array();
	public $boolean_fields = array();
	public $belongs_to = array('relationships' => 'relationships_test');
	public $serialized_fields = array('serialized_test');
	
	function __construct()
	{
		parent::__construct('projects'); // table name
	}

	function list_items($limit = null, $offset = null, $col = 'precedence', $order = 'desc')
	{
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function form_fields($values = array())
	{
		$CI =& get_instance();
		return $fields;
	}
	
	function on_after_save($values)
	{
		return $values;
	}
}

class {module_name}_model extends Base_module_record {
	
	// put your record model code here
}