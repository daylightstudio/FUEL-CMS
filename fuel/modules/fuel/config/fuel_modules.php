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
	'model_location' => 'fuel',
	'display_field' => 'location',
	'table_headers' => array(
		'id', 
		'location', 
		'layout', 
		'published',
	),
	'default_col' => 'location',
	'default_order' => 'asc',
	'js_controller' => 'PageController',
	'js_controller_params' => array('import_view_key' => 'vars--body'),
	'js_localized' => array('pages_default_location'),
	'preview_path' => '{location}',
	'views' => array(
		'list' => '_layouts/module_list', 
		'create_edit' => '_layouts/module_create_edit', 
		'delete' => '_layouts/module_delete'),
	'permission' => array('edit' => 'pages', 'publish' => 'pages_publish', 'delete' => 'pages_delete'),
	// 'permission' => array('edit', 'publish', 'delete'),
	'instructions' => lang('pages_instructions'),
	'archivable' => TRUE,
	'sanitize_input' => array('template','php'),
	'list_actions' => array('pages/upload' => lang('btn_upload')),
	'item_actions' => array('save', 'view', 'publish', 'delete', 'duplicate', 'create', 'others' => array('pages/upload' => lang('btn_upload'))),
	
);

// navigation module init values
$config['modules']['blocks'] = array(
	'module_name' => 'Blocks',
	'model_location' => 'fuel',
	'display_field' => 'name',
	'table_headers' => array(
		'id', 
		'name', 
		'description', 
		'view', 
		'published',
	),
	'default_col' => 'name',
	'default_order' => 'asc',
	'js_controller' => 'BlockController',
	'sanitize_input' => array('template','php'),
	'list_actions' => array('blocks/upload' => lang('btn_upload')),
	'item_actions' => array('save', 'view', 'publish', 'delete', 'duplicate', 'create', 'others' => array('blocks/upload' => lang('btn_upload'))),
);

// navigation module init values
$config['modules']['navigation'] = array(
	'module_name' => 'Navigation',
	'model_location' => 'fuel',
	'display_field' => 'label',
	'table_headers' => array(
		'id', 
		'label', 
		'nav_key',
		'precedence',
		'published',
	),
	'default_col' => 'nav_key',
	'default_order' => 'asc',
	'js_controller' => 'NavigationController',
	'preview_path' => '',
	'views' => array(
		'list' => '_layouts/module_list', 
		'create_edit' => '_layouts/module_create_edit', 
		'delete' => '_layouts/module_delete'),
	'permission' => 'navigation',
	'instructions' => lang('navigation_instructions'),
	'filters' => array('group_id' => array('default' => 1, 'label' => lang('form_label_navigation_group'), 'type' => 'select')),
	'archivable' => TRUE,
	'list_actions' => array('navigation/upload' => lang('btn_upload'))
);

// navigation module init values
$config['modules']['navigation_group'] = array(
	'module_name' => 'Navigation Groups',
	'model_name' => 'navigation_groups_model',
	'model_location' => 'fuel',
	'table_headers' => array(
		'id', 
		'label', 
		'nav_key',
	),
);
// assets module init values
$config['modules']['assets'] = array(
	'module_name' => 'Assets',
	'model_location' => 'fuel',
	'table_headers' => array(
		'id', 
		'name', 
		'preview/kb', 
		'link', 
		'last_updated',
	),
	'default_col' => 'name',
	'default_order' => 'asc',
	'js_controller' => 'AssetsController',
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'assets',
	'instructions' => lang('assets_instructions'),
	'filters' => array('group_id' => array('default' => 0, 'label' => lang('form_label_asset_folder'), 'type' => 'select', 'options' => array(0 => 'images'))),
	'archivable' => FALSE,
	'table_actions' => array('DELETE'),
	'rows_selectable' => FALSE,
	'create_action_name' => lang('btn_upload'),
	'sanitize_images' => FALSE
);

// sitevariable module init values
$config['modules']['sitevariables'] = array(
	'module_name' => 'Site Variables',
	'model_name' => 'Sitevariables_model',
	'model_location' => 'fuel',
	'table_headers' => array(
		'id', 
		'name', 
		'value', 
		'scope', 
		'active', 
	),
	
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'sitevariables',
	'instructions' => lang('sitevariables_instructions'),
	'archivable' => FALSE,
	'item_actions' => array('save', 'activate', 'duplicate', 'create', 'delete'),
	'clear_cache_on_save' => FALSE
);


// users module init values
$config['modules']['users'] = array(
	'module_name' => 'Users',
	'model_name' => 'users_model',
	'model_location' => 'fuel',
	'table_headers' => array(
		'id', 
		'email', 
		'user_name', 
		'first_name', 
		'last_name', 
		'super_admin', 
		'active', 
	),
	
	'js_controller' => 'UserController',
	'display_field' => 'email',
	'preview_path' => '',
	'permission' => 'users',
	'edit_method' => 'user_info',
	'instructions' => lang('users_instructions'),
	'archivable' => FALSE,
	'table_actions' => array('EDIT', 'DELETE' => array('func' => create_function('$cols', '
		if ($cols[\'super_admin\'] != "yes") { 
			$CI =& get_instance();
			$link = "";
			if ($CI->fuel_auth->has_permission($CI->permission, "delete") AND isset($cols[$CI->model->key_field()]))
			{
				$url = site_url("/".$CI->config->item("fuel_path", "fuel").$CI->module_uri."/delete/".$cols[$CI->model->key_field()]);
				$link = "<a href=\"".$url."\">".lang("table_action_delete")."</a>";
				$link .= " <input type=\"checkbox\" name=\"delete[".$cols[$CI->model->key_field()]."]\" value=\"1\" id=\"delete_".$cols[$CI->model->key_field()]."\" class=\"multi_delete\"/>";
			}
			return $link;
		}'))),
	'item_actions' => array('save', 'activate', 'duplicate', 'create', 'delete'),
	'clear_cache_on_save' => FALSE,
);

// permissions module init values
$config['modules']['permissions'] = array(
	'module_name' => 'Permissions',
	'model_name' => 'Permissions_model',
	'model_location' => 'fuel',
	'table_headers' => array(
		'id', 
		'name', 
		'description', 
		'active', 
	),
	
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'permissions',
	'instructions' => lang('permissions_instructions'),
	'archivable' => FALSE,
	'item_actions' => array('save', 'delete', 'create'),
	'clear_cache_on_save' => FALSE
);

@include(APPPATH.'config/MY_fuel_modules.php');


/* End of file fuel_modules.php */
/* Location: ./modules/fuel/config/fuel_modules.php */