<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Fuel_sitevariables_model extends Base_module_model {

	public $required = array('name');
	public $unique_fields = array('name');
	
	function __construct()
	{
		parent::__construct('fuel_site_variables');
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'desc')
	{

		$this->db->select('id, name, SUBSTRING(value, 1, 50) as value, scope, active', FALSE);	
		$data = parent::list_items($limit, $offset, $col, $order);
		foreach($data as $key => $val)
		{
			$data[$key]['value'] = htmlentities($val['value'], ENT_QUOTES, 'UTF-8');
		}
		return $data;
	}

	function retrieve_all()
	{
		$vars = $this->options_list('name', 'value', array('active' => 'yes'));
		return $vars;
	}

	function retrieve_one($name = null)
	{
		$vars = $this->find_one_array(array('active' => 'yes', 'name' => $name));
		return $vars['value'];
	}
	
	function form_fields($values = array()){
		$fields = parent::form_fields();
		$fields['value']['class'] = 'markitup';
		return $fields;
	}

}

class Fuel_sitevariable_model extends Base_module_record {
}
