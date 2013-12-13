<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 */

// ------------------------------------------------------------------------

/**
 * An extension of the Hooks class to allow for hooks within other modules
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/modules/hooks
 */
class MY_Hooks extends CI_Hooks {

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
	public function _call_hook($which = '', $params = array())
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
					if (isset($val['params']) AND is_array($params))
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
	public function _run_hook($data)
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