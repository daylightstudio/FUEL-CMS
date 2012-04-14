<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Settings_model extends Base_module_model
{
	public $required = array('key');
	
	function __construct()
	{
		parent::__construct('fuel_settings');
	}
}

class Setting_model extends Base_module_record {
}
