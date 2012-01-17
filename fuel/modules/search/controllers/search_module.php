<?php
require_once(FUEL_PATH.'/controllers/module.php');

class Search_module extends Module {
	
	public $nav_selected = 'tools/search';
	public $view_location = 'search';
	public $module = 'search';
	// function __construct()
	// {
	// 	parent::__construct();
	// 	
	// }
	// 
	// function index()
	// {
	// 	// $this->fuel_search->index();
	// 	// $this->fuel_search->display_log();
	// 	//$this->fuel_search->index();
	// }
	
	function reindex()
	{
		$this->load->library('fuel_search');
		$this->fuel_search->index();
		$this->fuel_search->display_log();
	}
	
}
/* End of file backup.php */
/* Location: ./fuel/modules/backup/controllers/backup.php */