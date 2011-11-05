<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Blog_settings_model extends MY_Model {
	public $key_field = 'name';
	public $required = array();
	public $has_auto_increment = FALSE;
	
	function __construct()
	{
		include(BLOG_PATH.'config/blog.php');
		$tables = $config['tables'];
		parent::__construct($tables['blog_settings']); // table name
	}
	
}