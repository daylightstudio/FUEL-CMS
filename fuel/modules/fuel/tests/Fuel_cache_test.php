<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Fuel_test_base.php');

class Fuel_cache_test extends Fuel_test_base {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setup()
	{
		parent::setup();
		$this->CI->load->helper('file');
		
		// clear all cache files first
		$this->fuel->cache->clear_all();
		
	}
	
	public function test_create_id()
	{
		// configure language to only be one
		$this->fuel->language->set_options(array('english' => 'English'));

		// check with no location value passed... should be the current URI location
		$test = $this->fuel->cache->create_id();
		$expected = 'fuel.tools.tester.run.english';
		$this->run($test, $expected, 'Test creating cache ID with NO location parameter passed');

		// check with location value passed
		$test = $this->fuel->cache->create_id('test/method');
		$expected = 'test.method.english';
		$this->run($test, $expected, 'Test creating cache ID WITH a location parameters passed');
	}

	public function test_saving_cache()
	{
		// cache a file to test against
		$cache_id = $this->fuel->cache->create_id();
		$to_cache = 'This is a test';
		$this->fuel->cache->save($cache_id, $to_cache);
		
		// check that .cache and .exp file are written
		$cached_file_name = $this->CI->config->item('cache_path').md5($cache_id);
		$test = file_exists($cached_file_name.'.cache') AND file_exists($cached_file_name.'.exp');
		$expected = TRUE;
		$this->run($test, $expected, 'Test saving a cached file writes .cache and .exp files');

		// test the contents are as they should be
		$cached_file_name = $this->CI->config->item('cache_path').md5($cache_id);
		$test = read_file($cached_file_name.'.cache');
		$expected = serialize($to_cache);
		$this->run($test, $expected, 'Test the content of the .cache file is serialized correctly');

		// test the contents are as they should be
		$cached_file_name = $this->CI->config->item('cache_path').md5($cache_id);
		$test = read_file($cached_file_name.'.exp');
		$expected = now() + 3600;
		$this->run($test, $expected, 'Test the content of the .exp file is serialized correctly');
		
	}
	
	public function test_is_cached()
	{
		// cache a file to test against
		$cache_id = $this->fuel->cache->create_id();
		$test = $this->fuel->cache->is_cached($cache_id);
		$expected = TRUE;
		$this->run($test, $expected, 'Test is_cached()');
	}

	public function test_clear_file_cached()
	{
		// cache a file to test against
		$cache_id = $this->fuel->cache->create_id();
		$this->fuel->cache->clear_file($cache_id);
		$test = $this->fuel->cache->is_cached($cache_id);
		$expected = FALSE;
		$this->run($test, $expected, 'Test clearing a file from cache');
		
		// check that .cache and .exp file are written
		$cached_file_name = $this->CI->config->item('cache_path').md5($cache_id);
		$test = file_exists($cached_file_name.'.cache') AND file_exists($cached_file_name.'.exp');
		$expected = FALSE;
		$this->run($test, $expected, 'Test .cache and .exp files are properly removed');
	}
	
	public function  test_page_cache()
	{
		// create a page so we can load it to create the cached files
		$location = 'test-cache';
		$page = $this->fuel->pages->create($location);
		$page->layout = 'none';
		$page_vars = array('body' => 'This is a test {date("Y-m-d")}', 'blocks' => FALSE);
		
		$page->add_variables($page_vars);
		$page->save();
		
		// now load the page to cache it
		$page_contents = $this->load_page($location);

		$cache_id = $this->fuel->cache->create_id($location);
		$test = $this->fuel->cache->is_cached($cache_id, $this->fuel->config('page_cache_group'));
		$expected = TRUE;
		$this->run($test, $expected, 'Test that pages are being cached');

		// check compiled folder to see how if any files exists
		$dwoo = $this->CI->config->item('cache_path').'dwoo/compiled/';
		$files = get_filenames($dwoo);
		
		$test = count($files) > 1; // take into account index.html
		$expected = TRUE;
		$this->run($test, $expected, 'Test that the compiled template was created', '****May fail if run via CLI because the folders can\'t be removed****');
		
		
		// now clear the compiled folder
		$this->fuel->cache->clear_compiled();
		$files = directory_to_array($dwoo);
		
		$test = count($files) == 1;
		$expected = TRUE;
		$this->run($test, $expected, 'Test that the compiled template directory was deleted');
		// remove the page... gets deleted with closing of script and database... cache still exists because of permissions issue
		//$page->delete();
	}

	public function tear_down()
	{
		parent::tear_down();
		$this->fuel->cache->clear_all();
	}


}
