<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Blog_settings_model extends MY_Model {
	public $key_field = 'name';
	public $required = array();
	public $has_auto_increment = FALSE;
	
	function __construct()
	{
		$CI =& get_instance();
		$CI->config->module_load(BLOG_FOLDER, BLOG_FOLDER);
		$tables = $CI->config->item('tables');
		parent::__construct($tables['blog_settings']); // table name
	}
	
}