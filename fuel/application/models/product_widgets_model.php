<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH . 'models/base_module_model.php');

class Product_widgets_model extends Base_module_model {

	public $required = array('name');
	public $unique_fields = array('name');

	function __construct()
	{
		parent::__construct('product_widgets'); // table name
	}

	function form_fields($values = array())
	{
		$fields = parent::form_fields($values);
		return $fields;
	}

}

class Product_widget_model extends Base_module_record {

	function get_name_formatted()
	{
		$CI =& get_instance();
		$CI->load->helper('inflector');
		return 'NXasdfasdfasdfasfd: ' . humanize($this->name);
	}

}