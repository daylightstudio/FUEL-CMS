<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');
class Dashboard extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->config->module_load('blog', 'blog');
		$this->view_location = 'blog';
	}
	
	function index()
	{
		$vars['posts'] = $this->fuel->blog->get_recent_posts();
		$this->load->view('_admin/dashboard', $vars);
	}

}