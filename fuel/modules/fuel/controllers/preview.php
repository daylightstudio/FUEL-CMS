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
		
		/*
		get query string parameters of module and field name if they exist so we can set those as variables 
		in the view to be used if they want to customize based on those parameters
		*/
		$this->uri->init_get_params();
		$vars['body'] = $data;
		$vars['module'] = $this->input->get('module', TRUE);
		$vars['field'] = $this->input->get('field', TRUE);
		$vars['preview'] = $this->input->get('preview', TRUE);

		$vars['CI'] =& get_instance();
		
		$this->asset->assets_path = $this->config->item('assets_path');
		$view = '';
		if (file_exists(APPPATH.'views/'.$vars['preview'].EXT))
		{
			$view = $this->load->view($vars['preview'], $vars, TRUE);
		}
		else if (file_exists(APPPATH.'views/_fuel_preview'.EXT))
		{
			$view = $this->load->view('_fuel_preview', $vars, TRUE);
		}
		else if (file_exists(APPPATH.'views/_layouts/main'.EXT))
		{
			$view = $this->load->view('_layouts/main', $vars, TRUE);
		}

		// parse for template syntax
		$output = $this->parser->parse_string($view, $vars, TRUE);
		
		// for safe_mailto issue causing conflict (and maybe other js we haven't found yet)
		$output = str_replace(array('<script', '</script>'), array('<xscript', '</xscript>'), $output);
		
		// render the preview
		$this->output->set_output($output);
	}
	
}