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
 * FUEL HTML Helper
 *
 * This helper is designed to provide assistance in building custom HTML tags.
 * 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_html_helper
 */

// --------------------------------------------------------------------

if (!function_exists('tag'))
{
	/**
	 * Wrap an string or array of values in opening and closing tag
	 *
	 * @access	public
	 * @param	string	opening tag element
	 * @param	string	closing tag element
	 * @param	mixed	array of values to be enclosed by tags
	 * @param	boolean	echo to the screen
	 * @return	string
	 */
	function tag($tag, $vals, $attrs = array())
	{
		$str = '';
		if (is_array($vals))
		{
			foreach($vals as $val)
			{
				$str .= '<'.$tag.html_attrs($attrs).'>';
				$str .= $val;
				$str .= '</'.$tag.'>';
				$str .= "\n";
			}
		}
		else
		{
			$str .= '<'.$tag.html_attrs($attrs).'>';
			$str .= $vals;
			$str .= '</'.$tag.'>';
		}
		return $str;
	}
}

// --------------------------------------------------------------------

if (!function_exists('quote'))
{
	/**
	 * Wrap a string into an HTML blockquote with quotes and cite added
	 *
	 * @access	public
	 * @param	string	string to be enclosed by quote elements
	 * @param	string	string source value
	 * @param	string	string company/position value
	 * @param	string	string css class
	 * @return	string
	 */
	function quote($quote, $cite = NULL, $title = NULL, $class = 'quote')
	{
		$str = '<blockquote';
		if (!empty($class))
		{
			$str .= ' class="'.$class.'"';
		}
		if (!empty($class))
		{
			$str .= '>';
		}
		$str .= "<span class=\"quote_left\">&#8220;</span>".$quote."<span class=\"quote_right\">&#8221;</span>";
		if (!empty($cite))
		{
			$str .= "<cite>".$cite;
			if (!empty($title))
			{
				$str .= ", <span class=\"cite_title\">".$title."</span>";
			}
			$str .= "</cite>";
		}
		$str .= "</blockquote>";
		return $str;
	}
}

// --------------------------------------------------------------------

if (!function_exists('html_attrs'))
{
	/**
	 * Create HTML attributes
	 *
	 * @access	public
	 * @param	mixed	HTML attributes
	 * @return	string
	 */
	function html_attrs($attrs)
	{
		if (is_array($attrs))
		{
			$str = '';
			foreach($attrs as $key => $val)
			{
				if (is_array($val) AND $key == 'data')
				{
					foreach($val as $k => $v)
					{
						if ($v !== '')
						{
							$str .= ' data-'.$k.'="'.$v.'"';
						}
					}
				}
				else
				{
					if ($val != '')
					{
						$str .= ' '.$key.'="'.$val.'"';
					}
				}
			}
			return $str;
		}
		else if (!empty($attrs))
		{
			return ' '.$attrs;
		}
	}
}

// https://github.com/refringe/CodeIgniter-HTMLPurifier/blob/master/htmlpurifier_helper.php
/*
 * Codeigniter HTMLPurifier Helper
 *
 * Purify input using the HTMLPurifier standalone class.
 * Easily use multiple purifier configurations.
 *
 * @author     Tyler Brownell <tyler.brownell@mssociety.ca>
 * @copyright  Public Domain
 *
 * @access  public
 * @param   string or array  $dirty_html  A string (or array of strings) to be cleaned.
 * @param   string           $config      The name of the configuration (switch case) to use.
 * @return  string or array               The cleaned string (or array of strings).
 */
if (!function_exists('html_purify'))
{
	function html_purify($dirty_html, $config = [], $replace = false)
	{
		// Modified to include the library if it doesn't exist
		require_once(FUEL_PATH.'libraries/HTMLPurifier/HTMLPurifier.standalone.php');

		$CI = &get_instance();
		$CI->load->config('purifier', TRUE);
		
		$settings = $CI->config->item('settings', 'purifier');

		if (is_array($dirty_html))
		{
			foreach ($dirty_html as $key => $val)
			{
				$clean_html[$key] = html_purify($val, $config, $replace);
			}

		} else {

			if (is_string($config) AND isset($settings[$config]))
			{
				$config = $settings[$config];
			}
			else
			{
				$config = ($replace) ? $config : array_merge($settings['default'], $config);
			}
	
			// This is no bueno when sanitizing data so we make sure it's not set unless explicitly passed.
			if (!isset($config['AutoFormat.AutoParagraph']))
			{
				$config['AutoFormat.AutoParagraph'] = false;
			}
	
			$encodeAmpersands = true;
	
			if (isset($config['HTML.EncodeAmpersand']) && $config['HTML.EncodeAmpersand'] === false)
			{
				$encodeAmpersands = false;
				unset($config['HTML.EncodeAmpersand']);
			}
	
			if ($encodeAmpersands)
			{
				$dirty_html = preg_replace('/&(?![a-z#]+;)/i', '__TEMP_AMP__', $dirty_html);
			}
			
			if (empty($config))
			{
				show_error('No HTML purifier configuration found');
			}

			$purifier_config = \HTMLPurifier_Config::createDefault();
			$purifier_config->set('Core.Encoding', $CI->config->item('charset'));
			foreach ($config as $key => $val)
			{
				$purifier_config->set($key, $val);
			}
			
			$purifier_config = \HTMLPurifier_Config::createDefault();
			$purifier = new \HTMLPurifier($purifier_config);
			$clean_html = $purifier->purify($dirty_html);
	
			if ($encodeAmpersands)
			{
				$clean_html = str_replace('__TEMP_AMP__', '&', $clean_html);
			}
		}

		return $clean_html;
	}
}

/* End of file MY_html_helper.php */
/* Location: ./modules/fuel/helpers/MY_html_helper.php */