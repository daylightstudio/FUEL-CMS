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
 */

// ------------------------------------------------------------------------

/**
 * Compatibility Helper
 *
 * Compatibility functions helpful for slightly older versions of PHP.
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/compatibility_helper
 */

// --------------------------------------------------------------------

if(!function_exists('str_putcsv'))
{
	/**
	 * Not really a compatibility function since it doesn't exist natively in PHP.
	 * However it probably should so we provide it here.
	 * A version of the original function found here: http://glossword.googlecode.com/svn-history/r600/trunk/core/gw_includes/functions.php
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	function str_putcsv($input, $delimiter = ',', $enclosure = '"')
	{
		// Open a memory "file" for read/write...
		$fp = fopen('php://temp', 'r+');
		// ... write the $input array to the "file" using fputcsv()...
		fputcsv($fp, $input, $delimiter, $enclosure);
		// ... rewind the "file" so we can read what we just wrote...
		rewind($fp);
		// ... read the entire line into a variable...
		$data = fgets($fp);
		// ... close the "file"...
		fclose($fp);
		// ... and return the $data to the caller, with the trailing newline from fgets() removed.
		return rtrim( $data, "\n" );
	}
}

/* End of file compatibility_helper.php */
/* Location: ./modules/fuel/helpers/compatibility_helper.php */
