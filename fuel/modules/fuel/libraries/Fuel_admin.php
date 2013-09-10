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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL master admin object
 * 
 * The class is in charge of rendering the admin views.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_admin
 */

// --------------------------------------------------------------------

class Fuel_admin extends Fuel_base_library {
	
	protected $main_layout = 'admin_main'; // The main layout file for the FUEL admin
	protected $validate = TRUE; // Whether to check that the user is logged in or not before rendering page
	protected $is_inline = FALSE; // Determines whether the page being displayed is the inline view or now
	protected $last_page = ''; // The last page rendered by the admin
	
	// The names of panels displayed in the admin
	protected $panels = array(
								'top' => TRUE,
								'nav' => TRUE,
								'titlebar' => TRUE,
								'actions' => TRUE,
								'notification' => TRUE,
								'bottom' => TRUE,
							); 
	protected $display_mode = NULL; // The name of the currently set display mode
	protected $_display_modes = array(); // An array of available display modes
	protected $titlebar = array(); // The title bar breadcrumb area in the admin
	protected $titlebar_icon = ''; // The title bar icon to display
	protected $_notifications = array(); // Stores notifications messages to be displayed
	
	const DISPLAY_NO_ACTION = 'no_action'; // Display mode that has no action panel (panel with all the buttons)
	const DISPLAY_COMPACT = 'compact'; // Display mode that has no left menu, or top
	const DISPLAY_COMPACT_NO_ACTION = 'compact_no_action'; // Display mode that has no left menu, action panel or top
	const DISPLAY_COMPACT_TITLEBAR = 'compact_titlebar'; // Display mode that has no left menu, action panel or top but does have the titlebar
	const DISPLAY_DEFAULT = 'default'; // Default display mode that shows all panels

