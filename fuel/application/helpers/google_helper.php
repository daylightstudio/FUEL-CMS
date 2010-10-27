<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
http://codeigniter.com/forums/viewthread/56515/
* CodeIgniter
*
* An open source application development framework for PHP 4.3.2 or newer
*
* @package        CodeIgniter
* @author        Rick Ellis
* @copyright    Copyright (c) 2006, EllisLab, Inc.
* @license        http://www.codeignitor.com/user_guide/license.html
* @link        http://www.codeigniter.com
* @since        Version 1.0
* @filesource
*/

// ------------------------------------------------------------------------

/**
* CodeIgniter GOOGLE Helpers
*
* @package        CodeIgniter
* @subpackage    Helpers
* @category    Helpers
* @author        Todd Perkins with recommendation from Code Arachn!d
* @link        http://www.undecisive.com
*/

// ------------------------------------------------------------------------

/**
* Google Analytics
*
* Inserts google analytics tracking code into view
* If a tracking code is passed in, then it will use that uacct info
* Otherwise, it will use the value defined in the google.php config file
* If both values do not exist, nothing will be inserted.
*
* @access    public
* @param    string
* @return    string
*/
function google_analytics($uacct = '')
{
	$CI =& get_instance();
	if (empty($uacct)) $uacct = $CI->config->item('google_uacct');
	if (!empty($uacct))
	{
		$google_analytics_code = '
			<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push([\'_setAccount\', \''.$uacct.'\']);
			  _gaq.push([\'_trackPageview\']);

			  (function() {
			    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
			    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
			    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			</script>
		';
		return $google_analytics_code;
	}
	else
	{
		return FALSE;
	}
    
}
/* End of file google_helper.php */
/* Location: ./application/helpers/google_helper.php */
