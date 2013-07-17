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
 * FUEL Format Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/format_helper
 */



// ------------------------------------------------------------------------

/**
 * Formats value into a currency string
 *
 * @access	public
 * @param	string
 * @param	bool	whether to include the cents or not
 * @return	string
 */
function currency($value, $symbol = '$',  $include_cents = TRUE, $decimal_sep = '.', $thousands_sep = ',')
{
	if (!isset($value) OR $value === "")
	{
		return;
	}
	$value = (float) $value;
	$dec_num = (!$include_cents) ? 0 : 2;
	return $symbol.number_format($value, $dec_num, $decimal_sep, $thousands_sep);
}

/* End of file format_helper.php */
/* Location: ./modules/fuel/helpers/format_helper.php */
