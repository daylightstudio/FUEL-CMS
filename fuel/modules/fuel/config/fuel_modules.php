<?php 
/*
|--------------------------------------------------------------------------
| Built In Modules **** DO NOT MODIFY ****
|--------------------------------------------------------------------------
|
| Specifies the module controller (key) and the name (value) for fuel
*/

$config['modules'] = array();

// page module init values
$config['modules']['pages'] = array(
	'module_name' => 'Pages',
	'model_name' => 'Pages_model',
	'display_field' => 'location',
	'js_controller' => 'PageController',
	'js_controller_params' => array('import_view_key' => 'vars--body'),
	'preview_path' => '{location}',
	'views' => array(
		'list' => '_layouts/module_list', 
		'create_edit' => '_layouts/module_create_edit', 
		'delete' => '_layouts/module_delete'),
	'permission' => array('edit' => 'pages', 'publish' => 'pages_publish'),
	// 'permission' => array('edit', 'publish', 'delete'),
	'instructions' => 'Here you can manage the data associated with the page.',
	'archivable' => TRUE,
	'sanitize_input' => 'php'
);

// navigation module init values
$config['modules']['blocks'] = array(
	'display_field' => 'name',
	'js_controller' => 'BlockController',
	'sanitize_input' => 'php'
);

// navigation module init values
$config['modules']['navigation'] = array(
	'module_name' => 'Navigation',
	'model_name' => 'navigation_model',
	'display_field' => 'label',
	'js_controller' => 'NavigationController',
	'preview_path' => '',
	'views' => array(
		'list' => '_layouts/module_list', 
		'create_edit' => '_layouts/module_create_edit', 
		'delete' => '_layouts/module_delete'),
	'permission' => 'navigation',
	'instructions' => 'Here you create and edit the top menu items of the page.',
	'filters' => array('group_id' => array('default' => 1, 'label' => 'Navigation Groups', 'type' => 'select')),
	'archivable' => TRUE,
	'list_actions' => array('navigation/upload' => 'Upload')
);

// navigation module init values
$config['modules']['navigation_group'] = array(
	'model_name' => 'navigation_groups_model',
	'hidden' => TRUE,
);
// assets module init values
$config['modules']['assets'] = array(
	'module_name' => 'Assets',
	'model_name' => 'assets_model',
	'js_controller' => 'AssetsController',
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'assets',
	'instructions' => 'Here you can upload new assets. Select overwrite if you would like to overwrite a file with the same name.',
	'filters' => array('group_id' => array('default' => 0, 'label' => 'Asset Folder', 'type' => 'select', 'options' => array(0 => 'images'))),
	'archivable' => FALSE,
	'table_actions' => array('DELETE'),
	'rows_selectable' => FALSE,
	'create_action_name' => 'Upload',
	'sanitize_images' => FALSE
);


// users module init values
$config['modules']['users'] = array(
	'module_name' => 'Users',
	'model_name' => 'users_model',
	'display_field' => 'email',
	'preview_path' => '',
	'permission' => 'users',
	'edit_method' => 'user_info',
	'instructions' => 'Here you can manage the data for users.',
	'archivable' => FALSE,
	'table_actions' => array('EDIT', 'DELETE' => array('func' => create_function('$cols', 'if ($cols[\'super_admin\'] != "yes") { return anchor(\'/fuel/users/delete/\'.$cols[\'id\'], \'DELETE\'); }'))),
	'item_actions' => array('save', 'activate', 'duplicate', 'create', 'delete'),
	'clear_cache_on_save' => FALSE
);

// permissions module init values
$config['modules']['permissions'] = array(
	'module_name' => 'Permissions',
	'model_name' => 'Permissions_model',
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'permissions',
	'instructions' => 'Here you can manage the permissions for FUEL modules 
	and later assign them to users.',
	'archivable' => FALSE,
	'item_actions' => array('save', 'delete', 'create'),
	'clear_cache_on_save' => FALSE
);

// permissions module init values
$config['modules']['sitevariables'] = array(
	'module_name' => 'Site Variables',
	'model_name' => 'Sitevariables_model',
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'sitevariables',
	'instructions' => 'Here you can manage the site variables for your website.',
	'archivable' => FALSE,
	'item_actions' => array('save', 'activate', 'duplicate', 'create', 'delete'),
	'clear_cache_on_save' => FALSE
);

@include(APPPATH.'config/MY_fuel_modules.php');


/* End of file fuel_modules.php */
/* Location: ./codeigniter/application/modules/fuel/config/fuel_modules.php */