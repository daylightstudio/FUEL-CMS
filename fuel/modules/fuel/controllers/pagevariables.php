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
		$this->load->module_model(FUEL_FOLDER, 'fuel_pagevariables_model');
		
		// set $id to $var if we didn't find a site variable
		// test if it is a string value first...
		// numeric name values won't work!
		$id = NULL;
		if (!is_numeric($field))
		{
			$var = $this->fuel_pagevariables_model->find_one(array('name' => $field, 'page_id' => $page_id));
			if (isset($var->id))
			{
				$id = $var->id;
			}
		}
		else
		{
			$id = $field;
		}
		
		if (empty($id))
		{
			$output = '<div id="fuel_main_content_inner"><p style="font-size: 12px; font-family: \'Lucida Grande\', \'Gill Sans\', Arial, Helvetica, Sans-serif;  width: 400px; height: 50px;">'.lang('error_inline_page_edit').'</p></div>';
			$this->output->set_output($output);
			return;
		}
		parent::inline_edit($id, 'value');
	}
	
}