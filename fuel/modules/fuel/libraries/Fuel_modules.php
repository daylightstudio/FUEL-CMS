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
 * FUEL modules object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/fuel/fuel_modules
 */

// --------------------------------------------------------------------

class Fuel_modules extends Fuel_base_library {

	protected $_modules = array();
	protected $_advanced = array();
	protected $_modules_grouped = array();
	protected $_overwrites;
	
	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set module initialization parameters
	 *
	 * Accepts an associative array as input, containing module initialization preferences.
	 *
	 * @access	public
	 * @param	array	Array of additional module initialiation parameters  (optional)
	 * @return	void
	 */	
	function initialize($params = array())
	{
		// get simple module init values. Must use require here because of the construct
		//require_once(MODULES_PATH.FUEL_FOLDER.'/libraries/fuel_modules.php');
		$allowed = $this->fuel->config('modules_allowed');
		$module_init = array();
		
		// load the application modules first
		$my_module_init = (array)$this->get_module_config('app');
		$this->_modules_grouped['app'] = $my_module_init;

		$fuel_module_init = (array)$this->get_module_config('fuel');
		$this->_modules_grouped['fuel'] = $module_init;
		$module_init = array_merge($my_module_init, $fuel_module_init);
		
		// no longer need these so we get rid of them
		unset($my_module_init, $fuel_module_init, $params);
		
		// then get the allowed modules initialization information
		foreach($allowed as $mod)
		{
			$mod_config = $this->get_module_config($mod);
			
			if (!empty($mod_config))
			{
				$this->_modules_grouped[$mod] = $mod_config;
				$module_init = array_merge($module_init, $mod_config);
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
	 * Returns an advanced module's simple module config information (sounds strange I know)
	 *
	 * @access	public
	 * @param	string	Advanced module folder name
	 * @return	array
	 */	
	function get_module_config($module)
	{
		switch($module)
		{
			case 'fuel':
				$file_path = FUEL_PATH.'/config/fuel_modules.php';
				break;
			case 'application': case 'app':
				$file_path = APPPATH.'/config/MY_fuel_modules.php';
				break;
			default:
				$file_path = MODULES_PATH.$module.'/config/'.$module.'_fuel_modules.php';
		}

		if (file_exists($file_path))
		{
			include($file_path);
			
			if (!empty($config['modules']))
			{
				// add folder value to the module init
				foreach($config['modules'] as $key => $val)
				{
					if (!isset($config['modules'][$key]['folder']))
					{
						$config['modules'][$key]['folder'] = $module;
					}
				}
				return $config['modules'];
			}
			else
			{
				return FALSE;
			}
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add a module 
	 *
	 * @access	public
	 * @param	string	Module name
	 * @param	array	Module initialization parameters
	 * @return	void
	 */	
	function add($mod, $init)
	{
		// check for specific module overwrites like for Fuel_navigation and Fuel_block
		if (isset($init['folder']))
		{
			$class_name = 'Fuel_'.strtolower($mod);
			$file_path = MODULES_PATH.$init['folder'].'/libraries/'.$class_name.EXT;

			if (file_exists($file_path))
			{
				// class must extend the fuel_module class to be legit
				if (strtolower(get_parent_class($class_name)) == 'fuel_module')
				{
					$this->CI->load->module_library($init['folder'], strtolower($class_name));
					$fuel_module =& $this->CI->$class_name;
				}
			}
		}
		
		if (!isset($fuel_module))
		{
			$fuel_module = new Fuel_module();
		}
		$fuel_module->initialize($mod, $init);
		$this->_modules[$mod] = $fuel_module;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a module object with the specified key name
	 *
	 * If no module parameter is passed, then an array of all simple modules will be returned
	 *
	 * @access	public
	 * @param	string	Module name (optional)
	 * @param	boolean	Whether to include advanced modules in the search. Defeault is TRUE. (optional)
	 * @return	object	Fuel_module object
	 */	
	function get($module = NULL, $include_advanced = TRUE)
	{
		if (!empty($module))
		{
	 		if (!empty($this->_modules[$module]))
			{
				return $this->_modules[$module];
			}
			else if ($this->allowed($module) AND $include_advanced)
			{
				return $this->fuel->$module;
			}
			return FALSE;
		}
		else
		{
			return $this->_modules;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Module overwrites
	 *
	 * Used to overwrite existing module configurations (e.g. pages, blocks, etc)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of all the pages
	 *
	 * @access	public
	 * @return	array
	 */	
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
	 * @param	string	Module name
	 * @param	boolean	Whether to include advanced modules in the search
	 * @return	boolean
	 */	
	function exists($module, $include_advanced = TRUE)
	{
		$module = $this->get($module, $include_advanced);
		return $module !== FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of all the advanced module objects
	 *
	 * @access	public
	 * @return	array	An array of Fuel_advanced_module objects
	 */	
	function advanced()
	{
		$advanced = array();
		foreach($this->fuel->config('modules_allowed') as $module)
		{
			$advanced[$module] =& $this->fuel->$module;
		}
		return $advanced;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determine whether a module is allowed in the MY_config
	 *
	 * @access	public
	 * @param	string	Module name
	 * @return	boolean
	 */	
	function allowed($module)
	{
		return (in_array($module, $this->fuel->config('modules_allowed')));
	}
	
	
}


// ------------------------------------------------------------------------

/**
 * FUEL module object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 */
class Fuel_module extends Fuel_base_library {
	
	protected $module = '';
	protected $_init = array();
	protected $_info = array();
	
	function __construct($params = array())
	{
		parent::__construct($params);
		
		// if the module name is still empty, then we will grab it from the class name
		if (empty($this->module))
		{
			$this->module = substr(get_class($this), 5);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 * as well as an array for simple module configuration parameters
	 *
	 * @access	public
	 * @param	array	config preferences (optional)
	 * @param	array	simple module initialization parameters (optional)
	 * @return	void
	 */	
	function initialize($params = array(), $init = array())
	{
		// setup any intialized variables
		if (is_array($params))
		{
			if (!empty($params['module']))
			{
				$this->module = $params['module'];
			}
			
			if (!empty($params['init']))
			{
				$this->_init = $params['init'];
			}
		}
		else
		{
			$this->module = $params;
			$this->_init = $init;
		}
		
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieve the info for a module
	 *
	 * @access	public
	 * @param	string	module name (optional)
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
				'sanitize_files' => FALSE,
				'displayonly' => FALSE,
				'language' => '',
				'hidden' => FALSE,
				'icon_class' => '',
				'folder' => '',
				);
			$info = array();

			foreach ($defaults as $key => $val)
			{
				// // look for variables on the CI object first
				// if (!empty($this->CI->$key))
				// {
				// 	$info[$key] = $this->CI->$key;
				// }
				// 
				// then look for variables in the module config file
				// else if (isset($this->_init[$key]))
				if (isset($this->_init[$key]))
				{
					$info[$key] = $this->_init[$key];
				}
				// otherwise use default
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
	 * Sets a simple module's property
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
	 * Returns the url based on the preview_path configuration of the simple module
	 *
	 * @access	public
	 * @param	array	data to be merged in with perview path URL
	 * @return	string
	 */	
	function url($data = array())
	{
		$preview_path = $this->info('preview_path');
		
		// substitute data values into preview path
		preg_match_all('#\{(.+)\}+#U', $preview_path, $matches);
		
		if (!empty($matches[1]))
		{
			foreach($matches[1] as $match)
			{
				if (!empty($data[$match]))
				{
					$preview_path = str_replace('{'.$match.'}', $data[$match], $preview_path);
				}
			}
		}
		return $preview_path;
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

	// --------------------------------------------------------------------
	
	/**
	 * Returns the model of the module
	 *
	 * @access	public
	 * @return	string
	 */	
	function &model()
	{
		$model = $this->info('model_name');
		$module = $this->info('folder');
		if (!isset($this->CI->$model))
		{
			$this->CI->load->module_model($module, $model);
		}
		return $this->CI->$model;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Loads a module model and creates a variable in the view that you can use to merge data 
	 *
	 * @access	public
	 * @param	array  (optional)
	 * @return	string
	 */
	function find($params = array())
	{
		$valid = array( 'find' => 'all',
						'select' => NULL,
						'where' => '', 
						'order' => '', 
						'limit' => NULL, 
						'offset' => 0, 
						'return_method' => 'auto', 
						'assoc_key' => '',
						'var' => '',
						'module' => '',
						'params' => array(),
						);

		if (!is_array($params))
		{
			$this->CI->load->helper('array');
			$params = parse_string_to_array($params);
		}

		foreach($valid as $p => $default)
		{
			$$p = (isset($params[$p])) ? $params[$p] : $default;
		}

		$model = $this->model();

		 // to get around escapinng issues we need to add spaces after =
		if (is_string($where))
		{
			$where = preg_replace('#([^>|<|!])=#', '$1 = ', $where);
		}

		// run select statement before the find
		if (!empty($select))
		{
			$model->db()->select($select, FALSE);
		}

		// retrieve data based on the method
		$data = array();
		if ($find === 'key')
		{
			$data = $model->find_by_key($where, $return_method);
			$var = $model->short_name(TRUE, TRUE);
		}
		else if ($find === 'one')
		{
			$data = $model->find_one($where, $order, $return_method);
			$var = $model->short_name(TRUE, TRUE);
		}
		else
		{
			if (empty($find) OR $find == 'all')
			{
				$data = $model->find_all($where, $order, $limit, $offset, $return_method, $assoc_key);
				$var = $model->short_name(TRUE, FALSE);
			}
			else
			{
				$method = 'find_'.$find;
				if (is_callable(array($model, $method)))
				{
					if (!empty($where)) $model->db()->where($where);
					if (!empty($order)) $model->db()->order_by($order);
					if (!empty($offset)) $model->db()->offset($offset);
					
					$data = call_user_func_array(array($model, $method), $params);
					//$data = $model->$method($where, $order, $limit, $offset);
					if (is_array($data) AND key($data) === 0)
					{
						$var = $model->short_name(TRUE, FALSE);
					}
					else
					{
						$var = $model->short_name(TRUE, TRUE);
					}
				}

			}
		}
		$vars[$var] = $data;

		// load the variable for the view to use
		$this->CI->load->vars($vars);

		// set the model to readonly so no data manipulation can occur
		$model->readonly = TRUE;
		return $data;
	}
	
	// --------------------------------------------------------------------

	/**
	 * An alias to the modules model to save
	 *
	 * @access	public
	 * @param	arrray	An array of values to save to the module's model
	 * @return	boolean
	 */
	function save($values)
	{
		return $this->model()->save($values);
	}

	// --------------------------------------------------------------------

	/**
	 * An alias to the module's model t create a new record
	 *
	 * @access	public
	 * @param	arrray	An array of values to save to the module's model (optional)
	 * @return	object	Record_class object
	 */
	function create($values = array())
	{
		return $this->model()->create($values);
	}
	
	// --------------------------------------------------------------------

	/**
	 * An alias to the module's model t delete a  record
	 *
	 * @access	public
	 * @param	array	Where condition to use for deleting record
	 * @return	boolean
	 */
	function delete($where)
	{
		return $this->model()->delete($where);
	}

	// --------------------------------------------------------------------

	/**
	 * Magic method to find records on the module's model
	 *
	 * @access	private
	 * @param	string	Method name
	 * @param	array	Method arguments
	 * @return	array
	 */
	function __call($name, $args)
	{
		if (preg_match('#^find_#', $name))
		{
			//$find = preg_replace('#^find_#', '', $name);
			//$params['find'] = $find;
			$params['params'] = $args;
			return $this->find($params);
		}
		else
		{
			throw new Exception(lang('error_class_method_does_not_exist', $name));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method to get value of a module property
	 *
	 * @access	public
	 * @param	string	Module property
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
}

/* End of file Fuel_modules.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_modules.php */