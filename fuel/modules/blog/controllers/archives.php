<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');
class Archives extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$cache_id = fuel_cache_id();
		$vars = $this->_common_vars();
		if ($cache = $this->fuel_blog->get_cache($cache_id))
		{
			$output =& $cache;
		}
		else
		{
			$vars['archives_by_month'] = $this->fuel_blog->get_post_archives();
			$vars['page_title'] = lang('blog_archives_page_title');
			$output = $this->_render('archives', $vars, TRUE);
			$this->fuel_blog->save_cache($cache_id, $output);
		}
		
		$this->output->set_output($output);
	}
}