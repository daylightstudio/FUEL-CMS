<?php
class Blog_base_controller extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		if (!$this->fuel->auth->accessible_module('blog'))
		{
			show_404();
		}
		$this->load->module_helper(BLOG_FOLDER, 'blog');
		$this->load->module_helper(BLOG_FOLDER, 'social');
	}
	
	function _common_vars()
	{
		$vars['blog'] =& $this->fuel->blog;
		$vars['is_blog'] = TRUE;
		$vars['page_title'] = '';
		//$this->load->vars($vars);
		return $vars;
	}
	
	function _render($view, $vars = array(), $return = FALSE, $layout = '')
	{
		if (empty($layout)) $layout = '_layouts/'.$this->fuel->blog->layout();

		// get any global variables for the headers and footers
		$_vars = $this->fuel->pagevars->retrieve(uri_path());
		
		if (is_array($_vars))
		{
			$vars = array_merge($_vars, $vars);
		}
		$view_folder = $this->fuel->blog->theme_path();
		$vars['CI'] =& get_instance();

		$page = $this->fuel->pages->create();
		
		if (!empty($layout))
		{
			$vars['body'] = $this->load->module_view($this->fuel->blog->settings('theme_module'), $view_folder.$view, $vars, TRUE);
			$view = $this->fuel->blog->theme_path().$this->fuel->blog->layout();
		}
		else
		{
			$view = $view_folder.$view;
		}

		$output = $this->load->module_view($this->fuel->blog->settings('theme_module'), $view, $vars, TRUE);
		$output = $page->fuelify($output);

		if ($return)
		{
			return $output;
		}
		else
		{
			$this->output->set_output($output);
		}
	}
}