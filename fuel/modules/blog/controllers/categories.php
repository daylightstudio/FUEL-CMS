<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');

class Categories extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->module_helper('blog', 'blog');
	}
	
	function _remap($category = NULL)
	{
		$cache_id = fuel_cache_id();
		if ($cache = $this->fuel->blog->get_cache($cache_id))
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
				$this->fuel->blog->feed_header();
				
				// set the output
				$output = $this->fuel->blog->feed_output($type, $category);
			}
			else if (!empty($category) AND $category != 'index')
			{
				$category_obj = $this->fuel->blog->get_category($category);
				if (!isset($category_obj->id)) show_404();

				// run before_posts_by_date hook
				$hook_params = array('category' => $category_obj, 'category_slug' => $category);
				$this->fuel->blog->run_hook('before_posts_by_category', $hook_params);
				
				$vars = array_merge($vars, $hook_params);
				$vars['posts'] = $this->fuel->blog->get_category_posts($category);
				$vars['page_title'] = $this->fuel->blog->page_title(array($category_obj->name, lang('blog_categories_page_title')));
				$output = $this->_render('posts', $vars, TRUE);
			}
			else
			{
				$vars['categories'] = $this->fuel->blog->get_categories();
				$vars['page_title'] = lang('blog_categories_page_title');
				$output = $this->_render('categories', $vars, TRUE);
			}
			$this->fuel->blog->save_cache($cache_id, $output);
		}
		$this->output->set_output($output);
	}

}