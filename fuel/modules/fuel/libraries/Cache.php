<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * FUEL Cache Class
 *
 * A generic file based caching class originally <a href="http://codeigniter.com/forums/viewthread/57117/">found on the CI Forums</a>.
 * This class is the basis for the more FUEL specific <a href="[user_guide_url]modules/fuel/fuel_cache">Fuel_cache class</a>.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @link		http://docs.getfuelcms.com/libraries/cache
 */

class Cache
{
	
	public $cache_postfix = '.cache'; //Prefix to all cache filenames
	public $expiry_postfix = '.exp'; //Expiry file prefix
	public $group_postfix = '.group'; //Group directory prefix
	public $default_ttl = 3600; //Default time to live = 3600 seconds (One hour).
	public $cache_path = ''; // The cache path. If no path is provided it will use the cache path value found in the main CI config file.
	
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
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		$this->set_params($params);
		
		// the cache path
		if (empty($this->cache_path))
		{
			$CI =& get_instance();
			$this->cache_path = $CI->config->item('cache_path');
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
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */
	public function set_params($params)
	{
		if (!is_array($params) OR empty($params)) return;

		foreach ($params as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}
	
	/**
	 * 	Check if a item has a (valid) cache
	 * 
	 * 	@param	Cache ID
	 * 	@param	Cache group ID (optional)
	 * 	@return Boolean indicating if cache available
	 */
	public function is_cached($cache_id, $cache_group = NULL)
	{
		
		if ($this->_get_expiry($cache_id, $cache_group) > time()) return TRUE;
		
		$this->remove($cache_id, $cache_group);
		
		return FALSE;
		
	}
	
	/**
	 * 	Save an item to the cache
	 * 
	 * 	@param	Cache ID
	 * 	@param	Data object
	 * 	@param	Cache group ID (optional)
	 * 	@param	Time to live for this item (optional)
	 * 	@return void
	 */
	public function save($cache_id, $data, $cache_group = NULL, $ttl = NULL)
	{
		
		if ($cache_group !== NULL)
		{
		
			$group_dir = $this->_group_dir($cache_group);
			
			if (!file_exists($group_dir)) mkdir($group_dir);
			
		}

		$file = $this->_file($cache_id, $cache_group);
		$cache_file = $file.$this->cache_postfix;
		$expiry_file = $file.$this->expiry_postfix;
		
		if ($ttl === NULL) $ttl = $this->default_ttl;

		//
		//	Ok, so setting ttl = 0 is not quite forever, but 1000 years
		//	Is your PHP code going to be running for 1000 years? If so dont use this library (or just regenerate the cache then)!
		//
		if ($ttl == 0) $ttl = 31536000000; //1000 years in seconds
				
		$expire_time = time() + $ttl;		
				
	    $f1 = fopen($expiry_file, 'w');
		$f2 = fopen($cache_file, 'w');
		
	    flock($f1, LOCK_EX);
		flock($f2, LOCK_EX);
		
		fwrite($f1, $expire_time);
		fwrite($f2, serialize($data));
		
		flock($f1, LOCK_UN);
		flock($f2, LOCK_UN);
		
		fclose($f1);
		fclose($f2);

		// Added by Daylight Studio 
		@chmod($f1, FILE_WRITE_MODE);
		@chmod($f1, FILE_WRITE_MODE);
	}
	
	/**
	 * 	Get and return an item from the cache
	 * 
	 * 	@param	Cache ID
	 * 	@param	Cache group ID (optional)
	 * 	@param	Should I check the expiry time? (optional)
	 * 	@return The object or NULL if not available
	 */
	public function get($cache_id, $cache_group = NULL, $skip_checking = FALSE)
	{
		
		if (!$skip_checking && !$this->is_cached($cache_id, $cache_group)) return NULL;

		$cache_file = $this->_file($cache_id, $cache_group).$this->cache_postfix;
		
		if (!is_file($cache_file)) return NULL;

		return unserialize(file_get_contents($cache_file));
		
	}
	
	/**
	 * 	Remove an item from the cache
	 * 
	 * 	@param	Cache ID
	 * 	@param 	Cache group ID (optional)
	 * 	@return void
	 */
	public function remove($cache_id, $cache_group = NULL)
	{
		
		$file = $this->_file($cache_id, $cache_group);
		$cache_file = $file.$this->cache_postfix;
		$expiry_file = $file.$this->expiry_postfix;
		
		@unlink($cache_file);
		@unlink($expiry_file);
		
	}
	
	/**
	 * 	Remove an entire group
	 * 
	 * 	@param	Cache group ID
	 */
	public function remove_group($cache_group)
	{
		
		$group_dir = $this->_group_dir($cache_group);

		//
		//	Empty the directory
		//
		if (!file_exists($group_dir) || !$dh = @opendir($group_dir)) return;
		
		while (($obj = readdir($dh))) {
		
		    if($obj=='.' || $obj=='..') continue;
	    	@unlink($group_dir.'/'.$obj);
		
		}
		
		closedir($dh);
		
		//	Delete the dir for tidyness
		@rmdir($group_dir);
		
	}
	
	/**
	 * 	Remove an array of cached items
	 * 
	 * 	@param	Array of cache IDs
	 * 	@param	Cache group ID
	 */
	public function remove_ids($cache_ids, $cache_group = NULL)
	{

		if (!is_array($cache_ids)) $cache_ids = array($cache_ids);

		//	Hash all IDs
		$hashes = array();
		
		foreach($cache_ids as $cache_id)
		{
		
			$hashes[] = md5($cache_id);
			
		}

		$group_dir = $this->_group_dir($cache_group);
		
		//	Delete matching files
		if(!$dh = @opendir($group_dir)) return;
		
		$filecount = 0;
		$delcount = 0;
		
		while (($obj = readdir($dh))) {
		
		    if($obj=='.' || $obj=='..') continue;
		
			$parts = explode(".", $obj);
			$hash = $parts[0];
		
	    	if (in_array($hash, $hashes)) 
			{
				
				@unlink($group_dir.'/'.$obj);
				$delcount++;
				
			}
				
			$filecount ++;
		
		}
		
		closedir($dh);
		
		//
		//	Delete the dir if empty
		//
		if ($filecount == $delcount) @rmdir($group_dir);
		
	}
	
	//	Protected methods
	
	// --------------------------------------------------------------------
	
	/**
	 * 	Returns the cache expiration time of a file
	 * 
	 * 	@param	Cache ID
	 * 	@param 	Cache group ID (optional)
	 * 	@return void
	 */
	protected function _get_expiry($cache_id, $cache_group = NULL)
	{
		$file = $this->_file($cache_id, $cache_group).$this->expiry_postfix;
		if (!is_file($file)) return 0;
		return intval(file_get_contents($file));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 	Retrieves a file path to an cached item
	 * 
	 * 	@param	Cache ID
	 * 	@param 	Cache group ID (optional)
	 * 	@return void
	 */
	protected function _file($cache_id, $cache_group = NULL)
	{
		return $this->_group_dir($cache_group).'/'.md5($cache_id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 	Returns the directory to a cached group
	 * 
	 * 	@param 	Cache group ID (optional)
	 * 	@return string
	 */
	protected function _group_dir($cache_group)
	{
		$CI =& get_instance();
		$dir = ($cache_group != NULL) ? md5($cache_group).$this->group_postfix : '';
		return $this->cache_path.$dir;
		
	}
	
}


/* End of file Cache.php */
/* Location: ./modules/fuel/libraries/Cache.php */