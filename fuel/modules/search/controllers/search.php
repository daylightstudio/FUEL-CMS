<?php
class Search extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		if (!defined('SEARCH_FOLDER')) show_404();
		$this->load->library('fuel_search');
	}
	
	function index()
	{
		$this->load->helper('text');
		//$sitemap = $this->fuel_search->sitemap_pages();
		
		// $this->fuel_search->index();
		// $this->fuel_search->display_log();
		$q = '(lore\'m AND "ipsum") OR test NOT whatever';
		$vars['results'] = $this->fuel_search->query($q, '', '', 300);
		$vars['q'] = $q;
		$this->load->view('results', $vars);
	}
	
}
/* End of file backup.php */
/* Location: ./fuel/modules/backup/controllers/backup.php */

