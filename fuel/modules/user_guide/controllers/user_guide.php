<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class User_guide extends Fuel_base_controller {
	
	public $current_page;
	
	function __construct()
	{
		parent::__construct(FALSE);
		$this->load->module_config(USER_GUIDE_FOLDER, 'user_guide');
		if ($this->config->item('user_guide_authenticate'))
		{
			$this->_check_login();
		}
	}
	
	function _remap()
	{
		$this->load->module_helper(USER_GUIDE_FOLDER, 'user_guide');
		$this->load->module_library(FUEL_FOLDER, 'fuel_pagevars');
		$this->load->helper('text');
		$this->load->helper('inflector');
		$this->load->helper('utility');
		$this->current_page = $this->_get_page();
		if (empty($this->current_page)) $this->current_page = 'home';
		$this->fuel_pagevars->vars_path = USER_GUIDE_PATH.'views/_variables/';
		$vars = array();
		
		// get modules
		$modules = array('', 'fuel');
		$modules = array_merge($modules, $this->config->item('modules_allowed', 'fuel'));

		$vars = $this->_get_vars($this->current_page);
		
		foreach($modules as $m)
		{
			if ((!$this->config->item('user_guide_authenticate') OR $this->fuel_auth->has_permission('user_guide_'.$m)) AND file_exists(MODULES_PATH.$m.'/views/_docs/index'.EXT)) 
			{
				$module_view = $this->load->module_view($m, '_docs/index', array(), TRUE);
				$mod_page_title = $this->_get_page_title($module_view);
				$vars['modules'][$m] = (!empty($mod_page_title)) ? $mod_page_title : humanize($m).' Module';
			}
		}
		
		// render page
		// pull from modules folder if URI says so	
		$uri_path_index = count(explode('/', $this->config->item('user_guide_root_url'))) + 1;
		$module_page = uri_path(FALSE, $uri_path_index);
		$module_view_path = (!empty($module_page)) ? '_docs/'.$module_page : '_docs/index';
		
		if ($this->_get_page_segment(1) == 'modules' AND ($this->_get_page_segment(2) AND file_exists(MODULES_PATH.$this->_get_page_segment(2).'/views/'.$module_view_path.EXT)))
		{
			$module = $this->_get_page_segment(2);
			if (!$this->config->item('user_guide_authenticate') OR $this->fuel_auth->has_permission('user_guide_'.$module))
			{
				$vars['body'] = $this->load->module_view($module, $module_view_path, $vars, TRUE);
				if ($this->_get_page_segment(3))
				{
					$vars['sections'] = array($vars['modules'][$module] => 'modules/'.$module);
				}
			}
		}
		else
		{
			if (!is_file(MODULES_PATH.USER_GUIDE_FOLDER.'/views/'.$this->current_page.EXT))
			{
				show_404();
			}
			$vars['body'] = $this->load->module_view(USER_GUIDE_FOLDER, $this->current_page, $vars, TRUE);
			if ($this->_get_page_segment(2))
			{
				$vars['sections'] = $this->_get_breadcrumb($this->current_page);
			}
		}
		$vars['page_title'] = $this->_get_page_title($vars['body']);
		
		$this->load->module_view(USER_GUIDE_FOLDER, '_layouts/user_guide', $vars);
	}
	
	function _get_page()
	{
		$uri = uri_path(FALSE);
		$root_url = $this->config->item('user_guide_root_url');
		if (substr($root_url, -1) == '/') $root_url = substr($root_url, 0, (strlen($root_url) -1));
		$new_uri = preg_replace('#^'.$root_url.'#', '', $uri);
		return $new_uri;
	}
	
	function _get_page_segment($segment)
	{
		$segment = $segment - 1;

		// clean off beginning and ending slashes
		$page = preg_replace('#^(\/)?(.+)(\/)?$#', '$2', $this->current_page);
		$segs = explode('/', $page);
		if (!empty($segs[$segment]))
		{
			return $segs[$segment];
		}
		return FALSE;
	}
	
	function _get_page_title($page)
	{
		preg_match('#<h1>(.+)<\/h1>#U', $page, $matches);
		if (!empty($matches[1]))
		{
			return strip_tags($matches[1]);
		}
		return '';
		
	}
	
	function _get_breadcrumb($page)
	{
		$vars = $this->_get_vars($page);
		$page_arr = explode('/', $page);
		if (count($page_arr) == 1) return '';
		array_pop($page_arr);
		$prev_page = implode('/', $page_arr);

		if (is_file(MODULES_PATH.USER_GUIDE_FOLDER.'/views'.$prev_page.EXT))
		{
			
			$prev_view = $this->load->module_view(USER_GUIDE_FOLDER, $prev_page, $vars, TRUE);
			//array($this->_get_page_title($prev_view) => $prev_page);
			$vars['breadcrumb'][$this->_get_page_title($prev_view)] = $prev_page;
		}
		return $vars['breadcrumb'];
	}
	
	function _get_vars($page)
	{
		$vars = $this->fuel_pagevars->view_variables($page, 'user_guide');
		$vars['modules'] = array();
		$vars['site_docs'] = '';
		$vars['use_search'] = TRUE;
		$vars['use_breadcrumb'] = TRUE;
		$vars['use_nav'] = TRUE;
		$vars['use_footer'] = TRUE;
		$vars['sections'] = array();
		$vars['breadcrumb'] = array();
		return $vars;
	}
}

/* End of file user_guide.php */
/* Location: ./fuel/modules/user_guide/controllers/user_guide.php */