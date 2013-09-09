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
 * FUEL modules advanced
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_advanced_modules
 */

// --------------------------------------------------------------------

class Fuel_advanced_module extends Fuel_base_library {
	
	protected $name = ''; // name of the advanced module... usually the same as the folder name
	protected $friendly_name = ''; // used for display purposes
	protected $folder = ''; // name of the folder for the advanced module
	protected $uri_path = ''; // the uri_path to the module
	protected $_attached = array(); // attached objects to the advanced module
	protected $_config = array(); // the config information for the advanced module
	protected $_settings = NULL; // the settings information for the advanced module
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
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
	public function initialize($params = array())
	{
		parent::initialize($params);

		// need this here instead of the constructor, because this gets executed by 
		// the parent Fuel_base_library before the rest of the constructor'
		if ($this->has_lang())
		{
			$lang = (defined('FUEL_ADMIN')) ? $this->fuel->auth->user_lang() : NULL;
			$this->load_language($this->name, $lang);
		}
		$this->load_config();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will return any attached objects or sub modules attached to the object
	 *
	 * @access	public
	 * @param	string	sub module name
	 * @return	object
	 */	
	public function __get($var)
	{
		// first check the attached
		if (isset($this->_attached[$var]))
		{
			return $this->_attached[$var];
		}

		// look for sub modules magically
		$sub_module_name = $var;

		// if there is a submodule with the name $var, then we return it
		$sub_module = $this->fuel->modules->get($sub_module_name, FALSE);
		if (!empty($sub_module))
		{
			return $sub_module;
		}

		// if there is a submodule with the name {module}_$var, then we return it
		$sub_module_name = $this->name.'_'.$var;

		$sub_module = $this->fuel->modules->get($sub_module_name, FALSE);
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
	public function name()
	{
		return $this->name;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The name of the module (usually the folder name)
	 *
	 * @access	public
	 * @return	string
	 */	
	public function friendly_name()
	{
		if (empty($this->friendly_name))
		{
			return ucwords(str_replace('_', ' ', $this->name));
		}
		else
		{
			return $this->friendly_name;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * The name of the folder for the module
	 *
	 * @access	public
	 * @return	string
	 */	
	public function folder()
	{
		if (empty($this->folder))
		{
			return $this->name;
		}
		return $this->folder;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the icon for the module
	 *
	 * @access	public
	 * @return	string
	 */	
	public function icon()
	{
		return 'ico_'.url_title(str_replace('/', '_', $this->uri_path()),'_', TRUE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * The server or web path to the module
	 *
	 * @access	public
	 * @param	boolean	Either return the full path to the module or the relative web_path (optional)
	 * @return	string
	 */	
	public function path($full = TRUE)
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
	 * Returns an array of any sub modules
	 *
	 * @access	public
	 * @param	string The name of a particular sub module you want returned (optional)
	 * @return	mixed Returns an array if no parameter is set, otherwise it will return an object
	 */	
	public function submodules($sub = NULL)
	{
		static $subs = NULL;

		if (!isset($subs))
		{
			$subs = array();
			$config = $this->fuel->modules->get_module_config($this->name());

			if (!empty($config))
			{	
				$config = array_keys($config);
				foreach($config as $c)
				{
					$mod = $this->fuel->modules->get($c, FALSE);
					if (!empty($mod))
					{
						$subs[$c] = $mod;
					}
				}
			}
		}
		
		if (!empty($sub))
		{
			if (isset($subs[$sub]))
			{
				return $subs[$sub];
			}
			return FALSE;
		}
		return $subs;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The models you can load for this advanced module
	 *
	 * @access	public
	 * @param	boolean Removes the '_model' suffix
	 * @return	array
	 */	
	public function models($remove_suffix = FALSE)
	{
		$this->CI->load->helper('file');
		$model_files = get_filenames($this->path().'models/');
		$models = array();
		foreach($model_files as $key => $m)
		{
			if (preg_match('#_model.php$#', $m))
			{
				$models[$key] = substr(strtolower($m), 0, -4);

				// removes '_model'... must have this suffix to work!!!
				if ($remove_suffix)
				{
					$mod_name = substr($models[$key], 0, -6);
					$models[$key] = substr($models[$key], 0, -6);
				}
			}
			
		}
		return $models;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the model of an advanced module that assumes it's the same name
	 *
	 * @access	public
	 * @param	string 	The name of a model. If no name is provided, it will assume a model's name the same as the advanced module's (optional)
	 * @return	array
	 */	
	public function &model($model = NULL)
	{
		$models = $this->models(TRUE);
		if (empty($model))
		{
			$model = $this->name;
		}
		$this->load_model($model);
		return $this->_attached[$model.'_model'];
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
	public function attach($key, $obj = NULL)
	{
		if (isset($obj))
		{
			// check if it is a straight up object and if so attach
			if (is_object($obj))
			{
				$this->_attached[$key] =& $obj;
			}
			
			// if it's an array, then we use the key as the module value if it is a string and the value is the library to laod
			else if (is_array($obj))
			{
				$module = key($obj);
				$library = strtolower(current($obj));
				
				if (is_string($module))
				{
					$this->CI->load->module_library($module, $library);
				}
				else
				{
					$this->CI->load->library($library);
				}
				$this->_attached[$key] =& $this->CI->$library;
			}
		}
		else
		{
			if (is_array($key))
			{

				// if an array, then we loop through it to load
				foreach($key as $k => $v)
				{
					
					// check nested arrays
					if (is_array($v))
					{
						$module = key($v);
						$library = strtolower(current($v));

						if (is_string($module))
						{
							$this->CI->load->module_library($module, $library);
						}
						else
						{
							$this->CI->load->library($library);
						}
						$this->_attached[$key] =& $this->CI->$library;
					}
					else
					{
						if (is_string($k))
						{
							$this->CI->load->module_library($k, $v);
						}
						else
						{
							$this->CI->load->library($v);
						}
						
						$this->_attached[$v] =& $this->CI->$v;
					}
				}
			}
			else
			{
				// first look for a Fuel_$key library in the module's folder'
				if (file_exists($this->server_path('libraries/Fuel_'.$key.'.php')))
				{
					// this was added to prevent the loading of config files auotmatically
					$init = array('name' => $key);
					$this->load_library('fuel_'.$key, $init);
					$this->_attached[$key] =& $this->CI->{'fuel_'.$key};
				}
				
				// else just try and do a simple load
				else
				{
					$this->CI->load->library($key);
					$this->_attached[$key] =& $this->CI->$key;
				}
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an advanced module's config item
	 *
	 * @access	public
	 * @param	string	The key name for the config item
	 * @param	boolean	Determines whether or not to also look into the settings (optional)
	 * @return	mixed
	 */	
	public function config($item = NULL, $look_in_settings = TRUE)
	{

		if (!empty($item))
		{
			if ($look_in_settings AND $this->has_settings())
			{
				// if a setting exists then we return that... otherwise we continue on to the config
				if ($this->settings($item) !== FALSE)
				{
					return $this->settings($item);
				}
			}
			
			return (isset($this->_config[$item])) ? $this->_config[$item] : FALSE;
		}
		else
		{
			if ($look_in_settings)
			{
                if (is_array($this->settings()))  //change from _settings to settings() because _settings isn't populated yet and settings() populates it
                    {
					return array_merge($this->_config, $this->_settings);	
				}
			}
			return $this->_config;
		}
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
	public function set_config($item, $val)
	{
		$this->_config[$item] = $val;
		$this->CI->config->set_item($item, $val, $this->name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Return the path to the config file
	 *
	 * @access	public
	 * @return	string
	 */
	public function config_path()
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
	public function has_config()
	{
		return (file_exists($this->config_path()));
	}

	// --------------------------------------------------------------------
	
	/**
	 * An alias to the config method
	 *
	 * @access	public
	 * @param	string	The key name for the config item (optional)
	 * @return	mixed
	 */	
	public function settings($item = NULL)
	{
		if (is_null($this->_settings))
		{
			$this->_settings = $this->fuel->settings->get($this->folder());
		}
		
		if (!empty($item))
		{
			if (isset($this->_settings[$item]))
			{
				return $this->_settings[$item];
			}
			else
			{
				return FALSE;
			}
		}
		return $this->_settings;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Determines whether or not CMS configurable settings exist
	 *
	 * @access	public
	 * @return	array
	 */	
	public function has_settings()
	{
		return !empty($this->_config['settings']);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the settings array from the config file which can be used in the CMS
	 *
	 * @access	public
	 * @param	string	The setting key. If left blank, then all the settings are returned (optional)
	 * @return	array
	 */	
	public function settings_fields($setting = NULL)
	{
		$settings = $this->config('settings');
		if (!empty($setting))
		{
			if (isset($settings[$setting]))
			{
				return $settings[$setting];
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return $settings;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a URL specific to the advanced module (e.g. http://localhost/fuel/{advanced_module}/create)
	 *
	 * @access	public
	 * @param	string	The URI path relative to the advanced module (optional)
	 * @return	string
	 */
	public function fuel_url($uri = '')
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
	public function uri_path()
	{
		static $routes;
		
		// if uri path is not set, then we grab the first one on the routes
		if (empty($this->uri_path))
		{
			$routes_file = $this->server_path().'config/'.$this->folder().'_routes.php';
			if (file_exists($routes_file))
			{
				@include($routes_file);
				if (isset($route))
				{
					$this->uri_path = str_replace(FUEL_ROUTE, '', key($route));
				}
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
	public function set_uri_path($uri_path)
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
	public function server_path($path = '')
	{
		return MODULES_PATH.$this->folder().'/'.$path;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the web path to the advanced module (e.g. /{advanced_module})
	 *
	 * @access	public
	 * @return	string
	 */
	public function web_path()
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
	public function lib_class_name($lowercase = FALSE)
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
	public function lib_class_path()
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
	public function has_lib_class()
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
	public function lang_path($lang = 'english', $file = NULL)
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
	public function has_lang($lang = 'english', $file = NULL)
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
	public function routes()
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
	public function routes_path()
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
	public function has_routes()
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
	public function css_path()
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
	public function has_css()
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
	public function nav()
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
	public function docs()
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
	public function docs_path()
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
	public function has_docs()
	{
		return (file_exists($this->docs_path()));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the contents of the documentation view file found in the advanced modules view/_docs/index
	 *
	 * @access	public
	 * @param	string	module
	 * @param	string	Cache ID
	 * @param	string	Cache group ID
	 * @param	mixed	Data to save to the cache  (optional)
	 * @param	int	Time to live in seconds for cache  (optional)
	 * @return	void
	 */
	public function save_cache($cache_id, $data, $group = NULL, $ttl = NULL)
	{
		$orig_cache_path = $this->_tmp_set_cache_path();
		
		$this->_cache->save($cache_id, $data, $group, $ttl);
		
		// reset it back to what it was
		$this->fuel->cache->set_cache_path($orig_cache_path);
	}

	
	// --------------------------------------------------------------------

	/**
	 * Get and return an item from the module's cache
	 * 
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID (optional)
	 * @param	boolean	Skip checking if it is in the cache or not (optional)
	 * @return	string
	 */
	public function get_cache($cache_id, $cache_group = NULL, $skip_checking = FALSE)
	{
		$orig_cache_path = $this->_tmp_set_cache_path();

		$cached =  $this->fuel->cache->get($cache_id, $cache_group, $skip_checking);

		// reset it back to what it was
		$this->fuel->cache->set_cache_path($orig_cache_path);
		return $cached;
	}

	// --------------------------------------------------------------------

	/**
	 * Clears the cache folder
	 * 
	 * @access	public
	 * @return	void
	 */
	public function clear_cache()
	{
		return $this->fuel->cache->clear_module($this->folder());
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if the file is cached based on the cache_id passed
	 * 
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID (optional)
	 * @return	boolean
	 */
	public function is_cached($cache_id, $group = NULL)
	{
		$orig_cache_path = $this->_tmp_set_cache_path();
		
		$is_cached = $this->fuel->cache->is_cached($cache_id, $group);
	
		// reset it back to what it was
		$this->fuel->cache->set_cache_path($orig_cache_path);
		return $is_cached;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the server path to the advanced modules documentation
	 *
	 * @access	public
	 * @return	string
	 */
	public function cache_path()
	{
		return $this->server_path().'views/cache/';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Temporarily sets the cache path to the module and returns the original path
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _tmp_set_cache_path()
	{
		$orig_cache_path = $this->fuel->cache->cache_path;
		$module_cache_path = $this->cache_path();
		if (!file_exists($module_cache_path))
		{
			return FALSE;
		}
		// temporarily set the cache path to the module path
		$this->set_cache_path($module_cache_path);
		return $orig_cache_path;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether documenation exists for the advanced module
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function has_cache()
	{
		return (file_exists($this->cache_path()) AND is_writable($this->cache_path()));
	}
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether documenation exists for the advanced module
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function tests()
	{
		$dir_path = $this->server_path().'tests/';
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
	public function has_tests()
	{
		$tests = $this->tests();
		return (!empty($tests));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the advanced module has a dashboard or not
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function has_dashboard()
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
	public function has_tools()
	{
		return ($this->config('toolbar') !== FALSE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the tools available from the advance module as an array or FALSE if none exist
	 *
	 * @access	public
	 * @return	array
	 */
	public function tools()
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
	 * @return	void
	 */
	public function load_config($config = NULL)
	{
		if (empty($config))
		{
			$config = $this->name;
		}
		
		// last parameter tells it to fail gracefully
		// check application directory for overwrites
		$this->CI->load->module_config('app', $config, FALSE, TRUE);
		$app_config = $this->CI->config->item($this->name);

		// now get the blog configuration
		$this->CI->load->module_config($this->folder(), $config, FALSE, TRUE);
		$module_config = $this->CI->config->item($this->name);

		// if app config exists then merge
		if (!empty($app_config))
		{
			$this->_config = array_merge($module_config, $app_config);
		}
		else
		{
			$this->_config = $module_config;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules config file
	 *
	 * @access	public
	 * @param	string Name of config file. Default is the name of the advanced module (optional)
	 * @return	void
	 */
	public function load_helper($helper)
	{
		$this->CI->load->module_helper($this->folder(), $helper);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads an advanced modules library file and attaches it to the object
	 *
	 * @access	public
	 * @param	string Name of the library file
	 * @param	array Initialization parameters (optional)
	 * @param	string Name you want to assign to the loaded library (optional)
	 * @return	void
	 */
	public function load_library($library, $init_params = array(), $name = NULL)
	{
		$this->CI->load->module_library($this->folder(), $library, $init_params, $name);
		if (empty($name))
		{
			$name = $library;
		}
		$this->attach($name, $this->CI->$name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads an advanced modules view file
	 *
	 * @access	public
	 * @param	string Name of the view file file
	 * @param	array Variables to pass to the view file (optional)
	 * @param	boolean Whether to return the contents as a string or send it to the output for display
	 * @return	mixed	string if $return equals TRUE and void if $return equals FALSE
	 */
	public function load_view($view, $vars = array(), $return = FALSE)
	{
		if ($return)
		{
			return $this->CI->load->module_view($this->folder(), $view, $vars, TRUE);
		}
		$this->CI->load->module_view($this->folder(), $view, $vars);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules model file and attaches it to the object
	 *
	 * @access	public
	 * @param	string Name of the model file.
	 * @param	string Name you want to assign to the loaded model (optional)
	 * @return	void
	 */
	public function load_model($model, $name = NULL)
	{
		if (substr($model, strlen($model) - 6) != '_model')
		{
			$model = $model.'_model';
		}

		if (empty($name))
		{
			$name = $model;
		}
		$this->CI->load->module_model($this->folder(), $model, $name);
		$this->attach($name, $this->CI->$name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the advanced modules language file
	 *
	 * @access	public
	 * @param	string Name of a language file. Default is the name of the advanced module (optional)
	 * @param	string Name of a language file folder. Default is "english" (optional)
	 * @return	void
	 */
	public function load_language($file = '', $lang = '')
	{
		if (empty($file))
		{
			$file = strtolower($this->name);
		}
		$this->CI->load->module_language($this->folder(), $file, $lang);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Installs the modules
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function install()
	{
		return $this->fuel->installer->install($this->name());
	}

	// --------------------------------------------------------------------
	
	/**
	 * Uninstalls the modules
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function uninstall()
	{
		return $this->fuel->installer->uninstall($this->name());
	}


// --------------------------------------------------------------------
	
	/**
	 * Returns the information from the install configuration file
	 *
	 * @access	public
	 * @param	string The key to the install config you want returned (optional)
	 * @return	string
	 */
	public function install_info($key = NULL)
	{
		$install_config_path = $this->server_path().'install/install.php';
		static $config;
		if (!isset($config))
		{
			if (file_exists($install_config_path))
			{
				include($install_config_path);
			}
		}

		if (!empty($config))
		{
			if (!empty($key))
			{
				return (isset($config[$key])) ? $config[$key] : FALSE;
			}
			return $config;
		}

		return FALSE;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Overwrite this method to run your own custom builds for an advanced module.
	 * This can be done by doing it via command line: > php index.php fuel/build/{module}. 
	 * FUEL uses > php index.php fuel/build to build optimized CSS and JS files.
	 *
	 * @access	public
	 * @return	mixed
	 */
	public function build()
	{
		return FALSE;
	}
}

/* End of file Fuel_advanced_module.php */
/* Location: ./modules/fuel/libraries/Fuel_advanced_module.php */