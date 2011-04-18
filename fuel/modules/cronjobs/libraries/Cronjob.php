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
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * A Cronjob wrapper class
 *
 * This class allows you to manage cron jobs
 * based on this:
 * http://www.underwaterdesign.com/2006/06/php_create_a_cron_job_with_php.php
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/cronjob.html
 */

class Cronjob {
	
	public $cronfile = 'crons/crontab.txt'; // path to the crontab fle
	public $mailto = ''; // mailto value of crontab
	public $user = ''; // the user the crontab belongs to
	public $sudo_pwd = ''; //the user password

	private $_jobs = array();
	
	/**
	 * Constructor - Sets Cronjob preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);		
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the file path to the cron file
	 *
	 * @access	public
	 * @param	string	cron file path
	 * @return	void
	 */
	function set_cronfile($cronfile)
	{
		$this->cronfile = $cronfile;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Adds a new line to the cron file
	 *
	 * @access	public
	 * @param	string	minute value of cronjob
	 * @param	string	hour value of cronjob
	 * @param	string	day of the month value of cronjob
	 * @param	string	month value of cronjob
	 * @param	string	week day value of cronjob
	 * @param	string	the command to execute
	 * @return	void
	 */
	function add($min = '*', $hour = '*', $month_day = '*', $month_num = '*', $week_day = '*', $command = '')
	{
		// if $min has a space, then we assume that that is the entire job as a single string
		if (strpos($min, ' ') !== FALSE)
		{
			$job = $min;
		}
		else
		{
			$time_segs = array('min', 'hour', 'month_day', 'month_num', 'week_day');
			foreach($time_segs as $seg)
			{
				if (!isset($$seg)) $$seg = '*';
			}
			$command = trim($command);
			if (!empty($command)) $job = $min." ".$hour." ".$month_day." ".$month_num." ".$week_day." ".$command;
		}
		
		if (!empty($job)) $this->_jobs[] = $job;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the cron file
	 * 
	 * If parameters are passed, they will be sent to the add method
	 *
	 * @access	public
	 * @param	mixed optional to add values that will call the add method
	 * @return	void
	 */
	function create()
	{
		// if arguments are passed to this method then we will pass them to the add method
		$args = func_get_args();
		$exec = $this->_exec('crontab '.$this->cronfile);
		
		if (!empty($args))
		{
			call_user_func_array(array(&$this, 'add'), $args);
		}
		
		// cast to array in case a string is input
		$this->_jobs = (array) $this->_jobs;
		
		// create cronjob string
		$cron = '';
		if (!empty($this->mailto)) $cron .= "MAILTO=".$this->mailto.PHP_EOL;
		$joiner = (empty($this->mailto)) ? ">> /dev/null 2>&1" : '';
		$cron .= implode($joiner.PHP_EOL, $this->_jobs);
		$cron .= PHP_EOL; // !!!important... for cronjobs to run, you must have this ending newline

		if (file_exists($this->cronfile))
		{
			$open = fopen($this->cronfile, "w"); // This overwrites current line
			fwrite($open, $cron); 
			fclose($open); 
			
			// this will reinstate your Cron job
	        exec($exec);
		}
		else
		{  
			// get directory and check if it is writable
			$dir = explode(DIRECTORY_SEPARATOR, $this->cronfile);
			array_pop($dir);
			$dir = implode(DIRECTORY_SEPARATOR, $dir).DIRECTORY_SEPARATOR;
			if (!is_writable($this->cronfile))
			{
				@mkdir($dir, 0777, TRUE);
			}
			touch($this->cronfile); // create the file, Directory "cron" must be writeable
			chmod($this->cronfile, 0777); // make new file writeable
			$open = fopen($this->cronfile, "w"); 
			fwrite($open, $cron); 
			fclose($open);
    		
			// start the cron job! 
	        exec($exec); 
	    }
	}
	
	// --------------------------------------------------------------------

	/**
	 * Removes the crontab
	 *
	 * @access	public
	 * @return	void
	 */
	function remove()
	{
		$exec = $this->_exec('crontab -r');
		exec($exec); 
	}

	// --------------------------------------------------------------------

	/**
	 * Views a crontab
	 *
	 * @access	public
	 * @return	void
	 */
	function view()
	{
		$exec = $this->_exec('crontab -l');
		exec($exec, $output);
		$return = array();
		foreach($output as $o)
		{
			$o = trim($o);
			if (!empty($o))
			{
				$return[] = $o;
			}
		}
		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Accessor method to the _jobs array that contains a list of the cronjobs
	 *
	 * @access	public
	 * @return	void
	 */
	function jobs()
	{
		return $this->_jobs;
	}

	// --------------------------------------------------------------------

	/**
	 * Helps create the executable command based on the user and sudo_pwd information
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	private function _exec($exec)
	{
		if (!empty($this->user)) 
		{
			if (!empty($this->sudo_pwd))
			{
				$exec = 'echo '.$this->sudo_pwd.' | sudo -S '.$exec;
			}
			$exec .= ' -u '.$this->user;
		}
		return $exec;
	}
	
}

/* End of file Cronjob.php */
/* Location: ./modules/cronjob/libraries/Cronjob.php */
