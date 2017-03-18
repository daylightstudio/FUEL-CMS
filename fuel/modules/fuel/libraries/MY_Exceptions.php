<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Exceptions Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/exceptions.html
 */

class MY_Exceptions extends CI_Exceptions {

	// --------------------------------------------------------------------

	/**
	 * 404 Error Handler
	 *
	 * @uses	CI_Exceptions::show_error()
	 *
	 * @param	string	$page		Page URI
	 * @param 	bool	$log_error	Whether to log the error
	 * @return	void
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		// <!-- FUEL 
		// Overwritten to allow for instances where a Controller exists but a method 
		// name doesn't and the 404 error template will still have an instance of $CI
		if ( ! get_instance())
		{
			global $class;
			if (class_exists($class))
			{
				$CI = new $class;
			}
		}
		// -->
		// 
		parent::show_404($page, $log_error);
		
	}
	
}

/* End of file MY_Exceptions.php */
/* Location: ./modules/fuel/libraries/MY_Exceptions.php */