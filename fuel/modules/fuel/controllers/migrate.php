<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Migrate extends Fuel_base_controller {
	
	protected $module = '';

	public function __construct()
	{
		// don't validate initially because we need to handle it a little different since we can use web hooks
		parent::__construct(FALSE);

		$remote_ips = $this->fuel->config('webhook_remote_ip');
		$is_web_hook = ($this->fuel->auth->check_valid_ip($remote_ips));

		// check if it is CLI or a web hook otherwise we need to validate
		$validate = (php_sapi_name() == 'cli' OR defined('STDIN') OR $is_web_hook) ? FALSE : TRUE;

		// validate user has permission
		if ($validate)
		{
			$this->fuel->admin->check_login();
			$this->_validate_user('migrate');
		}

	}


	public function latest($module = NULL)
	{
		$this->_init_migrate($module);

		$version = $this->migration->latest();
		if ( ! $version)
		{
			$this->_show_error();
		}
		$this->_success($version);
	}

	public function current($module = NULL)
	{
		$this->_init_migrate($module);

		$version = $this->migration->current();
		if ( ! $version)
		{
			$this->_show_error();
		}
		$this->_success($version);
	}

	public function version($version = 1, $module = NULL)
	{
		$this->_init_migrate($module);

		// must be in dev mode change the version to something uther then the latest or current
		if (!is_dev_mode())
		{
			$this->_show_error(lang('error_not_in_dev_mode'));
		}

		$version = $this->migration->version($version);
		if ( ! $version)
		{
			$this->_show_error();
		}
		$this->_success($version);
	}

	protected function _init_migrate($module)
	{
		$config['migration_enabled'] = TRUE;
		$config['module'] = $module;
		$this->load->library('migration', $config);
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