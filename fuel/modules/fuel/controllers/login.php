<?php
class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		// for flash data
		$this->load->library('session');
		$this->load->helper('fuel');
		$this->config->load('fuel', true);

		if (!$this->config->item('admin_enabled', 'fuel')) show_404();

		$this->load->vars(array(
			'js' => '', 
			'css' => '', 
			'js_controller_params' => array(), 
			'keyboard_shortcuts' => $this->config->item('keyboard_shortcuts', 'fuel')));

		// change assets path to admin
		$this->asset->assets_path = $this->config->item('fuel_assets_path', 'fuel');
		
		$this->lang->load('fuel');
		$this->load->helper('ajax');
		$this->load->library('form_builder');

		$this->load->module_model(FUEL_FOLDER, 'users_model');

		// set configuration paths for assets in case they are differernt from front end
		$this->asset->assets_module ='fuel';
		$this->asset->assets_folders = array(
				'images' => 'images/',
				'css' => 'css/',
				'js' => 'js/',
			);

	}
	
	function index()
	{
		// check if it's a password request and redirect'
		if ($this->uri->segment(3) == 'pwd_reset')
		{
			$this->pwd_reset();
			return;
		}
		else if ($this->uri->segment(3) == 'dev')
		{
			$this->dev();
			return;
		}
		$this->js_controller_params['method'] = 'add_edit';
		
		$this->load->helper('convert');
		$this->load->helper('cookie');
		
		$session_key = $this->fuel_auth->get_session_namespace();
		
		$user_data = $this->session->userdata($session_key);
		if (!empty($_POST))
		{

			// check if they are locked out out or not
			if (isset($user_data['failed_login_timer']) AND (time() - $user_data['failed_login_timer']) < (int)$this->config->item('seconds_to_unlock', 'fuel'))
			{
 				$this->users_model->add_error(lang('error_max_attempts', $this->config->item('seconds_to_unlock', 'fuel')));
				$user_data['failed_login_timer'] = time();
				
			}
			else
			{
				if ($this->input->post('user_name') AND $this->input->post('password'))
				{
					$this->load->module_library(FUEL_FOLDER, 'fuel_auth');
					if ($this->fuel_auth->login($this->input->post('user_name'), $this->input->post('password')))
					{

						// reset failed login attempts
						$user_data['failed_login_timer'] = 0;
						// set the cookie for viewing the live site with added FUEL capabilities
						$config = array(
							'name' => $this->fuel_auth->get_fuel_trigger_cookie_name(), 
							'value' => serialize(array('id' => $this->fuel_auth->user_data('id'), 'language' => $this->fuel_auth->user_data('language'))),
							'expire' => 0,
							'path' => WEB_PATH
						);

						set_cookie($config);
						
						$forward = $this->input->post('forward');
						$forward_uri = uri_safe_decode($forward);
						if ($forward AND $forward_uri != fuel_uri('dashboard'))
						{
							redirect($forward_uri);
						}
						else
						{
							redirect($this->config->item('login_redirect', 'fuel'));
						}
					}
					else
					{
						// check if they are no longer in the locked out state and reset variables
						if (isset($user_data['failed_login_timer']) AND (time() - $user_data['failed_login_timer']) > (int)$this->config->item('seconds_to_unlock', 'fuel'))
						{
							$user_data['failed_login_attempts'] = 0;
							$this->session->unset_userdata('failed_login_timer');
							unset($user_data['failed_login_timer']);
						}
						else
						{
							// add to the number of attempts if it's an invalid login'
							$num_attempts = (!isset($user_data['failed_login_attempts'])) ? 0 : $user_data['failed_login_attempts'] + 1;
							$user_data['failed_login_attempts'] = $num_attempts;
							
						}
						
						// check if they should be locked out
						if (isset($user_data['failed_login_attempts']) AND $user_data['failed_login_attempts'] >= (int)$this->config->item('num_logins_before_lock', 'fuel') -1)
						{
							$this->users_model->add_error(lang('error_max_attempts', $this->config->item('seconds_to_unlock', 'fuel')));
							$user_data['failed_login_timer'] = time();
						}
						else
						{
							$this->users_model->add_error(lang('error_invalid_login'));
						}
					}
				}
				else
				{
					$this->users_model->add_error(lang('error_empty_user_pwd'));
				}
			}
			$this->session->set_userdata($session_key, $user_data);
		}
		
		// build form
		$this->form_builder->set_validator($this->users_model->get_validation());
		$fields['user_name'] = array('size' => 25);
		$fields['password'] = array('type' => 'password', 'size' => 25);
		$fields['forward'] = array('type' => 'hidden', 'value' => fuel_uri_segment(2));
		$this->form_builder->show_required = FALSE;
		$this->form_builder->submit_value = lang('login_btn');
		$this->form_builder->set_fields($fields);
		if (!empty($_POST)) $this->form_builder->set_field_values($_POST);
		$vars['form'] = $this->form_builder->render();
		
		// notifications template
		$vars['error'] = $this->users_model->get_errors();
		$notifications = $this->load->view('_blocks/notifications', $vars, TRUE);
		$vars['notifications'] = $notifications;
		$vars['display_forgotten_pwd'] = $this->config->item('allow_forgotten_password', 'fuel');
		$vars['page_title'] = lang('fuel_page_title');
		$this->load->view('login', $vars);
	}
	
	function pwd_reset()
	{
		if (!$this->config->item('allow_forgotten_password', 'fuel')) show_404();
		$this->js_controller_params['method'] = 'add_edit';

		if (!empty($_POST))
		{
			if ($this->input->post('email')){
				$user = $this->users_model->find_one_array(array('email' => $this->input->post('email')));
				if (!empty($user['email']))
				{
					$new_pwd = $this->users_model->reset_password($user['email']);
					if ($new_pwd !== FALSE) {

						// send email to user
						$this->load->library('email');

						$config['wordwrap'] = TRUE;
						$this->email->initialize($config);

						$this->email->from($this->config->item('from_email', 'fuel'), $this->config->item('site_name', 'fuel'));
						$this->email->to($this->input->post('email')); 
						$this->email->subject(lang('pwd_reset_subject'));
						$url = 'reset/'.md5($user['email']).'/'.md5($new_pwd);
						$msg = lang('pwd_reset_email', fuel_url($url));

						$this->email->message($msg);
						if ($this->email->send()){
							$this->session->set_flashdata('success', lang('pwd_reset'));
						} else {
							$this->session->set_flashdata('error', lang('error_pwd_reset'));
						}
						redirect(fuel_uri('login'));
					}
					else
					{
						$this->users_model->add_error(lang('error_invalid_email'));
					}
				}
				else
				{
					$this->users_model->add_error(lang('error_invalid_email'));
				}

			}
			else
			{
				$this->users_model->add_error(lang('error_empty_email'));
			}
		}
		$this->form_builder->set_validator($this->users_model->get_validation());
		
		// build form
		$fields['Reset Password'] = array('type' => 'section', 'label' => lang('login_reset_pwd'));
		$fields['email'] = array('required' => true, 'size' => 30);
		$this->form_builder->show_required = false;
		$this->form_builder->set_fields($fields);
		$vars['form'] = $this->form_builder->render();
		
		// notifications template
		$vars['error'] = $this->users_model->get_errors();
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);
		$vars['page_title'] = lang('fuel_page_title');
		$this->load->view('pwd_reset', $vars);
	}
	
	
	function dev()
	{
		$this->config->set_item('allow_forgotten_password', FALSE);
		if (!empty($_POST))
		{
			if (!$this->config->item('dev_password', 'fuel'))
			{
				redirect('');
			}
			else if ($this->config->item('dev_password', 'fuel') == $this->input->post('password', TRUE))
			{
				$this->load->helper('convert');
				$this->session->set_userdata('dev_password', TRUE);
				$forward = uri_safe_decode($this->input->post('forward'));
				redirect($forward);
			}
			else
			{
				add_error(lang('error_invalid_login'));
			}
		}
		$fields['password'] = array('type' => 'password', 'size' => 25);
		$fields['forward'] = array('type' => 'hidden', 'value' => fuel_uri_segment(2));
		$this->form_builder->show_required = FALSE;
		$this->form_builder->submit_value = 'Login';
		$this->form_builder->set_fields($fields);
		if (!empty($_POST)) $this->form_builder->set_field_values($_POST);
		$vars['form'] = $this->form_builder->render();
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);
		
		$vars['display_forgotten_pwd'] = FALSE;
		$vars['instructions'] = lang('dev_pwd_instructions');
		$vars['page_title'] = lang('fuel_page_title');
		$this->load->view('login', $vars);
		
		
	}
}