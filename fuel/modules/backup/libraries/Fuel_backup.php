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
 * Backup object 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/backup
 */

// --------------------------------------------------------------------

class Fuel_backup extends Fuel_advanced_module {
	
	public $file_prefix = 'AUTO'; // used for the name of the backup file. A value of AUTO will automatically create the name
	public $file_date_format = 'Y-m-d'; // date format to append to file name
	public $zip = TRUE; // ZIP up the file or not
	public $include_assets = FALSE; // determines whether to backup assets by default
	public $download = TRUE; // download the file or not from the browser
	public $must_write_zip_file = FALSE; //specifies whether the zip should be saved to the file system
	public $days_to_keep = 30; // number of days to hold backups. A value of 0 or FALSE will be forever
	public $allow_overwrite = FALSE; // allow the backup to overwrite existing
	public $backup = FALSE; // determines whether to backup assets by default
	public $cron_email = ''; // the email address to send the backup cron notification
	public $cron_email_file = TRUE; // determines whether to send files in an email with cron backup
	public $backup_path = ''; //backup path for data. Deafult is at the same level as the system and application folder
	public $db_backup_prefs = array( //ddatabase backup preferences
									'ignore'	=> array(), // List of tables to omit from the backup
									'add_drop'	=> TRUE, // Whether to add DROP TABLE statements to backup file
									'add_insert'=> TRUE // Whether to add INSERT data to backup file
									);
	protected $_backup_data = array(); // an array of backed up data information
	
