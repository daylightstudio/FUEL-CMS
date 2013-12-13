<?php
class Page_router extends CI_Controller {

	public $segments = array();
	public $layout = '';
	public $location;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function _remap($method)
	{
		$this->location = uri_path(TRUE);
		
		// if the rerouted file can't be found, look for the non-routed file'
		if (!file_exists(APPPATH.'views/'.$this->location.EXT))
		{
			$non_routed_uri = uri_path(FALSE);
			if (file_exists(APPPATH.'views/'.$non_routed_uri.EXT))
			{
				$this->location = $non_routed_uri;
			}
			unset($non_routed_uri);
		}
		
		if (empty($this->location)) $this->location = $this->fuel->config('default_home_view');

		$config = array();
		$config['location'] = $this->location;

		if ($this->fuel->pages->mode() == 'views')
		{
			$config['render_mode'] = 'views';
			$page = $this->fuel->pages->create($config);
			
			$this->_remap_variables($page);
		}
		
		// using FUEL admin
		else
		{
			if ($this->fuel->pages->mode() != 'cms')
			{
				
				$config['render_mode'] = 'auto';
				if ($this->fuel->config('uri_view_overwrites'))
				{
					// loop through the pages array looking for wild-cards
					foreach ($this->fuel->config('uri_view_overwrites') as $val)
					{
						// convert wild-cards to RegEx
						$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $val));

						// does the RegEx match?
						if (preg_match('#^'.$val.'$#', $config['location']))
						{
							$config['render_mode'] = 'views';
						}
					}
				}
				$page = $this->fuel->pages->create($config);
				
				if ((!$page->has_cms_data() AND $config['render_mode'] == 'auto') OR $config['render_mode'] == 'views')
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
	public function _remap_cms($page)
	{
		$page_data = $page->properties();
		$this->load->helper('cookie');

		// set up cache info 
		$cache_group = $this->fuel->config('page_cache_group');
		$cache_id = $this->fuel->cache->create_id();

		$output = '';
		
		// grab from cache if exists without checking expiration time... 
		// Also.. saving from FUEL will remove cached page so you will always preview the latest saved
		if ($this->fuel->config('use_page_cache') !== FALSE AND 
			$this->fuel->config('use_page_cache') !== 'views' AND 
			$this->fuel->cache->get($cache_id, $cache_group, FALSE) AND 
			$page->is_cached() AND !is_fuelified())
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
				if ($this->fuel->config('use_page_cache') !== FALSE AND 
					$this->fuel->config('use_page_cache') !== 'views' AND 
					$page->is_cached() AND
					!is_fuelified())
				{
					$this->cache->save($cache_id, $output, $cache_group, $this->fuel->config('page_cache_ttl'));
				}
				
			}
			else
			{
				// do any redirects... will exit script if any
				redirect_404();
			}
		}

		// fuelify
		$output = $page->fuelify($output);
		
		// render output
		$this->output->set_output($output);
		
		// call the post render layout hook
		$page->layout->call_hook($page_data['layout'], 'post_render', $output);
		
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
	public function _remap_variables($page)
	{
		
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
			redirect_404();
		}
		
		// fuelify output
		$output = $page->fuelify($output);
		
		// render output
		$this->output->set_output($output);
		
	}

}