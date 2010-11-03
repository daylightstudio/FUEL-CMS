<?php 
/*
|--------------------------------------------------------------------------
| MY Custom Modules
|--------------------------------------------------------------------------
|
| Specifies the module controller (key) and the name (value) for fuel
*/
$config['modules']['features'] = array(
	'module_name' => 'Features',
	'model_name' => 'features_model',
	'model_location' => '', // so it will look in the normal model folder
	'display_field' => 'title',
	'preview_path' => '/example',
	'permission' => 'features',
	'instructions' => 'Here you can manage the feature item for your site.',
	'archivable' => TRUE,
);