	/**
	 * Constructor - Sets Fuel_backup preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();
		$this->CI->load->library('zip');
		$this->CI->load->helper('file');
		
		if (empty($params))
		{
			$params['name'] = 'backup';
		}
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the backup object
	 *
	 * Accepts an associative array as input, containing backup preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		$this->set_params($this->_config);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Backs up the database / and assets (if configured) information to the specified backup path
	 *
	 * Accepts an associative array as input, containing backup preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	boolean
	 */	
	function do_backup($params = array())
	{
		if (!empty($params))
		{
			$this->set_params($params);
		}
		
		$data = $this->_database();
		$file_name = $this->_file_name().'.sql';
		
		// if zip is specified, we'll zip it up '
		if (!empty($this->zip))
		{
			// clear any data on the object
			$this->CI->zip->clear_data();
			
			// add database data
			$this->CI->zip->add_data($file_name, $data);

			// include assets?
			if ($this->include_assets)
			{
				$this->CI->zip->read_dir(assets_server_path());
			}
			return $this->zip($file_name);
		}
		else
		{
			return $this->write($file_name, $data);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Backs up the database information to the specified backup path
	 *
	 * Accepts an associative array as input, containing backup preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	boolean
	 */	
	function database($params = array())
	{
		if (!empty($params))
		{
			$this->set_params($params);
		}
		
		$data = $this->_database();
		
		$file_name = $this->_file_name().'.sql';

		if (!empty($this->zip))
		{
			// clear any data on the object
			$this->CI->zip->clear_data();
		
			return $this->zip($file_name, $data);
		}
		else
		{
			
			return $this->write($file_name, $data);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Backs up the assets folder to the specified backup path
	 *
	 * Accepts an associative array as input, containing backup preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	boolean
	 */	
	function assets($params = array())
	{
		if (!empty($params))
		{
			$this->set_params($params);
		}
		
		$file_name = $this->_file_name();
		
		if (!empty($params['zip']))
		{
			// clear any data on the object
			$this->CI->zip->clear_data();
	
			return $this->zip($file_name, assets_server_path());
		}
		else
		{
			return $this->write($file_name);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Zips up data
	 *
	 * Accepts an associative array as input, containing backup preferences
	 *
	 * @access	public
	 * @param	string	file name
	 * @param	string	information to zip up
	 * @return	boolean
	 */	
	function zip($file_name, $data = NULL)
	{
		if (is_string($data))
		{
			// if string is a directory path, then we read the directory... 
			// may be too presumptious but it's convenient'
			if (is_dir($data))
			{
				$this->CI->zip->read_dir($data);
			}
			else
			{
				$this->CI->zip->add_data($file_name, $data);
			}
		}
		
		// check if folder is writable
		if (!is_really_writable($this->backup_path) AND $this->must_write_zip_file)
		{
			$this->_add_error(lang('data_backup_folder_not_writable', $this->backup_path));
			return FALSE;
		}
		
		// add .zip extension so file_name is correct
		$file_name = $file_name.'.zip';
		
		// path to download file
		$full_path = $this->backup_path.$file_name;
		
		if (file_exists($full_path) AND !$this->allow_overwrite AND $this->must_write_zip_file)
		{
			$this->_add_error(lang('data_backup_already_exists'));
			return FALSE;
		}
		
		// write the zip file to a folder on your server. 
		$archived = $this->CI->zip->archive($full_path); 
		
		if (!$archived AND $this->must_write_zip_file) 
		{
			$this->_add_error(lang('data_backup_zip_error'));
			return FALSE;
		}
	
		// download the file to your desktop. 
		if ($this->download)
		{
			$this->CI->zip->download($file_name);
		}

		// save to backup data
		$this->_add_backup_data('file_name', $file_name);

		// save to backup data
		$this->_add_backup_data('full_path', $full_path);

		return $archived;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Writes a file to the backup folder
	 *
	 * Accepts an associative array as input, containing backup preferences
	 *
	 * @access	public
	 * @param	string	file name
	 * @param	string	information to write to the file
	 * @return	boolean
	 */	
	function write($file_name, $data)
	{
		$full_path = $this->backup_path.$file_name;
		
		// check if folder is writable
		if (!is_really_writable($this->backup_path))
		{
			$this->_add_error(lang('data_backup_folder_not_writable', $full_path));
			return FALSE;
		}
		
		// save to backup data
		$this->_add_backup_data('file_name', $file_name);
		
		// save to backup data
		$this->_add_backup_data('full_path', $full_path);
		
		return write_file($full_path, $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the backed up data information
	 *
	 * Similar to File Upload class, provides post-mortem info
	 *
	 * @access	public
	 * @return	array
	 */	
	function backup_data()
	{
		$backup_data = array(
				'file_name' => '',
				'full_path' => '',
			);
		$backup_data = array_merge($backup_data, $this->_backup_data);
		return $backup_data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds information to backup data
	 *
	 * This is a convenience method 
	 *
	 * @access	protected
	 * @param	string	key name
	 * @param	mixed	data
	 * @return	void
	 */	
	function _add_backup_data($key, $data)
	{
		$this->_backup_data[$key] = $data;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Removes a backup file from the backup directory
	 *
	 * @access	public
	 * @param	string	file name
	 * @return	boolean
	 */	
	function remove($backup_file)
	{
		$filepath = $this->backup_path.$backup_file;
		$return = FALSE;
		if (file_exists($filepath) AND is_really_writable($filepath))
		{
			if (@unlink($filepath))
			{
				$this->_add_error(lang('data_backup_error_could_not_delete'));
			}
			else
			{
				$return = TRUE;
			}
		}
		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the file name based on the object parameters
	 *
	 * This is a convenience method 
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function _file_name()
	{
		$backup_date = date($this->file_date_format);
		$prefix = $this->file_prefix;
		
		if ($prefix == 'AUTO')
		{
			$prefix = url_title($this->fuel->config('site_name'), 'underscore', TRUE);
		}
		$file_name = $prefix.'_'.date($this->file_date_format);
		return $file_name;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the backed up database as a string
	 *
	 * This is a convenience method 
	 *
	 * @access	protected
	 * @return	string
	 */	
	protected function _database()
	{
		// Load the DB utility class
		$this->CI->load->dbutil();
		
		// need to do text here to make some fixes
		$db_back_prefs = $this->db_backup_prefs;
		$db_back_prefs['format'] = 'txt';
		$backup =& $this->CI->dbutil->backup($db_back_prefs); 
		
		return $backup;
	}
	
	

}

/* End of file Fuel_backup.php */
/* Location: ./modules/fuel/libraries/Fuel_backup.php */