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
		//$sitemap = $this->fuel_search->sitemap_pages();
		
		// $this->fuel_search->index();
		// $this->fuel_search->display_log();
		//$q = 'lorem';
		// $q = 'test';
		$q = $this->input->get('q');

		$vars['results'] = $this->fuel->search->query($q);
		$vars['q'] = $q;
		$this->load->view('results', $vars);
	}
	
}
/* End of file backup.php */
/* Location: ./fuel/modules/backup/controllers/backup.php */

