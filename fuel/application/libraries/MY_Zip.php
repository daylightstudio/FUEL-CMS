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
 */

// ------------------------------------------------------------------------

/**
 * A CodeIgniter Zip extension library
 *
 * This Library overides the original CI's read_dir method
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/my_zip.html
 */

class MY_Zip extends CI_Zip {
	
	function MY_Zip()
	{
		parent::__construct();
	}

	// added $orig_path to prevent deep file structures
    function read_dir($path, $orig_path = NULL)
    {
		if (empty($orig_path)) $orig_path = $path;
		
		if ($fp = @opendir($path))
		{
			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($path.$file) && substr($file, 0, 1) != '.')
				{
					$this->read_dir($path.$file."/", $orig_path);
				}
				elseif (substr($file, 0, 1) != ".")
				{
					if (FALSE !== ($data = file_get_contents($path.$file)))
					{
						$filepath = substr($path, strlen($orig_path));
						$this->add_data($filepath.$file, $data);
					}
				}
			}
			return TRUE;
		}
	}
}