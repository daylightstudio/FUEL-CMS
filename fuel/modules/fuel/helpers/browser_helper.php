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
 * FUEL Browser Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/browser_helper
 */


// --------------------------------------------------------------------

/**
 * Returns an array of browser information
 * Originally from: http://us3.php.net/function.get-browser
 * 
 * @access	public
 * @param	string	browser agant (optional)
 * @return	array
 */	
function browser_info($agent = NULL)
{
 	// Declare known browsers to look for
	$known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko');

	// Clean up agent and build regex that matches phrases for known browsers
	// (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
	// version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
	$agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
	$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';

	// Find all phrases (or return empty array if none found)
	if (!preg_match_all($pattern, $agent, $matches)) return array();

	// Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
	// Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
	// in the UA).  That's usually the most correct.
	$i = count($matches['browser'])-1;
	return array($matches['browser'][$i] => $matches['version'][$i]);
}

/* End of file browser_helper.php */
/* Location: ./modules/fuel/helpers/browser_helper.php */
