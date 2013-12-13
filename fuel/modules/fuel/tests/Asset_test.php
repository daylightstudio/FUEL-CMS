<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asset_test extends Tester_base {
	
	public $init = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library('asset');
	}

	public function test_paths()
	{
		
		/*******************************************
		Common path tests
		********************************************/ 
		$path_test = array(
			'images' => array(
				'func' => 'img_path',
				'file' => 'test.jpg'
				),
			'css' => array(
				'func' => 'css_path',
				'file' => 'test.css'
				),
			'js' => array(
				'func' => 'js_path',
				'file' => 'test.js'
				),
			
			);
		
		foreach($path_test as $type => $t)
		{
			$this->_reset();
			
			$p = $expected = WEB_PATH.$this->init['assets_path'].$this->init['assets_folders'][$type].$t['file'];

			// test 1 WITHOUT timestampe cache breaker
			$test = $this->CI->asset->$t['func']($t['file']);
			$expected = $p;
			$this->run($test, $expected, 'Asset '.$t['func'].'() test 1');

			// test 2 WITH timestampe cache breaker
			$config = array('asset_append_cache_timestamp' => array($type));
			$this->_reset($config);
			$test = $this->CI->asset->$t['func']($t['file']);
			$expected = $p.'?c='.strtotime($this->init['assets_last_updated']);
			$this->run($test, $expected, 'Asset '.$t['func'].'() test 2');

			// test 3 absolute path
			$config = array('assets_absolute_path' => TRUE);
			$this->_reset($config);
			$test = $this->CI->asset->$t['func']($t['file']);
			$expected = 'http://'.$_SERVER['HTTP_HOST'].$p;
			$this->CI->unit->run($test, $expected, 'Asset '.$t['func'].'() test 2');
		}
	}
	
	public function test_css()
	{
		$this->_reset();
		$test = trim($this->CI->asset->css('test'));
		$expected = '<link href="'.WEB_PATH.'assets/css/test.css" media="all" rel="stylesheet"/>';
		$this->run($test, $expected, 'Asset css() test 1');
		
		// strip tabs to make it easier to test
		$test = strip_whitespace($this->CI->asset->css('test1, test2'));
		$expected = "<link href=\"".WEB_PATH."assets/css/test1.css\" media=\"all\" rel=\"stylesheet\"/>";
		$expected .= "<link href=\"".WEB_PATH."assets/css/test2.css\" media=\"all\" rel=\"stylesheet\"/>";
		$this->run($test, $expected, 'Asset css() test 2');
		
	}
	
	private function _reset($changed_config = array())
	{
		$config['assets_path'] = 'assets/';
		$config['assets_module_path'] = 'fuel/modules/{module}/assets/';
		$config['assets_server_path'] = WEB_ROOT.$config['assets_path'];
		$config['assets_module'] = '';
		$config['assets_folders'] = array(
			'images' => 'images/',
			'css' => 'css/',
			'js' => 'js/',
			'pdf' => 'pdf/',
			'swf' => 'swf/',
			'media' => 'media/',
			'captchas' => 'captchas/'
			);
		$config['assets_absolute_path'] = FALSE;
		$config['assets_last_updated'] = '00/00/0000 00:00:00';
		$config['asset_append_cache_timestamp'] = array();
		$config['assets_output'] = FALSE;
		$config['assets_cache_folder'] = 'cache/';
		$config['assets_gzip_cache_expiration'] = 3600;
		$config = array_merge($config, $changed_config);
		$this->init = $config;
		$this->CI->asset->initialize($this->init);
	}

}
