<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');

class Categories extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->config->module_load('blog', 'blog');
		$this->load->module_library('blog', 'fuel_blog');
		$this->load->module_helper('blog', 'blog');
	}
	
	function _remap($category = NULL)
	{
		$cache_id = fuel_cache_id();
		if ($cache = $this->fuel_blog->get_cache($cache_id))
		{
			$output =& $cache;
		}
		else
		{
			$vars = $this->_common_vars();
			$vars['pagination'] = '';
			
			// check if RSS feed
			if ($this->uri->rsegment(3) == 'feed')
			{
				
				$type = ($this->uri->rsegment(4) == 'atom') ? 'atom' : 'rss';
				
				// set the header type
				$this->fuel_blog->feed_header();
				
				// set the output
				$output = $this->fuel_blog->feed_output($type, $category);
			}
			else if (!empty($category) AND $category != 'index')
			{
				$category_obj = $this->fuel_blog->get_category($category);
				if (!isset($category_obj->id)) show_404();

				$vars['posts'] = $this->fuel_blog->get_category_posts($category);
				$vars['page_title'] = $this->fuel_blog->page_title(array($category_obj->name, lang('blog_categories_page_title')));
				$output = $this->_render('posts', $vars, TRUE);
			}
			else
			{
				$vars['categories'] = $this->fuel_blog->get_categories();
				$vars['page_title'] = lang('blog_categories_page_title');
				$output = $this->_render('categories', $vars, TRUE);
			}
			$this->fuel_blog->save_cache($cache_id, $output);
		}
		$this->output->set_output($output);
	}

}