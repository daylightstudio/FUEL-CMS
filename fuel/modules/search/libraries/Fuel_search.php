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
 * FUEL search library
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 */

// --------------------------------------------------------------------

class Fuel_search extends Fuel_advanced_module {
	
	public $timeout = 20; // CURL timeout
	public $connect_timeout = 10; // CURL connection timeout
	public $title_limit = 100; // max character limit of the title of content
	public $user_agent = 'FUEL'; // the user agent used for indexing
	protected $CI; // reference to the CI super object
	protected $_errors = array(); // array to keep track of errors
	protected $_log = array(); // log of items index
	
	/**
	 * Constructor - Sets Fuel_search preferences and to any children
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		$this->CI =& get_instance();
		// $this->CI->load->module_config(SEARCH_FOLDER, 'search');
		// $this->CI->load->module_language(SEARCH_FOLDER, 'search');
		// $this->CI->load->module_model(SEARCH_FOLDER, 'search_model');

		// initialize object if any parameters
		if (!empty($params))
		{
			$this->initialize($params);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params = array())
	{
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */
	function set_params($params)
	{
		if (!is_array($params)) return;

		foreach ($params as $key => $val)
		{
			if (isset($this->$key) AND substr($key, 0, 1) != '_')
			{
				$this->$key = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Query the database
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function query($q = '', $limit, $offset, $excerpt_limit)
	{
		$results = $this->CI->search_model->find_by_keyword($q, $limit, $offset, $excerpt_limit);
		return $results;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Hook for updating an index item after a module save
	 * 
	 * Used when something is saved in admin to automatically update the index
	 *
	 * @access	public
	 * @param	object	search record
	 * @return	boolean
	 */	
	function after_save_hook($posted)
	{
		// if indexing is disabled, then we just return and don't continue'
		if (!$this->config('indexing_enabled')) return;
		
		// grab the config values for what should be indexed on save
		$index_modules = $this->config('auto_index');
		$module = $this->CI->module;
		
		// check if modules can be indexed. If an array is provided, then we only index those in the array
		if ($index_modules === TRUE OR (is_array($index_modules) AND isset($index_modules[$module])))
		{
			$location = $this->CI->_preview_path($posted);
			$this->index($location, $module);
		}
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Hook for removing an index item after a module save
	 * 
	 * Used when something is deleted in admin to automatically remove from the index
	 *
	 * @access	public
	 * @param	object	search record
	 * @return	boolean
	 */	
	function after_delete_hook($posted)
	{
		if (!empty($data['location']))
		{
			$location = $this->CI->_preview_path($posted);
			$this->remove($posted['location']);
		}
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Indexes the data for the search
	 *
	 * @access	public
	 * @return	mixed
	 */	
	function index($pages = array(), $scope = 'pages', $clear_all = FALSE)
	{
		// check if indexing is enabled first
		if ($this->config('indexing_enabled'))
		{
		
			// clear out the entire index
			if ($clear_all)
			{
				$this->clear_all();
			}
			
			if (empty($pages))
			{
				
				// if no pages provided, we load them all
				$index_method = $this->config('index_method');

				// figure out where to get the pages
				if ($index_method == 'sitemap')
				{
					$pages = $this->sitemap_pages();
				}
				else if ($index_method == 'crawl')
				{
				
					// if we crawl the page, then we automatically will save it's contents to save on CURL requests'
					return $this->crawl_pages();
				}
				
				// default will check if there is a sitemap, and if not, will crawl
				else
				{
					$pages = $this->sitemap_pages();
					if (empty($pages))
					{
						return $this->crawl_pages();
					}
				}
			}
			
			$pages = (array) $pages;
			
			// render the pages then look for delimiters within to get the content
			foreach($pages as $location)
			{
				// find indexable content in the html and create the index in the database
				$this->index_page($location);
			}
		}
		
		// if indexing isn't enabled, we'll add it to the errors list
		else
		{
			$this->_add_error(lang('search_indexing_disabled'));
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks to see if the page is indexable
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */	
	function is_indexable($location)
	{
		// sitemap.xml and robots.txt locations are automatically ignored
		$auto_ignore = array(
			'sitemap.xml',
			'robots.txt',
		);
		
		if (in_array($location, $auto_ignore) OR !$this->is_local_url($location))
		{
			return FALSE;
		}
		
		// get pages to exclude
		$exclude = (array)$this->config('exclude');
		
		if (!empty($exclude))
		{
			// loop through the exclude array looking for wild-cards
			foreach ($exclude as $val)
			{
				// convert wild-cards to RegEx
				$val = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $val));
	
				// does the RegEx match? If so, we it's not indexable'
				if (preg_match('#^'.$val.'$#', $p))
				{
					return FALSE;
				}
			}
		}
		
		// now check against the robots.txt
		if (!$this->check_robots_txt($location))
		{
			return FALSE;
		}
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Crawls the site for locations to be indexed
	 *
	 * @access	public
	 * @return	array
	 */	
	function crawl_pages($location = 'home', $index_content = TRUE)
	{
		static $crawled = array();
		
		// start at the homepage if no root page is specified
		if (empty($location) OR $location == 'home')
		{
			$location = site_url();
		}
		
		$html = '';
		
		// grab the HTML of the page to get all the links
		if ($this->is_local_url($location))
		{
			$html = $this->scrape_page($location);
		}
		
		// index the content at the same time to save on CURL bandwidth
		if (!empty($html))
		{
			if ($index_content)
			{
				$indexed = $this->index_page($location, $html);
			}
			
			// the page must be properly indexed above to continue on
			if ($indexed)
			{
				// grab all the page links
				preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $html, $matches);

				if (!empty($matches[1]))
				{
					foreach($matches[1] as $url)
					{
						// check if the url is local AND whether it has already been indexed
						if (!isset($crawled[$url]))
						{
							// add the url in the indexed array
							$crawled[$url] = $url;

							// now recursively crawl
							$this->crawl_pages($url);
						}
					}
				}
			}
		}
		return array_values($crawled);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parses the sitemap.xml file of the site and returns an array of pages to index
	 *
	 * @access	public
	 * @return	array
	 */	
	function index_page($location, $html = NULL)
	{
		// check if the page is indexable before continuing
		if (!$this->is_indexable($location))
		{
			return FALSE;
		}
		
		// get the page HTML need to use CURL instead of render_page because one show_404 error will halt the indexing
		if (empty($html))
		{
			$html = $this->scrape_page($location);
			if (!$html)
			{
				return FALSE;
			}
		}
		
		// get the proper scope for the page
		$scope = $this->get_location_scope($location);
		
		// get the xpath object so we can query the content of the page
		$xpath = $this->page_xpath($html);
		
		// get the content
		$content = $this->find_indexable_content($xpath);
		
		// get the title
		$title = $this->find_page_title($xpath);

		// create search record
		if (!empty($content) AND !empty($title))
		{
			$rec = array(
				'location' => $location,
				'scope' => $scope,
				'title' => $title,
				'content' => $content,
				);

			if (!$this->create($rec))
			{
				$this->_add_error(lang('search_error_saving'));
				return FALSE;
			}
		}
		return TRUE;
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Parses the sitemap.xml file of the site and returns an array of pages to index
	 *
	 * @access	public
	 * @return	array
	 */	
	function sitemap_pages()
	{
		$locations = array();

		$sitemap_xml = $this->scrape_page('sitemap.xml');
		if (!$sitemap_xml)
		{
			return FALSE;
		}
		
		$dom = new DOMDocument; 
		$dom->preserveWhiteSpace = FALSE;
		
		$sitemap_xml = preg_replace('#<\?xml.+\?>#U', '', $sitemap_xml);
		$dom->loadXML($sitemap_xml); 
		$locs = $dom->getElementsByTagName('loc');
		
		$site_url = site_url();
		foreach($locs as $node)
		{
			$loc = (string) $node->nodeValue;
			$locations[] = $loc;
		}
		return $locations;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Parses the sitemap.xml file of the site and returns an array of pages to index
	 *
	 * @access	public
	 * @return	array
	 */	
	function check_robots_txt($location)
	{
		 // static so we only do it once per execution
		static $robot_txt;
		static $disallow; 
		
		// if no robots.txt then we just return TRUE
		if ($robot_txt === FALSE) return TRUE;
		
		if (empty($robot_txt))
		{
			$robot_txt = site_url('robots.txt');
			
			// we will scrape the page instead of file_get_contents because the page may be dynamically generated by FUEL
			$robot_txt = $this->scrape_page($robot_txt);
			if (!$robot_txt)
			{
				// again... no robots.txt, return TRUE
				return TRUE;
			}
		}
		
		if (empty($disallow))
		{
			$disallow = array();
			$lines = explode("\n", $robot_txt);
			$check = FALSE;
			foreach($lines as $l)
			{
				// # symbol is for comments in regex in case you were wondering
				if (preg_match('/^user-agent:([^#]+)/i', $l, $matches1))
				{
					$agent = trim($matches1[1]);
					$check = ($agent == '*' OR $agent == $this->user_agent) ? TRUE : FALSE;
				}

				// check disallow
				if ($check AND preg_match('/disallow:([^#]+)/i', $l, $matches2))
				{
					$dis = trim($matches2[1]);
					if ($dis != '')
					{
						$disallow[] = $dis;
					}
				}
			}
		}
		
		// loop through the disallow and if it matches the location value, then we return FALSE
		foreach($disallow as $d)
		{
			$d = ltrim($d, '/'); // remove begining slash
			$d = str_replace('*', '__', $d); // escape wildcards with a character that won't be escaped by preg_quote'
			$d = preg_quote($d); // escape special regex characters (like periods)
			$d = str_replace('__', '.*', $d); // convert "__" (transformed from wildcard) to regex .* (0 or more of anything)
			if ($d == '/')
			{
				$d = '.*';
			}
			if (preg_match('#'.$d.'#', $location))
			{
				return FALSE;
			}
		}
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CURLs the page and gets the content
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function scrape_page($url, $just_header = FALSE)
	{
		if (!is_http_path($url))
		{
			$url = site_url($url);
		}

		$curl = curl_init();
		$opts = array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => TRUE,
						CURLOPT_TIMEOUT => $this->timeout,
						CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
						CURLOPT_BINARYTRANSFER => TRUE,
						CURLOPT_USERAGENT => $this->user_agent,
					);
		curl_setopt_array($curl, $opts);
		
		if ($just_header)
		{
			curl_setopt($curl, CURLOPT_HEADER, TRUE);
			curl_setopt($curl, CURLOPT_NOBODY, TRUE);
		}
		else
		{
			curl_setopt($curl, CURLOPT_HEADER, FALSE);
			curl_setopt($curl, CURLOPT_NOBODY, FALSE);
		}
		
		$output = curl_exec($curl);
		
		// any errors we capture
		$error = curl_error($curl);
		if (!empty($error))
		{
			$this->_add_error($error);
		}
		
		// if the page doesn't return a 200 status, we don't scrape
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($http_code != 200)
		{
			// remove from index if exists
			// $location = $this->get_location($url);
			// $rec = $this->CI->search_model->find_by_location($location);
			// if (isset($rec->id))
			// {
			// 	$this->remove($location);
			// }
			$msg = lang('search_log_index_page_error', 'HTTP Code '.$http_code.' for '.$url);
			$this->log($msg);
			$this->_add_error($msg);
			return FALSE;
		}
		curl_close($curl);
		
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the DomXpath 
	 *
	 * @access	public
	 * @param	string	page content to search
	 * @return	array
	 */	
	function page_xpath($content, $type = 'html')
	{
		// turn off errors for loading HTML into dom
		$old_setting = libxml_use_internal_errors(TRUE); 
		libxml_clear_errors();
		
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = FALSE;
		
		if ($type == 'html')
		{
			$loaded = @$dom->loadHTML($content);
		}
		else
		{
			$loaded = $dom->loadXML($content);
		}
		if (!$loaded)
		{
			$this->_add_error(lang('search_error_parsing_content'));
		}
		
		// change errors back to original settings
		libxml_clear_errors();
		libxml_use_internal_errors($old_setting); 
		
		// create xpath object to do some querying
		$xpath = new DOMXPath($dom);
		return $xpath;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Searches for index related content within html content
	 *
	 * @access	public
	 * @param	object	DOMXpath
	 * @return	array
	 */	
	function find_indexable_content($xpath)
	{
		$content = '';
		
		// get delimiters
		$delimiters = $this->config('delimiters');
		foreach($delimiters as $d)
		{
			// get the xpath equation for querying if it is not already in xpath format
			$query = $d;
			if (preg_match('#^<.+>#', $query, $matches))
			{
				$query = $this->get_xpath_from_node($query);
			}
			
			$results = $xpath->query($query);
			
			if (!empty($results))
			{
				$content_arr = array();
				foreach($results as $r)
				{
					$val = (string)$r->nodeValue;
					// using DOM has added benefit of stripping tags out!
					if (!empty($val))
					{
						$content_arr[] = $val;
					}
				}
				$content = implode('|', $content_arr);
			}
		}
		
		return $content;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Searches for title
	 *
	 * @access	public
	 * @param	object	DOMXpath
	 * @return	array
	 */	
	function find_page_title($xpath)
	{
		// get the h1 value for the title
		$title_results = $xpath->query('//h1');
		if ($title_results->item(0))
		{
			$title = $title_results->item(0)->nodeValue;
			return $title;
		}
		
		// if no h1, then we get the page title
		$title_results = $xpath->query('//title');
		if ($title_results->item(0))
		{
			$title = $title_results->item(0)->nodeValue;
			return $title;
		}
		
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the xpath syntax based on the node string (e.g. <div id="main">)
	 *
	 * @access	public
	 * @param	string	node string
	 * @return	string
	 */	
	function get_xpath_from_node($node)
	{
		$node_trimmed = trim($node, '<>');
		$node_pieces = preg_split('#\s#', $node_trimmed);
		$xpath_str = '//'.$node_pieces[0];
		if (count($node_pieces) > 1)
		{
			for($i = 1; $i < count($node_pieces); $i++)
			{
				$xpath_str .= '[@'.$node_pieces[$i].']';
			}
		}
		return $xpath_str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns scope based on the url by looking at the preview paths
	 *
	 * @access	public
	 * @param	string	url
	 * @return	string
	 */	
	function get_location_scope($location)
	{
		static $preview_paths;
		
		if (is_null($preview_paths))
		{
			// get all the preview paths
			$modules = $this->CI->fuel->modules->get();
			foreach($modules as $mod => $module)
			{
				//$info = $this->CI->fuel_modules->info($mod);
				$info = $module->info($mod);
				if (!empty($info['preview_path']))
				{
					$preview_paths[$mod] = $info['preview_path'];
				}
			}
		}
		
		if (is_array($preview_paths))
		{
			foreach($preview_paths as $mod => $path)
			{
				// ignore the pages preview path which will be assigned by default if no matches
				if ($path != '{location}')
				{
					$location = $this->get_location($location);
					$path_regex = preg_replace('#(.+/)\{.+\}(.*)#', '$1.+$2', $path);
					if (preg_match('#'.$path_regex.'#', $location))
					{
						return $mod;
					}
				}
			}
		}
		return 'pages';
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Creates a record in the search table. Will overwrite if the same location/model exist
	 *
	 * @access	public
	 * @param	mixed	can be a string or an array. If an array, it must contain the other values
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */	
	function create($location, $content = NULL, $scope = 'page')
	{
		if (!is_array($location))
		{
			$values['location'] = $location;
			$values['scope'] = $scope;
			$values['title'] = $title;
			$values['content'] = $content;
		}
		else
		{
			$values = $location;
		}
		$values['location'] = $this->get_location($values['location']);
		$values['title'] = $this->format_title($values['title']);
		$values['content'] = $this->clean($values['content']);

		if (empty($values['location']))
		{
			$values['location'] = 'home';
		}

		// to some checks here first to make sure it is valid content
		if (!$this->is_local_url($values['location']) OR !isset($values['content']) OR !isset($values['title']))
		{
			return FALSE;
		}

		$saved = $this->CI->search_model->save($values);

		if ($saved)
		{
			$msg = lang('search_log_index_created', $values['location']);
			$this->log($msg);
			return TRUE;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Removes a record in the search table
	 *
	 * @access	public
	 * @param	string	location
	 * @param	string	scope
	 * @return	boolean
	 */	
	function remove($location)
	{
		$location = $this->get_location($location);
		
		$where['location'] = $location;
		$deleted = $this->CI->search_model->delete($where);
		
		if ($deleted)
		{
			$msg = lang('search_log_index_removed', $location);
			$this->log($msg);
			return TRUE;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clears the entire search index
	 *
	 * @access	public
	 * @return	void
	 */	
	function clear_all()
	{
		$this->CI->search_model->truncate();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Cleans content to make it more searchable. 
	 * 
	 * The find_indexable_content already cleans up content in most cases
	 *
	 * @access	public
	 * @param	string	HTML content to clean for search index
	 * @return	boolean
	 */	
	function clean($content)
	{
		$content = safe_htmlentities($content);
		$content = strip_tags($content);
		return $content;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Cleans and truncates the title if it's too long
	 * 
	 * @access	public
	 * @param	string	title
	 * @return	boolean
	 */	
	function format_title($title)
	{
		$title = character_limiter($this->clean($title), $this->title_limit);
		return $title;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Determines whether the url is local to the site or not
	 * 
	 * @access	public
	 * @param	string	title
	 * @return	boolean
	 */	
	function is_local_url($url)
	{
		if (substr($url, 0, 7) == 'mailto:' OR substr($url, 0, 1) == '#' OR substr($url, 0, 11) == 'javascript:')
		{
			return FALSE;
		}
		if (is_http_path($url))
		{
			return preg_match('#^'.preg_quote(site_url()).'#', $url);
		}
		else
		{
			return TRUE;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Return the URI part of a URL
	 * 
	 * @access	public
	 * @param	string	url
	 * @return	string
	 */	
	function get_location($url)
	{
		return str_replace(site_url(), '', $url);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds an item to the index log
	 * 
	 * Used when printing out the index log informaiton
	 *
	 * @access	public
	 * @param	object	search record
	 * @return	boolean
	 */	
	function log($rec)
	{
		$this->_log[] = $rec;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds an item to the index log
	 * 
	 * Used when printing out the index log informaiton
	 *
	 * @access	public
	 * @param	object	search record
	 * @return	boolean
	 */	
	function display_log($return = FALSE)
	{
		$str = '';
		foreach($this->_log as $l)
		{
			$str .= $l."\n";
		}
		if (!$return)
		{
			echo '<pre>';
			echo $str;
			echo '</pre>';
		}
		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a search config item
	 *
	 * @access	public
	 * @param	string	key to config itme
	 * @return	boolean
	 */	
	function config($key)
	{
		$config = $this->CI->config->item('search');
		
		if (isset($config[$key]))
		{
			return $config[$key];
		}
		else
		{
			return NULL;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns any errors that may have occurred during backing up
	 *
	 * @access	public
	 * @return	array
	 */	
	function errors($formatted = FALSE, $open = '', $close = "\n\n")
	{
		if ($formatted === FALSE)
		{
			return $this->_errors;
		}

		$error = '';
		foreach($this->_errors as $e)
		{
			$error .= $open.$e.$close;
		}
		return $error;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether there were errors in backing up or not
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function has_errors()
	{
		return count($this->_errors) > 0;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds an error to the _errors array
	 *
	 * @access	protected
	 * @param	string	error message
	 * @return	array
	 */	
	protected function _add_error($error)
	{
		$this->_errors[] = $error;
	}
	
	
}

/* End of file Fuel_base_library.php */
/* Location: ./modules/fuel/libraries/Fuel_base_library.php */