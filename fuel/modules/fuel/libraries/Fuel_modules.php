<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_modules {
	
	protected $_modules = array();
	protected $_allowed = array();
	protected $_cached = array();
	protected $_overwrites = NULL;
	
	function __construct($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($config = array())
	{
		// setup any intialized variables
		foreach ($config as $key => $val)
		{
			$key = '_'.$key;
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		$CI =& get_instance();
		$CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);
		$this->_allowed = $CI->config->item('modules_allowed', 'fuel');
		$this->_modules = $this->get_modules();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieve the info for a module
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	array
	 */	
	public function info($module)
	{
		if (!empty($_cached[$module])) return $_cached[$module];
		//if (!$this->is_allowed($module)) return FALSE;

		if (!isset($this->_modules[$module])) return FALSE;
		
		$CI =& get_instance();
		$CI->load->helper('inflector');
		$CI->load->helper('string');
		
		$defaults = array(
			'module_name' => humanize($module),
			'module_uri' => $module,
			'model_name' => $module.'_model',
			'model_location' => '',
			'view_location' => '',
			'display_field' => '',
			'preview_path' => '',
			'views' => array(
				'list' => '_layouts/module_list', 
				'create_edit' => '_layouts/module_create_edit', 
				'delete' => '_layouts/module_delete'),
			'permission' => $module,
			'js_controller' => 'BaseFuelController',
			'js_controller_path' => '',
			'js_controller_params' => array(),
			'js_localized' => array(),
			'js' => '',
			'edit_method' => 'find_one_array',
			'instructions' => NULL,
			'filters' => array(),
			'archivable' => TRUE,
			'table_headers' => array(),
			'table_actions' => array('EDIT', 'VIEW', 'DELETE'),
			'table_row_limits' => array('25' => '25', '50' => '50', '100' => '100'),
			'item_actions' => array('save', 'view', 'publish', 'activate', 'delete', 'duplicate', 'create'),
			'list_actions' => array(),
			'rows_selectable' => TRUE,
			'precedence_col' => 'precedence',
			'clear_cache_on_save' => TRUE,
			'create_action_name' => lang('btn_create'),
			'configuration' => '',
			'nav_selected' => NULL,
			'default_col' => NULL,
			'default_order' => NULL,
			'sanitize_input' => TRUE,
			'sanitize_images' => TRUE,
			'displayonly' => FALSE,
			'language' => '',
			'hidden' => FALSE,
			);
		$return = array();
		$params = $this->_modules[$module];

		foreach ($defaults as $key => $val)
		{
			if (isset($params[$key]))
			{
				$return[$key] = $params[$key];
			}
			else
			{
				$return[$key] = $val;
			}
		}
		
		// localize certain fields
		if ($module_name = lang('module_'.$module))
		{
			$return['module_name'] = $module_name;
		}
		
		// set instructions
		if (empty($return['instructions']))
		{
			$return['instructions'] = lang('module_instructions_default', strtolower($return['module_name']));
		}
		
		// set proper jqxController name
		if (is_array($return['js_controller']))
		{
			if (empty($return['js_controller_path']))
			{
				$return['js_controller_path'] = js_path('', key($return['js_controller']));
			}
			$return['js_controller'] = current($return['js_controller']);
		}
		else if (is_string($return['js_controller']) AND strpos($return['js_controller'], '.') === FALSE)
		{
			$return['js_controller'] = 'fuel.controller.'.$return['js_controller'];
		}

		// convert slashes to jqx object periods
		$return['js_controller'] = str_replace('/', '.', $return['js_controller']);
		
		// set the base path to the controller file if still empty
		if (empty($return['js_controller_path']))
		{
			$return['js_controller_path'] = js_path('', FUEL_FOLDER);
		}
		
		
		
		if ($create_action_name = lang('module_'.$module.'_create'))
		{
			$return['create_action_name'] = $create_action_name;
		}
		
		$_cached[$module] = $return;
		return $return;
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Allowed modules found in the FUEL config
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	array
	 */	
	function allowed($module)
	{
		return $this->_allowed;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is the module passed found in the FUEL config
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	boolean
	 */	
	function is_allowed($module)
	{
		return in_array($module, $this->_allowed);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the module init info before being merged with defaults
	 *
	 * @access	public
	 * @return	array
	 */	
	function get_modules()
	{
		$CI =& get_instance();
		$module_init = $this->_modules;
		foreach($this->_allowed as $module)
		{
			// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
			if (file_exists(MODULES_PATH.$module.'/config/'.$module.'_fuel_modules.php'))
			{
				$CI->config->module_load($module, $module.'_fuel_modules');
				include(MODULES_PATH.$module.'/config/'.$module.'_fuel_modules.php');
				$module_init = array_merge($module_init, $config['modules']);
			}
		}
		
		
		// now must loop through the array and overwrite any values... array_merge_recursive won't work'
		$overwrites = $this->module_overwrites();
		if (!empty($overwrites) AND is_array($overwrites))
		{
			foreach($overwrites as $module => $val)
			{
				$module_init[$module] = array_merge($module_init[$module], $val);
			}
		}
		return $module_init;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the module init info before being merged with defaults
	 *
	 * @access	public
	 * @return	array
	 */	
	function get_pages()
	{
		$CI =& get_instance();
		$modules = $this->get_modules();

		$pages = array();
		foreach($modules as $mod => $module)
		{
			$info = $this->info($mod);

			if (!empty($info['model_location']))
			{
				$CI->load->module_model($info['model_location'], $info['model_name']);
			}
			else
			{
				$CI->load->model($info['model_name']);
			}
			
			$model = $info['model_name'];

			if (method_exists($model, 'find_all_array'))
			{
				$records = $CI->$model->find_all_array();
			}
			
			foreach($records as $record)
			{
				// need to put in global namesapce for preg_replace_callback to access
				preg_match_all('#{(\w+)}#', $info['preview_path'], $matches);
				$page = $info['preview_path'];
				$replaced = FALSE;
				if (!empty($matches[1]))
				{
					foreach($matches[1] as $match)
					{
						if (!empty($record[$match]))
						{
							$page = str_replace('{'.$match.'}', $record[$match], $page);
							$replaced = TRUE;
						}
					}
				}

				if (!empty($replaced))
				{
					$pages[$page] = $page;
				}
			}
			
		}
		return $pages;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Module overwrites
	 *
	 * @access	public
	 * @return	string
	 */	
	function module_overwrites()
	{
		if (isset($this->_overwrites))
		{
			return $this->_overwrites;
		}
		
		@include(APPPATH.'config/MY_fuel_modules.php');
		
		if (isset($config['module_overwrites']))
		{
			$this->_overwrites = $config['module_overwrites'];
		}
		else
		{
			$this->_overwrites = array();
		}
		return $this->_overwrites;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The server path to a module
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	string
	 */	
	static function module_path($module)
	{
		return MODULES_PATH.$module.'/';
	}

	

}
/* End of file Fuel_modules.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_modules.php */