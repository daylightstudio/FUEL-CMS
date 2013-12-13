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
 * Extends CI's array helper functions
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_array_helper
 */


// --------------------------------------------------------------------

/**
 * Array sorter that will sort on an array's key and allows for asc/desc order
 *
 * @access	public
 * @param	array
 * @param	string
 * @param	string
 * @param	boolean
 * @param	boolean
 * @return	array
 */
function array_sorter(&$array, $index, $order = 'asc', $nat_sort = FALSE, $case_sensitive = FALSE)
{
	if(is_array($array) && count($array) > 0)
	{
		foreach (array_keys($array) as $key)
		{
			$temp[$key]=$array[$key][$index];
			if (! $nat_sort)
			{
				($order == 'asc') ? asort($temp) : arsort($temp);
			} 
			else
			{
				($case_sensitive) ? natsort($temp) : natcasesort($temp);
			}
			if ($order != 'asc') $temp = array_reverse($temp,TRUE);
		}
		foreach(array_keys($temp) as $key)
		{
			(is_numeric($key)) ? $sorted[] = $array[$key] : $sorted[$key] = $array[$key];
		}
		return $sorted;
   }
	return $array;
}

// --------------------------------------------------------------------

/**
 * Array sorter that will sort an array of objects based on an objects 
 * property and allows for asc/desc order. Changes the original object
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	NULL
 */
function object_sorter(&$data, $key, $order = 'asc')
{
	for ($i = count($data) - 1; $i >= 0; $i--)
	{
		$swapped = false;
		for ($j = 0; $j < $i; $j++)
		{
			if ($order == 'desc')
			{
				if ($data[$j]->$key < $data[$j + 1]->$key)
				{ 
					$tmp = $data[$j];
					$data[$j] = $data[$j + 1];
					$data[$j + 1] = $tmp;
					$swapped = true;
				}
			}
			else
			{
				if ($data[$j]->$key > $data[$j + 1]->$key)
				{ 
					$tmp = $data[$j];
					$data[$j] = $data[$j + 1];
					$data[$j + 1] = $tmp;
					$swapped = true;
				}
				
			}
			
		}
		if (!$swapped) return;
	}
}

// --------------------------------------------------------------------

/**
 * Creates a key/value array based on an original array.
 *
 * Can be used in conjunction with the Form library class 
 * (e.g. $this->form->select('countries, option_list($options)))
 *
 * @access	public
 * @param	array
 * @param	string
 * @param	string
 * @param	boolean
 * @return	array
 */
function options_list($values, $value = 'id', $label = 'name', $value_as_key = FALSE)
{
	$return = array();
	foreach($values as $key => $val)
	{
		if (is_array($val))
		{
			if (is_object($val)) $val = get_object_vars($val);
			if (!empty($val[$label])) $return[$val[$value]] = $val[$label];
		}
		else if ($value_as_key)
		{
			$return[$val] = $val;
		}
		else
		{
			$return[$key] = $val;
		}
	}
	return $return;
}

// --------------------------------------------------------------------

/**
 * Parses a string in the format of key1="val1" key2="val2" into an array
 *
 * @access	public
 * @param	string
 * @return	array
 */
function parse_string_to_array($str)
{
	preg_match_all('#(\w+)=([\'"])(.*)\\2#U', $str, $matches);
	$params = array();
	foreach($matches[1] as $key => $val)
	{
		if (!empty($matches[3]))
		{
			$params[$val] = $matches[3][$key];
		}
	}
	return $params;
	
}

/**
 * Returns an array of arrays.
 *
 * @access	public
 * @param	array an array to be divided
 * @param	int number of groups to divide the array into
 * @return	array
 */	
function array_group($array, $groups)
{
	if (empty($array))
	{
		return array();
	}
	$items_in_each_group = ceil(count($array)/$groups);
	return array_chunk($array, $items_in_each_group);
}

/* End of file MY_array_helper.php */
/* Location: ./modules/fuel/helpers/MY_array_helper.php */