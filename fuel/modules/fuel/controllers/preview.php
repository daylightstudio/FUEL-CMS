<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');
class Preview extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->module_library(FUEL_FOLDER, 'fuel_page');
		$this->load->helper('string');
		$this->load->library('parser');
		
		// don't want to fuelify'
		define('FUELIFY', FALSE);
		
		// check for posted data
		$data = $this->input->post('data', TRUE);
		if (empty($data)) show_error(lang('error_cannot_preview'));
		
		// load global variables
		if (file_exists(APPPATH.'views/_variables/global'.EXT))
		{
			include(APPPATH.'views/_variables/global'.EXT);
		}
		
		$vars['body'] = $data;
		$vars['module'] = '';
		$vars['field'] = '';
		$vars['CI'] =& get_instance();
		
		/*
		get query string parameters of module and field name if they exist so we can set those as variables 
		in the view to be used if they want to customize based on thos parameters
		*/
		$this->uri->init_get_params();
		$context = (string) $this->input->get('q', TRUE);
		$context_arr = explode('|', urldecode($context));
		if (isset($context_arr[0])) 
		{
			$vars['module'] = $context_arr[0];
		}

		if (isset($context_arr[1])) 
		{
			$vars['field'] = $context_arr[1];
		}
		
		// set back to site path
		$this->asset->assets_path = $this->config->item('assets_path');
		$view = '';
		if (file_exists(APPPATH.'views/_fuel_preview'.EXT))
		{
			$view = $this->load->view('_fuel_preview', $vars, TRUE);
		}
		else if (file_exists(APPPATH.'views/_layouts/main'.EXT))
		{
			$view = $this->load->view('_layouts/main', $vars, TRUE);
		}
		// parse for template syntac
		$output = $this->parser->parse_string($view, $vars, TRUE);
		
		// render the preview
		$this->output->set_output($output);
	}
	
}