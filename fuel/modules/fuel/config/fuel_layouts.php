<?php
/*
|--------------------------------------------------------------------------
| layouts Path 
|--------------------------------------------------------------------------
|
| The subdirectory in the views folder where the layouts files are stored
|
*/
$config['layouts_folder'] = '_layouts';

/*
|--------------------------------------------------------------------------
| Default Layout
|--------------------------------------------------------------------------
|
| This will be the default selected layout when you create a new page
| in the FUEL admin.
|
*/
$config['default_layout'] = 'main';

/*
|--------------------------------------------------------------------------
| layouts for Pages 
|--------------------------------------------------------------------------
|
| A layout can be made up of several view files... or parts. They will 
| be stacked together in order. The parts array key is the name of the layout.
| You can also include a'hooks key for'layout_hooks which is code that 
| gets executed before the rendering of the layout. If set to the keyword
| "AUTO", then layouts will be read in from the views/_layouts directory
*/
//$config['layouts'] = 'AUTO';


/*
|--------------------------------------------------------------------------
| layout variables 
|--------------------------------------------------------------------------
|
| Specifies what variables and form information to display when you select
| a new layout in the FUEL page admin. The key (e.g. 'header') is associated
| with the layout part in the layout above. The value is an array of 
| variables that are associated with the layout. If the variable is just a
| string value, then it is considered the default value. If the variable is 
| an array, it will look for a key value of 'value. You can use any value 
| that can be passed to the Form_builder class such as:
| * name
| * type 
| * default
| * max_length
| * comment'|| description
| * label (string)
| * required (bool)
| * size (num)
| * class (image_select will trigger the asset's image select button)
| * options (assoc array)
| * value (mixed)
| * readonly (bool)
| * disabled (bool)
| * first_option (bool)
| * after_html (string)
| * displayonly (bool)
*/

@include(APPPATH.'config/MY_fuel_layouts.php');

// initialize layout fields 
$config['layouts']['301_redirect'] = array(
	'label' => '301 Redirect',
	'fields' => array(
		'copy' => array('type' => 'copy', 'label' => lang('layout_field_301_redirect_copy')),
		'redirect_to' => array('label' => lang('layout_field_redirect_to')),
	)
);

$config['layouts']['alias'] = array(
	'label' => 'Alias',
	'fields' => array(
		'copy' => array('type' => 'copy', 'label' => lang('layout_field_alias_copy')),
		'alias' => array('label' => lang('layout_field_alias')),
	)
);

$config['layouts']['404_error'] = array(
	'label' => '404 Error',
	'fields' => array(
		'heading' => array('type' => 'copy', 'label' => lang('layout_field_heading')),
		'body' => array('label' => lang('layout_field_body')),
	)
);

$config['layouts']['sitemap_xml'] = array(
	'label' => 'sitemap.xml',
	'fields' => array(
		'copy' => array('type' => 'copy', 'label' => lang('layout_field_sitemap_xml_copy')),
		'frequency' => array(
			'type' => 'select',
			'options' => array(
				'always' => lang('layout_field_frequency_always'),
				'hourly' => lang('layout_field_frequency_hourly'),
				'daily' => lang('layout_field_frequency_daily'),
				'weekly' => lang('layout_field_frequency_weekly'),
				'monthly' => lang('layout_field_frequency_monthly'),
				'yearly' => lang('layout_field_frequency_yearly'),
				'never' => lang('layout_field_frequency_never'),
			),
			'value' => 'always',
			'label' => lang('layout_field_frequency')),
	)
);

$config['layouts']['robots_txt'] = array(
	'label' => 'robots.txt',
	'fields' => array(
		'copy' => array('type' => 'copy', 'label' => lang('layout_field_robots_txt_copy')),
		'body' => array('type' => 'textarea', 'label' => lang('layout_field_body'), 'value' => "User-agent: *\nDisallow: /fuel/", 'class' => 'no_editor'),
	)
);

$config['layouts']['none'] = array(
	'label' => 'None',
	'fields' => array(
		'copy' => array('type' => 'copy', 'label' => lang('layout_field_none_copy')),
		'body' => array('type' => 'textarea', 'label' => lang('layout_field_body')),
	)
);



/* End of file fuel_layouts.php */
/* Location: ./modules/fuel/config/fuel_layouts.php */