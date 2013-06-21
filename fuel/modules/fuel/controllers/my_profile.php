<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class My_profile extends Fuel_base_controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->module_model(FUEL_FOLDER, 'fuel_users_model');
	}
	
	public function edit()
	{
		
		$user = $this->fuel->auth->user_data();
		$id = $user['id'];
		
		if (!empty($_POST))
		{
			if ($id)
			{
				if ($this->fuel_users_model->save())
				{
					$this->fuel->admin->set_notification(lang('data_saved'), Fuel_admin::NOTIFICATION_SUCCESS);
					redirect(fuel_uri('my_profile/edit/'));
				}
			}
		}
		$this->_form($id);
	}

	// seperated to make it easier in subclasses to use the form without rendering the page
	public function _form($id = null)
	{
		$this->load->library('form_builder');
		$this->js_controller_params['method'] = 'add_edit';
		
		// create fields... start with the table info and go from there
		$values = array('id' => $id);
		$fields = $this->fuel_users_model->form_fields($values);
		
		// remove permissions
		unset($fields['permissions']);
		
		// get saved data
		$saved = array();
		if (!empty($id))
		{
			$saved = $this->fuel_users_model->user_info($id);
		}

		// remove active from field list to prevent them from updating it
		unset($fields['active'], $fields['Permissions']);

		
		if (!empty($_POST))
		{
			$field_values = $this->fuel_users_model->clean();
		}
		else
		{
			$field_values = $saved;
		}
		
		$this->form_builder->form->validator = &$this->fuel_users_model->get_validation();
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
		$errors = $this->fuel_users_model->get_errors();
		if (!empty($errors))
		{
			add_errors($errors);	
		}
		
		$this->fuel->admin->set_titlebar_icon('ico_users');
		
		$crumbs = lang('section_my_profile');
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->render('my_profile', $vars);
	}
	
}