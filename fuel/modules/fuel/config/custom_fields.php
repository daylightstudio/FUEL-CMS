<?php

// datetime field
$fields['datetime'] = array(
	'css_class' => 'datepicker',
	'css' 		=> array(FUEL_FOLDER => 'fuel-theme/jquery-ui-1.8.17.custom'),
	'js'		=> array(FUEL_FOLDER => array('jquery/plugins/jquery-ui-1.8.17.custom.min',)),
	'js_function' => 'fuel.fields.datetime_field',
	// 'js_params' => array('format' => 'mm-dd-yyyy'),
	'represents' => 'datetime|timestamp',
);

// date field
$fields['date'] = array(
	'css_class' => 'datepicker',
	'css' 		=> array(FUEL_FOLDER => 'fuel-theme/jquery-ui-1.8.17.custom'),
	'js'		=> array(FUEL_FOLDER => array('jquery/plugins/jquery-ui-1.8.17.custom.min',)),
	'js_function' => 'fuel.fields.datetime_field',
);

// multi field
$fields['multi'] = array('js'		=> array(
						FUEL_FOLDER => array(
							'jquery/plugins/jquery.selso',
							'jquery/plugins/jquery-ui-1.8.17.custom.min',
							'jquery/plugins/jquery.disable.text.select.pack',
							'jquery/plugins/jquery.supercomboselect',
						)
	),
	'js_function' => 'fuel.fields.multi_field',
	'css' => array(FUEL_FOLDER => 'jquery.supercomboselect'),
	'represents' => 'array',
);	

// multi field
$fields['asset'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'asset',
	'filepath'	=> '',
	'js'		=> array(
						FUEL_FOLDER => array(
							'jquery/plugins/jqModal',
						)
	),
	'js_function' => 'fuel.fields.asset_field',
	'represents' => array('name' => '.*image$|.*img$'),
);

// wysiwyg field
$fields['wysiwyg'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'wysiwyg',
	'filepath'	=> '',
	'css' 		=> array(FUEL_FOLDER => 'markitup'),
	'js'		=> array(
						FUEL_FOLDER => array(
							'editors/markitup/jquery.markitup',
							'editors/markitup/jquery.markitup.set',
							'editors/ckeditor/ckeditor.js',
							'editors/ckeditor/config.js',
						)
	),
	'css' => array(FUEL_FOLDER => 'markitup'),
	'js_function' => 'fuel.fields.wysiwyg_field',
	'represents' => array('text', 'textarea', 'longtext', 'mediumtext'),
);

// file field
$fields['file'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'file',
	'filepath'	=> '',
	'js'		=> array(
						FUEL_FOLDER => array(
							'jquery/plugins/jquery.MultiFile',
						)
	),
	'js_function' => 'fuel.fields.file_upload_field',
	'represents' => 'blob',
	
);

// inline edit filed
$fields['inline_edit'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'linked',
	'filepath'	=> '',
	'js'		=> array(
		FUEL_FOLDER => array(
			'fuel/linked_field_formatters',
		)
	),
	'js_function' => 'fuel.fields.linked_field',

);

// linked field
$fields['linked'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'linked',
	'filepath'	=> '',
	'js'		=> array(
		FUEL_FOLDER => array(
			'fuel/linked_field_formatters',
		)
	),
	'js_function' => 'fuel.fields.linked_field',

);

// template field
$fields['template'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'template',
	'filepath'	=> '',
	'js'		=> array(
						FUEL_FOLDER => 
							'jquery/plugins/jquery.repeatable',
							),
	'js_function' => 'fuel.fields.template_field',
	'js_exec_order' => 0, // must be set to 0 so that the node clone will get raw nodes before other js is executed
);

// number field
$fields['number'] = array(
	'js'		=> array(FUEL_FOLDER => 'jquery/plugins/jquery.numeric'),
	'js_function' => 'fuel.fields.number_field',
	'represents' => array('int', 'smallint', 'mediumint', 'bigint'),
);

// currency field
$fields['currency'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'currency',
	'filepath'	=> '',
	'js'		=> array(FUEL_FOLDER => 'jquery/plugins/jquery.autoNumeric'),
	'js_function' => 'fuel.fields.currency_field',
);

// state field
$fields['state'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'state',
	'filepath'	=> '',
	'represents' => array('name' => 'state'),
	
);

// slug field
$fields['slug'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'slug',
	'filepath'	=> '',
	'js'		=> array(
		FUEL_FOLDER => array(
			'fuel/linked_field_formatters',
		)
	),
	'js_function' => 'fuel.fields.linked_field',
	'represents' => array('name' => 'slug|permalink'),
);

// list items
$fields['list_items'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'list_items',
	'filepath'	=> '',
);

/* End of file custom_fields.php */
/* Location: ./modules/fuel/config/custom_fields.php */