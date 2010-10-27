<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Features_model extends Base_module_model {

	public $required = array('title', 'copy');
	
	function __construct()
	{
		parent::__construct('features'); // table name
	}

	function list_items($limit = null, $offset = null, $col = 'title', $order = 'asc')
	{
		$data = parent::list_items('id, title, type, published', $limit, $offset, $col, $order);
		return $data;
	}
	
	function form_fields()
	{
		$fields = parent::form_fields();
		return $fields;
	}
}

class Feature_model extends Base_module_record {
	
}
?>