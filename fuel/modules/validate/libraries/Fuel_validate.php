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
 * FUEL Validate class
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/validate
 */

// --------------------------------------------------------------------

class Fuel_validate extends Fuel_advanced_module {
	
	
	/**
	 * Constructor - Sets Fuel_backup preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct($params);

		$this->CI->load->helper('scraper');
		
		// initialize object if any parameters
		if (!empty($params))
		{
			$this->initialize($params);
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the validate object
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
	
	function html($uri, $w3c_view = FALSE)
	{
		$this->CI->load->library('user_agent');
		
		if (!$w3c_view)
		{
			$path = VALIDATE_PATH.'libraries/';
			set_include_path(get_include_path() . PATH_SEPARATOR . $path);
			require_once VALIDATE_PATH.'libraries/Services/W3C/HTMLValidator.php';
			$v = new Services_W3C_HTMLValidator();
		}
		
		// if valid_internal_domains config match then read the file and post to validator
		// determine if server is local
		$results = '';
		$local = FALSE;
		$validator_url = $this->fuel->validate->config('validator_url');
		
		$servers = (array) $this->fuel->validate->config('valid_internal_server_names');
		foreach($servers as $server)
		{
			$server = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $server));
			if (preg_match('#^'.$server.'$#', $_SERVER['SERVER_NAME'])) $local = TRUE;
		}
		
		// if the server is determined to be local, then we need to upload the file and post
		if ($local)
		{
			$this->CI->load->helper('file');
			
			// scrape html from page running on localhost
			$fragment = scrape_html($uri);

			// post data using fragment variable
			$tmp_filename = str_replace(array('/', ':'), '_', $uri);
			$tmp_filename = substr($tmp_filename, 4);
			$tmp_file_for_validation_urls = $this->CI->config->item('cache_path').'validation_url-'.$tmp_filename.'.html';
			write_file($tmp_file_for_validation_urls, $fragment);
			
			// if just hte data to be returned, then we validate it here
			if (!$w3c_view)
			{
				$results = $v->validateFile($tmp_file_for_validation_urls);
			}
			else
			{
				$post['uploaded_file'] = '@'.$tmp_file_for_validation_urls.';type=text/html';
				$results = scrape_html($validator_url, $post);
			}
			
			
			if (file_exists($tmp_file_for_validation_urls)) 
			{
				unlink($tmp_file_for_validation_urls);
			}
		}

		// else just pass it the uri value for it to read itself
		else
		{
			if (!$w3c_view)
			{
				$results = $v->validate($uri);
				
			}
			else
			{
				$w3c_url = $validator_url.'?uri='.$uri;
				$results = scrape_html($w3c_url);
			}
		}
		
		
		// just return data if not W3C view
		if (!$w3c_view)
		{
			return $results;
		}
		
		// do some html cleanup so that css and images pull
		$url_parts = parse_url($validator_url);
		
		// insert base_url so that the images/css pull from the correct place
		$base_url = 'http://'.$url_parts['host'].'/';
		$results = str_replace('</head>', "<base href=\"".$base_url."\" />".PHP_EOL."</head>", $results);
		$results = str_replace(array('"./style/base.css"', '"./style/base"'), '"'.$base_url.'style/base.css"', $results);
		$results = str_replace(array('"./style/results.css"', '"./style/results"'), '"'.$base_url.'style/results.css"', $results);
		return $results;
	}
	
	function w3c($uri)
	{
		$this->CI->load->library('user_agent');
		
		// if valid_internal_domains config match then read the file and post to validator
		// determine if server is local
		$results = '';
		$local = FALSE;
		$validator_url = $this->config('validator_url');
		
		$servers = (array) $this->fuel->validate->config('valid_internal_server_names');
		foreach($servers as $server)
		{
			$server = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $server));
			if (preg_match('#^'.$server.'$#', $_SERVER['SERVER_NAME'])) $local = TRUE;
		}
		
		// if the server is determined to be local, then we need to upload the file and post
		if ($local)
		{
			$this->CI->load->helper('file');
			
			// scrape html from page running on localhost
			$fragment = scrape_html($uri);

			// post data using fragment variable
			$tmp_filename = str_replace(array('/', ':'), '_', $uri);
			$tmp_filename = substr($tmp_filename, 4);
			$tmp_file_for_validation_urls = $this->CI->config->item('cache_path').'validation_url-'.$tmp_filename.'.html';
			write_file($tmp_file_for_validation_urls, $fragment);
			
			// if just hte data to be returned, then we validate it here
			if (!$w3c_view)
			{
				$results = $v->validateFile($tmp_file_for_validation_urls);
			}
			else
			{
				$post['uploaded_file'] = '@'.$tmp_file_for_validation_urls.';type=text/html';
				$results = scrape_html($validator_url, $post);
			}
			
			
			if (file_exists($tmp_file_for_validation_urls)) 
			{
				unlink($tmp_file_for_validation_urls);
			}
		}

		// else just pass it the uri value for it to read itself
		else
		{
			if (!$w3c_view)
			{
				$results = $v->validate($uri);
				
			}
			else
			{
				$w3c_url = $validator_url.'?uri='.$uri;
				$results = scrape_html($w3c_url);
			}
		}
		
		
		// just return data if not W3C view
		if (!$w3c_view)
		{
			return $results;
		}
		
		// do some html cleanup so that css and images pull
		$url_parts = parse_url($validator_url);
		
		// insert base_url so that the images/css pull from the correct place
		$base_url = 'http://'.$url_parts['host'].'/';
		$results = str_replace('</head>', "<base href=\"".$base_url."\" />".PHP_EOL."</head>", $results);
		$results = str_replace(array('"./style/base.css"', '"./style/base"'), '"'.$base_url.'style/base.css"', $results);
		$results = str_replace(array('"./style/results.css"', '"./style/results"'), '"'.$base_url.'style/results.css"', $results);
		return $results;
	}
	
	function html2($uri)
	{
		$this->CI->load->library('user_agent');
		
		// if valid_internal_domains config match then read the file and post to validator
		// determine if server is local
		$results = '';
		$local = TRUE;
		$validator_url = $this->fuel->validate->config('validator_url').'/?output=soap12';
		
		$servers = (array) $this->fuel->validate->config('valid_internal_server_names');
		foreach($servers as $server)
		{
			$server = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $server));
			if (preg_match('#^'.$server.'$#', $_SERVER['SERVER_NAME'])) $local = TRUE;
		}
		
		// if the server is determined to be local, then we need to upload the file and post
		if ($local)
		{
			$this->CI->load->helper('file');
			
			// scrape html from page running on localhost
			$fragment = scrape_html($uri);

			// post data using fragment variable
			$tmp_filename = str_replace(array('/', ':'), '_', $uri);
			$tmp_filename = substr($tmp_filename, 4);
			$tmp_file_for_validation_urls = $this->CI->config->item('cache_path').'validation_url-'.$tmp_filename.'.html';
			write_file($tmp_file_for_validation_urls, $fragment);
			echo "<pre style=\"text-align: left;\">";
			print_r($tmp_file_for_validation_urls);
			echo "</pre>";
			
			echo "<pre style=\"text-align: left;\">";
			print_r($validator_url);
			echo "</pre>";
			
			$post['uploaded_file'] = '@'.$tmp_file_for_validation_urls.';type=text/html';
			$results = scrape_html($validator_url, $post);
			
			
			echo "<pre style=\"text-align: left;\">";
			print_r($results);
			echo "</pre>";
			exit();
			$result_arr = array();
			$xml = new DomDocument();
			@$xml->loadXML($results);
			$xpath = new DOMXpath($xml);
			$xpath->registerNamespace("m", "http://www.w3.org/2005/10/markup-validator");


			$elements = $xpath->query("//m:validity");
			if($elements->item(0)->nodeValue == 'true')
			{
				$result_arr['status'] = 'valid';
			}
			else
			{
				$result_arr['status'] = 'invalid';
			}

			$elements = $xpath->query("//m:errorcount");
			$result_arr['err_num'] = intval($elements->item(0)->nodeValue);

			$result_arr['errors'] = array();
			$result_arr['warnings'] = array();

			if ($elements->item(0) && $elements->item(0)->nodeValue > 0)
			{

				$node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:line");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['errors'][$i]['line'] = intval($node->nodeValue);
					$i++;
				}	

				$node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:col");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['errors'][$i]['col'] = intval($node->nodeValue);
					$i++;
				}

				$node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:message");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['errors'][$i]['message'] = $node->nodeValue;
					$i++;
				}

				$node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:messageid");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['errors'][$i]['messageid'] = $node->nodeValue;
					$i++;
				}

				$node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:explanation");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['errors'][$i]['explanation'] = trim($node->nodeValue);
					$i++;
				}
			}

			$elements = $xpath->query("//m:warningcount");
			$result_arr['warn_num'] = intval($elements->item(0)->nodeValue);

			if ($elements->item(0) && $elements->item(0)->nodeValue > 0)
			{
				$node_arr = $xpath->query("//m:warnings/m:warninglist/m:warning/m:messageid");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['warnings'][$i]['messageid'] = trim($node->nodeValue);
					$i++;
				}
				$node_arr = $xpath->query("//m:warnings/m:warninglist/m:warning/m:message");
				$i = 0;
				foreach ($node_arr as $node)
				{
					$result_arr['warnings'][$i]['message'] = trim($node->nodeValue);
					$i++;
				}
			}
			
			
			$client = new SoapClient($validator_url);
			// Call the SOAP method
			$result = $client->call('hello', array('name' => 'Scott'));
			// Display the result
			print_r($result);
			//$result = $client->TopGoalScorers(array('iTopN'=>5));
			
			// if just hte data to be returned, then we validate it here
			if (!$w3c_view)
			{
				$results = $v->validateFile($tmp_file_for_validation_urls);
			}
			else
			{
				$post['uploaded_file'] = '@'.$tmp_file_for_validation_urls.';type=text/html';
				$results = scrape_html($validator_url, $post);
			}
			
			
			if (file_exists($tmp_file_for_validation_urls)) 
			{
				unlink($tmp_file_for_validation_urls);
			}
		}

		// else just pass it the uri value for it to read itself
		else
		{
		}
		
		// 
		// 
		// // do some html cleanup so that css and images pull
		// $url_parts = parse_url($validator_url);
		// 
		// // insert base_url so that the images/css pull from the correct place
		// $base_url = 'http://'.$url_parts['host'].'/';
		// $results = str_replace('</head>', "<base href=\"".$base_url."\" />".PHP_EOL."</head>", $results);
		// $results = str_replace(array('"./style/base.css"', '"./style/base"'), '"'.$base_url.'style/base.css"', $results);
		// $results = str_replace(array('"./style/results.css"', '"./style/results"'), '"'.$base_url.'style/results.css"', $results);
		// return $results;
	}
	
	
	function links($url, $just_invalid = FALSE)
	{
		// grab all the links from a remote file
		//$links = scrape_dom($url, '//a');
		$this->CI->load->module_helper(FUEL_FOLDER, 'scraper');
		// use this method which is faster
		$html = scrape_html($url);
		
		preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $html, $matches);
		$links = (!empty($matches[1])) ? $matches[1] : array();
		
		$formatted_links = array();
		foreach($links as $href)
		{
			//$href = $link->getAttribute('href');
			if (substr($href, 0, 7) != 'mailto:' AND substr($href, 0, 1) != '#' AND substr($href, 0, 11) != 'javascript:')
			{
				if (substr($href, 0, 1) == '/')
				{
					$href = "http://".$_SERVER['HTTP_HOST'].$href;
				}
				else if (substr($href, 0, 4) != 'http')
				{
					$href = $url.$href;
				}
				
				// set the key value so that we only grab the link once
				$formatted_links[$href] = $href;
			}
		}
		
		$results = '';
		
		// now loop through the links and check if they are valid
		$return = ($just_invalid) ? array() :  array('valid' => array(), 'invalid' => array());
		foreach($formatted_links as $link)
		{
			$is_valid = is_valid_page($link);
			if ($just_invalid)
			{
				// capture just invalid links
				if (!$is_valid)
				{
					$return[] = $link;
				}
			}
			else
			{
				if ($is_valid)
				{
					$return['valid'][] = $link;
				}
				else
				{
					$return['invalid'][] = $link;
				}
			}
		}
		return $return;
		
	}
	
	function size_report($url)
	{
		$this->CI->load->helper('number');
		$this->CI->load->library('user_agent');
		
		$html = scrape_dom($url);
		
		$xpath = new DOMXPath( $html );
		
		// get image and extenral css and scripts
		$imgs = $xpath->query("//img[@src]");
		$css =  $xpath->query("//link[@href]");
		$js =  $xpath->query("//script[@src]");
		
		$ext_resources = array($imgs, $css, $js);
		$ext_resources_xpath_attr = array('src', 'href', 'src');
		
		$resources = array();
		$css_resources = array();
		
		$i = 0;
		foreach($ext_resources as $val)
		{
			foreach($val as $r)
			{
				$href = $r->getAttribute($ext_resources_xpath_attr[$i]);
				if (substr($href, 0, 1) == '/')
				{
					$href = "http://".$_SERVER['HTTP_HOST'].$href;
				}
				else if (substr($href, 0, 4) != 'http')
				{
					$href = $url.$href;
				}
				
				// check if css so we can put into a css_resource array to parse for bg images
				if ($ext_resources_xpath_attr[$i] == 'href')
				{
					$css_resources[] = $href;
				}
				$resources[] = $href;

			}
			$i++;
		}
		
		// now look through all css get background urls
		foreach($css_resources as $href)
		{
			$css_contents = file_get_contents($href);
			
			$file_base = explode('/', $href);
			array_pop($file_base);
			$file_base = implode('/', $file_base).'/';
			preg_match_all('|url\((.+)\)|Umis', $css_contents, $matches);
			if (isset($matches[1]))
			{
				foreach($matches[1] as $match)
				{
					if (substr($match, 0, 4) != 'http')
					{
						$resources[] = $file_base.$match;
					}
					else
					{
						$resources[] = $match;
					}
				}
			}
		}
		
		// remove duplicates
		$resources = array_unique($resources);
		
		$i++;
		$results = '';
		
		// now loop through the links and check if they are valid
		$valid = array();
		$invalid = array();
		$output_arr = array();
		$config_limit = $this->fuel->validate->config('size_report_warn_limit');
		$filesize_range = array('warn' => array(), 'ok' => array());
		$total_kb = 0;
		
		// using normal curl here so that we can use the same $ch object for multiple requests
		$ch = curl_init();
		
		foreach($resources as $link)
		{
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_USERAGENT, $this->CI->agent->agent_string());
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE); // will cause strange behavior if not set to TRUE
			
			$ret = curl_exec($ch);
			$err_num = curl_errno($ch);
			$info = curl_getinfo($ch);
			
			if ($info['http_code'] >= 400)
			{
				$invalid[] = $link;
				$output_arr[$link] = 'Invalid';
			}
			else
			{
				$valid[] = $link;
				$output_arr[$link] = $info['download_content_length'];
				
				// set filesize range
				$kb = $output_arr[$link]/1000;
				$total_kb += $output_arr[$link];
				switch($kb)
				{
					case ($kb < 0):
						$filesize_range['error'][$link] = $output_arr[$link];
						break;
					case ($kb >= $config_limit):
						$filesize_range['warn'][$link] = $output_arr[$link];
						break;
					default:
						$filesize_range['ok'][$link] = $output_arr[$link];
				}
			}
			
		}
		curl_close($ch);
		
		$results['invalid'] = $invalid;
		$results['valid'] = $valid;
		$results['total'] = count($resources);
		$results['total_kb'] = $total_kb;
		$results['link'] = $url;
		$results['config_limit'] = $config_limit;
		$results['filesize_range'] = $filesize_range;

		return $results;
	}

}

/* End of file Fuel_page_analysis.php */
/* Location: ./modules/fuel/libraries/Fuel_page_analysis.php */