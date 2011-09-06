<?php
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
 * FUEL master admin object
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel
 */

// --------------------------------------------------------------------

class Fuel {
	

	protected $CI;
	protected $_auth;
	protected $_admin;
	protected $_pages;
	protected $_modules;
	protected $_module_overwrites;

	private static $_instance;
	
	private function __construct()
	{
		$this->CI =& get_instance();
		$this->initialize();
	}
	
	static function get_instance()
	{
		if (!isset(self::$_instance))
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}
	
	function initialize()
	{
		// load main fuel config
		$this->CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);
	}
	
	function config($item)
	{
		return $this->CI->config->item($item, 'fuel');
	}
	
	function __get($var)
	{
		$method = 'get_'.$var;
		if (method_exists($this, $method))
		{
			return $this->$method();
		}
		else
		{
			throw new Exception(lang('error_class_property_does_not_exist', $var));
		}
	}
	protected function get_admin()
	{
		// lazy load ui object
		if (!isset($this->_admin))
		{
			$this->CI->load->module_library(FUEL_FOLDER, 'fuel_admin');
			$this->_admin =& $this->CI->fuel_admin;
		}
		return $this->_admin;
	}
	
	protected function get_auth()
	{
		// lazy load auth object
		if (!isset($this->_auth))
		{
			$this->CI->load->module_library(FUEL_FOLDER, 'fuel_auth');
			$this->_auth =& $this->CI->fuel_auth;
		}
		return $this->_auth;
	}
	
	function modules($module = NULL)
	{
		if (empty($this->_modules))
		{
			// get simple module init values. Must use require here because of the construct
			require_once(MODULES_PATH.FUEL_FOLDER.'/libraries/fuel_module.php');
			$this->CI->load->module_library(FUEL_FOLDER, 'fuel_module');
			$allowed = $this->config('modules_allowed');

			// get FUEL modules first
			include(MODULES_PATH.FUEL_FOLDER.'/config/fuel_modules.php');
			$module_init = $config['modules'];
			// then get the allowed modules
			foreach($allowed as $mod)
			{
				// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
				if (file_exists(MODULES_PATH.$mod.'/config/'.$mod.'_fuel_modules.php'))
				{
					include(MODULES_PATH.$mod.'/config/'.$mod.'_fuel_modules.php');
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

			// create module objects based on init values
			foreach($module_init as $mod => $init)
			{
				$fuel_module = new Fuel_module($mod, $init);
				$fuel_module->initialize($mod, $init);
				$this->_modules[$mod] = $fuel_module;
			}
		}
		
		if (empty($module))
		{
			return $this->_modules;
		}
		else if (isset($this->_modules[$module]))
		{
			return $this->_modules[$module];
		}
		else
		{
			return FALSE;
		}
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
		if (isset($this->_module_overwrites))
		{
			return $this->_module_overwrites;
		}
		
		@include(APPPATH.'config/MY_fuel_modules.php');
		
		if (isset($config['module_overwrites']))
		{
			$this->_module_overwrites = $config['module_overwrites'];
		}
		else
		{
			$this->_module_overwrites = array();
		}
		return $this->_module_overwrites;
	}
	
	
	function module_pages()
	{
		$all_pages = array();
		foreach($this->modules() as $module)
		{
			$pages = $module->pages();
			$all_pages = array_merge($all_pages, $pages);
		}
		return $all_pages;
	}
	
	function pages()
	{
		// init page object
		if (!isset($this->_pages))
		{
			$this->CI->load->module_library(FUEL_FOLDER, 'fuel_page');
			$this->_pages =& $this->CI->fuel_page;
		}
		return $this->_pages;
	}
	
	function page()
	{
		return $this->pages($module);
	}
	
	function has_module($module)
	{
		return $this->modules($module) !== FALSE;
	}
	
}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */