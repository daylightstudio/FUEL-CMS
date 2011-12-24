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
	'copy' => array('type' => 'copy', 'label' => lang('layout_field_main_copy')),
	'Header' => array('type' => 'fieldset', 'label' => 'Header', 'class' => 'tab'),
	'page_title' => array('label' => lang('layout_field_page_title')),
	'meta_description' => array('label' => lang('layout_field_meta_description')),
	'meta_keywords' => array('label' => lang('layout_field_meta_keywords')),
	'Body' => array('type' => 'fieldset', 'label' => 'Body', 'class' => 'tab'),
	'body' => array('label' => lang('layout_field_body'), 'type' => 'textarea', 'description' => lang('layout_field_body_description')),
	'body_class' => array('label' => lang('layout_field_body_class')),
	'Sections' => array('type' => 'fieldset', 'label' => 'Sections', 'class' => 'tab'),
	'sections' => array(
					'display_label' => FALSE,
					'type' => 'template', 
					'label' => 'Page sections', 
					'fields' => array(
							'title' => '',
							'action' => '',
							'content' => array('type' => 'textarea'),
						),
					
					'view' => '_fields/section', 
//					'post_process' =>  array($custom_fields, 'section_post_process'), 
					//'js' => array('jquery.repeatable'),
					'class' => 'repeatable',
					'add_extra' => FALSE,
					'repeatable' => TRUE,
					//'fieldset' => 'Sections',
					),
	// 'nested' => array('type' => 'nested', 'display_label' => FALSE, 'fields' => array(
	// 													'test1' => array('type' => 'textarea'),
	// 													'test2' => array('type' => 'enum', ''),
	// 												)),

	);



/* End of file MY_fuel_layouts.php */
/* Location: ./application/config/MY_fuel_layouts.php */

