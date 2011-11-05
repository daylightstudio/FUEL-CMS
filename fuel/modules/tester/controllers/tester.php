<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');
require_once(MODULES_PATH.TESTER_FOLDER.'/libraries/Tester_base.php');

class Tester extends Fuel_base_controller {
	public $module_uri = 'tester';
	public $nav_selected = 'tools/tester';
	
	function __construct()
	{
		parent::__construct();
		// must load first
		$this->load->library('unit_test');
		$this->load->module_language(TESTER_FOLDER, 'tester');
		$this->_validate_user('tools/tester');
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
		if (empty($_POST)) redirect(fuel_url('tools/tester'));

		$vars = array();
		$tests = $this->input->post('tests');
		
		if (empty($tests) )
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
		$vars['tests_serialized'] = base64_encode(serialize($tests));
		
		$crumbs = array('tools' => lang('section_tools'), lang('module_tester'), lang('tester_results'));
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_tester');
		$this->fuel->admin->render('tester_results', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}

}

/* End of file tester.php */
/* Location: ./fuel/modules/tester/controllers/tester.php */
