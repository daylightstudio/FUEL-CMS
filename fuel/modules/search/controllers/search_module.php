<?php
require_once(FUEL_PATH.'/controllers/module.php');

class Search_module extends Module {
	
	public $module = 'search'; // set here so the route can be tools/search
	
	function reindex()
	{
		$crumbs = array('tools' => lang('section_tools'), 'tools/search' => lang('module_search'), 'Reindex');
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_search');
		
		$vars = array();
		$this->fuel->admin->render('_admin/reindex', $vars, Fuel_admin::DISPLAY_DEFAULT, SEARCH_FOLDER);
		
	}
	
	function index_site()
	{
		$pages = $this->input->get_post('pages');
		
		if ($pages)
		{
			if (!is_array($pages))
			{
				$pages = explode(',', $this->input->get_post('pages'));
				$vars['crawled'] = $this->fuel->search->index($pages, 'pages', FALSE);
			}
		}
		else
		{
			$vars['crawled'] = $this->fuel->search->index(FALSE, 'pages', TRUE);
		}
		
		//$vars['log'] = $this->fuel->search->logs();
		$vars['log_msg'] = $this->fuel->search->display_log('all', 'span', TRUE);
		$this->load->module_view(SEARCH_FOLDER, '_admin/index_results', $vars);
	}
	
}
/* End of file backup.php */
/* Location: ./fuel/modules/backup/controllers/backup.php */