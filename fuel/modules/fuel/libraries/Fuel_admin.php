<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * FUEL master admin object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_admin
 */

// --------------------------------------------------------------------

class Fuel_admin extends Fuel_base_library {
	
	protected $validate = TRUE;
	protected $panels = array(
		'top' => TRUE,
		'nav' => TRUE,
		'titlebar' => TRUE,
		'actions' => TRUE,
		'notification' => TRUE,
		'bottom' => TRUE,
	);
	protected $display_mode = NULL;
	protected $titlebar = array();
	protected $titlebar_icon = '';

	const DISPLAY_NO_ACTION = 'no_action';
	const DISPLAY_COMPACT = 'compact';
	const DISPLAY_COMPACT_NO_ACTION = 'compact_no_action';
	const DISPLAY_COMPACT_TITLE = 'compact_title';

	const NOTIFICATION_SUCCESS = 'success';
	const NOTIFICATION_ERROR = 'error';
	const NOTIFICATION_WARNING = 'warning';
	const NOTIFICATION_INFO = 'info';
	
	function __construct($params = array())
	{
		parent::__construct($params);
		
		// load all the helpers we need
		$this->CI->load->library('form');
		$this->CI->load->helper('ajax');
		$this->CI->load->helper('date');
		$this->CI->load->helper('cookie');
		$this->CI->load->helper('inflector');
		$this->CI->load->helper('text');
		$this->CI->load->helper('convert');
		
		//$this->CI->load->module_helper(FUEL_FOLDER, 'fuel');... alternative syntax
		$this->fuel->load_helper('fuel');
	}
	
	function initialize($params = array())
	{
		parent::initialize($params);

		
		// set the language based on first the users profile and then what is in the config... (FYI... fuel_auth is loaded in the hooks)
		$language = $this->fuel->auth->user_data('language');

		// in case the language field doesn't exist... due to older fersions'
		if (empty($language) OR !is_string($language)) $language = $this->CI->config->item('language');
		
		// load this language file first because fuel_modules needs it
		$this->CI->load->module_language(FUEL_FOLDER, 'fuel', $language);

		// now load the other languages
		$this->load_languages();
		
		// load assets config
		$this->CI->config->load('asset');
		
		// load fuel helper
		$this->CI->load->module_helper(FUEL_FOLDER, 'fuel');
		
		// check if the admin is even accessible... this method looks at if the admin is enabled and at any remote host or IP restrictions
		if (!$this->fuel->auth->can_access())
		{
			show_404();
		}
		
		// set asset output settings
		$this->asset->assets_output = $this->fuel->config('fuel_assets_output');

		if ($this->validate) $this->check_login();
		
		$this->CI->load->model(FUEL_FOLDER.'/logs_model');
		
		// set variables
		$load_vars = array(
			'js' => array(), 
			'css' => $this->load_css(),
			'js_controller_params' => array(), 
			'keyboard_shortcuts' => $this->fuel->config('keyboard_shortcuts'),
			'nav' => $this->nav(),
			'modules_allowed' => $this->fuel->config('modules_allowed'),
			'page_title' => $this->page_title(),
			'form_action' => $this->CI->uri->uri_string(),
			);
			
			
		if ($this->validate)
		{
			$load_vars['user'] = $this->fuel->auth->user_data();
			$load_vars['session_key'] = $this->fuel->auth->get_session_namespace();
		}
		$this->CI->js_controller_path = js_path('', FUEL_FOLDER);

		$this->CI->load->vars($load_vars);
		$this->load_js_localized();
		
		// set asset paths
		//$this->CI->asset->assets_module = FUEL_FOLDER;
		$this->CI->asset->assets_folders = array(
				'images' => 'images/',
				'css' => 'css/',
				'js' => 'js/',
			);
			
		$this->last_page();
	}
	
