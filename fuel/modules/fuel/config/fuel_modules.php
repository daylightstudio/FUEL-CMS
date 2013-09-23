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
	'model_name' => 'fuel_pages_model',
	'display_field' => 'location',
	'table_headers' => array(
		'id', 
		'location', 
		'layout', 
		'last_modified',
		'published',
	),
	'default_col' => 'location',
	'default_order' => 'asc',
	'js_controller' => 'fuel.controller.PageController',
	'js_controller_params' => array('import_field' => 'vars--body'),
	'preview_path' => '{location}',
	'permission' => array('pages', 'create', 'edit', 'pages/upload' => 'pages/create', 'publish', 'delete'),
	'instructions' => lang('pages_instructions'),
	'archivable' => TRUE,
	'sanitize_input' => array('template','php'),
	'list_actions' => array('pages/upload' => lang('btn_upload')),
	'item_actions' => array('save', 'view', 'publish', 'delete', 'duplicate', 'replace', 'create', 'others' => array('pages/upload' => lang('btn_upload'))),
);

// page module init values
$config['modules']['pagevariables'] = array(
	'module_name' => 'Page Variables',
	'model_location' => 'fuel',
	'model_name' => 'fuel_pagevariables_model',
	'display_field' => 'name',
	'table_headers' => array(
		'id', 
		'location', 
		'name',
		'value',
		'type'
	),
	'sanitize_input' => array('template','php'),
	'default_col' => 'page_id',
	'default_order' => 'asc',
	'permission' => array('edit' => 'pages', 'publish' => 'pages/publish', 'delete' => 'pages/delete'),
	'hidden' => TRUE
);

// navigation module init values
$config['modules']['blocks'] = array(
	'module_name' => 'Blocks',
	'model_location' => 'fuel',
	'model_name' => 'fuel_blocks_model',
	'display_field' => 'name',
	'permission' => array('blocks', 'create', 'edit', 'blocks/upload' => 'blocks/create', 'publish', 'delete'),
	'default_col' => 'name',
	'default_order' => 'asc',
	'js_controller' => 'fuel.controller.BlockController',
	'sanitize_input' => array('template','php'),
	'list_actions' => array('blocks/upload' => lang('btn_upload')),
	'item_actions' => array('save', 'view', 'publish', 'delete', 'duplicate', 'replace', 'create', 'others' => array('blocks/upload' => lang('btn_upload'))),
);

// navigation module init values
$config['modules']['navigation'] = array(
	'module_name' => 'Navigation',
	'model_location' => 'fuel',
	'model_name' => 'fuel_navigation_model',
	'display_field' => 'label',
	'default_col' => 'nav_key',
	'default_order' => 'asc',
	'js_controller' => 'fuel.controller.NavigationController',
	'preview_path' => '',
	'permission' => array('navigation', 'create', 'edit', 'navigation/upload' => 'navigation/create', 'publish', 'delete'),
	'instructions' => lang('navigation_instructions'),
	'filters' => array('group_id' => array('default' => 1, 'label' => lang('form_label_navigation_group'), 'type' => 'select', 'model' => 'fuel_navigation_groups_model', 'hide_if_one' => TRUE)),
	'archivable' => TRUE,
	'list_actions' => array('navigation/upload' => lang('btn_upload'), 'navigation/download' => lang('btn_download'))
);

// navigation module init values
$config['modules']['navigation_group'] = array(
	'module_name' => 'Navigation Groups',
	'model_location' => 'fuel',
	'model_name' => 'fuel_navigation_groups_model',
	'table_headers' => array(
		'id', 
		'name', 
		'published',
	),
	'permission' => 'navigation'
);

// navigation module init values
$config['modules']['tags'] = array(
	'module_name' => 'Tags',
	'model_location' => 'fuel',
	'model_name' => 'fuel_tags_model',
	'table_headers' => array(
		'id', 
		'name', 
		'slug',
		'published',
	),
	//'filters' => array('context' => array('label' => lang('form_label_category'), 'type' => 'select', 'model' => 'fuel_categories_model', 'first_option' => '')),
);

// navigation module init values
$config['modules']['categories'] = array(
	'module_name' => 'Categories',
	'model_location' => 'fuel',
	'model_name' => 'fuel_categories_model',
	'table_headers' => array(
		'id', 
		'name', 
		'slug',
		'context',
		'parent_id',
		'precedence',
		'published',
	),
	'filters' => array('context' => array('label' => lang('form_label_context'), 'type' => 'select', 'model' => array(FUEL_FOLDER => array('fuel_categories_model' => 'context_options_list')), 'first_option' => 'Select a context...')),
);

