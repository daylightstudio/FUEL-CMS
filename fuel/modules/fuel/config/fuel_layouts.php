<?php
/*
|--------------------------------------------------------------------------
| layouts Path 
|--------------------------------------------------------------------------
|
| The subdirectory in the views folder where the layouts files are stored
|
*/
$config['layouts_path'] = '_layouts/';

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
$config['layouts'] = 'AUTO';


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


// initialize layout fields 
$config['layout_fields'] = array();

$config['layout_fields']['301_redirect'] = array(
	'copy' => array('copy' => 'This layout will do a 301 redirect to another page.'),
	'redirect_to' => '',
);


$config['layout_fields']['sitemap_xml'] = array(
	'copy' => array('copy' => 'This layout is used to generate a sitemap.'),
	'frequency' => array(
		'type' => 'select',
		'options' => array(
			'always' => 'always',
			'hourly' => 'hourly',
			'daily' => 'daily',
			'weekly' => 'weekly',
			'monthly' => 'monthly',
			'yearly' => 'yearly',
			'never' => 'never'
		),
		'value' => 'always'
	)
);

$config['layout_fields']['none'] = array(
	'copy' => array('copy' => 'This layout is the equivalent of having no layout assigned.'),
	'body' => array('type' => 'textarea'),
);

@include(APPPATH.'config/MY_fuel_layouts.php');

/* End of file fuel_layouts.php */
/* Location: ./codeigniter/application/modules/fuel/config/fuel_layouts.php */