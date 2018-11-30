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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
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
 * @link		http://docs.getfuelcms.com
 */

class Fuel_hooks
{
	
	public function __construct()
	{
	}
		
	// this hook performs redirects before trying to find the page (vs. passive redirects which will only happen if no page is found by FUEL)
	public function redirects()
	{
		$CI =& get_instance();
		$CI->fuel->redirects->enforce_host();
		$CI->fuel->redirects->ssl();
		$CI->fuel->redirects->non_ssl();

		if (!USE_FUEL_ROUTES)
		{
			$CI->fuel->redirects->execute(FALSE, FALSE);
		}
	}

	// this hook allows us to setup a development password for the site
	public function dev_password()
	{
		if (!USE_FUEL_ROUTES)
		{
			$CI =& get_instance();

			$CI->load->helper('convert');

			// Offline maintenance page not required password
			if( preg_match('#^offline(/?)$#', uri_path(FALSE)) ){
				return;
			}

			if ($CI->fuel->config('dev_password') AND !$CI->fuel->auth->is_logged_in() AND (!preg_match('#^'.fuel_uri('login').'#', uri_path(FALSE))))
			{
				if (isset($_POST['fuel_dev_password']) AND $_POST['fuel_dev_password'] == md5($CI->fuel->config('dev_password')))
				{
					return;
				}

				$CI->load->library('session');
				if (!$CI->session->userdata('dev_password'))
				{
					$forward = uri_safe_encode(uri_string());
                    redirect(FUEL_ROUTE.'login/dev/'.$forward); //to respect your MY_Fuel $config['fuel_path']
				}
			}
		}
	}
	
	// this hook allows us to display an offline page
	public function offline()
	{
		if (!USE_FUEL_ROUTES)
		{
			$CI =& get_instance();

			// Already in offline page
			if( preg_match('#^offline(/?)$#', uri_path(FALSE)) ){
				return;
			}

			if ($CI->fuel->config('offline') AND !$CI->fuel->auth->is_logged_in() AND (!preg_match('#^'.fuel_uri('login').'#', uri_path(FALSE))))
			{

				// By pass offline page if password inputted.
				$CI->load->library('session');
				if ($CI->session->userdata('dev_password'))
				{
					return;
				}

				// Display allowed page
				$allowed_uri = $CI->fuel->config('offline_allowed_uri');
				if( !empty( $allowed_uri ) ) {
					foreach( $allowed_uri as $uri_item ) {
						if( preg_match('#^'.$uri_item.'(/?)$#', uri_path(FALSE)) ){
							return;
						}
					}
				}

				// Instead of using render, changed to redirect
				redirect('offline');

				//echo $CI->fuel->pages->render('offline', array(), array(), TRUE);
				//exit();
			}
		}
	}

	// this hook allows us to enable profiler
	public function post_controller()
	{
		$CI =& get_instance();
		$enable = $CI->config->item('enable_profiler') || $CI->fuel->config('enable_profiler');
		$CI->output->enable_profiler($enable);
	}
}

/* End of file ClassName.php */
/* Location: ./application/hooks/Fuel_hooks.php */
