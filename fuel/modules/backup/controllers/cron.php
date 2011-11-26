<?php
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
 * Cron backup Controller
 *
 * @package		FUEL CMS
 * @subpackage	Controller
 * @category	Controller
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/backup
 */

// --------------------------------------------------------------------

class Cron extends CI_Controller  {
	
	function __construct()
	{
		parent::__construct();
		$this->load->module_library(FUEL_FOLDER, 'fuel');
	}
	
	function _remap($method)
	{
		// check for CRON OR STDIN constants
		if (defined('CRON') OR defined('STDIN'))
		{
			// set assets flag
			$include_assets = ($method == '1' OR ($method =='index' AND $this->fuel->backup->config('include_assets')));
			
			if (!empty($include_assets))
			{
				$this->fuel->backup->include_assets = TRUE;
			}
			//$this->fuel->backup->allow_overwrite = TRUE;
			$this->fuel->backup->download = FALSE;
			
			// perform backup
			if (!$this->fuel->backup->do_backup())
			{
				$output = $this->fuel->backup->errors(TRUE);
			}
			else
			{
				$backup_data = $this->fuel->backup->backup_data();
				$file_name = $backup_data['file_name'];
				$download_path = $backup_data['full_path'];
				
				// set initial output value
				$output = ($include_assets) ? lang('cron_db_backup_asset', $file_name) : lang('cron_db_backup', $file_name);
				

				if ($this->fuel->backup->config('cron_email'))
				{
					
					// set parameters for notification
					$params['to'] = $this->fuel->backup->config('cron_email');
					$params['message'] = $output;
					$params['subject'] = lang('cron_email_subject', $this->fuel->config('site_name'));
					$params['use_dev_mode'] = FALSE; // must be set for emails to always go to what is sent in the backup config
					if ($this->fuel->backup->config('cron_email_file'))
					{
						$params['attachments'] = $download_path;
					}

					if ($this->fuel->notification->send($params))
					{
						$output .= "\n".lang('cron_email', $this->fuel->backup->config('cron_email'));
					}
					else
					{
						$output .= "\n".lang('cron_email_error', $this->fuel->backup->config('cron_email'));
					}
					exit($output);
				}
				
				if ($this->fuel->backup->config('days_to_keep'))
				{
					$files = get_dir_file_info($this->fuel->backup->backup_path);

					foreach($files as $file)
					{
						$file_date = substr(end(explode($file['name'], '_')), 0, 10);
						$file_date_ts = strtotime($file['date']);
						$compare_date = mktime(0, 0, 0, date('m'), date('j') - $this->fuel->backup->config('days_to_keep'), date('Y'));
						
						if ($file_date_ts < $compare_date AND strncmp($this->fuel->backup->config('file_prefix'), $file['name'], strlen($this->fuel->backup->config('file_prefix'))) === 0)
						{
							@unlink($file['server_path']);
						}
					}
					// log message
					$msg = lang('data_backup');
					$this->fuel->logs->write($msg);
				}
			}
			$this->output->set_output($output);
		}
	}
	
}

/* End of file cron.php */
/* Location: ./fuel/modules/backup/controllers/cron.php */