<?php 
$config['js'][] = array(FUEL_FOLDER => array(
								'jquery/plugins/jquery.formbuilder',
								'fuel/custom_fields.js',
								)
						);
$config['required_text'] = '<span class="required">{required_indicator}</span> '.lang('required_text');
$config['representatives'] = array(
	'number' => array('int', 'smallint', 'mediumint', 'bigint'),
	'password' => array('name' => array('pwd', 'passwd')),
	);
	



/* End of file form_builder.php */
/* Location: ./application/config/form_builder.php */