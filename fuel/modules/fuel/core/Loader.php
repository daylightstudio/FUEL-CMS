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
 * This Library overides the original MX Loader library
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

class Fuel_Loader extends MX_Loader
{
	private $_module;
	
	public $_ci_plugins;

	public $_ci_cached_vars_scope = 'global';
	
	public function __construct() {
		
		parent::__construct();
	
		/* set the module name for Modular Separation */
		$this->_module = CI::$APP->router->fetch_module();
	}
	
	/****************************************************************************
	METHODS FOR MATCHBOX COMPATIBILITY
	****************************************************************************/
	
	/** Load config Matchbox style for backwards compatability **/
	public function module_config($module, $file = '', $use_sections = FALSE, $fail_gracefully = FALSE) {
		return $this->config($file, $use_sections, $fail_gracefully, $module);
	}

	/** Load helper Matchbox style for backwards compatability **/
	public function module_helper($module, $helper)
	{
		return $this->helper($helper, $module);
	}

	/** Load Language Matchbox style for backwards compatability **/
	public function module_language($module, $langfile, $lang = '', $return = FALSE)	{
		return $this->language($langfile, $lang, $return, $module);
	}

	/** Load Library Matchbox style for backwards compatability **/
	public function module_library($module, $library, $params = NULL, $object_name = NULL)
	{
		return $this->library($library, $params, $object_name, $module);
	}
	
	/** Load Model Matchbox style for backwards compatability **/
	public function module_model($module, $model, $object_name = NULL, $connect = FALSE)
	{
		return $this->model($model, $object_name, $connect, $module);
	}

	/** Load view Matchbox style for backwards compatability **/
	public function module_view($module, $view, $vars = array(), $return = FALSE, $scope = NULL)
	{
		return $this->view($view, $vars, $return, $scope, $module);
	}


	/****************************************************************************
	OVERWRITTEN METHODS SO WE COULD ADD THE $module parameter
	****************************************************************************/

	/** Load a module config file **/
	public function config($file = '', $use_sections = FALSE, $fail_gracefully = FALSE, $module = NULL) {
		if (!isset($module)) $module = $this->_module; // FUEL
		return CI::$APP->config->load($file, $use_sections, $fail_gracefully, $module);
	}

	/** Load the database drivers **/
	public function database($params = '', $return = FALSE, $active_record = NULL) {
		if (class_exists('CI_DB', FALSE) AND $return == FALSE AND $active_record == NULL) 
			return;

		require_once BASEPATH.'database/DB'.EXT;
		
		$db = DB($params, $active_record);
		
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
		CI::$APP->db = '';
		//CI::$APP->db = DB($params, $active_record);
		CI::$APP->db = $db;
		$this->_ci_assign_to_models();
		return CI::$APP->db;
	}

	/** Load a module helper **/
	public function helper($helper, $module = NULL) {
		if (!isset($module)) $module = $this->_module; // FUEL
		
		if (is_array($helper)) return $this->helpers($helper);
		
		if (isset($this->_ci_helpers[$helper]))	
			return;

		list($path, $_helper) = Modules::find($helper.'_helper', $module, 'helpers/');

		if ($path === FALSE) 
			return parent::helper($helper);

		Modules::load_file($_helper, $path);
		$this->_ci_helpers[$_helper] = TRUE;
	}

	/** Load a module language file **/
	public function language($langfile, $lang = '', $return = FALSE, $module = NULL)	{
		if (!isset($module)) $module = $this->_module; // FUEL
		
		if (is_array($langfile)) return $this->languages($langfile);
		return CI::$APP->lang->load($langfile, $lang, $return, $module);
	}

