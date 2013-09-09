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
 * FUEL HTML Helper
 *
 * This helper is designed to provide assistence building custom html tags.
 * 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_html_helper
 */


// --------------------------------------------------------------------

/**
 * Wrap an string or array of values in opening and closing tag
 *
 * @access	public
 * @param 	string 	opening tag element
 * @param 	string 	closing tag element
 * @param 	mixed 	array of values to be enclosed by tags  
 * @param	boolean	echo to the screen
 * @return	string
 */
function tag($tag, $vals)
{
	$str = '';
	if (is_array($vals))
	{
		foreach($vals as $val)
		{
			$str .= '<'.$tag.'>';
			$str .= $val;
			$str .= '</'.$tag.'>';
			$str .= "\n";
		}
	}
	else
	{
		$str .= '<'.$tag.'>';
		$str .= $val;
		$str .= '</'.$tag.'>';
	}
	return $str;
}

// --------------------------------------------------------------------

/**
 * Wrap a string into an HTML blockquote with quotes and cite added
 *
 * @access	public
 * @param 	string 	string to be enclosed by quote elements
 * @param 	string 	string source value
 * @param 	string 	string company/position value 
 * @param 	string 	string css class 
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


// --------------------------------------------------------------------

/**
 * Create HTML attributes
 *
 * @access	public
 * @param 	mixed 	HTML attributs
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

/* End of file MY_html_helper.php */
/* Location: ./modules/fuel/helpers/MY_html_helper.php */