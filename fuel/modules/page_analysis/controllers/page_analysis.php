<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Page Analysis object
 *
 * @package		FUEL CMS
 * @subpackage	Controller
 * @category	Controller
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/page_analysis
 */

// --------------------------------------------------------------------

class Page_analysis extends Fuel_base_controller {
	
	public $nav_selected = 'tools/page_analysis';
	public $view_location = 'page_analysis'; // location of view files
	
	function __construct()
	{
		parent::__construct();
		
		if ($this->fuel->config('dev_password'))
		{
			add_error(lang('error_seo_dev_password'));
		}
		$this->js_controller = 'PageAnalysisController';
		$this->js_controller_path = js_path('', PAGE_ANALYSIS_FOLDER);
	}

	function index()
	{
		$this->_validate_user('tools/page_analysis');
		
		$url = '';
		if ($this->input->post('page'))
		{
			$url = $this->input->post('page');
			$this->load->helper('text');
			
			// get the page analysis report
			$vars['results'] = $this->fuel->page_analysis->report($url);
		} 
		
		
		$this->js_controller_params['method'] = 'links';
		$this->js_controller_params['pages'] = $this->input->post('pages');
		
		$this->load->module_model(FUEL_FOLDER, 'pages_model');
		$pages = $this->pages_model->all_pages_including_views(TRUE);

		$this->js_controller_params['method'] = 'page_analysis';
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['url'] = $url;
		$vars['pages_select'] = $pages;
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('section_tools'), lang('module_page_analysis')), FALSE);
		$crumbs = array('tools' => lang('section_tools'), lang('module_page_analysis'));
		$this->fuel->admin->set_breadcrumb($crumbs, 'ico_tools_page_analysis');
		$this->fuel->admin->render('page_analysis', $vars, Fuel_admin::DISPLAY_NO_ACTION);
		
	}
	
	function _sort_word_density($a, $b)
	{
		if ($a == $b) {
		    return 0;
		}
    	return ($a < $b) ? 1 : -1;
	}
}

/* End of file page_analysis.php */
/* Location: ./fuel/modules/seo/controllers/page_analysis.php */