<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');

class Validate extends Fuel_base_controller {
	
	public $nav_selected = 'tools/validate|tools/validate/:any';
	public $view_location = 'validate';
	
	function __construct()
	{
		parent::__construct();
		$this->load->config('validate');
		$this->load->language('validate');
		$this->js_controller_params['module'] = 'tools';
		
		// get localized js
		$js_localized = json_lang('validate/validate_js', FALSE);
		
		$this->_load_js_localized($js_localized);
		$this->_validate_user('tools/validate');
		
		// set pages input to blank if it is default value
		if (!empty($_POST['pages_input']))
		{
			if ($_POST['pages_input'] == lang('validate_pages_input'))
			{
				$_POST['pages_input'] = FALSE;
			}
		}
		
	}
	
	function index()
	{
		//if (!$this->_has_module('fuel')) show_error(lang('error_missing_module', 'validate'));
		
		$this->load->module_model(FUEL_FOLDER, 'pages_model');
		$pages = $this->pages_model->all_pages_including_views(TRUE);
		$validate_config = $this->config->item('validate');
		$vars['default_page_input'] = $validate_config['default_page_input'];
		$vars['error'] = (!extension_loaded('curl')) ? lang('error_no_curl_lib') : '';
		$vars['validation_type'] = lang('validate_type_html');
		$vars['pages_select'] = $pages;
		$this->js_controller_params['method'] = 'validate';
		$this->_render('validate', $vars);
	}

