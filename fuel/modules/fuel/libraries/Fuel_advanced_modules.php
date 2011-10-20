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
 * FUEL modules advanced
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_advanced_modules
 */

// --------------------------------------------------------------------

// include base library class to extend
require_once('Fuel_base_library.php');

class Fuel_advanced_modules extends Fuel_base_library {
	
	protected $_adv_modules = array();
	
	function __construct()
	{
		parent::__construct();
		$this->initialize();
	}
	
	function initialize()
	{
		// get the allowed modules to initialize
		$allowed = $this->CI->fuel->config('modules_allowed');
		foreach($allowed as $mod)
		{
			if (file_exists(MODULES_PATH.$mod))
			{
				$init = array('name' => $mod, 'folder' => $mod);
				$this->_adv_modules[$mod] = new Fuel_advanced_module($init);
			}
		}
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Advanced module get
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	string
	 */	
	function get($folder)
	{
		if (!empty($this->_adv_modules[$folder]))
		{
			return $this->_adv_modules[$folder];
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Determines if an advanced module is allowed to be used by FUEL
	 *
	 * @access	public
	 * @param	string	module folder names
	 * @return	boolean
	 */	
	function allowed($module)
	{
		return $this->fuel->config('modules_allowed');
	}

}

class Fuel_advanced_module extends Fuel_base_library {
	
	protected $name = '';
	protected $folder = '';
	private $_config = '';
	
	function __construct($params = array())
	{
		parent::__construct($params);

	}
	
	function initialize($params)
	{
		parent::initialize($params);
		
		$this->load_config();
		$this->_config = $this->CI->config->item($this->name);
		
		if ($this->has_lang())
		{
			$this->load_language();
		}
	}
	
	function name()
	{
		return $this->name;
	}
	
	function path($full = TRUE)
	{
		if ($full)
		{
			return $this->server_path();
		}
		else
		{
			return $this->web_path();
		}
	}

	function server_path($full = TRUE)
	{
		return MODULES_PATH.$this->name.'/';
	}

	function web_path($full = TRUE)
	{
		return WEB_ROOT.$this->folder();
	}
	
	// function &lib_class()
	// {
	// 	if ($this->has_lib_class())
	// 	{
	// 		$lib_class = strtolower($this->lib_class_name());
	// 		if (!isset($this->CI->$lib_class))
	// 		{
	// 			$this->load_library($lib_class);
	// 		}
	// 		return $this->CI->$lib_class;
	// 	}
	// 	return FALSE;
	// }

	function lib_class_name()
	{
		return 'Fuel_'.$this->name;
	}
	
	function lib_class_path()
	{
		return $this->server_path().'libraries/'.$this->lib_class_name().'.php';
	}

	function has_lib_class()
	{
		$lib_class_path = $this->lib_class_path();
		return (file_exists($lib_class_path));
	}

	function folder()
	{
		return $this->folder;
	}
	
	function config($item)
	{
		return (isset($this->_config[$item])) ? $this->_config[$item] : FALSE;
	}

	function set_config($item, $val)
	{
		return $this->CI->config->set_item($item, $val, $this->name);
	}
	
	function config_path()
	{
		return $this->server_path().'config/'.strtolower($this->name).'.php';
	}
	
	function has_config()
	{
		return (file_exists($this->lang_path()));
	}
	
	function lang_path($lang = 'english', $file = NULL)
	{
		if (empty($file))
		{
			$file = strtolower($this->name);
		}
		return $this->server_path().'language/'.$lang.'/'.$file.'_lang.php';
	}
	
	function has_lang()
	{
		return (file_exists($this->lang_path()));
	}
	
	function routes()
	{
		if ($this->has_routes())
		{
			include($this->routes_path());
			return $route;
		}
	}

	function routes_path()
	{
		return $this->server_path().'config/'.strtolower($this->name).'_routes.php';
	}
	
	function has_routes()
	{
		return (file_exists($this->routes_path()));
	}
	
	function css_path()
	{
		$this->web_path().'assets/'.strtolower($this->name).'.css';
	}

	function has_css()
	{
		return (file_exists($this->css_path()));
	}
	
	function nav()
	{
		if ($this->has_config())
		{
			include($this->config_path());
			if (isset($nav))
			{
				return $nav;
			}
		}
		return FALSE;
	}

	function docs()
	{
		return $this->CI->load->module_view($this->folder(), '_docs/index', array(), TRUE);
	}
	
	function docs_path()
	{
		return $this->server_path().'views/_docs/index.php';
	}

	function has_docs()
	{
		return (file_exists($this->docs_path()));
	}
	
	function load_config($config = NULL)
	{
		if (empty($config))
		{
			$config = $this->name.'_config';
		}

		// last parameter tells it to fail gracefully
		$this->CI->load->module_config($this->folder(), $config, FALSE, TRUE);
	}

	function load_helper($helper = NULL)
	{
		if (empty($helper))
		{
			$helper = $this->name;
		}
		$this->CI->load->module_helper($this->folder(), $helper);
	}

	function load_library($library, $name = NULL)
	{
		$this->CI->load->module_library($this->folder(), $library, $name);
	}

	function load_model($model, $name = NULL)
	{
		if (substr($model, strlen($model) - 6) !='_model')
		{
			$name = $model;
			$model = $model.'_model';
		}
		$this->CI->load->module_model($this->folder(), $model, $name);
	}

	function load_language($file, $lang = '')
	{
		if (empty($file))
		{
			$file = strtolower($this->name);
		}
		$this->CI->load->module_language($this->folder(), $file, $lang);
	}
	
	
}


/* End of file Fuel_advanced_modules.php */
/* Location: ./modules/fuel/libraries/Fuel_advanced_modules.php */