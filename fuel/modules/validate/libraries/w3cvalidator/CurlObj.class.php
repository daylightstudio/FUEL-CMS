<?php
/*
copyright Massimiliano Rossetti www.mrossetti.it ALL RIGHTS RESERVED
Version 0.1 ALPHA
for use send an email to xspasix@gmail.com
*/
class CurlObj {
	
	protected $url; //link per la connessione
	protected $ref; //riferimento per il link
	protected $curl; //connessione curl
	protected $curl_error; //errori della connessione curl
	protected $curl_info; //informazioni sulla connessione
	protected $curl_output; //output html della connessione
	protected $curl_timeout; //tempo limite per la connessione
	protected $user_agent;//nome user agent usato per la connessione
	protected $cookie_file;//file cookie per la connessione
	protected $header_inc;//inclusione degli header assume true o false
	protected $query_string;//eventuale query string nella richiesta 
		
	public function __construct() {//inizializzo le proprietà di default
		/*$this->url = $url;*/
		$this->curl_timeout = 25;
		$this->user_agent = "Test Webbot";
		$this->cookie_file = 'cookie.txt';
		$this->ref = '';
		$this->query_string = '';
		$this->header_inc = false;
	}
	
	public function set_cookie_file($name) {
		$this->cookie_file = $name;
	}
		
	public function set_user_agent($string) {
		$this->user_agent = $string;
	}
	
	public function set_curl_timeout($sec) {
		$this->curl_timeout = intval($sec);
	}
	
	public function set_head_method() {
		$this->set_method("head");
	}
	
	public function set_get_method() {
		$this->set_method("get");
	}
	
	public function set_post_method() {
		$this->set_method("post");
	}
	
	protected function set_incl_header($bool) {
		$this->header_inc = ($bool) ? TRUE : FALSE;
	}
	
	protected function set_ref($ref = NULL) {
		$this->ref = (!empty($ref)) ? $ref : '';
	}
	
	protected function set_method($method) {
		# HEAD method configuration
		if($method == "head") {
			curl_setopt($this->curl, CURLOPT_HEADER, TRUE);                // No http head
			curl_setopt($this->curl, CURLOPT_NOBODY, TRUE);                // Return body
		}
		else {
			# GET method configuration
			if($method == "get") {
				if(!empty($this->query_string))
					$this->url = $this->url . "?" . $this->query_string;
				curl_setopt ($this->curl, CURLOPT_HTTPGET, TRUE); 
				curl_setopt ($this->curl, CURLOPT_POST, FALSE); 
			}
			# POST method configuration
			if($method == "post") {
				if(!empty($this->query_string))
					curl_setopt ($this->curl, CURLOPT_POSTFIELDS, $this->query_string);
				curl_setopt ($this->curl, CURLOPT_POST, TRUE); 
				curl_setopt ($this->curl, CURLOPT_HTTPGET, FALSE); 
			}
			curl_setopt($this->curl, CURLOPT_HEADER, $this->header_inc);   // Include head as needed
			curl_setopt($this->curl, CURLOPT_NOBODY, FALSE);        // Return body
			}
	}
	
	protected function set_query_string($data_arr) {
		# Prcess data, if presented
		if(is_array($data_arr)) {
			# Convert data array into a query string (ie animal=dog&sport=baseball)
			foreach ($data_arr as $key => $value) 
				{
				if(strlen(trim($value))>0)
					$temp_string[] = $key . "=" . urlencode($value);
				else
					$temp_string[] = $key;
				}
			$string = implode('&', $temp_string);
			$this->query_string = $string;
		}
	}
	
	
	protected function http_request($url) {
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookie_file);   // Cookie management.
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookie_file);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->curl_timeout);    // Timeout
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->user_agent);   // Webbot name
		curl_setopt($this->curl, CURLOPT_URL, $url);             // Target site
		curl_setopt($this->curl, CURLOPT_REFERER, $this->ref);            // Referer value
		curl_setopt($this->curl, CURLOPT_VERBOSE, FALSE);           // Minimize logs
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
		curl_setopt($this->curl, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
	
		$this->curl_output = curl_exec($this->curl); 
		$this->curl_info = curl_getinfo($this->curl);
		$this->curl_error  = curl_error($this->curl);
		
		curl_close($this->curl); # chiude la connessione Curl
	}
	
	public function http_get($url, $ref = NULL) {
		$this->curl = curl_init(); // Inizializza la connessione Curl
		$this->set_incl_header(false);
		$this->set_get_method();
		$this->set_ref($ref);
		$this->http_request($url);
    }
	
	function http_get_withheader($url, $ref) {
		$this->curl = curl_init(); // Inizializza la connessione Curl
		$this->set_incl_header(true);
		$this->set_get_method();
		$this->set_ref($ref);
		$this->http_request($url);
    }
	
	function http_get_form($url, $ref, $data_array) {
		$this->curl = curl_init(); // Inizializza la connessione Curl
		$this->set_incl_header(false);
		$this->set_query_string($data_array);
		$this->set_get_method();
		$this->set_ref($ref);
		$this->http_request($url);
	}
	
	function http_get_form_withheader($url, $ref, $data_array) {
		$this->curl = curl_init(); // Inizializza la connessione Curl
		$this->set_incl_header(true);
		$this->set_query_string($data_array);
		$this->set_post_method();
		$this->set_ref($ref);
		$this->http_request($url);
    }
	
	function http_post_form($url, $ref, $data_array) {
		$this->curl = curl_init(); // Inizializza la connessione Curl
		$this->set_incl_header(false);
		$this->set_query_string($data_array);
		$this->set_post_method();
		$this->set_ref($ref);
		$this->http_request($url);
    }

	function http_post_withheader($url, $ref, $data_array) {
			$this->curl = curl_init(); // Inizializza la connessione Curl
			$this->set_incl_header(true);
			$this->set_query_string($data_array);
			$this->set_post_method();
			$this->set_ref($ref);
			$this->http_request($url);
	}

	function http_header($url, $ref = NULL) {
			$this->curl = curl_init(); // Inizializza la connessione Curl
			$this->set_incl_header(true);
			$this->set_head_method();
			$this->set_ref($ref);
			$this->http_request($url);
	}
	
	public function get_curl_output() {
		return $this->curl_output;
	}
	
	public function get_curl_info() {
		return $this->curl_info;
	}
	
	public function get_curl_error() {
		return $this->curl_error;
	}

}

?>