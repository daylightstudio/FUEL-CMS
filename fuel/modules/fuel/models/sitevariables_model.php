<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Sitevariables_model extends Base_module_model {

	public $required = array('name');
	public $unique_fields = array('name');
	
	function __construct()
	{
		parent::__construct('fuel_site_variables');
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

class Sitevariable_model extends Base_module_record {
}
