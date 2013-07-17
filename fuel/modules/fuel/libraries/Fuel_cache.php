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
 * FUEL cache object
 *
 * This class is used for managing the different kinds of cached files used
 * with FUEL including pages, compiled templates and asset files.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_cache
 */

// --------------------------------------------------------------------

class Fuel_cache extends Fuel_base_library {
	
	public $ignore = '#^(\..+)|(index\.html)#'; // Regular expression of files to exclude from clearing like .gitignore and .htaccess
	public $cache_path = ''; // The cache path. If no path is provided it will use the cache path value found in the main CI config file.
	
	protected $_cache; // the Cache object used for saving, retrieving and deleting cached files
	protected $_types = array(
								'compiled',
								'pages',
								'assets',
							); // method names used when doing clear()
	
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
		$this->CI->load->library('cache');
		$this->_cache = & $this->CI->cache;
		$this->initialize($params);
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
	public function initialize($params = array())
	{
		parent::initialize($params);
		// set the cache path to the configs cache path if left empty
		if (empty($this->cache_path))
		{
			$this->set_cache_path($this->CI->config->item('cache_path'));
		}
		
		// set the compile templates path
		if (empty($this->compiled_path))
		{
			include(APPPATH.'config/parser.php');
			$this->set_compiled_path($config['parser_compile_dir']);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the cache path
	 *
	 * @access	public
	 * @param	string	The path to the cache folder
	 * @return	void
	 */	
	public function set_cache_path($path)
	{
		$this->cache_path = $path;
		$this->_cache->set_cache_path($this->cache_path);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the compiled templates path
	 *
	 * @access	public
	 * @param	string	The path to the compiled templates folder
	 * @return	void
	 */	
	public function set_compiled_path($path)
	{
		$this->compiled_path = $path;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a cache ID based on the page location
	 *
	 * If no $location value is provided, then the ID will be based on the current URI segments
	 * 
	 * <code>
	 * $cache_id = $this->fuel->cache->create_id(); // create a cache id... this will be based on the current URI location if no parameters are passed
	 * </code>
	 *
	 * @access	public
	 * @param	string	Location used in creating the ID (optional)
	 * @return	string
	 */
	public function create_id($location = NULL)
	{
		$lang = ($this->fuel->language->has_multiple()) ? $this->fuel->language->detect() : $this->fuel->language->default_option();
		if (empty($location))
		{
			$segs = $this->CI->uri->segment_array();

			if (empty($segs)) 
			{
				return 'home.'.$lang;
			}
			return implode('.', $segs).'.'.$lang;
		}
		$id = $location.'.'.$lang;
		return str_replace('/', '.', $id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Saves an item to the cache
	 * 
	 * <code>
	 * $cache_id = $this->fuel->cache->create_id();
	 * $data = 'These are not the droids you are looking for.';
	 * $file = $this->fuel->cache->save($cache_id, $data, NULL, 3600); // sets the cached item with a TTL of one hour
	 * </code>
	 *
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID
	 * @param	mixed	Data to save to the cache  (optional)
	 * @param	int	Time to live in seconds for cache  (optional)
	 * @return	void
	 */
	public function save($cache_id, $data, $group = NULL, $ttl = NULL)
	{
		$this->_cache->save($cache_id, $data, $group, $ttl);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get and return an item from the cache
	 * 
	 * <code>
	 * $cache_id = $this->fuel->cache->create_id();
	 * $file = $this->fuel->cache->get($cache_id, 'pages', FALSE);
	 * </code>
	 *
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID (optional)
	 * @param	boolean	Skip checking if it is in the cache or not (optional)
	 * @return	string	The contents of the cached file or NULL if it doesn't exist
	 */
	public function get($cache_id, $cache_group = NULL, $skip_checking = FALSE)
	{
		return $this->_cache->get($cache_id, $cache_group, $skip_checking);
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if the file is cached based on the cache_id passed
	 * 
	 * <code>
	 * $cache_id = $this->fuel->cache->create_id();
	 * if ($this->fuel->cache->is_cached($cache_id)){
	 *  	echo 'cached';
	 *  } else {
	 *  	echo 'not cached';
	 *  } 
	 * </code>
	 *
	 * @access	public
	 * @param	string	Cache ID
	 * @param	string	Cache group ID (optional)
	 * @return	boolean
	 */
	public function is_cached($cache_id, $group = NULL)
	{
		return $this->_cache->is_cached($cache_id, $group);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears the various types of caches
	 * 
	 * Value passed can be either a string or an array. 
	 * If a string, the value must be "compiled", "pages" or "assets".
	 * If an array, the array must contain one or more of the values (e.g. array("compiled", "pages", "assets"))
	 * If no parameters are passed, then all caches are cleared
	 * 
	 * <code>
	 * $this->fuel->cache->clear(array('compiled', 'pages')); // as an array
	 * $this->fuel->cache->clear('assets'); // as string
	 * </code>
	 *
	 * @access	public
	 * @param	mixed	Value can be either a string of one value or an array of multiple values. Valid values are compiled, pages and assets. (optional)
	 * @return	void
	 */
	public function clear($what = NULL)
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
	 * <code>
	 * $this->fuel->cache->clear(array('compiled', 'pages')); // as an array
	 * $this->fuel->cache->clear('assets'); // as string
	 * </code>
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_compiled()
	{
		
		// also delete DWOO compiled files
		$this->CI->load->helper('file');

		include(APPPATH.'config/parser.php');

		
		// remove all compiled files
		$dwoo_compile_path =& $config['parser_compile_dir'];
		if (is_dir($dwoo_compile_path) AND is_writable($dwoo_compile_path))
		{
			$this->_delete_files($dwoo_compile_path);
		}

		// remove all cache files
		$dwoo_cache_path =& $config['parser_cache_dir'];
		if (is_dir($dwoo_cache_path) AND is_writable($dwoo_cache_path))
		{
			$compiled_folder = trim(str_replace($dwoo_cache_path, '', $dwoo_compile_path), '/');
			$ignore = array($compiled_folder, 'index.html');
			delete_files($dwoo_cache_path, TRUE, $ignore);
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
	 * <code>
	 * $this->fuel->cache->clear_pages();
	 * </code>
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_pages()
	{
		$cache_group = $this->fuel->config('page_cache_group');
		$this->_cache->remove_group($cache_group);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clear a single page from the cache
	 * 
	 * <code>
	 * $this->fuel->cache->clear_page('about/history');
	 * </code>
	 *
	 * @access	public
	 * @param	string	Page location
	 * @return	void
	 */
	public function clear_page($location)
	{
		$cache_group = $this->fuel->config('page_cache_group');
		$cache_id = $this->create_id($location);
		$this->_cache->remove($cache_id, $cache_group);
	}
	

	// --------------------------------------------------------------------

	/**
	 * Clears the assets cache
	 * 
	 * Will look in module asset folders if the <dfn>fuel/{module}/assets/cache/</dfn> directory exist for that module
	 * 
	 * <code>
	 * $this->fuel->cache->clear_assets();
	 * </code>
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_assets()
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
	 * <code>
	 * $cache_id = $this->fuel->cache->create_id();
	 * $this->fuel->cache->clear_file($cache_id);
	 * </code>
	 *
	 * @access	public
	 * @param	string	Cache ID
	 * @return	void
	 */
	public function clear_file($cache_id)
	{
		$this->_cache->remove($cache_id);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears a group of cached files
	 * 
	 * <code>
	 * $group = 'pages';
	 * $this->fuel->cache->clear_group($group);
	 * </code>
	 * 
	 * @access	public
	 * @param	string	Cache group ID
	 * @return	void
	 */
	public function clear_group($group)
	{
		$this->_cache->remove_group($group);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears the cache folder for a particular module
	 * 
	 * <code>
	 * $module = 'my_module';
	 * $this->fuel->cache->clear_module($module);
	 * </code>
	 * 
	 * @access	public
	 * @param	string	Module name
	 * @return	void
	 */
	public function clear_module($module)
	{
		$module_path = MODULES_PATH.$module.'/cache';
		if (file_exists($module_path))
		{
			$this->_delete_files($module_path);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Clears all the allowed modules cache folders
	 * 
	 * <code>
	 * $this->fuel->cache->clear_all_modules();
	 * </code>
	 * 
	 * @access	public
	 * @return	void
	 */
	public function clear_all_modules()
	{
		$modules = $this->CI->fuel->config('modules_allowed');
		foreach($modules as $module)
		{
			$this->clear_module($module);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clears all cache types
	 * 
	 * Will remove page, compiled, and cached asset files
	 * 
	 * <code>
	 * $this->fuel->cache->clear_all();
	 * </code>
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_all()
	{
		$this->clear_pages();
		$this->clear_compiled();
		$this->clear_assets();
		$this->clear_all_modules();
		$this->_delete_files($this->cache_path);
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