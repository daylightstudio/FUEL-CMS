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
 * FUEL redirects object
 *
 * Looks at the redirects configuration for a possible match to do a HTTP redirect.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_redirects
 */

// --------------------------------------------------------------------

class Fuel_redirects extends Fuel_base_library {
	
	public $http_code = 301; // The HTTP response code to return... 301 = permanent redirect
	public $redirects = array(); // The pages to redirect to
	
	/**
	 * Constructor
	 *
	 */
	function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds to the redirects list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @param	string	The page to redirect to (optional)
	 * @return	array	
	 */	
	function add($uri, $redirect = '')
	{
		if (is_array($uri))
		{
			$this->redirects = array_merge($this->redirects, $uri);
		}
		else if (is_string($uri))
		{
			$this->redirects[$uri] = $redirect;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Remove from the redirects list
	 *
	 * @access	public
	 * @param	string	The URI location of the page to remove
	 * @return	array	
	 */	
	function remove($uri)
	{
		if (isset($this->redirects[$uri]))
		{
			unset($this->redirects[$uri]);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of all the redirects
	 *
	 * @access	public
	 * @return	array	
	 */	
	function config()
	{
		include(APPPATH.'config/redirects.php');
		$redirect = array_merge($redirect, $this->redirects);
		return $redirect; 
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loops through the redirects config to find a possible match to redirect a page to
	 *
	 * @access	public
	 * @param	boolean	Determines whether to show a 404 page if the page doesn't exist
	 * @return	void	
	 */	
	function execute($show_404 = TRUE)
	{
		$redirects = $this->config();

		$uri = implode('/', $this->CI->uri->segments);
		$query_string = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
		
		if (!empty($query_string)) 
		{
			$uri = $uri.'?'.$query_string;
		}
		if (!empty($redirects))
		{

			// Is there a literal match?  If so we're done
			if (isset($redirects[$uri]))
			{
				$url = site_url($redirects[$uri]);
				redirect($url, 'location', $this->http_code);
			}

			foreach ($redirects as $key => $val)
			{
				$key = trim($key, '/');
				$val = trim($val, '/');
				
				// Convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// Does the RegEx match?
				if (preg_match('#^'.$key.'$#', $uri))
				{
					// Do we have a back-reference?
					if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
					{
						$val = preg_replace('#^'.$key.'$#', $val, $uri);
					}
					$url = site_url($val);
					redirect($url, 'location', $this->http_code);
				}
			}
		}
		
		if ($show_404 === TRUE)
		{
			// check CMS first if set to AUTO or views
			if ($this->fuel->pages->mode() != 'views')
			{
				$error_404 = $this->fuel->pages->render('404_error', array(), array('render_mode' => 'cms'), TRUE); 
			}

			// then views
			if (empty($error_404) AND file_exists(APPPATH.'views/404_error'.EXT))
			{
				$error_404 = $this->fuel->pages->render('404_error', array(), array('render_mode' => 'views'), TRUE); 
			}

			if (empty($error_404))
			{
				$error_404 = $this->fuel->pages->render('404_error', array(), array('render_mode' => 'views'), TRUE);
			}
			if (!empty($error_404))
			{
				echo $error_404;
				exit();
			}
			else
			{
				show_404();
			}
		}
	}

}

/* End of file Fuel_redirects.php */
/* Location: ./modules/fuel/libraries/Fuel_redirects.php */