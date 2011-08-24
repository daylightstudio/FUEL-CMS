<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source application Content Management System based on the 
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
 * 	Hooks for FUEL
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide
 */

class Fuel_hooks
{
	
	function __construct()
	{
	}
		
	// this hook allows us to route the the fuel controller if the method 
	// on a controller doesn't exist... not just the controller itself'
	function pre_controller()
	{
		// if called from same Wordpress, the the global scope will not work
		global $method, $class, $RTR;
		$class_methods = get_class_methods($class);
		// for pages without methods defined
		if ((isset($class_methods) AND !in_array($method, $class_methods) AND !in_array('_remap', $class_methods))  AND !empty($RTR->default_controller))
		{
			$fuel_path = explode('/', $RTR->default_controller);
			require_once(FUEL_PATH.'/controllers/'.$fuel_path[1].'.php');
			$class = $fuel_path[1];
		}
	}

	// this hook allows us to route the the fuel controller if the method 
	// on a controller doesn't exist... not just the controller itself'
	function dev_password()
	{
		$CI =& get_instance();
		if ($CI->config->item('dev_password', 'fuel') AND (!isset($CI->fuel_auth) OR !$CI->fuel_auth->is_logged_in() AND !preg_match('#^'.fuel_uri('login').'#', uri_path(FALSE))))
		{
			$CI->load->library('session');
			if (!$CI->session->userdata('dev_password'))
			{
				redirect('fuel/login/dev');
			}
		}
	}
	
	function post_controller()
	{
		$CI =& get_instance();
		$CI->output->enable_profiler($CI->config->item('enable_profiler'));
	}
}

/* End of file ClassName.php */
/* Location: ./application/hooks/Fuel_hooks.php */