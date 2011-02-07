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
 * Blog Helper
 *
 * @package		Blog CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/blog/social_helper
 */


// --------------------------------------------------------------------

/**
 * Returns a blog specific URI. Convience function that maps to fuel_blog->url()
 *
 * @access	public
 * @param	string
 * @return	string
 */
function blog_url($uri)
{
	$CI =& get_instance();
	return $CI->fuel_blog->url($uri);
}

// --------------------------------------------------------------------

/**
 * Returns an HTML block from the specified theme's _block
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	boolean
 * @return	string
 */
function blog_block($view, $vars = array(), $return = TRUE)
{
	$CI =& get_instance();
	$view_folder = $CI->fuel_blog->theme_path();
	$block = $CI->load->module_view(BLOG_FOLDER, $view_folder.'_blocks/'.$view, $vars, TRUE);
	if ($return)
	{
		return $block;
	}
	echo $block;
}

/* End of file blog_helper.php */
/* Location: ./modules/blog/helpers/blog_helper.php */