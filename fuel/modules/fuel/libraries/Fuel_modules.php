<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_modules {
	
	protected $_modules = array();
	protected $_allowed = array();
	protected $_cached = array();
	
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
		if (!empty($this->_cached[$module])) return $this->_cached[$module];
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
			'js_controller_params' => array(),
			'js' => '',
			'edit_method' => 'find_one_array',
			'instructions' => lang('module_instructions_default', $module),
			'filters' => array(),
			'archivable' => TRUE,
			'table_actions' => array('EDIT', 'VIEW', 'DELETE'),
			'item_actions' => array('save', 'view', 'publish', 'activate', 'delete', 'duplicate', 'create'),
			'list_actions' => array(),
			'rows_selectable' => TRUE,
			'clear_cache_on_save' => TRUE,
			'create_action_name' => 'Create',
			'configuration' => '',
			'nav_selected' => NULL,
			'default_col' => NULL,
			'default_order' => NULL,
			'sanitize_input' => TRUE,
			'sanitize_images' => TRUE,
			'displayonly' => FALSE,
			'language' => '',
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
		$this->_cached[$module] = $return;
		
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
			if (file_exists(APPPATH.MODULES_FOLDER.'/'.$module.'/config/'.$module.'_fuel_modules.php'))
			{
				$CI->config->module_load($module, $module.'_fuel_modules');
				include(APPPATH.MODULES_FOLDER.'/'.$module.'/config/'.$module.'_fuel_modules.php');
				$module_init = array_merge($module_init, $config['modules']);
				
			}
		}
		return $module_init;
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
		return APPPATH.MODULES_FOLDER.'/'.$module.'/';
	}

	

}
/* End of file Fuel_modules.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_modules.php */