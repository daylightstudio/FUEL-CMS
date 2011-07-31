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
 * Social Helper
 *
 * @package		Blog CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/blog/social_helper
 */


// --------------------------------------------------------------------

/**
 * Creates HTML links for the various social sites set in the social configuration
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @param	array
 * @return	string
 */	
function social_bookmarking_links($post_url, $post_title, $joiner = ' | ', $only_show = array())
{
	$CI =& get_instance();
	$social_config = _get_social_config();
	$bookmarks = $social_config['bookmarks'];

	$links = array();
	foreach($social_config['bookmarks'] as $name => $social_url)
	{
		if (!empty($only_show) && in_array($name, $only_show) || empty($only_show))
		{
			$social_url = social_url($social_url, $post_url, $post_title);
			$links[] = '<a href="'.$social_url.'" target="_blank">'.$name.'</a>';
		}
	}
	return implode($joiner, $links);
}

// --------------------------------------------------------------------

/**
 * Creates HTML iframe for facebook recommend
 *
 * @access	public
 * @param	string
 * @return	string
 */	
function social_facebook_recommend($post_url)
{
	$social_config = _get_social_config();
	$src = social_url($social_config['facebook_recommend'], $post_url, NULL);
	return '<iframe src="'.$src.'" scrolling="no" frameborder="0" allowTransparency="true" id="iframe_facebook_recommend"></iframe>';
}

// --------------------------------------------------------------------

/**
 * Creates javascript for facebook share
 *
 * @access	public
 * @return	string
 */	
function social_facebook_share()
{
	$social_config = _get_social_config();
	return generate_social_js($social_config['facebook_share']);
}

// --------------------------------------------------------------------

/**
 * Creates Digg button
 *
 * http://about.digg.com/button for different sizes ... Medium, Large, Compact, Icon
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	string
 */	
function social_digg($post_url, $post_title, $size = 'Icon')
{
	$social_config = _get_social_config();
	$url = social_url($social_config['bookmarks']['Digg'], $post_url, $post_title);
	$str = generate_social_js($social_config['digg']);
	$str .= '<a class="DiggThisButton Digg'.ucfirst($size).'" href="'.$url.'"></a>';
	return $str;
}

// --------------------------------------------------------------------

/**
 * Creates tweetme button
 *
 * @access	public
 * @param	string
 * @return	string
 */	
function social_tweetme($post_url)
{
	$social_config = _get_social_config();
	$str = '<script type="text/javascript">tweetmeme_url = "'.$post_url.'";</script>';
	$str .= generate_social_js($social_config['tweetme'], FALSE);
	return $str;
}

// --------------------------------------------------------------------

/**
 * Creates stumbleupon button
 *
 * @access	public
 * @return	string
 */	
function social_stumbleupon()
{
	$social_config = _get_social_config();
	return generate_social_js($social_config['stumbleupon'], FALSE);
}

// --------------------------------------------------------------------

/**
 * Generates javascript for embedding social media links in a page
 *
 * @access	public
 * @param	string
 * @param	boolean
 * @return	string
 */	
function generate_social_js($path, $async = TRUE)
{
	$GLOBALS['__SOCIAL_JS__'][$path] = $path;
	
	// prevent reloading the script
	if (!empty($GLOBALS['__JS__'][$path])) return;
	if ($async)
	{
		$str = '';
		$str .= "\n<script type=\"text/javascript\">";
		$str .= "(function() {\n";
		$str .= "var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];\n";
		$str .= "s.type = 'text/javascript';\n";
		$str .= "s.async = true;\n";
		$str .= "s.src = '".$path."';\n";
		$str .= "s1.parentNode.insertBefore(s, s1);\n";
		$str .= "})();\n";
		$str .= "</script>\n";
		return $str;
	}
	else
	{
		return '<script type="text/javascript" src="'.$path.'"></script>';
	}
	
}

// --------------------------------------------------------------------

/**
 * Creates a social url link
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	string
 */
function social_url($base_url, $post_url, $post_title = NULL)
{
	$new_url = '';
	if (strpos($base_url, '{url}') === FALSE)
	{
		$new_url = $base_url.urlencode($post_url);
	}
	else
	{
		$new_url = str_replace('{url}', urlencode($post_url), $base_url);
		$new_url = str_replace('{title}', urlencode($post_title), $new_url);
	}
	return $new_url;
}

// --------------------------------------------------------------------

/**
 * Convenience function to get the social configuration
 *
 * @access	public
 * @return	array
 */function _get_social_config()
{
	$CI =& get_instance();
	$CI->load->module_config(BLOG_FOLDER, 'social');
	return $CI->config->item('social');
}

/* End of file social_helper.php */
/* Location: ./modules/blog/helpers/social_helper.php */