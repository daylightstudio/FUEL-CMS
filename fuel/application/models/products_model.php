<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH . 'models/base_module_model.php');

class Products_model extends Base_module_model {

	public $required = array('name');
	public $unique_fields = array('name');
	public $has_many = array('widgets' => 'product_widgets');

	function __construct()
	{
		parent::__construct('products'); // table name
	}

	function form_fields($values = array())
	{
		$fields = parent::form_fields($values);
		$fields['description']['class'] = 'no_editor';
		$fields['widgets']['class'] = 'add_edit product_widgets';
		return $fields;
	}

}

class Product_model extends Base_module_record {	
}
