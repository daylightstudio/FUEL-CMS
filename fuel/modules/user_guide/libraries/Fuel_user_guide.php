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
 * User guide library
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/page_analysis
 */

// --------------------------------------------------------------------

class Fuel_user_guide extends Fuel_advanced_module {
	
	public $use_search = TRUE;
	public $use_breadcrumb = TRUE;
	public $use_nav = TRUE;
	public $use_footer = TRUE;
	public $display_options = array(
									'use_search' => TRUE,
									'use_breadcrumb' => TRUE,
									'use_nav' => TRUE,
									'user_footer' => TRUE,
									);
	protected $current_page;

	/**
	 * Constructor - Sets user guide preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->load->helper('text');
		$this->CI->load->helper('inflector');
		$this->CI->load->helper('utility');

		$this->fuel->load_library('fuel_pagevars');
		$this->load_helper('user_guide');
		
		if (!empty($params))
		{
			$this->initialize($params);
		}
		$this->init_page();
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the page analysis object
	 *
	 * Accepts an associative array as input, containing backup preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		$this->set_params($this->_config);
		
	}
	
	function current_page()
	{
		return $this->current_page;
	}

	function set_current_page($page)
	{
		$this->current_page = $page;
		return $this->current_page;
	}
	
	function init_page()
	{
		$uri = uri_path(FALSE);
		$root_url = $this->config('root_url');
		if (substr($root_url, -1) == '/') $root_url = substr($root_url, 0, (strlen($root_url) -1));
		$new_uri = preg_replace('#^'.$root_url.'#', '', $uri);
		if (empty($new_uri)) $new_uri = 'home';
		$this->set_current_page($new_uri);
	}
	
	function get_page_segment($segment)
	{
		$segment = $segment - 1;

		// clean off beginning and ending slashes
		$page = preg_replace('#^(\/)?(.+)(\/)?$#', '$2', $this->current_page);
		$segs = explode('/', $page);
		if (!empty($segs[$segment]))
		{
			return $segs[$segment];
		}
		return FALSE;
	}
	
	function get_page_title($page)
	{
		preg_match('#<h1>(.+)<\/h1>#U', $page, $matches);
		if (!empty($matches[1]))
		{
			return strip_tags($matches[1]);
		}
		return '';
		
	}
	
	function get_breadcrumb($page = NULL)
	{
		if (empty($page))
		{
			$page = $this->current_page;
		}
		$vars = $this->get_vars($this->current_page);
		$page_arr = explode('/', $page);
		if (count($page_arr) == 1) return '';
		array_pop($page_arr);
		$prev_page = implode('/', $page_arr);

		if (is_file(USER_GUIDE_PATH.'/views'.$prev_page.EXT))
		{
			$prev_view = $this->CI->load->module_view(USER_GUIDE_FOLDER, $prev_page, $vars, TRUE);
			$vars['breadcrumb'][$this->get_page_title($prev_view)] = $prev_page;
		}
		return $vars['breadcrumb'];
	}
	
	function get_vars($page = NULL)
	{
		if (empty($page))
		{
			$page = $this->current_page;
		}
		$vars = $this->CI->fuel_pagevars->view_variables($page, 'user_guide');
		$vars['modules'] = array();
		$vars['site_docs'] = '';
		$vars['use_search'] = $this->display_option('use_search');
		$vars['use_breadcrumb'] = $this->display_option('use_breadcrumb');
		$vars['use_nav'] = $this->display_option('use_nav');
		$vars['use_footer'] = $this->display_option('use_footer');
		$vars['sections'] = array();
		$vars['breadcrumb'] = array();
		return $vars;
	}
	
	function set_display_option($opt, $val)
	{
		$this->display_options[$opt] = $val;
	}
	
	function display_option($opt = NULL)
	{
		$opts = $this->display_options();
		if (isset($opts[$opt]))
		{
			return $opts[$opt];
		}
		return FALSE;
	}
	
	function display_options()
	{
		return $this->display_options;
	}
	
	
}

/* End of file Fuel_user_guide.php */
/* Location: ./modules/fuel/libraries/Fuel_user_guide.php */