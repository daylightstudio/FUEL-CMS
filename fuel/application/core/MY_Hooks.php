<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class MY_Hooks extends CI_Hooks {

	var $enabled		= FALSE;
	var $hooks			= array();
	var $in_progress	= FALSE;

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		$this->_initialize();
		log_message('debug', "Hooks Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the Hooks Preferences
	 *
	 * @access	private
	 * @return	void
	 */
	function _initialize()
	{
		$CFG =& load_class('Config', 'core');

		// If hooks are not enabled in the config file
		// there is nothing else to do

		if ($CFG->item('enable_hooks') == FALSE)
		{
			return;
		}

		// Grab the "hooks" definition file.
		// If there are no hooks, we're done.

		@include(APPPATH.'config/hooks'.EXT);

		if ( ! isset($hook) OR ! is_array($hook))
		{
			return;
		}

		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Call Hook
	 *
	 * Calls a particular hook
	 *
	 * @access	private
	 * @param	string	the hook name
	 * @param	array	params
	 * @return	mixed
	 */
	function _call_hook($which = '', $params = array())
	{
		if ( ! $this->enabled OR ! isset($this->hooks[$which]))
		{
			return FALSE;
		}
		
		if (isset($this->hooks[$which][0]) AND is_array($this->hooks[$which][0]))
		{
			foreach ($this->hooks[$which] as $val)
			{
				
				// -----------------------------------
				// Add params to data to be passed to hooks
				// -----------------------------------
				
				if (!empty($params))
				{
					if (!isset($val['params']))
					{
						$val['params'] = array_merge($val['params'], $params);
					}
					else
					{
						$val['params'] = $params;
					}
				}
				$this->_run_hook($val);
			}
		}
		else
		{
			// -----------------------------------
			// Add params to data to be passed to hooks
			// -----------------------------------
			if (!empty($params))
			{
				if (!isset($this->hooks[$which]))
				{
					$this->hooks[$which]['params'] = array_merge($this->hooks[$which], $params);
				}
				else
				{
					$this->hooks[$which]['params'] = $params;
				}
			}
			$this->_run_hook($this->hooks[$which]);
		}
		
		
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Run Hook
	 *
	 * Runs a particular hook
	 *
	 * @access	private
	 * @param	array	the hook details
	 * @return	bool
	 */
	function _run_hook($data)
	{
		if ( ! is_array($data))
		{
			return FALSE;
		}

		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------

		// If the script being called happens to have the same
		// hook call within it a loop can happen

		if ($this->in_progress == TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------

		if ( ! isset($data['filepath']) OR ! isset($data['filename']))
		{
			return FALSE;
		}
		
		// -----------------------------------
		// Either pull from fuel/module/{module} folder or fuel/application folder
		// -----------------------------------
		if ( isset($data['module']) AND $data['module'] !== 'app')
		{
			$filepath = MODULES_PATH.$data['module'].'/'.$data['filepath'].'/'.$data['filename'];
		}
		else
		{
			$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];
		}
		
		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		// -----------------------------------
		// Set class/function name
		// -----------------------------------

		$class		= FALSE;
		$function	= FALSE;
		$params		= '';

		if (isset($data['class']) AND $data['class'] != '')
		{
			$class = $data['class'];
		}

		if (isset($data['function']))
		{
			$function = $data['function'];
		}

		if (isset($data['params']))
		{
			$params = $data['params'];
		}

		if ($class === FALSE AND $function === FALSE)
		{
			return FALSE;
		}

		// -----------------------------------
		// Set the in_progress flag
		// -----------------------------------

		$this->in_progress = TRUE;

		// -----------------------------------
		// Call the requested class and/or function
		// -----------------------------------

		if ($class !== FALSE)
		{
			if ( ! class_exists($class))
			{
				require($filepath);
			}

			$HOOK = new $class;
			$HOOK->$function($params);
		}
		else
		{
			if ( ! function_exists($function))
			{
				require($filepath);
			}

			$function($params);
		}

		$this->in_progress = FALSE;
		return TRUE;
	}

}

// END CI_Hooks class

/* End of file Hooks.php */
/* Location: ./system/core/Hooks.php */