<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Sitevariables_model extends Base_module_model {

	public $required = array('name');
	
	function __construct()
	{
		parent::__construct('fuel_site_variables');
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, value, active');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function retrieve_all()
	{
		$vars = $this->options_list('name', 'value', array('active' => 'yes'));
			return $vars;
	}

	function retrieve_one($name = null)
	{
		$vars = $this->find_one(array('active' => 'yes', 'name' => $name));
		return $vars['value'];
	}
	
	function form_fields($values = array()){
		$fields = parent::form_fields();
		$CI =& get_instance();
		return $fields;
	}

}

class Sitevariable_model extends Base_module_record {
}
