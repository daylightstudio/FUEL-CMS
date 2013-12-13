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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
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
 * @link		http://docs.getfuelcms.com/libraries/fuel
 */

// --------------------------------------------------------------------

// include base library classes to extend
require_once('Fuel_base_library.php');
require_once('Fuel_advanced_module.php');
require_once('Fuel_modules.php');

class Fuel extends Fuel_advanced_module {
	protected $name = 'FUEL'; // name of the advanced module... usually the same as the folder name
	protected $folder = 'fuel'; // name of the folder for the advanced module
	
	 // attached objects
	protected $_attached = array();
	
	 // objects to automatically attach
	protected $_auto_attach = array(
									'admin',
									'assets',
									'auth',
									'blocks',
									'cache',
									'categories',
									'installer',
									'language',
									'layouts',
									'logs',
									'modules',
									'navigation',
									'notification',
									'pages',
									'pagevars',
									'permissions',
									'redirects',
									'settings',
									'sitevars',
									'tags',
									'users',
									);

	// the singleton instance
	private static $_instance;
	
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		self::$_instance =& $this;
		$this->fuel =& self::$_instance; // for compatibility
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
	public function initialize($config = array())
	{
		// load main fuel config
		$this->CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);

		if (!empty($config))
		{
			$this->set_config($config);
		}
		
		$this->_config = $this->CI->config->config['fuel'];
		
		// merge in any "attach" objects to include on the FUEL object
		$this->_auto_attach = array_merge($this->_auto_attach, $this->_config['attach']);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a configuration value for FUEL (overwrites Fuel_advanced_module)
	 *
	 * @access	public
	 * @param	mixed	Can be a string that references the configuration key or an array of values
	 * @param	mixed	The value of the key configuration item (only works if $item parameter is not an array) (optional)
	 * @param	string	The module to set the configuration item. Default is fuel. (optional)
	 * @return	void
	 */	
	public function set_config($item, $value = NULL, $module = 'fuel')
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
		$this->_config[$item] = $value;
		$this->CI->config->set_item($module, $fuel_config);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will attach and return FUEL library objects
	 *
	 * @access	public
	 * @param	string	The object
	 * @return	object
	 */	
	public function &__get($var)
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
						$this->CI->load->module_library($var, $lib_class, $init);
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
	 * Magic method that will call any methods on an attached object that are "get"
	 *
	 * @access	public
	 * @param	string	The object
	 * @param	string	An array of arguments
	 * @return	object
	 */	
	public function __call($name, $args)
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
	
}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */