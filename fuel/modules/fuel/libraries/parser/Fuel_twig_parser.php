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
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		David McReynolds
 */

// https://github.com/geevcookie/CI-Twig-Parser/blob/master/libraries/MY_Parser.php
require_once(FUEL_PATH . 'libraries/parser/Twig/Autoloader.php');
require_once(FUEL_PATH . 'libraries/parser/Fuel_abstract_parser.php');

class Fuel_twig_parser extends Fuel_abstract_parser {

	protected $name = 'twig'; // name of the parsing enging

	// http://twig.sensiolabs.org/doc/api.html#environment-options
	protected $debug = NULL;
	protected $charset = 'utf-8';
	protected $base_template_class = 'Twig_Template';
	protected $strict_variables = FALSE;
	protected $autoescape = TRUE;
	protected $optimizations = -1;

	/**
	 * Spawns a new instance of Twig.
	 *
	 * @return object
	 **/
	protected function spawn()
	{
		// register the Twig autoloader.
		Twig_Autoloader::register();
		
		// Init the Twig loader.
		$loader = new Twig_Loader_String();

		// check if the cache dir is set.
		if ($this->compile_dir)
		{
			if (is_null($this->debug))
			{
				$this->debug = is_dev_mode();
			}

			$twig = new Twig_Environment($loader, array(
				'cache' => $this->compile_dir,
				'debug' => $this->debug,
				'charset' => $this->charset,
				'base_template_class' => $this->base_template_class,
				'strict_variables' => $this->strict_variables,
				'autoescape' => $this->autoescape,
				'optimizations' => $this->optimizations,
			));
		}
		else
		{
			$twig = new Twig_Environment($loader, array('autoescape' => FALSE));
		}

		// init all functions as Twig functions.
		foreach($this->allowed_functions as $function)
		{
			$twig->addFunction($function, new Twig_Function_Function($function));
		}

		// setup debugger
		$twig->addExtension(new Twig_Extension_Debug());

		// setup the Lexer
		$lexer = new Twig_Lexer($twig, $this->delimiters);
		$twig->setLexer($lexer);


		// finally, return the object.
		return $twig;
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
		// start benchmark
		$this->CI->benchmark->mark('twig_parse_start');

		// parse the template.
		try
		{
			$parsed_string = $this->engine->render($string, $data);
		}
		catch (Exception $e)
		{
			show_error($e);
		}

		// finish benchmark
		$this->CI->benchmark->mark('twig_parse_end');
		
		// return results or not ?
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
		$var_l_delim = $this->delimiters['tag_variable'][0];
		$var_r_delim = $this->delimiters['tag_variable'][1];

		$block_l_delim = $this->delimiters['tag_block'][0];
		$block_r_delim = $this->delimiters['tag_block'][1];

		$find = array('<?php foreach', '<?php endforeach', '<?php endif', '<?php echo $', '<?php echo ', '<?php ', '<?=$', '<?=');
		$replace = array($block_l_delim.' for ', $block_l_delim.' endfor ', $block_l_delim.' endif', $var_l_delim, $var_l_delim, $block_l_delim, $var_l_delim, $var_l_delim);

		// first, take care of simple variables being outputted
		$str = preg_replace_callback('#(.*)<\?(=|php echo )(.*)\?>(.*)#Ums', array($this, '_php_to_syntax_var_callback'), $str);

		// next, we work on block level elements
		$str = preg_replace_callback('#(.*)<\?php(.*)\?>(.*)#Ums', array($this, '_php_to_syntax_block_callback'), $str);

		return $str;
	}

	protected function _php_to_syntax_var_callback($matches)
	{
		$var_l_delim = $this->delimiters['tag_variable'][0];
		$var_r_delim = $this->delimiters['tag_variable'][1];

		$str = '';
		if (!empty($matches[3]))
		{
			$str = $matches[3];
			$str = str_replace(array('$', '->'), array('', '.'), $str);
		}
		$str = $matches[1].$var_l_delim.' '.$str.' '.$var_r_delim.$matches[4];
		return $str;
	}

	protected function _php_to_syntax_block_callback($matches)
	{
		$block_l_delim = $this->delimiters['tag_block'][0];
		$block_r_delim = $this->delimiters['tag_block'][1];
		if (!empty($matches[2]))
		{
			$str = $matches[2];
			$str = preg_replace('#(.+)array\(([^\)]+)\)(.+)#U', '$1{$2}$3', $str);
			$str = preg_replace('#(.+)(!)?empty\(([^\)]+)\)(.+)#U', '$1$3 is $2empty$4', $str);
			$str = preg_replace('#(.+)if\s*\((.+)\)\s*(:|\{)#U', '$1if $2', $str);
			$str = preg_replace('#(.+)foreach\s*\((.+)\s+as\s+(\$\w+\s*=>)?\s*\$(\w+)\)\s*(:|\{)#U', '$1for $4 in $2', $str);
			$str = preg_replace('#^\s*\$(\w+)\s*=(.+)#U', ' set $1 =$2', $str);
			$str = str_replace(array('$', ':', '.', '!', '=>', ';', 'endforeach', '}', '  '), array('', '', '->', 'not ', ':', '' , 'endfor', $block_r_delim, ' '), $str);
		}
		$str = $matches[1].$block_l_delim.$str.$block_r_delim.$matches[3];
		return $str;
	}
}


/* End of file Fuel_twig_parser.php */
/* Location: ./modules/fuel/libraries/parser/Fuel_twig_parser.php */
