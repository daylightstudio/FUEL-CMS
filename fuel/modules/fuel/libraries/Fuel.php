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
	protected $_auth;
	protected $_admin;
	protected $_layouts;
	protected $_pages;
	protected $_modules;
	protected $_module_overwrites;

	//private static $_instance;
	
	//private function __construct()
	function __construct()
	{
		parent::__construct();
	}
	
	// static function get_instance()
	// {
	// 	if (!isset(self::$_instance))
	// 	{
	// 		$c = __CLASS__;
	// 		self::$_instance = new $c;
	// 	}
	// 	return self::$_instance;
	// }
	// 
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
			$this->load_library('fuel_admin');
			$this->_admin =& $this->CI->fuel_admin;
		}
		return $this->_admin;
	}
	
	protected function get_auth()
	{
		// lazy load auth object
		if (!isset($this->_auth))
		{
			$this->load_library('fuel_auth');
			$this->_auth =& $this->CI->fuel_auth;
		}
		return $this->_auth;
	}
	
	protected function get_layouts()
	{
		// lazy load layouts object
		if (!isset($this->_layouts))
		{
			$this->load_library('fuel_layouts');
			$this->_layouts =& $this->CI->fuel_layouts;
		}
		return $this->_layouts;
	}

	protected function get_modules()
	{
		// lazy load modules object
		if (!isset($this->_modules))
		{
			$this->load_library('fuel_modules');
			$this->_modules =& $this->CI->fuel_modules;
		}
		return $this->_modules;
	}
	
	protected function get_pages()
	{
		// lazy load pages object
		if (!isset($this->_pages))
		{
			$this->load_library('fuel_pages');
			$this->_pages =& $this->CI->fuel_pages;
		}
		return $this->_pages;
	}
	
	protected function get_cache()
	{
		// lazy load pages object
		if (!isset($this->_pages))
		{
			$this->load_library('fuel_pages');
			$this->_pages =& $this->CI->fuel_pages;
		}
		return $this->_pages;
	}
	
	// --------------------------------------------------------------------
	
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