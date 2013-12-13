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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
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
	 * @access	public
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
				show_error(lang('error_no_access'));
			}
			else
			{
				exit();
			}
		}
	}
}

/* End of file Fuel_base_controller.php */
/* Location: ./modules/fuel/libraries/Fuel_base_controller.php */