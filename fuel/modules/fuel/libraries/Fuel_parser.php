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
 * Fuel_parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		David McReynolds
 * @link		http://docs.getfuelcms.com/libraries/fuel_parser
 */


require_once(FUEL_PATH . 'libraries/parser/Fuel_dwoo_parser.php');
require_once(FUEL_PATH . 'libraries/parser/Fuel_twig_parser.php');

class Fuel_parser extends Fuel_Base_library {

	protected $compile_dir = ''; // the directory to compile to
	protected $compile_dir_perms = DIR_WRITE_MODE; // the compile directory permissions
	protected $compile_file_perms = FILE_WRITE_MODE; // the compiled file permissions
	protected $allowed_functions = array(); // functions that are allowed by the parsing engine
	protected $delimiters = array(); // the delimiters used for parsing
	protected $refs = array(); // object references that get passed to the parsing engine
	protected $data = array(); // variables that get passed to the parsing engine
	protected $engine = NULL; // the actual parsing engine object

	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct();
		$this->initialize();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$_fuel_config = array('compile_dir', 'delimiters', 'allowed_functions', 'compile_dir_perms', 'compile_file_perms', 'refs');
		foreach($_fuel_config as $p)
		{
			$config = $this->fuel->config('parser_'.$p);
			if (!is_null($config))
			{
				$this->$p = $config;	
			}
		}
		$this->set_engine($this->fuel->config('parser_engine'));
		$this->create_compile_dir();
	}

	// --------------------------------------------------------------------

	/**
	 * Sets parsing engine
	 *
	 * @access	public
	 * @param	mixed 	Can be a string (e.g. 'dwoo', 'twig', 'ci') or an object that implements the Fuel_parser_interface
	 * @param	string 	Configuration parameters to pass to the parsing engine (e.g. compile_dir, delimiters, allowed_functions, refs, data)
	 * @return	object 	A reference to the Fuel_parser object
	 */
	public function set_engine($engine = NULL, $config = array())
	{
		if (empty($engine))
		{
			$engine = $this->fuel->config('parser_engine');
		}

		if (is_string($engine))
		{
			$default_config = array(
				'compile_dir' => $this->compile_dir,
				'delimiters' => $this->delimiters,
				'allowed_functions' => $this->allowed_functions,
				'refs' => $this->refs,
				'data' => $this->data,
				);
			$config = array_merge($default_config, $config);
			switch($engine)
			{
				case 'ci':
					$this->CI->load->library('parser');
					$this->engine = new CI_Parser();
					$delimiters = (isset($config['delimiters']['tag_variable'])) ? $config['delimiters']['tag_variable'] : $config['delimiters'];
					$l_delim = $delimiters[0];
					$r_delim = $delimiters[1];
					$this->engine->set_delimiters($l_delim, $r_delim);
					break;
				case 'twig':
					$this->engine = new Fuel_twig_parser($config);
					break;
				default:
					$config['compile_file_perms'] = $this->compile_file_perms;
					$this->engine = new Fuel_dwoo_parser($config);
			}
		}
		elseif ($engine instanceof Fuel_parser_interface OR $engine instanceof CI_Parser)
		{
			$this->engine = $engine;
		}
		else
		{
			throw(new Exception('Invalid parsing engine specified.'));
		}
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the parsing engine object
	 *
	 * @access	public
	 * @return	object
	 */
	public function get_engine()
	{
		return $this->engine;
	}

	// --------------------------------------------------------------------

	/**
	 * The parsing engine's name (e.g. dwoo, twig, ci)
	 *
	 * @access	public
	 * @return	string
	 */
	public function name()
	{
		if (method_exists($this->engine, 'name'))
		{
			return $this->engine->name();	
		}
		else
		{
			return 'ci';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the compiled directory path
	 *
	 * @access	public
	 * @param	string 	The directory path
	 * @return	object 	A reference to the Fuel_parser object
	 */
	public function set_compile_dir($dir)
	{
		$this->compile_dir = $dir;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the compiled directory path
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_compiled_dir()
	{
		return $this->compile_dir;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the delimiters for the parser
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function set_delimiters($delimiters, $type = NULL)
	{
		if (!empty($type))
		{
			$this->delimiters['tag_'.$type] = $delimiters;
		}
		else
		{
			$this->delimiters = $delimiters;
		}
		$params = array('delimiters' => $this->delimiters);
		$delimiters = $this->engine->set_params($params);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Gets the delimiter for the parser
	 *
	 * @access	public
	 * @param	string The type of delimiter
	 * @return	string
	 */
	public function get_delimiters($type = NULL)
	{
		if (!empty($type))
		{
			$key = 'tag_'.$type;
			if (isset($this->delimiters[$key]))
			{
				return $this->delimiters[$key];
			}
		}
		return $this->delimiters;
	}

	// --------------------------------------------------------------------

	/**
	 * Adds an allowed function
	 *
	 * @access	public
	 * @param	string 	The name of the function to remove
	 * @return	object 	A reference to the Fuel_parser object
	 */
	public function add_allowed_function($func)
	{
		if (!in_array($func, $this->allowed_functions))
		{
			$this->allowed_functions[] = $func;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Removes a function from the allowed list of functions
	 *
	 * @access	public
	 * @param	string 	The name of the function to remove
	 * @return	object 	A reference to the Fuel_parser object
	 */
	public function remove_allowed_function($func)
	{
		$this->allowed_functions = array_diff($this->allowed_functions, array($func));
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Adds data to be passed to the parsing engine
	 *
	 * @access	public
	 * @param	mixed 	Can be an array or a string (but if string must pass the second $value parameter)
	 * @param	mixed 	A value to associate with the $data (which is the $variable name... optional)
	 * @return	object 	A reference to the Fuel_parser object
	 */
	public function add_data($data, $value = NULL)
	{
		if (is_string($data))
		{
			$data = array($data => $value);
		}

		// convert from object to array
		if (!is_array($data))
		{
			$data = (array)$data;
		}

		$this->data = array_merge($this->data, $data);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Removes the data passed to the parsing engine
	 *
	 * @access	public
	 * @return	object 	A reference to the Fuel_parser object
	 */
	public function clear_data()
	{
		$this->data = array();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a view file
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
	public function parse($view, $data = array(), $return = FALSE)
	{
		return $this->engine->parse($view, $data, $return);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Parses a string
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_string($string, $data = array(), $return = TRUE)
	{
		return $this->engine->parse_string($string, $data, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the compile dir if possible in case it doesn't exist
	 *
	 * @access	public
	 * @return	bool
	 */
	public function create_compile_dir()
	{
		// try to create directory if it doesn't exist'
		if (!is_dir($this->compile_dir))
		{
			$this->CI->load->helper('directory');
			$created = @mkdir($this->compile_dir, $this->compile_dir_perms, TRUE);
			chmodr($this->compile_dir, $this->compile_dir_perms);
			return $created;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Converts PHP syntax to the templating syntax... as best it can without getting crazy
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function php_to_syntax($str)
	{
		return $this->engine->php_to_syntax($str);
	}
}

/* End of file Fuel_parser.php */
/* Location: ./modules/fuel/libraries/Fuel_parser.php */