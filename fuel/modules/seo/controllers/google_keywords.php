<?php
require_once(SEO_PATH.'libraries/Seo_base_controller.php');

class Google_keywords extends Seo_base_controller {
	
	var $nav_selected = 'tools/seo/google_keywords';
	
	function __construct()
	{
		parent::__construct();
	}

	
	function index()
	{
		$this->_validate_user('tools/seo/google_keywords');
		
		$this->js_controller_params['method'] = 'google_keywords';
		
		$seo_config = $this->config->item('seo');
		$vars = array();
		if (!empty($_POST['domain']))
		{
			$vars['domain'] = $this->input->post('domain');
		}
		else
		{
			$vars['domain'] = (!empty($seo_config['keyword_search_default_domain'])) ? $seo_config['keyword_search_default_domain'] : $_SERVER['SERVER_NAME'];
		}
		$vars['keywords'] = (!empty($_POST['keywords'])) ? $this->input->post('keywords') : $seo_config['keyword_google_default_keywords'];

		if (is_array($vars['keywords']))
		{
			$options = array();
			foreach($vars['keywords'] as $val) 
			{
				$options[$val] = $val;
			}
			$vars['keywords'] = $options;
		}
		$vars['num_results'] = $seo_config['keyword_google_num_results'];
		
		if (!empty($_POST))
		{
			$keywords = explode(',', $this->input->post('keywords'));
			$domain = str_replace(array('http://', 'www'), '', $this->input->post('domain'));
			$ch = curl_init();

			$found = array();
			foreach($keywords as $keyword)
			{
				$keyword = trim($keyword);

				$uri = 'http://www.google.com/search?q='.rawurlencode($keyword).'&num='.$seo_config['keyword_google_num_results'].'&'.http_build_query($seo_config['keyword_google_additional_params']);

				// scrape html from page running on localhost
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $uri);
				curl_setopt($ch, CURLOPT_HEADER, 0); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$google_page = curl_exec($ch);				
				preg_match_all('|<h3 class=(["\'])?r\\1?><a.+href="(.+)".+</h3>|Umis', $google_page, $matches);
				// echo "<pre style=\"text-align: left;\">";
				// print_r($matches);
				// echo "</pre>";
				// exit();
				$results = array();


				if (!empty($matches[2]))
				{
					$results = $matches[2];
				}

				$num = 1;
				foreach($results as $uri)
				{
					if (strpos($uri, $domain) !== FALSE)
					{
						if (!isset($found[$keyword]))
						{
							$found[$keyword] = array();
						}
						$found[$keyword][] = $num;
					}
					$num++;
				}
			}

			curl_close($ch); 
			$vars['results'] = $found;
			if (is_ajax())
			{
				$this->load->view('google_keywords_result', $vars);
				return false;
			}
		}

		$this->_render('google_keywords', $vars);
	}
}

/* End of file tools.php */
/* Location: ./modules/tools/controllers/google_keywords.php */