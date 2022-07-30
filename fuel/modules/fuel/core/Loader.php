<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Some additions to the Awesome Modular Extension Library mostly for Matchbox compatibility
 *
 * This Library overrides the original MX Loader library
 *
 * @package		FUEL CMS
 * @subpackage	Third Party
 * @category	Third Party
 * @author		Changes by David McReynolds @ Daylight Studio. Original Author info is below
 */


/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Loader class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Loader.php
 *
 * @copyright	Copyright (c) Wiredesignz 2010-11-12
 * @version 	5.3.5
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/

require_once(APPPATH.'third_party/MX/Loader.php');

class Fuel_Loader extends CI_Loader
{

	protected $_module;
	
	public $_ci_plugins = array();
	public $_ci_cached_vars = array();
	public $_ci_cached_vars_scope = 'global';

	/** Initialize the loader variables **/
	public function initialize($controller = NULL) 
	{
		/* set the module name */
		$this->_module = CI::$APP->router->fetch_module();
		
		if ($controller instanceof MX_Controller) 
		{
			/* reference to the module controller */
			$this->controller = $controller;
			
			/* references to ci loader variables */
			foreach (get_class_vars('CI_Loader') as $var => $val) 
			{
				if ($var != '_ci_ob_level') 
				{
					$this->$var =& CI::$APP->load->$var;
				}
			}
		} 
		else 
		{
			parent::initialize();
			
			/* autoload module items */
			$this->_autoloader(array());
		}
		
		/* add this module path to the loader variables */
		$this->_add_module_paths($this->_module);
	}

