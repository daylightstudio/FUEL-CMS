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
 * @link		http://www.getfuelcms.com/user_guide/modules/fuel/fuel_cache
 */

// --------------------------------------------------------------------

class Fuel_cache extends Fuel_base_library {
	
	public $ignore = '#^(\..+)|(index\.html)#'; // files to exclude from clearing like .gitignore and .htaccess
	
	protected $_cache; // the Cache object used for saving, retrieving and deleting cached files
	protected $_types = array(
								'compiled',
								'pages',
								'assets',
							); // method names used when doing clear_all()
	
	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->load->library('cache');
		$this->_cache = & $this->CI->cache;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 *
	 * @access	public
	 * @param	array	Array of initalization parameters  (optional)
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a cache ID based on the page location
	 *
	 * If no $location value is provided, then the ID will be based on the current URI segments
	 * 
	 * @access	public
	 * @param	string	Location used in creating the ID (optional)
	 * @return	string
	 */
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
	
	// --------------------------------------------------------------------

	/**
	 * Saves an item to the cache
	 * 
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID
	 * @param	mixed	Data to save to the cache  (optional)
	 * @param	int		Time to live in seconds for cache  (optional)
	 * @return	void
	 */
	function save($cache_id, $data, $group = NULL, $ttl = NULL)
	{
		$this->_cache->save($cache_id, $data, $group, $ttl);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get and return an item from the cache
	 * 
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID (optional)
	 * @param	boolean	Skip checking if it is in the cache or not (optional)
	 * @return	object	The object or NULL if not available
	 */
	function get($cache_id, $cache_group = NULL, $skip_checking = FALSE)
	{
		return $this->_cache->get($cache_id, $cache_group, $skip_checking);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Checks if the file is cached based on the cache_id passed
	 * 
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID (optional)
	 * @return	boolean
	 */
	function is_cached($cache_id, $group = NULL)
	{
		return $this->_cache->is_cached($cache_id, $group);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears the various types of caches
	 * 
	 * Value passed can be either a string or an array. 
	 * If a string, the value must "compiled", "pages" or "assets".
	 * If an array, the array must contain one or more of the values (e.g. array("compiled", "pages", "assets"))
	 * If no parameters are passed, then all caches are cleared
	 * 
	 * @access	public
	 * @param	mixed	Value can be either a string of one value or an array of multiple values. Valid values are compiled, pages and assets. (optional)
	 * @return	void
	 */
	function clear($what = NULL)
	{
		if (!empty($what) AND is_array($what))
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
		else if (!empty($what) AND is_string($what))
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
	
	// --------------------------------------------------------------------

	/**
	 * Clears the compiled templating files
	 * 
	 * @access	public
	 * @return	void
	 */
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
	
	// --------------------------------------------------------------------

	/**
	 * Clears the pages cache
	 * 
	 * @access	public
	 * @return	void
	 */
	function clear_pages()
	{
		$cache_group = $this->fuel->config('page_cache_group');
		$this->_cache->remove_group($cache_group);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clear a single page from the cache
	 * 
	 * @access	public
	 * @param	string	Page location
	 * @return	void
	 */
	function clear_page($location)
	{
		$cache_group = $this->fuel->config('page_cache_group');
		$cache_id = $this->create_id($location);
		$this->_cache->remove($cache_id, $cache_group);
	}
	

	// --------------------------------------------------------------------

	/**
	 * Clears the assets cache
	 * 
	 * Will look in module asset folders if the fuel/{module}/assets/cache/ directory exist for that module
	 *
	 * @access	public
	 * @return	void
	 */
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

	// --------------------------------------------------------------------

	/**
	 * Clears a single cache file
	 * 
	 * @access	public
	 * @param	string	Cache ID
	 * @return	void
	 */
	function clear_file($cache_id)
	{
		$this->_cache->remove($cache_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears a group of cached files
	 * 
	 * @access	public
	 * @param	string	Cache group ID
	 * @return	void
	 */
	function clear_group($group)
	{
		$this->_cache->remove_group($group);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears all cache types
	 * 
	 * Will remove page, compiled, and cached asset files
	 * 
	 * @access	public
	 * @return	void
	 */
	function clear_all()
	{
		$this->clear_pages();
		$this->clear_compiled();
		$this->clear_assets();
		$this->_delete_files($this->CI->config->item('cache_path'));
	}
	
	// --------------------------------------------------------------------

	/**
	 * Deletes cached files from specified path
	 *
	 * @access	protected
	 * @param	string	path to file
	 * @return	void
	 */
	protected function _delete_files($path)
	{
		@delete_files($path, FALSE, $this->ignore);
	}
}

/* End of file Fuel_cache.php */
/* Location: ./modules/fuel/libraries/Fuel_cache.php */