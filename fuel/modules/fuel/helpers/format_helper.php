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
 * FUEL Format Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/helpers/format_helper
 */



// ------------------------------------------------------------------------

/**
 * Formats value into a dollar string
 *
 * @access	public
 * @param	string
 * @param	bool	whether to include the cents or not
 * @return	string
 */
function dollar($value, $include_cents = TRUE)
{
	if (!$include_cents)
	{
		return "$".number_format($value);
	}
	else
	{
		return "$".number_format($value, 2, '.', ',');
	}
}

/* End of file format_helper.php */
/* Location: ./application/helpers/format_helper.php */
