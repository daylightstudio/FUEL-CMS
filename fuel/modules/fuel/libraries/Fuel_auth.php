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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
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
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_auth
 */

// --------------------------------------------------------------------

class Fuel_auth extends Fuel_base_library {
	
	protected $_user_perms = array(); // cached values of user permissions
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function __construct($params = array())
	{
		parent::__construct($params);

		$this->CI->load->library('session');
		$this->CI->load->helper('cookie');
		
		// needs to be loaded so that we can use the site name for namespacing
		$this->CI->config->module_load('fuel', 'fuel', TRUE);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Logs a user into the CMS
	 *
	 * @access	public
	 * @param	string	User name
	 * @param	string	Password
	 * @return	boolean
	 */	
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

	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the valid user to the session (used by login method as well)
	 *
	 * @access	public
	 * @param	array	User data to save to the session
	 * @return	void
	 */	
	function set_valid_user($valid_user)
	{
		$this->CI->load->helper('string');
		$this->CI->session->set_userdata($this->get_session_namespace(), $valid_user);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of user information for the current logged in user
	 *
	 * @access	public
	 * @return	array
	 */	
	function valid_user()
	{
		return ($this->CI->session->userdata($this->get_session_namespace())) ? $this->CI->session->userdata($this->get_session_namespace()) : NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets session data for the currently logged in user
	 *
	 * @access	public
	 * @access	string	The array key to associate the session data
	 * @access	mixed	The session data to save
	 * @return	void
	 */	
	function set_user_data($key, $value)
	{
		$session_key = $this->fuel->auth->get_session_namespace();
		$user_data = $this->fuel->auth->user_data();
		$user_data[$key] = $value;
		$this->CI->session->set_userdata($session_key, $user_data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns either an array of the logged in users session data or a single value of the data if a $key parameter is passed
	 *
	 * @access	public
	 * @param	string	The session key value you want access to (optional)
	 * @return	mixed
	 */	
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
	// --------------------------------------------------------------------
	
	/**
	 * Returns the sessions namespace which helps distinguish it from other FUEL installs (it's based on the site_name config parameter)
	 *
	 * @access	public
	 * @return	string
	 */	
	function get_session_namespace()
	{
		$key = 'fuel_'.md5(FCPATH); // unique to the site installation
		if (!$this->CI->session->userdata($key)) $this->CI->session->set_userdata($key, array());
		return $key;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the cookie name used on the front end to trigger the inline editing toolbar
	 *
	 * @access	public
	 * @return	string
	 */	
	function get_fuel_trigger_cookie_name()
	{
		return $this->get_session_namespace();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks the FUEL configuration for any IP restrictions
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function can_access()
	{
		return ($this->fuel->config('admin_enabled') AND 
					(!$this->fuel->config('restrict_to_remote_ip') OR !in_array($_SERVER['REMOTE_ADDR'], $this->fuel->config('restrict_to_remote_ip'))));
	}


	// --------------------------------------------------------------------
	
	/**
	 * Checks if the current user is logged in
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function is_logged_in()
	{
		
		$user = $this->valid_user();
		return (!empty($user) AND !empty($user['user_name']));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks the users permissions for a particular permission
	 *
	 * @access	public
	 * @param	string	The name of the permission (usually the module's name)
	 * @param	string	The type of permission (e.g. 'edit', 'delete'). A user that just has the permission (e.g. my_module) without the type (e.g. my_module_edit) will be given access (optional)
	 * @return	boolean
	 */	
	function has_permission($permission, $type = '')
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
						if ($val != $this->CI->module)
						{
							$permission[$val] = $this->CI->module.'/'.$val;
						}
						else
						{
							$permission[$val] = $val;
							if (empty($type))
							{
								$type = $val;
							}
						}
						unset($permission[$key]);
					}
				}
				
				if (!empty($permission[$type]))
				{
					$permission = $permission[$type];
				}
				else if (empty($type))
				{
					$permission = reset($permission);
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
	

	// --------------------------------------------------------------------
	
	/**
	 * Determines if a user can access a certain module
	 *
	 * @access	public
	 * @param	string	The name of the module
	 * @return	boolean
	 */	
	function accessible_module($module)
	{
		$this->CI->load->module_config('fuel', 'fuel', TRUE);
		$allowed = (array) $this->CI->config->item('modules_allowed', 'fuel');
		return in_array($module, $allowed);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of permissions values for the currently logged in user
	 *
	 * @access	public
	 * @return	array
	 */	
	function get_permissions()
	{
		$valid_user = $this->valid_user();
		if (empty($valid_user['id'])) return FALSE;
		
		// get the users permissions
		if (!empty($this->_user_perms))
		{
			return $this->_user_perms;
		}
		$CI =& get_instance();
		$this->CI->load->module_model(FUEL_FOLDER, 'users_model');
		$where = array('id' => $valid_user['id'], 'active' => 'yes');
		$user = $CI->users_model->find_one($where);
		
		if (empty($user))
		{
			return NULL;
		}
		
		//$user_perms = $user->get_permissions(TRUE, 'name', 'array');
		$this->_user_perms = $user->get_permissions(TRUE)->find_all_array_assoc('name');
		
		if (!empty($this->_user_perms))
		{
			return $this->_user_perms;
		}
		return NULL;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Determines if the currently logged in user is a 'super admin'
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function is_super_admin()
	{
		$valid_user = $this->valid_user();
		
		if (!empty($valid_user['super_admin'])) {
			return ($valid_user['super_admin'] == 'yes');
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks that a module has a specific type of action (e.g. edit, delete)
	 *
	 * @access	public
	 * @param	string	The name of the action
	 * @return	boolean
	 */	
	function module_has_action($action)
	{
		if (empty($this->CI->item_actions)) return FALSE;
		return (isset($this->CI->item_actions[$action]) OR in_array($action, $this->CI->item_actions));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Determines if a user is logged in and can make inline editing changes
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function is_fuelified()
	{
		return (get_cookie($this->get_fuel_trigger_cookie_name()));
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Returns the currently logged in users language preference
	 *
	 * @access	public
	 * @return	string
	 */	
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

	// --------------------------------------------------------------------
	
	/**
	 * Logs a user out of the CMS
	 *
	 * @access	public
	 * @return	void
	 */	
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