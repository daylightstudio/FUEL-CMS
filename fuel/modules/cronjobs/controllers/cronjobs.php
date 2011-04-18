<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Cronjobs extends Fuel_base_controller {

	public $nav_selected = 'tools/cronjobs';
	public $view_location = 'cronjobs';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->_validate_user('cronjobs');
		$this->load->module_config(CRONJOBS_FOLDER, 'cronjobs');
		
		$this->load->library('cronjob');
		$this->load->library('validator');
		
		$cronjobs_config = $this->config->item('cronjobs');
		
		$this->js_controller_params['method'] = 'cronjobs';
		$cronjob_path = INSTALL_ROOT.$cronjobs_config['crons_folder'].'crontab.php';
		
		$config['cronfile'] = $cronjob_path;
		$config['mailto'] = $this->input->post('mailto');
		$config['user'] = $cronjobs_config['cron_user'];
		$config['sudo_pwd'] = $cronjobs_config['sudo_pwd'];
		$this->cronjob->initialize($config);
		
		if (!empty($_POST))
		{
			if ($this->input->post('action') == 'remove')
			{
				$this->cronjob->remove();
			}
			else
			{
				$mailto = $this->input->post('mailto');

				$num = count($_POST['command']);
				$line = '';

				for ($i = 0; $i < $num; $i++)
				{
					if (!empty($_POST['command'][$i]) AND $_POST['command'][$i] != 'command')
					{
						$min = ($_POST['min'][$i] == 'min') ? NULL : $_POST['min'][$i];
						$hour = ($_POST['hour'][$i] == 'hour') ? NULL : $_POST['hour'][$i];
						$month_day = ($_POST['month_day'][$i] == 'month day') ? NULL : $_POST['month_day'][$i];
						$month_num = ($_POST['month_num'][$i] == 'month num') ? NULL : $_POST['month_num'][$i];
						$week_day = ($_POST['week_day'][$i] == 'week day') ? NULL : $_POST['week_day'][$i];
						$command = $_POST['command'][$i];
						$this->cronjob->add($min, $hour, $month_day, $month_num, $week_day, $command);
					}
				}

				$this->cronjob->create();

			}
			
		}
		
		
		$cronjob_file = $this->cronjob->view();
		$action = 'edit';
		$mailto = '';
		
		if (file_exists($cronjob_path) AND empty($cronjob_file))
		{
			// turn on output buffering so that php will work inside the crontab.php
			ob_start();
			include($cronjob_path);
			$cronjob_file = ob_get_clean();
			$cronjob_file = trim($cronjob_file);
			$action = 'create';
		}
		$cronjob_lines = array();
		
		if (!empty($cronjob_file))
		{
			if (is_string($cronjob_file))
			{
				$cronjob_lines = explode(PHP_EOL, $cronjob_file);
			}
			else
			{
				$cronjob_lines = (array) $cronjob_file;
			}
		}
		
		// clean up whitespace
		$cronjob_lines = array_map('trim', $cronjob_lines);

		if (!empty($cronjob_lines) AND strncasecmp($cronjob_lines[0], 'MAILTO', 6) === 0)
		{
			$mailto_arr = explode('=', $cronjob_lines[0]);
			$mailto = (count($mailto_arr) == 2) ? trim($mailto_arr[1]) : '';
			unset($cronjob_lines[0]);
			$cronjob_lines = array_values($cronjob_lines); // reset
		}
		
		
		$vars['cronjob_path'] = $cronjob_path;
		$vars['action'] = $action;
		$vars['mailto'] = $mailto;
		$vars['cronjob_lines'] = $cronjob_lines;
		$this->_render('cronjobs', $vars);
	}
}
/* End of file cronjobs.php */
/* Location: ./fuel/modules/cronjobs/controllers/cronjobs.php */
