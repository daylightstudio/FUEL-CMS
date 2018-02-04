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
 * Fuel_abstract_parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		David McReynolds
 */

require_once(FUEL_PATH . 'libraries/parser/Fuel_parser_interface.php');

abstract class Fuel_abstract_parser extends Fuel_Base_library implements Fuel_parser_interface {

	protected $name = ''; // the name of the parsing engine
	protected $engine = NULL; // the actual parsing engine object
	protected $compile_dir = ''; // the directory to compile to
	protected $delimiters = array(); // the delimiters used for parsing
	protected $allowed_functions = array(); // functions that are allowed by the parsing engine
	protected $refs = array(); // object references that get passed to the parsing engine
	protected $data = array(); // variables that get passed to the parsing engine

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->initialize($config);
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
		parent::initialize($config);

		// Get the twig instance.
		$this->engine = $this->spawn();
	}

	/**
	 * Spawns a new instance of Twig.
	 *
	 * @return object
	 **/
	protected function spawn()
	{
		// must implement
	}

	/**
	 * Parse a view file.
	 *
	 * @param string
	 * @param array
	 * @param bool
	 * @return string
	 **/
	public function parse($template, $data = array(), $return = FALSE)
	{
		// get all data including references to CI objects
		$data = $this->get_data($data);

		// first get the view.
		$string = $this->CI->load->view($template, $data, TRUE);

		// now run the Twig parser.
		return $this->_parse($string, $data, $return);
	}

	/**
	 * Parse a view file.
	 *
	 * @param string
	 * @param array
	 * @param bool
	 * @return string
	 **/
	public function parse_string($string, $data = array(), $return = FALSE)
	{
		// get all data including references to CI objects
		$data = $this->get_data($data);

		// now run the Twig parser.
		return $this->_parse($string, $data, $return);
	}

	/**
	 * Merges data together for compiling.
	 *
	 * @param array
	 * @return array
	 **/
	public function get_data($data = array())
	{
		// ensure that we get all the data.
		$data = array_merge($this->CI->load->get_vars(), $this->data, $data);
		foreach ($this->refs as $ref)
		{
			$data[$ref] = & $this->CI->{$ref};
		}
		return $data;
	}

	/**
	 * Parse
	 *
	 * @param string
	 * @param array
	 * @param bool
	 * @return string
	 */
	public function _parse($string, $data, $return = FALSE)
	{
		
		// must implement
		return '';
	}

	/**
	 * Converts basic PHP syntax to templating syntax
	 *
	 * @param string
	 * @return string
	 */
	public function php_to_syntax($str)
	{
		return $str;
	}

	/**
	 * Returns the name of the parsing engine
	 *
	 * @return string
	 */
	public function name()
	{
		return $this->name;
	}
}


// END Fuel_abstract_parser Class

/* End of file Fuel_abstract_parser.php */
/* Location: ./modules/fuel/libraries/Fuel_abstract_parser.php */