	function html()
	{
		//if (!$this->_has_module('fuel')) show_error(lang('error_missing_module'));
		
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));
		
		$validate_config = $this->config->item('validate');
		
		if ($this->input->post('uri'))
		{
			$this->load->library('user_agent');
			$this->load->module_model(FUEL_FOLDER, 'pages_model');
			
			$page_data = $this->pages_model->find_by_location($this->input->post('uri'), FALSE);
			
			
			// if valid_internal_domains config match then read the file and post to validator
			// determine if server is local
			$results = '';
			$local = false;
			$servers = (array) $validate_config['valid_internal_server_names'];
			foreach($servers as $server)
			{
				$server = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $server));
				if (preg_match('#^'.$server.'$#', $_SERVER['SERVER_NAME'])) $local = TRUE;
			}
			if ($local)
			{
				$this->load->helper('file');
				// scrape html from page running on localhost
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $this->input->post('uri'));
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->agent->agent_string());
				
				$fragment = curl_exec($ch);
				curl_close($ch); 

				// post data using fragment variable
				$tmp_filename = str_replace(array('/', ':'), '_', $this->input->post('uri'));
				$tmp_filename = substr($tmp_filename, 4);
				$tmp_file_for_validation_urls = $this->config->item('cache_path').'validation_url-'.$tmp_filename.'.html';
				write_file($tmp_file_for_validation_urls, $fragment);

				//$post['fragment'] = $fragment;
				$post['uploaded_file'] = '@'.$tmp_file_for_validation_urls.';type=text/html';
				
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $validate_config['validator_url']);
				curl_setopt($ch, CURLOPT_HEADER, 0); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

				$results = curl_exec($ch);
				curl_close($ch); 

				if (file_exists($tmp_file_for_validation_urls)) unlink($tmp_file_for_validation_urls);
			}

			// else just pass it the uri value for it to read itself
			else
			{
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $validate_config['validator_url'].'?uri='.$this->input->post('uri'));
				curl_setopt($ch, CURLOPT_HEADER, 0); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				// curl_setopt($ch, CURLOPT_POST, 1);
				// curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				$results = curl_exec($ch);
				curl_close($ch); 
			}

			// do some html cleanup so that css and images pull
			$url_parts = parse_url($validate_config['validator_url']);
			$base_url = 'http://'.$url_parts['host'].'/';
			$results = str_replace('</head>', "<base href=\"".$base_url."\" />".PHP_EOL."</head>", $results);
			if (!empty($page_data['id'])) $results = str_replace('<body>', "<body><span style=\"display: none\" id=\"edit_url\">".fuel_url('pages/edit/'.$page_data['id'])."</span>", $results);
			$results = str_replace(array('"./style/base.css"', '"./style/base"'), '"'.$base_url.'style/base.css"', $results);
			$results = str_replace(array('"./style/results.css"', '"./style/results"'), '"'.$base_url.'style/results.css"', $results);
			$vars['results'] = $results;
			$this->output->set_output($results);
			return;
		} 
		else if (!$this->input->post('pages') AND !$this->input->post('pages_input') AND !$this->input->post('pages_serialized'))
		{
			$this->session->set_flashdata('error', lang('error_no_pages_selected'));
			redirect(fuel_uri('tools/validate'));
		}

		if (!is_writable($this->config->item('cache_path')))
		{
			$vars['error'] = lang('error_cache_folder_not_writable', $this->config->item('cache_path'));
		}
		
		$this->js_controller_params['method'] = 'html';
		// $vars['js_method'] = 'html';
		$pages = $this->_get_pages();
		$this->js_controller_params['pages'] = $pages;
		$vars['pages_serialized'] = base64_encode(serialize($pages));
		$vars['validation_type'] = lang('validate_type_html');
		$vars['page_title'] = $this->_page_title(array('Tools', 'Validate', 'HTML'), FALSE);
		
		$this->_render('run', $vars);
	}

	function links()
	{
		//if (!$this->_has_module('fuel')) show_error(lang('error_missing_module'));
		
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));
		if ($this->input->post('uri'))
		{
			$this->load->library('user_agent');
			// first we grab links using the DomDocument object
			
			// turn off the warnings for bad html
			$oldSetting = libxml_use_internal_errors(TRUE); 
			libxml_clear_errors(); 
			$url = $this->input->post('uri');
			$html = new DOMDocument(); 
			
			if (!@$html->loadHTMLFile($url))
			{
				$vars['error'] = lang('error_checking_page_links');
			} 

			$xpath = new DOMXPath( $html ); 
			$links = $xpath->query("//a");

			$formatted_links = array();
			foreach($links as $link)
			{
				$href = $link->getAttribute('href');

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
					$formatted_links[] = $href;

				}

			}
			
			// change errors back to original settings
			libxml_clear_errors(); 
			libxml_use_internal_errors( $oldSetting ); 
			
			
			$results = '';
			
			// now loop through the links and check if they are valid
			$valid = array();
			$invalid = array();
			$ch = curl_init();
			foreach($formatted_links as $link)
			{
				curl_setopt($ch, CURLOPT_URL, $link);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_NOBODY, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->agent->agent_string());
				$ret = curl_exec($ch);
				$err_num = curl_errno($ch);
				$hcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				
				if ($hcode >= 400)
				{
					$invalid[] = $link;
				}
				else
				{
					$valid[] = $link;
				}
				
			}
			curl_close($ch);

			$this->load->module_model(FUEL_FOLDER, 'pages_model');
			$page_data = $this->pages_model->find_by_location($this->input->post('uri'), FALSE);
			
			$vars['invalid'] = $invalid;
			$vars['valid'] = $valid;
			$vars['total'] = count($formatted_links);
			$vars['link'] = $url;
			$vars['edit_url'] = (!empty($page_data['id'])) ? fuel_url('pages/edit/'.$page_data['id']) : '';
			$output = $this->load->view('links_output', $vars, TRUE);
			
			$this->output->set_output($output);
			return;
		} 
		else if (!$this->input->post('pages') AND !$this->input->post('pages_input') AND !$this->input->post('pages_serialized'))
		{
			$this->session->set_flashdata('error', lang('error_no_pages_selected'));
			redirect(fuel_uri('tools/validate'));
		}
		
		$this->js_controller_params['method'] = 'links';
		$pages = $this->_get_pages();
		$this->js_controller_params['pages'] = $pages;
		$vars['pages_serialized'] = base64_encode(serialize($pages));
		$vars['js_method'] = 'links';
		$vars['validation_type'] =  lang('validate_type_links');
		$this->_render('run', $vars);
	}

	function size_report()
	{
		//if (!$this->_has_module('fuel')) show_error(lang('error_missing_module'));
		
		if (!extension_loaded('curl')) show_error(lang('error_no_curl_lib'));
		$this->load->helper('number');
		
		$validate_config = $this->config->item('validate');
		
		if ($this->input->post('uri'))
		{
			$this->load->library('user_agent');
			
			// first we grab links using the DomDocument object
			
			// turn off the warnings for bad html
			$oldSetting = libxml_use_internal_errors(TRUE); 
			libxml_clear_errors(); 
			
			$url = $this->input->post('uri');
			
			$this->benchmark->mark('load_page_check_weight_start');
			
			
			//$url = $uri;
			$html = new DOMDocument(); 

			$html->loadHTMLFile($url);
			
			$this->benchmark->mark('load_page_check_weight_end');
			
			//echo 'LOAD PAGE: '.$this->benchmark->elapsed_time('load_page_check_weight_start', 'load_page_check_weight_end').'<br />';
			$xpath = new DOMXPath( $html );
			
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
			
			// change errors back to original settings
			libxml_clear_errors(); 
			libxml_use_internal_errors( $oldSetting ); 
			
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
			$config_limit = $validate_config['size_report_warn_limit'];
			$filesize_range = array('warn' => array(), 'ok' => array());
			$total_kb = 0;
			$ch = curl_init();
			
			$this->benchmark->mark('load_page_files_start');
			
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
				curl_setopt($ch, CURLOPT_USERAGENT, $this->agent->agent_string());
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
			$this->benchmark->mark('load_page_files_end');
			
			//echo 'FILE PAGE: '.$this->benchmark->elapsed_time('load_page_files_start', 'load_page_files_end').'<br />';
			
			// sort array in reverse order of size
			//arsort($output_arr);
			if (!empty($filesize_range['error'])) arsort($filesize_range['error']);
			if (!empty($filesize_range['warn'])) arsort($filesize_range['warn']);
			if (!empty($filesize_range['ok'])) arsort($filesize_range['ok']);

			$vars['invalid'] = $invalid;
			$vars['valid'] = $valid;
			$vars['total'] = count($resources);
			$vars['total_kb'] = $total_kb;
			$vars['link'] = $url;
		//	$vars['output_arr'] = $output_arr;
			$vars['config_limit'] = $config_limit;
			$vars['filesize_range'] = $filesize_range;
			
			$output = $this->load->view('size_report_output', $vars, TRUE);
			
			$this->output->set_output($output);
			return;
		} 
		else if (!$this->input->post('pages') AND !$this->input->post('pages_input') AND !$this->input->post('pages_serialized'))
		{
			$this->session->set_flashdata('error', lang('error_no_pages_selected'));
			redirect(fuel_uri('tools/validate'));
		}
		
		$this->js_controller_params['method'] = 'size';
		//$vars['js_method'] = 'size';
		$pages = $this->_get_pages();
		$this->js_controller_params['pages'] = $pages;
		
		$vars['pages_serialized'] = base64_encode(serialize($pages));
		
		$vars['validation_type'] = lang('validate_type_size_report');
		$this->_render('run', $vars);
	}
	
	function _get_pages()
	{
		$pages_input = $this->input->post('pages_input', TRUE);
		$extra_pages = array();
		if (!empty($pages_input) AND $pages_input != lang('validate_pages_input'))
		{
			$extra_pages = explode("\n", $pages_input);
			foreach($extra_pages as $key => $page)
			{
				$extra_pages[$key] = site_url(trim($page));
			}
		}
		$post_pages = (!empty($_POST['pages'])) ? $this->input->post('pages', TRUE) : array();
		$pages = array_merge($post_pages, $extra_pages);
		
		if (empty($pages) )
		{
			$pages = $this->input->post('pages_serialized');
			if (empty($pages))
			{
				redirect(fuel_uri('tools/validate'));
			}
			else
			{
				$pages = unserialize(base64_decode($pages));
			}
		}
		
		return $pages;
	}

}
/* End of file validate.php */
/* Location: ./fuel/modules/validate/controllers/validate.php */