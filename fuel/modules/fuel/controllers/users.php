<?php
require_once('module.php');

class Users extends Module {
	
	var $module = '';
	
	function __construct()
	{
		parent::__construct();
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
		parent::edit($id, NULL);
	}

	protected function _process_create()
	{
		// reset dup id
		if ($_POST[$this->model->key_field()] == 'dup')
		{
			$_POST[$this->model->key_field()] = '';
			$_POST['user_name'] = '';
			$_POST['password'] = '';
			$_POST['email'] = '';
			$_POST['first_name'] = '';
			$_POST['last_name'] = '';
		}
		
		parent::_process_create();
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