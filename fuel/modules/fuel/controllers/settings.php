<?php

require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Settings extends Fuel_base_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->_validate_user('settings');

		$crumbs = array(lang('section_settings'));
		$this->fuel->admin->set_titlebar($crumbs);
	}

	function index()
	{
		$this->fuel->admin->render('settings');
	}

	function manage($module = '')
	{
		if (empty($module)) {
			redirect('fuel/settings');
		}
		
		$fields = $this->fuel->modules->get_advanced_module_settings($module);
		
		if (empty($fields)) {
			show_error("There was a problem with the settings for the advanced module: {$module}.<br />Check that {$module}/config/{$module}.php config is configured to handle settings.");
		}
		
		if ($this->fuel->settings->process($module, $fields))
		{
			if ($module == 'blog') {
				$this->fuel->blog->remove_cache();
			}
			$this->session->set_flashdata('success', lang('data_saved'));
			redirect($this->uri->uri_string());
		}

		$field_values = $this->fuel->settings->get($module);
		
		$this->load->library('form_builder');
		
		//$this->form_builder->id = 'form';
		$this->form_builder->label_layout = 'left';
		$this->form_builder->form->validator = $this->fuel->settings->get_validation();
		//$this->form_builder->submit_value = null;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->name_array = 'settings';
		$this->form_builder->submit_value = 'Save';
		$this->form_builder->set_field_values($field_values);
		
		$vars = array();
		$vars['module'] = $module;
		$vars['form'] = $this->form_builder->render();
		
		$this->fuel->admin->render('manage/settings', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

}