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

if (!function_exists('eval_string'))
{
	/**
	 * Evaluates a strings PHP code. Used especially for outputting FUEL page data
	 *
	 * @param	string	string to evaluate
	 * @param	mixed	variables to pass to the string
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
}

// --------------------------------------------------------------------

if (!function_exists('pluralize'))
{
	/**
	 * Add an s to the end of s string based on the number
	 *
	 * @param	int		number to compare against to determine if it needs to be plural
	 * @param	string	string to evaluate
	 * @param	string	plural value to add
	 * @return	string
	 */
	function pluralize($num, $str = '', $plural = 's')
	{
		if (is_array($num))
		{
			$num = count($num);
		}
		
		if ($num != 1)
		{
			$str .= $plural;
		}
		return $str;
	}
}

// --------------------------------------------------------------------

if (!function_exists('strip_whitespace'))
{
	/**
	 * Strips extra whitespace from a string
	 *
	 * @param	string
	 * @return	string
	 */
	function strip_whitespace($str)
	{
		return trim(preg_replace('/\s\s+|\n/m', '', $str));
	}
}

// --------------------------------------------------------------------

if (!function_exists('trim_multiline'))
{
	/**
	 * Trims extra whitespace from the end and beginning of a string on multiple lines
	 *
	 * @param	string
	 * @return	string
	 */
	function trim_multiline($str)
	{
		return trim(implode("\n", array_map('trim', explode("\n", $str))));
	}
}

// --------------------------------------------------------------------

if (!function_exists('smart_ucwords'))
{
	/**
	 * Converts words to title case and allows for exceptions
	 *
	 * @param	string	string to evaluate
	 * @param	mixed	variables to pass to the string
	 * @return	string
	 */
	function smart_ucwords($str, $exceptions = array('of', 'the'))
	{
		$out = "";
		$i = 0;
		foreach (explode(" ", $str) as $word)
		{
			$out .= (!in_array($word, $exceptions) OR $i == 0) ? strtoupper($word[0]) . substr($word, 1) . " " : $word . " ";
			$i++;
		}
		return rtrim($out);
	}
}

// --------------------------------------------------------------------

if (!function_exists('zap_gremlins'))
{
	/**
	 * Removes "Gremlin" characters
	 *
	 * (hidden control characters that the remove_invisible_characters function misses)
	 *
	 * @param	string	string to evaluate
	 * @param	string	the value used to replace a gremlin
	 * @return	string
	 */
	function zap_gremlins($str, $replace = '')
	{
		// there is a hidden bullet looking thingy that photoshop likes to include in it's text'
		// the remove_invisible_characters doesn't seem to remove this
		$str = preg_replace('/[^\x0A\x0D\x20-\x7E]/', $replace, $str);
		return $str;
	}
}

// --------------------------------------------------------------------

if (!function_exists('strip_javascript'))
{
	/**
	 * Removes javascript from a string
	 *
	 * @param	string	string to remove javascript
	 * @return	string
	 */
	function strip_javascript($str)
	{
		if (!is_numeric($str))
		{
			$str = preg_replace('#<script[^>]*>.*?</script>#is', '', $str);
			$str = preg_replace('#(<[^>]*)onerror=|onload=|ontoggle=(.+>)#Uis', '$1$2', $str);
		}

		return $str;
	}
}

// --------------------------------------------------------------------

if (!function_exists('safe_htmlentities'))
{
	/**
	 * Safely converts a string's entities without encoding HTML tags and quotes
	 *
	 * @param	string	string to evaluate
	 * @param	boolean	determines whether to encode the ampersand or not
	 * @param	mixed	determines whether to sanitize the string
	 * @return	string
	 */
	function safe_htmlentities($str, $protect_amp = TRUE, $sanitize = TRUE)
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

		// sanitize
		if ($sanitize)
		{
			$CI = &get_instance();
			$CI->load->config('purifier', TRUE);
			if ($CI->config->item('enabled', 'purifier'))
			{
				// Better method
				$str = html_purify($str);
			}
			else
			{
				$str = strip_javascript($str);
			}
		}

		return $str;
	}
}

// --------------------------------------------------------------------

/**
 * Convert PHP syntax to templating syntax
 *
 * @param	string	string to evaluate
 * @param	string	the templating engine to use
 * @return	string
 */
function php_to_template_syntax($str, $engine = NULL)
{
	$CI =& get_instance();
	if (empty($engine))
	{
		$engine = $CI->fuel->config('parser_engine');
	}
	return $CI->fuel->parser->set_engine($engine)->php_to_syntax($str);
}

// --------------------------------------------------------------------
/**
 * Convert string to  templating syntax
 *
 * @param	string	string to evaluate
 * @param	array	variables to parse with string
 * @param	string	the templating engine to use
 * @param	array	an array of configuration variables like compile_dir, delimiters, allowed_functions, refs and data
 * @return	string
 */
function parse_template_syntax($str, $vars = array(), $engine = NULL, $config = array())
{
	$CI =& get_instance();

	// for backwards compatibility
	if ($engine === TRUE)
	{
		$engine = 'ci';
	}
	elseif (empty($engine))
	{
		$engine = $CI->fuel->config('parser_engine');
	}

	return $CI->fuel->parser->set_engine($engine, $config)->parse_string($str, $vars, TRUE);	
}

/* End of file MY_string_helper.php */
/* Location: ./modules/fuel/helpers/MY_string_helper.php */