// assets module init values
$config['modules']['assets'] = array(
	'module_name' => 'Assets',
	'model_location' => 'fuel',
	'model_name' => 'fuel_assets_model',
	'table_headers' => array(
		'id',
		'name', 
		'preview/kb', 
		'link', 
		'last_updated',
	),
	'default_col' => 'name',
	'default_order' => 'asc',
	'js_controller' => 'fuel.controller.AssetsController',
	'display_field' => 'name',
	'preview_path' => '',
	'permission' => 'assets',
	'instructions' => lang('assets_instructions'),
	'filters' => array('group_id' => array('default' => 0, 'label' => lang('form_label_asset_folder'), 'type' => 'select', 'options' => array(0 => 'images'), 'default' => 'images')),
	'archivable' => FALSE,
	'table_actions' => array('DELETE'),
	'rows_selectable' => FALSE,
	'create_action_name' => lang('btn_upload'),
	'sanitize_images' => FALSE
);

// sitevariable module init values
$config['modules']['sitevariables'] = array(
	'module_name' => 'Site Variables',
	'model_location' => 'fuel',
	'model_name' => 'fuel_sitevariables_model',
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
	'model_location' => 'fuel',
	'model_name' => 'fuel_users_model',
	'table_headers' => array(
		'id', 
		'email', 
		'user_name', 
		'first_name', 
		'last_name', 
		'super_admin', 
		'active', 
	),
	'language_col' => FALSE, // so it won't render the dropdown filter select
	'js_controller' => 'fuel.controller.UserController',
	'display_field' => 'email',
	'preview_path' => '',
	'permission' => 'users',
	//'edit_method' => 'user_info',
	'instructions' => lang('users_instructions'),
	'archivable' => FALSE,
	'table_actions' => array(
		'EDIT',
		'DELETE' => array(
			'func' => create_function('$cols', '
				if ($cols[\'super_admin\'] != "yes") { 
					$CI =& get_instance();
					$link = "";
					if ($CI->fuel->auth->has_permission($CI->permission, "delete") AND isset($cols[$CI->model->key_field()]))
					{
						$url = site_url("/".$CI->config->item("fuel_path", "fuel").$CI->module_uri."/delete/".$cols[$CI->model->key_field()]);
						$link = "<a href=\"".$url."\">".lang("table_action_delete")."</a>";
						$link .= " <input type=\"checkbox\" name=\"delete[".$cols[$CI->model->key_field()]."]\" value=\"1\" id=\"delete_".$cols[$CI->model->key_field()]."\" class=\"multi_delete\"/>";
					}
					return $link;
				}')
			),
		'LOGIN' => array(
			'func' => create_function('$cols', '
				$CI =& get_instance();
				$link = "";
				$user = $CI->fuel->auth->user_data();
				if ($CI->fuel->auth->is_super_admin() AND ($cols[$CI->model->key_field()] != $user["id"]))
				{
					$url = site_url("/".$CI->config->item("fuel_path", "fuel").$CI->module_uri."/login_as/".$cols[$CI->model->key_field()]);
					$link = "<a href=\"".$url."\">".lang("table_action_login_as")."</a>";
				}
				return $link;
				'),
			),
		),
	'item_actions' => array('save', 'activate', 'duplicate', 'create', 'delete'),
	'clear_cache_on_save' => FALSE,
);

// permissions module init values
$config['modules']['permissions'] = array(
	'module_name' => 'Permissions',
	'model_location' => 'fuel',
	'model_name' => 'fuel_permissions_model',
	'table_headers' => array(
		'id', 
		'description', 
		'name', 
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

// permissions module init values
$config['modules']['logs'] = array(
	'module_name' => 'Activity Log',
	'model_location' => 'fuel',
	'model_name' => 'fuel_logs_model',
	'table_headers' => array(
		'id', 
		'entry_date', 
		'name', 
		'message', 
		'type', 
	),
	'default_col' => 'entry_date',
	'default_order' => 'desc',
	'display_field' => 'message',
	'preview_path' => '',
	'permission' => 'logs',
	'instructions' => lang('permissions_instructions'),
	'archivable' => FALSE,
	'item_actions' => array(),
	'table_actions' => array(),
	'rows_selectable' => FALSE,
	'clear_cache_on_save' => FALSE,
	'filters' => array(
		'type' => array('type' => 'select', 'label' => 'Type:', 'options' => array('info' => 'info', 'debug' => 'debug'), 'first_option' => lang('label_select_one')),
		),
);

//@include(APPPATH.'config/MY_fuel_modules.php');


/* End of file fuel_modules.php */
/* Location: ./modules/fuel/config/fuel_modules.php */