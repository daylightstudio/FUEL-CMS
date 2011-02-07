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
 * FUEL Utility Helper
 *
 * This helper is a collection of functions that assists a developer in
 * capturing/debugging content and its related code 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/helpers/asset_helpers
 */

// --------------------------------------------------------------------

/**
 * Capture content via an output buffer
 *
 * @param	boolean	turn on output buffering
 * @param	string	if set to 'all', will clear end the buffer and clean it
 * @return 	string	return buffered content
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

// --------------------------------------------------------------------

/**
 * Format true value
 *
 * @param	mixed	possible true value
 * @return 	string	formatted true value
 */
function is_true_val($val)
{
	$val = strtolower($val);
	return ($val == 'y' || $val == 'yes' || $val === 1  || $val == '1' || $val== 'true' || $val == 't');
}

// --------------------------------------------------------------------

/**
 * Boolean check to determine string content is serialized
 *
 * @param	mixed	possible serialized string
 * @return 	boolean
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

// --------------------------------------------------------------------

/**
 * Print object in human-readible format
 *
 * @param	object	object variable
 * @param	boolean	return as string if true
 * @return 	string
 */
function print_obj($obj, $return = false)
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
			$str .= print_r($obj, true);
		}
	}
	$str .= "</pre>";
	if ($return) return $str;
	echo $str;
}

// --------------------------------------------------------------------

/**
 * Returns the global CI object
 *
 * @return 	object
 */
function CI() {
    if (!function_exists('get_instance')) return FALSE;

    $CI =& get_instance();
    return $CI;
}

/* End of file utility_helper.php */
/* Location: ./application/helpers/utility_helper.php */
