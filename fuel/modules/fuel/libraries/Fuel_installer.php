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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
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
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_install
 */

// --------------------------------------------------------------------

class Fuel_installer extends Fuel_base_library {
	
	public $module = ''; // name of the module
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
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->load->helper('inflector');
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
	function initialize($params)
	{
		parent::initialize($params);
	}

	function install_path()
	{
		return MODULES_FOLDER.'/'.$this->module.'/install/';
	}
	
	function set_module($module)
	{
		$this->module = $module;
	}

	function module()
	{
		return $this->module;
	}

	function migrate_up()
	{
		if (isset($this->config['migration_version']))
		{
			$this->_migrate();
		}
	}

	function migrate_down()
	{
		$this->_migrate(1);

		// 001 should already be loaded so just create the class
		$class = 'Migration_'.ucfirst($this->module->name());

		$path = $this->module->path().'install/migrations/001_'.$this->module->name().'.php';
		if (file_exists($path))
		{
			require_once($path);
			$migration = new $class();
			$migration->down();
		}

	}


	protected function _migrate($version = NULL)
	{
		$config['migration_path'] = $this->module->path().'install/migrations/';
		$config['migration_enabled'] = TRUE;
		$config['module'] = $this->module->name();
		$this->CI->load->library('migration', $config);

		if (!isset($version))
		{
			$version = $this->CI->migration->latest();
		}

		if ( ! $this->CI->migration->version($version))
		{
			show_error($this->CI->migration->error_string());
		}

	}

	function install_sql()
	{
		$basepath = $this->module->path().'install/';
		if (isset($this->config['install_sql']))
		{
			$path = $basepath.$this->config['install_sql'];
		}
		else
		{
			$path = $basepath.$this->module->name().'_install.sql';
		}

		if (file_exists($path))
		{
			$this->CI->db->load_sql($path);
		}
	}
	
	function uninstall_sql()
	{
		$basepath = $this->module->path().'install/';
		if (isset($this->config['uninstall_sql']))
		{
			$path = $basepath.$this->config['uninstall_sql'];
		}
		else
		{
			$path = $basepath.$this->module->name().'_uninstall.sql';
		}

		if (file_exists($path))
		{
			$this->CI->db->load_sql($path);
		}
	}
	
	function create_permissions()
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
					$save[$i]['active'] = 'yes';
					if (is_int($key))
					{
						$save[$i]['name'] = $val;
						$save[$i]['description'] = humanize($val);
					}
					else
					{
						$save[$i]['name'] = $key;
						$save[$i]['description'] = $val;
					}
					$i++;
				}
				
				// save multiple permissions
				if ($this->fuel->permissions->save($save))
				{
					return FALSE;
				}
			}
			else
			{
				// save a single permission
				$save['name'] = $this->config['permissions'];
				$save['description'] = humanize($this->config['permissions']);
				$this->fuel->permissions->save($save);
				if ($this->fuel->permissions->save($save))
				{
					return FALSE;
				}
			}
			return $save;
		}
		return FALSE;
	}
	
	function remove_permissions()
	{
		if (!empty($this->config['permissions']))
		{
			if (is_array($this->config['permissions']))
			{
				foreach($this->config['permissions'] as $key => $val)
				{
					$save = array();
					if (is_int($key))
					{
						$where['name'] = $val;
					}
					else
					{
						$where['name'] = $key;
					}
					$this->fuel->permissions->delete($were);
				}
			}
			else
			{
				$where['name'] = $this->config['permissions'];
				$this->fuel->permissions->delete($where);
			}
		}
	}
	
	// adds to DB
	function install($module = NULL)
	{
		$config = $this->config($module);

		if ($this->is_compatible())
		{

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
	
	// removes from DB
	function uninstall($module)
	{
		$config = $this->config($module);

		if ($this->is_compatible())
		{	

			$this->migrate_down();
			$this->uninstall_sql();
			$this->remove_permissions();
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

	function is_compatible()
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

	protected function _get_install_config($module)
	{
		$this->module = $module;
		$this->config();

		$install_path = MODULES_PATH.$module.'/install/install.php';
		
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
		
		// now load the module object
		if (is_string($module))
		{
			$module = $this->fuel->modules->get($module);
		}
		$key = self::INSTALLED_SETTINGS_KEY;
		
		$installed = $this->fuel->settings->get($module->name(), $key);
		if (empty($installed))
		{
			$installed = array();
		}
		return $installed;
	}

	function config($module)
	{
		if (!empty($module))
		{
			$this->set_module($module);
		}

		$this->module = $this->fuel->modules->get($this->module);
		$install_path = MODULES_PATH.$module.'/install/install.php';
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
	
	function validate()
	{
		return !empty($this->config) AND !empty($this->module);
	}
}

/* End of file Fuel_install.php */
/* Location: ./modules/fuel/libraries/Fuel_install.php */