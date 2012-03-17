<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('base_module_model.php');

class Users_model extends Base_module_model {
	
	public $required = array('user_name', 'email', 'first_name', 'last_name');
	public $filters = array('first_name', 'last_name', 'user_name');
	public $unique_fields = array('user_name');
	
	function __construct()
	{
		parent::__construct('users');
	}
	
	function valid_user($user, $pwd)
	{
		$where = array('user_name' => $user, 'password' => md5($pwd), 'active' => 'yes');
		return $this->find_one_array($where);
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'email', $order = 'desc')
	{
		$CI =& get_instance();
		$user = $CI->fuel_auth->user_data();
		if (!$CI->fuel_auth->is_super_admin())
		{
			$this->db->where(array('super_admin' => 'no'));
		}
		$this->db->select('id, email, user_name, first_name, last_name, super_admin, active');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function user_info($user_id)
	{
		$user_data = $this->find_one_array(array('id' => $user_id));
		
		// load user permisisons
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'user_to_permissions_model');
		$user_data['permissions'] = $CI->user_to_permissions_model->get_permissions($user_id);
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
		}
		if (!$CI->fuel_auth->is_super_admin())
		{
			$this->db->where(array('super_admin' => 'no'));
		}
		
		$return = parent::options_list($key, $val, $where , $order);
		return $return;
	}
	
	function form_fields($values = null)
	{
		$CI =& get_instance();
		$CI->load->helper('directory');
		
		$fields = parent::form_fields();
		
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
		$user = $CI->fuel_auth->user_data();
		
		//if (($CI->fuel_auth->is_super_admin() AND ($user['id'] != $user_id)) AND (!empty($values['super_admin']) AND $values['super_admin'] != 'yes'))
		if (($CI->fuel_auth->is_super_admin() AND ($user['id'] != $user_id))
			OR (!$CI->fuel_auth->is_super_admin() AND $CI->fuel_auth->has_permission('permissions'))
		)
		{
			$CI->load->module_model(FUEL_FOLDER, 'user_to_permissions_model');
			$selected_perms = $CI->user_to_permissions_model->get_permissions($user_id, FALSE);

			// if (!empty($selected_perms)) 
			// {
				$fields[lang('permissions_heading')] = array('type' => 'section', 'order' => 10);
//			}
			
			$CI->load->module_model(FUEL_FOLDER, 'permissions_model');
			$perms = $CI->permissions_model->find_all_array(array('active' => 'yes'), 'name asc');
			
			$order = 11;
			foreach($perms as $val)
			{
				$perm_field = 'permissions_'.$val['id'];
				$perm_fields[$perm_field]['type'] = 'checkbox';
				$perm_fields[$perm_field]['value'] = $val['id'];
				$perm_fields[$perm_field]['order'] = $order;
				$label = lang('perm_'.$val['name']);
				if (empty($label))
				{
					$label = (!empty($val['description'])) ? $val['description'] : $val['name'];
				}
				$perm_fields[$perm_field]['label'] = $label;
				if (!empty($selected_perms[$val['id']])) $perm_fields[$perm_field]['checked'] = TRUE;
				$order++;
			}
		}
		$fields = array_merge($fields, $perm_fields);
		unset($fields['reset_key']);
		return $fields;
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
		return $values;
	}
	
	function on_before_clean($values)
	{
		//if (empty($values['id']) AND !empty($values['password'])) $values['password'] = md5($values['password']);
		if (!empty($values['password'])) $values['password'] = md5($values['password']);
		if (!empty($values['new_password'])) $values['password'] = md5($values['new_password']);
		unset($values['super_admin']); // can't save from UI as security precaution'
		return $values;
	}
	
	function on_after_save($values)
	{
		$CI =& get_instance();
		
		// delete all permissions first to start clean
		$CI->load->module_model(FUEL_FOLDER, 'user_to_permissions_model');
		
		$has_permission_change = FALSE;
		foreach($this->normalized_save_data as $key => $val)
		{
			if (strncmp($key, 'permissions_', 12) === 0)
			{
				$has_permission_change = TRUE;
				break;
			}
		}
		
		if ($has_permission_change AND !empty($values['id']))
		{
			$CI->user_to_permissions_model->delete(array('user_id' => $values['id']));
		}
		
		foreach($this->normalized_save_data as $key => $val)
		{
			if (strncmp($key, 'permissions_', 12) === 0)
			{
				$perm_values['permission_id'] = str_replace('permissions_', '', $key);
				$perm_values['user_id'] = $values['id'];
				$user_to_perm_saved = $CI->user_to_permissions_model->save($perm_values);
				if (!$user_to_perm_saved) return;
			}
		}

		$user = $CI->fuel_auth->user_data();

		// reset session information... 
		//if (isset($values['id'], $user['id']) AND $values['id'] == $user['id'])
		if (isset($values['id'], $user['id']) AND $values['id'] == $user['id'])
		{
			if (!empty($values['password']))
			{
				$CI->fuel_auth->set_valid_user_property('password', $values['password']);
			}

			if (!empty($values['language']))
			{
				$CI->fuel_auth->set_valid_user_property('language', $values['language']);
			}
			
			//$CI->fuel_auth->set_valid_user($values);
			
		}
		
	}
	
	function delete($where)
	{
		//prevent the deletion of the super admins
		$where['super_admin'] = 'no';
		parent::delete($where);
		$CI =& get_instance();
		$CI->load->module_model('fuel','user_to_permissions_model');
		return $CI->user_to_permissions_model->delete(array('user_id' => $where['id']));
	}
	
	function is_new_email($email)
	{
		if (empty($email)) return FALSE;
		$this->record_exists(array('email' => $email));
		return !$this->record_exists(array('email' => $email));
	}

	function is_editable_email($email, $id)
	{
		$data = $this->find_one_array(array('email' => $email));
		if (empty($data) || (!empty($data) AND $data['id'] == $id)) return TRUE;
		return FALSE;
	}
}

class User_model extends Base_module_record {
}