	function render($view, $vars = array(), $mode = '', $module = NULL)
	{
		// set the active state of the menu
		$this->nav_selected();
		
		// set the module parameter to know where to look for view files
		if (!isset($module))
		{
			$module = (!empty($this->CI->view_location)) ? $this->CI->view_location : $this->CI->router->fetch_module();
		}
		
		// get notification if not already loaded in $vars and if any errors
		if (empty($vars['notifications']))
		{
			$vars['error'] = $this->get_model_errors();
			// if ($vars['error'])
			// {
				$vars['notifications'] = $this->CI->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
			// }
		}
		
		// get titlebar only if there is no $vars set for it
		if (empty($vars['titlebar']))
		{
			$vars['titlebar'] = $this->titlebar();
		}

		// get titlebar icon only if there is no $vars set for it
		if (empty($vars['titlebar_icon']))
		{
			$vars['titlebar_icon'] = $this->titlebar_icon();
		}
		
		if (!empty($mode))
		{
			$this->set_display_mode($mode);
		}
		
		// set the form action
		if (empty($vars['form_action']))
		{
			$vars['form_action'] = site_url($this->CI->uri->uri_string());
		}
		else if (!empty($vars['form_action']) AND !is_http_path($vars['form_action']))
		{
			$vars['form_action'] = fuel_url($vars['form_action']);
		}
		
		$layout = (isset($vars['layout'])) ? $vars['layout'] : 'admin_shell';
		if (!empty($layout))
		{
			$vars['body'] = $this->CI->load->module_view($module, $view, $vars, TRUE);
			$vars['panels'] = $this->panels;
			$this->CI->load->module_view(FUEL_FOLDER, '_layouts/'.$layout, $vars);
		}
		else
		{
			$this->CI->load->module_view($module, $view, $vars);
		}
	}
	
	function get_model_errors()
	{
		if (isset($this->CI->model) AND is_a($this->CI->model, 'MY_Model'))
		{
			return $this->CI->model->get_errors();
		}
		return FALSE;
	}
	
	
	function check_login()
	{
		// set no cache headers to prevent back button problems in FF
		$this->no_cache();

		// check if logged in
		if (!$this->CI->fuel->auth->is_logged_in() OR !is_fuelified())
		{
			$login = $this->CI->fuel->config('fuel_path').'login';
			
			// logout officially to unset the cookie data
			$this->CI->fuel->auth->logout();
			
			if (!is_ajax())
			{
				redirect($login.'/'.uri_safe_encode($this->CI->uri->uri_string()));
			}
			else 
			{
				$output = "<script type=\"text/javascript\" charset=\"utf-8\">\n";
				$output .= "top.window.location = '".site_url($login)."'\n";
				$output .= "</script>\n";
				$this->CI->output->set_output($output);
				return;
			}
		}
	}
	
	function validate_user($permission, $type = 'edit', $show_error = TRUE)
	{
		if (!$this->fuel->auth->has_permission($permission, $type))
		{
			if ($show_error)
			{
				show_error(lang('error_no_access'));
			}
			else
			{
				exit();
			}
		}
	}
	
	function last_page()
	{
		if (!isset($key)) $key = uri_path(FALSE);
		$invalid = array(
			fuel_uri('recent')
		);
		$session_key = $this->fuel->auth->get_session_namespace();
		$user_data = $this->fuel->auth->user_data();
		
		if (!is_ajax() AND empty($_POST) AND !in_array($key, $invalid))
		{
			$user_data['last_page'] = $key;
			$this->CI->session->set_userdata($session_key, $user_data);
		}
		
	}
	
