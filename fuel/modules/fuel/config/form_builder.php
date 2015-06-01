<?php 
$config['js'][] = array(FUEL_FOLDER => array(
	'jquery/plugins/jquery-migrate-1.1.1.js',
	'jquery/plugins/jquery.formbuilder',
	'fuel/custom_fields.js'
	)
);

$config['required_text'] = '<span class="required">{required_indicator}</span> '.lang('required_text');

$config['representatives'] = array(
	'number' => array('int', 'smallint', 'mediumint', 'bigint'),
	'password' => array(
		'name' => array('pwd', 'passwd')
	)
);

if (file_exists(APPPATH.'config/form_builder.php'))
{
	include(APPPATH.'config/form_builder.php');
}

/* End of file form_builder.php */
/* Location: ./application/config/form_builder.php */