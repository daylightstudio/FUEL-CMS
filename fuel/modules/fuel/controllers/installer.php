<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Installer extends Fuel_base_controller {
	
	protected $git_path = '/usr/bin/git';
	protected $module = '';

	function __construct()
	{
		$validate = (php_sapi_name() == 'cli' or defined('STDIN')) ? FALSE : TRUE;
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
	
	// function index()
	// {
	// 	$vars['modules'] = $this->fuel->modules->advanced();
	// 	$crumbs = array(lang('section_my_modules'));
	// 	$this->fuel->admin->set_titlebar($crumbs);
		
	// 	$this->fuel->admin->render('manage/my_modules', $vars);
	// }
	
	function install($module = NULL)
	{
		if (empty($module))
		{
			show_error(lang('error_missing_params'));
		}
	
		if (!$this->fuel->$module->install())
		{
			echo $this->fuel->installer->last_error()."\n";
		}
		else
		{
			echo lang('module_install', $module);
		}
	}

	function add_git_submodule($params = NULL)
	{

		if (empty($params))
		{
			show_error(lang('error_missing_params'));
		}

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
		//$output = $this->run_command($cmd);

		if (!empty($output))
		{
			echo $output."\n";;
			return;
		}

		return $output;
	}
	
	function uninstall($module = NULL)
	{
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
	
	function update()
	{
		
	}
	
	function delete()
	{
		
	}

	
}