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

if (!function_exists('currency'))
{
	/**
	 * Formats value into a currency string
	 *
	 * @access	public
	 * @param	string	value to format
	 * @param	string	currency symbol
	 * @param	bool	whether to include the cents or not
	 * @param	string	decimal separator
	 * @param	string	thousands separator
	 * @return	string
	 */
	function currency($value, $symbol = '$',  $include_cents = TRUE, $decimal_sep = '.', $thousands_sep = ',')
	{
		$value = (float) $value;
		$dec_num = (!$include_cents) ? 0 : 2;
		$is_negative = (strpos($value, '-') === 0) ? '-' : '';
		$value = abs($value);
		return $is_negative.$symbol.number_format($value, $dec_num, $decimal_sep, $thousands_sep);
	}
}

/* End of file format_helper.php */
/* Location: ./modules/fuel/helpers/format_helper.php */
