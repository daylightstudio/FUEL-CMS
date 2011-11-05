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
 * FUEL cache object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_console
 */

// --------------------------------------------------------------------

class Fuel_cache extends Fuel_base_library {
	
	public $ignore = '#^(\..+)|(index\.html)#'; // files to exclude from clearing like .gitignore and .htaccess
	
	protected $_cache;
	protected $_types = array(
								'compiled',
								'pages',
								'assets',
							);
	
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->load->library('cache');
		$this->_cache = & $this->CI->cache;
	}
	
	function initialize($params)
	{
		parent::initialize($params);
		
	}
	
	/**
	 * 	Get and return an item from the cache
	 * 
	 * 	@param	Cache Id
	 * 	@param	Cache group Id
	 * 	@param	Should I check the expiry time?
	 * 	@return The object or NULL if not available
	 */
	function get($cache_id, $cache_group = NULL, $skip_checking = FALSE)
	{
		$this->_cache->get($cache_id, $cache_group, $skip_checking);
	}
	
	function save($cache_id, $data, $group = NULL, $ttl = NULL)
	{
		return $this->_cache->save($cache_id, $data, $group, $ttl);
	}
	
	function is_cached($cache_id, $group = NULL)
	{
		return $this->_cache->is_cached($cache_id, $group);
	}
	
	function clear($what = NULL)
	{
		if (is_array($what))
		{
			foreach($what as $w)
			{
				if (in_array($w, $this->_types))
				{
					$method = 'clear_'.$w;
					if (method_exists($this, $method))
					{
						$this->$method();
					}
				}
			}
		}
		else if (is_string($what))
		{
			if (in_array($what, $this->_types))
			{
				$this->$what();
			}
		}
		else
		{
			$this->clear_all();
		}
	}
	
	function clear_compiled()
	{
		
		// also delete DWOO compiled files
		$this->CI->load->helper('file');
		$dwoo_path = $this->CI->config->item('cache_path').'dwoo/compiled/';
		
		if (is_dir($dwoo_path) AND is_writable($dwoo_path))
		{
			@delete_files($dwoo_path, FALSE, $this->ignore);
		}
		
		// remove asset cache files if exist
		$modules = $this->fuel->config('modules_allowed');
		$modules[] = FUEL_FOLDER; // fuel
		$modules[] = ''; // main application assets
		foreach($modules as $module)
		{
			// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
			$cache_folder = assets_server_path($this->CI->asset->assets_cache_folder, 'cache', $module);
			if (is_dir($cache_folder) AND is_writable($cache_folder))
			{
				$this->_delete_files($cache_folder);
			}
		}
	}
	
	function clear_pages()
	{
		$cache_group = $this->fuel->config('page_cache_group');
		$this->_cache->remove_group($cache_group);
	}
	
	function clear_assets()
	{
		// remove asset cache files if exist
		$modules = $this->fuel->config('modules_allowed');
		$modules[] = FUEL_FOLDER; // fuel
		$modules[] = ''; // main application assets
		foreach($modules as $module)
		{
			// check if there is a css module assets file and load it so it will be ready when the page is ajaxed in
			$cache_folder = assets_server_path($this->CI->asset->assets_cache_folder, 'cache', $module);
			if (is_dir($cache_folder) AND is_writable($cache_folder))
			{
				$this->_delete_files($cache_folder);
			}
		}
		
	}

	function clear_file($cache_id)
	{
		$this->_cache->remove($cache_id);
	}
	
	function clear_group($group)
	{
		$this->_cache->remove_group($group);
	}
	
	function clear_all()
	{
		$this->clear_pages();
		$this->clear_compiled();
		$this->clear_assets();
		$this->_delete_files($this->CI->config->item('cache_path'));
	}
	
	function create_id($location = NULL)
	{
		if (empty($location))
		{
			$segs = $this->CI->uri->segment_array();

			if (empty($segs)) 
			{
				return 'home';
			}
			return implode('.', $segs);
		}
		return str_replace('/', '.', $location);
	}
	
	protected function _delete_files($path)
	{
		@delete_files($path, FALSE, $this->ignore);
	}
}

/* End of file Fuel_cache.php */
/* Location: ./modules/fuel/libraries/Fuel_cache.php */