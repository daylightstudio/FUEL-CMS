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
 * Extends CI's array helper functions
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_array_helper
 */

// --------------------------------------------------------------------

if ( ! function_exists('array_orderby'))
{
	// http://php.net/manual/en/function.array-multisort.php
	/**
	 * An alternative array sorter that is a bit faster then array_sorter
	 *
	 * @access	public
	 * @param	array	The array of data
	 * @param	string	The column to sort by
	 * @param	string	The direction (asc/desc)
	 * @return	array
	 */
	function array_orderby()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
				}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('array_sorter'))
{
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
}

// --------------------------------------------------------------------

if ( ! function_exists('object_sorter'))
{
	/**
	 * Array sorter that will sort an array of objects based on an objects
	 * property and allows for asc/desc order. Changes the original object
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	void
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
}

// --------------------------------------------------------------------

if ( ! function_exists('options_list'))
{
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
}

// --------------------------------------------------------------------

if ( ! function_exists('parse_string_to_array'))
{
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
}

// --------------------------------------------------------------------

if ( ! function_exists('array_group'))
{
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
}

// --------------------------------------------------------------------

if ( ! function_exists('csv_to_array'))
{
	/**
	 * Converts a .csv file to an associative array. Must have header row.
	 *
	 * @access	public
	 * @param	string	file name
	 * @param	string	the delimiter that separates each column
	 * @param	int		the index for where the header row starts
	 * @param	int		must be greater then the maximum line length. Setting to 0 is slightly slower, but works for any length
	 * @return	array|false
	 */
	function csv_to_array($filename = '', $delimiter =  ',', $header_row = 0, $length = 0)
	{
		if(!file_exists($filename) || !is_readable($filename))
		{
			return FALSE;
		}

		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE)
		{
			$i = -1;
			while (($row = fgetcsv($handle, $length, $delimiter)) !== FALSE)
			{
				$i++;
				if ($i >= $header_row) {
					if(!$header)
					{
						$header = $row;
					}
					else
					{
						$data[] = array_combine($header, $row);
					}
				}
			}
			fclose($handle);
		}
		return $data;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('array_get'))
{
	/**
	 * Return the value from an associative array or an object.
	 * credit: borrowed from Vanilla forums GetValueR function
	 *
	 * @access	public
	 * @param	mixed	$array		The array or object to search.
	 * @param	string	$key		The key or property name of the value.
	 * @param	mixed	$default	The value to return if the key does not exist.
	 * @return	mixed				The value from the array or object.
	 */
	function array_get($array, $key, $default = FALSE)
	{
		$path = explode('.', $key);

		$value = $array;
		for ($i = 0; $i < count($path); ++$i)
		{
			$sub_key = $path[$i];

			if (is_array($value) AND isset($value[$sub_key]))
			{
				$value = $value[$sub_key];
			}
			elseif (is_object($value) AND isset($value->$sub_key))
			{
				$value = $value->$sub_key;
			}
			else
			{
				return $default;
			}
		}
		return $value;
	}
}

/* End of file MY_array_helper.php */
/* Location: ./modules/fuel/helpers/MY_array_helper.php */