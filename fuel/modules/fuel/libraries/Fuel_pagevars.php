<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_pagevars {
	
	public $location = ''; // the default location used for grabbing variables
	public $vars_path = ''; // the path to the _variables folder
	
	private $_CI;
	
	function __construct()
	{
		$this->_CI =& get_instance();
		$this->location = uri_path();
	}
	
	/**
	 * Retrieve all the variables for a specific location
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function retrieve($location = null, $what = 'all')
	{
		if (is_null($location))
		{
			$location = $this->location;
		}
		
		switch($what)
		{
			case 'db' :
				return $this->db_variables($location);
			case 'views' :
				return $this->view_variables($location);
			default:
				$db = (array) $this->db_variables($location);
				$view = (array) $this->view_variables($location);

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
	 * @param	string
	 * @return	array
	 */
	function db_variables($location = null)
	{
		if (is_null($location))
		{
			$location = $this->location;
		}
		$this->_CI->load->module_model(FUEL_FOLDER, 'pagevariables_model', 'pagevariables_model');
		$this->_CI->load->module_model(FUEL_FOLDER, 'sitevariables_model', 'sitevariables_model');
		
		$site_vars = $this->_CI->sitevariables_model->find_all_array(array('active' => 'yes'));
		
		$vars = array();
		
		// Loop through the pages array looking for wild-cards
		foreach ($site_vars as $site_var){
			
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $site_var['scope']));

			// Does the RegEx match?
			if (empty($key) OR preg_match('#^'.$key.'$#', $location))
			{
				$vars[$site_var['name']] = $site_var['value'];
			}
		}
		$page_vars = $this->_CI->pagevariables_model->find_all_by_location($location);
		$vars = array_merge($vars, $page_vars);
		return $vars;
	}
	
	/**
	 * Retrieve the view variables for a specific location/controller
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function view_variables($location = null, $controller = null){
		if (is_null($location))
		{
			$location = $this->location;
		}
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

	/* SAVED FOR IN CASE WE WANT TO RESURRECT
	private function _preview_module_vars($location)
	{
		if (is_null($location))
		{
			$location = $this->location;
		}

		$this->_CI->load->module_library(FUEL_FOLDER, 'fuel_modules');
		$modules = $this->_CI->fuel_modules->get_modules();

		foreach($modules as $module => $info)
		{
			$mod_info = $this->_CI->fuel_modules->info($module);

			//$regex = preg_replace('#(.*\{)([a-zA-Z0-9-_]+)(\}.*)#', '$1([a-zA-Z0-9-_]+)$3', $mod_info['preview_path']);
			$regex = preg_match_all('#(.*\{)(/([a-zA-Z0-9-_]+)+)(\}.*)#', $mod_info['preview_path'], $matches);
			// echo "<pre style=\"text-align: left;\">";
			// print_r($matches);
			// echo "</pre>";
			// 
			// exit();
			preg_match('#'.$regex.'#', $location, $matches);
			if (!empty($matches[1]))
			{
				// echo "<pre style=\"text-align: left;\">";
				// print_r($matches);
				// echo "</pre>";

			}

			if (strpos($mod_info['preview_path'], '{') !== FALSE)
			{
				$preview_segs = explode('/', $mod_info['preview_path']);
				foreach($preview_segs as $seg)
				{
					if (preg_match('#.*\{([a-zA-Z0-9-_]+)\}.*#', $seg, $matches))
					{
						// echo "<pre style=\"text-align: left;\">";
						// print_r($matches);
						// echo "</pre>";

					}
				}
			}

			// echo "<pre style=\"text-align: left;\">";
			// print_r($info);
			// echo "</pre>";


		}

		$segs = $this->_CI->uri->segment_array();
		echo "<pre style=\"text-align: left;\">";
		print_r($segs);
		echo "</pre>";

		foreach($segs as $seg)
		{
			if (preg_match('#.*\{([a-zA-Z0-9-_]+)\}.*#', $seg, $matches))
			{
				echo "<pre style=\"text-align: left;\">";
				print_r($matches);
				echo "</pre>";

			}
		}
	}*/

}
/* End of file Fuel_pagevars.php */
/* Location: ./application/modules/fuel/libraries/Fuel_pagevars.php */