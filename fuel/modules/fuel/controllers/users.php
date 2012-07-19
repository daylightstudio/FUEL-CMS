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
		$id = parent::create(NULL, $redirect);
		$this->_send_email($id);
	}

	function edit($id)
	{
		$user = $this->model->find_by_key($id, 'array');
		if (!empty($user))
		{
			if (!$this->fuel->auth->is_super_admin() && is_true_val($user['super_admin']))
			{
				show_404();
			}
		}
		$redirect = !isset($_POST['send_email']);
		parent::edit($id, NULL, $redirect);
		$this->_send_email($id);
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
	
	function _toggle_callback($cols, $heading)
	{
		$valid_user = $this->fuel->auth->valid_user();
		$can_publish = ($heading == 'active' AND $this->fuel->auth->has_permission($this->permission) AND $cols['id'] != $valid_user['id']);
		$no = lang("form_enum_option_no");
		$yes = lang("form_enum_option_yes");
		$col_txt = lang('click_to_toggle');

		// boolean fields
		if (!is_true_val($cols[$heading]))
		{
			$text_class = ($can_publish) ? "publish_text unpublished toggle_on" : "unpublished";
			$action_class = ($can_publish) ? "publish_action unpublished hidden" : "unpublished hidden";
			return '<span class="publish_hover"><span class="'.$text_class.'" id="row_published_'.$cols[$this->model->key_field()].'" data-field="'.$heading.'">'.$no.'</span><span class="'.$action_class.'">'.$col_txt.'</span></span>';
		}
		else
		{
			$text_class = ($can_publish) ? "publish_text published toggle_off" : "published";
			$action_class = ($can_publish) ? "publish_action published hidden" : "published hidden";
			return '<span class="publish_hover"><span class="'.$text_class.'" id="row_published_'.$cols[$this->model->key_field()].'" data-field="'.$heading.'">'.$yes.'</span><span class="'.$action_class.'">'.$col_txt.'</span></span>';
			
		}
	}
}