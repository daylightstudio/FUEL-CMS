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
 * FUEL Utility Helper
 *
 * This helper is a collection of functions that assists a developer in
 * capturing/debugging content and its related code 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/utility_helper
 */

// --------------------------------------------------------------------

/**
 * Returns the global CI object
 *
 * @return	object
 */
if (!function_exists('CI'))
{
	function CI() {
	    if (!function_exists('get_instance')) return FALSE;

	    $CI =& get_instance();
	    return $CI;
	}
}

// --------------------------------------------------------------------

if (!function_exists('capture'))
{
	/**
	 * Capture content via an output buffer
	 *
	 * @param	boolean	turn on output buffering
	 * @param	string	if set to 'all', will clear end the buffer and clean it
	 * @return	string	return buffered content
	 */
	function capture($on = TRUE, $clean = 'all')
	{
		$str = '';
		if ($on)
		{
			ob_start();
		}
		else
		{
			$str = ob_get_contents();
			if (!empty($str))
			{
				if ($clean == 'all')
				{
					ob_end_clean();
				}
				else if ($clean)
				{
					ob_clean();
				}
			}
			return $str;
		}
	}
}

// --------------------------------------------------------------------

if (!function_exists('is_true_val'))
{
	/**
	 * Format true value
	 *
	 * @param	mixed	possible true value
	 * @return	string	formatted true value
	 */
	function is_true_val($val)
	{
		$val = strtolower($val);
		return ($val == 'y' || $val == 'yes' || $val === 1  || $val == '1' || $val== 'true' || $val == 't');
	}
}

// --------------------------------------------------------------------

if (!function_exists('is_serialized_str'))
{
	/**
	 * Boolean check to determine string content is serialized
	 *
	 * @param	mixed	possible serialized string
	 * @return	boolean
	 */
	function is_serialized_str($data)
	{
		if ( !is_string($data))
			return false;
		$data = trim($data);
		if ( 'N;' == $data )
			return true;
		if ( !preg_match('/^([adObis]):/', $data, $badions))
			return false;
		switch ( $badions[1] ) :
		case 'a' :
		case 'O' :
		case 's' :
			if ( preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
				return true;
			break;
		case 'b' :
		case 'i' :
		case 'd' :
			if ( preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
				return true;
			break;
		endswitch;
		return false;
	}
}

// --------------------------------------------------------------------

if (!function_exists('is_json_str'))
{
	/**
	 * Boolean check to determine string content is a JSON object string
	 *
	 * @param	mixed	possible serialized string
	 * @return	boolean
	 */
	function is_json_str($data)
	{
		if (is_string($data))
		{
			$json = json_decode($data, TRUE);
			return ($json !== NULL AND $data != $json);
		}
		return NULL;
	}
}

// --------------------------------------------------------------------

if (!function_exists('print_obj'))
{
	/**
	 * Print object in human-readable format
	 *
	 * @param	mixed	The variable to dump
	 * @param	boolean	Return string
	 * @return	string
	 */
	function print_obj($obj, $return = FALSE)
	{
		$str = "<pre>";
		if (is_array($obj))
		{
			// to prevent circular references
			if (is_a(current($obj), 'Data_record'))
			{
				foreach($obj as $key => $val)
				{
					$str .= '['.$key.']';
					$str .= $val;
				}
			}
			else
			{
				$str .= print_r($obj, TRUE);
			}
		}
		else
		{
			if (is_a($obj, 'Data_record'))
			{
				$str .= $obj;
			}
			else
			{
				$str .= print_r($obj, TRUE);
			}
		}
		$str .= "</pre>";
		if ($return) return $str;
		echo $str;
	}
}

// --------------------------------------------------------------------

if (!function_exists('log_error'))
{
	/**
	 * Logs an error message to logs file
	 *
	 * @param	string	Error message
	 * @return	void
	 */
	function log_error($error) 
	{
		log_message('error', $error);
	}
}

// --------------------------------------------------------------------

if (!function_exists('is_dev_mode'))
{
	/**
	 * Returns whether the current environment is set for development
	 *
	 * @return	boolean
	 */
	function is_dev_mode()
	{
		return (ENVIRONMENT != 'production');
	}
}

// --------------------------------------------------------------------

if (!function_exists('is_environment'))
{
	/**
	 * Returns whether the current environment is equal to the passed environment
	 *
	 * @access	public
	 * @param	string	environment to test
	 * @return	boolean
	 */
	function is_environment($environment)
	{
		return (strtolower(ENVIRONMENT) == strtolower($environment));
	}
}

/* End of file utility_helper.php */
/* Location: ./modules/fuel/helpers/utility_helper.php */
