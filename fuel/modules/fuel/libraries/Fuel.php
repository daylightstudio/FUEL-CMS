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
 * FUEL master object
 *
 * The master FUEL object that other objects attach to
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel
 */

// --------------------------------------------------------------------

// include base library classes to extend
require_once('Fuel_base_library.php');
require_once('Fuel_advanced_module.php');
require_once('Fuel_modules.php');

class Fuel extends Fuel_base_library {
	
	protected $CI; // the super CI object
	protected $_attached = array(); // attached objects
	protected $_auto_attach = array(
									'admin',
									'auth',
									'layouts',
									'pages',
									'pagevars',
									'blocks',
									'assets',
									'navigation',
									'modules',
									'sitevars',
									'users',
									'permissions',
									'cache',
									'logs',
									'notification',
									'template',
									); // objects to automatically attach

	private static $_instance; // the singleton instance
	
	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();
		self::$_instance =& $this;
		$this->initialize();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Static method that returns the instance of the FUEL object.
	 *
	 * This object is auto-loaded and so you will most likely use $this->fuel instead of this method
	 *
	 * @access	public
	 * @return	object	
	 */	
	public static function &get_instance()
	{
		return self::$_instance;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the FUEL object
	 *
	 * Accepts an associative array as input containing the FUEL config parameters to set
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */	
	function initialize($config = array())
	{
		// load main fuel config
		$this->CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);
		
		if (!empty($config))
		{
			$this->set_config($config);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a configuration value for FUEL.
	 *
	 * @access	public
	 * @param	string	The configuration items key
	 * @param	string	The module to set the configuration item. Default is 'fuel
	 * @return	void
	 */	
	function config($item, $module = 'fuel')
	{
		return $this->CI->config->item($item, $module);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a configuration value for FUEL
	 *
	 * This object is auto-loaded and so you will most likely use $this->fuel instead of this method
	 *
	 * @access	public
	 * @param	mixed	Can be a string that references the configuration key or an array of values
	 * @param	mixed	The value of the key configuration item (only works if $item parameter is not an array)
	 * @param	string	The module to set the configuration item. Default is fuel. (optional)
	 * @return	void
	 */	
	function set_config($item, $value, $module = 'fuel')
	{
		$fuel_config = $this->CI->config->item($module);
		if (is_array($item))
		{
			foreach($item as $key => $val)
			{
				$fuel_config[$key] = $val;
			}
		}
		else
		{
			$fuel_config[$item] = $value;
		}
		$this->CI->config->set_item($module, $fuel_config);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Convenience method to load helpers from within the fuel module by default.
	 *
	 * @access	public
	 * @param	mixed	Loads helpers
	 * @param	string	The module folder to load from. Default is fuel. (optional)
	 * @return	void
	 */	
	function load_helper($helper, $module = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_helper($module, $helper);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Convenience method to load library items from within the fuel module by default.
	 *
	 * @access	public
	 * @param	mixed	Loads libraries
	 * @param	string	The module folder to load from. Default is fuel. (optional)
	 * @param	array	Initialization parameters to pass to the library class. (optional)
	 * @return	void
	 */	
	function load_library($library, $module = NULL, $init = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_library($module, $library, $init);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Convenience method to load models from within the fuel module by default.
	 *
	 * @access	public
	 * @param	mixed	Loads models
	 * @param	string	The module folder to load from. Default is fuel. (optional)
	 * @param	string	The name of the model to assign it upon intialization. (optional)
	 * @return	void
	 */	
	function load_model($model, $module = NULL, $name = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		if (substr($model, strlen($model) - 6) != '_model')
		{
			$model = $model.'_model';
			if (empty($name)) $name = $model;
		}
		$this->CI->load->module_model($module, $model, $name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Convenience method to load language from within the fuel module by default.
	 *
	 * @access	public
	 * @param	mixed	loads language file
	 * @param	string	The module folder to load from. Default is fuel. (optional)
	 * @return	void
	 */	
	function load_language($lang, $module = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_language($module, $lang);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will attach and return FUEL library objects
	 *
	 * @access	public
	 * @param	string	The object
	 * @return	object
	 */	
	function &__get($var)
	{
		if (!isset($this->_attached[$var]))
		{
			if (in_array($var, $this->_auto_attach))
			{
				$this->attach($var);
			}
			else if ($this->modules->allowed($var))
			{
				$init = array('name' => $var, 'folder' => $var);

				$fuel_class = 'Fuel_'.$var;
				if (file_exists(MODULES_PATH.$var.'/libraries/'.$fuel_class.'.php'))
				{
					$lib_class = strtolower($fuel_class);
					if (!isset($this->CI->$lib_class))
					{
						$this->load_library($lib_class, $var, $init);
					}
					return $this->CI->$lib_class;
				}
				else
				{
					$module = new Fuel_advanced_module($init);
					$this->CI->$var = $module;
					return $module;
					
				}
			}
			else
			{
				throw new Exception(lang('error_class_property_does_not_exist', $var));
			}
		}
		return $this->_attached[$var];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will call any methods on an attached object that start with "get"
	 *
	 * @access	public
	 * @param	string	The object
	 * @param	string	An array of arguments
	 * @return	object
	 */	
	function __call($name, $args)
	{
		$obj = $this->$name;
		if (method_exists($obj, 'get'))
		{
			return call_user_func_array(array($obj, 'get'), $args);
		}
		else
		{
			return $obj;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Attaches an object to the fuel object
	 *
	 * @access	public
	 * @param	string	The name of the object
	 * @param	object	The object to attach. If none is provided it will look in the fuel module. (optional)
	 * @return	void
	 */	
	function attach($key, $obj = NULL)
	{
		if (isset($obj))
		{
			$this->_attached[$key] =& $obj;
		}
		else
		{
			$init = array('name' => $key);
			$this->load_library('fuel_'.$key, FUEL_FOLDER, $init);
			$this->_attached[$key] =& $this->CI->{'fuel_'.$key};
		}
	}
}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */