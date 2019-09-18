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
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL users object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_users
 */

// --------------------------------------------------------------------

require_once('Fuel_modules.php');

class Fuel_users extends Fuel_module {

	protected $module = 'users';

	public function initialize($params = array(), $init = array())
	{
		parent::initialize($params, $init);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns a user provided a user ID
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	public function get($user_id, $return_type = NULL)
	{
		if (is_int($user_id))
		{
			$user = $this->model()->find_by_key($user_id, $return_type);
		}
		else
		{
			$user = $this->model()->find_one('(user_name = "'.$user_id.'" OR email = "'.$user_id.'")', 'id', $return_type);
		}
		return $user;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Assigns a permission to a user
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
/*	function assign_permissions_to_user($perms, $user_id)
	{
		foreach($perms as $perm)
		{
			if (!$this->assign_permission_to_user($perm, $user_id))
			{
				$this->_add_error(lang('error_saving'));
			}
		}
		return $this->has_errors();
	}*/

	// --------------------------------------------------------------------

	/**
	 * Assigns a permission to a user
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
/*	function assign_permission_to_user($perm_id, $user_id)
	{
		$this->fuel->load_model('permissions');
		$user = $this->get($user_id);
		$permissions = array();
		$user->permissions = $permissions;
		$user->save();
		if (!isset($user->id)) return FALSE;

		$permission = $this->fuel->permissions->get($perm_id);
		if (!isset($permission->id)) return FALSE;
		
		$perm_to_user = $this->CI->user_to_permissions_model->create();
		$perm_to_user->permission_id = $user->id;
		$perm_to_user->user_id = $user->id;
		return $perm_to_user->save();
	}*/

	// --------------------------------------------------------------------

	/**
	 * Resets a user's password given their email
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	public function reset_password($email)
	{
		// make sure user exists when saving
		$model = &$this->model();
		return $model->reset_password($email);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Checks to see if a user exists based on a user_name or email
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	public function user_exists($email)
	{
		return $this->record_exists(array('email' => $email));
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sends the welcome email to a user
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	public function send_email($user_id)
	{
		$user = $this->get($user_id, 'array');
		
		$params['to'] = $user['email'];
		$params['message'] = lang('new_user_account_email', site_url('fuel/login'), $user['user_name'], $user['password']);
		$params['subject'] = lang('new_user_email_subject');
		$params['use_dev_mode'] = FALSE; // must be set for emails to always go

		if (!$this->fuel->notification->send($params))
		{
			$this->_add_error(lang('error_sending_email'));
			return FALSE;
		}
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the required password strength in an HTML format
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_password_strength_text()
	{
		$str_arr = array();
		if ($this->fuel->config('password_min_length') AND is_numeric($this->fuel->config('password_min_length')))
		{
			$str_arr[] = lang('pwd_min_length_required', $this->fuel->config('password_min_length'));
		}

		if ($this->fuel->config('password_max_length') AND is_numeric($this->fuel->config('password_max_length')))
		{
			$str_arr[] = lang('pwd_max_length_required', $this->fuel->config('password_max_length'));
		}

		if ($this->CI->fuel->config('password_pattern_match'))
		{
			$rules_array = explode("|", strtolower($this->CI->fuel->config('password_pattern_match')));

			if (in_array('lower', $rules_array) OR in_array('lowercase', $rules_array))
			{
				$str_arr[] = lang('pwd_lowercase_required');
			}

			if (in_array('upper', $rules_array) OR in_array('uppercase', $rules_array))
			{
				$str_arr[] = lang('pwd_uppercase_required');
			}

			if (in_array('numbers', $rules_array))
			{
				$str_arr[] = lang('pwd_numbers_required');
			}

			if (in_array('symbols', $rules_array))
			{
				$str_arr[] = lang('pwd_symbols_required').' (e.g. +_!@#$\%^&*.,?-)'; // broken out to prevent sprintf error
			}
		}

		if (!empty($str_arr))
		{
			$str = lang('pwd_requirements');
			$str .= '<ul>';
			foreach($str_arr as $arr)
			{
				$str .= '<li>'.$arr.'</li>';
			}
			$str .= '</ul>';
			return $str;
		}
	}
	
}

/* End of file Fuel_users.php */
/* Location: ./modules/fuel/libraries/Fuel_users.php */