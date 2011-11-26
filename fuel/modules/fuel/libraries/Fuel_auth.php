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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL Authorization object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_admin
 */

// --------------------------------------------------------------------

class Fuel_auth extends Fuel_base_library {
	
	function __construct($params = array()){
		parent::__construct($params);

		$this->CI->load->library('session');
		$this->CI->load->helper('cookie');
		
		// needs to be loaded so that we can use the site name for namespacing
		$this->CI->config->module_load('fuel', 'fuel', TRUE);
	}
	
	function valid_user()
	{
		return ($this->CI->session->userdata($this->get_session_namespace())) ? $this->CI->session->userdata($this->get_session_namespace()) : NULL;
	}
	
	function set_valid_user($valid_user)
	{
		$this->CI->load->helper('string');
		$this->CI->session->set_userdata($this->get_session_namespace(), $valid_user);
	}
	
	function get_session_namespace()
	{
		$key = 'fuel_'.md5($this->CI->config->item('site_name', 'fuel'));
		if (!$this->CI->session->userdata($key)) $this->CI->session->set_userdata($key, array());
		return $key;
	}
	
	function get_fuel_trigger_cookie_name()
	{
		return $this->get_session_namespace();
	}
	
	function set_valid_user_property($prop, $val)
	{
		$user_data = $this->CI->session->userdata($this->get_session_namespace());
		if (isset($user_data[$prop]))
		{
			$user_data[$prop] = $val;
			$this->CI->session->set_userdata($this->get_session_namespace(), $user_data);
		}
	}

	// check any remote host or IP restrictions first
	function can_access()
	{
		return ($this->fuel->config('admin_enabled') AND 
					(!$this->fuel->config('restrict_to_remote_ip') OR !in_array($_SERVER['REMOTE_ADDR'], $this->fuel->config('restrict_to_remote_ip'))));
	}
	
	function login($user, $pwd)
	{
		$this->CI->load->module_model(FUEL_FOLDER, 'users_model');
		$valid_user = $this->CI->users_model->valid_user($user, $pwd);
		unset($valid_user['password'], $valid_user['salt']); // no need to store this too
		if (!empty($valid_user))
		{
			//$valid_user = $this->CI->users_model->user_info($valid_user['id']);
			$this->set_valid_user($valid_user);
			return TRUE;
		}
		return FALSE;
	}
	
	function user_data($key = NULL)
	{
		$valid_user = $this->valid_user();
		
		if (!empty($valid_user))
		{
			if (!empty($key) && isset($valid_user[$key]))
			{
				return $valid_user[$key];
			}
			return $valid_user;
		}
		return FALSE;
	}
	
	function is_logged_in()
	{
		$user = $this->valid_user();
		return (!empty($user) AND !empty($user['user_name']));
	}

	function has_permission($permission, $type = 'edit')
	{
		if ($this->is_super_admin()) return TRUE; // super admin's control anything

		// get the users permissions
		$user_perms = $this->get_permissions();

		if (!empty($user_perms))
		{
			if (is_array($permission))
			{
				foreach($permission as $key => $val)
				{
					if (is_int($key) && !empty($this->CI->module))
					{
						$permission[$val] = $this->CI->module.'_'.$val;
					}
				}
				if (!empty($permission[$type]))
				{
					$permission = $permission[$type];
				}
				else
				{
					$permission = NULL;
				}
			}
			return (!empty($permission) AND !empty($user_perms[$permission]));
		}
		return FALSE;
	}
	
	function accessible_module($module)
	{
		$this->CI->load->module_config('fuel', 'fuel', TRUE);
		$allowed = (array) $this->CI->config->item('modules_allowed', 'fuel');
		return in_array($module, $allowed);
	}
	
	function get_permissions()
	{
		$valid_user = $this->valid_user();
		if (empty($valid_user['id'])) return FALSE;
		
		// get the users permissions
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'user_to_permissions_model');
		$user_perms = $CI->user_to_permissions_model->get_permissions($valid_user['id']);
		if (!empty($user_perms))
		{
			return $user_perms;
		}
		return NULL;
	}
	
	function is_super_admin()
	{
		$valid_user = $this->valid_user();
		
		if (!empty($valid_user['super_admin'])) {
			return ($valid_user['super_admin'] == 'yes');
		}
		return NULL;
	}
	
	function module_has_action($action)
	{
		if (empty($this->CI->item_actions)) return FALSE;
		return (isset($this->CI->item_actions[$action]) OR in_array($action, $this->CI->item_actions));
	}
	
	function is_fuelified()
	{
		return (get_cookie($this->get_fuel_trigger_cookie_name()));
	}
	
	function user_lang()
	{
		$default_lang = $this->CI->config->item('language');
		$cookie_val = get_cookie($this->get_fuel_trigger_cookie_name());
		if (is_string($cookie_val))
		{
			$cookie_val = unserialize($cookie_val);
			if (empty($cookie_val['language']) OR !is_string($cookie_val['language']))
			{
				$cookie_val['language'] = $default_lang;
			}
			return $cookie_val['language'];
		}
		else
		{
			return $default_lang;
		}
	}

	function logout()
	{
		$this->CI->load->library('session');
		$this->CI->session->sess_destroy();
		
		$this->CI->load->helper('cookie');
		
		$this->CI->session->unset_userdata($this->get_session_namespace());
		
		$config = array(
			'name' => $this->get_fuel_trigger_cookie_name(),
			'path' => WEB_PATH
		);
		delete_cookie($config);
		
	}
	
}
/* End of file Fuel_auth.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_auth.php */