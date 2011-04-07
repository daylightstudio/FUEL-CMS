<?php
require_once(MODULES_PATH.'/blog/libraries/Blog_base_controller.php');

class Search extends Blog_base_controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _remap($method)
	{
		$this->load->library('pagination');
		$this->load->helper('text');
		
		$this->uri->init_get_params();
		$q = $this->uri->segment(3);
		$use_get = FALSE;
		
		if (empty($q))
		{
			$q = $this->input->post('q');
		}
		
		if (empty($q))
		{
			$use_get = TRUE;
			$q = $this->input->get('q');
		}
		
		// initiate this here first
		$vars = $this->_common_vars();
		$vars['posts'] = array();
		$vars['page_title'] = lang('blog_search_page_title', '&ldquo;'.$q.'&rdquo;');
		if (!empty($q))
		{
			$limit = $this->fuel_blog->settings('per_page');
			if ($use_get)
			{
				$this->config->set_item('enable_query_strings', TRUE);
				$config['base_url'] = $this->fuel_blog->url('search?q='.$q);
			}
			else
			{
				$config['base_url'] = $this->fuel_blog->url('search');
			}
			$config['total_rows'] = count($this->fuel_blog->search_posts($q));
			$config['uri_segment'] = 3;
			$config['page_query_string'] = $use_get;
			$config['per_page'] = $limit;
			$config['prev_link'] = lang('blog_prev_page');
			$config['next_link'] = lang('blog_next_page');
			$config['first_link'] = lang('blog_first_link');
			$config['last_link'] = lang('blog_last_link');;

			$this->pagination->initialize($config); 

			$offset = ($use_get) ? $this->input->get('per_page') : $this->uri->segment($config['uri_segment']);
			
			$vars['posts'] = $this->fuel_blog->search_posts($q, 'date_added desc', $limit, $offset);
			$vars['limit'] = $limit;
			$vars['offset'] = $offset;
			$vars['pagination'] = $this->pagination->create_links();
		}
		$vars['q'] = $q;
		$vars['searched'] = (!empty($q));
		$vars['search_input'] = $this->fuel_blog->block('search');
		$this->_render('search', $vars);
	}
}