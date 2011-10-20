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
 * FUEL pages 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_backup
 */

// --------------------------------------------------------------------

class Fuel_backup extends Fuel_advanced_module {
	
	function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	function initialize($params)
	{
		parent::initialize($params);
	}
	
	function database($params = array())
	{
		$default = array(
			'backup_path' => $this->config('db_backup_path'),
			'db_prefs' => $this->config('db_backup_prefs'),
			'file_prefix' => $this->config('backup_file_prefix'),
			'file_date_format' => $this->config('backup_file_date_format'),
			'zip' => $this->config('backup_zip'),
			'download' => $this->config('backup_download'),
		);
		
		$params = array_merge($default, $params);
		
		$download_path = $params['backup_path'];
		$is_writable = is_writable($download_path);

		// Load the DB utility class
		$this->load->dbutil();
		
		// need to do text here to make some fixes
		$db_back_prefs = $params['db_prefs'];
		$db_back_prefs['format'] = 'txt';
		$backup =& $this->dbutil->backup($db_back_prefs); 
		
		$filename = $this->_filename($params['file_prefix'], $params['file_date_format']).'.sql';
		
		if (!empty($params['backup_zip']))
		{
			return $this->_zip($filename, $backup, $download_path, $params['download']);
		}
		else
		{
			$download_file = $download_path.$filename;
			return $this->_write($download_file);
		}
	}
	
	function assets($params = array())
	{
		$default = array(
			'backup_path' => $this->config('db_backup_path'),
			'file_prefix' => $this->config('backup_file_prefix'),
			'file_date_format' => $this->config('backup_file_date_format'),
			'zip' => $this->config('backup_zip'),
			'download' => $this->config('backup_download'),
		);
		
		$params = array_merge($default, $params);
		$filename = $this->_filename($params['file_prefix'], $params['file_date_format']);
		
		$this->CI->load->library('zip');
		$this->CI->zip->read_dir(assets_server_path());
		
		if (!empty($params['backup_zip']))
		{
			return $this->_zip($filename, $backup, $download_path, $params['download']);
		}
		else
		{
			$download_file = $download_path.$filename;
			return $this->_write($download_file);
		}
		
	}
	// TODO FIX
	function cron()
	{
		if (defined('CRON') OR defined('STDIN'))
		{
			$this->load->library('email');
			$this->load->helper('string');
			$this->load->helper('file');
			$this->load->module_model(FUEL_FOLDER, 'logs_model');

			$backup_config = $this->config->item('backup');
			$include_assets = ($method == '1' OR ($method =='index' AND $backup_config['backup_assets']));
			$download_path = $backup_config['db_backup_path'];
			
			if (!is_writable($download_path))
			{
				$this->output->set_output(lang('cron_db_folder_not_writable', $download_path));
				return;
			}

			// Load the DB utility class
			$this->load->dbutil();

			// Backup your entire database and assign it to a variable
			//$config = array('newline' => "\r", 'format' => 'zip');

			// need to do text here to make some fixes
			$db_back_prefs = $backup_config['db_backup_prefs'];
			$db_back_prefs['format'] = 'txt';
			$backup =& $this->dbutil->backup($db_back_prefs); 

			//fixes to work with PHPMYAdmin
			// $backup = str_replace('\\\t', "\t",	$backup);
			// $backup = str_replace('\\\n', '\n', $backup);
			// $backup = str_replace("\\'", "''", $backup);
			// $backup = str_replace('\\\\', '', $backup);

			$backup_date = date($backup_config['backup_file_date_format']);
			if ($backup_config['backup_file_prefix'] == 'AUTO')
			{
				$this->load->helper('url');
				$backup_config['backup_file_prefix'] = url_title($this->config->item('site_name', FUEL_FOLDER), '_', TRUE);
			}

			$filename = $backup_file_prefix.'_'.$backup_date.'.sql';
			
			if (!empty($backup_config['backup_zip']))
			{
				$this->load->library('zip');
				$this->zip->add_data($filename, $backup);

				// include assets folder
				if ($include_assets)
				{
					$this->zip->read_dir(assets_server_path());
				}
				$download_file = $download_path.$filename.'.zip';
			}
			else
			{
				$download_file = $download_path.$filename;
			}
			
			// write the zip file to a folder on your server. 
			if (!file_exists($download_file))
			{
				
				if (!empty($backup_config['backup_zip']))
				{
					$this->zip->archive($download_file);
				}
				else
				{
					write_file($download_file, $backup);
				}
				
				$output = ($include_assets) ? lang('cron_db_backup_asset', $filename) : lang('cron_db_backup', $filename);
				$this->logs_model->logit($output, 0); // the 0 is for the system as the user
				
				// send email if set in config
				if (!empty($backup_config['backup_cron_email']))
				{
					$this->email->to($backup_config['backup_cron_email']);
					$this->email->from($this->config->item('from_email', 'fuel'), $this->config->item('site_name', 'fuel'));
					$this->email->message($output);
					$this->email->subject(lang('cron_email_subject', $this->config->item('site_name', 'fuel')));
					
					if ($backup_config['backup_cron_email_file'])
					{
						$this->email->attach($download_file);
					}
					
					if ($this->email->send())
					{
						$output .= "\n".lang('cron_email', $backup_config['backup_cron_email']);
					}
					else
					{
						$output .= "\n".lang('cron_email_error', $backup_config['backup_cron_email']);
					}
				}
				
				// now delete old files
				if (!empty($backup_config['backup_days_to_keep']))
				{
					$files = get_dir_file_info($download_path);
					
					foreach($files as $file)
					{
						$file_date = substr(end(explode($file['name'], '_')), 0, 10);
						$file_date_ts = strtotime($file['date']);
						$compare_date = mktime(0, 0, 0, date('m'), date('j') - $backup_config['backup_days_to_keep'], date('Y'));
						
						if ($file_date_ts < $compare_date AND strncmp($backup_config['backup_file_prefix'], $file['name'], strlen($backup_config['backup_file_prefix'])) === 0)
						{
							@unlink($file['server_path']);
						}
					}
				}
				
			}
			else
			{
				$output = lang('cron_db_backed_up_already');
			}
			$this->output->set_output($output);
		}
	}
	
	protected function _filename($prefix, $date_format)
	{
		$backup_date = date($date_format);
		if ($prefix == 'AUTO')
		{
			$prefix = url_title($this->fuel->config('site_name'), '_', TRUE);
		}
		
		$filename = $prefix.'_'.$backup_date;
		return $filename;
	}
	
	protected function _zip($filename, $backup, $download_path, $download = TRUE)
	{
		$this->CI->load->library('zip');
		$this->CI->zip->add_data($filename, $backup);
		$download_file = $download_path.$filename.'.zip';
		
		// write the zip file to a folder on your server. 
		$archived = $this->CI->zip->archive($download_file); 
		
		if (!$archived) return FALSE;
		
		// download the file to your desktop. 
		if ($download)
		{
			$this->CI->zip->download($filename.'.zip');
		}
		return $archived;
	}
	
	protected function _write($path)
	{
		$this->CI->load->helper('file');
		return write_file($path, $backup);
	}
}



/* End of file Fuel_backup.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_backup.php */