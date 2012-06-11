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

class Fuel_install extends Fuel_base_library {
	
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
	
	function migrate()
	{
		if (isset($this->config['migration_version']))
		{
			$module = $this->fuel->modules->get($this->module);
			echo "<pre style=\"text-align: left;\">";
			print_r($module->path());
			echo "</pre>";

			$config['migration_path'] = $module->path().'install/migrations/';
			$config['migration_enabled'] = TRUE;
			$config['migration_version'] = (int)$this->config['migration_version'];
			$config['module'] = $module->name();
			echo "<pre style=\"text-align: left;\">";
			print_r($config);
			echo "</pre>";

			$this->CI->load->library('migration', $config);
			if ( ! $this->CI->migration->latest())
			{
				show_error($this->CI->migration->error_string());
			}
		}
	}
	
	function test_writable()
	{
		if (!empty($this->config['writable']))
		{
			$writable = (array) $this->config['writable'];
			$return = array();
			foreach($writable as $file)
			{
				if (!is_really_writable($file))
				{
					$return['errors'][] = $file;
				}
				else
				{
					$return['valid'][] = $file;
				}
			}
			return $return;
		}
		return array();
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
				$save['name'] = $this->config['permission'];
				$save['description'] = humanize($this->config['permission']);
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
		if (!empty($this->config['permission']))
		{
			if (is_array($this->config['permission']))
			{
				foreach($this->config['permission'] as $key => $val)
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
		}
	}
	
	// adds to DB
	function activate($module)
	{
		$installed = $this->_get_install_config($module);
		$installed[$module] = TRUE;
		//$this->fuel->settings->save($module, $key, $installed);
	//	$this->CI->fuel_settings_model->debug_query();
		//$this->fuel->modules->install($module);
		
		// test version number
		// if ($this->validate())
		// {
		// 	// create writable folders
		// 	$writable = $this->test_writable();
		// 
		// 	// create permissions
		// 	$permissions = $this->create_permissions();
		// 
			// load sql
			//$this->load_sql();
			$this->migrate();

			// display checklist notes
		// }
		
		
		
		
	}
	
	// removes from DB
	function deactivate($module)
	{

	}
	
	protected function _get_install_config($module)
	{
		$this->module = $module;

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
	
	function validate()
	{
		return !empty($this->config) AND !empty($this->module);
	}
}

/* End of file Fuel_install.php */
/* Location: ./modules/fuel/libraries/Fuel_install.php */