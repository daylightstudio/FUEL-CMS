<?php
class Search extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		if (!defined('SEARCH_FOLDER')) show_404();
	}
	
	function index()
	{
		$this->load->helper('text');
		$this->load->library('pagination');

		$q = $this->input->get('q');
		
		$pagination = $this->fuel->search->config('pagination');

		$per_page = $pagination['per_page'];
		$offset = $this->input->get('per_page');

		if (strlen($q) < $this->fuel->search->config('min_length_search'))
		{
			$results = array();
			$count = 0;
		}
		else
		{
			$results = $this->fuel->search->query($q, $per_page, $offset);
			$count = $this->fuel->search->count();
		}
		
		$config = $pagination;
	
		$config['base_url'] = current_url().'?q='.$q;
		$config['total_rows'] = $count;
		$config['page_query_string'] = TRUE;
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config); 
		

		// set variables for view
		$vars['results'] = $results;
		$vars['count'] = $count;
		$vars['q'] = $q;
		$vars['pagination'] = $this->pagination->create_links();
		
		$view = $this->fuel->search->config('view');
		$params = array();
		if (is_array($view))
		{
			$module = key($view);
			$params['views_path'] = MODULES_PATH.$module.'/views/';
			$params['view_module'] = $module;
			$view = current($view);
		}
		
		$this->fuel->pages->render($view, $vars, $params);
	}
	
}
/* End of file backup.php */
/* Location: ./fuel/modules/backup/controllers/backup.php */

