<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');

class Feed extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
		
		// set the header type
		$this->fuel_blog->feed_header();
	}
	
	function index()
	{
		$this->rss();
	}
	
	function atom()
	{
		print($this->fuel_blog->feed_output('atom'));
	}

	function rss()
	{
		print($this->fuel_blog->feed_output('rss'));
	}

}