	/** Load a module library **/
	public function library($library, $params = NULL, $object_name = NULL, $module = NULL) {
		if (!isset($module)) $module = $this->_module; // FUEL
		
		if (is_array($library)) return $this->libraries($library);		
		
		$library_parts = explode('/', $library);
		$class = strtolower(end($library_parts)); 

    	($_alias = strtolower($object_name)) OR $_alias = $class;

    	if (isset($this->_ci_classes[$_alias]) AND $_alias == $this->_ci_classes[$_alias]) // alias change
        	return CI::$APP->$_alias;

		($_alias = strtolower($object_name)) OR $_alias = $class;
		list($path, $_library) = Modules::find($library, $module, 'libraries/');
		
		/* load library config file	 */
		if ($params == NULL) {
			list($path2, $file) = Modules::find($_alias, $module, 'config/');	
			($path2) AND $params = Modules::load_file($file, $path2, 'config');

			// FUEL check application directory
			if ($params == NULL AND file_exists(APPPATH.'/config/'.$file.EXT))
			{
				$path3 = APPPATH.'/config/';
				$params = Modules::load_file($file, $path3, 'config');
			}
		}
		
			
		if ($path === FALSE) {
			$this->_ci_load_class($library, $params, $object_name);
			$_alias = $this->_ci_classes[$class];
		} else {

			Modules::load_file($_library, $path);
			$library = ucfirst($_library);

			// look for both upper and lower cased version of the file
			foreach (array(ucfirst($class), strtolower($class)) as $class)
			{
				// FUEL fix due to allow for extending from application folder
				$subclassfile = APPPATH.'libraries/' . config_item('subclass_prefix') . $class . '.php';
				$subclass = config_item('subclass_prefix') . $class;

				if (is_file($subclassfile))
				{
					Modules::load_file($subclass, APPPATH.'libraries/');
					$library = config_item('subclass_prefix') . $class;
					break;
				}
			}
			
			// FUEL fix due to issue with SimplePie library still seeing a NULL value as a parameter
			if (!empty($params))
			{
				CI::$APP->$_alias = new $library($params);
			}
			else
			{
				CI::$APP->$_alias = new $library();
			}

			$this->_ci_classes[$class] = $_alias;
		}
		
		if (CI_VERSION < 2) $this->_ci_assign_to_models();
		return CI::$APP->$_alias;
    }

	/** Load a module model **/
	public function model($model, $object_name = NULL, $connect = FALSE, $module = NULL) {
		if (!isset($module)) $module = $this->_module; // FUEL
		
		if (is_array($model)) return $this->models($model);

		$model_parts = explode('/', $model);
		($_alias = $object_name) OR $_alias = end($model_parts);

		if (in_array($_alias, $this->_ci_models, TRUE)) 
			return CI::$APP->$_alias;
		
		list($path, $model) = Modules::find(strtolower($model), $module, 'models/');
		(CI_VERSION < 2) ? load_class('Model', FALSE) : load_class('Model', 'core');

		if ($connect !== FALSE) {
			if ($connect === TRUE) $connect = '';
			$this->database($connect, FALSE, TRUE);
		}
		
		Modules::load_file($model, $path);
		$model = ucfirst($model);
		
		CI::$APP->$_alias = new $model();
		if (CI_VERSION < 2) $this->_ci_assign_to_models();
		
		$this->_ci_models[] = $_alias;
		return CI::$APP->$_alias;
	}

