<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class My_profile extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->module_model(FUEL_FOLDER, 'users_model');
	}
	
	function edit()
	{
		
		$user = $this->fuel_auth->user_data();
		$id = $user['id'];
		
		if (!empty($_POST))
		{
			if ($id)
			{
				if ($this->users_model->save())
				{
					$this->session->set_flashdata('success', lang('data_saved'));
					redirect(fuel_uri('my_profile/edit/'));
				}
			}
		}
		$this->_form($id);
	}

	// seperated to make it easier in subclasses to use the form without rendering the page
	function _form($id = null)
	{
		$this->load->library('form_builder');
		$this->js_controller_params['method'] = 'add_edit';
		
		// create fields... start with the table info and go from there
		$fields = $this->users_model->form_fields($id);
		
		// get saved data
		$saved = array();
		if (!empty($id)) $saved = $this->users_model->user_info($id);

		// set active to hidden since setting this is an buttton/action instead of a form field
		// $fields['active']['type'] = 'hidden';
		unset($fields['active']);
		
		if (!empty($_POST)){
			$field_values = $this->users_model->clean();
		} else {
			$field_values = $saved;
		}
		
		if (!empty($saved['permissions']))
		{
			foreach($saved['permissions'] as $key => $val)
			{
				$field_values['permissions['.$val['perm_id'].']'] = true;
			}
		}
		
		$this->form_builder->form->validator = &$this->users_model->get_validation();
		$this->form_builder->submit_value = lang('btn_save');
		$this->form_builder->use_form_tag = false;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = false;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();
		
		// other variables
		$vars['id'] = $id;
		$vars['data'] = $saved;
		
		// active or publish fields
		$vars['error'] = $this->users_model->get_errors();
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);
		$this->_render('my_profile', $vars);
	}
	
}