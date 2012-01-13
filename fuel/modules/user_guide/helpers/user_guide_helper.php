<?php 
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
 * User Guide Helper
 *
 * Contains several convenience functions for creating user documentation.
 *
 * @package		User Guide
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/user_guide/user_guide_helper
 */


// --------------------------------------------------------------------

/**
 * Convenience function to easily create user guide urls
 * 
 * <code>
 * echo user_guide_url('libraries/assets);
 * </code>
 *
 * @access	public
 * @param	string	URI location
 * @return	string
 */
function user_guide_url($uri = '')
{
	$CI =& get_instance();
	$url_base = $CI->fuel->user_guide->config('root_url');
	return site_url($url_base.$uri);
}


// --------------------------------------------------------------------

/**
 * Generates the class documentation based on the class passed to it
 * 
 * <code>
 * $vars = array('intro');
 * echo generate_class_docs('Fuel_cache', $vars);
 * </code>
 *
 * @access	public
 * @param	string	Name of class
 * @param	array 	Variables to be passed to the layout
 * @param	string	Module folder name
 * @param	string	Subfolder in module. Deafult is the libraries
 * @return	string
 */
function generate_docs($class, $vars = array(), $module = 'fuel', $folder = 'libraries')
{
	$CI =& get_instance();
	return $CI->fuel->user_guide->generate_docs($class, $vars, $module, $folder);
}