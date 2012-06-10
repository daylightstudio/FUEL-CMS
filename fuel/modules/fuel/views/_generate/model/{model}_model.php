<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class {model_name}_model extends Base_module_model {

	public $record_class = '{model_record}';
	public $filters = array();
	public $required = array();
	public $foreign_keys = array();
	public $linked_fields = array();
	public $boolean_fields = array();
	public $unique_fields = array();
	public $parsed_fields = array();
	public $serialized_fields = array();
	public $belongs_to = array();
	public $has_many = array();
	
	
	function __construct()
	{
		parent::__construct('{table}'); // table name
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'precedence', $order = 'desc')
	{
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		return $fields;
	}
	
	function on_after_save($values)
	{
		parent::on_after_save($values);
		return $values;
	}
}

class {model_record}_model extends Base_module_record {
	
	// put your record model code here
}