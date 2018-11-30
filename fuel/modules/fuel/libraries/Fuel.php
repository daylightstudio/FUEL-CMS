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
 * FUEL master object
 *
 * The master FUEL object that other objects attach to
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel
 */

// --------------------------------------------------------------------

// include base library classes to extend
require_once('Fuel_base_library.php');
require_once('Fuel_advanced_module.php');
//require_once('Fuel_modules.php');

class Fuel extends Fuel_advanced_module {
	protected $name = 'FUEL'; // name of the advanced module... usually the same as the folder name
	protected $folder = 'fuel'; // name of the folder for the advanced module
	
	 // attached objects
	protected $_attached = array();
	
	 // objects to automatically attach
	protected $_auto_attach = array(
									'admin',
									'assets',
									'auth',
									'blocks',
									'cache',
									'categories',
									'installer',
									'language',
									'layouts',
									'logs',
									'modules',
									'navigation',
									'notification',
									'pages',
									'parser',
									'posts',
									'pagevars',
									'permissions',
									'redirects',
									'settings',
									'sitevars',
									'tags',
									'users',
									);

	// the singleton instance
	private static $_instance;
	
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		self::$_instance =& $this;
		$this->fuel =& self::$_instance; // for compatibility
		$this->initialize();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Static method that returns the instance of the FUEL object.
	 *
	 * This object is auto-loaded and so you will most likely use $this->fuel instead of this method
	 *
	 * @access	public
	 * @return	object	
	 */	
	public static function &get_instance()
	{
		return self::$_instance;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the FUEL object
	 *
	 * Accepts an associative array as input containing the FUEL config parameters to set
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */	
	public function initialize($config = array())
	{
		// load main fuel config
		$this->CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);

		if (!empty($config))
		{
			$this->set_config($config);
		}
		
		$this->_config = $this->CI->config->config['fuel'];
		
		// merge in any "attach" objects to include on the FUEL object
		$this->_auto_attach = array_merge($this->_auto_attach, $this->_config['attach']);

		// add package paths
		foreach($this->_config['modules_allowed'] as $module)
		{
			$this->CI->load->add_package_path(MODULES_PATH.$module);
		}
		// needed to add this here so views will load correctly
		$this->CI->load->add_package_path(APPPATH);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a configuration value for FUEL (overwrites Fuel_advanced_module)
	 *
	 * @access	public
	 * @param	mixed	Can be a string that references the configuration key or an array of values
	 * @param	mixed	The value of the key configuration item (only works if $item parameter is not an array) (optional)
	 * @param	string	The module to set the configuration item. Default is fuel. (optional)
	 * @return	void
	 */	
	public function set_config($item, $value = NULL, $module = 'fuel')
	{
		$fuel_config = $this->CI->config->item($module);
		if (is_array($item))
		{
			foreach($item as $key => $val)
			{
				$fuel_config[$key] = $val;
			}
		}
		else
		{
			$fuel_config[$item] = $value;
		}
		$this->_config[$item] = $value;
		$this->CI->config->set_item($module, $fuel_config);
	}

// --------------------------------------------------------------------
	
	/**
	 * Returns the FUEL version 
	 *
	 * @access	public
	 * @param	string	Value of what part of the version number to return. Options are "major", "minor", or "patch" (optional)
	 * @return	string
	 */	
	public function version($part = NULL)
	{
		$version = FUEL_VERSION;
		if (!empty($part))
		{
			$parts = explode('.', $version);
			switch($part)
			{
				case 'major':
					return $parts[0];
					break;
				case 'minor':
					if (isset($parts[1])) return $parts[1];
					break;
				case 'patch':
					if (isset($parts[2])) return $parts[2];
					break;
			}
			return '0';
		}
		return $version;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Installs FUEL via the command line
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function install()
	{
		$cli = $this->installer->cli();

		if (!$cli->is_cli()) return FALSE;

		$module = strtolower($this->name());
		$this->installer->config($module);

		// $intro = array(
		// 	"The FUEL CMS installer is an easy way to setup the CMS with common configurations. It will do the following:",
		// 	"1) Automatically generate an encryption key for the fuel/application/config/config.php.",
		//  "2) Set the session save path in fuel/application/config/config.php.",
		// 	"3) Enable the CMS admin by changing the 'admin_enabled' config value in fuel/application/config/MY_fuel.php.",
		// 	"4) Change the 'fuel_mode' config value in in fuel/application/config/MY_fuel.php to allow for pages to be created in the CMS.",
		// 	"5) Change the 'site_name' config value in the fuel/application/config/MY_fuel.php.",
		// 	"6) Setup your environments fuel/application/config/environments.php.",
		// 	"7) Will make the fuel/application/logs, fuel/application/cache and assets/images folders writable.",
		// 	"8) Update the fuel/application/config/database.php file with the inputted values.",
		// 	"9) Create a database and install the fuel_schema.sql file using your local MySQL connection.\n",
		// );
		
		$cli->write(lang('install_cli_intro'));

		// add the session_path key
		$session_path = $cli->prompt(lang('install_session_path'));
		if (!empty($session_path)) $this->installer->change_config('config', '$config[\'sess_save_path\'] = NULL;', '$config[\'sess_save_path\'] = \''.$session_path.'\';');	

		// add the encryption key
		$this->installer->change_config('config', '$config[\'encryption_key\'] = \'\';', '$config[\'encryption_key\'] = \''.md5(uniqid()).'\';');

		// change the admin to be enabled
		$this->installer->change_config('MY_fuel', '$config[\'admin_enabled\'] = FALSE;', '$config[\'admin_enabled\'] = TRUE;');

		// change the fuel_model to "auto"
		$this->installer->change_config('MY_fuel', '$config[\'fuel_mode\'] = \'views\';', '$config[\'fuel_mode\'] = \'auto\';');

		// change the site_name config value
		$site_name = $cli->prompt(lang('install_site_name'));
		$this->installer->change_config('MY_fuel', '$config[\'site_name\'] = \'My Website\';', '$config[\'site_name\'] = \''.$site_name.'\';');

		// setup environments
		$staging_environment = $cli->prompt(lang('install_environments_testing'));
		$prod_environment = $cli->prompt(lang('install_environments_production'));
		$environment_search = "'development' => array('localhost*', '192.*', '*.dev'),\n\t\t\t\t);";
		$environment_replace = "'development' => array('localhost*', '192.*', '*.dev'),";
		if (!empty($staging_environment)) $environment_replace .= "\n\t\t\t\t'testing' => array('" . implode("', '", preg_split('#\s+#', str_replace(',', '', $staging_environment))) . "'),";
		if (!empty($prod_environment)) $environment_replace .= "\n\t\t\t\t'production' => array('" . implode("', '", preg_split('#\s+#', str_replace(',', '', $prod_environment))) . "'),";
		$environment_replace .= "\n\t\t\t\t);";
		$this->installer->change_config('environments', $environment_search, $environment_replace);

		// change file permissions for writable folders
		$perms = $cli->prompt(lang('install_permissions'));
		if (!empty($perms))
		{
			$this->CI->load->helper('directory');

			$writable_folders = array(
				APPPATH.'cache/',
				APPPATH.'logs/',
				WEB_ROOT.'assets/images/',
				);
			$perms = intval($perms, 8);
			foreach($writable_folders as $folder)
			{
				@chmodr($folder, $perms);
				if (!is_writable($folder))
				{
					$this->_add_error(lang('error_folder_not_writable', $folder));
				}
			}
		}
		
		
		// ask database questions
		$db_name = $cli->prompt(lang('install_db_name'));
		$db_user = $cli->prompt(lang('install_db_user'));
		$db_pwd = $cli->secret(lang('install_db_pwd'));

		// change database config
		if (!empty($db_name) AND !empty($db_user) AND !empty($db_pwd))
		{
			$this->installer->change_config('database', '$db[\'default\'][\'username\'] = \'\';', '$db[\'default\'][\'username\'] = \''.$db_user.'\';');
			$this->installer->change_config('database', '$db[\'default\'][\'password\'] = \'\';', '$db[\'default\'][\'password\'] = \''.$db_pwd.'\';');

			// now check the database connection and see if the database exists yet or not... if not create it
			$this->CI->load->dbutil();
			if (!$this->CI->dbutil->database_exists($db_name))
			{
				$this->CI->load->dbforge();
				$this->CI->dbforge->create_database($db_name);
				$this->installer->change_config('database', '$db[\'default\'][\'database\'] = \'\';', '$db[\'default\'][\'database\'] = \''.$db_name.'\';');
				$this->installer->install_sql();
			}
			else
			{	
				// must do this afterward to prevent errors
				$this->installer->change_config('database', '$db[\'default\'][\'database\'] = \'\';', '$db[\'default\'][\'database\'] = \''.$db_name.'\';');	
			}
		}

		$cli->write("\n...\n");
		if ($this->has_errors())
		{
			$cli->write(lang('install_success_with_errors', implode("\n", $this->fuel->errors())));
		}
		else
		{
			$cli->write(lang('install_success'));
		}
		$cli->new_line();
		$cli->write(lang('install_further_info'));
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Updates FUEL to v1.4
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function update()
	{
		$cli = $this->installer->cli();

		if (!$cli->is_cli()) return FALSE;

		if (!($this->version('major') == '1' AND $this->version('minor') == '4'))
		{
			$cli->write('You must be using version 1.4x to run the updater.');
			exit(); 
		}
		
		$this->CI->load->helper('directory');
		$this->CI->load->helper('file');

		// $intro = array(
		// 	"FUEL CMS 1.4x is built on CodeIgniter 3. If you are upgrading from 1.3x or earlier, this updater will help fix some of the common issues when upgrading including:",
		// 	"1) Upper-cased first letter for models, libraries and controller file names.",
		// 	"2) Will upper case common references to Base_module_model.php.",
		// 	"3) Update common method signatures in models and libraries like form_fields and _common_query and initialize to match their parents.",
		// 	"Run this ONLY if you are using GIT in case you need to roll back!"
		// 	"Do you wish to continue (y/n)",
		// );
		$continue = $cli->prompt(lang('update_cli_intro'));

		if (strtolower($continue) == 'y' || strtolower($continue) == 'yes')
		{
			$modules_path = MODULES_PATH;
			$module_paths = list_directories(MODULES_PATH, array(), TRUE, FALSE, FALSE);
			$module_paths[] = APPPATH;
			
			foreach($module_paths as $module_path)
			{
				$module = pathinfo($module_path, PATHINFO_BASENAME);
				if ($module == 'fuel') continue;

				// ucfirst file names
				$folders = array('controllers', 'libraries', 'models');

				foreach($folders as $folder)
				{

					// change controller file names to be ucfirst
					$path = $module_path.'/'.$folder;
					// $files = get_filenames($path, TRUE, FALSE);
					$files = directory_to_array($path, FALSE);
					$cmd1 = "git mv -f ";
					$cmd2 = "mv "; // in case GIT isn't being used

					foreach($files as $file)
					{
						$file = DIRECTORY_SEPARATOR.ltrim($file, '/');
						if (pathinfo($file, PATHINFO_EXTENSION) == 'php')
						{
							if ($folder == 'controllers' OR $folder == 'models')
							{
								// first ucfirst file names
								$newFile = pathinfo($file, PATHINFO_DIRNAME) .'/'. ucfirst(pathinfo($file, PATHINFO_BASENAME));
								if ($file != $newFile)
								{
									$mv = $file . " " .$newFile;
									exec($cmd1 . $mv);
									exec($cmd2 . $mv);
								}
							}

							//  now search and replace common issues
							if ($folder == 'controllers')
							{
								// public function initialize($params = array())
								$find = array(
									'/controllers/module.php'
									);
								$replace = array(
									'/controllers/Module.php'
									);

								$this->_update_search_replace($file, $find, $replace);
							}
							elseif ($folder == 'models')
							{
								$find = array(
									'models/base_module_model.php', 
									'function form_fields()',
									'function form_fields($values = array())',
									'function _common_query()',
									);
								$replace = array(
									'models/Base_module_model.php', 
									'function form_fields($values = array(), $related = array())',
									'function form_fields($values = array(), $related = array())',
									'function _common_query($display_unpublished_if_logged_in = NULL)',
									);
								$this->_update_search_replace($file, $find, $replace);
							}
							elseif ($folder == 'libraries')
							{
								$find = array(
									'function initialize($params)', 
									);
								$replace = array(
									'function initialize($params = array())', 
									);
								$this->_update_search_replace($file, $find, $replace);
							}
						}
					}
				}
				

				$models_path = $module_path.'/models';
				$model_files = get_filenames($models_path);

			}
		}
		$cli->write(lang('update_success')); 
		return TRUE;
	}

	protected function _update_search_replace($file, $find, $replace)
	{
		$f = read_file($file);

		foreach($find as $i => $_f)
		{
			if (strpos($f, $_f) !== FALSE)
			{
				$f = str_replace($find[$i], $replace[$i], $f);
				echo "REPLACED: ".$find[$i]." => ".$replace[$i]."\n";
			}
		}
		
		write_file($file, $f);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will attach and return FUEL library objects
	 *
	 * @access	public
	 * @param	string	The object
	 * @return	object
	 */	
	public function __get($var)
	{
		if (!isset($this->_attached[$var]))
		{
			if (in_array($var, $this->_auto_attach))
			{
				$this->attach($var);
			}
			else if ($this->modules->allowed($var))
			{
				$init = array('name' => $var, 'folder' => $var);

				$fuel_class = 'Fuel_'.$var;
				
				if (file_exists(MODULES_PATH.$var.'/libraries/'.$fuel_class.'.php'))
				{
					$lib_class = strtolower($fuel_class);
					if (!isset($this->CI->$lib_class))
					{
						$this->CI->load->module_library($var, $lib_class, $init);
					}
					return $this->CI->$lib_class;
				}
				else
				{
					$module = new Fuel_advanced_module($init);
					$this->CI->$var = $module;
					return $module;
					
				}
			}
			else
			{
				// To resolve issue when called outside of admin
				$this->load_language('fuel');
				throw new Exception(lang('error_class_property_does_not_exist', $var));
			}
		}
		return $this->_attached[$var];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will call any methods on an attached object that are "get"
	 *
	 * @access	public
	 * @param	string	The object
	 * @param	string	An array of arguments
	 * @return	object
	 */	
	public function __call($name, $args)
	{
		$obj = $this->$name;
		if (method_exists($obj, 'get'))
		{
			return call_user_func_array(array($obj, 'get'), $args);
		}
		else
		{
			return $obj;
		}
	}
	
}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */