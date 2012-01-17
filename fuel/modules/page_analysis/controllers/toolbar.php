<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Toolbar extends Fuel_base_controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->_validate_user('tools/page_analysis');
		
		if (empty($_GET['location'])) show_error('page_analysis_no_url');
		
		$url = site_url($this->input->get('location'));
		$this->load->helper('text');
		
		// get the page analysis report
		$vars['url'] = $url;
		$vars['results'] = $this->fuel->page_analysis->report($url);
		$vars['report'] = $this->load->module_view(PAGE_ANALYSIS_FOLDER, '_admin/report', $vars, TRUE);
			
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('section_tools'), lang('module_page_analysis')), FALSE);
		
		$this->fuel->admin->set_titlebar(lang('module_page_analysis'), 'ico_tools_page_analysis');
		
		$this->fuel->admin->render('_admin/toolbar_report', $vars, Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
		
	}

}

/* End of file dashboard.php */
/* Location: ./fuel/modules/backup/controllers/dashboard.php */