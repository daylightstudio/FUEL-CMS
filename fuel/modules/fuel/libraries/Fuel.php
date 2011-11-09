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

// include base library classes to extend
require_once('Fuel_base_library.php');
require_once('Fuel_advanced_module.php');
require_once('Fuel_modules.php');

class Fuel extends Fuel_base_library {
	

	protected $CI;
	protected $_attached = array();
	protected $_auto_attach = array(
									'admin',
									'auth',
									'layouts',
									'pages',
									'navigation',
									'modules',
									'cache',
									'logs',
									'notification',
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

	function set_config($item, $value, $module = 'fuel')
	{
		$fuel_config = $this->CI->config->item($module);
		$fuel_config[$item] = $value;
		return $this->CI->config->set_item($module, $fuel_config);
	}
	
	function load_helper($helper, $module = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_helper($module, $helper);
	}

	function load_library($library, $module = NULL, $init = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_library($module, $library, $init);
	}

	function load_model($model, $module = NULL, $name = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		if (substr($model, strlen($model) - 6) !='_model')
		{
			$model = $model.'_model';
			if (empty($name)) $name = $model;
		}
		$this->CI->load->module_model($module, $model, $name);
	}

	function load_language($lang, $module = NULL, $name = NULL)
	{
		if (empty($module)) $module = FUEL_FOLDER;
		$this->CI->load->module_language($module, $lang);
	}
	
	function &__get($var)
	{
		if (!isset($this->_attached[$var]))
		{
			if (in_array($var, $this->_auto_attach))
			{
				$this->attach($var);
			}
			else if ($this->allowed($var))
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
					return new Fuel_advanced_module($init);
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
			$this->_attached[$key] =& $obj;
		}
		else
		{
			$init = array('name' => $key);
			$this->load_library('fuel_'.$key, FUEL_FOLDER, $init);
			$this->_attached[$key] =& $this->CI->{'fuel_'.$key};
		}
	}
	
	function allowed($module)
	{
		return (in_array($module, $this->fuel->config('modules_allowed')));
	}
	

}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */