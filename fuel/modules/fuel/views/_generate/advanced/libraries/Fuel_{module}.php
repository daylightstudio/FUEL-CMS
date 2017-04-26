<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 */

// ------------------------------------------------------------------------

/**
 * Fuel {module_name} object 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 */

// ------------------------------------------------------------------------

class Fuel_{module} extends Fuel_advanced_module {

	public $name = "{module}"; // the folder name of the module
	
	/**
	 * Constructor - Sets preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the backup object
	 *
	 * Accepts an associative array as input, containing preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$this->set_params($this->_config);
	}

	// --------------------------------------------------------------------

	/**
	 * Add your custom methods for this advanced module below.
	 * You will be able to access it via $this->fuel->{module}->my_method()
	 */
}