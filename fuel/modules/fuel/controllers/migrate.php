<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Migrate extends Fuel_base_controller {
	
	protected $module = '';

	function __construct()
	{
		$validate = (php_sapi_name() == 'cli' or defined('STDIN')) ? FALSE : TRUE;
		parent::__construct($validate);
		
		// validate user has permission
		if ($validate)
		{
			$this->_validate_user('migrate');
		}

		$this->load->library('migration');
	}


	function latest()
	{
		$version = $this->migration->latest();
		if ( ! $version)
		{
			$this->_show_error();
		}
		$this->_success($version);
	}

	function current()
	{
		$version = $this->migration->current();
		if ( ! $version)
		{
			$this->_show_error();
		}
		$this->_success($version);
	}

	function version($version = 1)
	{
		// must be in dev mode change the version to something uther then the latest or current
		if (!is_dev_mode())
		{
			$this->_show_error(lang('error_not_in_dev_mode'));
		}

		$version = $this->migration->version();
		if ( ! $version)
		{
			$this->_show_error();
		}
		$this->_success($version);
	}

	protected function _success($version)
	{
		if ($version === TRUE)
		{
			echo lang('migrate_nothing_todo', $version);
		}
		else
		{
			echo lang('migrate_success', $version);
		}
	}

	protected function _show_error($error = '')
	{
		if (empty($error))
		{
			$error = $this->migration->error_string();
		}
		echo $error."\n";
		exit();
	}
}