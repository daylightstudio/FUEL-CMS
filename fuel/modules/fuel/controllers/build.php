<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Build extends Fuel_base_controller {

	function __construct()
	{
		// don't validate yet... we check that you are a super admin later
		parent::__construct(FALSE);

		if (is_environment('production'))
		{
			exit('Cannot execute in production environment');
		}
	}
	
	function _remap($module)
	{
		$remote_ips = $this->fuel->config('webhook_romote_ip');
		$is_web_hook = ($this->fuel->auth->check_valid_ip($remote_ips));

		// check if it is CLI or a web hook otherwise we need to validate
		$validate = (php_sapi_name() == 'cli' OR defined('STDIN') OR $is_web_hook) ? FALSE : TRUE;

		// Only super admins can execute builds for now
		if ($validate AND !$this->fuel->auth->is_super_admin())
		{
			show_error(lang('error_no_access'));
		}

		// call before build hook
		$params = array('module' => $module);
		$GLOBALS['EXT']->_call_hook('before_build', $params);

		if ($module != 'index' AND $this->fuel->modules->exists($module) AND $this->fuel->modules->is_advanced($this->fuel->$module))
		{
			$results = $this->fuel->$module->build();

			if ($results === FALSE)
			{
				echo lang('error_no_build');
			}
		}
		else
		{
			// run default FUEL optimizations if no module is passed
			$this->optimize_js();
			$this->optimize_css();
		}

		// call after build hook
		$GLOBALS['EXT']->_call_hook('after_build', $params);

	}

	function optimize_js()
	{
		$js = array(
			'jquery/plugins/jquery-ui-1.8.17.custom.min',
			'jquery/plugins/jquery.easing',
			'jquery/plugins/jquery.bgiframe',
			'jquery/plugins/jquery.tooltip',
			'jquery/plugins/jquery.scrollTo-min',
			'jquery/plugins/jqModal',
			'jquery/plugins/jquery.checksave',
			'jquery/plugins/jquery.form',
			'jquery/plugins/jquery.treeview.min',
			'jquery/plugins/jquery.serialize',
			'jquery/plugins/jquery.cookie',
			'jquery/plugins/jquery.supercookie',
			'jquery/plugins/jquery.hotkeys',
			'jquery/plugins/jquery.cookie',
			'jquery/plugins/jquery.simpletab.js',
			'jquery/plugins/jquery.tablednd.js',
			'jquery/plugins/jquery.placeholder',
			'jquery/plugins/jquery.selso',
			'jquery/plugins/jquery.disable.text.select.pack',
			'jquery/plugins/jquery.supercomboselect',
			'jquery/plugins/jquery.MultiFile',
			'fuel/linked_field_formatters',
			'jquery/plugins/jquery.numeric',
			'jquery/plugins/jquery.repeatable',

			// NASTY Chrome JS bug...
			// http://stackoverflow.com/questions/10314992/chrome-sometimes-calls-incorrect-constructor
			// http://stackoverflow.com/questions/10251272/what-could-cause-this-randomly-appearing-error-inside-jquery-itself
			'jquery/plugins/chrome_pushstack_fix',
			'jqx/plugins/util',
			'fuel/global',
		);
	

		// set the folder in which to place the file
		$output_params['type'] = 'js';
		$output_params['whitespace'] = TRUE;
		$output_params['destination'] = assets_server_path('fuel/fuel.min.js', 'js', FUEL_FOLDER);
		$output_params['module'] = FUEL_FOLDER;
		$output = $this->asset->optimize($js, $output_params);
		echo "FUEL JS Optimized!\n";
		//echo $output;
	}

	function optimize_css()
	{
		$css = array(
			'jqmodal',
			'jquery.tooltip', 
			'jquery.treeview',
			'jquery.supercomboselect',
			'markitup',
			'jquery-ui-1.8.17.custom',
			'fuel'
		);
	
		// set the folder in which to place the file
		$output_params['type'] = 'css';
		$output_params['whitespace'] = TRUE;
		$output_params['destination'] = assets_server_path('fuel.min.css', 'css', FUEL_FOLDER);
		$output_params['module'] = FUEL_FOLDER;

		$output = $this->asset->optimize($css, $output_params);
		echo "FUEL CSS Optimized!\n";
		// echo $output;
	}
}