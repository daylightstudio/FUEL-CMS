<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');
class Preview extends Fuel_base_controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->helper('string');
		$this->load->helper('typography');
		$this->load->helper('markdown');
		$this->load->library('parser');
		
		// don't want to fuelify'
		define('FUELIFY', FALSE);

		// check for posted data
		$data = $this->input->get_post('data', FALSE);
		
		//if (empty($data)) show_error(lang('error_cannot_preview'));

		// load global variables
		if (file_exists(APPPATH.'views/_variables/global'.EXT))
		{
			include(APPPATH.'views/_variables/global'.EXT);
		}
		
		/*
		get query string parameters of module and field name if they exist so we can set those as variables 
		in the view to be used if they want to customize based on those parameters
		*/
		$vars['module'] = $this->input->get('module', TRUE);
		$vars['field'] = $this->input->get('field', TRUE);
		$vars['preview'] = $this->input->get('preview', TRUE);
		$vars['CI'] =& get_instance();

		// parse for template syntax here so it doesn't escape single quotes
		$vars['body'] = $this->parser->parse_string($data, $vars, TRUE);
		
		$this->asset->assets_path = $this->config->item('assets_path');
		$view = '';
		
		if (file_exists(APPPATH.'views/_admin/'.$vars['preview'].EXT))
		{
			$view = $this->load->view('_admin/'.$vars['preview'], $vars, TRUE);
		}
		else if (file_exists(APPPATH.'views/_admin/_fuel_preview'.EXT))
		{
			$view = $this->load->view('_admin/_fuel_preview', $vars, TRUE);
		}
		else if (file_exists(APPPATH.'views/_layouts/main'.EXT))
		{
			$view = $this->load->view('_layouts/main', $vars, TRUE);
		}

		// render the preview
		$this->output->set_output($view);
	}
	
}