<?php
class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// for flash data
		$this->load->library('session');

		if (!$this->fuel->config('admin_enabled')) show_404();

		$this->load->vars(array(
			'js' => '', 
			'css' => css($this->fuel->config('xtra_css')), // use CSS function here because of the asset library path changes below
			'js_controller_params' => array(), 
			'keyboard_shortcuts' => $this->fuel->config('keyboard_shortcuts')));

		// change assets path to admin
		$this->asset->assets_path = $this->fuel->config('fuel_assets_path');

		// set asset output settings
		$this->asset->assets_output = $this->fuel->config('fuel_assets_output');
		
		$this->lang->load('fuel');
		$this->load->helper('ajax');
		$this->load->library('form_builder');

		$this->load->module_model(FUEL_FOLDER, 'fuel_users_model');

		// set configuration paths for assets in case they are differernt from front end
		$this->asset->assets_module ='fuel';
		$this->asset->assets_folders = array(
				'images' => 'images/',
				'css' => 'css/',
				'js' => 'js/',
			);

	}
	
	public function index()
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
		
		$session_key = $this->fuel->auth->get_session_namespace();
		
		$user_data = $this->session->userdata($session_key);
		if (!empty($_POST))
		{

			// check if they are locked out out or not
			if (isset($user_data['failed_login_timer']) AND (time() - $user_data['failed_login_timer']) < (int)$this->fuel->config('seconds_to_unlock'))
			{
 				$this->fuel_users_model->add_error(lang('error_max_attempts', $this->fuel->config('seconds_to_unlock')));
				$user_data['failed_login_timer'] = time();
				
			}
			else
			{
				if ($this->input->post('user_name') AND $this->input->post('password'))
				{
					if ($this->fuel->auth->login($this->input->post('user_name', TRUE), $this->input->post('password', TRUE)))
					{
						// reset failed login attempts
						$user_data['failed_login_timer'] = 0;
						// set the cookie for viewing the live site with added FUEL capabilities
						$config = array(
							'name' => $this->fuel->auth->get_fuel_trigger_cookie_name(), 
							'value' => serialize(array('id' => $this->fuel->auth->user_data('id'), 'language' => $this->fuel->auth->user_data('language'))),
							'expire' => 0,
							//'path' => WEB_PATH
							'path' => $this->fuel->config('fuel_cookie_path')
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
							redirect($this->fuel->config('login_redirect'));
						}
					}
					else
					{
						// check if they are no longer in the locked out state and reset variables
						if (isset($user_data['failed_login_timer']) AND (time() - $user_data['failed_login_timer']) > (int)$this->fuel->config('seconds_to_unlock'))
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
						if (isset($user_data['failed_login_attempts']) AND $user_data['failed_login_attempts'] >= (int)$this->fuel->config('num_logins_before_lock') -1)
						{
							$this->fuel_users_model->add_error(lang('error_max_attempts', $this->fuel->config('seconds_to_unlock')));
							$user_data['failed_login_timer'] = time();
							$this->fuel->logs->write(lang('auth_log_account_lockout', $this->input->post('user_name', TRUE), $this->input->ip_address()), 'debug');
						}
						else
						{
							$this->fuel_users_model->add_error(lang('error_invalid_login'));
							$this->fuel->logs->write(lang('auth_log_failed_login', $this->input->post('user_name', TRUE), $this->input->ip_address(), ($user_data['failed_login_attempts'] + 1)), 'debug');
						}
					}
				}
				else
				{
					$this->fuel_users_model->add_error(lang('error_empty_user_pwd'));
				}
			}
			$this->session->set_userdata($session_key, $user_data);
		}
		
		// build form
		
		$this->form_builder->set_validator($this->fuel_users_model->get_validation());
		$fields['user_name'] = array('size' => 25);
		$fields['password'] = array('type' => 'password', 'size' => 25);
		$fields['forward'] = array('type' => 'hidden', 'value' => fuel_uri_segment(2));
		$this->form_builder->show_required = FALSE;
		$this->form_builder->submit_value = lang('login_btn');
		$this->form_builder->set_fields($fields);
		$this->form_builder->remove_js();
		if (!empty($_POST)) $this->form_builder->set_field_values($this->input->post(NULL, TRUE));
		$vars['form'] = $this->form_builder->render();
		
		// set any errors that 
		if ($this->session->flashdata('error'))
		{
			$errors = array($this->session->flashdata('error'));
		}
		else
		{
			$errors =  $this->fuel_users_model->get_errors();
		}
		
		$vars['error'] = $errors;

		// notifications template
		$notifications = $this->load->view('_blocks/notifications', $vars, TRUE);
		$vars['notifications'] = $notifications;
		$vars['display_forgotten_pwd'] = $this->fuel->config('allow_forgotten_password');
		$vars['page_title'] = lang('fuel_page_title');
		$this->load->view('login', $vars);
	}
	
	public function pwd_reset()
	{
		if (!$this->fuel->config('allow_forgotten_password')) show_404();
		$this->js_controller_params['method'] = 'add_edit';

		if (!empty($_POST))
		{
			if ($this->input->post('email'))
			{
				$user = $this->fuel_users_model->find_one_array(array('email' => $this->input->post('email')));
				if (!empty($user['email']))
				{
					$users = $this->fuel->users;
					
					$new_pwd = $this->fuel->users->reset_password($user['email']);
					
					if ($new_pwd !== FALSE)
					{
						$url = 'reset/'.md5($user['email']).'/'.md5($new_pwd);
						$msg = lang('pwd_reset_email', fuel_url($url));
						
						$params['to'] = $this->input->post('email');
						$params['subject'] = lang('pwd_reset_subject');
						$params['message'] = $msg;
						$params['use_dev_mode'] = FALSE;
						
						if ($this->fuel->notification->send($params))
						{
							$this->session->set_flashdata('success', lang('pwd_reset'));
							$this->fuel->logs->write(lang('auth_log_pass_reset_request', $user['email'], $this->input->ip_address()), 'debug');
						}
						else
						{
							$this->session->set_flashdata('error', lang('error_pwd_reset'));
						}
						redirect(fuel_uri('login'));
					}
					else
					{
						$this->fuel_users_model->add_error(lang('error_invalid_email'));
					}
				}
				else
				{
					$this->fuel_users_model->add_error(lang('error_invalid_email'));
				}
			}
			else
			{
				$this->fuel_users_model->add_error(lang('error_empty_email'));
			}
		}
		$this->form_builder->set_validator($this->fuel_users_model->get_validation());
		
		// build form
		$fields['Reset Password'] = array('type' => 'section', 'label' => lang('login_reset_pwd'));
		$fields['email'] = array('required' => TRUE, 'size' => 30);
		$this->form_builder->show_required = FALSE;
		$this->form_builder->set_fields($fields);
		$vars['form'] = $this->form_builder->render();
		
		// notifications template
		$vars['error'] = $this->fuel_users_model->get_errors();
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);
		$vars['page_title'] = lang('fuel_page_title');
		$this->load->view('pwd_reset', $vars);
	}
	
	
	public function dev()
	{
		$this->config->set_item('allow_forgotten_password', FALSE);
		if (!empty($_POST))
		{
			if (!$this->fuel->config('dev_password'))
			{
				redirect('');
			}
			else if ($this->fuel->config('dev_password') == $this->input->post('password', TRUE))
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
		if (!empty($_POST)) $this->form_builder->set_field_values($this->input->post(NULL, TRUE));
		$vars['form'] = $this->form_builder->render();
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);
		
		$vars['display_forgotten_pwd'] = FALSE;
		$vars['instructions'] = lang('dev_pwd_instructions');
		$vars['page_title'] = lang('fuel_page_title');
		$this->load->view('login', $vars);
		
		
	}
}