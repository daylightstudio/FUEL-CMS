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
		$redirect = !isset($_POST['send_email']);
		$id = parent::create($redirect);
		$this->_send_email($id);
	}

	function edit($id)
	{
		$user = $this->model->find_by_key($id, 'array');
		if (!empty($user))
		{
			if (!$this->fuel_auth->is_super_admin() && is_true_val($user['super_admin']))
			{
				show_404();
			}
		}
		$redirect = !isset($_POST['send_email']);
		parent::edit($id, $redirect);
		$this->_send_email($id);
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
	
	function _send_email($id)
	{
		if (!empty($id) AND !has_errors() AND isset($_POST['send_email']) AND (!empty($_POST['password']) OR !empty($_POST['new_password'])))
		{
			$password = (!empty($_POST['password'])) ? $this->input->post('password') : $this->input->post('new_password');
			// send email to user
			$this->load->library('email');

			$config['wordwrap'] = TRUE;
			$this->email->initialize($config);

			$this->email->from($this->config->item('from_email', 'fuel'), $this->config->item('site_name', 'fuel'));
			$this->email->to($this->input->post('email')); 
			$this->email->subject(lang('new_user_email_subject'));
			$msg = lang('new_user_email', $this->input->post('user_name'), $password);

			$this->email->message($msg);
	
			if ($this->email->send())
			{
				$this->session->set_flashdata('success', lang('new_user_created_notification', $this->input->post('email')));
				redirect(fuel_uri($this->module_uri.'/edit/'.$id));
			}
			else
			{
				add_error(lang('error_sending_email', $this->input->post('email')));
			}
		}
	}
	
}