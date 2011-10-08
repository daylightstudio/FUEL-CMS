<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL module object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel
 */

// --------------------------------------------------------------------

// include base library class to extend
require_once('Fuel_base_library.php');

class Fuel_modules extends Fuel_base_library {

	protected $_modules = array();
	protected $_modules_grouped = array();
	protected $_overwrites;
	
	function __construct()
	{
		parent::__construct();
		$this->initialize();
	}
	
	function initialize()
	{
		// get simple module init values. Must use require here because of the construct
		//require_once(MODULES_PATH.FUEL_FOLDER.'/libraries/fuel_modules.php');
		$allowed = $this->CI->fuel->config('modules_allowed');

		// get FUEL modules first
		include(MODULES_PATH.FUEL_FOLDER.'/config/fuel_modules.php');
		$module_init = $config['modules'];

		$this->_modules_grouped['app'] = $module_init;
		
		// then get the allowed modules initialization information
		foreach($allowed as $mod)
		{
			if (file_exists(MODULES_PATH.$mod.'/config/'.$mod.'_fuel_modules.php'))
			{
				include(MODULES_PATH.$mod.'/config/'.$mod.'_fuel_modules.php');
				if (!empty($config['modules']))
				{
					$config['modules']['folder'] = $mod;
					$this->_modules_grouped[$mod] = $config['modules'];
					$module_init = array_merge($module_init, $config['modules']);
				}
			}
		}

		// now must loop through the array and overwrite any values... array_merge_recursive won't work'
		$overwrites = $this->overwrites();
		if (!empty($overwrites) AND is_array($overwrites))
		{
			foreach($overwrites as $module => $val)
			{
				$module_init[$module] = array_merge($module_init[$module], $val);
			}
		}
		
		// create module objects based on init values
		foreach($module_init as $mod => $init)
		{
			$this->add($mod, $init);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add a module 
	 *
	 * @access	public
	 * @param	string	module name
	 * @param	array	module initialization parameters
	 * @return	string
	 */	
	function add($mod, $init)
	{
		$fuel_module = new Fuel_module();
		$fuel_module->initialize($mod, $init);
		$this->_modules[$mod] = $fuel_module;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Module get
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	string
	 */	
	function get($module)
	{
		if (!empty($this->_modules[$module]))
		{
			return $this->_modules[$module];
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Module overwrites
	 *
	 * @access	public
	 * @return	string
	 */	
	function overwrites()
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
	
	
	function pages()
	{
		$all_pages = array();
		foreach($this->_modules as $module)
		{
			$pages = $module->pages();
			$all_pages = array_merge($all_pages, $pages);
		}
		return $all_pages;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determine whether a module exists or not
	 *
	 * @access	public
	 * @return	string
	 */	
	function exists($module)
	{
		$module = $this->get($module);
		return $module !== FALSE;
	}
	
}



class Fuel_module {
	
	protected $module = '';
	protected $CI;
	protected $_init = array();
	protected $_info = array();
	
	function __construct($params = array())
	{
		$this->CI =& get_instance();
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
	function initialize($config = array(), $init = array())
	{
		// setup any intialized variables
		if (is_array($config))
		{
			$this->module = $config['module'];
			if (!empty($config['init']))
			{
				$this->_init = $config['init'];
			}
		}
		else
		{
			$this->module = $config;
			$this->_init = $init;
		}
		
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieve the info for a module
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	array
	 */	
	function info($prop = NULL)
	{
		if (empty($this->_info))
		{
			$this->CI->load->helper('inflector');
			$this->CI->load->helper('string');

			$defaults = array(
				'module_name' => humanize($this->module),
				'module_uri' => $this->module,
				'model_name' => $this->module.'_model',
				'model_location' => '',
				'view_location' => '',
				'display_field' => '',
				'preview_path' => '',
				'views' => array(
					'list' => 'modules/module_list', 
					'create_edit' => 'modules/module_create_edit', 
					'delete' => 'modules/module_delete'),
				'permission' => $this->module,
				'js_controller' => 'BaseFuelController',
				'js_controller_path' => '',
				'js_controller_params' => array(),
				'js_localized' => array(),
				'js' => '',
				'edit_method' => 'find_one_array',
				'instructions' => lang('module_instructions_default', strtolower(humanize($this->module))),
				'filters' => array(),
				'archivable' => TRUE,
				'table_headers' => array(),
				'table_actions' => array('EDIT', 'VIEW', 'DELETE'),
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
				'icon_class' => '',
				);
			$info = array();

			foreach ($defaults as $key => $val)
			{
				if (isset($this->_init[$key]))
				{
					$info[$key] = $this->_init[$key];
				}
				else
				{
					$info[$key] = $val;
				}
			}
			
			// icon class for module
			if (empty($info['icon_class']))
			{
				$info['icon_class'] = 'ico_'.url_title(str_replace('/', '_', $info['module_uri']),'_', TRUE);
			}
			
			// localize certain fields
			if ($module_name = lang('module_'.$this->module))
			{
				$info['module_name'] = $module_name;
			}

			// set proper jqxController name
			if (is_array($info['js_controller']))
			{
				if (empty($info['js_controller_path']))
				{
					$info['js_controller_path'] = js_path('', key($info['js_controller']));
				}
				$info['js_controller'] = current($info['js_controller']);
			}
			else if (is_string($info['js_controller']) AND strpos($info['js_controller'], '.') === FALSE)
			{
				$info['js_controller'] = 'fuel.controller.'.$info['js_controller'];
			}

			// convert slashes to jqx object periods
			$info['js_controller'] = str_replace('/', '.', $info['js_controller']);

			// set the base path to the controller file if still empty
			if (empty($info['js_controller_path']))
			{
				$info['js_controller_path'] = js_path('', FUEL_FOLDER);
			}



			if ($create_action_name = lang('module_'.$this->module.'_create'))
			{
				$info['create_action_name'] = $create_action_name;
			}
			$this->_info = $info;
		}
		if (empty($prop))
		{
			return $this->_info;
		}
		else if (isset($this->_info[$prop]))
		{
			return $this->_info[$prop];
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the pages of a module
	 *
	 * @access	public
	 * @return	array
	 */	
	function set_info($key, $prop)
	{
		$info = $this->info();
		if (is_array($key))
		{
			foreach($key as $k => $v)
			{
				if (isset($this->_info[$k]))
				{
					$this->_info[$k] = $v;
				}
				else
				{
					throw new Exception(lang('error_class_property_does_not_exist', $k));
				}
			}
		}
		else
		{
			if (isset($this->_info[$key]))
			{
				return $this->_info[$key];
			}
			else
			{
				throw new Exception(lang('error_class_property_does_not_exist', $key));
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the pages of a module
	 *
	 * @access	public
	 * @return	array
	 */	
	function __get($var)
	{
		$info = $this->info();
		if (isset($info[$var]))
		{
			return $info[$var];
		}
		else
		{
			throw new Exception(lang('error_class_property_does_not_exist', $var));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the pages of a module
	 *
	 * @access	public
	 * @return	array
	 */	
	function pages()
	{
		$pages = array();
		$info = $this->info();

		if (!empty($info['model_location']))
		{
			$this->CI->load->module_model($info['model_location'], $info['model_name']);
		}
		else
		{
			$this->CI->load->model($info['model_name']);
		}
		$model = $info['model_name'];
		
		$records = array();
		if (method_exists($model, 'find_all_array'))
		{
			$records = $this->CI->$model->find_all_array();
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
		
		return $pages;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the model of the module
	 *
	 * @access	public
	 * @return	string
	 */	
	function &model()
	{
		$model = $this->_info['model_name'];
		$this->CI->load->model($model);
		return $this->CI->$model;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The server path to a module
	 *
	 * @access	public
	 * @return	string
	 */	
	static function module_path()
	{
		return MODULES_PATH.$this->module.'/';
	}

	

}
/* End of file Fuel_modules.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_modules.php */