<?php defined('BASEPATH') OR exit('No direct script access allowed');
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
 * Dwoo Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		David McReynolds... originally inspired by Phil Sturgeon (http://philsturgeon.co.uk/code/codeigniter-dwoo)
 */

// Modified by David McReynolds @ Daylight Studio to automatically create the compile directory if it doesn't exist 9/16/10'
require_once(FUEL_PATH . 'libraries/parser/dwoo/dwooAutoload.php');
require_once(FUEL_PATH . 'libraries/parser/Fuel_abstract_parser.php');

class Fuel_dwoo_parser extends Fuel_abstract_parser {

	protected $name = 'dwoo'; // name of the parsing engine
	protected $compile_file_perms = FILE_WRITE_MODE; // the file permissions for the compiled string

	/**
	 * Spawns a new instance of Dwoo.
	 *
	 * @return object
	 **/
	protected function spawn()
	{
		if (is_writable($this->compile_dir))
		{
			// Main Dwoo object
			$dwoo = new Dwoo;

			// Set the directory of where to compile the files
			$dwoo->setCompileDir($this->compile_dir);

			// set security policy
			$security = new MY_Security_Policy;

			// no PHP handline
			$security->setPhpHandling(1);
			$security->allowPhpFunction($this->allowed_functions);
			$dwoo->setSecurityPolicy($security);

			return $dwoo;
		}
	}

	/**
	 * Parse
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param string
	 * @param array
	 * @param bool
	 * @return string
	 */
	public function _parse($string, $data, $return = FALSE)
	{
		// Start benchmark
		$this->CI->benchmark->mark('dwoo_parse_start');

		// Object containing data
		$dwoo_data = new Dwoo_Data;
		$dwoo_data->setData($data);

		$parsed_string = '';
		try
		{
			// Object of the template
			$tpl = new Dwoo_Template_String($string, NULL, NULL, NULL);

			$tpl->setChmod($this->compile_file_perms);

			$dwoo = !isset($this->engine) ? $this->spawn() : $this->engine;
			
			// check for existence of dwoo object... may not be there if folder is not writable
			// added by David McReynolds @ Daylight Studio 1/20/11
			if (!empty($dwoo))
			{
				// Create the compiler instance
				$compiler = new Dwoo_Compiler();

				$delimiters = (isset($this->delimiters['tag_variable'])) ? $this->delimiters['tag_variable'] : $this->delimiters;
				$l_delim = $delimiters[0];
				$r_delim = $delimiters[1];

				$compiler->setDelimiters($l_delim, $r_delim);
				
				//Add a pre-processor to help fix javascript {}
				// added by David McReynolds @ Daylight Studio 11/04/10
				$callback = function($compiler) use ($l_delim) {
					$string = $compiler->getTemplateSource();
					
					$callback = function($matches) use ($l_delim){
						if (isset($matches[1]))
						{
							$str = "<script";
							$str .= preg_replace('#'.$l_delim.'([^s])#ms', $l_delim.' $1', $matches[1]);
							$str .= "</script>";
							return $str;
						}
						else
						{
							return $matches[0];
						}
					};

					$string = preg_replace_callback("#<script(.+)</script>#Ums", $callback, $string);
					$compiler->setTemplateSource($string);
					return $string;
				};
				$compiler->addPreProcessor($callback);

				// render the template
				$parsed_string = $dwoo->get($tpl, $dwoo_data, $compiler);
			}
			else
			{
				// load FUEL language file because it has the proper error
				// added by David McReynolds @ Daylight Studio 1/20/11
				$this->CI->load->module_language(FUEL_FOLDER, 'fuel');
				throw(new Exception(lang('error_folder_not_writable', $this->compile_dir)));
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
		$this->CI->benchmark->mark('dwoo_parse_end');

		// Return results or not ?
		if (!$return)
		{
			$this->CI->output->append_output($parsed_string);
			return;
		}

		return $parsed_string;
	}

	/**
	 * Converts basic PHP syntax to templating syntax
	 *
	 * @param string
	 * @return string
	 */
	public function php_to_syntax($str)
	{
		$delimiters = (isset($this->delimiters['tag_variable'])) ? $this->delimiters['tag_variable'] : $this->delimiters;
		$l_delim = $delimiters[0];
		$r_delim = $delimiters[1];

		$find = array('$CI->', '$this->', '<?php endforeach', '<?php endif', '<?php echo ', '<?php ', '<?=');
		$replace = array('$', '$', $l_delim.'/foreach', $l_delim.'/if', $l_delim, $l_delim, $l_delim);

		// translate HTML comments NOT! Javascript
		
		// close ending php
		$str = preg_replace('#([:|;])?\s*\?>#U', $r_delim.'$3', $str);

		$str = str_replace($find, $replace, $str);
		
		// TODO javascript escape... commented out because it's problematic... will need to revisit if it makes sense'
		//$str = preg_replace('#((?<!\{literal\}).*)<script(.+)>(.+)<\/script>.*(?!\{\\\literal\})#Us', "$1\n{literal}\n<script$2>$3</script>\n{\literal}\n", $str);
		
		// foreach cleanup
		$str = preg_replace('#'.$l_delim.'\s*foreach\s*\((\$\w+)\s+as\s+\$(\w+)\s*(=>\s*\$(\w+))?\)\s*'.$r_delim.'#U', $l_delim.'foreach $1 $2 $4'.$r_delim, $str); // with and without keys

		// remove !empty
		$callback = function($matches) use ($l_delim) {
			if (!empty($matches[2]))
			{
				return $l_delim.$matches[1].$matches[3];
			}
			else
			{
				return $l_delim.$matches[1]."!".$matches[3];
			}
		};
		
		$str = preg_replace_callback('#'.$l_delim.'(.+)(!)\s*?empty\((.+)\)#U', $callback, $str);
		
		// remove parenthesis from within if conditional
		$callback2 = function($matches) {
			$CI =& get_instance();
			$allowed_funcs = $CI->fuel->config("parser_allowed_functions");
			$str = $matches[0];
			$ldlim = "___<";
			$rdlim = ">___";

			// loop through all allowed function and escape any parenthesis
			foreach($allowed_funcs as $func)
			{
				$regex = "#(.*)".preg_quote($func)."\((.*)\)(.*)#U";
				$str = preg_replace($regex, "$1".$func.$ldlim."$2".$rdlim."$3", $str);
			}

			// now replace any other parenthesis
			$str = str_replace(array("(", ")"), array(" ", ""), $str);
			$str = str_replace(array($ldlim, $rdlim), array("(", ")"), $str);
			return $str;
		};
		
		$str = preg_replace_callback('#'.$l_delim.'if.+'.$r_delim.'#U', $callback2, $str);
		// fix arrays
		$callback = function($matches){
			if (strstr($matches[0], "=>"))
			{
				$key_vals = explode(",", $matches[0]);
				$return_arr = array();
				foreach($key_vals as $val)
				{
					@list($k, $v) = explode("=>", $val);
					$k = str_replace(array("\"", "\'"), "", $k);
					$return_arr[] = trim($k)."=".trim($v);
				}
				$return = implode(" ", $return_arr);
				return $return;
			}
			return $matches[0];
			};
		
		$str = preg_replace_callback('#(array\()(.+)(\))#U', $callback, $str);
		return $str;
	}
}


class MY_Security_Policy extends Dwoo_Security_Policy {

	public function callMethod(Dwoo_Core $dwoo, $obj, $method, $args)
	{
		return call_user_func_array(array($obj, $method), $args);
	}

	public function isMethodAllowed($class, $method = null)
	{
		return TRUE;
	}

}

// END MY_Parser Class

/* End of file MY_Parser.php */
/* Location: ./modules/fuel/libraries/MY_Parser.php */