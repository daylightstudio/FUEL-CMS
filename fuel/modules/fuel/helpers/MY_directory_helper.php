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
 * FUEL Directory Helper
 *
 * Adds some extra functions to CI's directory helper
 * 
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_directory_helper
 */


// --------------------------------------------------------------------

/**
 * Recursively copies from one directory to another
 *
 * @access	public
 * @param 	string
 * @param 	string
 * @return	array
 */
function copyr($source, $dest)
{
	// Simple copy for a file
	if (is_file($source))
	{
		return copy($source, $dest);
	}
 
	// Make destination directory
	if (!is_dir($dest))
	{
		mkdir($dest);
	}
	
	// If the source is a symlink
	if (is_link($source))
	{
		$link_dest = readlink($source);
		return symlink($link_dest, $dest);
	}
 
	// Loop through the folder
	$dir = dir($source);
	if (!is_object($dir)) return FALSE;
	
	while (false !== $entry = $dir->read())
	{
		// Skip pointers
		if ($entry == '.' OR $entry == '..')
		{
			continue;
		}

		// Deep copy directories
		if ($dest !== "$source/$entry")
		{
			copyr("$source/$entry", "$dest/$entry");
		}
	}

	// Clean up
	$dir->close();
	return true;
}

// --------------------------------------------------------------------

/**
 * Recursively changes the permissions of a folder structure
 *
 *  from php.net/chmod
 * @access	public
 * @param 	string
 * @param 	octal
 * @return	boolean
 */
function chmodr($path, $filemode) { 
	if (!is_dir($path))
	{
		return chmod($path, $filemode); 
	}

	$dh = opendir($path); 
	while (($file = readdir($dh)) !== false)
	{ 
		if($file != '.' AND $file != '..')
		{ 
			$fullpath = $path.'/'.$file; 
			if(is_link($fullpath))
			{
				return FALSE; 
			}
			elseif(!is_dir($fullpath) AND !chmod($fullpath, $filemode))
			{
				return FALSE; 
			}
			elseif(!chmodr($fullpath, $filemode))
			{
				return FALSE; 
			}
		} 
	} 

	closedir($dh); 

	if(chmod($path, $filemode))
	{
		return TRUE; 
	}
	else
	{
		return FALSE; 
	}
}

// --------------------------------------------------------------------

/**
 * Returns an array of file names from a directory
 *
 * @access	public
 * @param 	string
 * @param 	boolean
 * @param 	mixed
 * @param 	boolean
 * @return	array
 */
function directory_to_array($directory, $recursive = TRUE, $exclude = array(), $append_path = TRUE, $no_ext = FALSE, $_first_time = TRUE)
{
	static $orig_directory;
	if ($_first_time) $orig_directory = $directory;
	$array_items = array();
	if ($handle = @opendir($directory)) {
		while (false !== ($file = readdir($handle)))
		{
			if (strncmp($file, '.', 1) !== 0  AND 
				(empty($exclude) OR (is_array($exclude) AND !in_array($file, $exclude))OR (is_string($exclude) AND !preg_match($exclude, $file)))
				)
			{
				if (is_dir($directory. "/" . $file))
				{
					if ($recursive)
					{
						$array_items = array_merge($array_items, directory_to_array($directory."/". $file, $recursive, $exclude, $append_path, $no_ext, FALSE));
					}
				}
				else
				{
					if ($no_ext)
					{
						$period_pos = strrpos($file, '.');
						if ($period_pos) $file = substr($file, 0, $period_pos);
					}
					$file_prefix = (!$append_path) ? substr($directory, strlen($orig_directory)) : $directory;
					$file = $file_prefix."/".$file;
					$file = str_replace("//", "/", $file); // replace double slash
					if (substr($file, 0, 1) == '/') $file = substr($file, 1); // remove begining slash
					if (!empty($file) AND !in_array($file, $array_items)) $array_items[] = $file;
				}
				
			}
		}
		closedir($handle);
	}
	return $array_items;
}

// --------------------------------------------------------------------

/**
 * Lists the directories only from a give directory
 *
 * @access	public
 * @param 	string
 * @param 	mixed
 * @param 	boolean
 * @param 	boolean
 * @param 	boolean
 * @param 	boolean
 * @return	array
 */
function list_directories($directory, $exclude = array(), $full_path = TRUE, $is_writable = FALSE, $recursive = TRUE, $_first_time = TRUE)
{
	static $orig_directory;
	static $dirs;
	if ($_first_time)
	{
		$orig_directory = rtrim($directory, '/');
		$dirs = NULL;
	}

	if ($handle = opendir($directory)) 
	{
		while (FALSE !== ($file = readdir($handle))) 
		{
			if (strncmp($file, '.', 1) !== 0  AND 
				((is_array($exclude) AND !in_array($file, $exclude)) OR (is_string($exclude) AND !empty($exclude) AND !preg_match($exclude, $file)))
				)
			{
				$file_path = $directory. "/" . $file;

				if (is_dir($file_path))
				{
					if ($is_writable AND !is_writable($file_path)) 
					{
						continue;
					}
					if (!$full_path)
					{
						$dir_prefix = substr($directory, strlen($orig_directory));
						$dir = trim($dir_prefix."/".$file, '/');
					}
					else
					{
						$dir_prefix = $directory;
						$dir = $dir_prefix."/".$file;
					}

					$dir = str_replace("//", "/", $dir); // replace double slash


					if (!isset($dirs))
					{
						$dirs = array();
					}
					if (!empty($dir) AND !in_array($dir, $dirs)) 
					{
						$dirs[] = $dir;
					}
					if ($recursive)
					{
						list_directories($file_path, $exclude, $full_path, $is_writable, TRUE, FALSE);	
					}
					
				}
				
			}
		}
		closedir($handle);
	}
	return $dirs;
}

/* End of file MY_directory_helper.php */
/* Location: ./modules/fuel/helpers/MY_directory_helper.php */