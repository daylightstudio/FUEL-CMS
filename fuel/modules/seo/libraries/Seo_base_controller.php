<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Seo_base_controller extends Fuel_base_controller {
	
	public $view_location = 'seo';
	
	function __construct()
	{
		parent::__construct();
		$this->config->load('seo');
		$this->js_controller_params['module'] = 'seo';
		$this->load->module_language(SEO_FOLDER, 'seo', $this->config->item('language'));
		if ($this->config->item('dev_password', 'fuel'))
		{
			add_error(lang('error_seo_dev_password'));
		}
		
	}
	

}