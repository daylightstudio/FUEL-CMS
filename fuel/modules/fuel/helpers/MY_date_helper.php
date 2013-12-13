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
 * FUEL Date Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_date_helper
 */


// --------------------------------------------------------------------

/**
 * Returns the date into a specified format. Will look at the configuration
 *
 * @access	public'
 * @param	string
 * @param	mixed
 * @return	string
 */
function date_formatter($date, $format = FALSE){
	$date_ts = strtotime($date);
	$CI = get_instance();
	if ($format === FALSE AND $CI->config->item('date_format_verbose'))
	{
		$format = $CI->config->item('date_format');
	}
	else if (($format === TRUE OR $format === 'verbose') AND $CI->config->item('date_format_verbose'))
	{
		$format = $CI->config->item('date_format_verbose');
	}
	else if ($format == 'time')
	{
		$format =  $CI->config->item('date_format_verbose').' '. $CI->config->item('time_format');
	}
	else if (!is_string($format))
	{
		$format = 'm/d/Y';
	}
	return date($format, $date_ts);
}


// --------------------------------------------------------------------

/**
 * Returns the current datetime value in MySQL format
 *
 * @access	public
 * @param	boolean
 * @return	string
 */
function datetime_now($hms = TRUE){
	if ($hms)
	{
		return date("Y-m-d H:i:s");
	}
	else
	{
		return date("Y-m-d");
	}
}

// --------------------------------------------------------------------

/**
 * Test for common date format
 *
 * @access	public
 * @param	string
 * @return	boolean
 */
function is_date_format($date)
{
	return (is_string($date) AND (!empty($date) AND (int)$date != 0) AND 
	(is_date_english_format($date) OR is_date_db_format($date)));
}

// --------------------------------------------------------------------

/**
 * Test for MySQL date format
 *
 * @access	public
 * @param	string
 * @return	boolean
 */
function is_date_db_format($date)
{
	return preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})#", $date);
}

// --------------------------------------------------------------------

/**
 * Test for mm/dd/yyyy format
 *
 * @access	public
 * @param	string
 * @return	boolean
 */
function is_date_english_format($date)
{
	return preg_match("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})#", $date) OR preg_match("#([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})#", $date);
}

// --------------------------------------------------------------------

/**
 * Returns date in mm/dd/yyy format by default
 * Can be configured with a date_format config value
 *
 * @access	public
 * @param	string
 * @param	boolean
 * @param	string
 * @param	string
 * @return	string
 */
function english_date($date, $long = FALSE, $timezone = NULL, $delimiter = '/')
{
	if (!is_numeric($date) AND !preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs))
	{
		return FALSE;
	}
	if (!empty($date))
	{
		$date_ts = (!is_numeric($date)) ? strtotime($date) : $date;
		if (strtolower($timezone) == 'auto')
		{
			$timezone = date('e');
		}
		if (!$long)
		{
			return date("m".$delimiter."d".$delimiter."Y", $date_ts).' '.$timezone;
		}
		else
		{
			return date("m".$delimiter."d".$delimiter."Y h:i a", $date_ts).' '.$timezone;
		}
	} else {
		return FALSE;
	}
}

// --------------------------------------------------------------------

/**
 * Returns date in 'verbose' (e.g. Jan. 1, 2010) format
 * Can be configured with a date_format_verbose config value
 *
 * @access	public
 * @param	string
 * @return	boolean
 */
function english_date_verbose($date)
{
	$date_ts = (!is_numeric($date)) ? strtotime($date) : $date;
	if (!empty($date))
	{
		return date("M. d, Y", $date_ts);
	}
	else
	{
		return FALSE;
	}
}


// --------------------------------------------------------------------

/**
 * Returns the time into a verbose format (e.g. 12hrs 10mins 10secs)
 *
 * must be passed a string in hh:mm format
 *
 * @access	public
 * @param	string
 * @param	boolean
 * @return	boolean
 */
function time_verbose($time, $include_seconds = FALSE)
{
	if (is_date_format($time))
	{
		$time = strtotime($time);
	}
	if (is_int($time))
	{
		$time = date('H:i:s', $time);
	}

	$hms = explode(':', $time);
	if (empty($hms)) return $time;
	$h = (int) $hms[0];
	$m = (!empty($hms[1])) ? (int) $hms[1] : 0;
	$s = (!empty($hms[2])) ? (int) $hms[2] : 0;
	$new_time = '';
	if ($h != 0) $new_time .= $h.'hrs ';
	if ($m != 0) $new_time .= $m.'mins ';
	if ($include_seconds AND $s != 0) $new_time .= $s.'secs';
	return $new_time;
}

