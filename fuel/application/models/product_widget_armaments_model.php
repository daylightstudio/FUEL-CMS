<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH . 'models/base_module_model.php');

class Product_widget_armaments_model extends Base_module_model {

	public $required = array('name');
	public $unique_fields = array('name');
	public $belongs_to = array('product_widgets' => 'product_widgets');

	function __construct()
	{
		parent::__construct('product_widget_armaments'); // table name
	}

	function form_fields($values = array())
	{
		$fields = parent::form_fields($values);
		return $fields;
	}

}

class Product_widget_armament_model extends Base_module_record {

	function get_name_formatted()
	{
		$CI =& get_instance();
		$CI->load->helper('inflector');
		return 'A: ' . humanize($this->name);
	}

}