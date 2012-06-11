<?php 
/*
|--------------------------------------------------------------------------
| MY Custom Modules
|--------------------------------------------------------------------------
|
| Specifies the module controller (key) and the name (value) for fuel
*/


/*********************** EXAMPLE ***********************************

$config['modules']['quotes'] = array(
	'preview_path' => 'about/what-they-say',
);

$config['modules']['projects'] = array(
	'preview_path' => 'showcase/project/{slug}',
	'sanitize_images' => FALSE // to prevent false positives with xss_clean image sanitation
);

*********************** EXAMPLE ***********************************/

$config['modules']['products'] = array(
	'preview_path' => 'products',
	);
$config['modules']['product_widgets'] = array(
	'preview_path' => 'products',
	);
$config['modules']['product_widget_armaments'] = array(
	'preview_path' => 'products',
	);

$config['modules']['projects'] = array(
	'preview_path' => 'showcase/project/{slug}',
	'sanitize_files' => FALSE // to prevent false positives with xss_clean image sanitation
);

$config['modules']['quotes'] = array(
	'preview_path' => 'about',
	'sanitize_files' => FALSE // to prevent false positives with xss_clean image sanitation
);

