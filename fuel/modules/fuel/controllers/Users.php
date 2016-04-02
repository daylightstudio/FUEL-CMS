<?php
require_once('Module.php');

class Users extends Module {

	var $module = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function edit($id = NULL, $field = NULL, $redirect = TRUE)
	{
		$user = $this->model->find_by_key($id, 'array');

		if ( ! empty($user))
		{
			// security precaution to make sure that you can't edit a super admins profile unless you are one
			if ( ! $this->fuel->auth->is_super_admin() AND is_true_val($user['super_admin']))
			{
				show_404();
			}
		}

		// security precaution to remove permissions from $_POST if you don't have permissions for err... assigning permissions
		if ( ! empty($_POST['permissions']) AND ! $this->fuel->auth->has_permission('permissions'))
		{
			unset($_POST['permissions']);
		}

		parent::edit($id, NULL);
	}

	/**
	 * Login as another user if super admin
	 */
	public function login_as($id, $original_user_hash = '')
	{
		$this->load->library('session');
		$this->load->module_model('fuel', 'fuel_users_model');

		$change_logged_in_user = $this->fuel->auth->is_super_admin();

		if ($original_user_hash AND ($this->session->userdata('original_user_hash') == $original_user_hash))
		{
			$change_logged_in_user = TRUE;
		}

		if ($change_logged_in_user)
		{
			$curr_user = $this->fuel->auth->user_data();
			$valid_user = $this->fuel_users_model->find_one_array(array('id' => $id));

			$this->fuel->auth->set_valid_user($valid_user);

			if ($original_user_hash)
			{
				$this->session->unset_userdata('original_user_id');
				$this->session->unset_userdata('original_user_hash');
			}
			else
			{
				$this->session->set_userdata('original_user_id', $curr_user['id']);
				$this->session->set_userdata('original_user_hash', random_string('sha1'));
			}
		}

		redirect($this->fuel->config('login_redirect'));
	}

	protected function _process_create()
	{
		// reset dup id
		if ($_POST[$this->model->key_field()] == 'dup')
		{
			$_POST['user_name'] = '';
			$_POST['password'] = '';
			$_POST['email'] = '';
			$_POST['first_name'] = '';
			$_POST['last_name'] = '';
		}

		return parent::_process_create();
	}

	public function _toggle_callback($cols, $heading)
	{
		$valid_user = $this->fuel->auth->valid_user();
		$can_publish = ($heading == 'active' AND $this->fuel->auth->has_permission($this->permission) AND $cols['id'] != $valid_user['id']);
		$no = lang("form_enum_option_no");
		$yes = lang("form_enum_option_yes");
		$col_txt = lang('click_to_toggle');

		// boolean fields
		if ( ! is_true_val($cols[$heading]))
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