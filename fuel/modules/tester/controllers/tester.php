<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');
require_once(MODULES_PATH.TESTER_FOLDER.'/libraries/Tester_base.php');

class Tester extends Fuel_base_controller {

	public $view_location = 'tester';
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
		$this->load->helper('directory');
		$this->load->helper('inflector');
		
		// get tests from application folder
		$test_list = array();
		$app_tests = $this->_get_tests(APPPATH.'/tests/', 'application');
		
		// get tests for modules
		$modules = $this->config->item('modules_allowed', 'fuel');
		$modules[] = 'fuel';
		
		$test_list = array();
		foreach($modules as $module)
		{
			$module_test_folder = Fuel_modules::module_path($module).'/tests/';
			$module_tests_list = $this->_get_tests($module_test_folder, $module);

			// merge the arrays with a + to preserve keys
			if (!empty($module_tests_list))
			{
				$test_list = $test_list + $module_tests_list;
			}
		}

		// merge the arrays with a + to preserve keys
		if (!empty($app_tests))
		{
			$test_list = $test_list + $app_tests;
		}
		asort($test_list);
		
		$vars['test_list'] = $test_list;
		$this->_render('tester', $vars);
	}
	
	function run()
	{
		if (empty($_POST)) redirect(fuel_url('tools/tester'));
		$this->load->helper('inflector');
		$vars = array();
		$tmpl = $this->load->module_view(TESTER_FOLDER, 'report_template', $vars, TRUE);
		$this->unit->set_template($tmpl);
		$this->load->module_config(TESTER_FOLDER, 'tester');
		$tester_config = $this->config->item('tester');

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
		$vars['results'] = array();
		
		foreach($tests as $test)
		{
			$this->unit->reset();
			$test_class = str_replace(EXT, '', end(explode('/', $test)));
			$test_arr = explode(':', $test);
			$test = end($test_arr);
			$module = $test_arr[0];
			require_once($test);

			$test_obj = new $test_class();
			$test_obj->setup();
			$methods = get_class_methods($test_obj);
			foreach($methods as $method)
			{
				if (preg_match('/^test_(.+)/', $method))
				{
					$test_obj->$method();
				}
			}
			$test_obj->tear_down();
			
			$key = '<strong>'.$module.':</strong> '.humanize($test_class);
			$vars['results'][$key] = array();
			$vars['results'][$key]['report'] = $this->unit->report();
			$vars['results'][$key]['raw'] = $this->unit->result();
			$vars['results'][$key]['passed'] = 0; // initialize
			$vars['results'][$key]['failed'] = 0; // initialize
			
		}
		
		
		//$results = $this->unit->result();

		$results = $vars['results'];

		
		$vars['total_passed'] = 0;
		$vars['total_failed'] = 0;
		$lang_results = lang('ut_result');
		$lang_passed = lang('ut_passed');
		$lang_failed = lang('ut_failed');

		foreach($vars['results'] as $key => $result)
		{
			foreach($result['raw'] as $k => $v)
			{
				if (strtolower($v[$lang_results]) == strtolower($lang_passed))
				{
					$vars['total_passed']++;
					$vars['results'][$key]['passed']++;
				}
				else
				{
					$vars['total_failed']++;
					$vars['results'][$key]['failed']++;
				}
			}
		}
		
		$vars['tests_serialized'] = base64_encode(serialize($tests));
		$this->_render('tester_results', $vars);
	}

	function _get_tests($folder, $module = NULL)
	{
		$return = array();
		if (file_exists($folder))
		{
			$tests = directory_to_array($folder);
			foreach($tests as $test)
			{
				$dir = '/'.$test;
				if (substr($test, -9) ==  '_test.php')
				{
					$val = str_replace(EXT, '', end(explode('/', $test)));
					$return[$module.':'.$dir] = (!empty($module)) ? '<strong>'.$module.':</strong> '.humanize($val) : humanize($val);
				}
			}
		}
		return $return;
	}
}

/* End of file tester.php */
/* Location: ./fuel/modules/tester/controllers/tester.php */
