<?php

// Datetime field
$fields['datetime'] = array(
	'css_class' => 'datepicker',
	'js_function' => 'fuel.fields.datetime_field',
	'represents' => 'datetime|timestamp'
);

// Date field
$fields['date'] = array(
	'css_class' => 'datepicker',
	'js_function' => 'fuel.fields.datetime_field'
);

// Multi field
$fields['multi'] = array(
	'class'	=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'multi',
	'js_function' => 'fuel.fields.multi_field',
	'represents' => 'array'
);	

// Asset field
$fields['asset'] = array(
	'class'	=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'asset',
	'filepath' => '',
	'js_function' => 'fuel.fields.asset_field',
	'represents' => array('name' => '.*image\]?$|.*img\]?$'),
);

// Wysiwyg field
$fields['wysiwyg'] = array(
	'class'	=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'wysiwyg',
	'filepath' => '',
	'js' => array(
		FUEL_FOLDER => array(
			'editors/markitup/jquery.markitup',
			'editors/markitup/jquery.markitup.set',
			'editors/ckeditor/ckeditor.js',
			'editors/ckeditor/config.js',
		)
	),
	'js_function' => 'fuel.fields.wysiwyg_field',
	'represents' => array('text', 'textarea', 'longtext', 'mediumtext')
);

// File field
$fields['file'] = array(
	'class'	=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'file',
	'filepath' => '',
	'js_function' => 'fuel.fields.file_upload_field',
	'represents' => 'blob'
);

// inline edit filed
$fields['inline_edit'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'inline_edit',
	'js_function' => 'fuel.fields.inline_edit_field',
	'represents' => array('select'),
);

// Linked field
$fields['linked'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'linked',
	'filepath' => '',
	'js_function' => 'fuel.fields.linked_field'
);

// Template field
$fields['template'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'template',
	'filepath' => '',
	'js_function' => 'fuel.fields.template_field',
	'js_exec_order' => 0 // Must be set to 0 so that the node clone will get raw nodes before other js is executed
);

// Number field
$fields['number'] = array(
	'js_function' => 'fuel.fields.number_field',
	'represents' => array('int', 'tinyint', 'smallint', 'mediumint', 'bigint'),
);

// Currency field
$fields['currency'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'currency',
	'filepath' => '',
	'js' => array(FUEL_FOLDER => 'jquery/plugins/jquery.autoNumeric'),
	'js_function' => 'fuel.fields.currency_field',
	'represents' => array('name' => 'price|cost')
);

// State field
$fields['state'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'state',
	'filepath' => '',
	'represents' => array('name' => '^state$')
);

// Slug field
$fields['slug'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'slug',
	'filepath' => '',
	'js_function' => 'fuel.fields.linked_field',
	'represents' => array('name' => 'slug|permalink')
);

// List items
$fields['list_items'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'list_items',
	'filepath' => ''
);

// URL field
$fields['url'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'url',
	'filepath' => '',
	'js_function' => 'fuel.fields.url_field',
	'represents' => array('name' => 'url|link')
);

// Language field
$fields['language'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'language',
	'filepath'	=> '',
	'represents' => array('name' => 'language')
);

// Keyval field
$fields['keyval'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'keyval',
	'filepath' => ''
);

// Block field
$fields['block'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'block',
	'filepath' => '',
	'js_function' => 'fuel.fields.block_field'
);

// Toggler field
$fields['toggler'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'toggler',
	'filepath' => '',
	'js_function' => 'fuel.fields.toggler_field'
);

// Color picker field
$fields['colorpicker'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'colorpicker',
	'filepath' => '',
	'js_function' => 'fuel.fields.colorpicker_field',
	'js' => array(
		FUEL_FOLDER => array(
			'jquery/plugins/colorpicker',
		)
	),
	'css' => array(FUEL_FOLDER => 'colorpicker'),
);

// Dependent field
$fields['dependent'] = array(
	'class' => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'dependent',
	'filepath' => '',
	'css' => array(FUEL_FOLDER => 'jquery.supercomboselect'),
	'js_function' => 'fuel.fields.dependent_field'
);

// Embedded list field
$fields['embedded_list'] = array(
	'class'    => array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function' => 'embedded_list',
	'filepath' => '',
	'js_function' => 'fuel.fields.embedded_list'
);

// Select2 field
$fields['select2'] = array(
	'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
	'function'	=> 'select2',
	'js' => array(
		FUEL_FOLDER => array(
			'jquery/plugins/select2.min',
		),
	),
	'js_exec_order' => 1,
	'js_function' => 'fuel.fields.select2',
	'css' => array(
		FUEL_FOLDER => array(
			'select2',
		)
	),
);
/* End of file custom_fields.php */
/* Location: ./modules/fuel/config/custom_fields.php */