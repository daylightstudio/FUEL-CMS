$config['modules']['{module}'] = array(
	'preview_path' => '', // put in the preview path on the site e.g products/{slug}
	'model_location' => '{advanced_module}', // put in the advanced module name here
	'module_uri' => '{advanced_module}/{module}' // IMPORTANT! need to define so actions buttons (eg create) have right urls
);
