<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Generate extends Fuel_base_controller {
	
	public $created = array();
	public $errors = array();
	public $modified = array();
	
	function __construct()
	{
		$validate = (php_sapi_name() == 'cli' or defined('STDIN')) ? FALSE : TRUE;
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
	function advanced($name = NULL)
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

		foreach($config as $val)
		{
			$substituted = str_replace('{module}', $name, $val);
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
						$errors[] = lang('error_could_not_create_file', $dir)."\n";
					}
					write_file($file, $content);
					$this->created[] = $file;
				}

			}
		}
		
		// add to modules_allowed to MY_fuel and to the database
		if (!in_array($name, $this->fuel->config('modules_allowed')))
		{
			// add to advanced module config
			$my_fuel_path = APPPATH.'config/MY_fuel.php';

			$content = file_get_contents($my_fuel_path);
			$append = "\n\$config['modules_allowed'][] = '".$name."';";

			// create variables for parsed files
			$content = preg_replace('#(\$config\[([\'|"])modules_allowed\\2\].+;)#Ums', '$1'.$append, $content);
			write_file($my_fuel_path, $content);
			$this->modified[] = $my_fuel_path;
			
			// save to database
			$modules_allowed = $this->fuel->config('modules_allowed');
			$modules_allowed[] = $name;
			$this->fuel->settings->save(FUEL_FOLDER, 'modules_allowed', $modules_allowed);
		}

		$vars['created'] = $this->created;
		$vars['errors'] = $this->errors;

		// create a generic permission for the advanced module
		$this->fuel->permissions->create($name);
		
		$this->_load_results($vars);
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
	function simple($name = NULL, $module = '')
	{
		if (empty($name))
		{
			show_error(lang('error_missing_params'));
		}
		
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
		$my_fuel_modules_path = APPPATH.'config/MY_fuel_modules.php';
		if (file_exists($my_fuel_modules_path))
		{
			@include(APPPATH.'config/MY_fuel_modules.php');
			if (!isset($config['modules'][$name]))
			{
				// create variables for parsed files
				$vars = $this->_common_vars($name);
				$file = current($config);
				$content = "\n".$this->_parse_template($file, $vars, 'simple');

				write_file($my_fuel_modules_path, $content, FOPEN_WRITE_CREATE);
				$this->modified[] = $file;
			}
		}

		// now create permissions
		$this->fuel->permissions->create_simple_module_permissions($name);
		$vars['created'] = $this->created;
		$vars['errors'] = $this->errors;

		$this->_load_results($vars);
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
	function model($model, $module = '', $display_results = TRUE)
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
		
		$config = (array)$fuel_config['model'];

		// create variables for parsed files
		$vars = $this->_common_vars($model);
		
		// create model file
		$basepath = (!empty($module)) ? MODULES_PATH.$module.'/' : APPPATH;
		foreach($config as $val)
		{
			$substituted = str_replace('{model}', $model, $val);
			$ext = pathinfo($substituted, PATHINFO_EXTENSION);
			$file = $basepath .'models/'. $substituted;

			// create file if it doesn't exits'
			if (!file_exists($file))
			{
				$content = $this->_parse_template($val, $vars, 'model');
				
				if (!$content)
				{
					$this->errors[] = lang('error_could_not_create_file', $dir)."\n";
				}
				
				// if SQL file extension, then we try and load the SQL
				if (preg_match('#\.sql$#', $file))
				{
					$this->_load_sql($content);
				}
				else
				{
					write_file($file, $content);
					$this->created[] = $file;
				}
			}
		}
		
		if ($display_results)
		{
			$vars['created'] = $this->created;
			$vars['errors'] = $this->errors;
			$vars['modified'] = $this->modified;

			$this->_load_results($vars);
		}
	}
	
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
			$this->fuel->admin->render('_generate/results', $vars, Fuel_admin::DISPLAY_NO_ACTION);
		}
	}
	
	protected function _parse_template($file, $vars, $type = 'advanced')
	{
		
		// first check APPPATH for template files
		$template_path = rtrim(APPPATH.'views/_generate/'.$type.'/'.$file, '/');
		if (!file_exists($template_path))
		{
			$template_path = rtrim(FUEL_PATH.'views/_generate/'.$type.'/'.$file, '/');
		}
		
		// grab the contents of the templates
		if (!file_exists($template_path))
		{
			return FALSE;
		}
		
		$contents = file_get_contents($template_path);
		
		// parse
		$contents = $this->parser->parse_simple($contents, $vars);
		return $contents;
	}
	
	protected function _common_vars($name)
	{
		$vars = array();
		$vars['module'] = $name;
		$vars['table'] = $name;
		$vars['module_name'] = ucwords(humanize($name));
		$vars['model_name'] = ucfirst($name);
		$vars['model_record'] = ucfirst(trim($name, 's'));
		
		if ($vars['model_name'] == $vars['model_record'])
		{
			$vars['model_record'] = $vars['model_record'].'_item';
		}
		return $vars;
	}
	
	protected function _load_sql($sql)
	{
		$sql = preg_replace('#^/\*(.+)\*/$#U', '', $sql);
		$sql = preg_replace('/^#(.+)$/U', '', $sql);
		
		// load database config
		include(APPPATH.'config/database.php');
		$this->load->database();
	
		// select the database
		$db = $db[$active_group]['database'];
		
		$use_sql = 'USE '.$db;
		
		$this->db->query($use_sql);
		$sql_arr = explode(";\n", $sql);
		foreach($sql_arr as $s)
		{
			$s = trim($s);
			if (!empty($s))
			{
				$this->db->query($s);
			}
		}
	}	
	
	protected function _create_permissions($module)
	{
		$this->fuel->permissions();
	}
	
}