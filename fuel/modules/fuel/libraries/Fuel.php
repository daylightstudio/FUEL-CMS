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
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel
 */

// --------------------------------------------------------------------

// include base library class to extend
require_once('Fuel_base_library.php');

class Fuel extends Fuel_base_library {
	

	protected $CI;
	protected $_attached = array();
	protected $_auto_attach = array(
									'admin',
									'auth',
									'layouts',
									'pages',
									'modules',
									'cache',
									'logs',
									'advanced_modules',
									);

	private static $_instance;
	
	//private function __construct()
	function __construct()
	{
		parent::__construct();
		self::$_instance =& $this;
		$this->initialize();
	}
	
	public static function &get_instance()
	{
		return self::$_instance;
	}
	
	function initialize()
	{
		// load main fuel config
		$this->CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);
	}
	
	function config($item, $module = 'fuel')
	{
		return $this->CI->config->item($item, $module);
	}
	
	function load_helper($helper, $module = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_helper($module, $helper);
	}

	function load_library($library, $module = NULL, $name = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_library(FUEL_FOLDER, $library, $name);
	}

	function load_model($model, $module = NULL, $name = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		if (substr($model, strlen($model) - 6) !='_model')
		{
			$name = $model;
			$model = $model.'_model';
		}
		$this->CI->load->module_model(FUEL_FOLDER, $model, $name);
	}

	function load_language($lang, $module = NULL, $name = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_language(FUEL_FOLDER, $lang);
	}
	
	function __get($var)
	{
		if (!isset($this->_attached[$var]))
		{
			if (in_array($var, $this->_auto_attach))
			{
				$this->attach($var);
			}
			else if ($this->advanced_modules->allowed($var))
			{
				$adv_module = $this->advanced_modules->get($var);
				if ($adv_module AND $adv_module->lib_class())
				{
					$obj = $adv_module->lib_class();
					$this->attach($var, $obj);
				}
			}
			else
			{
				throw new Exception(lang('error_class_property_does_not_exist', $var));
			}
		}
		return $this->_attached[$var];
	}
	
	function attach($key, $obj = NULL)
	{
		if (isset($obj))
		{
			$this->_attached[$key] = $obj;
		}
		else
		{
			$this->load_library('fuel_'.$key);
			$this->_attached[$key] =& $this->CI->{'fuel_'.$key};
		}
	}
	
	/**
	 * alias to module information
	 *
	 * @access	public
	 * @return	string
	 */	
	protected function get_navigation()
	{
		return $this->modules->get('navigation');
	}

	protected function get_blocks()
	{
		return $this->modules->get('blocks');
	}

	protected function get_sitevariables()
	{
		return $this->modules->get('sitevariables');
	}

	protected function get_blog()
	{
		return $this->modules->get('blog');
	}
	
	protected function get_users()
	{
		return $this->modules->get('users');
	}
	
	protected function get_permissions()
	{
		return $this->modules->get('permissions');
	}
	
	protected function get_activity()
	{
		return $this->modules->get('activity');
	}

}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */