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
 * FUEL Convert Helper
 *
 * This helper is to help aid in common conversions of popular
 * formats from one to another. I got the ascii_to_hex function and hex_to_ascii
 * function somewhere but didn't document where for some reason
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/asset_helpers
 */

// --------------------------------------------------------------------

/**
 * Convert ascii characters to hex values
 *
 * @access	public
 * @param	string	string containing ascii characters
 * @return	string
 */	
function ascii_to_hex($ascii)
{
	$hex = '';

	for($i = 0; $i < strlen($ascii); $i++)
	{
		$hex .= str_pad(base_convert(ord($ascii[$i]), 10, 16), 2, '0', STR_PAD_LEFT);
	}
	return $hex;
}

// --------------------------------------------------------------------

/**
 * Convert ascii characters to hex values
 *
 * @access	public
 * @param	string	string containing ascii characters
 * @return	string
 */	   
function hex_to_ascii($hex)
{
	$ascii = '';

	if (strlen($hex) % 2 == 1)
	{
		$hex = '0'.$hex;
	}

	for($i = 0; $i < strlen($hex); $i += 2)
	{
		$ascii .= chr(base_convert(substr($hex, $i, 2), 16, 10));
	}
	return $ascii;
}

// --------------------------------------------------------------------

/**
 * Convert a string into a safe, encoded uri value
 *
 * @access	public
 * @param	string	string to be converted
 * @param	boolean	convert to hex value 
 * @return	string
 */
function uri_safe_encode($str, $hexify = TRUE)
{
	$str = ($hexify) ? ascii_to_hex(base64_encode($str)) : base64_encode($str);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Decode a base64 encoded string
 *
 * @access	public
 * @param	string	string to be converted
 * @param	boolean	value is hexified 
 * @return	string
 */
function uri_safe_decode($str, $hexify = TRUE)
{
	$str = ($hexify) ? base64_decode(hex_to_ascii($str)) : base64_decode($str);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Encode a key/value array or string into a URI safe value
 *
 * @access	public
 * @param	string	string to be converted
 * @param	boolean	value is hexified 
 * @return	string
 */
function uri_safe_batch_encode($uri, $delimiter = '|', $hexify = TRUE)
{
	$str = '';
	if (!empty($uri))
	{
		if (is_string($uri)) {
			$arr = explode('/', $uri);
			foreach($arr as $val)
			{
				$uri[$val] = next($arr);
			}
		}
		foreach($uri as $key => $val)
		{
			if (!is_string($val))
			{
				$val = '??'.serialize($val);
			}
			$str .= $key.'/'.$val.$delimiter;
		}
		return uri_safe_encode($str, $hexify);
	}
	return $str;
}

// --------------------------------------------------------------------

/**
 * Decode a key/value array or string into a URI safe value
 *
 * @access	public
 * @param	string	string to be converted
 * @param	string	delimiter to split string 
 * @param	boolean	value is hexified 
 * @return	string
 */
function uri_safe_batch_decode($str, $delimiter = '|', $hexify = TRUE)
{
	$str = uri_safe_decode($str, $hexify);
	$tmp = explode($delimiter, $str);
	$params = array();
	foreach($tmp as $val)
	{
		$key_val = explode('/', $val);
		if (count($key_val) >= 2)
		{
			if (strncmp($key_val[1], '??', 2) === 0)
			{
				$key_val[1] = unserialize(substr($key_val[1], 2));
			}
			$params[$key_val[0]] = $key_val[1];
		}
	}
	return $params;
}

/* End of file convert_helper.php */
/* Location: ./modules/fuel/helpers/convert_helper.php */