	function recent_pages($link, $name, $type)
	{
		$this->CI->load->helper('array');
		$session_key = $this->fuel->auth->get_session_namespace();
		$user_data = $this->fuel->auth->user_data();
		
		if (!isset($user_data['recent'])) $user_data['recent'] = array();
		$already_included = false;
		foreach($user_data['recent'] as $key => $pages)
		{
			if ($pages['link'] == $link AND $pages['name'] == $name AND $pages['type'] == $type)
			{
				$user_data['recent'][$key]['last_visited'] = time();
				$already_included = TRUE;
			}
		}

		if (!$already_included)
		{
			if (strlen($name) > 100) $name = substr($name, 0, 100).'&hellip;';
			$val = array('name' => $name, 'link' => $link, 'last_visited' => time(), 'type' => $type);
			array_unshift($user_data['recent'], $val);
		}

		if (count($user_data['recent']) > $this->fuel->config('max_recent_pages'))
		{
			array_pop($user_data['recent']);
		}
		$user_data['recent'] = array_sorter($user_data['recent'], 'last_visited', 'desc', TRUE);
		$this->CI->session->set_userdata($session_key, $user_data);
		
	}
	
	function nav()
	{
		$nav = $this->fuel->config('nav');
		$modules = array('fuel');
		$modules = array_merge($modules, $this->fuel->config('modules_allowed'));
		
		foreach($modules as $module)
		{
			$nav_path = MODULES_PATH.$module.'/config/'.$module.'.php';
			if (file_exists($nav_path))
			{
				include($nav_path);
				$nav = array_merge($nav, $config['nav']);
			}
		}
		
		// automatically include modules if set to AUTO
		if (is_string($nav['modules']) AND strtoupper($nav['modules']) == 'AUTO')
		{
			@include(APPPATH.'config/MY_fuel_modules.php');
			
			$nav['modules'] = array();
			
			if (!empty($config['modules']))
			{
				foreach($config['modules'] as $key => $module)
				{
					if (isset($module['hidden']) AND $module['hidden'] === TRUE)
					{
						continue;
					}
					
					if (!empty($module['module_name']))
					{
						$nav['modules'][$key] = $module['module_name'];
					}
					else
					{
						$nav['modules'][$key] = humanize($key);
					}
				}
				
			}
		}
		return $nav;
	}
	
