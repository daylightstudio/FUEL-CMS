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
 * FUEL base controller object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel
 */

// --------------------------------------------------------------------

define('FUEL_ADMIN', TRUE);

require_once(MODULES_PATH.FUEL_FOLDER.'/libraries/Fuel.php');

class Fuel_base_controller extends CI_Controller {
	
	public $js_controller = 'fuel.controller.BaseFuelController';
	public $js_controller_params = array();
	public $js_controller_path = '';
	public $nav_selected;
	public $fuel;
	
	function __construct($validate = TRUE)
	{
		parent::__construct();
		$this->fuel->admin->initialize(array('validate' => $validate));
	}

	protected function _validate_user($permission, $type = 'edit', $show_error = TRUE)
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
	
	function reset_page_state()
	{
		$state_key = $this->fuel->admin->get_state_key();
		if (!empty($state_key))
		{
			$session_key = $this->fuel->auth->get_session_namespace();
			$user_data = $this->fuel->auth->user_data();
			$user_data['page_state'] = array();
			$this->session->set_userdata($session_key, $user_data);
			redirect(fuel_url($state_key));
		}
	}


}

/* End of file fuel_base.php */
/* Location: ./modules/fuel/controllers/Fuel_base_controller.php */