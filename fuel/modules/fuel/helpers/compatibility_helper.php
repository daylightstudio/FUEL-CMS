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
 */

// ------------------------------------------------------------------------

/**
 * Compatibility Helper
 *
 * Compatibility functions helpful for slightly older versions of PHP.
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/compatibility_helper
 */


// --------------------------------------------------------------------

/**
 * Used for older versions of PHP that don't support json_encode.
 * another option http://derekallard.com/blog/post/using-json-on-servers-without-native-support/
 * Original function found here: http://php.net/manual/en/function.json-encode.php
 *
 * @access	public
 * @param	mixed	php value to encode into JSON format
 * @return	string
 */
if (!function_exists('json_encode'))
{
	function json_encode($a=FALSE)
	{
		if (is_null($a)) return 'null';
		if ($a === FALSE) return 'false';
		if ($a === TRUE) return 'true';
		if (is_scalar($a))
		{
			if (is_float($a))
			{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_string($a))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
		}
		$isList = TRUE;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a))
		{
			if (key($a) !== $i)
			{
				$isList = FALSE;
				break;
			}
		}
		$result = array();
		if ($isList)
		{
			foreach ($a as $v) $result[] = json_encode($v);
			return '[' . join(',', $result) . ']';
		}
		else
		{
			foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
}

// --------------------------------------------------------------------

/**
 * Used for older versions of PHP that don't support json_decode.
 * another option http://derekallard.com/blog/post/using-json-on-servers-without-native-support/
 * Original function found here: http://php.net/manual/en/function.json-decode.php
 *
 * @access	public
 * @param	string	json formatted string
 * @return	mixed
 */
if ( !function_exists('json_decode')){ 
	function json_decode($json) 
	{  
		// Author: walidator.info 2009 
		$comment = FALSE; 
		$out = '$x='; 
		
		for ($i=0; $i<strlen($json); $i++) 
		{ 
			if (!$comment) 
			{ 
				if ($json[$i] == '{')
				{
					$out .= ' array('; 
				}
				else if ($json[$i] == '}')
				{
					$out .= ')'; 
				}
				else if ($json[$i] == ':')
				{
					$out .= '=>';
				}
				else
				{
					$out .= $json[$i];
				}
			} 
			else
			{
				$out .= $json[$i];
			}
			if ($json[$i] == '"')
			{
				$comment = !$comment; 
			}
		} 
		eval($out . ';'); 
		return $x; 
	}  
}

// ------------------------------------------------------------------------

/**
 * Used for older versions of PHP that don't support str_getcsv.
 * Original function found here: http://php.net/manual/en/function.str-getcsv.php
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @param	string
 * @return	mixed
 */
if (!function_exists('str_getcsv'))
{ 
	function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\")
	{ 
		$fiveMBs = 5 * 1024 * 1024; 
		$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+'); 
		fputs($fp, $input); 
		rewind($fp); 

		$data = fgetcsv($fp, 1000, $delimiter, $enclosure); //	$escape only got added in 5.3.0 

		fclose($fp); 
		return $data; 
	} 
}

// --------------------------------------------------------------------

/**
 * Not really a compatibility function since it doesn't exist natively in PHP.
 * However it probably should so we provide it here.
 * A version of the original function found here: http://glossword.googlecode.com/svn-history/r600/trunk/core/gw_includes/functions.php
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	mixed
 */
if(!function_exists('str_putcsv'))
{
	function str_putcsv($input, $delimiter = ',', $enclosure = '"')
	{
		// Open a memory "file" for read/write...
		$fp = fopen('php://temp', 'r+');
		// ... write the $input array to the "file" using fputcsv()...
		fputcsv($fp, $input, $delimiter, $enclosure);
		// ... rewind the "file" so we can read what we just wrote...
		rewind($fp);
		// ... read the entire line into a variable...
		$data = fgets($fp);
		// ... close the "file"...
		fclose($fp);
		// ... and return the $data to the caller, with the trailing newline from fgets() removed.
		return rtrim( $data, "\n" );
	}
}

/* End of file compatibility_helper.php */
/* Location: ./modules/fuel/helpers/compatibility_helper.php */
