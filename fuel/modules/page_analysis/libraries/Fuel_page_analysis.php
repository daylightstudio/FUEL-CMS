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
 * Page analysis 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/page_analysis
 */

// --------------------------------------------------------------------


class Fuel_page_analysis extends Fuel_advanced_module {
	
	public $url = '';
	protected $_xpath = '';
	
	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();

		if (!extension_loaded('curl')) 
		{
			$this->_add_error(lang('error_no_curl_lib'));
		}
		
		if (empty($params))
		{
			$params['name'] = 'page_analysis';
		}
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the page analysis object
	 *
	 * Accepts an associative array as input, containing backup preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		$this->set_params($this->_config);
		
	}
	
	function set_page($url)
	{
		if (!is_http_path($url))
		{
			$url = site_url($url);
		}
		
		// turn off the warnings for bad html
		$old_setting = libxml_use_internal_errors(TRUE); 
		libxml_clear_errors(); 
		$dom = new DOMDocument(); 
		
		if (empty($url))
		
		$html = file_get_contents($url); // suppress errors because it
		// scrape html from page running on localhost
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);
		
		// clean html from script and css tags
		$html = preg_replace('/<script(.)*>(.+)<\/script>/Um', '', $html); // javascript
		$html = preg_replace('/<style(.)*>(.+)<\/style>/Um', '', $html); // css
		
		if (!$dom->loadHTML($html))
		{
			$this->_add_error(lang('page_analysis_error_loading_html', $url));
		} 
		// change errors back to original settings
		libxml_clear_errors(); 
		libxml_use_internal_errors($old_setting); 
		
		$this->_xpath = new DOMXPath($dom);
		$this->url = $url;
	}
	
	function find($tag, $multiple = TRUE)
	{
		// a semicolon separates out the attributes to search for in a tag
		$attrs = explode(';', $tag);
		
		$xpath_results = $this->_xpath->query("//".$attrs[0]);
		
		if (empty($xpath_results)) continue;
		$results = array();
		
		foreach($xpath_results as $r)
		{
			$content = NULL;
			
			// if an attribute needs to be found, we continue the search within the tag
			if (count($attrs) > 2)
			{
				$new_attrs = $attrs;
				array_shift($new_attrs);
				foreach($new_attrs as $attr)
				{
					$content[$attr] = $r->getAttribute($attr);
				}
			}
			// if there is only one attribute, then we grab just that one
			else if (isset($attrs[1]))
			{
				$content = $r->getAttribute($attrs[1]);
			}
			// otherwise, we get the contents of the node
			else
			{
				$content = $r->nodeValue;
			}
			
			// put the results into the result array
			if (!empty($content))
			{
				$content = (string) $content;
				if (!$multiple)
				{
					$results = trim($content);
					break;
				}
				else
				{
					$results[] = $content;
				}
			}
		}
		return $results;
	}
	
	function title($page = NULL)
	{
		$content = $this->find('title', FALSE);
		return $content;
	}
	
	
	function meta_description()
	{
		$content = $this->find('meta[@name="description"];content', FALSE);
		return $content;
	}
	
	function meta_keywords()
	{
		$content = $this->find('meta[@name="keywords"];content', FALSE);
		return $content;
	}
	
	function heading($h = '1')
	{
		if (is_numeric($h))
		{
			$h = 'h'.$h;
		}
		$content = $this->find($h, FALSE);
		return $content;
	}
	
	function top_keywords($limit = 20)
	{
		$content = $this->find('body', FALSE);

		$content = str_replace("\n", " ", $content);
		
		// split content into words
		$page_words = preg_split("/[\s,\.]+/", $content);
		$page_words_count = array();
		foreach($page_words as $word)
		{
			// only grab alpha numeric values of words
			if (ctype_alnum($word))
			{
				if (empty($page_words_count[$word]) && strlen($word) > 3)
				{
					$page_words_count[$word] = 0;
				}
				if (strlen($word) > 3)
				{
					$page_words_count[$word] += 1;
				}
			}
		}

		uasort($page_words_count, array(&$this, '_sort_word_density'));
		$page_words_count_limited = array_slice($page_words_count, 0, $limit);
		return $page_words_count_limited;
	}
	
	function first_paragraph_words($limit = 100)
	{
		$content = $this->find('p');
		$content = word_limiter(implode(' ', $content), $limit);
		return $content;
	}
	
	function image_alt()
	{
		$content = $this->find('img;alt;src');
		$results = array();
		foreach($content as $key => $img)
		{
			if (substr($img['src'], 0, 4) != 'http')
			{
				$img['src'] = $this->url.'/'.$img['src'];
			}
			if (empty($img['alt']))
			{
				$img['alt'] = lang('page_analysis_image_alt_empty');
			}
			$results[] = anchor($img['src'], $img['alt']);
		}
	}
	
	function outbound_links()
	{
		$content = $this->find('a[@href];href');
		
		// format links
		$formatted_links = array();
		$local_hostname = parse_url(site_url());
		$results = array();
		$url = $this->url;
		if (!empty($content))
		{
			foreach($content as $key => $link)
			{
				$link = str_replace(array('#', 'javascript:;', 'javascript:void(0);'), '', $link);

				if (!empty($link) && substr($link, 0, 7) != 'mailto:')
				{
					$href = $url.$link;
					if (substr($href, 0, 4) != 'http')
					{
						$href = $url.$link;
					}
					$formatted_links[] = $link;

					$link_parsed = parse_url($link);
					if (!empty($link_parsed['host']) && $link_parsed['host'] != $local_hostname['host'])
					{
						$results[] = anchor($link);
					}

				}
			}
		}
		return $results;
	}
	
	function report($url)
	{
		$keyword_limit = 20;
		
		$this->set_page($url);
		$results[lang('heading_title')] = $this->title();
		$results[lang('heading_description')] = $this->meta_description();
		$results[lang('heading_keywords')] = $this->meta_description();
		$results['&lt;H1&gt;'] = $this->heading(1);
		$results['&lt;H2&gt;'] = $this->heading(2);
		$results['&lt;H3&gt;'] = $this->heading(3);
		$results['&lt;H4&gt;'] = $this->heading(4);
		$results[lang('heading_top_keywords', $keyword_limit)] = $this->top_keywords($keyword_limit);
		$results[lang('heading_first_100_words')] = $this->first_paragraph_words();
		$results[lang('heading_outbound_links')] = $this->outbound_links();
		$results[lang('heading_image_alt')] = $this->image_alt();
		return $results;
		
	}

	protected function _sort_word_density($a, $b)
	{
		if ($a == $b) {
		    return 0;
		}
    	return ($a < $b) ? 1 : -1;
	}
}

/* End of file Fuel_page_analysis.php */
/* Location: ./modules/fuel/libraries/Fuel_page_analysis.php */