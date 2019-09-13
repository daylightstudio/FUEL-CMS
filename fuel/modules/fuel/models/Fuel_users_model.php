<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Extends Base_module_model
 *
 * <strong>Fuel_users_model</strong> is used for managing FUEL users in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_users_model
 */

require_once('Base_module_model.php');

class Fuel_users_model extends Base_module_model {
	
	public $required = array('user_name', 'email', 'first_name', 'last_name'); // User name, email, first name, and last name are required
	public $filters = array('first_name', 'last_name', 'user_name'); // Additional fields that will be searched
	public $unique_fields = array('user_name'); // User name is a unique field
	public $has_many = array('permissions' => array(FUEL_FOLDER => 'fuel_permissions_model')); // Users have a "has_many" relationship with permissions
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_users');
	}

	/**
	 * Determines whether the passed user name and password are valid
	 *
	 * @access	public
	 * @param	string	The user name
	 * @param	string	The password
	 * @return	boolean 
	 */
	public function valid_user($user, $pwd)
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
	
	/**
	 * Determines whether the passed user name and password are valid for FUEL 0.93
	 *
	 * @access	public
	 * @param	string	The user name
	 * @param	string	The password
	 * @return	boolean 
	 */
	public function valid_old_user($user, $pwd)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the user items
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data (optional)
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data (optional)
	 */	
	public function list_items($limit = NULL, $offset = NULL, $col = 'email', $order = 'desc', $just_count = FALSE)
	{
		$CI =& get_instance();
		$user = $CI->fuel->auth->user_data();
		if (!$CI->fuel->auth->is_super_admin())
		{
			$this->db->where(array('super_admin' => 'no'));
		}
		$this->db->select('id, email, user_name, first_name, last_name, super_admin, active');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of the currently logged in user
	 *
	 * @access	public
	 * @param	int The user ID of the person logged in
	 * @return	array
	 */	
	 public function user_info($user_id)
	{
		$user = $this->find_one(array('id' => $user_id));
		$user_data = $user->values();
		
		// load user permissions
		$user_data['permissions'] = $user->permissions;
		return $user_data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Creates a password token for reset
	 *
	 * @access	public
	 * @param	string The email address of the user to reset 
	 * @return	string The new token or FALSE if no user is found with the provided email address
	 */	
	public function get_reset_password_token($email)
	{
		// check first to see if they exist in the system
		$CI =& get_instance();
		$CI->load->helper('string');
		
		// make sure user exists when saving
		$this->add_validation('email', array(&$this, 'user_exists'), 'User does not exist', '{email}');
		
		$user = $this->find_one_array(array('email' => $email));

		if (!empty($user))
		{
			// Generate a token
			$token = $this->generate_token();

			// $user['password'] = $new_pwd;
			$user['reset_key'] = $token;
			$where['email'] = $email;

			unset($user['password']);
			if ($this->save($user, $where))
			{
				return $token;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validates that a reset token exists
	 *
	 * @access	public
	 * @param	string The reset token
 	 * @return	bool
	 */	
	public function validate_reset_token($token)
	{
		$user = $this->find_one('(reset_key = "' . $token .'")', 'id', 'array');
		return count($user);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Resets a password given a provided email and token
	 *
	 * @access	public
	 * @param	string The email address of the user
	 * @param	string The reset token
	 * @param	string The new user password
	 * @return	bool
	 */	
	public function reset_password_from_token($email, $token, $password)
	{
		if ($email && $token)
		{
			$user = $this->find_one('(reset_key = "' . $token . '" AND email = "' . $email . '")', 'id', 'array');

			if (count($user))
			{
				if ($password)
				{
					$user['password'] = $password;
					$user['reset_key'] = '';
					$where['email'] = $email;

					if ($this->save($user, $where))
					{
						return TRUE;
					}
				}
			} 
		}
		
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generates a token used for resetting your password
	 *
	 * @access	public
	 * @return	string
	 */	
	public function generate_token()
	{
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$hash_key =  $this->config->item('encryption_key');
			$length = 16;      
			$bytes = openssl_random_pseudo_bytes($length, $strong);

			$length = 40;
			$string = '';

			while (($len = strlen($string)) < $length)
			{
				$size = $length - $len;
				$string .= substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $size);
			}

			$reset_key = hash_hmac('sha256', $string, $hash_key);
		}
		else
		{
			$reset_key = sha1(uniqid($this->config->item('encryption_key'), TRUE));
		}

		return $reset_key;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines whether a user exists
	 *
	 * @access	public
	 * @param	string The email address of the user
	 * @return	boolean 
	 */	
	public function user_exists($email)
	{
		return $this->record_exists(array('email' => $email));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a random salt value to be used with a user
	 *
	 * @access	public
	 * @return	string 
	 */	
	public function salt()
	{
		return substr(md5(uniqid(rand(), TRUE)), 0, 32);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a hash value based on the users password and salt value
	 *
	 * @access	public
	 * @param	string The users password
	 * @param	string The users salt value
	 * @return	string 
	 */	
	public function salted_password_hash($password, $salt)
	{
		return sha1($password.$salt);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwritten options list method
	 *
	 * @access	public
	 * @param	string The key value for the options list (optional)
	 * @param	string The value (label) value for the options list (optional)
	 * @param	string A where condition to apply to options list data
	 * @param	string The order to return the options list data
	 * @return	array 
	 */	
	public function options_list($key = 'id', $val = 'name', $where = array(), $order = 'name')
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays related password information in the right related box
	 *
	 * @access	public
	 * @param	array Values
	 * @return	string
	 */	
	public function related_items($values = array())
	{
		return $this->fuel->users->get_password_strength_text();
	}

	// --------------------------------------------------------------------
	
	/**
	 * User form fields
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array())
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

		$fields['is_invite'] = array('label' => lang('form_label_new_invite'), 'type' => 'checkbox', 'id' => 'is_invite');

		$fields['confirm_password'] = array('label' => lang('form_label_confirm_password'), 'type' => 'password', 'size' => 20, 'order' => 6);

		if (!empty($user_id))
		{
			$fields['new_password'] = array('label' => lang(	'form_label_new_password'), 'type' => 'password', 'size' => 20, 'order' => 5);
		}
		else
		{
			$pwd_field['type'] = 'password';
			$pwd_field['size'] = 20;
			$pwd_field['order'] = 5;
			//$fields['password']= $pwd_field;
			$fields['new_password'] = array('label' => lang('form_label_password'), 'type' => 'password', 'size' => 20, 'order' => 5, 'required' => TRUE);
			$fields['confirm_password']['required'] = TRUE;
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
		
		$fields['active']['order'] = 8;

		// get permissions
		$CI =& get_instance();
		$perm_fields = array();
		$user = $CI->fuel->auth->user_data();


		//if you are a super admin or a user that has permissions to assign permissions and you are not editing yourself, then display the permissions
		if ($CI->fuel->auth->has_permission('permissions') AND 
			(($user['id'] != $user_id) AND // can't edit yourself's permissions
			(!empty($values['super_admin']) AND $values['super_admin'] != 'yes') OR // super admin profiles don't show permissions
			($CI->fuel->auth->has_permission('permissions') AND empty($values['id']))) // for creating new users so as to show permissions if you are a super admin
			)
		{

			$fields[lang('permissions_heading')] = array('type' => 'section', 'order' => 10);
			$fields['permissions'] = array('type' => 'custom', 'func' => array($this, '_create_permission_fields'), 'order' => 11, 'user_id' => (isset($values['id']) ? $values['id'] : ''));
			$fields['permissions']['mode'] = 'checkbox';
			$fields['permissions']['display_label'] = FALSE;
			$fields['permissions']['wrapper_tag'] = 'div';
			$fields['exists_permissions'] = array('type' => 'hidden', 'value' => 1);
		}
		else
		{
			unset($fields['permissions']);
		}

		//$fields = array_merge($fields, $perm_fields);
		unset($fields['reset_key'], $fields['salt']);
		
		return $fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Permissions custom field callback which returns permission checkboxes
	 *
	 * @access	protected
	 * @param	array The parameters passed to the permissions form field
	 * @return	string 
	 */	
	public function _create_permission_fields($params = array())
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

		// for duplicated users
		elseif (!empty($_POST['permissions']))
		{
			$user_perms = $CI->fuel_permissions_model->find_within($_POST['permissions'], array(), NULL, NULL, NULL, 'name');
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right before the data is cleaned
	 *
	 * @access	public
	 * @param	array The values to be saved right the clean method is run
	 * @return	array Returns the values to be cleaned
	 */	
	public function on_before_clean($values)
	{
		if (!empty($values['new_password'])) 
		{
			// set to blank in order to be picked up on on_before_save
			$values['password'] = $values['new_password'];
		}
		
		return $values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right before validation is run
	 *
	 * @access	public
	 * @param	array The values to be saved right before validation
	 * @return	array Returns the values to be validated right before saving
	 */	
	public function on_before_validate($values)
	{
		$this->add_validation('email', 'valid_email', lang('error_invalid_email'));
		
		// for new 
		if (empty($values['id']))
		{


			$this->required[] = 'password';

			$this->add_validation('email', array(&$this, 'is_new_email'), lang('error_val_empty_or_already_exists', lang('form_label_email')));
			if (isset($this->normalized_save_data['confirm_password']))
			{
				$this->get_validation()->add_rule('password', 'is_equal_to', lang('error_invalid_password_match'), array($this->normalized_save_data['new_password'], $this->normalized_save_data['confirm_password']));
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

		if (!empty($values['password']))
		{
			$this->add_validation('password', array(&$this, 'check_password_strength'), lang('error_val_empty_or_already_exists', lang('form_label_password')));
		}
					
		if (isset($this->normalized_save_data['is_invite']) AND $this->normalized_save_data['is_invite'] == 1)
		{
			$this->remove_validation('password');
			unset($this->required[array_search('password', $this->required)]);
		}

		unset($values['super_admin']); // can't save from UI as security precaution'
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validates that the password meets the required strength specified in the config (under Security config parameters)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	public function check_password_strength($value)
	{
		if ($this->CI->fuel->config('password_min_length') AND is_numeric($this->CI->fuel->config('password_min_length')))
		{
			if (strlen($value) < $this->CI->fuel->config('password_min_length'))
			{
				$this->add_error(lang('error_pwd_too_short', $this->CI->fuel->config('password_min_length')));
				return FALSE;
			}
		}

		if ($this->CI->fuel->config('password_max_length') AND is_numeric($this->CI->fuel->config('password_max_length')))
		{
			if (strlen($value) > $this->CI->fuel->config('password_max_length'))
			{
				$this->add_error(lang('error_pwd_too_long', $this->CI->fuel->config('password_max_length')));
				return FALSE;
			}
		}

		if ($this->CI->fuel->config('password_pattern_match'))
		{
			$rules_array = explode("|", strtolower($this->CI->fuel->config('password_pattern_match')));

			$regex = '/^';

			if (in_array('lower', $rules_array) OR in_array('lowercase', $rules_array))
			{
				$regex .= '(?=.*[a-z])';
			}

			if (in_array('upper', $rules_array) OR in_array('uppercase', $rules_array))
			{
				$regex .= '(?=.*[A-Z])';
			}

			if (in_array('numbers', $rules_array))
			{
				$regex .= '(?=.*\d)';
			}

			if (in_array('symbols', $rules_array))
			{
				$regex .= '(?=.*[-+_!@#$%^&*.,?])';
			}

			$regex .= '.+$/';

			if(!preg_match($regex, $value))
			{

				if (count($rules_array) > 1)
				{
					$rules_array[count($rules_array)-1] = " and " . $rules_array[count($rules_array)-1];
				}

				$missing = implode(", ", $rules_array);
				$this->add_error(lang('error_pwd_invalid', $missing));
				return FALSE;

			}
		}

		return TRUE;

	}

	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right before saving
	 *
	 * @access	public
	 * @param	array The values to be saved right before saving
	 * @return	array Returns the values to be saved
	 */	
	public function on_before_save($values)
	{
		$CI =& get_instance();
		$valid_user = $CI->fuel->auth->valid_user();

		if ((isset($values['id']) AND $valid_user['id'] == $values['id']) AND (isset($values['active']) AND $values['active'] == 'no'))
		{
			show_error(lang('error_cannot_deactivate_yourself'));
		}

		// added here instead of on_before_clean in case of any cleaning that may alter the salt and password values
		if (!empty($values['password'])) 
		{
			$values['salt'] = substr($this->salt(), 0, 32);
			$values['password'] = $this->salted_password_hash($values['password'], $values['salt']);
		}

		if ($this->_is_invite())
		{
			$token = $this->generate_token();
			$values['reset_key']= $token;
		}

		return $values;

	}

	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right after saving
	 *
	 * @access	public
	 * @param	array The values that were just saved
	 * @return	array Returns the values that were saved
	 */	
	public function on_after_save($values)
	{
		parent::on_after_save($values);
		$CI =& get_instance();

		$user = $CI->fuel->auth->user_data();

		// reset session information... 
		if (isset($values['id'], $user['id']) AND $values['id'] == $user['id'])
		{
			// if (!empty($values['password']))
			// {
			// 	$CI->fuel->auth->set_user_data('password', $values['password']);
			// }
		
			if (!empty($values['language']))
			{
				$CI->fuel->auth->set_user_data('language', $values['language']);
			}
			
		}

		if ( ! empty($this->normalized_save_data['new_password']))
		{
			$this->fuel->logs->write(lang('auth_log_cms_pass_reset', $values['user_name'], $this->input->ip_address()), 'debug');
		}
	 
		if ($this->_is_invite())
		{
			$this->_send_email($values['user_name'], $values['reset_key']);
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Protected method that will send out a password change email to a new user
	 *
	 * @access	protected
	 * @param	int The user ID
	 * @return	void
	 */	
	protected function _send_email($user_name, $token) 
	{
		$CI =& get_instance();

		if ($this->_is_invite()) {

			$msg = lang('new_user_email', $user_name, '<a href="'.fuel_url('login/reset/'.$token).'">'.fuel_url('login/reset/'.$token).'</a>');

			$params['to'] = $CI->input->post('email');
			$params['subject'] = lang('new_user_email_subject');
			$params['message'] = $msg;
			$params['use_dev_mode'] = FALSE;
			$params['mailtype'] = 'html';


			if (!$CI->fuel->notification->send($params))
			{
				$CI->fuel->logs->write($CI->fuel->notification->last_error(), 'debug');
				add_error(lang('error_sending_email', $CI->input->post('email')));
			}

		} 
	}

	// --------------------------------------------------------------------
	
	/**
	 * Protected method that checks to see if the save request is an invite request
	 *
	 * @access	protected
	 * @return	boolean
	 */	
	protected function _is_invite()
	{
		return (!has_errors() AND isset($_POST['is_invite']) AND $_POST['is_invite'] == 1 AND isset($_POST['email'])) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Overwrites parent model so as you won't accidentally delete the super admin user
	 *
	 * @access	public
	 * @param	mixed The where condition to be applied to the delete (e.g. array('user_name' => 'darth'))
	 * @return	void
	 */	
	public function delete($where)
	{
		//prevent the deletion of the super admins
		$this->db->where(array('super_admin' => 'no'));
		return parent::delete($where);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validation callback to check if a new user's email already exists
	 *
	 * @access	public
	 * @param	string The email address
	 * @return	boolean
	 */	
	public function is_new_email($email)
	{
		return $this->is_new($email, 'email');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validation callback to check if an existing user's email address doesn't already exist in the system
	 *
	 * @access	public
	 * @param	string The email address
	 * @param	string The email address
	 * @return	boolean
	 */	
	public function is_editable_email($email, $id)
	{
		return $this->is_editable($email, 'email', $id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwritten: used to clear out parent base_module_model common query
	 *
	 * @access	public
	 * @param mixed parameter to pass to common query (optional)
	 * @return	void
	 */	
	public function _common_query($params = NULL)
	{
		
	}

}

class Fuel_user_model extends Base_module_record {

	function get_name()
	{
		return $this->first_name.' '.$this->last_name;
	}
	
}
