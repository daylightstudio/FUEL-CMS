<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Generate extends Fuel_base_controller {
	
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
	
	function advanced($module = NULL)
	{
		if (empty($module))
		{
			show_error(lang('error_missing_params'));
		}
		$fuel_config = $this->fuel->config('generate');
		$config = $fuel_config['advanced'];
		$module_path = MODULES_PATH.$module.'/';
		
		$created = array();
		$errors = array();
		if (!file_exists($module_path))
		{
			if (!mkdir($module_path, DIR_READ_MODE, TRUE))
			{
				$errors[] = lang('error_could_not_create_folder', $module_path)."\n";
			}
			else
			{
				$created[] = $module_path;
			}
		}
		
		foreach($config as $val)
		{
			$substituted = str_replace('{module}', $module, $val);
			$ext = pathinfo($substituted, PATHINFO_EXTENSION);
			$file = $module_path . $substituted;
			
			if (empty($ext))
			{
				if (!file_exists($file))
				{
					if (!mkdir($file, DIR_READ_MODE, TRUE))
					{
						$errors[] = lang('error_could_not_create_folder', $file)."\n";
					}
					else
					{
						$created[] = $file;
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
						$errors[] = lang('error_could_not_create_folder', $dir)."\n";
					}
				}
				
				// create file if it doesn't exits'
				if (!file_exists($file))
				{
					
					// create variables for parsed files
					$vars = array();
					$vars['module'] = $module;
					$vars['module_name'] = ucwords(humanize($module));
					$vars['model_name'] = ucfirst($module);
					

					$content = $this->_parse_template($val, $vars, 'advanced');
					
					if (!$content)
					{
						$errors[] = lang('error_could_not_create_file', $dir)."\n";
					}
					write_file($file, $content);
					$created[] = $file;
				}
				
			}
		}
		
		$vars['created'] = $created;
		$vars['errors'] = $errors;
		
		$this->_load_results($vars);
	}	
	
	function simple($module = NULL)
	{
		if (empty($module))
		{
			show_error(lang('error_missing_params'));
		}
		$fuel_config = $this->fuel->config('generate');
		$config = $fuel_config['simple'];

		$created = array();
		$errors = array();

		foreach($config as $val)
		{
			$substituted = str_replace('{module}', $module, $val);
			$ext = pathinfo($substituted, PATHINFO_EXTENSION);
			$file = APPPATH . $substituted;

			// create file if it doesn't exits'
			if (!file_exists($file))
			{
				// create variables for parsed files
				$vars = array();
				$vars['module'] = $module;
				$vars['module_name'] = ucwords(humanize($module));
				$vars['model_name'] = ucfirst($module);

				$content = $this->_parse_template($val, $vars, 'simple');
				
				if (!$content)
				{
					$errors[] = lang('error_could_not_create_file', $dir)."\n";
				}
				write_file($file, $content);
				$created[] = $file;
			}
		}
		
		
		// add to MY_fuel_modules if it doesn't exist'
		$my_fuel_modules_path = APPPATH.'config/MY_fuel_modules.php';
		@include(APPPATH.'config/MY_fuel_modules.php');
		
		if (!isset($config['modules'][$module]))
		{
			$str = "\n\n\$config['modules']['".$module."'] = array(
	'preview_path' => '',
);";
			write_file($my_fuel_modules_path, $str, FOPEN_WRITE_CREATE);
		}
		
		
		$vars['created'] = $created;
		$vars['errors'] = $errors;
		
		$this->_load_results($vars);
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
	
}