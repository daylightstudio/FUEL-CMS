<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Fuel_settings_model extends Base_module_model
{
	public $required = array('key');
	public $serialized_fields = array('value');
	
	function __construct()
	{
		parent::__construct('fuel_settings');
	}
}

class Fuel_setting_model extends Base_module_record {
}
