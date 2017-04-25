<?php 
// Can be JSON string or an array. The json_encode doesn't support native javascript function callbacks when using PHP arrays.
// To help get around this, you can use shortcuts for button toolbar names like "img" and "link" which are callback functions.
// The default configuration is loaded by default and thus empty, but is listed below as an example.
$config['markitup']['default'] = array(
	// 'root' => 'skins/simple/',
	// 'namespace' => 'html',
	// 'previewParserPath' => fuel_url('preview'),
	// 'previewInWindow' => TRUE,
	// 'previewInWindow' => TRUE,
	// 'previewParserVar' => 'data',
	// 'onShiftEnter' => array('keepDefault' => FALSE, 'replaceWith' => '<br />\n'),
	// 'onCtrlEnter' => array('keepDefault' => FALSE, 'replaceWith' => '\n<p>', 'closeWith' => '</p>'),
	// 'onTab' => array('keepDefault' => FALSE, 'replaceWith' => '    '),
	// 'toolbar' => array('b','i', '|', 'p', 'h1', 'h2', 'h3', '|', 'ol', 'ul', 'li', 'blockquote', 'hr', '|', 'img', 'link','mailto', 'php', 'clean', '|', 'fullscreen'

		// OR... the more toolbar example above can be written in a more verbose way 
		// (note that we can't use img, link etc, which are callback functions because they don't encode properly with json_encode)
		// 
		// 'b' => array('name' => 'b', 'key' => 'B', 'className' => 'bold', 'openWith' => '(!(<strong>|!|<b>)!)', 'closeWith' => '(!(</strong>|!|</b>)!)'),
		// 'i' => array('name' => 'i', 'key' => 'I', 'className' => 'italic', 'openWith' => '(!(<em>|!|<i>)!)', 'closeWith' => '(!(</em>|!|</i>)!)'),
		// 'separator1' => array('separator' => '---------------'),
		// 'p' => array('name' => 'p', 'key' => 'P', 'className' => 'p', 'openWith' => '<p>', 'closeWith' => '</p>'),
		// 'h1' => array('name' => 'h1', 'key' => '1', 'className' => 'h1', 'openWith' => '<h1>', 'closeWith' => '</h1>'),
		// 'h2' => array('name' => 'h2', 'key' => '2', 'className' => 'h2', 'openWith' => '<h2>', 'closeWith' => '</h2>'),
		// 'h3' => array('name' => 'h3', 'key' => '3', 'className' => 'h3', 'openWith' => '<h3>', 'closeWith' => '</h3>'),
		// 'separator2' => array('separator' => '---------------'),
		// 'ol' => array('name' => 'ol', 'key' => '', 'className' => 'ol', 'openWith' => '<ol>', 'closeWith' => '</ol>'),
		// 'ul' => array('name' => 'ul', 'key' => '', 'className' => 'ul', 'openWith' => '<ul>', 'closeWith' => '</ul>'),
		// 'li' => array('name' => 'li', 'key' => '', 'className' => 'li', 'openWith' => '<li>', 'closeWith' => '</li>'),
		// 'blockquote' => array('name' => 'blockquote', 'key' => '', 'className' => 'blockquote', 'openWith' => '<blockquote>', 'closeWith' => '</blockquote>'),
		// 'hr' => array('name' => 'hr', 'key' => '', 'className' => 'hr', 'openWith' => '<hr>', 'closeWith' => '</hr>'),
	// ),
);

$config['markitup']['markdown'] = array();

// Can be JSON string or an array. The json_encode doesn't support the RegExp object when using PHP arrays.
// Because of this, the protectedSource is automatically added by default.
$config['ckeditor']['default'] = array(
	'toolbar' => array(
		array('Bold', 'Italic', 'Strike'),
		array('Format'),
		array('FUELImage', 'HorizontalRule'),
		array('NumberedList', 'BulletedList'),
		array('FUELLink', 'FUELUnlink'),
		array('Undo', 'Redo', 'RemoveFormat'),
		array('PasteFromWord', 'PasteText'),
		array('Maximize'),
	),
	'contentsCss' => WEB_PATH.'assets/css/main.css',
	'htmlEncodeOutput' => FALSE,
	'entities' => FALSE,
	'bodyClass' => 'ckeditor',
	/*'protectedSource' => array('/\{fuel_\w+\(.+\)\}/g', '/<\?[\s\S]*?\?>/g'),  */
	'toolbarCanCollapse' => FALSE,
	'extraPlugins' => 'fuellink,fuelimage',
	'removePlugins' => 'link,image',
	'allowedContent' => TRUE,
	// 'previewParserPath' => fuel_url('preview'),
);

// An example of the above as simply a JSON string
/*$config['ckeditor']['default'] = "{
	toolbar:[
			['Bold','Italic','Strike'],
			['Format'],
			['FUELImage','HorizontalRule'],
			['NumberedList','BulletedList'],
			['FUELLink','FUELUnlink'],
			['Undo','Redo','RemoveFormat'],
			['PasteFromWord','PasteText'],
			['Preview'],
			['Maximize']
		],
	contentsCss: '".WEB_PATH."assets/css/main.css',
	htmlEncodeOutput: false,
	entities: false,
	bodyClass: 'ckeditor',
	protectedSource: [/\{fuel_\w+\(.+\)\}/g, /<\?[\s\S]*?\?>/g],
	toolbarCanCollapse: false,
	extraPlugins: 'fuellink,fuelimage',
	removePlugins: 'link,image',
	allowedContent: true
	}";*/