// --------------------------------------------------------------------

/**
 * Converts a date from english (e.g. mm/dd/yyyy) to db format (e.g yyyy-mm-dd)
 *
 * @access	public
 * @param	string
 * @param	int
 * @param	int
 * @param	int
 * @param	string
 * @return	string
 */
function english_date_to_db_format($date, $hour = 0, $min = 0, $sec = 0, $ampm = 'am')
{
	$hour = (int) $hour;
	$min = (int) $min;
	$sec = (int) $sec;
	if ($hour > 12) $ampm = 'pm';
	if ($ampm == 'pm' AND $hour < 12)
	{
		$hour += 12;
	}
	else if ($ampm == 'am' AND $hour == 12)
	{
		$hour = 0;
	}
	$date_arr = preg_split('#-|/#', $date);
	
	if (count($date_arr) != 3) return 'invalid';
	
	// convert them all to integers
	foreach($date_arr as $key => $val)
	{
		$date_arr[$key] = (int) $date_arr[$key]; // convert to integer
	}
	
	$new_date = '';
	
	if (preg_match("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})#", $date))
	{
		if (!checkdate($date_arr[0], $date_arr[1], $date_arr[2]))
		{
			return 'invalid'; // null will cause it to be ignored in validation
		}
		$new_date = $date_arr[2].'-'.$date_arr[0].'-'.$date_arr[1].' '.$hour.':'.$min.':'.$sec;
	}
	else if (preg_match("#([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})#", $date))
	{
		if (!checkdate($date_arr[1], $date_arr[0], $date_arr[2]))
		{
			return 'invalid'; // null will cause it to be ignored in validation
		}
		$new_date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0].' '.$hour.':'.$min.':'.$sec;
	}
	else
	{
		return 'invalid';
	}
	$date = date("Y-m-d H:i:s", strtotime($new_date)); // normalize
	return $date;
}

// --------------------------------------------------------------------

/**
 * Formats a date into yyyy-mm-dd hh:mm:ss format
 *
 * @access	public
 * @param	int
 * @param	int
 * @param	int
 * @param	int
 * @param	int
 * @param	int
 * @return	string
 */
// formats a date into a mysql date
function format_db_date($y = NULL, $m = NULL, $d = NULL, $h = NULL, $i = NULL, $s = NULL) {
	if (empty($m) AND !empty($y))
	{
		$dates = convert_date_to_array($y);
		$str = $dates['year'].'-'.$dates['month'].'-'.$dates['day'].' '.$dates['hour'].':'.$dates['min'].':'.$dates['sec'];
	}
	else
	{
		if (empty($y))
		{
			return date("Y-m-d H:i:s");
		}
		$time = time();
		$y = is_numeric($y) ? $y : date('Y', $time);
		$m = is_numeric($m) ? $m : date('m', $time);
		$m = sprintf("%02s",  $m);
		$d = is_numeric($d) ? $d : date('d', $time);
		$d = sprintf("%02s",  $d);
		$str = $y.'-'.$m.'-'.$d;
		if (isset($h)) {
			$h = is_numeric($h) ? $h : date('H', $time);
			$i = is_numeric($i) ? $i : date('i', $time);
			$s = is_numeric($s) ? $s : date('s', $time);
			$h = sprintf("%02s",  $h);
			$i = sprintf("%02s",  $i);
			$s = sprintf("%02s",  $s);
			$str .= ' '.$h.':'.$i.':'.$s;
		}
	}
	return $str;
}

// --------------------------------------------------------------------

/**
 * Creates a date range string (e.g. January 1-10, 2010)
 *
 * @access	public
 * @param	string start date
 * @param	string end date
 * @param	array formatting parameters
 * @return	string
 */
