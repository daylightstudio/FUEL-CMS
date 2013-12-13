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
 * FUEL String Helper
 *
 * This helper extends CI's string helper
 * 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_string_helper
 */

// --------------------------------------------------------------------

/**
 * Evaluates a strings PHP code. Used especially for outputing FUEL page data
 *
 * @param 	string 	string to evaluate
 * @param 	mixed 	variables to pass to the string
 * @return	string
 */
function eval_string($str, $vars = array())
{
	$CI =& get_instance();
	extract($CI->load->get_vars()); // extract cached variables
	extract($vars);

	// fix XML
	$str = str_replace('<?xml', '<@xml', $str);

	ob_start();
	if ((bool) @ini_get('short_open_tag') === FALSE AND $CI->config->item('rewrite_short_tags') == TRUE)
	{
		$str = eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $str)).'<?php ');
	}
	else
	{
		$str = eval('?>'.$str.'<?php ');
	}
	$str = ob_get_clean();
	
	// change XML back
	$str = str_replace('<@xml', '<?xml', $str);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Add an s to the end of s string based on the number 
 *
 * @param 	int 	number to compare against to determine if it needs to be plural
 * @param 	string 	string to evaluate
 * @param 	string 	plural value to add
 * @return	string
 */
// 
function pluralize($num, $str = '', $plural = 's')
{
	if ($num != 1)
	{
		$str .= $plural;
	}
	return $str;
}

// --------------------------------------------------------------------

/**
 * Strips extra whitespace from a string
 *
 * @param 	string
 * @return	string
 */
function strip_whitespace($str)
{
	return trim(preg_replace('/\s\s+|\n/m', '', $str));
}

// --------------------------------------------------------------------

/**
 * Trims extra whitespace from the end and beginning of a string on multiple lines
 *
 * @param 	string
 * @return	string
 */
function trim_multiline($str)
{
	return trim(implode("\n", array_map('trim', explode("\n", $str))));
}

// --------------------------------------------------------------------

/**
 * Converts words to title case and allows for exceptions
 *
 * @param 	string 	string to evaluate
 * @param 	mixed 	variables to pass to the string
 * @return	string
 */
function smart_ucwords($str, $exceptions = array('of', 'the'))
{
	$out = "";
	$i = 0;
	foreach (explode(" ", $str) as $word)
	{
		$out .= (!in_array($word, $exceptions) OR $i == 0) ? strtoupper($word{0}) . substr($word, 1) . " " : $word . " ";
		$i++;
	}
	return rtrim($out);
}

// --------------------------------------------------------------------

/**
 * Removes "Gremlin" characters 
 *
 * (hidden control characters that the remove_invisible_characters function misses)
 *
 * @param 	string 	string to evaluate
 * @return	string
 */
