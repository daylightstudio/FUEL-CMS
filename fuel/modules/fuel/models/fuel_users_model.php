<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('base_module_model.php');

class Fuel_users_model extends Base_module_model {
	
	public $required = array('user_name', 'email', 'first_name', 'last_name');
	public $filters = array('first_name', 'last_name', 'user_name');
	public $unique_fields = array('user_name');
	public $has_many = array('permissions' => array('model' => array(FUEL_FOLDER => 'fuel_permissions_model')));
	
	function __construct()
	{
		parent::__construct('fuel_users');
	}

	function valid_user($user, $pwd)
	{
		//$where = array('user_name' => $user, 'password' => $password, 'active' => 'yes');
		$where = array('user_name' => $user, 'active' => 'yes');
		$user = $this->find_one_array($where);

		if (empty($user['salt'])) return FALSE;

		if ($user['password'] == $this->salted_password_hash($pwd, $user['salt']))
		{
			return $user;
		}
		return FALSE;
	}
	
	function valid_old_user($user, $pwd)
	{
		$where = array('user_name' => $user, 'active' => 'yes');
		$user = $this->find_one_array($where);
		
		if (empty($user)) {
			return FALSE;
		}
		
		if (empty($user['salt']) AND ($user['password'] == md5($pwd))) {
			return $user;
		}
		
		return FALSE;
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'email', $order = 'desc')
	{
		$CI =& get_instance();
		$user = $CI->fuel->auth->user_data();
		if (!$CI->fuel->auth->is_super_admin())
		{
			$this->db->where(array('super_admin' => 'no'));
		}
		$this->db->select('id, email, user_name, first_name, last_name, super_admin, active');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function user_info($user_id)
	{
		$user = $this->find_one(array('id' => $user_id));
		$user_data = $user->values();
		
		// load user permisisons
		$user_data['permissions'] = $user->permissions;
		return $user_data;
	}
	
	function reset_password($email)
	{
		// check first to see if they exist in the system
		$CI =& get_instance();
		$CI->load->helper('string');
		
		// make sure user exists when saving
		$this->add_validation('email', array(&$this, 'user_exists'), 'User does not exist', '{email}');
		
		$user = $this->find_one_array(array('email' => $email));
		if (!empty($user))
		{
			$reset_key = random_string('alnum', 8);
			//$user['password'] = $new_pwd;
			$user['reset_key'] = $reset_key;
			$where['email'] = $email;
			unset($user['password']);
			if ($this->save($user, $where))
			{
				return $reset_key;
			}
		}
		return false;
	}
	
	function user_exists($email)
	{
		return $this->record_exists(array('email' => $email));
	}
	
	function salt()
	{
		return md5(uniqid(rand(), TRUE));
	}
	
	function salted_password_hash($password, $salt)
	{
		return sha1($password.$salt);
	}
	
	/* overwrite */
	function options_list($key = 'id', $val = 'name', $where = array(), $order = 'name')
	{
		$CI =& get_instance();
		if ($key == 'id')
		{
			$key = $this->table_name.'.id';
		}
		if ($val == 'name')
		{
			$val = 'CONCAT(first_name, " ", last_name) as name';
			$order = 'name';
		}
		else
		{
			$order = $val;
		}

		if (!$CI->fuel->auth->is_super_admin())
		{
			$this->db->where(array('super_admin' => 'no'));
		}
		$return = parent::options_list($key, $val, $where, $order);
		return $return;
	}
	
	function form_fields($values = array(), $related = array())
	{
		$CI =& get_instance();
		$CI->load->helper('directory');
		
		$fields = parent::form_fields($values, $related);
		
		unset($fields['super_admin']);
		
		// save reference it so we can reorder
		$pwd_field = $fields['password'];
		unset($fields['password']);
		
		$user_id = NULL;
		if (!empty($values['id']))
		{
			$user_id = $values['id'];
		}

		if (!empty($user_id))
		{
			$fields['new_password'] = array('label' => lang('form_label_new_password'), 'type' => 'password', 'size' => 20, 'order' => 5);
		}
		else
		{
			$pwd_field['type'] = 'password';
			$pwd_field['size'] = 20;
			$pwd_field['order'] = 5;
			$fields['password']= $pwd_field;
		}
		
		$lang_dirs = list_directories(FUEL_PATH.'language/', array(), FALSE);
		$lang_options = array_combine($lang_dirs, $lang_dirs);
		
		if (count($lang_options) >= 2)
		{
			$fields['language'] = array('type' => 'select', 'options' => $lang_options, 'value' => 'english');
		}
		else
		{
			$fields['language']['type'] = 'hidden';
		}


		$fields['user_name']['order'] = 1;
		$fields['email']['order'] = 2;
		$fields['first_name']['order'] = 3;
		$fields['last_name']['order'] = 4;
		$fields['confirm_password'] = array('label' => lang('form_label_confirm_password'), 'type' => 'password', 'size' => 20, 'order' => 6);
		
		$fields['active']['order'] = 8;

		// get permissions
		$CI =& get_instance();
		$perm_fields = array();
		$user = $CI->fuel->auth->user_data();
		
		//if (($CI->fuel->auth->is_super_admin() AND ($user['id'] != $user_id)) AND (!empty($values['super_admin']) AND $values['super_admin'] != 'yes'))
		if (($user['id'] != $user_id) OR (!$CI->fuel->auth->is_super_admin() AND $CI->fuel->auth->has_permission('permissions')))
		{
			$fields[lang('permissions_heading')] = array('type' => 'section', 'order' => 10);
			$fields['permissions'] = array('type' => 'custom', 'func' => array($this, '_create_permission_fields'), 'order' => 11, 'user_id' => (isset($values['id']) ? $values['id'] : ''));
			$fields['permissions']['mode'] = 'checkbox';
			$fields['permissions']['display_label'] = FALSE;
			$fields['permissions']['wrapper_tag'] = 'div';
		}
		else
		{
			unset($fields['permissions']);
		}

		//$fields = array_merge($fields, $perm_fields);
		unset($fields['reset_key'], $fields['salt']);
		
		return $fields;
	}
	
	function _create_permission_fields($params = array())
	{
		$CI =& get_instance();
		


		// first get the permissions
		$perms_list = array();
		if ($CI->fuel->auth->is_super_admin() OR $CI->fuel->auth->has_permission('permissions'))
		{
			$perms_list = $CI->fuel_permissions_model->find_all_array_assoc('name', array('active' => 'yes'), 'name asc');
		}
		else
		{
			$auth_user_id = $CI->fuel->auth->user_data('id');
			$auth_user = $this->find_by_key($auth_user_id);

			$perms = array();
			$auth_user_perms_obj = $auth_user->get_permissions(TRUE);

			$perms_list = $auth_user_perms_obj->find_all_array_assoc('name');

		}

		// next get the saved permissions for the user
		$user = $this->find_by_key($params['user_id']);

		$user_perms = array();
		if (isset($user->id))
		{
			$user_perms_obj = $user->get_permissions(TRUE);
			if (!empty($user_perms_obj))
			{
				$user_perms = $user_perms_obj->find_all_array_assoc('name');
			}
		}

		$perms = array();
		foreach($perms_list as $perm => $perm_val)
		{
			$sub = explode('/', $perm);
			if (!isset($perms[$sub[0]]))
			{
				$perms[$sub[0]] = array();
			}
			
			if (!isset($sub[1]))
			{
				$perms[$sub[0]] = $perm_val;
				$perms[$sub[0]]['permissions'] = array();
			}
			else
			{
				$perms[$sub[0]]['permissions'][$perm] = $perm_val;
			}
		}
		$str = "<div class=\"perms_list\">\n";
		$str .= "<ul>\n";
		foreach($perms as $key => $val)
		{
			$no_ul = FALSE;
			if (!empty($val['id']))
			{
				$label = lang('perm_'.$val['name']);
  				if (empty($label))
  				{
  					$label = (!empty($val['description'])) ? $val['description'] : $val['name'];
  				}
				$str .= "<li><input type=\"checkbox\"/ name=\"permissions[]\" value=\"".$val["id"]."\" id=\"permission".$val["id"]."\" ".(isset($user_perms[$val['name']]) ? 'checked="checked"' : '')."  /><label for=\"permission".$val["id"]."\"> ".$label."</label>";
			}
			else
			{
				$no_ul = TRUE;
			}

			if (!empty($val['permissions']))
			{
				if (!$no_ul) $str .= "<ul>\n";
				foreach($val['permissions'] as $k => $v)
				{
						$label = lang('perm_'.$v['name']);
		  				if (empty($label))
		  				{
		  					$label = (!empty($v['description'])) ? $v['description'] : $v['name'];
		  				}

					$str .= "\t<li><input type=\"checkbox\"/ name=\"permissions[]\" value=\"".$v["id"]."\" id=\"permission".$v["id"]."\" ".(isset($user_perms[$v['name']]) ? 'checked="checked"' : '')." /><label for=\"permission".$v["id"]."\"> ".$label."</label></li>";
				}
				if (!$no_ul) $str .= "</ul>\n";
			}
			$str .= "</li>\n";
		}
		$str .= "</ul>\n";
		$str .= "</div>\n";
		$str .= '<input type="hidden" name="permissions_exists" value="1" />';
		return $str;
	}
	
	
	function on_before_validate($values)
	{
		$this->add_validation('email', 'valid_email', lang('error_invalid_email'));
		
		// for new 
		if (empty($values['id']))
		{
			$this->required[] = 'password';
			$this->add_validation('email', array(&$this, 'is_new_email'), lang('error_val_empty_or_already_exists', lang('form_label_email')));
			if (isset($this->normalized_save_data['confirm_password']))
			{
				$this->get_validation()->add_rule('password', 'is_equal_to', lang('error_invalid_password_match'), array($this->normalized_save_data['password'], $this->normalized_save_data['confirm_password']));
			}
		}
		
		// for editing
		else
		{
			$this->add_validation('email', array(&$this, 'is_editable_email'), lang('error_val_empty_or_already_exists', lang('form_label_email')), $values['id']);
			if (isset($this->normalized_save_data['new_password']) AND isset($this->normalized_save_data['confirm_password']))
			{
				$this->get_validation()->add_rule('password', 'is_equal_to', lang('error_invalid_password_match'), array($this->normalized_save_data['new_password'], $this->normalized_save_data['confirm_password']));
			}
		}
		unset($values['super_admin']); // can't save from UI as security precaution'
		return $values;
	}

	function on_before_save($values)
	{
		$CI =& get_instance();
		$valid_user = $CI->fuel->auth->valid_user();
		if ((isset($values['id']) AND $valid_user['id'] == $values['id']) AND (isset($values['active']) AND $values['active'] == 'no'))
		{
			show_error(lang('error_cannot_deactivate_yourself'));
		}
		return $values;
	}

	function on_before_clean($values)
	{
		$has_pwd = FALSE;
		if (!empty($values['password'])) 
		{
			if (empty($values['salt'])) $values['salt'] = $this->salt();
			$values['password'] = $this->salted_password_hash($values['password'], $values['salt']);
		}
		if (!empty($values['new_password']))
		{
			if (empty($values['salt'])) $values['salt'] = $this->salt();
			$values['password'] = $this->salted_password_hash($values['new_password'], $values['salt']);
		}
		return $values;
	}
	
	function on_after_save($values)
	{
		parent::on_after_save($values);
		$CI =& get_instance();

		$user = $CI->fuel->auth->user_data();

		// reset session information... 
		if (isset($values['id'], $user['id']) AND $values['id'] == $user['id'])
		{
			if (!empty($values['password']))
			{
				$CI->fuel->auth->set_user_data('password', $values['password']);
			}

			if (!empty($values['language']))
			{
				$CI->fuel->auth->set_user_data('language', $values['language']);
			}
			
		}

		$this->_send_email($values['id']);
	}

	protected function _send_email($id)
	{
		$CI =& get_instance();
		if (!empty($id) AND !has_errors() AND isset($_POST['send_email']) AND (!empty($_POST['password']) OR !empty($_POST['new_password'])))
		{
			$password = (!empty($_POST['password'])) ? $CI->input->post('password') : $CI->input->post('new_password');
			// send email to user
			$CI->load->library('email');

			$config['wordwrap'] = TRUE;
			$CI->email->initialize($config);

			$CI->email->from($CI->config->item('from_email', 'fuel'), $CI->config->item('site_name', 'fuel'));
			$CI->email->to($CI->input->post('email')); 
			$CI->email->subject(lang('new_user_email_subject'));
			$msg = lang('new_user_email', $CI->input->post('user_name'), $password);

			$CI->email->message($msg);
	
			if (!$CI->email->send())
			{
				add_error(lang('error_sending_email', $this->input->post('email')));
			}
		}
	}
	
	function delete($where)
	{
		//prevent the deletion of the super admins
		$where['super_admin'] = 'no';
		return parent::delete($where);
	}

	function is_new_email($email)
	{
		return $this->is_new($email, 'email');
	}
	
	function is_editable_email($email, $id)
	{
		return $this->is_editable($email, 'email', $id);
	}
	
	// used to clear out parent base_module_model common query
	function _common_query()
	{
		
	}

}

class Fuel_user_model extends Base_module_record {

	
}