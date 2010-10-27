<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Authors_model extends Base_module_model {
	
	public $required = array('name', 'email');
	
	function __construct()
	{
		parent::__construct('authors'); // table name
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		
		$upload_path = assets_server_path('authors/', 'images');
		$fields['avatar_upload'] = array('type' => 'file', 'upload_path' => $upload_path, 'overwrite' => TRUE);
		$fields['published']['order'] = 1000;
		return $fields;
	}
	
	
}

class Author_model extends Data_record {
}
?>