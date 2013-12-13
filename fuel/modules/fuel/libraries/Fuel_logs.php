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
 * FUEL logs object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_logs
 */

// --------------------------------------------------------------------

class Fuel_logs extends Fuel_base_library {
	
	public $location = 'db'; // the location to write the log. The default is the database. Otherwise it will write to applications logs folder
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 *
	 * @access	public
	 * @param	array	Array of initalization parameters  (optional)
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$this->fuel->load_model('fuel_logs');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Writes a log message to either the database or to a log file
	 *
	 * @access	public
	 * @param	string	Message to log
	 * @param	string	Message level. Options are error, debug, info. Default is 'info'.   (optional)
	 * @param	string	Where to store the log message. Options are 'db' or 'file'. Default is 'db.'  (optional)
	 * @return	void
	 */	
	public function write($msg, $level = 'info', $location = 'db')
	{
		if ($location == 'db')
		{
			$this->CI->fuel_logs_model->logit($msg, $level);
		}
		else
		{
			log_message($level, $msg);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the FUEL fuel_logs_model object
	 *
	 * @access	public
	 * @return	object
	 */
	public function &model()
	{
		return $this->CI->fuel_logs_model;
	}
	
}

/* End of file Fuel_logs.php */
/* Location: ./modules/fuel/libraries/Fuel_logs.php */