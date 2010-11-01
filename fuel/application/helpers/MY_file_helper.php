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
 * @copyright	Copyright (c) 2010, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL File Helper
 *
 * Overwrites CI's file helper
 * 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/helpers/file_helper
 */


// --------------------------------------------------------------------

/**
 * Deletes files in a directory with the added option to exclude certain files
 *
 * @access	public
 * @param 	string
 * @param 	boolean
 * @param 	mixed
 * @param 	int
 * @return	void
 */
function delete_files($path, $del_dir = FALSE, $exclude = NULL, $level = 0)
{	
	// Trim the trailing slash
	$path = preg_replace("|^(.+?)/*$|", "\\1", $path);
	
	if ( ! $current_dir = @opendir($path))
		return;

	while(FALSE !== ($filename = @readdir($current_dir)))
	{
		if ($filename != "." and $filename != ".." && 
			(is_null($exclude) || (is_array($exclude) && !in_array($filename, $exclude)) || (is_string($exclude) && !preg_match($exclude, $filename))))
		{
			if (is_dir($path.'/'.$filename))
			{
				// Ignore empty folders
				if (substr($filename, 0, 1) != '.')
				{
					delete_files($path.'/'.$filename, $del_dir, $exclude, $level + 1);
				}
			}
			else
			{
				unlink($path.'/'.$filename);
			}
		}
	}
	@closedir($current_dir);

	if ($del_dir == TRUE AND $level > 0)
	{
		@rmdir($path);
	}
}

function is_image_file($path)
{
	if (preg_match("/(.)+\\.(jp(e){0,1}g$|gif$|png$)/i",$path))
	{
		return TRUE;
	}
	return FALSE;
}

/* End of file MY_file_helper.php */
/* Location: ./application/helpers/MY_file_helper.php */
