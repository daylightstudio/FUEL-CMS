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
 * FUEL base controller object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_base_controller
 * @autodoc		FALSE
 */

// --------------------------------------------------------------------

define('FUEL_ADMIN', TRUE);

class Fuel_base_controller extends CI_Controller {
	
	public $js_controller = 'fuel.controller.BaseFuelController'; // The default jQX controller
	public $js_controller_params = array(); // jQX controller parameters
	public $js_controller_path = ''; // The path to the jQX controllers. If blank it will load from the fuel/modules/fuel/assets/js/jqx/ directory
	public $nav_selected; // the navigation item in the left menu to show selected
	public $fuel; // the FUEL master object
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	boolean	Determines whether to validate the user or not (optional)
	 * @return	void
	 */	
	public function __construct($validate = TRUE)
	{
		parent::__construct();
		
		$this->fuel->admin->initialize(array('validate' => $validate));
		
		if (method_exists($this, '_init'))
		{
			$this->_init();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the page state for the current page by default
	 *
	 * @access	public
	 * @param	string (optional)
	 * @return	void
	 */	
	public function reset_page_state($state_key = NULL)
	{
		if (empty($state_key))
		{
			$state_key = $this->fuel->admin->get_state_key();
		}
		if (!empty($state_key))
		{
			$session_key = $this->fuel->auth->get_session_namespace();
			$user_data = $this->fuel->auth->user_data();
			$user_data['page_state'] = array();
			$this->session->set_userdata($session_key, $user_data);
			redirect(fuel_url($state_key));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Validates that the currently logged in user has the proper permissions to view the current page
	 *
	 * @access	protected
	 * @param	string The name of the permission to check for the currently logged in user
	 * @param	string The type of permission (e.g. publish, edit, delete) (optional)
	 * @param	boolean Determines whether to show a 404 error or to just exit. Default is to show a 404 error(optional)
	 * @return	void
	 */	
	protected function _validate_user($permission, $type = '', $show_error = TRUE)
	{
		if (!$this->fuel->auth->has_permission($permission, $type))
		{
			if ($show_error)
			{
				show_error(lang('error_no_access', fuel_url()));
			}
			else
			{
				exit();
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generates a CSRF token in case xss is not turned on in CI
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _generate_csrf_token()
	{
		return $this->security->xss_hash();
	}

	// --------------------------------------------------------------------

	/**
	 * Generates a CSRF token in case xss is not turned on in CI
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _get_csrf_token_name()
	{
		return $this->security->get_csrf_token_name().'_FUEL';
	}

	// --------------------------------------------------------------------

	/**
	 * Sets an XSS session variable to be able to check on posts
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _prep_csrf()
	{
		// The session CSRF is only created once otherwise we'll 
		// have issues with inline module editing and elsewhere
		if (!$this->_has_session_csrf())
		{
			$hash = $this->_generate_csrf_token();
			$this->_set_session_csrf($hash);
		}
		else
		{
			$hash = $this->_session_csrf();
		}
		if (!isset($this->form_builder))
		{
			$this->load->library('form_builder');
		}

		$this->form_builder->key_check_name = $this->_get_csrf_token_name();
		$this->form_builder->key_check = $hash;

		$this->load->vars(array('_csrf' => $this->form_builder->key_check, '_csrf_name' => $this->form_builder->key_check_name));
	}

	// --------------------------------------------------------------------

	/**
	 * Determines if the session CSRF exists
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _has_session_csrf()
	{
		return isset($_SESSION[$this->fuel->auth->get_session_namespace()][$this->_get_csrf_token_name()]);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the session CSRF
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _set_session_csrf($hash)
	{
		$_SESSION[$this->fuel->auth->get_session_namespace()][$this->_get_csrf_token_name()] = $hash;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the session CSRF
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _session_csrf()
	{
		return !empty($_SESSION[$this->fuel->auth->get_session_namespace()][$this->_get_csrf_token_name()]) ? $_SESSION[$this->fuel->auth->get_session_namespace()][$this->_get_csrf_token_name()] : NULL;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Validates a submission based on the CSRF token
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _is_valid_csrf()
	{
		return $this->_session_csrf() AND $this->_session_csrf() === $this->input->post($this->_get_csrf_token_name());
	}
}

/* End of file Fuel_base_controller.php */
/* Location: ./modules/fuel/libraries/Fuel_base_controller.php */