<?php

class Page_router extends CI_Controller {

	public $segments = array();
	public $layout = '';
	public $location;
	
	function __construct()
	{
		parent::__construct();
		$this->load->add_package_path(FUEL_PATH, FALSE);
	}
	
	function _remap($method)
	{
		$this->location = uri_path(TRUE);
		
		// if the rerouted file can't be found, look for the non-routed file'
		if (!file_exists(APPPATH.'views/'.$this->location.EXT))
		{
			$this->location = uri_path(FALSE);
		}
		
		if (empty($this->location)) $this->location = $this->fuel->config('default_home_view');

		$config = array();
		$config['location'] = $this->location;
		
		if ($this->fuel->config('fuel_mode') == 'views')
		{
			$config['render_mode'] = 'views';
			$page = $this->fuel->pages->create($config);
			
			$this->_remap_variables($page);
		}
		
		// using FUEL admin
		else
		{
			if ( $this->fuel->config('fuel_mode') != 'cms')
			{
				$config['render_mode'] = 'auto';
				$page = $this->fuel->pages->create($config);
				if (!$page->has_cms_data())
				{
					$this->_remap_variables($page);
					return;
				}
			}
			
			$this->_remap_cms($page);
		}
		
	}
	
	
	/*
	* ------------------------------------------------------
	* Checks database for page variables (FUEL CMS)
	* ------------------------------------------------------
	*/
	function _remap_cms($page)
	{
		$page_data = $page->properties();
		$this->load->helper('cookie');

		// set up cache info 
		$cache_group = $this->fuel->config('page_cache_group');
		$cache_id = $this->fuel->cache->create_id();

		$output = '';
		
		
		// grab from cache if exists without checking expiration time... 
		// Also.. saving from FUEL will remove cached page so you will always preview the latest saved
		if ($this->fuel->config('use_page_cache') !== 'views' AND $this->fuel->cache->get($cache_id, $cache_group, FALSE) AND $page->is_cached() AND !is_fuelified())
		{
			$output = $this->cache->get($cache_id, $cache_group);
		}
		else
		{
			if (!empty($page->layout))
			{
				
				// get output
				$output = $page->cms_render(TRUE, FALSE);

				// save to cache but you must not be logged in for it to save
				if ($this->fuel->config('use_page_cache') !== FALSE AND $this->fuel->config('use_page_cache') !== 'views' AND !is_fuelified())
				{
					$this->cache->save($cache_id, $output, $cache_group, $this->fuel->config('page_cache_ttl'));
				}
				
			}
			else
			{
				// do any redirects... will exit script if any
				$this->_redirects();

				// else show 404
				show_404();
			}
		}

		// fuelify
		$output = $page->fuelify($output);
		
		// render output
		$this->output->set_output($output);
		
		// call the post render layout hook
		$this->fuel->layouts->call_hook($page_data['layout'], 'post_render', $output);
		
	}
	
	/*
	* ------------------------------------------------------
	* If a controller method exists then call it. Otherwise, 
	* look for a corresponding view file if one exists. 
	* Eliminates the need to create controller methods with 
	* each page. It also will pull in the global and "section"
	* specific configuration variables if they exist
	* ------------------------------------------------------
	*/
	function _remap_variables($page){
		
		// set up cache info 
		$cache_group = $this->fuel->config('page_cache_group');
		$cache_id = $this->fuel->cache->create_id();
		if ($this->fuel->config('use_page_cache') !== 'cms' AND $this->cache->get($cache_id, $cache_group, FALSE) AND !is_fuelified())
		{
			$output = $this->cache->get($cache_id, $cache_group);
		}
		else
		{

			// get the output
			$output = $page->variables_render(TRUE, FALSE);
			
			// save to cache but you must not be logged in for it to save
			if ($this->fuel->config('use_page_cache') !== FALSE AND $this->fuel->config('use_page_cache') !== 'cms' AND !is_fuelified())
			{
				$this->fuel->cache->save($cache_id, $output, $cache_group, $this->fuel->config('page_cache_ttl'));
			}
		}
		
		// show 404 if output is explicitly set to FALSE
		if ($output === FALSE)
		{
			// do any redirects... will exit script if any
			$this->_redirects();
			
			// else show 404
			show_404();
		}
		
		// fuelify output
		$output = $page->fuelify($output);
		
		// render output
		$this->output->set_output($output);
		
	}
	
	/*
	* ------------------------------------------------------
	* Looks for any redirects to perform. 
	* Used before showing
	* ------------------------------------------------------
	*/
	function _redirects()
	{
		include(APPPATH.'config/redirects.php');
		
		$uri = implode('/', $this->uri->segments);

		if (!empty($redirect))
		{
			
			foreach ($redirect as $key => $val)
			{
				// Convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// Does the RegEx match?
				if (preg_match('#^'.$key.'$#', $uri))
				{
					// Do we have a back-reference?
					if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
					{
						$val = preg_replace('#^'.$key.'$#', $val, $uri);
					}
					$url = site_url($val);
					redirect($url, 301);
				}
			}
		}
	}

}