	function nav_selected()
	{
		if (empty($this->CI->nav_selected))
		{
			if (fuel_uri_segment(1) == '')
			{
				$nav_selected = 'dashboard';
			}
			else
			{
				$nav_selected = fuel_uri_segment(1);
			}
		}
		else
		{
			$nav_selected = $this->CI->nav_selected;
		}
		
		// Convert wild-cards to RegEx
		$nav_selected = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $nav_selected));
		
		$this->CI->load->vars(array('nav_selected' => $nav_selected));
		return $nav_selected;
	}
	
	function load_css()
	{
		$modules = $this->fuel->config('modules_allowed');
		
		$css = array();
		foreach($modules as $module)
		{
			// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
			if (file_exists(MODULES_PATH.$module.'/assets/css/'.$module.'.css'))
			{
				$css[] = array($module => $module);
			}
		}
		if ($this->fuel->config('xtra_css'))
		{
			$css[] = array('' => $this->fuel->config('xtra_css'));
		}
		return $css;
		
	}
	
	function load_languages()
	{
		$modules = $this->fuel->config('modules_allowed');
		foreach($modules as $module)
		{
			$language = $this->fuel->auth->user_lang();
			if (file_exists(MODULES_PATH.$module.'/language/'.$language.'/'.$module.'_lang'.EXT))
			{
				$this->CI->load->module_language($module, $module);
			}
		}
	}
	
	function load_js_localized($js_localized = array(), $load = TRUE)
	{
		static $localized;
		if (empty($localized))
		{
			$localized = json_lang('fuel/fuel_js', FALSE);
		}
		$localized = array_merge($localized, $js_localized);
		if  ($load)
		{
			$this->CI->load->vars(array('js_localized' => $localized));
		}
		return $localized;
	}
	
	function page_title($segs = array(), $humanize = TRUE)
	{
		$segs = (array) $segs;
		
		if (empty($segs))
		{
			$segs = $this->CI->uri->segment_array();
			array_shift($segs);
		}
		$page_segs = array();
		if (empty($segs)) $segs = array('dashboard');
		foreach($segs as $seg)
		{
			if (!is_numeric($seg))
			{
				if ($humanize) $seg = humanize($seg);
				$page_segs[] = $seg;
			}
		}
		$page_title = lang('fuel_page_title').' : '.implode(' : ', $page_segs);
		return $page_title;
	}
	
	function reset_page_state()
	{
		$state_key = $this->get_state_key();
		if (!empty($state_key))
		{
			$session_key = $this->fuel->auth->get_session_namespace();
			$user_data = $this->fuel->auth->user_data();
			$user_data['page_state'] = array();
			$this->CI->session->set_userdata($session_key, $user_data);
			redirect(fuel_url($state_key));
		}
	}
	
	function save_page_state($vars)
	{
		$state_key = $this->get_state_key();
		if (!empty($state_key))
		{
			$session_key = $this->fuel->auth->get_session_namespace();
			$user_data = $this->fuel->auth->user_data();
			if (!isset($user_data['page_state']))
			{
				$user_data['page_state'] = array();
			}
			
			// if greater then what is set in config, then we pop the array to save on page state info
			if (count($user_data['page_state']) > $this->fuel->config('saved_page_state_max'))
			{
				array_pop($user_data['page_state']);
			}
			$user_data['page_state'][$state_key] = $vars;
			$this->CI->session->set_userdata($session_key, $user_data);
		}
	}
	
	function get_page_state($state_key = NULL)
	{
		if (empty($state_key))
		{
			$state_key = $this->get_state_key();
		}
		if (!empty($state_key))
		{
			$user_data = $this->fuel->auth->user_data();
			return (isset($user_data['page_state'][$state_key])) ? $user_data['page_state'][$state_key] : array();
		}
		return array();
	}
	
	function get_state_key()
	{
		if (!empty($this->CI->module))
		{
			return $this->CI->module_uri;
		}
		else
		{
			return FALSE;
		}
		
	}
	
	function no_cache()
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0',false);
		header('Pragma: no-cache');
	}
	
	function panel_display($key)
	{
		if (isset($this->panels))
		{
			return $this->panels[$key];
		}
	}

	function set_panel_display($key, $value)
	{
		$this->panels[$key] = (bool) $value;
	}

	function display_mode($mode)
	{
		return $this->display_mode;
	}

	function set_display_mode($mode)
	{
		switch($mode)
		{
			case self::DISPLAY_NO_ACTION:
				$this->set_panel_display('actions', FALSE);
				break;
			case self::DISPLAY_COMPACT:
				$this->set_panel_display('top', FALSE);
				$this->set_panel_display('nav', FALSE);
				$this->set_panel_display('titlebar', FALSE);
				$this->set_panel_display('bottom', FALSE);
				break;
			case self::DISPLAY_COMPACT_NO_ACTION:
				$this->set_panel_display('top', FALSE);
				$this->set_panel_display('nav', FALSE);
				$this->set_panel_display('titlebar', FALSE);
				$this->set_panel_display('actions', FALSE);
				$this->set_panel_display('bottom', FALSE);
				break;
			case self::DISPLAY_COMPACT_TITLE:
				$this->set_panel_display('top', FALSE);
				$this->set_panel_display('nav', FALSE);
				$this->set_panel_display('actions', FALSE);
				$this->set_panel_display('notification', FALSE);
				$this->set_panel_display('bottom', FALSE);
				break;
			default:
				$this->set_panel_display('top', TRUE);
				$this->set_panel_display('nav', TRUE);
				$this->set_panel_display('titlebar', TRUE);
				$this->set_panel_display('actions', TRUE);
				$this->set_panel_display('bottom', TRUE);
				
		}
		$this->display_mode = $mode;
	}
	
	function set_titlebar($title, $icon = '')
	{
		if (empty($icon))
		{
			$icon = $this->titlebar_icon();
		}
		else
		{
			$this->titlebar_icon = $icon;
		}
		$this->CI->load->vars(array('titlebar' => $title, 'titlebar_icon' => $icon));
		$this->titlebar = $title;
	}
	
	function titlebar()
	{
		return $this->titlebar;
	}
	
	function titlebar_icon()
	{
		if (!empty($this->titlebar_icon))
		{
			return $this->titlebar_icon;
		}
		
		// set in simple module configuration
		else if (!empty($this->CI->icon_class))
		{
			$this->titlebar_icon = $this->CI->icon_class;
		}
		else if (!empty($this->CI->module_uri))
		{
			$this->titlebar_icon = url_title(str_replace('/', '_', $this->CI->module_uri),'_', TRUE);
		}

		return $this->titlebar_icon;
	}
	
	function set_notification($msg, $type = '')
	{
		if (empty($type)) $type = Fuel_admin::NOTIFICATION_SUCCESS;
		$this->CI->session->set_flashdata($type, $msg);
	}
	
	function dashboards()
	{
		$dashboards = array();
		$dashboards_config = $this->fuel->config('dashboards');
		if (!empty($dashboards_config))
		{
			
			if (is_string($dashboards_config) AND strtoupper($dashboards_config) == 'AUTO')
			{
				$modules = $this->fuel->modules->advanced();
				//$modules = $this->fuel->config('modules_allowed');

				foreach($modules as $module)
				{
					// check if there is a dashboard controller for each module
					if ($this->fuel->auth->has_permission($module) AND $module->had_dashboard())
					{
						$dashboards[] = $module;
					}
				}
			}
			else if (is_array($dashboards_config))
			{
				foreach($dashboards_config as $module)
				{
					if ($this->fuel->auth->has_permission($module))
					{
						$dashboards[] = $module;
					}
				}
			}
		}
		return $dashboards;
	}
	
	function tools()
	{
		$tools = array();
		$modules = $this->fuel->modules->advanced();
		
		//$modules = $this->fuel->config('modules_allowed');

		foreach($modules as $module)
		{
			// check if there is a dashboard controller for each module
			$t = $module->tools();
			if ($t)
			{
				$tools = $tools + $t;
			}
		}
		return $tools;
	}

	function toolbar()
	{
		$this->fuel->load_language('fuel_inline_edit');
		$this->fuel->load_language('fuel_js');
	
		$vars['page'] = $this->fuel->page->properties();
		$vars['layouts'] = $this->fuel->layouts->options_list();
		$vars['tools'] = $this->tools();
		$vars['js_localized'] = json_lang('fuel/fuel_js');

		if ($this->fuel->config('fuel_mode') == 'views')
		{
			$vars['others'] = array();
		}
		else
		{
			$location = uri_path();
			$this->CI->load->module_model(FUEL_FOLDER, 'pages_model');
			$vars['others'] = $this->CI->pages_model->get_others('location', $location, 'location');
		}
		$vars['init_params']['pageId'] = (!empty($vars['page']['id']) ? $vars['page']['id'] : 0);
		$vars['init_params']['pageLocation'] = (!empty($vars['page']['location']) ? $vars['page']['location'] : '');
		$vars['init_params']['basePath'] = WEB_PATH;
		$vars['init_params']['imgPath'] = img_path('', 'fuel'); 
		$vars['init_params']['cssPath'] = css_path('', 'fuel'); 
		$vars['init_params']['jsPath'] = js_path('', 'fuel');
		$vars['init_params']['editor'] = $this->fuel->config('text_editor');
		$vars['init_params']['editorConfig'] = $this->fuel->config('ck_editor_settings');
		
		$output = $this->CI->load->module_view(FUEL_FOLDER, '_blocks/inline_edit_bar', $vars, TRUE);
		
		return $output;
	}
}

/* End of file Fuel_admn.php */
/* Location: ./modules/fuel/libraries/Fuel_admn.php */