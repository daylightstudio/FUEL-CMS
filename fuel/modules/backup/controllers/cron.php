<?php
class Cron extends CI_Controller  {
	
	function __construct()
	{
		parent::__construct();
		$this->config->load('backup');
		$this->config->module_load(FUEL_FOLDER, 'fuel', TRUE);
		$this->load->language('backup');

	}
	
	function _remap($method){
		if (defined('CRON'))
		{
			$this->load->library('email');
			$this->load->helper('string');
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
			$backup = str_replace('\\\t', "\t",	$backup);
			$backup = str_replace('\\\n', '\n', $backup);
			$backup = str_replace("\\'", "''", $backup);
			$backup = str_replace('\\\\', '', $backup);

			// load the file helper and write the file to your server
			$this->load->helper('file');
			$this->load->library('zip');

			$backup_date = date('Y-m-d');
			$filename = $backup_config['backup_file_prefix'].'_'.$backup_date.'.sql';
			$this->zip->add_data($filename, $backup);
			
			// include assets folder
			if ($include_assets)
			{
				$this->zip->read_dir(assets_server_path());
			}
			
			$download_file = $download_path.$filename.'.zip';
			
			// write the zip file to a folder on your server. 
			if (!file_exists($download_file))
			{
				$this->zip->archive($download_file);
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
			}
			else
			{
				$output = lang('cron_db_backed_up_already');
			}
			$this->output->set_output($output);
		}
	}
	
}

/* End of file cron.php */
/* Location: ./modules/fuel/controllers/cron.php */