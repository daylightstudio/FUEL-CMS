<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_assets_test extends Tester_base {
	
	public $init = array();
	private $nav = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setup()
	{
	}
	
	public function test_basic()
	{
		$params['upload_path'] = assets_server_path('images/__fuel_tmp__/'); // create a tmp folder
		$params['files'] = array(
				'img1' => assets_server_path('widgicorp_logo.png', 'images'),
			
			);
		$this->CI->fuel->assets->upload($params);
	}

}
