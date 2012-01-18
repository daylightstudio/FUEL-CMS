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

class Fuel_advanced_module extends Fuel_base_library {
	
	protected $name = ''; // name of the advanced module... usually the same as the folder name
	protected $folder = ''; // name of the folder for the advanced module
	protected $uri_path = ''; // the uri_path to the module
	protected $_attached = array(); // attached objects to the advanced module
	protected $_config = array(); // the config information for the advanced module
	
	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct($params);
		
		// initialize object if any parameters
		if (!empty($params))
		{
			$this->initialize($params);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 *
	 * @access	public
	 * @param	array	Array of initalization parameters  (optional)
	 * @return	void
	 */	
	function initialize($params = array())
	{
		// need this here instead of the constructor, because this gets executed by 
		// the parent Fuel_base_library before the rest of the constructor'
		$this->load_config();
		$this->_config = $this->CI->config->item($this->name);
		if ($this->has_lang())
		{
			$this->load_language();
		}
		parent::initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will return any sub modules attached to the object
	 *
	 * @access	public
	 * @param	string	sub module name
	 * @return	object
	 */	
	function __get($var)
	{
		// look for sub modules magically
		$sub_module_name = $this->name.'_'.$var;
		
		$sub_module = $this->fuel->modules->get($sub_module_name);
		if (!empty($sub_module))
		{
			return $sub_module;
		}
		else
		{
			throw new Exception(lang('error_class_property_does_not_exist', $var));
		}
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * The name of the module (usually the folder name)
	 *
	 * @access	public
	 * @return	string
	 */	
	function name()
	{
		return $this->name;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * The name of the folder for the module
	 *
	 * @access	public
	 * @return	string
	 */	
	function folder()
	{
		if (empty($this->folder))
		{
			return $this->name;
		}
		return $this->folder;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * The server or web path to the module
	 *
	 * @access	public
	 * @param	boolean	Either return the full path to the module or the relative web_path (optional)
	 * @return	string
	 */	
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
	
	// --------------------------------------------------------------------
	
	/**
	 * The models you can load for this advanced module
	 *
	 * @access	public
	 * @return	array
	 */	
	function models()
	{
		$this->CI->load->helper('file');
		$model_files = get_filenames($this->path().'models/');
		$models = array();
		foreach($model_files as $m)
		{
			$models[] = substr(strtolower($m), 0 -4);
		}
		return $models;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Attaches other objects to this object
	 *
	 * @access	public
	 * @param	string	The name to be used for the attached object
	 * @param	object	The object to attach. If blank, then it will look for a library class of 'Fuel_{$key} in the libraries folder (optional)
	 * @return	void
	 */	
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

	// --------------------------------------------------------------------
	
	/**
	 * Returns an advanced module's config item
	 *
	 * @access	public
	 * @param	string	The key name for the config item
	 * @return	mixed
	 */	
	function config($item)
	{
		return (isset($this->_config[$item])) ? $this->_config[$item] : FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a config item for the advanced module
	 *
	 * @access	public
	 * @param	string	The key value for the config item
	 * @param	mixed	The value of the config item
	 * @return	void
	 */	
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
		return (file_exists($this->config_path()));
	}
	
	function fuel_url($uri = '')
	{
		$uri = trim($uri, '/');
		return fuel_url($this->uri_path().'/'.$uri);
	}
	
	function uri_path()
	{
		static $routes;
		
		// if uri path is not set, then we grab the first one on the routes
		if (empty($this->uri_path))
		{
			$routes_file = $this->server_path().'config/'.$this->folder().'_routes.php';
			@include($routes_file);
			if (isset($route))
			{
				$this->uri_path = str_replace(FUEL_ROUTE, '', key($route));
			}
		}
		return $this->uri_path;
	}
	
	function set_uri_path($uri_path)
	{
		$this->uri_path = $uri_path;
	}

	function server_path($full = TRUE)
	{
		return MODULES_PATH.$this->name.'/';
	}

	function web_path($full = TRUE)
	{
		return WEB_ROOT.$this->folder();
	}
	
	function lib_class_name($lowercase = FALSE)
	{
		$class = 'Fuel_'.$this->name;
		if ($lowercase)
		{
			return strtolower($class);
		}
		return $class;
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

	function has_dashboard()
	{
		return (file_exists($this->server_path().'controllers/dashboard'.EXT));
	}
	
	function has_tools()
	{
		return ($this->config('toolbar') !== FALSE);
	}

	function tools()
	{
		$toolbar = $this->config('toolbar');
		if (empty($toolbar))
		{
			return FALSE;
		}
		$tools = array();
		foreach($toolbar as $key => $val)
		{
			$url = $this->fuel_url($key);
			$tools[$url] = $val;
		}
		
		return $tools;
	}

	function load_config($config = NULL)
	{
		if (empty($config))
		{
			$config = $this->name;
		}
		
		// last parameter tells it to fail gracefully
		$this->CI->load->module_config($this->folder(), $config, FALSE, TRUE);
	}

	function load_helper($helper)
	{
		$this->CI->load->module_helper($this->folder(), $helper);
	}

	function load_library($library, $name = NULL)
	{
		$this->CI->load->module_library($this->folder(), $library, $name);
	}

	function load_view($view, $vars = array(), $return = FALSE)
	{
		if ($return)
		{
			return $this->CI->load->module_view($this->folder(), $view, $vars, TRUE);
		}

		$this->CI->load->module_view($this->folder(), $view, $vars);
	}

	function load_model($model, $name = NULL)
	{
		if (substr($model, strlen($model) - 6) !='_model')
		{
			//$name = $model;
			$model = $model.'_model';
		}
		$this->CI->load->module_model($this->folder(), $model, $name);
	}

	function load_language($file = '', $lang = '')
	{
		if (empty($file))
		{
			$file = strtolower($this->name);
		}
		$this->CI->load->module_language($this->folder(), $file, $lang);
	}
}

/* End of file Fuel_advanced_module.php */
/* Location: ./modules/fuel/libraries/Fuel_advanced_module.php */