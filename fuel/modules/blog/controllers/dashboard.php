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
		$this->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$vars['posts'] = $this->fuel_blog->get_recent_posts();
		$this->load->view('dashboard', $vars);
	}

}