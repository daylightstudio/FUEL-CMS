<?php
class Reset extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->config->load('fuel', true);
	}
	
	public function _remap($method)
	{
		if (!$this->config->item('allow_forgotten_password', 'fuel')) show_404();
		$this->load->library('session');
		$this->load->helper('string');
		
		$this->load->module_model(FUEL_FOLDER, 'fuel_users_model');
		$this->load->module_language(FUEL_FOLDER, 'fuel');
		
		$email = fuel_uri_segment(2);
		$reset_key = fuel_uri_segment(3);
		$user = $this->fuel_users_model->find_one('MD5(email) = "'.$email.'" AND MD5(reset_key) = "'.$reset_key.'"');
		if (isset($user->id))
		{
			$new_pwd = random_string('alnum', 8);
			
			$user->password = $new_pwd;
			$user->reset_key = '';
			if ($user->save())
			{
				$params['to'] = $user->email;
				$params['subject'] = lang('pwd_reset_subject_success');
				$params['message'] = lang('pwd_reset_email_success', $new_pwd);
				$params['use_dev_mode'] = FALSE;
				
				if ($this->fuel->notification->send($params))
				{
					$this->session->set_flashdata('success', lang('pwd_reset_success'));
					$this->fuel->logs->write(lang('auth_log_pass_reset', $user->user_name, $this->input->ip_address()), 'debug');
				}
				else
				{
					$this->session->set_flashdata('error', $this->email->print_debugger());
				}
			}
			else
			{
				$this->session->set_flashdata('error', lang('error_pwd_reset'));
			}
		}
		else
		{
			$this->session->set_flashdata('error', lang('error_pwd_reset'));
		}
		redirect(fuel_url('login'));
		
	}

}