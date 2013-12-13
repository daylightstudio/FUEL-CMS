<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Installer extends Fuel_base_controller {
	
	protected $module = '';

	public function __construct()
	{
		$validate = (php_sapi_name() == 'cli' OR defined('STDIN')) ? FALSE : TRUE;
		parent::__construct($validate);
		
		// must be in dev mode to install modules
		if (!is_dev_mode())
		{
			show_error(lang('error_not_in_dev_mode'));
		}
		
		// validate user has permission
		if ($validate)
		{
			$this->_validate_user('installer');
		}
		
	}
	
	public function install($module = NULL)
	{
		if (empty($module))
		{
			show_error(lang('error_missing_params'));
		}
		
		// load constants
		$constant = strtoupper($module).'_VERSION';
		if (!defined($constant))
		{
			$constants_file = MODULES_PATH.$module.'/config/'.$module.'_constants.php';	
			if (file_exists($constants_file))
			{
				require_once($constants_file);
			}
		}
		
		// need to load it the old fashioned way because it is not enabled by default
		$module_file = MODULES_PATH.$module.'/libraries/Fuel_'.$module.'.php';
		if (file_exists($module_file))
		{
			$init = array('name' => $module, 'folder' => $module);
			$this->load->module_library($module, 'fuel_'.$module, $init);
			$module_lib = 'fuel_'.$module;
			if (!$this->$module_lib->install())
			{
				echo $this->fuel->installer->last_error()."\n";
			}
			else
			{
				echo lang('module_install', $module);
			}
		}
		else
		{
			echo lang('module_install_error', $module);
		}
	}

	public function add_git_submodule($params = NULL)
	{

		if (empty($params))
		{
			show_error(lang('error_missing_params'));
		}

		$uri = trim($this->uri->uri_string(), '/');
		$uri = str_replace('-at-', '@', $uri);
		$segs = explode('/', trim($this->uri->uri_string(), '/'));
		$segs = array_slice($segs, 3);
		$module = array_pop($segs);
		$repo = implode('/', $segs);

		if (empty($module))
		{
			$module = $this->module;
		}
	
		$module_folder = MODULES_WEB_PATH.$module;
		$cmd = 'git submodule add '.$repo.' '.$module_folder;
		$output = shell_exec($cmd);

		if (!empty($output))
		{
			echo $output."\n";
			return;
		}

		return $output;
	}
	
	public function uninstall($module = NULL)
	{
		if (!$this->fuel->modules->exists($module))
		{
			echo lang('cannot_determine_module')."\n";
			return;
		}

		// uninstall
		if (!$this->fuel->$module->uninstall())
		{
			echo $this->fuel->installer->last_error();
		}
		else
		{
			$module_folder = MODULES_WEB_PATH.$module;
			echo lang('module_uninstall', $module, $module_folder);
		}

	}
	
}