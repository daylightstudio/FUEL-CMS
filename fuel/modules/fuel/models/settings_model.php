<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Settings_model extends Base_module_model {

	public $required = array('key', 'value');
	public $unique_fields = array('key');
	
	function __construct()
	{
		parent::__construct('fuel_settings');
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
}

class Setting_model extends Base_module_record {
}
