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
 * FUEL page variables 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_pagevars
 */

// --------------------------------------------------------------------

class Fuel_pagevars extends Fuel_base_library {
	
	public $location = ''; // the default location used for grabbing variables
	public $lang = 'english'; // the language
	public $vars_path = ''; // the path to the _variables folder
	public $include_pagevar_object = FALSE; // determines whether to include the $pagevars object or not
	public $honor_page_status = FALSE;
	
	const VARIABLE_TYPE_DB = 'db';
	const VARIABLE_TYPE_VIEW = 'views';
	
	public function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	public function initialize($params)
	{
		parent::initialize($params);

		if (empty($this->location))
		{
			$this->location = uri_path();
		}

		$default_home = $this->fuel->config('default_home_view');
		
		if (empty($this->location) OR $this->location == 'page_router')
		{
			$this->location = $default_home;
		}
	}
	
	
	/**
	 * Retrieve all the variables for a specific location
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public function retrieve($location = NULL, $what = '')
	{
		if (isset($location))
		{
			$this->location = $location;
		}

		switch($what)
		{
			case self::VARIABLE_TYPE_DB:
				return $this->db();
			case self::VARIABLE_TYPE_VIEW:
				return $this->view();
			default:
				$db = (array) $this->db();
				$view = (array) $this->view();

				//$page_data = (!empty($db)) ? $db : $view;
				$page_data = array_merge($view, $db);
				foreach($page_data as $key => $val)
				{
					if (empty($page_data[$key]) AND !empty($view[$key]))
					{
						$page_data[$key] = $view[$key];
					}
				}
				return $page_data;
		}
	}
	
	/**
	 * Retrieve the FUEL db variables for a specific location
	 *
	 * @access	public
	 * @param	boolean
	 * @return	array
	 */
	public function db($parse = FALSE)
	{
		$location = $this->location;

		$site_vars = $this->fuel->sitevars->get($location);
		
		$this->fuel->load_model('fuel_pagevariables_model');
		$this->CI->fuel_pagevariables_model->honor_page_status = $this->honor_page_status;
		$page_vars = $this->CI->fuel_pagevariables_model->find_all_by_location($location, $this->lang, $this->include_pagevar_object);
		
		// if the selected languages page variables is empty, then we try the default
		if (empty($page_vars))
		{
			$page_vars = $this->CI->fuel_pagevariables_model->find_all_by_location($location, $this->fuel->language->default_option(), $this->include_pagevar_object);
		}
		$vars = array_merge($site_vars, $page_vars);

		if ($parse)
		{
			$this->CI->load->library('parser');
			foreach($vars as $key => $val)
			{
				if (is_string($val))
				{
					$vars[$key] = $this->CI->parser->parse_string($val, $vars, TRUE);	
				}
			}
		}
		return $vars;
	}
	
	/**
	 * Retrieve the view variables for a specific location/controller
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public function view($controller = NULL){

		$location = $this->location;
		
		$vars = array();
		$page_vars = array();

		if (empty($this->vars_path)) $this->vars_path = APPPATH.'/views/_variables/';
		
		$global_vars = $this->vars_path.'global'.EXT;
		
		if (file_exists($global_vars))
		{
			require($global_vars);
		}
		
		// get controller name so that we can load in its corresponding variables file if exists
		if (empty($controller)) $controller = current(explode('/', $location));
		if (empty($controller) OR $controller == 'page_router') $controller = 'home';
		
		$controller_vars =  $this->vars_path.$controller.EXT;

		// load controller specific config if it exists... and any page specific vars
		if (file_exists($controller_vars))
		{
			require($controller_vars);
		}
		if (isset($pages) AND is_array($pages))
		{
			// loop through the pages array looking for wild-cards
			foreach ($pages as $key => $val)
			{

				// convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// does the RegEx match?
				if (preg_match('#^'.$key.'$#', $location))
				{
					$page_vars = array_merge($page_vars, $val);
				}
			}
		}
		
		// now merge global, controller and page vars with page vars taking precedence
		$vars = array_merge($vars, $page_vars);
		return $vars;
	}
}


/* End of file Fuel_pagevars.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_pagevars.php */