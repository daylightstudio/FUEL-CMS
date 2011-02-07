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
 * Gets the directory file info.
 *
 * @access	public
 * @param 	string
 * @param 	boolean
 * @param 	boolean
 * @return	array
 */
function get_dir_file_info($source_dir, $include_path = FALSE, $_recursion = FALSE)
{
	static $_filedata = array();
	static $orig_directory;
	if (!isset($orig_directory)) $orig_directory = $source_dir;

	$relative_path = $source_dir;

	if ($fp = @opendir($source_dir))
	{
		// reset the array and make sure $source_dir has a trailing slash on the initial call
		if ($_recursion === FALSE)
		{
			$_filedata = array();
			$source_dir = str_replace("\\", "/", rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
		}

		while (FALSE !== ($file = readdir($fp)))
		{
			if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
			{
				 get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
			}
			elseif (strncmp($file, '.', 1) !== 0)
			{
				$fileinfo = get_file_info($source_dir.$file);
				$file_prefix = ($include_path) ? substr($source_dir, strlen($orig_directory) - 1) : $source_dir;
				if (!empty($file_prefix))
				{
					$file = $file_prefix."/".$file;
					$file = str_replace(array("//", "\\/"), array("/", "/"), $file); // replace double slash
					if (substr($file, 0, 1) == '/')
					{
						$file = substr($file, 1);
					}
				}

				$_filedata[$file] = $fileinfo;
				$_filedata[$file]['relative_path'] = $relative_path;
			}
		}
		return $_filedata;
	}
	else
	{
		return FALSE;
	}
}


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
