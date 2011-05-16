<?php
class Reset extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->config->load('fuel', true);
	}
	
	function _remap($method)
	{
		if (!$this->config->item('allow_forgotten_password', 'fuel')) show_404();
		$this->load->library('session');
		$this->load->helper('string');
		
		$this->load->module_model(FUEL_FOLDER, 'users_model');
		$this->load->module_language(FUEL_FOLDER, 'fuel');
		
		$email = fuel_uri_segment(2);
		$reset_key = fuel_uri_segment(3);
		$user = $this->users_model->find_one('MD5(email) = "'.$email.'" AND MD5(reset_key) = "'.$reset_key.'"');
		if (isset($user->id))
		{
			$new_pwd = random_string('alnum', 8);
			
			$user->password = $new_pwd;
			$user->reset_key = '';
			if ($user->save())
			{
				$this->load->library('email');

				$config['wordwrap'] = TRUE;
				$this->email->initialize($config);

				$this->email->from($this->config->item('from_email', 'fuel'), $this->config->item('site_name', 'fuel'));
				$this->email->to($user->email);
				$this->email->subject(lang('pwd_reset_subject_success'));
				$msg = lang('pwd_reset_email_success', $new_pwd);

				$this->email->message($msg);
				if ($this->email->send())
				{
					$this->session->set_flashdata('success', lang('pwd_reset_success'));
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