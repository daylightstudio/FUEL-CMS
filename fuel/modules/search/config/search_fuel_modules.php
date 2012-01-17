<?php
$config['modules']['search'] = array(
	'module_name' => 'Search',
	'module_uri' => 'tools/search',
	'model_name' => 'search_model',
	'model_location' => 'search',
	'table_headers' => array(
		'id', 
		'location',
		'scope', 
		'title', 
		'date_added', 
	),
	'display_field' => 'location',
	'preview_path' => '{location}',
	'permission' => 'tools/search',
	'instructions' => lang('search_instructions'),
	'archivable' => TRUE,
	'configuration' => array('search' => 'search'),
	'nav_selected' => 'tools/search',
	'list_actions' => array('tools/search/reindex' => 'Re-index'),
//	'language' => array('blog' => 'blog'),
	'default_col' => 'date_added',
	'default_order' => 'desc',
	'sanitize_input' => array('template','php'),
	'folder' => 'search'
);