function zap_gremlins($str)
{
	// there is a hidden bullet looking thingy that photoshop likes to include in it's text'
	// the remove_invisible_characters doesn't seem to remove this
	$str = preg_replace('/[^\x0A\x0D\x20-\x7E]/','', $str);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Removes javascript from a string
 *
 * @param 	string 	string to remove javascript
 * @return	string
 */
function strip_javascript($str)
{
	$str = preg_replace('#<script[^>]*>.*?</script>#is', '', $str);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Safely converts a string's entities without encoding HTML tags and quotes
 *
 * @param 	string 	string to evaluate
 * @param 	boolean determines whether to encode the ampersand or not
 * @return	string
 */
function safe_htmlentities($str, $protect_amp = TRUE)
{
	// convert all hex single quotes to numeric ... 
	// this was due to an issue we saw with htmlentities still encoding it's ampersand again'... 
	// but was inconsistent across different environments and versions... not sure the issue
	// may need to look into other hex characters
	$str = str_replace('&#x27;', '&#39;', $str);
	
	// setup temp markers for existing encoded tag brackets 
	$find = array('&lt;','&gt;');
	$replace = array('__TEMP_LT__','__TEMP_GT__');
	$str = str_replace($find,$replace, $str);
	
	// encode just &
	if ($protect_amp)
	{
		$str = preg_replace('/&(?![a-z#]+;)/i', '__TEMP_AMP__', $str);
	}

	// safely translate now
	if (version_compare(PHP_VERSION, '5.2.3', '>='))
	{
		//$str = htmlspecialchars($str, ENT_NOQUOTES, 'UTF-8', FALSE);
		$str = htmlentities($str, ENT_NOQUOTES, config_item('charset'), FALSE);
	}
	else
	{
		$str = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $str);
		$str = str_replace(array('<', '>'), array('&lt;', '&gt;'), $str);
	}
	
	// translate everything back
	$str = str_replace($find, array('<','>'), $str);
	$str = str_replace($replace, $find, $str);
	if ($protect_amp)
	{
		$str = str_replace('__TEMP_AMP__', '&', $str);
	}
	return $str;
}

// --------------------------------------------------------------------

/**
 * Convert PHP syntax to Dwoo templating syntax
 *
 * @param 	string 	string to evaluate
 * @return	string
 */
function php_to_template_syntax($str)
{
	// order matters!!!
	$CI = &get_instance();
	$CI->load->library('parser');
	
	$l_delim = $CI->parser->l_delim;
	$r_delim = $CI->parser->r_delim;
	
	$find = array('$CI->', '$this->', '<?php endforeach', '<?php endif', '<?php echo ', '<?php ', '<?=');
	$replace = array('$', '$', $l_delim.'/foreach', $l_delim.'/if', $l_delim, $l_delim, $l_delim);

	// translate HTML comments NOT! Javascript
	
	// close ending php
	$str = preg_replace('#([:|;])?\s*\?>#U', $r_delim.'$3', $str);

	$str = str_replace($find, $replace, $str);
	
	// TODO javascript escape... commented out because it's problematic... will need to revisit if it makes sense'
	//$str = preg_replace('#((?<!\{literal\}).*)<script(.+)>(.+)<\/script>.*(?!\{\\\literal\})#Us', "$1\n{literal}\n<script$2>$3</script>\n{\literal}\n", $str);
	
	// foreach cleanup
	$str = preg_replace('#'.$l_delim.'\s*foreach\s*\((\$\w+)\s+as\s+\$(\w+)\s*(=>\s*\$(\w+))?\)\s*'.$r_delim.'#U', $l_delim.'foreach $1 $2 $4'.$r_delim, $str); // with and without keys

	// remove !empty
	$callback = create_function('$matches', '
		if (!empty($matches[2]))
		{
			return "'.$l_delim.'".$matches[1].$matches[3];
		}
		else
		{
			return "'.$l_delim.'".$matches[1]."!".$matches[3];
		}');
	
	$str = preg_replace_callback('#'.$l_delim.'(.+)(!)\s*?empty\((.+)\)#U', $callback, $str);
	
	// remove paranthesis from within if conditional
	$callback2 = create_function('$matches', 'return str_replace(array("(", ")"), array(" ", ""), $matches[0]);');
	
	$str = preg_replace_callback('#'.$l_delim.'if.+'.$r_delim.'#U', $callback2, $str);
	
	// fix arrays
	$callback = create_function('$matches', '
		if (strstr($matches[0], "=>"))
		{
			$key_vals = explode(",", $matches[0]);
			$return_arr = array();
			foreach($key_vals as $val)
			{
				@list($k, $v) = explode("=>", $val);
				$k = str_replace(array("\"", "\'"), "", $k);
				$return_arr[] = trim($k)."=".trim($v);
			}
			$return = implode(" ", $return_arr);
			return $return;
		}
		return $matches[0];
		');
	
	$str = preg_replace_callback('#(array\()(.+)(\))#U', $callback, $str);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Convert string to Dwoo templating syntax
 *
 * @param 	string 	string to evaluate
 * @param 	array 	variables to parse with string
 * @param 	string 	the cache ID
 * @return	string
 */
function parse_template_syntax($str, $vars = array(), $cache_id = NULL)
{
	$CI =& get_instance();
	if (!isset($CI->parser))
	{
		$CI->load->library('parser');
	}
	return $CI->parser->parse_string($str, $vars, TRUE, $cache_id);
}

/* End of file MY_string_helper.php */
/* Location: ./modules/fuel/helpers/MY_string_helper.php */