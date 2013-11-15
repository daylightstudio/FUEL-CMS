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
 * FUEL pages 
 *
 * This class is used to find and create <a href="#fuel_page">Fuel_page</a> objects.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_pages
 */

// --------------------------------------------------------------------

class Fuel_pages extends Fuel_base_library {
	
	protected $_active = NULL; // the currently active page
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences
	 *
	 * @access	public
	 * @param	array	Config preferences (optional)
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Creates a Fuel_page
	 *
	 * @access	public
	 * @param	array	Page initialization preferences
	 * @param	boolean	Sets the page as the currently active
	 * @return	object
	 */	
	public function create($init = array(), $set_active = TRUE)
	{
		$page = new Fuel_page($init);
		if ($set_active)
		{
			$this->set_active($page);
		}
		return $page;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets a page as the currently active page
	 *
	 * @access	public
	 * @param	object	The page to set as active
	 * @return	void
	 */	
	public function set_active(&$page)
	{
		// for backwards compatability
		$this->CI->fuel_page = $page;
		$this->CI->fuel->attach('page', $page);
		$this->_active = $page;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a Fuel_page object given a provided location
	 *
	 * @access	public
	 * @param	string	The page to set as active
	 * @return	object
	 */	
	public function get($location)
	{
		$init['location'] = $location;
		$page = $this->create($init, FALSE);
		return $page;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the active page
	 *
	 * @access	public
	 * @return	object
	 */	
	public function &active()
	{
		return $this->_active;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an associative array of pages used for form option lists
	 *
	 * @access	public
	 * @param	string	The types of pages to include in the array. Options are cms, modules, views (optional)
	 * @param	boolean	Determines whether to use the URI location as the key (optional)
	 * @param	boolean	Applies the site_url() to the keys of the returned array (optional)
	 * @return	array
	 */	
	public function options_list($include = 'all', $paths_as_keys = FALSE, $apply_site_url = TRUE)
	{
		$valid_include = array('cms', 'modules', 'views');
		$pages = array();

		if (is_string($include))
		{
			if ($include == 'all')
			{
				$include = $valid_include;
			}
			else
			{
				$include = array($include);
			}
		}
		foreach($include as $method)
		{
			if (in_array($method, $valid_include))
			{
				$pages = array_merge($pages, $this->$method());
			}
		}
		
		// must get the merged unique values (array_values resets the indexes)
		$pages = array_values(array_unique($pages));
		sort($pages);
		
		if ($paths_as_keys)
		{
			$keyed_pages = array();
			foreach($pages as $page)
			{
				$key = ($apply_site_url) ? site_url($page) : $page;
				$keyed_pages[$key] = $page;
			}
			$pages = $keyed_pages;
		}
		
		return $pages;
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of view files pages used with opt-in controller method
	 *
	 * @access	public
	 * @return	array
	 */	
	public function views()
	{
		$this->CI->load->helper('directory');
		$views_path = APPPATH.'views/';
		$view_pages = directory_to_array($views_path, TRUE, '/^_(.*)|\.html$/', FALSE, TRUE);
		return $view_pages;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of pages created in the CMS
	 *
	 * @access	public
	 * @return	array
	 */	
	public function cms()
	{
		$this->fuel->load_model('fuel_pages');
		$cms_pages = $this->CI->fuel_pages_model->list_locations(FALSE);
		return $cms_pages;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of pages created by a module. The module must have a prevew path
	 *
	 * @access	public
	 * @return	array
	 */	
	public function modules()
	{
		return $this->fuel->modules->pages();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finds a Fuel_page object based on either a location or ID value
	 *
	 * @access	public
	 * @param	mixed	Either the pages location or ID value (CMS only)
	 * @return	object
	 */	
	public function find($id)
	{
		$this->fuel->load_model('fuel_pages');
		if (!is_numeric($id))
		{
			$page = $this->CI->fuel_pages_model->find_by_location($id);
		}
		else
		{
			$page = $this->CI->fuel_pages_model->find_by_key($id);
		}
		return $page;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the rendering mode for the pages module
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function mode()
	{
		$fuel_mode = $this->fuel->config('fuel_mode');
		if (is_array($fuel_mode))
		{
			if (isset($fuel_mode['pages']))
			{
				return $fuel_mode['pages'];
			}
			else
			{
				return 'auto';
			}
		}
		return $fuel_mode;

	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders a Fuel_page which includes any inline editing markers. 
	 * The 3rd parameter can contain Fuel_page class properties (e.g. array('render_mode' => 'cms'))
	 *
	 * @access	public
	 * @access	string	The location value of the page
	 * @access	array	Variables to pass to the page
	 * @access	array	Additional initialization parameters to pass to the page
	 * @access	boolean	Return the result or echo it out
	 * @return	string
	 */	
	public function render($location, $vars = array(), $params = array(), $return = FALSE)
	{
		// TODO: cant have this be called within another page or will cause an infinite loop
		$params['location'] = $location;
		$page = $this->create($params);
		$page->add_variables($vars);
		$output = $page->render($return);
		if ($output)
		{
			return $output;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Imports a block view file into the database
	 *
	 * @access	public
	 * @param	string	The name of the page to import to the CMS
	 * @param	boolean	Determines whether to sanitize the page by applying the php to template syntax function before uploading
	 * @return	string
	 */
	public function import($page, $sanitize = TRUE)
	{
		$this->CI->load->helper('file');

		if (!isset($this->CI->fuel_pages_model))
		{
			$this->CI->load->module_model(FUEL_FOLDER, 'fuel_pages_model');
		}
		$model =& $this->CI->fuel_pages_model;

		if (!is_numeric($page))
		{
			$page_data = $model->find_by_location($page, FALSE);
		}
		else
		{
			$page_data = $model->find_by_key($page, 'array');
		}
		
		$view_twin = APPPATH.'views/'.$page_data['location'].EXT;

		$pagevars = array();
		if (file_exists($view_twin))
		{

			// must have content in order to not return error
			$output = file_get_contents($view_twin);

			$pagevars['layout'] = $page_data['layout'];
			$layout = $this->fuel->layouts->get($pagevars['layout']);

			if (isset($layout) AND $layout->import_field())
			{
				$import_field = $layout->import_field();
			}
			else
			{
				$import_field = 'body';
			}

			// parse out fuel_set_var

			// for arrays... since I couldn't get it under one regex... not perfect but works OK
			$fuel_set_var_arr_regex = '#(?<!//)fuel_set_var\(([\'|"])(.+)\\1,\s*([^\)]+\s*\))\s*\)\s*;?#Um';
			//$pagevars = $this->_import_fuel_set_var_callback($fuel_set_var_arr_regex, $output, $sanitize, $pagevars);
			$output = preg_replace($fuel_set_var_arr_regex, '', $output);
	
			// for strings
			$fuel_set_var_regex = '#(?<!//)fuel_set_var\(([\'|"])(.+)\\1,\s*([^\)]+)\s*\)\s*;?#Um';
			//$pagevars = $this->_import_fuel_set_var_callback($fuel_set_var_regex, $output, $sanitize, $pagevars);
			$output = preg_replace($fuel_set_var_regex, '', $output);

			// cleanup empty tags
			$output = preg_replace('#<\?php\s*\?>#Ums', '', $output);


			// now get the variables loaded into the page by comparing the FUEL vars after a page is rendered
			$pre_render_vars = $this->CI->load->get_vars();
			$this->fuel->pages->render($page_data['location'], array('fuelified' => FALSE), array('render_mode' => 'views'), TRUE);
			$post_render_vars = $this->CI->load->get_vars();

			foreach($post_render_vars as $key => $val)
			{
				if (!isset($pre_render_vars[$key]) OR (isset($pre_render_vars[$key]) AND $pre_render_vars[$key] !== $val))
				{
					if (is_string($val) OR is_array($val))
					{
						if ($sanitize AND is_string($val))
						{
							$val = php_to_template_syntax($val);
						}
						$pagevars[$key] = $val;
					}
				}
			}
			if ($sanitize)
			{
				$output = php_to_template_syntax($output);
			}
			$pagevars[$import_field] = $output;

		}
		
		return $pagevars;
	}

	// --------------------------------------------------------------------

	/**
	 * Helper method used for parsing out fuel_set_var
	 *
	 * @access	protected
	 * @param	string	The regex to use for matches
	 * @return	array
	 */
	protected function _import_fuel_set_var_callback($regex, $output, $sanitize, $pagevars)
	{
		preg_match_all($regex, $output, $matches, PREG_SET_ORDER);
		foreach($matches as $match)
		{
			if (!empty($match[2]))
			{
				$match[3] = trim($match[3]);

				// fix array issue with regex
				$eval_str = '$_var = '.$match[3].';';

				eval($eval_str);
				$pagevars[$match[2]] = $_var;

				// replace PHP tags with template tags... comments are replaced because of xss_clean()
				if ($sanitize AND is_string($pagevars[$match[2]]))
				{
					$pagevars[$match[2]] = 	php_to_template_syntax($pagevars[$match[2]]);
				}
			}
		}
		return $pagevars;
	}


}

// ------------------------------------------------------------------------

/**
 * FUEL page object.
 *
 * Can be retrieved by $this->fuel->pages->get('{location}')
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @prefix		$page->
 */

class Fuel_page extends Fuel_base_library {
	
	public $id = NULL; // the page ID if it is coming from a database
	public $location = ''; // the uri location 
	public $layout = ''; // the layout file to apply to the view
	public $is_published = TRUE; // whether the page can be seen or not
	public $is_cached = TRUE; // is the file cached in the system cache directory
	public $views_path = ''; // the path to the views folder for rendering. Used with modules
	public $render_mode = 'views'; // values can be either "views" or "cms"
	public $view_module = 'app'; // the module to look for the view file
	public $language = ''; // the language to use for rendering both a static view and CMS page
	public $markers = array(); // the inline editing markers for the page
	public $include_pagevar_object = TRUE; // includes a $pagevar object that contains the variables for the page
	public $vars_honor_page_status = FALSE; // determines whether to honor the page's published status when pulling variable data from the CMS
	public $only_published = TRUE; // view only published pages is used if the person viewing page is not logged in
	public static $marker_key = '__FUEL_MARKER__'; // used for placing inline editing content in the output
	
	protected $_variables = array(); // variables applied to the page
	protected $_segments = array(); // uri segments to pass to the page
	protected $_page_data = array(); // specific data about the page (not variables being passed necessarily)
	protected $_fuelified = FALSE; // is the person viewing the page logged in?... If so we will make inline editing and toolbar visible
	protected $_fuelified_processed = FALSE; // if fuelify has already been called	

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
		parent::__construct();
		$this->CI->load->helper('cookie');
		
		// cookie check... would be nice to remove to speed things up a little bit
		if (is_fuelified())
		{
			$this->_fuelified = TRUE;
			$this->only_published = FALSE;
		}
		
		if (!empty($params))
		{
			$this->initialize($params);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences. If a string is passed it will assume it is the location property
	 *
	 * @access	public
	 * @param	mixed	config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		// if $params is a string then we will assume that they are passing the most common parameter location
		if (is_string($params))
		{
			$params = array('location' => $params);
		}
		
		// setup any intialized variables
		foreach ($params as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		
		// assign the location of the page
		$this->assign_location($this->location);

		// grab the view path from a view module if it exists
		if (empty($this->views_path) AND $this->view_module != 'app')
		{
			$mod = $this->fuel->modules->get($this->view_module);
			if ($mod AND method_exists($mod, 'path'))
			{
				$this->views_path = $mod->path().'views/';
			}
		}

		// assign layout
		$this->assign_layout($this->layout);

		// assign variables to the page
		$this->assign_variables($this->views_path);

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Assigns the location value of the page
	 *
	 * @access	public
	 * @param	string	location to assign to the page
	 * @return	void
	 */	
	public function assign_location($location)
	{

		$this->language = ($this->fuel->language->has_multiple()) ? $this->fuel->language->detect() : $this->fuel->language->default_option();

		$this->location = $location;
		
		$default_home = $this->fuel->config('default_home_view');

		if (empty($this->location) OR $this->location == 'page_router') $this->location = $default_home;
		
		$page_data = array('id' => NULL, 'cache' => NULL, 'published' => NULL, 'layout' => NULL, 'location' => NULL);
		$this->_page_data = $page_data;

		if ($this->render_mode == 'views')
		{
			return;
		}
		
		// if a location is provided in the init config, then use it instead of the uri segments
		if (!empty($this->location))
		{
			$segs = explode('/', $this->location);
			$segments = array_combine(range(1, count($segs)), array_values($segs));
		}
		else
		{
			$segments = $this->CI->uri->rsegment_array();
		}

		// in case a module has a name the same (like news...)
		if (!empty($segments))
		{
			if ($segments[count($segments)] == 'index')
			{
				array_pop($segments);	
			}
		}

		// check if we are using language segments
		if ($this->fuel->language->has_multiple())
		{
			$lang_seg = (empty($segments)) ? '' : $segments[1];
			if ($this->fuel->language->lang_segment($lang_seg))
			{
				$this->fuel->language->set_selected($lang_seg);
				$this->language = $lang_seg;
				//array_shift($segments);	
			}
		}

		// MUST LOAD AFTER THE ABOVE SO THAT IT DOESN'T THROW A DB ERROR WHEN A DB ISN'T BEING USED
		// get current page segments so that we can properly iterate through to determine what the actual location 
		// is and what are params being passed to the location
		$this->CI->load->module_model(FUEL_FOLDER, 'fuel_pages_model');

		//if (count($this->CI->uri->segment_array()) == 0 OR $this->location == $default_home) 
		if (count($segments) == 0 OR $this->location == $default_home) 
		{
			$page_data = $this->CI->fuel_pages_model->find_by_location($default_home, $this->only_published);
			$this->location = $default_home;
		} 
		else 
		{
			// if $location = xxx/yyy/zzz/, check first to see if /xxx/yyy/zzz exists in the DB, then reduce segments to xxx/yyy,
			// xxx... until one is found in the DB. If only xxx is found in the database yyy and zzz will be treated as parameters
			
			// determine max page params
			$max_page_params = $this->get_max_page_param();
			
			$matched = FALSE;
			while(count($segments) >= 1)
			{
				if (count($this->_segments) > (int)$max_page_params)
				{
					break;
				}
				
				$location = implode('/', $segments);

				// if a prefix for the location is provided in the config, change the location value accordingly so we can find it
				$prefix = $this->fuel->config('page_uri_prefix');
				if ($prefix)
				{
					if (strpos($location, $prefix) === 0)
					{
						$location = substr($location, strlen($prefix));
					}
					$page_data = $this->CI->fuel_pages_model->find_by_location($location, $this->only_published);
				}
				else
				{
					$page_data = $this->CI->fuel_pages_model->find_by_location($location, $this->only_published);
				}
				
				if (!empty($page_data))
				{
					break;
				}
				$this->_segments[] = array_pop($segments);
			}
		}
		
		if (!empty($page_data['id']))
		{
			$this->id = $page_data['id'];
			$this->location = $page_data['location'];
			$this->layout = $page_data['layout'];
			$this->is_published = is_true_val($page_data['published']);
			$this->is_cached = is_true_val($page_data['cache']);
			$this->_segments = $segments;
			$this->_page_data = $page_data;
		}

		// assign page segment values
		$this->_segments = array_reverse($this->_segments);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Assigns variables to the page
	 *
	 * @access	public
	 * @param	string	The server path to the views file (optional)
	 * @param	string	Determines whether to assign variables from the CMS as well as variables from the _variables folder (optional)
	 * @return	void
	 */	
	public function assign_variables($views_path = NULL, $page_mode = NULL)
	{
		$this->views_path = (empty($views_path)) ? APPPATH.'views/' : $views_path;
		$page_mode = (empty($page_mode)) ? $this->fuel->pages->mode() : $page_mode;
		
		$vars_path = $this->views_path.'_variables/';
		$init_vars = array('vars_path' => $vars_path, 'lang' => $this->language, 'include_pagevar_object' => $this->include_pagevar_object, 'honor_page_status' => $this->vars_honor_page_status);
		$this->fuel->pagevars->initialize($init_vars);
		$vars = $this->fuel->pagevars->retrieve($this->location, $page_mode);
		$this->add_variables($vars);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Assigns the layout of the page
	 *
	 * @access	public
	 * @param	mixed	Can be either a name of the layout or a Fuel_layout object
	 * @return	void
	 */	
	public function assign_layout($layout)
	{
		if (!empty($layout) AND is_string($layout))
		{
			$layout = $this->fuel->layouts->get($layout);
		}
		
		if (is_a($layout, 'Fuel_layout'))
		{
			$this->layout = $layout;
			$this->include_pagevar_object = $this->layout->include_pagevar_object;
		}
		else
		{
			$this->layout = '';
		}

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders the page either from the CMS or via the "opt-in controller" method. Pages in the CMS take precedence
	 *
	 * @access	public
	 * @param	boolean	Determines whether to return the value or to echo it out (optional)
	 * @param	boolean	Determines whether to render any inline editing (optional)
	 * @return	string
	 */	
	public function render($return = FALSE, $fuelify = TRUE)
	{
		// check the _page_data to see if it even exists in the CMS
		if (!isset($this->_page_data['id']))
		{
			$this->render_mode = 'views';
			return $this->variables_render($return, $fuelify);
		}
		else
		{
			$this->render_mode = 'cms';
			return $this->cms_render($return, $fuelify);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a CMS page
	 *
	 * @access	public
	 * @param	boolean	Determines whether to return the value or to echo it out
	 * @param	boolean	Determines whether to render any inline editing
	 * @return	string
	 */
	public function cms_render($return = FALSE, $fuelify = FALSE)
	{

		$this->CI->load->library('parser');

		// render template with page variables if data exists
		if (!empty($this->layout))
		{
			$field_values = $this->layout->field_values();
			
			$vars = array_merge($field_values, $this->variables());
			
			$this->load_resources($vars);
			
			// call layout hook
			$this->layout->call_hook('pre_render', array('vars' => $vars));

			// run the variables through the pre_process method on the layout
			$vars = $this->layout->pre_process($vars);
			$layout_vars = $vars;
			$layout_vars['CI'] =& $this->CI;
			$output = $this->CI->load->view($this->layout->view_path(), $layout_vars, TRUE);

			// now parse any template like syntax...
			$output = $this->CI->parser->parse_string($output, $vars, TRUE);
			unset($layout_vars);

			// check if the content should be double parsed
			if ($this->layout->is_double_parse())
			{
				// first parse any template like syntax
				$this->CI->parser->parse_string($output, $vars, TRUE);

				// then grab variables again
				$ci_vars = $this->CI->load->get_vars();

				// then parse again to get any variables that were set from within a block
				$output = $this->CI->load->view($this->layout->view_path(), $ci_vars, TRUE);
				$output = $this->CI->parser->parse_string($output, $ci_vars, TRUE);
				unset($ci_vars);
			}
			else
			{
				// parse any template like syntax
				$output = $this->CI->parser->parse_string($output, $vars, TRUE);

			}
			
			// call layout hook
			$this->layout->call_hook('post_render', array('vars' => $vars, 'output' => $output));
			
			// run the post_process layout method... good for appending to the output (e.g. google analytics code)
			$output = $this->layout->post_process($output);

			// turn on inline editing if they are logged in and cookied
			if ($fuelify) $output = $this->fuelify($output);
		
			if ($return) 
			{
				return $output;
			}
		
			$this->CI->output->set_output($output);
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a static view page that is using the "opt-in controller" method
	 *
	 * @access	public
	 * @param	boolean	Determines whether to return the value or to echo it out (optional)
	 * @param	boolean	Determines whether to render any inline editing (optional)
	 * @return	string
	 */
	public function variables_render($return = FALSE, $fuelify = FALSE)
	{
		// get the location and load page vars
		$page = $this->location;

		$vars = $this->variables();
		
		$this->load_resources($vars);
		
		// for convenience we'll add the $CI object'
		$vars['CI'] = &$this->CI;
		$this->CI->load->vars($vars);

		if (!empty($vars['view']))
		{
			$view = $vars['view'];
		}
		else
		{
			$view = $page;

			// if view is the index.html file, then show a 404
			if ($view == 'index.html')
			{
				redirect_404();
			}

			// do not display any views that have an underscore at the beginning of the view name, or is part of the path (e.g. about/_hidden/contact_email1.php)
			$view_parts = explode('/', $view);

			foreach($view_parts as $view_part)
			{
				if (!strncmp($view_part, '_', 1))
				{
					show_404();
				}
			}

		}

		$output = NULL;
		
		// test that the file exists in the associated language
		if (!empty($this->language) AND !$this->fuel->language->is_default($this->language))
		{
			$view_tmp = 'language/'.$this->language.'/'.$view;
			if (file_exists($this->views_path . $view_tmp .'.php'))
			{
				$view = $view_tmp;
			}
		}

		// set the extension... allows for sitemap.xml for example
		$ext = '.'.pathinfo($view, PATHINFO_EXTENSION);
		$check_file = $this->views_path.$view;

		// added .php so $check_file will essentially not work and will then go to any redirects or 404
		if (empty($ext) OR $ext == '.' OR $ext == '.php') 
		{
			$ext = EXT;
			$check_file = $this->views_path.$view.$ext;
		}

		// find a view file
		if (!file_exists($check_file))
		{
			if (!isset($vars['find_view']) OR (isset($vars['find_view']) AND $vars['find_view'] !== FALSE))
			{
				$view = $this->find_view_file($view);
				$check_file = $this->views_path.$view.$ext;
			}
		}
		
		// if view file exists, set the appropriate layout 
		if (file_exists($check_file))
		{
			// set layout variable if it isn't set yet'
			if (!empty($vars['layout']))
			{
				$layout = $vars['layout'];
				$this->layout = $this->fuel->layouts->get($layout);
			}

			if ($this->layout)
			{
				// call layout hook
				$this->layout->call_hook('pre_render', array('vars' => $vars));

				// run the variables through the pre_process method on the layout
				// !important ... will reference the layout specified to this point so a layout variable set within the body of the page will not work
				$vars = $this->layout->pre_process($vars);
			}

			// load the file so we can parse it 
			if (!empty($vars['parse_view']))
			{
				// load here to save on execution time... 
				$this->CI->load->library('parser');
				
				$body = file_get_contents($check_file);

				// now parse any template like syntax
				$vars = $this->CI->load->get_vars();
				$body = $this->CI->parser->parse_string($body, $vars, TRUE);
			}
			else
			{
				$body = $this->CI->load->module_view($this->view_module, $view, $vars, TRUE);
			}

			// now set $vars to the cached so that we have a fresh set to send to the layout in case any were declared in the view
			$vars = $this->CI->load->get_vars();

			// set layout variable again if it's changed'
			if (isset($vars['layout']) AND (empty($this->layout) OR (is_object($this->layout) AND $this->layout->name != $vars['layout'])))
			{
				$layout = $vars['layout'];
				if (empty($layout)) $layout = '';
				$this->layout = $this->fuel->layouts->get($layout);	
			}

			if ($this->layout)
			{
				$this->_page_data['layout'] = $layout;
				if (is_object($this->layout))
				{
					$layout = $this->layout->name;
				}
			}
			else
			{
				$this->_page_data['layout'] = NULL;
				$layout = FALSE;
			}

			if (!empty($layout))
			{
				$vars['body'] = $body;
				$layout_dir = trim($this->fuel->layouts->layouts_folder, '/'); // remove any trailing slash... we'll add it below'
				if (strncmp($layout, $layout_dir, strlen($layout_dir)) !== 0)
				{
					$layout = $layout_dir.'/'.$layout;
				}
				$output = $this->CI->load->view($layout, $vars, TRUE);
			}
			else
			{
				$output = $body;
			}
		}
		
		if (empty($output) && empty($vars['allow_empty_content'])) return FALSE;
		

		// call layout hook
		if ($layout)
		{
			$this->layout->call_hook('post_render', array('vars' => $vars, 'output' => $output));

			// run the post_process layout method... good for appending to the output (e.g. google analytics code)
			$output = $this->layout->post_process($output);
		}

		if ($fuelify) $output = $this->fuelify($output);

		if ($return)
		{
			return $output;
		}
		else
		{
			$this->CI->output->set_output($output);
			
			return TRUE;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads any resources by checking for a "library", "helpers", or "models" variable assigned to the page
	 *
	 * @access	public
	 * @param	array	An array of variables to check for resources
	 * @return	void
	 */
	public function load_resources($vars = NULL)
	{
		if (empty($vars))
		{
			$vars = $this->variables();
		}
		
		// load helpers
		if (!empty($vars['helpers']))
		{
			if (is_string($vars['helpers']))
			{
				$vars['helpers'] = array($vars['helpers']);
			}

			foreach($vars['helpers'] as $key => $val)
			{
				if (!is_numeric($key))
				{
					$this->CI->load->module_helper($key, $val);
				}
				else
				{
					$this->CI->load->helper($val);
				}
			}
		}
			
		// load libraries
		if (!empty($vars['libraries']))
		{
			if (is_string($vars['libraries']))
			{
				$vars['libraries'] = array($vars['libraries']);
			}

			foreach($vars['libraries'] as $key => $val)
			{
				if (!is_numeric($key))
				{
					$this->CI->load->module_library($key, $val);
				}
				else
				{
					$this->CI->load->library($val);
				}
			}
		}

		// load models
		if (!empty($vars['models']))
		{
			if (is_string($vars['models']))
			{
				$vars['models'] = array($vars['models']);
			}

			foreach($vars['models'] as $key => $val)
			{
				if (!is_numeric($key))
				{
					$this->CI->load->module_model($key, $val);
				}
				else
				{
					$this->CI->load->model($val);
				}
			}
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders the inline editing markers before final output
	 *
	 * @access	public
	 * @param	string	The output to be rendered
	 * @return	string
	 */
	public function fuelify($output)
	{
		// if not logged in then we remove the markers
		if (!$this->fuel->config('admin_enabled') OR $this->variables('fuelified') === FALSE OR 
			!$this->_fuelified OR empty($output) OR (defined('FUELIFY') AND FUELIFY === FALSE) ) 
		{
			return $this->remove_markers($output);
		} 
		
		$this->CI->load->helper('convert');
		
		
		// add top edit bar for fuel
		$this->CI->config->module_load('fuel', 'fuel', TRUE);
		
		// render the markers to the proper html
		$output = $this->render_all_markers($output);
		
		// set main image and assets path before switching to fuel assets path
		$vars['init_params'] = array(
			'assetsImgPath' => img_path(''),
			'assetsPath' => assets_path(''),
			);
		
		$this->CI->asset->assets_path = $this->fuel->config('fuel_assets_path');
		$this->CI->load->helper('ajax');
		$this->CI->load->library('form');
		$last_page = uri_path();
		if (empty($last_page)) $last_page = $this->fuel->config('default_home_view');
		$vars['last_page'] = uri_safe_encode($last_page);

		if (!$this->_fuelified_processed)
		{
			// create the inline edit toolbar
			$inline_edit_bar = $this->fuel->admin->toolbar();
			$fuel_js_obj = "<script>if (typeof fuel == 'undefined') fuel = {}</script>\n";
			$inline_css = css('fuel_inline', 'fuel', array('output' => $this->fuel->config('fuel_assets_output')));
			$output = preg_replace('#(</head>)#i', $fuel_js_obj.$inline_css."\n$1", $output);
			$output = preg_replace('#(</body>)#i', $inline_edit_bar."\n$1", $output);
			$this->CI->config->set_item('assets_path', $this->CI->config->item('assets_path'));
		}
		$this->_fuelified_processed = TRUE;
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds marker code to the page for inline editing areas and returns a unique key value to identify it during the final rendering process
	 *
	 * @access	public
	 * @param	array	An array of marker values which includes, id, label, module, published, xoffset, and yoffset values
	 * @return	string
	 */
	public function add_marker($marker)
	{
		$key = $this->get_marker_prefix().count($this->markers);
		$this->markers[$key] = $marker;
		return $key;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the marker prefix used for adding markers
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_marker_prefix()
	{
		return Fuel_page::$marker_key;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the regular expression needed to find markers on the page
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_marker_regex()
	{
		$marker_prefix = $this->get_marker_prefix();
		$marker_reg_ex = '<!--('.$marker_prefix.'\d+)-->';
		return $marker_reg_ex;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a marker on the page which is used on the front end to create the inline editng pencil icons with some javascript
	 *
	 * @access	public
	 * @param	string	The markers key value used to identify it on the apge
	 * @return	string
	 */
	public function render_marker($key)
	{
		if (!isset($this->markers[$key])) return '';
		
		$marker = $this->markers[$key];
		if (empty($marker)) return '';
		extract($marker);

		// fix for pages permission
		$perm = ($module == 'pagevariables') ? 'pages' : $module;
		
		if ($this->fuel->config('admin_enabled') AND 
			is_fuelified() AND 
			(is_null($this->CI->load->get_var('fuelified')) OR $this->CI->load->get_var('fuelified') === TRUE)
			AND $this->CI->fuel->auth->has_permission($perm, 'edit') 
			)
		{
			if (empty($label))
			{
				$label_arr = explode('|', $id);
				$label = (!empty($label_arr[1])) ? $label_arr[1] : $label_arr[0];
				$label = ucfirst(str_replace('_', ' ', $label));
			}

			// must use span tags because nesting anchors can cause issues;
			$published = (is_true_val($marker['published'])) ? '1' : '0';
			$edit_method = (empty($id) OR substr($id, 0, 6) == 'create') ? 'inline_create' : 'inline_edit';
			
			$output = '<span class="__fuel_marker__" data-href="'.fuel_url($module).'/'.$edit_method.'/" data-rel="'.$id.'" title="'.$label.'" data-module="'.$module.'" data-published="'.$published.'"';
			if (isset($xoffset) OR isset($yoffset))
			{
				$output .= ' style="';
				if (isset($xoffset)) $output .= 'left:'.$xoffset.'px;';
				if (isset($yoffset)) $output .= 'top:'.$yoffset.'px;';
				$output .= '"';
			}
			$output .= "></span>";
			return $output;
		}
		return '';
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finds all the markers in the output and renders them
	 *
	 * @access	public
	 * @param	string	The output of the page
	 * @return	string
	 */
	public function render_all_markers($output)
	{
		// get the marker regex
		$marker_reg_ex = $this->get_marker_regex();
		if (stripos($output, '<html') !== FALSE AND stripos($output, '</html>') !== FALSE)
		{	

			// move all edit markers in attributes to before the node
			$callback = create_function(
			            // single quotes are essential here,
			            // or alternative escape all $ as \$
			            '$matches',
			            '
						$CI =& get_instance();
						$marker_reg_ex = $CI->fuel->page->get_marker_regex();
						$output = $matches[0];
						preg_match_all("#".$marker_reg_ex."#", $matches[0], $tagmatches);
						if (!empty($tagmatches[0]))
						{
							// clean out the tag and append them before the node
							$output = $CI->fuel->page->remove_markers($matches[0]);
							$output = implode($tagmatches[0], " ").$output;
						}
						return $output;'
			        );

			$output = preg_replace_callback('#<[^>]+=["\'][^<]*'.$marker_reg_ex.'.*(?<!--)>#Ums', $callback, $output);
			//$output = preg_replace('#(=["\'][^<]*)('.$marker_reg_ex.')#Ums', '${2}${1}', $output);  // doesn't work with fuel_var in multiple tag attributes

			// extract everything above the body
			preg_match_all('/(.*)<body/Umis', $output, $head);
			// get all the markers in the head and move them to within the body
			if (!empty($head[1][0]))
			{
				// match all markers in head
				preg_match_all('/('.$marker_reg_ex.')/Umis', $head[1][0], $matches);

				// append them to the body
				if (!empty($matches[1]))
				{
					$head_markers = implode("\n", array_unique($matches[1]));
					$output = preg_replace('/(<body[^>]*>)/e', '"\\1\n".\$head_markers', $output);
				}
				// remove the markers from the head now that we've captured them'
				$cleaned_head = preg_replace('/('.$marker_reg_ex.')/', '', $head[1][0]);
				
				// replace the cleaned head back on.. removed 's' modifier because it was causing memory issues
				//$output = preg_replace('/(.*)(<body.+)/Umis', "$cleaned_head\\2", $output);
				preg_match('/(<body.+){1}/mis', $output, $matches);
				$body = $matches[0];
				$output = $cleaned_head.$body;
			}
		}
		else
		{
			// if not html, then we remove the marker and inline editing is not available
			$output = $this->remove_markers($output);
		}

		$output = preg_replace_callback('#'.$marker_reg_ex.'#U', array($this, '_render_markers_callback'), $output);
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finds all the markers in the output and renders them
	 *
	 * @access	protected
	 * @param	string	The output of the page
	 * @return	string
	 */	protected function _render_markers_callback($matches)
	{
		return $this->render_marker($matches[1]);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finds all the markers in the output and renders them
	 *
	 * @access	public
	 * @param	string	The output of the page
	 * @return	string
	 */
	public function remove_markers($output)
	{
		// if no markers, then we simply return the output to speed up processing
		// if (empty($this->markers)) return $output; // needs to run all the time in case it's cached'
		
		// get the marker regex and replace it with nothing
		$marker_reg_ex = $this->get_marker_regex();
		$output = preg_replace('#'.$marker_reg_ex.'#', '', $output);
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the Fuel_page properties which includes location, layout, published and cached values
	 *
	 * @access	public
	 * @param	string	The output of the page (optional)
	 * @return	string
	 */
	public function properties($prop = NULL)
	{
		if (is_string($prop) AND isset($this->_page_data[$prop])) return $this->_page_data[$prop];
		return $this->_page_data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns all the variables currently assigned to the page for rendering
	 *
	 * @access	public
	 * @param	string	The output of the page
	 * @return	string
	 */
	public function variables($key = NULL)
	{
		if (is_string($key) AND isset($this->_variables[$key])) return $this->_variables[$key];
		return $this->_variables;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds variables to the page for rendering
	 *
	 * @access	public
	 * @param	array	An array of variables to assign to the page for rendering
	 * @return	void
	 */
	public function add_variables($vars = array())
	{
		$vars = (array) $vars;
		$this->_variables = array_merge($this->_variables, $vars);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns segment value(s) passed to a CMS page. 
	 *
	 * For example, a page with a location of "projects" in the CMS can have segment parameters passed to it (e.g. <dfn>projects/123456</dfn>).
	 * The FUEL config value of "max_page_params" must have a value greater then 0 (which is the default) for parameters to be passed. 
	 * If no segment number is specified, all segments will be returned.
	 *
	 *
	 * @access	public
	 * @param	string	The segment number to return (optional)
	 * @return	string
	 */	
	public function segment($n = NULL)
	{
		if (is_int($n) AND isset($this->_segments[$n])) return $this->_segments[$n];
		return $this->_segments;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the page is from the CMS or not
	 *
	 * @access	public
	 * @param	string	The output of the page
	 * @return	string
	 */
	public function has_cms_data()
	{
		return !empty($this->_page_data['location']);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns whether the page should be cached
	 *
	 * @access	public
	 * @param	string	The output of the page
	 * @return	boolean
	 */	
	public function is_cached()
	{
		return is_true_val($this->is_cached);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Saves a page to the CMS
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function save()
	{
		$this->fuel->load_model('fuel_pages');
		$this->fuel->load_model('fuel_pagevariables');
		
		$page_props = $this->CI->fuel_pages_model->create();
		$page_props->location = $this->location;
		if (empty($this->layout))
		{
			$layout = new Fuel_layout();
		}
		else if (is_string($this->layout))
		{
			$layout = $this->fuel->layouts->get($this->layout);
		}
		else
		{
			$layout = $this->layout;
		}
		
		$page_props->layout = $layout->name;
		$page_props->published = $this->is_published;
		$page_props->cache = $this->is_cached;
		if (empty($page_props->is_published))
		{
			$page_props->published = 'yes';
		}

		if (empty($page_props->is_cached))
		{
			$page_props->cache = 'yes';
		}
		
		if (!($id = $page_props->save()))
		{
			return FALSE;
		}
		
		$page_vars = $this->variables();

		$valid = TRUE;
		foreach($page_vars as $key => $val)
		{
			$page_var = $this->CI->fuel_pagevariables_model->create();
			$page_var->page_id = $id;
			$page_var->name = $key;
			if (is_array($val))
			{
				$val = serialize($val);
				$page_var->type = 'array';
			}
			$page_var->value = $val;
			
			if (!$page_var->save())
			{
				$valid = FALSE;
			}
			
		}
		return $valid;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Deletes a page from the CMS
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function delete()
	{
		// remove cached files
		$this->fuel->load_model('pages');
		$page_props = $this->properties();
		$this->fuel->cache->clear_pages();
		if (isset($page_props['id']))
		{
			$where['id'] = $page_props['id'];
			return $this->CI->fuel_pages_model->delete($where);
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Uses the assigned "location" value to scan the views directory for a corresponding view file. 
	 * The Fuel configuration value of "auto_search_views" must be set to TRUE or a number greater than 1 (it is FALSE by default).
	 * If a number is provided, it will loop
	 *
	 * @access	public
	 * @param	string	The original location value.
	 * @param	int	The number of levels deep to search for a view
	 * @return	string
	 */
	public function find_view_file($view, $depth = NULL)
	{
		if (is_null($depth))
		{
			$depth = (is_int($this->fuel->config('auto_search_views'))) ? $this->fuel->config('auto_search_views') : $this->get_max_page_param(); // if not a number (e.g. set to TRUE), we default to 2
		}
		
		if (!$this->fuel->config('auto_search_views') AND empty($depth)) return NULL;

		static $cnt;
		if (is_null($cnt)) $cnt = 0;
		$cnt++;
		$view_parts = explode('/', $view);
		array_pop($view_parts);
		$view = implode('/', $view_parts);
		if (!file_exists($this->views_path.$view.EXT) AND count($view_parts) > 1 AND $cnt < $depth)
		{
			$view = $this->find_view_file($view, $depth);
		}
		return $view;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the maximum number of page parameters associated with the current page
	 *
	 * @access	public
	 * @return	int
	 */
	public function get_max_page_param()
	{
		static $max_page_params;

		// determine max page params
		if (is_null($max_page_params))
		{
			$max_page_params = 0;	
		}
		
		if (is_array($this->fuel->config('max_page_params')))
		{
			//$location = implode('/', $this->CI->uri->rsegment_array());
			$location = uri_path(); // use this function instead so it will remove any language parameters
			
			foreach($this->fuel->config('max_page_params') as $key => $val)
			{
				// add any match to the end of the key in case it doesn't exist (no problems if it already does)'
				$key .= ':any';
				
				// convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// does the RegEx match?
				if (preg_match('#^'.$key.'$#', $location))
				{
					$max_page_params = $val;
					break;
				}
			}
		}
		else
		{
			$max_page_params = (int)$this->fuel->config('max_page_params');
		}
		return $max_page_params;
	}
}

/* End of file Fuel_pages.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_pages.php */