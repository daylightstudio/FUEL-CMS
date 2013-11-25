<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/codeigniter-dwoo
 */

// Modified by David McReynolds @ Daylight Studio to automatically create the compile directory if it doesn't exist 9/16/10'

include(FUEL_PATH . 'libraries/dwoo/dwooAutoload.php');

class MY_Parser extends CI_Parser {

	private $_ci;
	private $_dwoo;
	private $_parser_compile_dir = '';
	private $_parser_cache_dir = '';
	private $_parser_cache_time = 0;
	private $_parser_allow_php_tags = array();
	private $_parser_allowed_php_functions = array();
	private $_parser_assign_refs = array();

	public function __construct($config = array())
	{
		if (!empty($config))
		{
			$this->initialize($config);
		}
		$this->_ci =& get_instance();

		// added by David McReynolds @ Daylight Studio 9/16/10 to prevent problems of axing the entire directory
		$this->_ci->load->helper('directory');
		$this->_ci = & get_instance();
		
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			$this->{'_' . $key} = $val;
		}
		
		// set these manually since inherited from CI_Parser
		// added by David McReynolds @ Daylight Studio 1/26/12 to set custom delimiters
		if (isset($config['l_delim'])) $this->l_delim = $config['l_delim'];
		if (isset($config['r_delim'])) $this->r_delim = $config['r_delim'];
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates Dwoo parser object
	 *
	 * @access	public
	 * @return	object
	 */
	public function spawn()
	{
		// try to create directory if it doesn't exist'
		// added by David McReynolds @ Daylight Studio 9/16/10 to prevent problems of axing the entire directory
		if (!is_dir($this->_parser_compile_dir))
		{
			@mkdir($this->_parser_compile_dir, DIR_WRITE_MODE, TRUE);
			chmodr($this->_parser_compile_dir, DIR_WRITE_MODE);
		}

		if (is_writable($this->_parser_compile_dir))
		{
			// Main Dwoo object
			$dwoo = new Dwoo;

			// The directory where compiled templates are located
			$dwoo->setCompileDir($this->_parser_compile_dir);

			$dwoo->setCacheDir($this->_parser_cache_dir);


			$dwoo->setCacheTime($this->_parser_cache_time);

			// Security
			$security = new MY_Security_Policy;

			$security->setPhpHandling($this->_parser_allow_php_tags);
			$security->allowPhpFunction($this->_parser_allowed_php_functions);
			$dwoo->setSecurityPolicy($security);

			// added by David McReynolds @ Daylight Studio 11/26/11
			return $dwoo;
		}

	}

	// --------------------------------------------------------------------

	/**
	 *  Parse a view file
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @param	string
	 * @return	string
	 */
	public function parse($template, $data = array(), $return = FALSE, $cache_id = NULL)
	{
		$string = $this->_ci->load->view($template, $data, TRUE);

		return $this->_parse_compiled($string, $data, $return, $cache_id);
	}

	// --------------------------------------------------------------------

	/**
	 *  String parse
	 *
	 * Parses pseudo-variables contained in the string content,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @param	strin
	 * @return	string
	 */
	
	// Changed default return to TRUE by David McReynolds @ Daylight Studio 12/21/10
	// removed is_include parameter and changed to use compile parameter instead 11/26/11
	public function string_parse($string, $data = array(), $return = TRUE, $cache_id = NULL)
	{
		return $this->_parse_compiled($string, $data, $return, $cache_id);
	}

	// Changed default return to TRUE by David McReynolds @ Daylight Studio 12/21/10
	// removed is_include parameter and changed to use compile parameter instead 11/26/11
	public function parse_string($string, $data = array(), $return = TRUE, $cache_id = NULL)
	{
		return $this->_parse_compiled($string, $data, $return, $cache_id);
	}

	// --------------------------------------------------------------------

