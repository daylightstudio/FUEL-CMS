<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Dashboard extends Fuel_base_controller {
	
	function __construct()
	{
		parent::__construct();
		$this->js_controller = 'fuel.controller.DashboardController';
	}
	
	function index()
	{
		if (is_ajax())
		{
			$this->ajax();
		}
		else
		{
			$user = $this->fuel_auth->user_data();
			$vars['change_pwd'] = ($user['password'] == md5($this->config->item('default_pwd', 'fuel')));

			$dashboards = array();
			$dashboards_config = $this->config->item('dashboards', 'fuel');
			if (!empty($dashboards_config))
			{
				
				if (is_string($dashboards_config) AND strtoupper($dashboards_config) == 'AUTO')
				{
					$modules = $this->config->item('modules_allowed', 'fuel');

					foreach($modules as $module)
					{
						// check if there is a dashboard controller for each module
						if ($this->fuel_auth->has_permission($module) AND 
							file_exists(MODULES_PATH.$module.'/controllers/dashboard.php'))
						{
							$dashboards[] = $module;
						}
					}
				}
				else if(is_array($dashboards_config))
				{
					foreach($dashboards_config as $module)
					{
						$dashboards[] = $module;
					}
				}
			}
			$vars['dashboards'] = $dashboards;
			$this->_render('dashboard', $vars);
		}

	}
	
	/* need to be outside of index so when you click back button it will not show the ajax */
	function ajax()
	{
		if (is_ajax())
		{
			$this->load->module_model(FUEL_FOLDER, 'pages_model');

			$vars['recently_modifed_pages'] = $this->pages_model->recently_updated();
			$vars['latest_activity'] = $this->logs_model->list_items(10);
			if (file_exists(APPPATH.'/views/_docs/fuel'.EXT))
			{
				$vars['docs'] = $this->load->module_view(NULL, '_docs/fuel', $vars, TRUE);
			}
			$vars['feed'] = $this->_feed();
			$this->load->view('dashboard_ajax', $vars);
		}
	}
	
	function _feed()
	{
		$feed = $this->config->item('dashboard_rss', 'fuel');
		$limit = 3;
		if (!empty($feed))
		{
			$this->load->library('simplepie');
			$this->simplepie->set_feed_url($feed);
			$this->simplepie->set_cache_duration(600);
			$this->simplepie->enable_order_by_date(TRUE);
			$this->simplepie->enable_cache(TRUE);
			$this->simplepie->set_cache_location($this->config->item('cache_path'));
			@$this->simplepie->init();
			$this->simplepie->handle_content_type();
			
			return $this->simplepie->get_items(0, $limit);
		}
	}
	
	function recent()
	{
		$recent = $this->session->userdata('recent');
		if (!empty($recent[0]))
		{
			$redirect_to = $recent[0]['link'];
		}
		else
		{
			$redirect_to = $this->config->item('fuel_path', 'fuel').'dashboard';
		}
		redirect($redirect_to);
	}
	
	
}