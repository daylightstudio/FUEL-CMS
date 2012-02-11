<?php

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
 * Google_keywords object
 *
 * @package		FUEL CMS
 * @subpackage	Controller
 * @category	Controller
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/google_keywords
 */

// --------------------------------------------------------------------

require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Google_keywords extends Fuel_base_controller {
	
	public $nav_selected = 'tools/google_keywords'; // navigation item selected on menu
	
	function __construct()
	{
		parent::__construct();
		$this->js_controller = 'GoogleKeywordsController';
		$this->js_controller_path = js_path('', GOOGLE_KEYWORDS_FOLDER);
		$this->js_controller_params['method'] = 'google_keywords';
	}

	
	function index()
	{
		$this->_validate_user('tools/google_keywords');
		
		// domain
		$vars = array();
		if (!empty($_POST['domain']))
		{
			$vars['domain'] = $this->input->post('domain');
		}
		else
		{
			$vars['domain'] = ($this->fuel->google_keywords->config('default_domain')) ? $this->fuel->google_keywords->config('default_domain') : $_SERVER['SERVER_NAME'];
		}
		
		// keywords
		$vars['keywords'] = (!empty($_POST['keywords'])) ? $this->input->post('keywords') : $this->fuel->google_keywords->config('default_keywords');

		if (is_array($vars['keywords']))
		{
			$options = array();
			foreach($vars['keywords'] as $val) 
			{
				$options[$val] = $val;
			}
			$vars['keywords'] = $options;
		}

		$vars['num_results'] = $this->fuel->google_keywords->config('num_results');
		
		// process data to get results here after variables are set above
		if (!empty($_POST))
		{
			$keywords = explode(',', $this->input->post('keywords'));
			$domain = str_replace(array('http://', 'www'), '', $this->input->post('domain'));
			
			$params['keywords'] = $keywords;
			$params['domain'] = $domain;
			$found =  $this->fuel->google_keywords->results($params);
			$vars['results'] = $found;
			$this->load->view('_admin/google_keywords_result', $vars);
			return;
		}
		
		$vars['form_action'] = 'tools/google_keywords';
		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('section_tools'), lang('module_google_keywords')), FALSE);
		$crumbs = array('tools' => lang('section_tools'), lang('module_google_keywords'));
		$this->fuel->admin->set_titlebar($crumbs, 'ico_tools_google_keywords');
		$this->fuel->admin->render('_admin/google_keywords', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}
}

/* End of file google_keywords.php */
/* Location: ./modules/google_keywords/controllers/google_keywords.php */