	const NOTIFICATION_SUCCESS = 'success'; // Success notification
	const NOTIFICATION_ERROR = 'error'; // Error notification
	const NOTIFICATION_WARNING = 'warning'; // Warning notification
	const NOTIFICATION_INFO = 'info'; // Info notification
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the FUEL admin object
	 *
	 * Accepts an associative array as input for setting properties
	 *
	 * @access	public
	 * @param	array	Config preferences (optional)
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);

		// now load the other languages
		$this->load_languages();
		
		// load assets config
		$this->CI->config->load('asset');
		
		// load fuel helper
		$this->CI->load->module_helper(FUEL_FOLDER, 'fuel');
		
		// check if the admin is even accessible... this method looks at if the admin is enabled and at any remote host or IP restrictions
		if (!$this->fuel->auth->can_access() AND $this->validate)
		{
			show_404();
		}
		
		// set asset output settings
		$this->CI->asset->assets_output = $this->fuel->config('fuel_assets_output');

		if ($this->validate) 
		{
			$this->check_login();
		}
		
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
		$this->CI->asset->assets_folders = array(
				'images' => 'images/',
				'css' => 'css/',
				'js' => 'js/',
				'pdf' => 'pdf/',
				'media' => 'media/',
				'swf' => 'swf/',
				'docs' => 'docs/',
			);

		$this->CI->asset->assets_path = 'assets/';
			
		$this->main_layout = $this->fuel->config('main_layout');
		
		$this->set_inline($this->CI->input->get('inline'));
		
		// set last page
		$this->last_page = $this->fuel->auth->user_data('fuel_last_page');
		$this->init_display_modes();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a page in the admin
	 *
	 * @access	public
	 * @param	string	The name of the view file to display
	 * @param	array	Variables to pass to the view file (optional)
	 * @param	string	The display mode to use (can be the class constant Fuel_admin::DISPLAY_NO_ACTION, DISPLAY_COMPACT, DISPLAY_COMPACT_NO_ACTION, DISPLAY_COMPACT_TITLEBAR, DISPLAY_DEFAULT) (optional)
	 * @param	array	The module to pull the view file from (optional)
	 * @return	void
	 */	
	public function render($view, $vars = array(), $mode = '', $module = NULL)
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
			$vars['notifications'] = $this->CI->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
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
		
		if (!empty($mode) OR empty($this->display_mode))
		{
			$this->set_display_mode($mode);
		}
		
		// set inline
		if (!empty($_POST['fuel_inline']) OR (int)$this->CI->input->get('inline') != 0)
		{
			if (!empty($_POST['fuel_inline']) AND $_POST['fuel_inline'] != 0)
			{
				$inline = $this->CI->input->post('fuel_inline');
			}
			else if ((int) $this->CI->input->get('inline') != 0)
			{
				$inline = $this->CI->input->get('inline');
			}
			
			$this->set_inline($inline);
		}
		
		// set the form action
		if (empty($vars['form_action']))
		{
			$vars['form_action'] = site_url($this->CI->uri->uri_string(). '?inline='.$this->is_inline());
		}
		else if (!empty($vars['form_action']) AND !is_http_path($vars['form_action']))
		{
			$vars['form_action'] = fuel_url($vars['form_action']);
		}
		
		$layout = (isset($vars['layout'])) ? $vars['layout'] : $this->main_layout;
		if (!empty($layout))
		{
			$vars['body'] = $this->CI->load->module_view($module, $view, $vars, TRUE);
			$vars['panels'] = $this->panels;
			if (is_array($layout))
			{
				$layout_module = key($layout);
				$layout_view = current($layout);
				$this->CI->load->module_view($layout_module, $layout_view, $vars);
			}
			else
			{
				$this->CI->load->module_view(FUEL_FOLDER, '_layouts/'.$layout, $vars);
			}
		}
		else
		{
			$this->CI->load->module_view($module, $view, $vars);
		}
		
		// register the last page
		$this->set_last_page();
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Checks that the user is logged in to FUEL and if not will redirect to the login
	 *
	 * @access	public
	 * @return	void
	 */	
	public function check_login()
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Will validate that the user has the proper permission
	 *
	 * @access	public
	 * @param	string	Permission name
	 * @param	string	Type of permission (edit, publish, delete... etc)  (optional)
	 * @param	boolean	Whether to display the error page or simply exit the script (optional)
	 * @return	void
	 */	
	public function validate_user($permission, $type = 'edit', $show_error = TRUE)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of the errors from a module's model
	 *
	 * @access	public
	 * @return	array	Will return FALSE if no errors
	 */	
	public function get_model_errors()
	{
		if (isset($this->CI->model) AND is_a($this->CI->model, 'MY_Model'))
		{
			return $this->CI->model->get_errors();
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the last page that was visited in the admin
	 *
	 * @access	public
	 * @param	string	The URI path of a page (optional)
	 * @return	void
	 */	
	public function set_last_page($page = NULL)
	{
		if (!isset($page)) $page = uri_path(FALSE);
		
		$invalid = array(
			fuel_uri('recent'),
		);
		if (!is_ajax() AND empty($_POST) AND !in_array($page, $invalid) AND !$this->is_inline())
		{
			$this->fuel->auth->set_user_data('fuel_last_page', $page);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the last page that was visited in the admin
	 *
	 * @access	public
	 * @return	void
	 */	
	public function last_page()
	{
		return $this->last_page;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds to the recent page array
	 *
	 * @access	public
	 * @param	string	The URI path of a page
	 * @param	string	The name to display
	 * @param	string	The type of page
	 * @return	void
	 */	
	public function add_recent_page($link, $name, $type)
	{
		$this->CI->load->helper('array');
		$session_key = $this->fuel->auth->get_session_namespace();
		$user_data = $this->fuel->auth->user_data();
		
		$name = strip_tags($name);
		
		if (!isset($user_data['recent'])) $user_data['recent'] = array();
		$already_included = FALSE;
		foreach($user_data['recent'] as $key => $pages)
		{
			if ($pages['l'] == $link AND $pages['n'] == $name AND $pages['t'] == $type)
			{
				$user_data['recent'][$key]['ts'] = time();
				$already_included = TRUE;
			}
		}

		if (!$already_included)
		{
			if (strlen($name) > 100) $name = substr($name, 0, 100).'&hellip;';
			$val = array('n' => $name, 'l' => $link, 'ts' => time(), 't' => $type);
			array_unshift($user_data['recent'], $val);
		}

		if (count($user_data['recent']) > $this->fuel->config('max_recent_pages'))
		{
			array_pop($user_data['recent']);
		}
		$user_data['recent'] = array_sorter($user_data['recent'], 'ts', 'desc', TRUE);
		$this->CI->session->set_userdata($session_key, $user_data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The left menu navigation array
	 *
	 * @access	public
	 * @return	array
	 */	
	public function nav()
	{
		$nav = $this->fuel->config('nav');
		
		$nav_orig = $nav;
		
		$modules = array('fuel');
		$modules = array_merge($modules, $this->fuel->config('modules_allowed'));
		
		@include(APPPATH.'config/MY_fuel_modules.php');
		
		foreach($modules as $module)
		{
			$nav_path = MODULES_PATH.$module.'/config/'.$module.'.php';
			if (file_exists($nav_path))
			{
				include($nav_path);
				
				if (array_key_exists('module_overwrites', $config) AND ! empty($config['module_overwrites']))
				{
					foreach ($config['nav'] as $section => $fuel_modules)
					{
						if (is_array($fuel_modules) AND ! empty($fuel_modules))
						{
							foreach ($fuel_modules as $fuel_module => $fuel_module_title)
							{
								if (array_key_exists($fuel_module, $config['module_overwrites'])
											AND array_key_exists('hidden', $config['module_overwrites'][$fuel_module])
											AND $config['module_overwrites'][$fuel_module]['hidden'])
								{
									unset($config['nav'][$section][$fuel_module]);
								}
							}
						}
					}
				}

				$nav = array_merge($nav, $config['nav']);
			}
		}
		
		// automatically include modules if set to blank array
		if (isset($nav_orig['modules']) AND $nav_orig['modules'] === array())
		{
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


		if ($this->fuel->config('nav_auto_arrange'))
		{
			// rearrange
			$orig_nav = $nav;
			unset($nav['site'], $nav['tools'], $nav['manage'], $nav['modules']);

			$arranged_nav = array();
			$arranged_nav['site'] = $orig_nav['site'];
			foreach($nav as $key => $val)
			{
				$arranged_nav[$key] = $val;
			}
			$arranged_nav['modules'] = $orig_nav['modules'];
			$arranged_nav['tools'] = $orig_nav['tools'];
			$arranged_nav['manage'] = $orig_nav['manage'];
			return $arranged_nav;
		}
		else
		{
			return $nav;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the selected navigation
	 *
	 * @access	public
	 * @return	string
	 */	
	public function nav_selected()
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the selected navigation
	 *
	 * @access	public
	 * @param	string	The name of the selected nav item
	 * @return	void
	 */	
	public function set_nav_selected($selected)
	{
		$this->CI->nav_selected = $selected;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of CSS files for the admin including FUEL "xtra_css" parameter
	 *
	 * @access	public
	 * @return	array
	 */	
	public function load_css()
	{
		$modules = $this->fuel->config('modules_allowed');
		
		$css = array();
		foreach($modules as $module)
		{
			// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
			if (file_exists(MODULES_PATH.$module.'/assets/css/'.$module.'.css'))
			{
				$css[$module][] = $module;
			}
		}
		if ($this->fuel->config('xtra_css'))
		{
			$css[] = array('' => $this->fuel->config('xtra_css'));
		}
		return $css;
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the logged in users display language
	 *
	 * @access	public
	 * @param	string	The language folder to set 
	 * @return	void
	 */	
	public function set_language($language)
	{
		$this->CI->config->set_item('language', $language);
		$this->CI->fuel->auth->set_user_data('language', $language);
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Returs the logged in users display language
	 *
	 * @access	public
	 * @return	string
	 */	
	public function language()
	{
		// set the language based on first the users profile and then what is in the config... (FYI... fuel_auth is loaded in the hooks)
		$language = $this->fuel->auth->user_data('language');
		
		// in case the language field doesn't exist... due to older fersions'
		if (empty($language) OR !is_string($language)) $language = $this->CI->config->item('language');

		return $language;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads the language files from the allowed modules
	 *
	 * @access	public
	 * @return	array
	 */	
	public function load_languages()
	{
		// set the language based on first the users profile and then what is in the config... (FYI... fuel_auth is loaded in the hooks)
		$language = $this->language();
		
		// set the usrers language
		$this->set_language($language);

		// load this language file first because fuel_modules needs it
		$this->CI->load->module_language(FUEL_FOLDER, 'fuel', $language);
		$modules = $this->fuel->config('modules_allowed');
		foreach($modules as $module)
		{
			if (file_exists(MODULES_PATH.$module.'/language/'.$language.'/'.$module.'_lang'.EXT))
			{
				$this->CI->load->module_language($module, $module, $language);
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads the localized language files used for javascript (e.g. markItUp!)
	 *
	 * @access	public
	 * @param	array 	An array of javascript languange files (optional)
	 * @param	boolean	"Private" parameter so that the file will only be loaded once (optional)
	 * @return	array
	 */	
	public function load_js_localized($js_localized = array(), $load = TRUE)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the Fuel admin page title based on the module name if set or uri segments
	 *
	 * @access	public
	 * @param	array 	An array of page segments to be parsed into a cookie crumb type page title
	 * @param	boolean Whether to run the humanize inflector helper function for the page title
	 * @return	string
	 */	
	public function page_title($segs = array(), $humanize = TRUE)
	{
		$segs = (array) $segs;
		$simple_module_configs = $this->fuel->modules->get_module_config('app');
		$page_title = lang('fuel_page_title').' : ';

		if (empty($segs))
		{
			$segs = $this->CI->uri->segment_array();
			array_shift($segs);
		}
		if (empty($segs)) $segs = array('dashboard');

		if ($segs AND ! empty($simple_module_configs[ $segs[0] ]['module_name']))
		{
			$page_title .= $simple_module_configs[ $segs[0] ]['module_name'];
		}
		else
		{
			$page_segs = array();
			foreach($segs as $seg)
			{
				if (!is_numeric($seg))
				{
					if ($humanize) $seg = humanize($seg);
					$page_segs[] = $seg;
				}
			}
			$page_title .= implode(' : ', $page_segs);
		}

		return $page_title;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Resets the page state of a list view page (e.g. searching/sorting )
	 *
	 * @access	public
	 * @return	void
	 */	
	public function reset_page_state()
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Saves the page state
	 *
	 * @access	public
	 * @param	array 	An array of page state variables
	 * @return	void
	 */	
	public function save_page_state($vars)
	{
		if ($this->fuel->config('saved_page_state_max') == 0)
		{
			return FALSE;
		}
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the page state of either the specified page. If  or current page
	 *
	 * @access	public
	 * @param	string 	The key to associate with page state (optional)
	 * @return	array
	 */	
	public function get_page_state($state_key = NULL)
	{
		if ($this->fuel->config('saved_page_state_max') == 0)
		{
			return array();
		}
		
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
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the state key
	 *
	 * @access	public
	 * @return	string
	 */	
	public function get_state_key()
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

	// --------------------------------------------------------------------
	
	/**
	 * Returns the UI cookie values
	 *
	 * @access	public
	 * @param	string 	The key value to return. If none provided, it will return all (optional)
	 * @return	mixed
	 */	
	public function ui_cookie($key = NULL)
	{
		$cookie_val = json_decode(urldecode($this->CI->input->cookie('fuel_ui')), TRUE);
		if (!empty($key))
		{
			if (isset($cookie_val[$key]))
			{
				return $cookie_val[$key];	
			}
			else
			{
				return FALSE;
			}
		}
		return $cookie_val;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the UI cookie values
	 *
	 * @access	public
	 * @param	string 	The key value to set. If none provided, it will set the entire cookie
	 * @return	void
	 */	
	public function set_ui_cookie($key = NULL, $val = NULL)
	{
		if (!empty($key))
		{
			$cookie_val = $this->ui_cookie();
			$cookie_val[$key] = $val;
		}
		else
		{
			$cookie_val = json_encode(urlencode($key));
		}
		$this->CI->input->set_cookie('fuel_ui', $cookie_val);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the no cache header
	 *
	 * @access	public
	 * @return	void
	 */	
	public function no_cache()
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the page is in inline editing mode
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_inline()
	{
		return (bool) $this->is_inline;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets whether the page is in inline editing mode or not
	 *
	 * @access	public
	 * @param	boolean
	 * @return	void
	 */	
	public function set_inline($inline)
	{
		$this->is_inline = (bool) $inline;
		
		// set the display mode if inline
		if ($inline)
		{
			$this->set_panel_display('top', FALSE);
			$this->set_panel_display('nav', FALSE);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the main layout for the admin
	 *
	 * @access	public
	 * @param	string	The name of the layout
	 * @return	void
	 */	
	public function set_main_layout($layout)
	{
		$this->main_layout = (string) $layout;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the main layout for the admin
	 *
	 * @access	public
	 * @return	void
	 */	
	public function main_layout()
	{
		return $this->main_layout;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether a panel is displayed or not
	 *
	 * @access	public
	 * @param	string	The panels name
	 * @return	void
	 */	
	public function panel_display($key)
	{
		if (isset($this->panels))
		{
			return $this->panels[$key];
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether a panel is displayed or not
	 *
	 * @access	public
	 * @param	string	The panels name
	 * @param	boolean	Whether to display the panel or not
	 * @return	void
	 */	
	public function set_panel_display($key, $value)
	{
		$this->panels[$key] = (bool) $value;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether a panel exists or not in the admin
	 *
	 * @access	public
	 * @param	string The panel name you want to determine exists in the admin
	 * @return	boolean
	 */	
	public function has_panel($key)
	{
		return $this->panels[$key];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Initializes different display modes
	 *
	 * @access	public
	 * @return	void
	 */	
	public function init_display_modes()
	{
		// no_action
		$panels = array('actions' => FALSE);
		$this->register_display_mode(self::DISPLAY_NO_ACTION, $panels);
		
		// compact
		$panels = array(
						'top' => FALSE,
						'nav' => FALSE,
						'titlebar' => TRUE,
						'actions' => TRUE,
						'bottom' => FALSE,
						);
		$this->register_display_mode(self::DISPLAY_COMPACT, $panels);

		// compact_no_action
		$panels = array(
						'top' => FALSE,
						'nav' => FALSE,
						'titlebar' => FALSE,
						'actions' => FALSE,
						'bottom' => FALSE,
						);
		$this->register_display_mode(self::DISPLAY_COMPACT_NO_ACTION, $panels);

		// compact_titlebar
		$panels = array(
						'top' => FALSE,
						'nav' => FALSE,
						'titlebar' => TRUE,
						'actions' => TRUE,
						'bottom' => FALSE,
						);
		$this->register_display_mode(self::DISPLAY_COMPACT_TITLEBAR, $panels);
		
		// default
		$panels = array(
						'top' => TRUE,
						'nav' => TRUE,
						'titlebar' => TRUE,
						'actions' => TRUE,
						'bottom' => TRUE,
						);
		$this->register_display_mode(self::DISPLAY_DEFAULT, $panels);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Registers a display mode with the admin
	 *
	 * @access	public
	 * @param	string A name to associate with the display mode
	 * @param	array An array of panels to associate with the display mode
	 * @return	void
	 */	
	public function register_display_mode($name, $panels = array())
	{
		$this->_display_modes[$name] = $panels;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the different registered display modes
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */	
	public function display_mode()
	{
		return $this->display_mode;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the display mode for the admin
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	public function set_display_mode($mode)
	{
		if (is_string($mode))
		{
			if (isset($this->_display_modes[$mode]))
			{
				foreach($this->_display_modes[$mode] as $panel => $display)
				{
					$this->set_panel_display($panel, $display);
				}
			}
		}

		$this->display_mode = $mode;
		
		if ($this->is_inline())
		{
			$_GET['inline'] = (int)$this->is_inline;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the top titlebar (breadcrumb/icon area)
	 *
	 * @access	public
	 * @param	string	The text to display in the titlebar
	 * @param	string	The icon to dislpay to the left of the title bar breadcrumb (optional)
	 * @return	void
	 */	
	public function set_titlebar($title, $icon = '')
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the text for the title bar (breadcrumb/icon area)
	 *
	 * @access	public
	 * @return	void
	 */	
	public function titlebar()
	{
		return $this->titlebar;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the title bar icon
	 *
	 * @access	public
	 * @param	string	The icon to dislpay to the left of the title bar breadcrumb (optional)
	 * @return	void
	 */	
	public function set_titlebar_icon($icon = '')
	{
		$this->titlebar_icon = $icon;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the title bar icon
	 *
	 * @access	public
	 * @return	string
	 */	
	public function titlebar_icon()
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
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a nofication flash message to display
	 *
	 * @access	public
	 * @param	type	The type of message to display. Options are error, success and info. (optional)
	 * @return	string
	 */	
	public function notification($type = '')
	{
		if (empty($type)) $type = Fuel_admin::NOTIFICATION_SUCCESS;
		return $this->_notifications[$type];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the nofication flash message to display
	 *
	 * @access	public
	 * @param	string	Notification message to display
	 * @param	type	The type of message to display. Options are error, success and info. (optional)
	 * @return	string
	 */	
	public function set_notification($msg, $type = '')
	{
		if (empty($type)) $type = Fuel_admin::NOTIFICATION_SUCCESS;
		$this->_notifications[$type] = $msg;
		$this->CI->session->set_flashdata($type, $msg);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean value based on if a notification has been set per a given type
	 *
	 * @access	public
	 * @param	type	The type of message to display. Options are error, success and info. (optional)
	 * @return	boolean
	 */	
	public function has_notification($type = '')
	{
		if (empty($type)) $type = Fuel_admin::NOTIFICATION_SUCCESS;
		return isset($this->_notifications[$type]);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of advanced module names that have dashboards to display (dashboard controllers) on the dashboard page
	 *
	 * @access	public
	 * @return	array
	 */	
	public function dashboards()
	{
		$dashboards = array();
		$dashboards_config = $this->fuel->config('dashboards');
		if (!empty($dashboards_config))
		{
			if (is_string($dashboards_config) AND strtoupper($dashboards_config) == 'AUTO')
			{
				$modules = $this->fuel->modules->advanced();

				foreach($modules as $module)
				{
					// check if there is a dashboard controller for each module
					if ($this->fuel->auth->has_permission($module) AND $module->has_dashboard())
					{
						$dashboards[] = $module;
					}
				}
			}
			else if (is_array($dashboards_config))
			{
				foreach($dashboards_config as $module)
				{
					if ($module == 'fuel' OR $this->fuel->auth->has_permission($module))
					{
						$dashboards[] = $module;
					}
				}
			}
		}
		return $dashboards;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds a dashboard to the FUEL configuration settings which will be displayed on the dashboard page
	 *
	 * @access	public
	 * @access	string	The name of the module that has the dashboard you want to add
	 * @return	void
	 */	
	public function add_dashboard($dashboard)
	{
		$dashboards = $this->fuel->config('dashboards');
		if (is_array($dashboard))
		{
			foreach($dashboards as $d)
			{
				if (!in_array($d, $dashbaords))
				{
					$dashboards[] = $d;
				}
			}
		}
		else
		{
			if (!in_array($d, $dashboard))
			{
				$dashboards[] = $dashboard;
			}
		}
		$this->fuel->set_config('dashboards', $dashboards);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of toolbar tools to display in hte toolbar
	 *
	 * @access	public
	 * @return	array
	 */	
	public function toolbar_tools()
	{
		$tools = array();
		$modules = $this->fuel->modules->advanced();
		
		//$modules = $this->fuel->config('modules_allowed');

		foreach($modules as $module)
		{
			// check if there is a dashboard controller for each module
			$t = $module->tools();
			if ($t AND $this->CI->fuel->auth->has_permission($module->name()))
			{
				$tools = $tools + $t;
			}
		}
		return $tools;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the inline editing toolbar HTML
	 *
	 * @access	public
	 * @return	void
	 */	
	public function toolbar()
	{
		$user_lang = $this->fuel->auth->user_lang();
		$this->fuel->load_language('fuel_inline_edit', $user_lang);
		$this->fuel->load_language('fuel_js', $user_lang);
	
		$vars['page'] = $this->fuel->page->properties();
		$vars['layouts'] = $this->fuel->layouts->options_list();
		$vars['language'] = $this->fuel->language->detect();
		$vars['language_mode'] = $this->fuel->language->mode();
		$vars['language_default'] = $this->fuel->language->default_option();
		$vars['tools'] = $this->toolbar_tools();
		$vars['js_localized'] = json_lang('fuel/fuel_js', $user_lang);
		$vars['is_fuelified'] = is_fuelified();
		$vars['can_edit_pages'] = $this->CI->fuel->auth->has_permission('pages', 'edit');

		if ($this->fuel->pages->mode() == 'views')
		{
			$vars['others'] = array();
		}
		else
		{
			
			$location = uri_path();
			$this->CI->load->module_model(FUEL_FOLDER, 'fuel_pages_model');
			$vars['others'] = $this->CI->fuel_pages_model->get_others('location', $location, 'location');
		}
		$vars['init_params']['pageId'] = (!empty($vars['page']['id']) ? $vars['page']['id'] : 0);
		$vars['init_params']['pageLocation'] = (!empty($vars['page']['location']) ? $vars['page']['location'] : uri_path());
		$vars['init_params']['basePath'] = WEB_PATH;
		$vars['init_params']['cookiePath'] = $this->CI->fuel->config('fuel_cookie_path');
		$vars['init_params']['imgPath'] = img_path('', 'fuel'); 
		$vars['init_params']['cssPath'] = css_path('', 'fuel'); 
		$vars['init_params']['jsPath'] = js_path('', 'fuel');
		$vars['init_params']['editor'] = $this->fuel->config('text_editor');
		$vars['init_params']['editorConfig'] = $this->fuel->config('ck_editor_settings');
		$last_page = uri_path();
		if (empty($last_page)) $last_page = $this->fuel->config('default_home_view');
		$vars['last_page'] = uri_safe_encode($last_page);
		
		$output = $this->CI->load->module_view(FUEL_FOLDER, '_blocks/inline_edit_bar', $vars, TRUE);
		
		return $output;
	}
}

/* End of file Fuel_admin.php */
/* Location: ./modules/fuel/libraries/Fuel_admin.php */