function date_range_string($date1, $date2, $params = array())
{
	
	// set formatting defaults
	$format['same_day_and_time'] = 'F j, Y h:i a';
	$format['same_day'] = array('F j, h:i a', 'h:i a');
	$format['same_month'] = array('F j', 'j, Y');
	$format['same_year'] = array('F j', 'F j, Y');
	$format['default'] = 'F j, Y';
	$format['joiner'] = '-';

	$format = array_merge($format, $params);

	$date1TS = (is_string($date1)) ? strtotime($date1) : $date1;
	if (is_null($date2) OR (int) $date2 == 0)
	{
		$date2TS = $date1TS;
	}
	else
	{
		$date2TS = (is_string($date2)) ? strtotime($date2) : $date2;
	}

	// same day
	if (date('Y-m-d', $date1TS) == date('Y-m-d', $date2TS) OR (int) $date2 == 0)
	{
		// same day but different time
		if (date('H:i', $date1TS) != date('H:i', $date2TS))
		{
			return date($format['same_day'][0], $date1TS).$format['joiner'].date($format['same_day'][1], $date2TS);
		}

		// same day and time format
		return date($format['same_day_and_time'], $date1TS);
	}
	// same month
	else if (date('m/Y', $date1TS) == date('m/Y', $date2TS))
	{
		return date($format['same_month'][0], $date1TS).$format['joiner'].date($format['same_month'][1], $date2TS);
	}
	// same year
	else if (date('Y', $date1TS) == date('Y', $date2TS))
	{
		return date($format['same_year'][0], $date1TS).$format['joiner'].date($format['same_year'][1], $date2TS);
	}

	// default
	else
	{
		return date($format['default'], $date1TS).$format['joiner'].date($format['default'], $date2TS);
	}
}

// --------------------------------------------------------------------

/**
 * Creates a string based on how long from the current time the date provided.
 * 
 * (e.g. 10 minutes ago)
 *
 * @access	public
 * @param	string
 * @param	booelan
 * @return	string
 */
function pretty_date($timestamp, $use_gmt = FALSE)
{
	if (is_string($timestamp))
	{
		$timestamp = strtotime($timestamp);
	}
	$now = ($use_gmt) ? mktime() : time();
	$diff = $now - $timestamp;
	$day_diff = floor($diff/86400);
	
	// don't go beyond '
	if ($day_diff < 0)
	{
		return;
	}
	
	if ($diff < 60)
	{
		return 'just now';
	}
	else if ($diff < 120)
	{
		return '1 minute ago';
	}
	else if ($diff < 3600)
	{
		return floor( $diff / 60 ).' minutes ago';
	}
	else if ($diff < 7200)
	{
		return '1 hour ago';
	}	
	else if ($diff < 86400)
	{
		return floor( $diff / 3600 ).' hours ago';
	}
	else if ($day_diff == 1)
	{
		return 'Yesterday';
	}
	else if ($day_diff < 7)
	{
		return $day_diff ." days ago";
	}
	else
	{
		return ceil($day_diff / 7 ).' weeks ago';
	}
	
}

// --------------------------------------------------------------------

/**
 * Calculate the age between 2 dates
 *
 * @access	public
 * @param	int
 * @param	int
 * @return	string
 */
function get_age($bday_ts, $at_time_ts = NULL)  
{ 
	if (empty($at_time_ts)) $at_time_ts = time();
	if (is_string($bday_ts)) $bday_ts = strtotime($bday_ts);
	
	// See http://php.net/date for what the first arguments mean. 
	$diff_year  = date('Y', $at_time_ts) - date('Y', $bday_ts); 
	$diff_month = date('n', $at_time_ts) - date('n', $bday_ts); 
	$diff_day   = date('j', $at_time_ts) - date('j', $bday_ts); 

	// If birthday has not happened yet for this year, subtract 1. 
	if ($diff_month < 0 OR ($diff_month == 0 AND $diff_day < 0)) 
	{ 
	    $diff_year--; 
	} 
    
	return $diff_year; 
}

// ------------------------------------------------------------------------

/**
 * Standard Date.. OVERWRITE CI version due to bugs
 *
 * Returns a date formatted according to the submitted standard.
 * http://codeigniter.com/forums/viewthread/171906/
 *
 * @access	public
 * @param	string	the chosen format
 * @param	int	Unix timestamp
 * @return	string
 */
