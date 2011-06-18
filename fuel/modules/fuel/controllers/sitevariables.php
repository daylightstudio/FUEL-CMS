<?php
require_once('module.php');

class Sitevariables extends Module {
	
	// Thanks floorish!
	// http://www.getfuelcms.com/forums/discussion/comment/1216/#Comment_1216
	function inline_edit($var)
	{
		if (is_ajax())
		{
			// try to get the id, if $var is a name
			$this->load->module_model(FUEL_FOLDER, 'sitevariables_model');
			
			// set $id to $var if we didn't find a site variable
			// test if it is a string value first...
			// numeric name values won't work!
			if (!is_numeric($var))
			{
				$site_var = $this->sitevariables_model->find_one(array('name' => $var));
				$id = $site_var->id;
			}
			else
			{
				$id = $var;
			}
			parent::inline_edit($id);
		}
	}
	
}