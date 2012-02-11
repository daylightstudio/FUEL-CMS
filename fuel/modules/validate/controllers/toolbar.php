<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Toolbar extends Fuel_base_controller {

	function __construct()
	{
		parent::__construct();
		$this->load->vars(array('css' => array('validate' => array('validate', 'validate_results'))));
	}

	function index()
	{
		$this->html();
	}
	function html()
	{
		$this->_validate_user('tools/validate');
		
		if (empty($_GET['location'])) show_error('validate_no_url');
		
		$url = site_url($this->input->get('location'));

		$vars = $this->fuel->validate->html($url);
		$vars['link'] = $url;
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('module_validate'), lang('validate_type_html')), FALSE);
		$vars['output'] = $this->load->view('_admin/html_output', $vars, TRUE);
		$this->fuel->admin->set_inline(TRUE);
		$this->fuel->admin->set_titlebar(lang('validate_type_html'), 'ico_tools_validate');
		$this->fuel->admin->render('_admin/toolbar_validate', $vars, Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
	}

	function links()
	{
		$this->_validate_user('tools/validate');
		
		if (empty($_GET['location'])) show_error('validate_no_url');
		
		$url = site_url($this->input->get('location'));
		
		$vars = $this->fuel->validate->links($url);
		$vars['link'] = $url;
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('module_validate'), lang('validate_type_links')), FALSE);
		$vars['output'] = $this->load->view('_admin/links_output', $vars, TRUE);
		
		$this->fuel->admin->set_inline(TRUE);
		$this->fuel->admin->set_titlebar(lang('validate_type_links'), 'ico_tools_validate');
		$this->fuel->admin->render('_admin/toolbar_validate', $vars, Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
	}

	function size_report()
	{
		$this->_validate_user('tools/validate');
		
		if (empty($_GET['location'])) show_error('validate_no_url');
		
		$url = site_url($this->input->get('location'));
		
		$vars = $this->fuel->validate->size_report($url);
		$vars['link'] = $url;
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('module_validate'), lang('validate_type_size_report')), FALSE);
		$vars['output'] = $this->load->view('_admin/size_report_output', $vars, TRUE);
		
		$this->fuel->admin->set_inline(TRUE);
		$this->fuel->admin->set_titlebar(lang('validate_type_size_report'), 'ico_tools_validate');
		$this->fuel->admin->render('_admin/toolbar_validate', $vars, Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
	}

}

/* End of file dashboard.php */
/* Location: ./fuel/modules/backup/controllers/dashboard.php */