	/** Add a module path loader variables **/
	public function _add_module_paths($module = '') 
	{
		
		if (empty($module)) return;
		
		foreach (Modules::$locations as $location => $offset) 
		{
			/* only add a module path if it exists */
			if (is_dir($module_path = $location.$module.'/') && ! in_array($module_path, $this->_ci_model_paths)) 
			{
				array_unshift($this->_ci_model_paths, $module_path);
			}
		}
	}
		/** Load a module config file **/
	public function config($file, $use_sections = FALSE, $fail_gracefully = FALSE, $module = NULL) 
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return CI::$APP->config->load($file, $use_sections, $fail_gracefully, $module);
	}

	
	/** Load a module helper **/
	public function helper($helper = array(), $module = NULL) 
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		if (is_array($helper)) return $this->helpers($helper);
		
		if (isset($this->_ci_helpers[$helper]))	return;

		list($path, $_helper) = Modules::find($helper.'_helper', $module, 'helpers/');

		if ($path === FALSE) return parent::helper($helper);

		Modules::load_file($_helper, $path);
		$this->_ci_helpers[$_helper] = TRUE;
		return $this;
	}

	/** Load an array of helpers **/
	public function helpers($helpers = array()) 
	{
		foreach ($helpers as $_helper) $this->helper($_helper);	
		return $this;
	}

	/** Load a module language file **/
	public function language($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '', $module = NULL) 
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		CI::$APP->lang->load($langfile, $idiom, $return, $add_suffix, $alt_path, $module);
		return $this;
	}
	
	public function languages($languages) 
	{
		foreach($languages as $_language) $this->language($_language);
		return $this;
	}
	
	/** Load a module library **/
	public function library($library, $params = NULL, $object_name = NULL, $module = NULL) 
	{

		if (!isset($module)) $module = $this->_module; // FUEL
		if (is_array($library)) return $this->libraries($library);		
		
		$class = strtolower(basename($library));

		if (isset($this->_ci_classes[$class]) && $_alias = $this->_ci_classes[$class])
			return $this;
			
		($_alias = is_string($object_name) && strtolower($object_name)) OR $_alias = $class;
		
		list($path, $_library) = Modules::find($library, $module, 'libraries/');
		
		/* load library config file as params */
		if ($params == NULL) 
		{
			list($path2, $file) = Modules::find($_alias, $module, 'config/');
			($path2) && $params = Modules::load_file($file, $path2, 'config');
			
			// FUEL check application directory
			if ($params == NULL AND file_exists(APPPATH.'/config/'.$file.EXT))
			{
				$path3 = APPPATH.'/config/';
				$params = Modules::load_file($file, $path3, 'config');
			}
		}	
		
		if ($path === FALSE) 
		{
			$this->_ci_load_library($library, $params, $object_name);
		} 
		else 
		{
			Modules::load_file($_library, $path);
			
			$library = ucfirst($_library);
			CI::$APP->$_alias = new $library($params);
			
			$this->_ci_classes[$class] = $_alias;
		}
		return $this;
    }

	/** Load an array of libraries **/
	public function libraries($libraries) 
	{
		foreach ($libraries as $_library) $this->library($_library);
		return $this;
	}

	/** Load a module model **/
	public function model($model, $object_name = NULL, $connect = FALSE, $module = NULL) 
	{
		if (!isset($module)) $module = $this->_module; // FUEL

		if (is_array($model)) return $this->models($model);

		($_alias = $object_name) OR $_alias = basename($model);

		if (in_array($_alias, $this->_ci_models, TRUE)) 
			return $this;
			
		/* check module */
		list($path, $_model) = Modules::find(strtolower($model), $module, 'models/');
		
		if ($path == FALSE)
		{
			/* check application & packages */
			parent::model($model, $object_name, $connect);
		} 
		else 
		{
			class_exists('CI_Model', FALSE) OR load_class('Model', 'core');
			
			if ($connect !== FALSE && ! class_exists('CI_DB', FALSE)) 
			{
				if ($connect === TRUE) $connect = '';
				$this->database($connect, FALSE, TRUE);
			}
			
			Modules::load_file($_model, $path);
			
			$model = ucfirst($_model);
			CI::$APP->$_alias = new $model();
			
			$this->_ci_models[] = $_alias;
		}
		return $this;
	}

	/** Load an array of models **/
	public function models($models) 
	{
		foreach ($models as $_model) $this->model($_model);	
		return $this;
	}

	/** Load a module controller **/
	public function module($module, $params = NULL)	
	{
		if (is_array($module)) return $this->modules($module);

		$_alias = strtolower(basename($module));
		CI::$APP->$_alias = Modules::load(array($module => $params));
		return $this;
	}

	/** Load an array of controllers **/
	public function modules($modules) 
	{
		foreach ($modules as $_module) $this->module($_module);
		return $this;	
	}

	/** Load a module plugin **/
	public function plugin($plugin)	
	{	
		if (is_array($plugin)) return $this->plugins($plugin);		
		
		if (isset($this->_ci_plugins[$plugin]))	
			return $this;

		list($path, $_plugin) = Modules::find($plugin.'_pi', $this->_module, 'plugins/');	
		
		if ($path === FALSE && ! is_file($_plugin = APPPATH.'plugins/'.$_plugin.EXT)) 
		{	
			show_error("Unable to locate the plugin file: {$_plugin}");
		}

		Modules::load_file($_plugin, $path);
		$this->_ci_plugins[$plugin] = TRUE;
		return $this;
	}

	/** Load an array of plugins **/
	public function plugins($plugins) 
	{
		foreach ($plugins as $_plugin) $this->plugin($_plugin);
		return $this;	
	}

	/** Load a module view **/
	public function view($view, $vars = array(), $return = FALSE, $scope = NULL, $module = NULL) 
	{
		if (!isset($module)) $module = $this->_module; // <!-- FUEL

		list($path, $_view) = Modules::find($view, $module, 'views/');

		if ($path != FALSE) 
		{
			$this->_ci_view_paths = array($path => TRUE) + $this->_ci_view_paths;
			$view = $_view;
		}
		$this->_ci_view_path = $path; // <!-- FUEL
		return $this->_ci_load(array('_ci_view' => $view, '_ci_path' => $path, '_ci_vars' => $this->_ci_prepare_view_vars($vars), '_ci_return' => $return), $scope);
	}

	public function _ci_load($_ci_data, $scope = NULL) 
	{
		extract($_ci_data);
		
		if (isset($_ci_view)) 
		{
			$_ci_path = '';
			
			/* add file extension if not provided */
			$_ci_file = (pathinfo($_ci_view, PATHINFO_EXTENSION)) ? $_ci_view : $_ci_view.EXT;
			$_ci_path = $this->_ci_view_path.$_ci_file;

			if ( ! file_exists($_ci_path))
			{
				// <!-- FUEL...will call issues if you have the same view file in different modules
				foreach ($this->_ci_view_paths as $path => $cascade) 
				{
					if (file_exists($view = $path.$_ci_file)) 
					{
						$_ci_path = $view;
						break;
					}
					if ( ! $cascade) break;
				}
			}
			
		} 
		elseif (isset($_ci_path)) 
		{
			$_ci_file = basename($_ci_path);
			if( ! file_exists($_ci_path)) $_ci_path = '';
		}

		if (empty($_ci_path)) 
			show_error('Unable to load the requested file: '.$_ci_file);

		$_ci_CI =& get_instance();
		foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var)
		{
			if ( ! isset($this->$_ci_key))
			{
				$this->$_ci_key =& $_ci_CI->$_ci_key;
			}
		}

		if (empty($scope))
		{
			$scope = $this->_ci_cached_vars_scope;
		}
		if (!isset($this->_ci_cached_vars[$scope]))
		{
			$this->_ci_cached_vars[$scope] = array();
		}
		if (is_array($_ci_vars))
		{
			$this->_ci_cached_vars[$scope] = array_merge($this->_ci_cached_vars[$scope], (array) $_ci_vars);
		}
		$vars = $this->_ci_cached_vars[$scope];

		// if the scope is set to TRUE then it is just unique to this view and is not loaded into the global view namespace so we just use that instead
		extract(($scope === TRUE) ? $_ci_vars : $this->_ci_cached_vars[$scope]);

		ob_start();

		if ( ! is_php('5.4') && ! ini_get('short_open_tag') && config_item('rewrite_short_tags') === TRUE)
		{
			echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else 
		{
			include($_ci_path); 
		}

		log_message('debug', 'File loaded: '.$_ci_path);

		if ($_ci_return == TRUE) return ob_get_clean();

		if (ob_get_level() > $this->_ci_ob_level + 1) 
		{
			ob_end_flush();
		} 
		else 
		{
			CI::$APP->output->append_output(ob_get_clean());
		}
	}

	protected function &_ci_get_component($component) 
	{
		return CI::$APP->$component;
	} 

	
	/****************************************************************************
	METHODS FOR MATCHBOX COMPATIBILITY
	****************************************************************************/
	
	/** Load config Matchbox style for backwards compatibility **/
	public function module_config($module, $file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return $this->config($file, $use_sections, $fail_gracefully, $module);
	}

	/** Load helper Matchbox style for backwards compatibility **/
	public function module_helper($module, $helper)
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return $this->helper($helper, $module);
	}

	/** Load Language Matchbox style for backwards compatibility **/
	public function module_language($module, $langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return $this->language($langfile, $idiom, $return, $add_suffix, $alt_path, $module);
	}

	/** Load Library Matchbox style for backwards compatibility **/
	public function module_library($module, $library, $params = NULL, $object_name = NULL)
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return $this->library($library, $params, $object_name, $module);
	}
	
	/** Load Model Matchbox style for backwards compatibility **/
	public function module_model($module, $model, $object_name = NULL, $connect = FALSE)
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return $this->model($model, $object_name, $connect, $module);
	}

	/** Load view Matchbox style for backwards compatibility **/
	public function module_view($module, $view, $vars = array(), $return = FALSE, $scope = NULL)
	{
		if (!isset($module)) $module = $this->_module; // FUEL
		return $this->view($view, $vars, $return, $scope, $module);
	}


	/** Load the database drivers **/
	public function database($params = '', $return = FALSE, $query_builder = NULL) {
		if ($return === FALSE && $query_builder === NULL && isset($CI->db) && is_object($CI->db) && ! empty($CI->db->conn_id))
		{
			return FALSE;
		}

		require_once BASEPATH.'database/DB'.EXT;
		
		$db = DB($params, $query_builder);
		
		// <!-- FUEL
		$my_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
		$my_driver_file = APPPATH.'core/'.$my_driver.EXT;
		
		if (file_exists($my_driver_file))
		{
		    require_once($my_driver_file);
		    $db = new $my_driver(get_object_vars($db));
		}

		if ($return === TRUE) 
		{
			return $db;
		}
		//	return DB($params, $active_record);
		// FUEL -->
		//CI::$APP->db = DB($params, $active_record);
		CI::$APP->db = $db;
		//$this->_ci_assign_to_models();
		return CI::$APP->db;
	}

	// --------------------------------------------------------------------

	/**
	 * Internal CI Library Loader
	 *
	 * @used-by	CI_Loader::library()
	 * @uses	CI_Loader::_ci_init_library()
	 *
	 * @param	string	$class		Class name to load
	 * @param	mixed	$params		Optional parameters to pass to the class constructor
	 * @param	string	$object_name	Optional object name to assign to
	 * @return	void
	 */
	protected function _ci_load_library($class, $params = NULL, $object_name = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace('.php', '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, ++$last_slash);

			// Get the filename from the path
			$class = substr($class, $last_slash);
		}
		else
		{
			$subdir = '';
		}

		$class = ucfirst($class);

		// Is this a stock library? There are a few special conditions if so ...
		if (file_exists(BASEPATH.'libraries/'.$subdir.$class.'.php'))
		{
			return $this->_ci_load_stock_library($class, $subdir, $params, $object_name);
		}

		// Let's search for the requested library file and load it.
		foreach ($this->_ci_library_paths as $path)
		{
			// BASEPATH has already been checked for
			if ($path === BASEPATH)
			{
				continue;
			}

			$filepath = $path.'libraries/'.$subdir.$class.'.php';


			// Safety: Was the class already loaded by a previous call?
			if (class_exists($class, FALSE))
			{
				// Before we deem this to be a duplicate request, let's see
				// if a custom object name is being supplied. If so, we'll
				// return a new instance of the object

				// <!-- FUEL ... causes issues
				$CI =& get_instance();

				if ( ! is_null($object_name))
				{
					if ( ! isset($CI->$object_name))
					{
						return $this->_ci_init_library($class, '', $params, $object_name);
					}
				}
				elseif (!isset($CI->{strtolower($class)}))
				{
					return $this->_ci_init_library($class, '', $params, $object_name);
				}
				// <!/-- FUEL ... causes issues

				log_message('debug', $class.' class already loaded. Second attempt ignored.');
				return;
			}
			// Does the file exist? No? Bummer...
			elseif ( ! file_exists($filepath))
			{
				continue;
			}

			include_once($filepath);
			return $this->_ci_init_library($class, '', $params, $object_name);
		}

		// One last attempt. Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir === '')
		{
			return $this->_ci_load_library($class.'/'.$class, $params, $object_name);
		}

		// If we got this far we were unable to find the requested class.
		log_message('error', 'Unable to load the requested class: '.$class);
		show_error('Unable to load the requested class: '.$class);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal CI Stock Library Loader
	 *
	 * @used-by	CI_Loader::_ci_load_library()
	 * @uses	CI_Loader::_ci_init_library()
	 *
	 * @param	string	$library	Library name to load
	 * @param	string	$file_path	Path to the library filename, relative to libraries/
	 * @param	mixed	$params		Optional parameters to pass to the class constructor
	 * @param	string	$object_name	Optional object name to assign to
	 * @return	void
	 */
	protected function _ci_load_stock_library($library_name, $file_path, $params, $object_name)
	{
		$prefix = 'CI_';

		if (class_exists($prefix.$library_name, FALSE))
		{
			if (class_exists(config_item('subclass_prefix').$library_name, FALSE))
			{
				$prefix = config_item('subclass_prefix');
			}

			// Before we deem this to be a duplicate request, let's see
			// if a custom object name is being supplied. If so, we'll
			// return a new instance of the object
			if ($object_name !== NULL)
			{
				$CI =& get_instance();
				if ( ! isset($CI->$object_name))
				{
					return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
				}
			}

			log_message('debug', $library_name.' class already loaded. Second attempt ignored.');
			return;
		}

		$paths = $this->_ci_library_paths;
		array_pop($paths); // BASEPATH
		array_pop($paths); // APPPATH (needs to be the first path checked)
		array_unshift($paths, APPPATH);

		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'libraries/'.$file_path.$library_name.'.php'))
			{
				// Override
				include_once($path);
				if (class_exists($prefix.$library_name, FALSE))
				{
					return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
				}
				else
				{
					log_message('debug', $path.' exists, but does not declare '.$prefix.$library_name);
				}
			}
		}

		include_once(BASEPATH.'libraries/'.$file_path.$library_name.'.php');

		// Check for extensions
		$subclass = config_item('subclass_prefix').$library_name;
		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'libraries/'.$file_path.$subclass.'.php'))
			{
				include_once($path);
				if (class_exists($subclass, FALSE))
				{
					$prefix = config_item('subclass_prefix');
					break;
				}
				else
				{
					log_message('debug', APPPATH.'libraries/'.$file_path.$subclass.'.php exists, but does not declare '.$subclass);
				}
			}
		}

		return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal CI Library Instantiator
	 *
	 * @used-by	CI_Loader::_ci_load_stock_library()
	 * @used-by	CI_Loader::_ci_load_library()
	 *
	 * @param	string		$class		Class name
	 * @param	string		$prefix		Class name prefix
	 * @param	array|null|bool	$config		Optional configuration to pass to the class constructor:
	 *						FALSE to skip;
	 *						NULL to search in config paths;
	 *						array containing configuration data
	 * @param	string		$object_name	Optional object name to assign to
	 * @return	void
	 */
	protected function _ci_init_library($class, $prefix, $config = FALSE, $object_name = NULL)
	{
		// Is there an associated config file for this class? Note: these should always be lowercase
		if ($config === NULL)
		{
			// Fetch the config paths containing any package paths
			$config_component = $this->_ci_get_component('config');

			if (is_array($config_component->_config_paths))
			{
				$found = FALSE;
				foreach ($config_component->_config_paths as $path)
				{
					// We test for both uppercase and lowercase, for servers that
					// are case-sensitive with regard to file names. Load global first,
					// override with environment next
					if (file_exists($path.'config/'.strtolower($class).'.php'))
					{
						include($path.'config/'.strtolower($class).'.php');
						$found = TRUE;
					}
					elseif (file_exists($path.'config/'.ucfirst(strtolower($class)).'.php'))
					{
						include($path.'config/'.ucfirst(strtolower($class)).'.php');
						$found = TRUE;
					}

					if (file_exists($path.'config/'.ENVIRONMENT.'/'.strtolower($class).'.php'))
					{
						include($path.'config/'.ENVIRONMENT.'/'.strtolower($class).'.php');
						$found = TRUE;
					}
					elseif (file_exists($path.'config/'.ENVIRONMENT.'/'.ucfirst(strtolower($class)).'.php'))
					{
						include($path.'config/'.ENVIRONMENT.'/'.ucfirst(strtolower($class)).'.php');
						$found = TRUE;
					}

					// Break on the first found configuration, thus package
					// files are not overridden by default paths
					if ($found === TRUE)
					{
						break;
					}
				}
			}
		}

		$class_name = $prefix.$class;

		// Is the class name valid?
		if ( ! class_exists($class_name, FALSE))
		{
			log_message('error', 'Non-existent class: '.$class_name);
			show_error('Non-existent class: '.$class_name);
		}

		// Set the variable name we will assign the class to
		// Was a custom class name supplied? If so we'll use it
		if (empty($object_name))
		{
			$object_name = strtolower($class);
			if (isset($this->_ci_varmap[$object_name]))
			{
				$object_name = $this->_ci_varmap[$object_name];
			}
		}

		// Don't overwrite existing properties
		$CI =& get_instance();
		if (isset($CI->$object_name))
		{
			if ($CI->$object_name instanceof $class_name)
			{
				log_message('debug', $class_name." has already been instantiated as '".$object_name."'. Second attempt aborted.");
				return;
			}

			show_error("Resource '".$object_name."' already exists and is not a ".$class_name." instance.");
		}

		// Save the class name and object name
		$this->_ci_classes[$object_name] = $class;

		// Instantiate the class
		$CI->$object_name = isset($config)
			? new $class_name($config)
			: new $class_name();
	}


	// --------------------------------------------------------------------

	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within
	 * the controller class and its "view" files.
	 *
	 * @param	array
	 * @param 	string
	 * @return	void
	 */
	public function vars($vars = array(), $val = '', $scope = NULL)
	{
		if (empty($scope))
		{
			$scope = $this->_ci_cached_vars_scope;
		}
		if ($val != '' AND is_string($vars))
		{
			$vars = array($vars => $val);
		}

		$vars = $this->_ci_prepare_view_vars($vars);
		if (is_array($vars) AND count($vars) > 0)
		{
			if (!isset($this->_ci_cached_vars[$scope]))
			{
				$this->_ci_cached_vars[$scope] = array();
			}
			foreach ($vars as $key => $val)
			{
				$this->_ci_cached_vars[$scope][$key] = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Return the cached view variables
	 *
	 * @access	public
	 * @return	array
	 */	
	
	//<!-- FUEL Added... 
	function get_vars($key = NULL, $scope = NULL)
	{
		if (empty($scope))
		{
			$scope = $this->_ci_cached_vars_scope;
		}
		if (isset($key))
		{
			if (isset($this->_ci_cached_vars[$scope][$key]))
			{
				return $this->_ci_cached_vars[$scope][$key];
			}
			return NULL;
		}
		if ($scope == 'all')
		{
			return $this->_ci_cached_vars;
		}

		if (!isset($this->_ci_cached_vars[$scope]))
		{
			$this->_ci_cached_vars[$scope] = array();
		}
		return $this->_ci_cached_vars[$scope];
	}

	// --------------------------------------------------------------------

	/**
	 * Get Variable
	 *
	 * Check if a variable is set and retrieve it.
	 *
	 * @param	array
	 * @return	void
	 */
	public function get_var($key, $scope = NULL)
	{
		if (empty($scope))
		{
			$scope = $this->_ci_cached_vars_scope;
		}
		return isset($this->_ci_cached_vars[$scope][$key]) ? $this->_ci_cached_vars[$scope][$key] : NULL;
	}

	/** Autoload module items **/
	public function _autoloader($autoload) 
	{
		$path = FALSE;
		
		if ($this->_module) 
		{
			list($path, $file) = Modules::find('constants', $this->_module, 'config/');	
			
			/* module constants file */
			if ($path != FALSE) 
			{
				include_once $path.$file.EXT;
			}
					
			list($path, $file) = Modules::find('autoload', $this->_module, 'config/');
		
			/* module autoload file */
			if ($path != FALSE) 
			{
				$autoload = array_merge(Modules::load_file($file, $path, 'autoload'), $autoload);
			}
		}
		
		/* nothing to do */
		if (count($autoload) == 0) return;
		
		/* autoload package paths */
		if (isset($autoload['packages'])) 
		{
			foreach ($autoload['packages'] as $package_path) 
			{
				$this->add_package_path($package_path);
			}
		}
				
		/* autoload config */
		if (isset($autoload['config'])) 
		{
			foreach ($autoload['config'] as $config) 
			{
				$this->config($config);
			}
		}

		/* autoload helpers, plugins, languages */
		foreach (array('helper', 'plugin', 'language') as $type) 
		{
			if (isset($autoload[$type]))
			{
				foreach ($autoload[$type] as $item) 
				{
					$this->$type($item);
				}
			}
		}	
			
		/* autoload database & libraries */
		if (isset($autoload['libraries'])) 
		{
			if (in_array('database', $autoload['libraries'])) 
			{
				/* autoload database */
				if ( ! $db = CI::$APP->config->item('database')) 
				{
					$this->database();
					$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
				}
			}

			/* autoload libraries */
			foreach ($autoload['libraries'] as $library) 
			{
				$this->library($library);
			}
		}
		
		/* autoload models */
		if (isset($autoload['model'])) 
		{
			foreach ($autoload['model'] as $model => $alias)
			{
				(is_numeric($model)) ? $this->model($alias) : $this->model($model, $alias);
			}
		}
		
		/* autoload module controllers */
		if (isset($autoload['modules'])) 
		{
			foreach ($autoload['modules'] as $controller) 
			{
				($controller != $this->_module) && $this->module($controller);
			}
		}
	}
}

/** load the CI class for Modular Separation **/
(class_exists('CI', FALSE)) OR require APPPATH.'third_party/MX/Ci.php';