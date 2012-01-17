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

class Fuel_search {
	
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
		$this->CI->load->module_config(SEARCH_FOLDER, 'search');
		$this->CI->load->module_language(SEARCH_FOLDER, 'search');
		$this->CI->load->module_model(SEARCH_FOLDER, 'search_model');
		$this->CI->load->module_library(FUEL_FOLDER, 'fuel_page');

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
	 * Hook for saving after a module
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
		$model = $this->CI->model_name;
		$module = $this->CI->module;
		
		// check if modules can be indexed. If an array is provided, then we only index those in the array
		if ($index_modules === TRUE OR (is_array($index_modules) AND isset($index_modules[$module])))
		{
			$location = $this->CI->_preview_path($posted);
			$this->index($location, $model);
		}
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Hook for saving after a module
	 * 
	 * Used when something is saved in admin to automatically update the index
	 *
	 * @access	public
	 * @param	object	search record
	 * @return	boolean
	 */	
	function after_save_hook_old($posted)
	{
		// if indexing is disabled, then we just return and don't continue'
		if (!$this->config('indexing_enabled')) return;
		
		// grab the config values for what should be indexed on save
		$index_fields = $this->config('index_fields');
		$model = $this->CI->model_name;
		$module = $this->CI->module;
		
		// check if the current module is in the list of modules that we index
		if (isset($index_fields[$module]))
		{
			$location = $this->CI->_preview_path($posted);
			
			$fields = (array)$index_fields[$module];
			
			// if there is a field in the array with the key of 'title', then we use that
			if (isset($fields['title']))
			{
				$title_field = $fields['title'];
			}
			// otherwise we use the first field
			else
			{
				$title_field = $fields[0];
			}
			$title = (isset($posted[$title_field])) ? $posted[$title_field] : '';
			
			$content = '';
			$content_arr = array();
			foreach($fields as $f)
			{
				if (!empty($posted[$f]) AND is_string($posted[$f]))
				{
					$content_arr[] = $posted[$f];
				}
			}
			$content = implode('|', $content_arr);
			
			// create search record
			if (!empty($content) AND !empty($title))
			{
				$rec = array(
					'location' => $location,
					'model' => $model,
					'title' => $this->format_title($title),
					'content' => $this->clean($content),
					'context' => 'pages',
					);

				if (!$this->create($rec))
				{
					$this->_add_error(lang('search_error_saving'));
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Indexes the data for the search
	 *
	 * @access	public
	 * @return	array
	 */	
	function index($pages = array(), $context = 'pages')
	{
		// check if indexing is enabled first
		if ($this->config('indexing_enabled'))
		{
			// if pages is empty, then we'll look at the sitemap.xml or the pages_model'
			if (empty($pages))
			{
				$pages = $this->indexable_pages($pages);
			}
			
			$pages = (array) $pages;

			// render the pages then look for delimiters within to get the content
			foreach($pages as $location)
			{
				// get the page HTML need to use CURL instead of render_page because one show_404 error will halt the indexing
				$html = $this->scrape_page($location);
				
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
						'model' => 'pages_model',
						'title' => $title,
						'content' => $content,
						'context' => 'pages',
						);

					if (!$this->create($rec))
					{
						$this->_add_error(lang('search_error_saving'));
					}
				}
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
	 * Crawls the site for locations to be indexed
	 *
	 * @access	public
	 * @return	array
	 */	
	function crawl($root_page = 'home')
	{
		static $indexed;
		
		// start at the homepage if no root page is specified
		if (empty($root_page) OR $root_page == 'home')
		{
			$root_page = site_url();
		}
		
		// grab the HTML of the page to get all the links
		$html = $this->scrape_page($root_page);
		
		// grab all the page links
		preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $html, $matches);
		
		if (!empty($matches[1]))
		{
			foreach($matches[1] as $url)
			{
				// check if the url is local AND whether it has already been indexed
				if ($this->is_local_url($url) AND !isset($indexed[$url]))
				{
					$this->index($url);
					$indexed[$url] = $url;
					
					// now recursively crawl
					$this->crawl($url);
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Provides a list of pages to index
	 *
	 * @access	public
	 * @param	array	pages to index. Can contain regex
	 * @param	boolean	whether to use regex pattern matching against the URLs 
	 * @return	array
	 */	
	function indexable_pages()
	{
		// if no pages provided, we load them all
		if (empty($pages))
		{
			$pages = $this->parse_sitemap();
			if (empty($pages))
			{
				$this->CI->load->module_model(FUEL_FOLDER, 'pages_model');
				$pages = $this->CI->pages_model->all_pages_including_views();
			}
		}
		
		// pages to index
		$to_index = array();
		
		// get pages to exlucde
		$exclude = (array)$this->config('exclude');
		
		if (!empty($exclude))
		{
			foreach($pages as $p)
			{
				// loop through the pages array looking for wild-cards
				foreach ($exclude as $val)
				{
					// convert wild-cards to RegEx
					$val = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $val));
		
					// does the RegEx match? If so, we don't include it in the index'
					if (!preg_match('#^'.$val.'$#', $p) AND !in_array($val, $to_index))
					{
						array_push($to_index, $p);
					}
				}
			}
		}
		else
		{
			$to_index = $pages;
		}
		$to_index = $pages;
		return $to_index;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Parses the sitemap.xml file of the site and returns an array of pages to index
	 *
	 * @access	public
	 * @return	array
	 */	
	function parse_sitemap($remove_site_url = TRUE)
	{
		$locations = array();

		$sitemap_url = site_url('sitemap.xml');
		$sitemap_xml = $this->scrape_page($sitemap_url);
		if (!$sitemap_xml)
		{
			return FALSE;
		}
		
		$dom = new DOMDocument; 
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($sitemap_xml); 
		$locs = $dom->getElementsByTagName('loc');
		
		$site_url = site_url();
		foreach($locs as $node)
		{
			$loc = (string) $node->nodeValue;
			if ($remove_site_url)
			{
				$locations[] = str_replace($site_url, '', $loc);
			}
			else
			{
				$locations[] = $loc;
			}
		}
		return $locations;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CURLs the page and gets the content
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function scrape_page($url)
	{
		if (!is_http_path($url))
		{
			$url = site_url($url);
		}
		
		$curl = curl_init();
		$opts = array(
						CURLOPT_URL => $url,
						CURLOPT_HEADER => 0,
						CURLOPT_RETURNTRANSFER => TRUE,
						CURLOPT_TIMEOUT => $this->timeout,
						CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
						CURLOPT_BINARYTRANSFER => TRUE,
						CURLOPT_USERAGENT => $this->user_agent,
					);
		curl_setopt_array($curl, $opts);
		$output = curl_exec($curl);
		
		// any errors we capture
		$error = curl_error($curl);
		if (!empty($error))
		{
			$this->_add_error($error);
		}
		
		// if the page doesn't return a 200 status, we don't scrape
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
		{
			return FALSE;
		}
		curl_close($curl);
		
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders the page
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function render_page($location)
	{
		$page_init = array('location' => $location);
		$this->CI->fuel_page->initialize($page_init);
		$page = $this->CI->fuel_page->render(TRUE, FALSE);
		return $page;
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
				$query = $this->derive_xpath_from_node($query);
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
		
		// if no h1, then we get the title
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
	function derive_xpath_from_node($node)
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
	 * Creates a record in the search table. Will overwrite if the same location/model exist
	 *
	 * @access	public
	 * @param	mixed	can be a string or an array. If an array, it must contain the other values
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	boolean
	 */	
	function create($location, $model = NULL, $content = NULL, $context = NULL)
	{
		if (!is_array($location))
		{
			$values['location'] = $location;
			$values['model'] = $model;
			$values['title'] = $title;
			$values['content'] = $content;
			$values['context'] = $context;
		}
		else
		{
			$values = $location;
		}
		$values['location'] = str_replace(site_url(), '', $values['location']);
		$values['title'] = $this->format_title($values['title']);
		$values['content'] = $this->clean($values['content']);

		// to some checks here first to make sure it is valid content
		if (!$this->is_local_url($values['location']) OR !isset($values['content']) OR !isset($values['title']))
		{
			return FALSE;
		}

		$saved = $this->CI->search_model->save($values);

		if ($saved)
		{
			$this->log($values);
			return TRUE;
		}
		return FALSE;
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
			$str .= "Indexed: ".$l['location']."\n";
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