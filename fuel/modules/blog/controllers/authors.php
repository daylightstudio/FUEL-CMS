<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');

class Authors extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _remap($id = NULL)
	{
		$cache_id = fuel_cache_id();
		if ($cache = $this->fuel->blog->get_cache($cache_id))
		{
			$output =& $cache;
		}
		else
		{
			$vars = $this->_common_vars();
			if ($id == 'posts')
			{
				$author_id = (int) $this->uri->rsegment(3);
				
				$author = $this->fuel->blog->get_user($author_id);
				if (empty($author)) show_404();
				$where['author_id'] = $author_id;
				
				// run before_posts_by_date hook
				$hook_params = array('author' => $author);
				$this->fuel->blog->run_hook('before_posts_by_author', $hook_params);
				
				$vars['posts'] = $this->fuel->blog->get_posts($where);
				$vars['page_title'] = lang('blog_author_posts_page_title', $author->name);
				$output = $this->_render('posts', $vars, TRUE);
			}
			else if (!empty($id) && $id != 'index')
			{
				$author = $this->fuel->blog->get_user($id);
				if (empty($author)) show_404();
				$vars['author'] = $author;
				$vars['page_title'] = $author->name;
				$output = $this->_render('author', $vars, TRUE);
			}
			else
			{
				$vars['authors'] = $this->fuel->blog->get_users();
				$vars['page_title'] = lang('blog_authors_list_page_title');
				$output = $this->_render('authors', $vars, TRUE);
			}
			$this->fuel->blog->save_cache($cache_id, $output);
		}
		$this->output->set_output($output);
	}

}