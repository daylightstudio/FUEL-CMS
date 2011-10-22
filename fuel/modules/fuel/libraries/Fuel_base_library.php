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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL base library object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_base_library
 */

// --------------------------------------------------------------------

class Fuel_base_library {
	
	protected $CI; // reference to the CI super object
	protected $fuel; // reference to the fuel object
	
	protected $_errors = array(); // array to keep track of errors
	
	/**
	 * Constructor - Sets Fuel_base_library preferences and to any children
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
		// 
		// // initialize object if any parameters
		// if (!empty($params))
		// {
		// 	$this->initialize($params);
		// }
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params = array())
	{
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */
	function set_params($params)
	{
		if (!is_array($params)) return;

		foreach ($params as $key => $val)
		{
			if (isset($this->$key) AND substr($key, 0, 1) != '_')
			{
				$this->$key = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns any errors that may have occurred during backing up
	 *
	 * @access	public
	 * @return	array
	 */	
	function errors($formatted = FALSE, $open = '', $close = "\n\n")
	{
		if ($formatted === FALSE)
		{
			return $this->_errors;
		}

		$error = '';
		foreach($this->_errors as $e)
		{
			$error .= $open.$e.$close;
		}
		return $error;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether there were errors in backing up or not
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function has_errors()
	{
		return count($this->_errors) > 0;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds an error to the _errors array
	 *
	 * @access	protected
	 * @param	string	error message
	 * @return	array
	 */	
	protected function _add_error($error)
	{
		$this->_errors[] = $error;
	}
	
	
}

/* End of file Fuel_base_library.php */
/* Location: ./modules/fuel/libraries/Fuel_base_library.php */