function standard_date($fmt = 'DATE_RFC822', $time = '')
{
	$formats = array(
					'DATE_ATOM'		=>	'%Y-%m-%dT%H:%i:%s%P',
					'DATE_COOKIE'	=>	'%l, %d-%M-%y %H:%i:%s UTC',
					'DATE_ISO8601'	=>	'%Y-%m-%dT%H:%i:%s%P',
					'DATE_RFC822'	=>	'%D, %d %M %y %H:%i:%s %O',
					'DATE_RFC850'	=>	'%l, %d-%M-%y %H:%m:%i UTC',
					'DATE_RFC1036'	=>	'%D, %d %M %y %H:%i:%s %O',
					'DATE_RFC1123'	=>	'%D, %d %M %Y %H:%i:%s %O',
					'DATE_RSS'		=>	'%D, %d %M %Y %H:%i:%s %O',
					'DATE_W3C'		=>	'%Y-%m-%dT%H:%i:%s%P'
					);

	if ( ! isset($formats[$fmt]))
	{
		return FALSE;
	}

	return mdate($formats[$fmt], $time);
}

// --------------------------------------------------------------------

/**
 * Returns a timestamp from the provided time
 *
 * @access	public
 * @param	string date (optional)
 * @return	string
 */
function timestamp($date = NULL)
{
	if (empty($date))
	{
		return time();
	}

	return strtotime($date);
}

// --------------------------------------------------------------------

/**
 * Returns a the month value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'm/numeric', 'F/long', 'M/short' <- default  (optional)
 * @return	string
 */
function month($date = NULL, $format = 'M')
{
	$ts = timestamp($date);
	switch($format)
	{
		case 'm':
			return date('m', $ts);
		case 'n': case 'numeric':
			return date('n', $ts);
		case 'M': case 'short':
			return date('M', $ts);
		default:
			return date('F', $ts);
	}
}

// --------------------------------------------------------------------

/**
 * Returns a the day value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'd/leading', 'j' <- default  (optional)
 * @return	string
 */
function day($date = NULL, $format = 'j')
{
	switch($format)
	{
		case 'd': case 'leading':
			return date('d', timestamp($date));
		default:
			return date('j',timestamp($date));
	}
}

// --------------------------------------------------------------------

/**
 * Returns a the weekday value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'l/full', 'N/numeric', 'D' <- default (optional)
 * @return	string
 */
function weekday($date = NULL, $format = 'D')
{
	$ts = timestamp($date);
	switch($format)
	{
		case 'l': case 'full':
			return date('l', $ts);
		case 'N': case 'numeric':
			return date('N', $ts);
		default:
			return date('D', $ts);
	}
}

// --------------------------------------------------------------------

/**
 * Returns a the year value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'y/short', 'Y/long' <- default (optional)
 * @return	string
 */
function year($date = NULL, $format = 'Y')
{
	$ts = timestamp($date);
	switch($format)
	{
		case 'y': case 'short':
			return date('y', $ts);
		default:
			return date('Y', $ts);
	}
}

// --------------------------------------------------------------------

/**
 * Returns a the weekday value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are '24/military', '12' <- default (optional)
 * @return	string
 */
function hour($date = NULL, $format = '12')
{
	$ts = timestamp($date);
	switch($format)
	{
		case '24':  case 'military':
			return date('H', $ts);
		default:
			return date('h', $ts);
	}
}

// --------------------------------------------------------------------

/**
 * Returns a the weekday value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'noleading', 'leading' <- default (optional)
  * @return	string
 */
function minute($date = NULL, $format = 'leading')
{
	$min = date('i', timestamp($date));
	if ($format != 'leading')
	{
		return (int) $min;
	}
	return $min;
}

// --------------------------------------------------------------------

/**
 * Returns a the weekday value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'noleading', 'leading' <- default (optional)
 * @return	string
 */
function second($date = NULL, $format = 'leading')
{
	$sec = date('s', timestamp($date));
	if ($format != 'leading')
	{
		return (int) $sec;
	}
	return $sec;
}

// --------------------------------------------------------------------

/**
 * Returns a the ampm value of a provided date
 *
 * @access	public
 * @param	string date (optional)
 * @param	string options are 'A/upper/uppercase', 'a/lower/lowercase' <- default (optional)
 * @return	string
 */
function ampm($date = NULL, $format = 'a')
{
	$ts = timestamp($date);
	switch($format)
	{
		case 'A':  case 'upper':  case 'uppercase':
			return date('A', $ts);
		default:
			return date('a', $ts);
	}
}

// --------------------------------------------------------------------

/**
 * Determines whether the time is midnight or not. Helps with dates that are set without time values
 *
 * @access	public
 * @param	string date
 * @return	string
 */
function is_midnight($date)
{
	$ts = timestamp($date);
	return (date('H:i:s', $ts) == '00:00:00');
}


/* End of file MY_date_helper.php */
/* Location: ./modules/fuel/helpers/MY_date_helper.php */