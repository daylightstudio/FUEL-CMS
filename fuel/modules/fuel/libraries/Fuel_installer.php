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
 * FUEL install object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_installer
 */

// --------------------------------------------------------------------

class Fuel_installer extends Fuel_base_library {
	
	public $module = ''; // module name (NOT THE ACTUAL OBJECT SINCE IT MAY NOT BE LOADED YET!)
	public $config = array(); // the configuration settings found in the install/install.php
	
	const INSTALLED_SETTINGS_KEY = 'installed_modules';
	
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
		$this->CI->load->helper('inflector');
		$this->CI->load->helper('file');
		$this->initialize($params);
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
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the path to the install directory of the advanced module
	 *
	 * @access	public
	 * @return	string
	 */	
	public function install_path()
	{
		// module may not be installed yet so this doesn't exist
		//return $this->module->path().'install/';
		return MODULES_PATH.$this->module.'/install/';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the name of the advanced module to install
	 *
	 * @access	public
	 * @param	string	The name of the module to install
	 * @return	void
	 */	
	public function set_module($module)
	{
		$this->module = $module;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the name of the module being installed
	 *
	 * @access	public
	 * @return	string
	 */	
	public function module()
	{
		return $this->module;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Installs the advanced module by creating any permissions and database tables
	 *
	 * @access	public
	 * @param	string	The name of the module (optional)
	 * @return	void
	 */	
	public function install($module = NULL)
	{
		$config = $this->config($module);

		if ($this->is_valid())
		{
			$this->allow();
			$this->migrate_up();
			$this->install_sql();
			$this->create_permissions();
		}
		else
		{
			$this->_add_error(lang('module_incompatible'));
		}

		if (!$this->has_errors())
		{
			return TRUE;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Uninstalls the advanced module by removing any permissions and database tables
	 *
	 * @access	public
	 * @param	string	The name of the module (optional)
	 * @return	void
	 */	
	public function uninstall($module = NULL)
	{
		$config = $this->config($module);

		if ($this->is_valid())
		{	
			$this->migrate_down();
			$this->uninstall_sql();
			$this->remove_permissions();
			$this->disallow();
		}
		else
		{
			$this->_add_error(lang('module_incompatible'));
		}

		if (!$this->has_errors())
		{
			return TRUE;
		}
		return FALSE;

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Runs a database migration up
	 *
	 * @access	public
	 * @return	void
	 */	
	public function migrate_up()
	{
		if (!empty($this->config['migration_version']))
		{
			$this->_migrate();
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Runs a database migration down
	 *
	 * @access	public
	 * @return	void
	 */	
	public function migrate_down()
	{
		if (!empty($this->config['migration_version']))
		{
			$this->_migrate(1);

			// 001 should already be loaded so just create the class
			$class = 'Migration_'.ucfirst($this->module);

			$path = $this->install_path().'migrations/001_'.$this->module.'.php';
			if (file_exists($path))
			{
				require_once($path);
				$migration = new $class();
				$migration->down();
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Migrates the database to a specific version
	 *
	 * @access	protected
	 * @param	int
	 * @return	void
	 */	
	protected function _migrate($version = NULL)
	{
		$config['migration_path'] = $this->install_path().'migrations/';
		$config['migration_enabled'] = TRUE;
		$config['module'] = $this->module;
		$this->CI->load->library('migration', $config);

		if (!isset($version))
		{
			$version = $this->CI->migration->latest();
		}

		if ( ! $this->CI->migration->version($version))
		{
			$this->_add_error($this->CI->migration->error_string());
		}

	}

	// --------------------------------------------------------------------
	
	/**
	 * Runs the install SQL file associated in the config
	 *
	 * @access	public
	 * @return	void
	 */	
	public function install_sql()
	{
		$basepath = $this->install_path();
		if (isset($this->config['install_sql']))
		{
			$path = $basepath.$this->config['install_sql'];
		}
		else
		{
			$path = $basepath.$this->module.'_install.sql';
		}

		if (is_file($path))
		{
			if (empty($this->CI->db))
			{
				$this->CI->load->database();	
			}
			
			$this->CI->db->load_sql($path);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Runs the uninstall SQL file associated in the config
	 *
	 * @access	public
	 * @return	void
	 */	
	public function uninstall_sql()
	{
		$basepath = $this->install_path();
		if (isset($this->config['uninstall_sql']))
		{
			$path = $basepath.$this->config['uninstall_sql'];
		}
		else
		{
			$path = $basepath.$this->module.'_uninstall.sql';
		}

		if (is_file($path))
		{
			$this->CI->db->load_sql($path);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Creates the permissions in FUEL necessary for the advanced module
	 *
	 * @access	public
	 * @return	void
	 */	
	public function create_permissions()
	{
		if (!empty($this->config['permissions']))
		{
			$permissions = $this->config['permissions'];
			if (is_array($permissions))
			{
				$i = 0;
				$save = array();
				foreach($permissions as $key => $val)
				{
					if (!empty($val))
					{
						if (is_int($key))
						{
							$this->fuel->permissions->create_simple_module_permissions($val);
							// $save[$i]['name'] = $val;
							// $save[$i]['description'] = humanize($val);
						}
						else
						{
							$save[$i]['active'] = 'yes';
							$save[$i]['name'] = $key;
							$save[$i]['description'] = $val;
						}
						$i++;
					}
				}
					
				// save multiple permissions
				if (!$this->fuel->permissions->save($save))
				{
					return FALSE;
				}
			}
			else
			{
				// save a single permission
				$save['name'] = $this->config['permissions'];
				$save['description'] = humanize($this->config['permissions']);
				if (!$this->fuel->permissions->save($save))
				{
					return FALSE;
				}
			}
			return $save;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Removes permissions for the advanced module
	 *
	 * @access	public
	 * @return	void
	 */	
	public function remove_permissions()
	{
		if (!empty($this->config['permissions']))
		{
			if (is_array($this->config['permissions']))
			{
				foreach($this->config['permissions'] as $key => $val)
				{
					if (!empty($val))
					{
						if (is_int($key))
						{
							$this->fuel->permissions->delete_simple_module_permissions($val);
						}
						else
						{
							$where['name'] = $key;
							$this->fuel->permissions->delete($where);
						}
					}
				}
			}
			else
			{
				$where['name'] = $this->config['permissions'];
				$this->fuel->permissions->delete($where);
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds the advanced module to the MY_fuel.php "modules_allowed" config array
	 *
	 * @access	public
	 * @return	void
	 */	
	public function allow()
	{
		$module = $this->module;

		// add to modules_allowed to MY_fuel and to the database
		if (!in_array($module, $this->fuel->config('modules_allowed')))
		{
			// add to advanced module config
			$my_fuel_path = APPPATH.'config/MY_fuel.php';

			$modules_allowed = $this->fuel->config('modules_allowed');
			$modules_allowed[] = $module;

			// write allowed modules to MY_fuel.php
			$this->_write_allowed_modules($modules_allowed);

			// save to database if the settings is there
			$this->_save_allowed_settings($module, $modules_allowed);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds the advanced module to the MY_fuel.php "modules_allowed" config array
	 *
	 * @access	public
	 * @return	void
	 */	
	public function disallow()
	{
		$module = $this->module;

		// remove to modules_allowed to MY_fuel and to the database
		if (in_array($module, $this->fuel->config('modules_allowed')))
		{

			$modules_allowed = $this->fuel->config('modules_allowed');
			if (($key = array_search($module, $modules_allowed)) !== FALSE)
			{
    			unset($modules_allowed[$key]);
			}

			// write allowed modules to MY_fuel.php
			$this->_write_allowed_modules($modules_allowed);

			// save to database if the settings is there
			$this->_save_allowed_settings($module, $modules_allowed);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds the advanced module to the MY_fuel.php "modules_allowed" config array
	 *
	 * @access	protected
	 * @param	array 	Allowed modules to save in MY_fuel.php
	 * @return	void
	 */	
	protected function _write_allowed_modules($allowed)
	{
		// add to advanced module config
		$my_fuel_path = APPPATH.'config/MY_fuel.php';

		$content = file_get_contents($my_fuel_path);
		$allowed_str = "\$config['modules_allowed'] = array(\n";
		foreach($allowed as $mod)
		{
			$allowed_str .= "\t\t'".$mod."',\n";
		}
		$allowed_str .= ");";

		// create variables for parsed files
		$content = preg_replace('#(\$config\[([\'|"])modules_allowed\\2\].+;)#Ums', $allowed_str, $content, 1);

		if (!write_file($my_fuel_path, $content))
		{
			$this->_add_error(lang('error_could_not_create_file', $my_fuel_path));
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Saves the module to the allowed settings in the CMS
	 *
	 * @access	protected
 	 * @param	string 	The name of the module
 	 * @param	array 	Allowed modules to save in the CMS settings
	 * @return	void
	 */	
	protected function _save_allowed_settings($module, $allowed)
	{
		// save to database if the settings is there
		$module_obj = $this->fuel->modules->get($module);
		if (!empty($module_obj) AND $this->fuel->modules->is_advanced($module_obj))
		{
			$settings = $module_obj->settings_fields();
			if (isset($settings['modules_allowed']))
			{
				$this->fuel->settings->save(FUEL_FOLDER, 'modules_allowed', $allowed);
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks that the module is comptable with the installed version of FUEL
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_compatible()
	{
		if (isset($this->config['compatibility']))
		{
			$compatibility = (float) $this->config['compatibility'];
			$fuel_version = (float) FUEL_VERSION;
			if ($compatibility < $fuel_version)
			{
				return FALSE;
			}
			return TRUE;
		}
		return TRUE;
	}

	
	// --------------------------------------------------------------------
	
	/**
	 * Validates that the module can be installed
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_valid()
	{
		return ($this->is_compatible() AND !empty($this->config) AND !empty($this->module));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the install configuration
	 *
	 * @access	public
	 * @return	void
	 */	
	public function config($module = NULL)
	{
		if (!empty($module))
		{
			$this->set_module($module);
		}

		$install_path = $this->install_path().'install.php';

		// if the install configuration file doesn't exist, then we return FALSE'
		if (!file_exists($install_path))
		{
			return FALSE;
		}

		// get the contents of the install config
		include($install_path);

		// if no $config variable found, then we return FALSE
		if (!isset($config))
		{
			return FALSE;
		}
		
		$this->config = $config;

		return $this->config;
	}
}

/* End of file Fuel_installer.php */
/* Location: ./modules/fuel/libraries/Fuel_installer.php */