	/**
	 *  Parse a simple string
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_simple($string, $data, $return = TRUE)
	{
		if ($string == '')
		{
			return FALSE;
		}

		foreach ($data as $key => $val)
		{
			if (is_array($val))
			{
				$string = $this->_parse_pair($key, $val, $string);
			}
			else
			{
				$string = $this->_parse_single($key, (string)$val, $string);
			}
		}

		if ($return == FALSE)
		{
			$CI =& get_instance();
			$CI->output->append_output($string);
		}

		return $string;
	}

	// --------------------------------------------------------------------

	/**
	 *  Parse
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @param	string
	 * @return	string
	 */
	public function _parse_compiled($string, $data, $return = FALSE, $cache_id = NULL)
	{
		// Start benchmark
		$this->_ci->benchmark->mark('dwoo_parse_start');

		// Convert from object to array
		if (!is_array($data))
		{
			$data = (array) $data;
		}

		$data = array_merge($this->_ci->load->get_vars(), $data);
		foreach ($this->_parser_assign_refs as $ref)
		{
			$data[$ref] = & $this->_ci->{$ref};
		}

		// Object containing data
		$dwoo_data = new Dwoo_Data;
		$dwoo_data->setData($data);

		$parsed_string = '';
		try
		{
			// Object of the template
			$tpl = new Dwoo_Template_String($string, NULL, $cache_id, NULL);

			$dwoo = !isset($this->_dwoo) ? self::spawn() : $this->_dwoo;
			
			// check for existence of dwoo object... may not be there if folder is not writable
			// added by David McReynolds @ Daylight Studio 1/20/11
			if (!empty($dwoo))
			{
				// Create the compiler instance
				$compiler = new Dwoo_Compiler();

				// added by David McReynolds @ Daylight Studio 1/22/12
				$compiler->setDelimiters($this->l_delim, $this->r_delim);

				
				//Add a pre-processor to help fix javascript {}
				// added by David McReynolds @ Daylight Studio 11/04/10
				$callback = create_function('$compiler', '
					$string = $compiler->getTemplateSource();
					
					$callback = create_function(\'$matches\',
						\'if (isset($matches[1]))
						{
							$str = "<script";
							$str .= preg_replace("#\\'.$this->l_delim.'([^s])#ms", "'.$this->l_delim.' $1", $matches[1]);
							$str .= "</script>";
							return $str;
						}
						else
						{
							return $matches[0];
						}
						\'
						);

					$string = preg_replace_callback("#<script(.+)</script>#Ums", $callback, $string);
					$compiler->setTemplateSource($string);
					return $string;
				');
				$compiler->addPreProcessor($callback);

				// render the template
				$parsed_string = $dwoo->get($tpl, $dwoo_data, $compiler);
			}
			else
			{
				// load FUEL language file because it has the proper error
				// added by David McReynolds @ Daylight Studio 1/20/11
				$this->_ci->load->module_language(FUEL_FOLDER, 'fuel');
				throw(new Exception(lang('error_folder_not_writable', $this->_ci->config->item('cache_path'))));
			}
		}

		catch (Exception $e)
		{
			if (strtolower(get_class($e)) == 'dwoo_exception')
			{
				echo ('<div class="error">'.$e->getMessage().'</div>');
			}
			else
			{
				show_error($e->getMessage());
			}
		}

		// Finish benchmark
		$this->_ci->benchmark->mark('dwoo_parse_end');

		// Return results or not ?
		if (!$return)
		{
			$this->_ci->output->append_output($parsed_string);
			return;
		}

		return $parsed_string;
	}

	// --------------------------------------------------------------------
}

class MY_Security_Policy extends Dwoo_Security_Policy {

	public function callMethod(Dwoo_Core $dwoo, $obj, $method, $args)
	{
		return call_user_func_array(array($obj, $method), $args);
	}

	public function isMethodAllowed()
	{
		return TRUE;
	}

}

// END MY_Parser Class

/* End of file MY_Parser.php */
/* Location: ./modules/fuel/libraries/MY_Parser.php */