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
 * FUEL Language Helper
 *
 * This helper is designed to help with language files.
 * 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_language_helper
 */


// --------------------------------------------------------------------

/**
 * Get translated local strings with arguments. 
 * Overwrites CI langauge_helper to have a more useful args
 *
 * @param 	string
 * @param 	mixed
 * @return	string
 */
function lang($key, $args = NULL)
{

	// must test for this first because we may load a config 
	// file that uses this function before lang file is loaded
	if (class_exists('CI_Controller'))
	{
		$CI =& get_instance();
		if (!is_array($args))
		{
			$args = func_get_args();
			if (isset($CI->lang->language[$key]))
			{
				$args[0] = $CI->lang->line($key);
			}
			else
			{
				$args[0] = FALSE;
			}
		}
		return call_user_func_array('sprintf', $args);
	}
}

// --------------------------------------------------------------------

/**
 * Creates an array or JSON aobject for your javascript files that need localization
 *
 * @param 	array
 * @param 	boolean
 * @return	string
 */
function json_lang($js_localized = array(), $return_json = TRUE)
{
	
	// if $js_localized is a string, then we assume it is the name of a lang file
	if (is_string($js_localized))
	{
		$path_parts = explode('/', $js_localized);
		
		// we use english because we know it exists... we just want the keys
		if (count($path_parts) >= 2)
		{
			$lang_path = MODULES_PATH.$path_parts[0].'/language/english/'.$path_parts[1].'_lang'.EXT;
		}
		else
		{
			$lang_path = APPPATH.'language/english/'.$path_parts[0].'_lang'.EXT;
		}
		
		if (file_exists($lang_path))
		{
			include($lang_path);
			$js_localized = array_keys($lang);
		}
		else
		{
			$js_localized = array();
		}
	}
	
	$vars = array();
	foreach($js_localized as $key => $val)
	{
		// handle both types of arrays
		if (is_int($key))
		{
			$vars[$val] = lang($val);
		}
		else
		{
			$vars[$key] = lang($key);
		}
	}
	
	if ($return_json)
	{
		return json_encode($vars);
	}
	return $vars;
}

// --------------------------------------------------------------------

/**
 * Detects any specified language settings pulling from the URI, query string and then the user's browser settings
 *
 * @param 	boolean	Determines whether to set the "langauge" config property
 * @return	string
 */
function detect_lang($set_config = FALSE)
{
	$CI =& get_instance();
	return $CI->fuel->language->detect($set_config);
}

/* End of file MY_language_helper.php */
/* Location: ./modules/fuel/helpers/MY_language_helper.php */