	/** Load a module view **/
	public function view($view, $vars = array(), $return = FALSE, $scope = NULL, $module = NULL) {
		if (!isset($module)) $module = $this->_module; // FUEL
		list($path, $view) = Modules::find($view, $module, 'views/');
		$this->_ci_view_path = $path;
		return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return), $scope);
	}

	function _ci_load($_ci_data, $scope = NULL) {
		
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}

		if ($_ci_path == '') {
			$_ci_file = strpos($_ci_view, '.') ? $_ci_view : $_ci_view.EXT;
			$_ci_path = $this->_ci_view_path.$_ci_file;
		} else {
			$_ci_path_parts = explode('/', $_ci_path);
			$_ci_file = end($_ci_path_parts);
		}

		if ( ! file_exists($_ci_path)) 
			show_error('Unable to load the requested file: '.$_ci_file);

		//if (is_array($_ci_vars)) $this->_ci_cached_vars += $_ci_vars;
		
		//<!-- FUEL FIX
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.

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
			$this->_ci_cached_vars[$scope] = array_merge($this->_ci_cached_vars[$scope], $_ci_vars);
		}
		$vars = $this->_ci_cached_vars[$scope];
		// FUEL FIX -->

		// if the scope is set to TRUE then it is just unique to this view and is not loaded into the global view namespace so we just use that instead
		extract(($scope === TRUE) ? $_ci_vars : $this->_ci_cached_vars[$scope]);

		ob_start();

		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE) {
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		} else {
			include($_ci_path); 
		}

		log_message('debug', 'File loaded: '.$_ci_path);

		if ($_ci_return === TRUE) return ob_get_clean();
		
		if (ob_get_level() > $this->_ci_ob_level + 1) {
			ob_end_flush();
		} else {
			$this->output->append_output(ob_get_clean());
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Load class
	 *
	 * This function loads the requested class.
	 *
	 * @access	private
	 * @param	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */
	function _ci_load_class($class, $params = NULL, $object_name = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace(EXT, '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		$subdir = '';
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, $last_slash + 1);

			// Get the filename from the path
			$class = substr($class, $last_slash + 1);
		}

		// We'll test for both lowercase and capitalized versions of the file name
		foreach (array(ucfirst($class), strtolower($class)) as $class)
		{
			//<!-- FUEL
			$subclass = MODULES_PATH.$this->_module.'/libraries/'.$subdir.config_item('subclass_prefix').$class.EXT;
			if (!file_exists($subclass))
			{
				$subclass = APPPATH.'libraries/'.$subdir.config_item('subclass_prefix').$class.EXT;
			}
			// FUEL -->
			
			// Is this a class extension request?
			if (file_exists($subclass))
			{
				$baseclass = BASEPATH.'libraries/'.ucfirst($class).EXT;

				if ( ! file_exists($baseclass))
				{
					//<!-- FUEL ... changed so that base classes can be loaded from application directory too
					$baseclass = APPPATH.'libraries/'.ucfirst($class).EXT;
					
					if ( ! file_exists($baseclass))
					{
						log_message('error', "Unable to load the requested class: ".$class);
						show_error("Unable to load the requested class: ".$class);
					}
					// FUEL -->
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($subclass, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( ! is_null($object_name))
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);
						}
					}

					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}
				
				include_once($baseclass);
				include_once($subclass);
				$this->_ci_loaded_files[] = $subclass;

				return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);
			}

			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;
			foreach ($this->_ci_library_paths as $path)
			{
				$filepath = $path.'libraries/'.$subdir.$class.EXT;

				// Does the file exist?  No?  Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($filepath, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( ! is_null($object_name))
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, '', $params, $object_name);
						}
					}

					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}

				include_once($filepath);
				$this->_ci_loaded_files[] = $filepath;
				return $this->_ci_init_class($class, '', $params, $object_name);
			}

		} // END FOREACH

		// One last attempt.  Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir == '')
		{
			$path = strtolower($class).'/'.$class;
			return $this->_ci_load_class($path, $params);
		}

		// If we got this far we were unable to find the requested class.
		// We do not issue errors if the load call failed due to a duplicate request
		if ($is_duplicate == FALSE)
		{
			log_message('error', "Unable to load the requested class: ".$class);
			show_error("Unable to load the requested class: ".$class);
		}
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

		$vars = $this->_ci_object_to_array($vars);
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

	// --------------------------------------------------------------------

	/**
	 * Fix for mysqli
	 * http://forum.getfuelcms.com/discussion/2031/mysqli-the-fuel-backup-module#Item_4
	 * @return	void
	 */
	public function dbutil()
    {

        if (! class_exists('CI_DB'))
        {
            $this->database();
        }

        $CI =& get_instance();

        // for backwards compatibility, load dbforge so we can extend dbutils off it
        // this use is deprecated and strongly discouraged
        $CI->load->dbforge();

        require_once(BASEPATH . 'database/DB_utility.php');

        // START custom >>

        // path of default db utility file
        $default_utility = BASEPATH . 'database/drivers/' . $CI->db->dbdriver . '/' . $CI->db->dbdriver . '_utility.php';

        // path of my custom db utility file
        $my_utility = APPPATH . 'libraries/MY_DB_' . $CI->db->dbdriver . '_utility.php';

        // set custom db utility file if it exists
        if (file_exists($my_utility))
        {
            $utility = $my_utility;
            $extend = 'MY_DB_';
        }
        else
        {
            $utility = $default_utility;
            $extend = 'CI_DB_';
        }

        // load db utility file
        require_once($utility);

        // set the class
        $class = $extend . $CI->db->dbdriver . '_utility';

        // << END custom

        $CI->dbutil = new $class();

    }
}

/** load the CI class for Modular Separation **/
(class_exists('CI', FALSE)) OR require dirname(__FILE__).'/Ci.php';