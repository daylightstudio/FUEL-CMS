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
	
	function install($params = NULL)
	{
		if (empty($params))
		{
			show_error(lang('error_missing_params'));
		}

		// get the repo and module name from the arguments
		$segs = explode('/', trim($this->uri->uri_string(), '/'));
		$segs = array_slice($segs, 3);
		$module = array_pop($segs);
		$repo = implode('/', $segs);

		// add GIT submodule
//		$output = $this->_add_git_submodule($repo, $module);

		// install/migrate the database if any

		// if (!empty($output))
		// {
		// 	echo $output;
		// 	return;
		// }
		
		if (!$this->fuel->$module->install())
		{
			echo $this->fuel->installer->last_error()."\n";
		}
		else
		{
			echo "The module '".$module."' has successfully bee installed.\n";
		}
	}

	function _add_git_submodule($repo, $module = NULL)
	{
		if (empty($module))
		{
			$module = $this->module;
		}
	
		$module_folder = MODULES_WEB_PATH.$module;
		$cmd = 'git submodule add '.$repo.' '.$module_folder;
		$output = shell_exec($cmd);
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
			$msg = '';
			$msg .= "The module '".$module."' has been uninstalled in FUEL.\n\n";
			$msg .= "However, removing a module from GIT is a little more work that we haven't automated yet. However, the below steps should help.\n\n";
			$msg .= "1. Delete the relevant section from the .gitmodules file.\n";
			$msg .= "2. Delete the relevant section from .git/config.\n";
			$msg .= "3. Run git rm --cached ".$module_folder." (no trailing slash).\n";
			$msg .= "4. Commit and delete the now untracked submodule files.\n";
			echo $msg;
		}

	}
	
	function update()
	{
		
	}
	
	function delete()
	{
		
	}

	
}