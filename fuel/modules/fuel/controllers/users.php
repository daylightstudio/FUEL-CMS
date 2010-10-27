<?php
require_once('module.php');

class Users extends Module {
	
	var $module = '';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function create()
	{
		$this->model->required[] = 'password';
		$this->model->add_validation('email', array(&$this->model, 'is_new_email'), 'The email is empty or already exists');
		parent::create();
	}

	function edit($id)
	{
		if (!empty($_POST['new_password']) && isset($_POST['confirm_password'])) {
			$this->model->get_validation()->add_rule('password', 'is_equal_to', 'Your password confirmation needs to match', array($_POST['new_password'], $_POST['confirm_password']));
		}
		$user = $this->model->find_by_key($id, 'array');
		if (!empty($user))
		{
			if (!$this->fuel_auth->is_super_admin() && is_true_val($user['super_admin']))
			{
				show_404();
			}
		}
		$this->model->add_validation('email', array(&$this->model, 'is_editable_email'), 'The email is empty or already exists', $id);
		parent::edit($id);
	}

	// seperated to make it easier in subclasses to use the form without rendering the page
	function _form($id = NULL)
	{
		$this->load->library('form_builder');
		$model = $this->model;
		$this->js_controller_params['method'] = 'add_edit';
		
		// get saved data
		$saved = array();
		if (!empty($id)) $saved = $this->model->user_info($id);
		
		// create fields... start with the table info and go from there
		$fields = $this->model->form_fields($saved);

		// set active to hidden since setting this is an buttton/action instead of a form field
		// $fields['active']['type'] = 'hidden';
		if (!empty($saved['active'])) $fields['active']['value'] = $saved['active'];
		
		$field_values = (!empty($_POST)) ? $_POST : $saved;

		if (!empty($saved))
		{
			foreach($saved as $key => $val)
			{
				if (strncmp($key, 'permissions_', 12) === 0)
				{
					$field_values['permissions_'.$val['perm_id']] = TRUE;
				}
			}
		}
		
		$this->form_builder->form->validator = &$this->model->get_validation();
		$this->form_builder->submit_value = 'Save';
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();
		
		// other variables
		$vars['id'] = $id;
		$vars['data'] = $saved;
		$vars['action'] =  (!empty($saved['id'])) ? 'edit' : 'create';
		
		$vars['others'] = $this->model->get_others('name', $id);
		
		
		// active or publish fields
		$vars['activate'] = (!empty($saved['active']) && is_true_val($saved['active'])) ? 'Deactivate' : 'Activate';
		$vars['module'] = $this->module;
		$vars['actions'] = $this->load->module_view(FUEL_FOLDER, '_blocks/module_create_edit_actions', $vars, TRUE);
		return $vars;
		
	}
	
}