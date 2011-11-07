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
 * An extension to the Unit_test class to correct line numbers and allow you 
 * to pass the test and expected information in the results template.
 * 
 * THIS IS NEEDED BY THE TESTER MODULE
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/MY_unit_test.html
 */

class MY_Unit_test extends CI_Unit_test{

	var $active			= TRUE;
	var $results 		= array();
	var $strict			= FALSE;
	var $_template 		= NULL;
	var $_template_rows	= NULL;

	// --------------------------------------------------------------------

	/**
	 * Generate a report
	 *
	 * Displays a table with the test data
	 *
	 * @access	public
	 * @return	string
	 */
	function report($result = array(), $format = FALSE)
	{
		if (count($result) == 0)
		{
			$result = $this->result();
		}

		$CI =& get_instance();
		$CI->load->language('unit_test');

		$this->_parse_template();

		$r = '';
		foreach ($result as $res)
		{
			$table = '';

			foreach ($res as $key => $val)
			{
				if ($key == $CI->lang->line('ut_result'))
				{
					if ($val == $CI->lang->line('ut_passed') AND $format)
					{
						$val = '<span style="color: #0C0;">'.$val.'</span>';
					}
					elseif ($val == $CI->lang->line('ut_failed') AND $format)
					{
						$val = '<span style="color: #C00;">'.$val.'</span>';
					}
				}

				$temp = $this->_template_rows;
				$temp = str_replace('{item}', $key, $temp);
				$temp = str_replace('{result}', $val, $temp);
				$table .= $temp;
			}

			$r .= str_replace('{rows}', $table, $this->_template);
		}

		return $r;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Run the tests
	 *
	 * Runs the supplied tests
	 *
	 * @access	public
	 * @param	mixed
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */	
	function run($test, $expected = TRUE, $test_name = 'undefined', $notes = '', $format = FALSE)
	{
		if ($this->active == FALSE)
		{
			return FALSE;
		}
	
		if (in_array($expected, array('is_string', 'is_bool', 'is_true', 'is_false', 'is_int', 'is_numeric', 'is_float', 'is_double', 'is_array', 'is_null'), TRUE))
		{
			$expected = str_replace('is_float', 'is_double', $expected);
			$result = ($expected($test)) ? TRUE : FALSE;	
			$extype = str_replace(array('true', 'false'), 'bool', str_replace('is_', '', $expected));
		}
		else
		{
			if ($this->strict == TRUE)
				$result = ($test === $expected) ? TRUE : FALSE;	
			else
				$result = ($test == $expected) ? TRUE : FALSE;	
			
			$extype = gettype($expected);
		}
				
		$back = $this->_backtrace();
	
		if (!is_string($test))
		{
			$test = var_export($test, TRUE);
		}

		if (!is_string($expected))
		{
			$expected = var_export($expected, TRUE);
		}
		
		if ($format)
		{
			$test_data = gettype($test).' <pre>'.htmlentities($test).'</pre>';
			$res_data = $extype.' <pre>'.htmlentities($expected).'</pre>';
		}
		else
		{
			$test_data = gettype($test).' - '.$test;
			$res_data = $extype.' - '.$expected;
		}
		$report[] = array (
							'test_name'			=> $test_name,
							'test_data'		=> $test_data,
							'res_data'		=> $res_data,
							'result'			=> ($result === TRUE) ? 'passed' : 'failed',
							'file'				=> $back['file'],
							'line'				=> $back['line'],
							'notes'				=> $notes
						);

		$this->results[] = $report;		
		
		return($this->report($this->result($report), $format));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generate a backtrace
	 *
	 * This lets us show file names and line numbers
	 *
	 * @access	private
	 * @return	array
	 */
	function _backtrace()
	{
		if (function_exists('debug_backtrace'))
		{
			$back = debug_backtrace();
			$index = (string) count($back) - 6; // {{{ FUEL Added to get proper line numbers}}}
			$file = ( ! isset($back[$index]['file'])) ? '' : $back[$index]['file'];
			$line = ( ! isset($back[$index]['line'])) ? '' : $back[$index]['line'];
						
			return array('file' => $file, 'line' => $line);
		}
		return array('file' => 'Unknown', 'line' => 'Unknown');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Resets the report
	 *
	 * @access	public
	 * @return	void
	 */
	function reset()
	{
		$this->results = array();
	}

}
// END MY_Unit_test Class



/* End of file MY_Unit_test.php */
/* Location: ./application/libraries/MY_Unit_test.php */