<?php
require_once('module.php');

class Pagevariables extends Module {
	
	function __construct()
	{
		parent::__construct();
	}

	
	// Thanks floorish!
	// http://www.getfuelcms.com/forums/discussion/comment/1216/#Comment_1216
	function inline_edit($field, $page_id = NULL)
	{
		// try to get the id, if $var is a name
		$this->load->module_model(FUEL_FOLDER, 'pagevariables_model');
		
		// set $id to $var if we didn't find a site variable
		// test if it is a string value first...
		// numeric name values won't work!
		if (!is_numeric($field))
		{
			$var = $this->pagevariables_model->find_one(array('name' => $field, 'page_id' => $page_id));
			$id = $var->id;
		}
		else
		{
			$id = $field;
		}
		parent::inline_edit($id, 'value');
	}
	
}