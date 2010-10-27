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
	'page_title' => '',
	'meta_description' => '',
	'meta_keywords' => '',
	'body' => array('type' => 'textarea', 'description' => 'Main content of the page'),
	'body_class' => ''
	);



/* End of file MY_fuel_layouts.php */
/* Location: ./application/config/MY_fuel_layouts.php */

