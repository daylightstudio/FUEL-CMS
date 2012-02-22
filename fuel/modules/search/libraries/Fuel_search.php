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
	public $q = ''; // search term
	
	protected $_logs = array(); // log of items indexed
	
	const LOG_ERROR = 'error';
	const LOG_REMOVED = 'removed';
	const LOG_INDEXED = 'indexed';
	
	/**
	 * Constructor - Sets Fuel_search preferences and to any children
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();

		if (empty($params))
		{
			$params['name'] = 'search';
		}
		$this->initialize($params);
		
		$this->load_model('search');
		$this->CI->load->library('curl');

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Query the database
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	int
	 * @return	array
	 */	
	function query($q = '', $limit = 100, $offset = 0, $excerpt_limit = 200)
	{
		$results = $this->CI->search_model->find_by_keyword($q, $limit, $offset, $excerpt_limit);
//		$this->CI->search_model->debug_query();
		$this->q = $q;
		return $results;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the count of the returned rows. If no parameter is passed then it will assume the last query.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function count($q = '')
	{
		if (empty($q))
		{
			$q = $this->q;
		}
		$count = $this->CI->search_model->find_by_keyword_count($q);
		return $count;
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
		$index_modules = $this->config('index_modules');
		$module = $this->CI->module;
		
		// check if modules can be indexed. If an array is provided, then we only index those in the array
		if ($index_modules === TRUE OR (is_array($index_modules) AND isset($index_modules[$module])))
		{
			$module_obj = $this->CI->fuel->modules->get($module);
			$location = $module_obj->url($posted);

			// now index the page
			//$this->index($location, $module);
			
			// use ajax to speed things up
			$output = lang('data_saved').'<script type="text/javascript">
			//<![CDATA[
				$(function(){
					$.get("'.fuel_url('tools/search/index_site').'?pages='.$location.'")
				});
			//]]>
			</script>';
			$this->CI->session->set_flashdata('success', $output);
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
		if (empty($posted['location']))
		{
			// grab the config values for what should be deleted
			$index_modules = $this->config('index_modules');
			$module = $this->CI->module;
			
			// check if modules can be indexed. If an array is provided, then we only delete those in the array
			if ($index_modules === TRUE OR (is_array($index_modules) AND isset($index_modules[$module])))
			{
				$module_obj = $this->CI->fuel->modules->get($module);
				$key_field = $module_obj->model()->key_field();
				if (is_array($posted))
				{
					foreach($posted as $key => $val)
					{
						if (is_int($key))
						{
							$data = array($key_field => $val);
							$location = $module_obj->url($data);
						}
					}
				}
				$this->remove($location);
			}
		}
		else
		{
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
			return $pages;
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
			'search',
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
	 * @param	string
	 * @param	boolean
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
		if ($this->is_local_url($location) AND $this->is_indexable($location))
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
						
						// remove page anchors
						$url_arr = explode('#', $url);
						$url = $this->get_location($url_arr[0]);
						
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
	 * Indexes a single page
	 *
	 * @access	public
	 * @param	string
	 * @param	string
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
		
		// remove the opening xml tag to prevent parsing issues
		$sitemap_xml = preg_replace('#<\?xml.+\?>#U', '', $sitemap_xml);

		@$dom->loadXML($sitemap_xml); 
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
	 * @param	string
	 * @return	boolean
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
	 * @param	boolean
	 * @return	string
	 */	
	function scrape_page($url, $just_header = FALSE)
	{
		if (!is_http_path($url))
		{
			$url = site_url($url);
		}

		$this->CI->curl->initialize();
		
		$opts = array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => TRUE,
						CURLOPT_TIMEOUT => $this->timeout,
						CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
						CURLOPT_BINARYTRANSFER => TRUE,
						CURLOPT_USERAGENT => $this->user_agent,
					);
		if ($just_header)
		{
			$opts[CURLOPT_HEADER] = TRUE;
			$opts[CURLOPT_NOBODY] = TRUE;
		}
		else
		{
			$opts[CURLOPT_HEADER] = FALSE;
			$opts[CURLOPT_NOBODY] = FALSE;
		}
		
		// add a CURL session
		$this->CI->curl->add_session($url, $opts);
		
		// execut the CURL request to scrape the page
		$output = $this->CI->curl->exec();
		
		// get any errors
		$error = $this->CI->curl->error();
		if (!empty($error))
		{
			$this->_add_error($error);
		}
		
		// if the page doesn't return a 200 status, we don't scrape
		$http_code = $this->CI->curl->info('http_code');
		
		if ($http_code != 200)
		{
			$msg = lang('search_log_index_page_error', 'HTTP Code '.$http_code.' for '.$url);
			$this->log_message($msg, self::LOG_ERROR);
			$this->_add_error($msg);
			return FALSE;
		}
		
		// remove javascript
		$output = strip_javascript($output);
		
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the DomXpath 
	 *
	 * @access	public
	 * @param	string	page content to search
	 * @param	string
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
			$this->log_message($msg, self::LOG_INDEXED);
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
	function remove($location, $scope = NULL)
	{
		$location = $this->get_location($location);
		
		$where['location'] = $location;
		if (!empty($scope))
		{
			$where['scope'] = $scope;
		}
		$deleted = $this->CI->search_model->delete($where);
		
		if ($deleted)
		{
			$msg = lang('search_log_index_removed', $location);
			$this->log_message($msg, self::LOG_REMOVED);
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
		$content = trim(preg_replace('#(\s)\s+|(\n)\n+|(\r)\r+#m', '$1', $content));
		$content = trim($content);
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
		$url = str_replace(site_url(), '', $url);
		if ($url .= '/')
		{
			$url = trim($url, '/');
		}
		return $url;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds an item to the index log
	 * 
	 * Used when printing out the index log informaiton
	 *
	 * @access	public
	 * @param	object	search record
	 * @param	string
	 * @return	void
	 */	
	function log_message($rec, $type = self::LOG_ERROR)
	{
		$this->_logs[$type][] = $rec;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of log messages
	 * 
	 * Used when printing out the index log informaiton
	 *
	 * @access	public
	 * @return	array
	 */	
	function logs()
	{
		return $this->_logs;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds an item to the index log
	 * 
	 * Used when printing out the index log informaiton
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	string
	 */	
	function display_log($type = 'all', $tag = 'span', $return = FALSE)
	{
		$str = '';
		$types = array(self::LOG_ERROR, self::LOG_REMOVED, self::LOG_INDEXED);
		
		if (is_string($type))
		{
			if (empty($type) OR !in_array($type, $types))
			{
				$type = $types;
			}
			else
			{
				$type = (array) $type;
			}
		}
		foreach($types as $t)
		{
			if (isset($this->_logs[$t]))
			{
				foreach($this->_logs[$t] as $l)
				{
					if (!empty($tag))
					{
						$str .= '<'.$tag.' class="'.$t.'">';
					}
					$str .= $l."\n";
					if (!empty($tag))
					{
						$str .= '</'.$tag.'>';
					}
				}
				if (!$return)
				{
					echo $str;
				}
			}
		}
		
		return $str;
	}
}

/* End of file Fuel_search.php */
/* Location: ./modules/search/libraries/Fuel_search.php */