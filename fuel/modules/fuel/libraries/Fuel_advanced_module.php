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
		parent::__construct();
		
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
		parent::initialize($params);

		// need this here instead of the constructor, because this gets executed by 
		// the parent Fuel_base_library before the rest of the constructor'
		if ($this->has_lang())
		{
			$this->load_language();
		}
		
		$this->load_config();
		$this->_config = $this->CI->config->item($this->name);
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

	// --------------------------------------------------------------------
	
	/**
	 * Return the path to the config file
	 *
	 * @access	public
	 * @return	string
	 */
	function config_path()
	{
		return $this->server_path().'config/'.strtolower($this->name).'.php';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines if a config file exists for the advanced module
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_config()
	{
		return (file_exists($this->config_path()));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a URL specific to the advanced module (e.g. http://localhost/fuel/{advanced_module}/create)
	 *
	 * @access	public
	 * @param	string	The URI path relative to the advanced module (optional)
	 * @return	string
	 */
	function fuel_url($uri = '')
	{
		$uri = trim($uri, '/');
		return fuel_url($this->uri_path().'/'.$uri);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the URI path specific to the advanced module (e.g. fuel/{advanced_module})
	 *
	 * @access	public
	 * @return	string
	 */
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the URI path for the advanced module
	 *
	 * @access	public
	 * @param	string	The URI path relative to the advanced module
	 * @return	string
	 */
	function set_uri_path($uri_path)
	{
		$this->uri_path = $uri_path;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the server path to the advanced module (e.g. /vars/www/httpdocs/fuel/modules/{advanced_module}/)
	 *
	 * @access	public
	 * @param	string The path to the file relative to the advanced modules directory (optional)
	 * @return	string
	 */
	function server_path($path = '')
	{
		return MODULES_PATH.$this->name.'/',$path;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the web path to the advanced module (e.g. /{advanced_module})
	 *
	 * @access	public
	 * @return	string
	 */
	function web_path()
	{
		return WEB_ROOT.$this->folder();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the advanced modules main class name (e.g. Fuel_my_module)
	 *
	 * @access	public
	 * @param	boolean Whether to return the name lowercased or not (optional)
	 * @return	string
	 */
	function lib_class_name($lowercase = FALSE)
	{
		$class = 'Fuel_'.$this->name;
		if ($lowercase)
		{
			return strtolower($class);
		}
		return $class;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the server path to the advanced modules main class name (e.g. /vars/www/httpdocs/fuel/modules/{advanced_module}/Fuel_my_module)
	 *
	 * @access	public
	 * @return	string
	 */
	function lib_class_path()
	{
		return $this->server_path().'libraries/'.$this->lib_class_name().'.php';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the whether a main library class exists or not
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_lib_class()
	{
		$lib_class_path = $this->lib_class_path();
		return (file_exists($lib_class_path));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the server path to the language file
	 *
	 * @access	public
	 * @param	string The language folder to pull from. Default is 'english (optional)
	 * @param	string The language file name. Default is the modules name. (optional)
	 * @return	string
	 */
	function lang_path($lang = 'english', $file = NULL)
	{
		if (empty($file))
		{
			$file = strtolower($this->name);
		}
		return $this->server_path().'language/'.$lang.'/'.$file.'_lang.php';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the language file exists
	 *
	 * @access	public
	 * @param	string The language folder to pull from. Default is 'english (optional)
	 * @param	string The language file name. Default is the modules name. (optional)
	 * @return	boolean
	 */
	function has_lang($lang = 'english', $file = NULL)
	{
		return (file_exists($this->lang_path()));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The routes array specific to the advance module
	 *
	 * @access	public
	 * @return	array
	 */
	function routes()
	{
		if ($this->has_routes())
		{
			include($this->routes_path());
			return $route;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the server path to the advanced module's route file
	 *
	 * @access	public
	 * @return	string
	 */
	function routes_path()
	{
		return $this->server_path().'config/'.strtolower($this->name).'_routes.php';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether a routes file exists for the advanced module
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_routes()
	{
		return (file_exists($this->routes_path()));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the web path to the advanced modules CSS file
	 *
	 * @access	public
	 * @return	string
	 */
	function css_path()
	{
		$this->web_path().'assets/'.strtolower($this->name).'.css';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the CSS file exists
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_css()
	{
		return (file_exists($this->css_path()));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the navigaiton items for the advanced module
	 *
	 * @access	public
	 * @return	mixed (returns an array of navigation menu items or FALSE if none)
	 */
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

	// --------------------------------------------------------------------
	
	/**
	 * Returns the contents of the documentation view file found in the advanced modules view/_docs/index
	 *
	 * @access	public
	 * @return	string
	 */
	function docs()
	{
		return $this->CI->load->module_view($this->folder(), '_docs/index', array(), TRUE);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the server path to the advanced modules documentation
	 *
	 * @access	public
	 * @return	string
	 */
	function docs_path()
	{
		return $this->server_path().'views/_docs/index.php';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether documenation exists for the advanced module
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_docs()
	{
		return (file_exists($this->docs_path()));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether documenation exists for the advanced module
	 *
	 * @access	public
	 * @return	boolean
	 */
	function tests()
	{
		$dir_path = $this->server_path().'tests/'r;
		$tests = array();
		
		// if a directory, grab all the tests in it
		if (is_dir($dir_path))
		{
			$tests = directory_to_array($dir_path);
		}
		return $tests;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the advanced module has tests or not
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_tests()
	{
		return (!empty($this->tests()));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the advanced module has a dashboard or not
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_dashboard()
	{
		return (file_exists($this->server_path().'controllers/dashboard'.EXT));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the advanced module has a tools or not
	 *
	 * @access	public
	 * @return	boolean
	 */
	function has_tools()
	{
		return ($this->config('toolbar') !== FALSE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the tools available from the advance module
	 *
	 * @access	public
	 * @return	boolean
	 */
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

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules config file
	 *
	 * @access	public
	 * @param	string Name of config file. Default is the name of the advanced module (optional)
	 * @return	boolean
	 */
	function load_config($config = NULL)
	{
		if (empty($config))
		{
			$config = $this->name;
		}
		
		// last parameter tells it to fail gracefully
		$this->CI->load->module_config($this->folder(), $config, FALSE, TRUE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules config file
	 *
	 * @access	public
	 * @param	string Name of config file. Default is the name of the advanced module (optional)
	 * @return	boolean
	 */
	function load_helper($helper)
	{
		$this->CI->load->module_helper($this->folder(), $helper);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads an advanced modules library file
	 *
	 * @access	public
	 * @param	string Name of the library file
	 * @param	string Name you want to assign to the loaded library (optional)
	 * @return	boolean
	 */
	function load_library($library, $name = NULL)
	{
		$this->CI->load->module_library($this->folder(), $library, $name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads an advanced modules view file
	 *
	 * @access	public
	 * @param	string Name of the view file file
	 * @param	array Variables to pass to the view file (optional)
	 * @param	boolean Whether to return the contents as a string or send it to the output for display
	 * @return	boolean
	 */
	function load_view($view, $vars = array(), $return = FALSE)
	{
		if ($return)
		{
			return $this->CI->load->module_view($this->folder(), $view, $vars, TRUE);
		}

		$this->CI->load->module_view($this->folder(), $view, $vars);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules model file
	 *
	 * @access	public
	 * @param	string Name of the model file.
	 * @param	string Name you want to assign to the loaded model (optional)
	 * @return	boolean
	 */
	function load_model($model, $name = NULL)
	{
		if (substr($model, strlen($model) - 6) !='_model')
		{
			//$name = $model;
			$model = $model.'_model';
		}
		$this->CI->load->module_model($this->folder(), $model, $name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules language file
	 *
	 * @access	public
	 * @param	string Name of a language file. Default is the name of the advanced module (optional)
	 * @param	string Name of a language file folder. Default is "english" (optional)
	 * @return	boolean
	 */
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