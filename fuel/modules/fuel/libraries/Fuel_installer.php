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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
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
		$this->CI->load->library('cli');
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 *
	 * @access	public
	 * @param	array	Array of initialization parameters  (optional)
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
			$this->copy_config();
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
			// check if the file is a .php file and if so, include it's contents and process the string
			$data = (pathinfo($path, PATHINFO_EXTENSION) == 'php') ? include($path) : $path;
			if (empty($this->CI->db))
			{
				$this->CI->load->database();	
			}
			$this->CI->db->load_sql($data);
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
			// check if the file is a .php file and if so, include it's contents and process the string
			$data = (pathinfo($path, PATHINFO_EXTENSION) == 'php') ? include($path) : $path;

			if (empty($this->CI->db))
			{
				$this->CI->load->database();	
			}
			
			$this->CI->db->load_sql($data);
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
	 * Checks that the module is compatible with the installed version of FUEL
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_compatible()
	{
		if (isset($this->config['compatibility']))
		{
			$compatibility = $this->config['compatibility'];
			$fuel_version = $this->fuel->version();

			// if the current version of FUEL is greater then or equal to the compatibility version, the we are good to go
			if (version_compare($compatibility, $fuel_version, '>='))
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

	// --------------------------------------------------------------------
	
	/**
	 * CLI prompts the user for a response and then returns the response
	 *
	 * @access	public
	 * @param	int Length of input value to read
	 * @return	string
	 */	
	public function cli_prompt($msg, $length = 4096)
	{
		echo $msg;

		if (!isset($GLOBALS['StdinPointer'])) 
		{ 
			$GLOBALS['StdinPointer'] = fopen("php://stdin","r"); 
		} 
		$line = fgets($GLOBALS['StdinPointer'], $length); 
		return trim($line); 
	}


	// --------------------------------------------------------------------
	
	/**
	 * CLI silent prompt (for passwords) the user for a response and then returns the response
	 * http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
	 * http://stackoverflow.com/questions/297850/is-it-really-not-possible-to-write-a-php-cli-password-prompt-that-hides-the-pass
	 * And from Laravel CLI
	 *
	 * @access	public
	 * @param	int Length of input value to read
	 * @return	string
	 */	
	public function cli_silent_prompt($prompt = "Enter Password:", $length = 4096)
	{

		if (defined('PHP_WINDOWS_VERSION_BUILD')) 
		{
			$exe = FUEL_PATH.'libraries/resources/hiddeninput.exe';

			// handle code running from a phar
			if ('phar:' === substr(__FILE__, 0, 5)) {
			    $tmp_exe = sys_get_temp_dir().'/hiddeninput.exe';
			    copy($exe, $tmp_exe);
			    $exe = $tmp_exe;
			}
			echo $prompt;
			$value = rtrim(shell_exec($exe));

			if (isset($tmp_exe))
			{
			    unlink($tmp_exe);
			}

			return $value;
        }
		// if (preg_match('/^win/i', PHP_OS))
		// {
		// 	$vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
		// 	file_put_contents($vbscript, 'wscript.echo(InputBox("' . addslashes($prompt) . '", "", "password here"))');
		// 	$command = "cscript //nologo " . escapeshellarg($vbscript);
		// 	$value = rtrim(shell_exec($command));
		// 	unlink($vbscript);
		// }
		else
		{
			static $has_stty;
			if (is_null($has_stty))
			{
				exec('stty 2>&1', $output, $exitcode);
				$has_stty = $exitcode === 0;
			}

			if ($has_stty)
			{
				echo $prompt;
				$stty_mode = shell_exec('stty -g');
				shell_exec('stty -echo');
				$value = fgets(STDIN, $length);
	            shell_exec(sprintf('stty %s', $stty_mode));

	            if (false === $value)
	            {
	                return FALSE;
	            }

	            $value = trim($value);
			}
			else
			{
				$command = "/usr/bin/env bash -c 'echo OK'";
				if (rtrim(shell_exec($command)) !== 'OK')
				{
					trigger_error("Can't invoke bash");
					return;
				}
				$command = "/usr/bin/env bash -c 'read -s -p \"". addslashes($prompt) . "\" mypassword && echo \$mypassword'";
				$value = rtrim(shell_exec($command));
				echo "\n";
			}
		}
		return $value;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Copies a module config file to the fuel/application folder
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function copy_config()
	{
		$source_path = $this->install_path().'../config/'.$this->module.'.php';
		$dest_path = APPPATH.'config/'.$this->module.'.php';

		if (file_exists($source_path) AND !file_exists($dest_path))
		{
			$results = copy($source_path, $dest_path);

			if (!$results)
			{
				$this->_add_error(lang('error_could_not_create_file', $dest_path));
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Looks in a config file and changes it's value
	 *
	 * @access	public
	 * @param	string File name in main fuel/application/config folder
	 * @param	string String to find
	 * @param	string String to replace
	 * @return	string
	 */	
	public function change_config($file, $find, $replace)
	{
		if (empty($replace))
		{
			return;
		}
		
		$this->CI->load->helper('file');
		$filepath = APPPATH.'config/'.$file.'.php';
		$file = file_get_contents($filepath);

		$file = str_replace($find, $replace, $file);
		write_file($filepath, $file);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Simply returns a reference to the loaded CLI class
	 *
	 * @access	public
	 * @return	object
	 */	
	public function &cli()
	{
		return $this->CI->cli;
	}


}

/* End of file Fuel_installer.php */
/* Location: ./modules/fuel/libraries/Fuel_installer.php */