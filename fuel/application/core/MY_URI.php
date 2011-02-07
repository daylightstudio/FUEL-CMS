<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * A CodeIgniter MY URI Class Extension
 *
 * This class extends the original CodeIgniter URI class to allow you to remove
 * empty values from the segment when creating a uri from an array
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/my_uri
 */

class MY_URI  extends CI_URI {

	function MY_URI(){
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a URI string from an associative array. Added noemptys parameter
	 *
	 * @access	public
	 * @param	array	an associative array of key/values
	 * @param	boolean	indicates whether to remove empty array values from uri
	 * @return	array
	 */	
	function assoc_to_uri($array, $noemptys = FALSE)
	{	
		$temp = array();
		foreach ((array)$array as $key => $val)
		{
			if ($noemptys && $val == "" && !is_numeric($val)) {
				continue;
			}
			$temp[] = $key;
			$temp[] = $val;
		}
		
		return implode('/', $temp);
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the $_GET parameters
	 *
	 * @access	public
	 * @return	void
	 */	
	function init_get_params()
	{
		// borrowed from http://github.com/dhorrigan/codeigniter-query-string/blob/master/hooks/query_string.php
		$orig_query_string = $_SERVER['QUERY_STRING'];
		if (strpos($_SERVER['QUERY_STRING'], '?') !== FALSE)
		{
			if (strpos($_SERVER['QUERY_STRING'], '?') < strpos($_SERVER['QUERY_STRING'], '&'))
			{
				$orig_query_string = substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], '?') + 1);
				$_SERVER['QUERY_STRING'] = str_replace('?'.$orig_query_string, '', $_SERVER['QUERY_STRING']);
			}
		}
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		// $request_arr = explode('?', $_SERVER['REQUEST_URI']);
		// parse_str(end($request_arr), $_GET);
	}



}
// END URI Class
/* End of file URI.php */
/* Location: ./application/libraries/MY_URI.php */