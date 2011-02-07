<?php
require_once(SEO_PATH.'libraries/Seo_base_controller.php');

class Seo extends Seo_base_controller {
	
	var $nav_selected = 'tools/seo';
	
	function __construct()
	{
		parent::__construct();
	}

	
	function index()
	{
		$this->_validate_user('tools/seo');
		
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));
		$url = '';
		if ($this->input->post('page'))
		{
			
			$this->load->helper('text');
			
			// first we grab links using the DomDocument object

			// turn off the warnings for bad html
			$oldSetting = libxml_use_internal_errors(TRUE); 
			libxml_clear_errors(); 
			$url = $this->input->post('page');
			$html = new DOMDocument(); 
			//$url = 'http://www.thedaylightstudio.com';
			
			$page_html = @file_get_contents($url); // suppress errors because it
			
			// scrape html from page running on localhost
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$page_html = curl_exec($ch);
			curl_close($ch);
			
			
			// clean html from script and css tags
			$page_html = preg_replace('/<script(.)*>(.+)<\/script>/Um', '', $page_html); // javascript
			$page_html = preg_replace('/<style(.)*>(.+)<\/style>/Um', '', $page_html); // css
			
			
			//if (!@$html->loadHTMLFile($url))
			if (!@$html->loadHTML($page_html))
			{
				$vars['error'] = lang('error_checking_page_links');
			} 

			$xpath = new DOMXPath( $html );


			$results = array();
			$keyword_limit = 20;
			$heading_top_keywords = lang('heading_top_keywords', $keyword_limit);
			$heading_first_100_words = lang('heading_first_100_words');
			$heading_outbound_links = lang('heading_outbound_links');
			$heading_image_alt = lang('heading_image_alt');
			
			
			// search meta elements elements with attributes
			$search_tags = array(
				lang('heading_title') => 'title',
				lang('heading_description') => 'meta[@name="description"];content', 
				lang('heading_keywords') => 'meta[@name="keywords"];content',
				'&lt;H1&gt;' => 'h1', 
				'&lt;H2&gt;' => 'h2', 
				'&lt;H3&gt;' => 'h3', 
				'&lt;H4&gt;' => 'h4', 
			//	$top_keywords => 'h1|//h2|//h3|//h4|//h5|//h6|//p|//li|//blockquote|//a|//div|//address|//cite',
				$heading_top_keywords => 'body',
				$heading_first_100_words => 'p',
				'Links' => 'a[@href];href', 
				$heading_image_alt => 'img;alt;src'
			);
				
			foreach($search_tags as $key => $tag)
			{
				//if (empty($results[$key])) $results[$key] = array();
				$attrs = explode(';', $tag);
				
				$xpath_results = $xpath->query("//".$attrs[0]);
				if (empty($xpath_results)) continue;
				foreach($xpath_results as $r)
				{
					if (is_int($key)) $key = $tag;
					$content = NULL;
					if (count($attrs) > 2)
					{
						$new_attrs = $attrs;
						array_shift($new_attrs);
						foreach($new_attrs as $attr)
						{
							$content[$attr] = $r->getAttribute($attr);
						}
					}
					else if (isset($attrs[1]))
					{
						$content = $r->getAttribute($attrs[1]);
					}
					else
					{
						$content = $r->nodeValue;
					}
					if (!empty($content))
					{
						$results[$key][] = $content;
					}
				}
			}
			
			if (!empty($results[$heading_top_keywords][0]))
			{
				// clean up script tags and style tags
				$page_content = $results[$heading_top_keywords][0];
				// echo "<pre style=\"text-align: left;\">";
				// print_r($page_content);
				// echo "</pre>";

				$page_content = str_replace("\n", " ", $page_content);
				
				$page_words = preg_split("/[\s,\.]+/", $page_content);
				$page_words_count = array();
				foreach($page_words as $word)
				{
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
				$page_words_count_limited = array_slice($page_words_count, 0, $keyword_limit);
				$results[$heading_top_keywords] = $page_words_count_limited;
			}

			
			// format First 100 words
			if (!empty($results[$heading_first_100_words]))
			{
				$results[$heading_first_100_words] = word_limiter(implode(' ', $results[$heading_first_100_words]), 100);
			}
			
			// format image alt tags
			if (!empty($results[$heading_image_alt]))
			{
				foreach($results[$heading_image_alt] as $key => $img)
				{
					if (substr($img['src'], 0, 4) != 'http')
					{
						$img['src'] = $url.'/'.$img['src'];
					}
					if (empty($img['alt']))
					{
						$img['alt'] = lang('seo_image_alt_empty');
					}
					$results[$heading_image_alt][$key] = anchor($img['src'], $img['alt']);
				}
			}
			
			// format links
			$formatted_links = array();
			$local_hostname = parse_url(site_url());
			if (!empty($results['Links']))
			{
				foreach($results['Links'] as $key => $link)
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
							$results[$heading_outbound_links][] = anchor($link);
						}

					}
				}
				$results['Links'] = $formatted_links;
			}
			
			
			unset($results['Links']);
			
			// change errors back to original settings
			libxml_clear_errors(); 
			libxml_use_internal_errors( $oldSetting ); 
			
			$vars['results'] = $results;
		} 
		
		
		$this->js_controller_params['method'] = 'links';
		$this->js_controller_params['pages'] = $this->input->post('pages');
		
		$this->load->module_model(FUEL_FOLDER, 'pages_model');
		$pages = $this->pages_model->all_pages_including_views(TRUE);

		$this->js_controller_params['method'] = 'page_analysis';
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['url'] = $url;
		$vars['pages_select'] = $pages;
		$vars['page_title'] = $this->_page_title(array('Tools', 'SEO'), FALSE);
		
		$this->_render('page_analysis', $vars);
		
	}
	
	function _sort_word_density($a, $b)
	{
		if ($a == $b) {
		    return 0;
		}
    	return ($a < $b) ? 1 : -1;
	}
}

/* End of file seo.php */
/* Location: ./fuel/modules/seo/controllers/seo.php */