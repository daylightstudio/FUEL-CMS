<?php 
/*
|--------------------------------------------------------------------------
| MY Custom Layouts
|--------------------------------------------------------------------------
|
| specify the name of the layouts and their fields associated with them
*/


/*$config['layouts']['main'] = array(
	'parts' => $config['layouts_path'].'main'
);*/

// main layout fields
$config['layout_fields']['main'] =  array(
	'copy' => array('copy' => lang('layout_field_main_copy')),
	'page_title' => array('label' => lang('layout_field_page_title')),
	'meta_description' => array('label' => lang('layout_field_meta_description')),
	'meta_keywords' => array('label' => lang('layout_field_meta_keywords')),
	'body' => array('label' => lang('layout_field_body'), 'type' => 'textarea', 'description' => lang('layout_field_body_description')),
	'body_class' => array('label' => lang('layout_field_body_class')),
	);



/* End of file MY_fuel_layouts.php */
/* Location: ./application/config/MY_fuel_layouts.php */

