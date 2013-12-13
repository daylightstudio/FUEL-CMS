<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Generate extends Fuel_base_controller {
	
	public $created = array();
	public $errors = array();
	public $modified = array();
	
	public function __construct()
	{
		$validate = (php_sapi_name() == 'cli' OR defined('STDIN')) ? FALSE : TRUE;
		parent::__construct($validate);
		
		// must be in dev mode to generate
		if (!is_dev_mode())
		{
			show_error(lang('error_not_in_dev_mode'));
		}
		
		// validate user has permission
		if ($validate)
		{
			$this->_validate_user('generate');
		}
		
		$this->load->helper('file');
		$this->load->library('parser');
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generates the folders and files for an advanced module
	 *
	 * @access	public
	 * @param	string	Module name
	 * @return	void
	 */	
	public function advanced($name = NULL)
	{
		if (empty($name))
		{
			show_error(lang('error_missing_params'));
		}
		$fuel_config = $this->fuel->config('generate');
		
		$names = $this->_get_module_names($name);
		foreach($names as $name)
		{
			$this->_advanced($name);
		}

		// create a generic permission for the advanced module
		$this->fuel->permissions->create($name);
		
		$vars['created'] = $this->created;
		$vars['errors'] = $this->errors;
		$vars['modified'] = $this->modified;

		$this->_load_results($vars);
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method to that generates the folders and files for an advanced module
	 *
	 * @access	protected
	 * @param	string	Module name
	 * @return	void
	 */	
	protected function _advanced($name = NULL)
	{
		if (empty($name))
		{
			show_error(lang('error_missing_params'));
		}
		$fuel_config = $this->fuel->config('generate');
		
		
		// check that the configuration to map to files to generate exists
		if (!isset($fuel_config['advanced']))
		{
			show_error(lang('error_missing_generation_files', 'advanced'));
		}
		
		$config = (array)$fuel_config['advanced'];
		$name_path = MODULES_PATH.$name.'/';

		if (!file_exists($name_path))
		{
			if (!mkdir($name_path, DIR_READ_MODE, TRUE))
			{
				$this->errors[] = lang('error_could_not_create_folder', $name_path)."\n";
			}
			else
			{
				$this->created[] = $name_path;
			}
		}

		// create variables for parsed files
		$vars = $this->_common_vars($name);
		$find_arr = array_keys($vars);
		$find = array();
		foreach($find_arr as $f)
		{
			$find[] = '{'.$f.'}';
		}
		$replace = array_values($vars);
		foreach($config as $val)
		{
			$substituted = str_replace($find, $replace, $val);
			$ext = pathinfo($substituted, PATHINFO_EXTENSION);
			$file = $name_path . $substituted;

			if (empty($ext))
			{
				if (!file_exists($file))
				{
					if (!mkdir($file, DIR_READ_MODE, TRUE))
					{
						$this->errors[] = lang('error_could_not_create_folder', $file)."\n";
					}
					else
					{
						$this->created[] = $file;
					}
				}
			}
			else
			{
				$dir =  dirname($file);

				// create parent directory if it doesn't exist already'
				if (!file_exists($dir))
				{
					if (!mkdir($dir, DIR_READ_MODE, TRUE))
					{
						$this->errors[] = lang('error_could_not_create_folder', $dir)."\n";
					}
				}

				// create file if it doesn't exits'
				if (!file_exists($file))
				{
					$content = $this->_parse_template($val, $vars, 'advanced');

					if (!$content)
					{
						$this->errors[] = lang('error_could_not_create_file', $file)."\n";
					}
					else
					{
						if (!write_file($file, $content))
						{
							$this->errors[] = lang('error_could_not_create_file', $file)."\n";
						}
						else
						{
							$this->created[] = $file;
						}
					}
				}
			}
		}
		
		// add to modules_allowed to MY_fuel and to the database
		if (!in_array($name, $this->fuel->config('modules_allowed')))
		{
			// add to advanced module config
			$my_fuel_path = APPPATH.'config/MY_fuel.php';

			$modules_allowed = $this->fuel->config('modules_allowed');
			$modules_allowed[] = $name;

			$content = file_get_contents($my_fuel_path);
			$allowed_str = "\$config['modules_allowed'] = array(\n";
			foreach($modules_allowed as $mod)
			{
				$allowed_str .= "\t\t'".$mod."',\n";
			}
			$allowed_str .= ");";

			// create variables for parsed files
			$content = preg_replace('#(\$config\[([\'|"])modules_allowed\\2\].+;)#Ums', $allowed_str, $content, 1);

			if (!write_file($my_fuel_path, $content))
			{
				$this->errors[] = lang('error_could_not_create_file', $my_fuel_path)."\n";
			}
			$this->modified[] = $my_fuel_path;
			
			// save to database if the settings is there
			
			$module_obj = $this->fuel->modules->get($name);
			if (!empty($module_obj) AND $this->fuel->modules->is_advanced($module_obj))
			{
				$settings = $module_obj->settings_fields();
				if (isset($settings['modules_allowed']))
				{
					$this->fuel->settings->save(FUEL_FOLDER, 'modules_allowed', $modules_allowed);
				}
			}
		}

		// create a generic permission for the advanced module
		$this->fuel->permissions->create($name);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Generates the table, model, permissions and adds to MY_fuel_modules
	 *
	 * @access	public
	 * @param	string	Model name
	 * @param	string	Module name (optional)
	 * @return	void
	 */	
	public function simple($name = NULL, $module = '')
	{
		if (empty($name))
		{
			show_error(lang('error_missing_params'));
		}
		
		$names = $this->_get_module_names($name);
		foreach($names as $name)
		{
			$this->_simple($name, $module);
		}
		
		$vars['created'] = $this->created;
		$vars['errors'] = $this->errors;
		$vars['modified'] = $this->modified;

		$this->_load_results($vars);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that generates the table, model, permissions and adds to MY_fuel_modules
	 *
	 * @access	public
	 * @param	string	Model name
	 * @param	string	Module name (optional)
	 * @return	void
	 */	
	protected function _simple($name = NULL, $module = '')
	{
		// create the model
		$this->model($name, $module, FALSE);

		$fuel_config = $this->fuel->config('generate');

		// check that the configuration to map to files to generate exists
		if (!isset($fuel_config['simple']))
		{
			show_error(lang('error_missing_generation_files', 'simple'));
		}
		
		$config = (array)$fuel_config['simple'];

		$basepath = (!empty($module)) ? MODULES_PATH.$module.'/' : APPPATH;
		
		
		// add to MY_fuel_modules if it doesn't exist'
		$module_path = (!empty($module)) ? $basepath.'config/'.$module.'_fuel_modules.php' : $basepath.'config/MY_fuel_modules.php';
		
		if (file_exists($module_path))
		{
			@include($module_path);
		}
		else
		{
			$content = "<?php \n";
			if (!write_file($module_path, $content, FOPEN_WRITE_CREATE))
			{
				$this->errors[] = lang('error_could_not_create_file', $module_path)."\n";
			}
			else
			{
				$this->created[] = $module_path;
			}
		}
		
		if (!isset($config['modules'][$name]))
		{
			// create variables for parsed files
			$vars = $this->_common_vars($name);
			$vars['advanced_module'] = $module;
			$file = current($config);
			$content = "\n".$this->_parse_template($file, $vars, 'simple');

			if (!write_file($module_path, $content, FOPEN_WRITE_CREATE))
			{
				$this->errors[] = lang('error_could_not_create_file', $module_path)."\n";
			}
			else
			{
				$this->modified[] = $file;
			}
		}

		// now create permissions
		$this->fuel->permissions->create_simple_module_permissions($name);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generates the table and model
	 *
	 * @access	public
	 * @param	string	Model name
	 * @param	string	Module name (optional)
	 * @return	void
	 */	
	public function model($model, $module = '', $display_results = TRUE)
	{
		if (empty($model))
		{
			show_error(lang('error_missing_params'));
		}
		$fuel_config = $this->fuel->config('generate');

		// check that the configuration to map to files to generate exists
		if (!isset($fuel_config['model']))
		{
			show_error(lang('error_missing_generation_files', 'model'));
		}
		
		$names = $this->_get_module_names($model);
		foreach($names as $name)
		{
			$this->_model($name, $module);
		}
		
		if ($display_results)
		{
			$vars['created'] = $this->created;
			$vars['errors'] = $this->errors;
			$vars['modified'] = $this->modified;

			$this->_load_results($vars);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that generates the table and model
	 *
	 * @access	public
	 * @param	string	Model name
	 * @param	string	Module name (optional)
	 * @return	void
	 */	
	public function _model($model, $module = '')
	{
		$fuel_config = $this->fuel->config('generate');
		
		$config = (array)$fuel_config['model'];

		// create variables for parsed files
		$vars = $this->_common_vars($model);
		
		$find_arr = array_keys($vars);
		$find = array();
		foreach($find_arr as $f)
		{
			$find[] = '{'.$f.'}';
		}
		
		$replace = array_values($vars);
		// create model file
		$basepath = (!empty($module)) ? MODULES_PATH.$module.'/' : APPPATH;
		foreach($config as $val)
		{
			$substituted = str_replace($find, $replace, $val);
			$ext = pathinfo($substituted, PATHINFO_EXTENSION);
			$file = $basepath .'models/'. $substituted;

			// create file if it doesn't exits'
			if (!file_exists($file))
			{
				$file_arr = array($substituted, $val);
				$content = $this->_parse_template($file_arr, $vars, 'model');

				if (!$content)
				{
					$this->errors[] = lang('error_could_not_create_file', $dir)."\n";
				}
				
				// if SQL file extension, then we try and load the SQL
				if (preg_match('#\.sql$#', $file))
				{
					// load database if it isn't already
					if (!isset($this->db))
					{
						$this->load->database();
					}
					$this->db->load_sql($content, FALSE);
				}
				else
				{
					if (!write_file($file, $content))
					{
						$this->errors[] = lang('error_could_not_create_file', $file)."\n";
					}
					$this->created[] = $file;
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that returns the module names
	 *
	 * @access	protected
	 * @param	string	Model names which can be plural separated by a colon ":"
	 * @return	array
	 */	
	protected function _get_module_names($names)
	{
		return explode(':', $names);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that displays the output results
	 *
	 * @access	protected
	 * @param	array	Variables used to generate the results
	 * @return	string
	 */	
	protected function _load_results($vars)
	{
		if (php_sapi_name() == 'cli' or defined('STDIN'))
		{
			$this->load->module_view(FUEL_FOLDER, '_generate/results_cli', $vars);
		}
		else
		{
			$crumbs = array(lang('module_generate'));
			$this->fuel->admin->set_titlebar($crumbs);
			$this->fuel->admin->render('_generate/results', $vFuears, Fuel_admin::DISPLAY_NO_ACTION);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that generates the table and model
	 *
	 * @access	protected
	 * @param	string	Model names which can be plural separated by a colon ":"
	 * @return	array
	 */	
	protected function _find_template($file, $vars, $type = 'advanced')
	{
		$fuel_config = $this->fuel->config('generate');
		
		$search = (array)$fuel_config['search'];
		
		$file = (array)$file;
		
		$found = FALSE;
		foreach($file as $f)
		{
			foreach($search as $module)
			{
				$file_path = ($module == 'app' OR $module == 'application') ? APPPATH : MODULES_PATH.$module.'/';
				// first check APPPATH for template files
				$template_path = rtrim($file_path.'views/_generate/'.$type.'/'.$f, '/');
				if (file_exists($template_path))
				{
					$found = TRUE;
					break 2;
				}
			}
		}
		return $template_path;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that parses a template file
	 *
	 * @access	protected
	 * @param	string	The file to parse
	 * @param	array	Variables to pass to the parsed template
	 * @param	string	The type of generation of model, simple, advanced (optional)
	 * @return	string
	 */	
	protected function _parse_template($file, $vars, $type = 'advanced')
	{
		$template_path = $this->_find_template($file, $vars, $type);
		if (file_exists($template_path))
		{
			$contents = file_get_contents($template_path);
		
			// parse
			$contents = $this->parser->parse_simple($contents, $vars);
			return $contents;
		}
		$this->errors[] = lang('error_could_not_create_file', $file)."\n";
		return '';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Protected helper method that returns common variables that get used during the parsing of a template
	 *
	 * @access	protected
	 * @param	string	The module name
	 * @return	array
	 */	
	protected function _common_vars($name)
	{
		$vars = array();
		$vars['module'] = $name;
		$vars['model'] = $name;
		$vars['table'] = $name;
		$vars['module_name'] = ucwords(humanize($name));
		$vars['model_name'] = ucfirst($name);
		$vars['model_record'] = ucfirst(preg_replace('#ie$#', 'y', rtrim($name, 's')));
		$vars['ModuleName'] = ucfirst(camelize($name));
		$vars['MODULE_NAME'] = strtoupper($name);
		if ($vars['model_name'] == $vars['model_record'])
		{
			$vars['model_record'] = $vars['model_record'].'_item';
		}
		return $vars;
	}
	
}