<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');
require_once(MODULES_PATH.TESTER_FOLDER.'/libraries/Tester_base.php');

class Tester extends Fuel_base_controller {
	
	public $module_uri = 'tester';
	public $nav_selected = 'tools/tester';
	
	function __construct()
	{
		$validate = (defined('STDIN')) ? FALSE : TRUE;
		parent::__construct($validate);
		
		// must load first
		$this->load->library('unit_test');
		
		if ($validate)
		{
			$this->_validate_user('tools/tester');
		}
	}
	
	function index()
	{
		$test_list = $this->fuel->tester->get_tests();
		$vars['test_list'] = $test_list;
		$vars['form_action'] = 'tools/tester/run';
		$crumbs = array('tools' => lang('section_tools'), lang('module_tester'));
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_tester');
		$this->fuel->admin->render('tester', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}
	
	function run()
	{
		$is_cli = $this->fuel->tester->is_cli();
		
		if (empty($_POST) AND !$is_cli)
		{
			redirect(fuel_url('tools/tester'));
		}
		
		$tests = array();
		if ($is_cli)
		{
			if (empty($_SERVER['argv'][2]))
			{
				$this->output->set_output(lang('tester_no_cli_arguments'));
				return;
			}
			
			$module = $_SERVER['argv'][2];
			$folders = array();
			
			if (isset($_SERVER['argv'][3]))
			{
				// no loop through the argv arguments to get the folders/tests
				for ($i = 3; $i < count($_SERVER['argv']); $i++)
				{
					if (!empty($_SERVER['argv'][$i]))
					{
						$folders[] = $_SERVER['argv'][$i];
					}
				}
			}
			
			$tests = $this->fuel->tester->get_tests($module, $folders, TRUE);
			
		}
		else
		{
			$tests = $this->input->post('tests');
		}
		
		$vars = array();
		
		if (empty($tests))
		{
			$tests = $this->input->post('tests_serialized');
			if (empty($tests))
			{
				redirect(fuel_url('tools/tester'));
			}
			else
			{
				$tests = unserialize(base64_decode($tests));
			}
		}
		$vars['results'] = $this->fuel->tester->run($tests);
		
		if ($is_cli)
		{
			$this->load->module_view(TESTER_FOLDER, 'tester_results_cli', $vars);
		}
		else
		{
			$vars['tests_serialized'] = base64_encode(serialize($tests));

			$crumbs = array('tools' => lang('section_tools'), lang('module_tester'), lang('tester_results'));
			$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_tester');
			$this->fuel->admin->render('tester_results', $vars, Fuel_admin::DISPLAY_NO_ACTION);
		}
	}

}

/* End of file tester.php */
/* Location: ./fuel/modules/tester/controllers/tester.php */
