<?php
/*
copyright Massimiliano Rossetti www.mrossetti.it ALL RIGHTS RESERVED
Version 0.1 ALPHA
for use send an email to xspasix@gmail.com
*/
class W3cValidator extends CurlObj {
	
	private $link;//link che va validato
	private $xml;//xml ritornato dalla verifica richiesta
		
	public function __construct() {//inizializzo le proprietà di default
		parent::__construct();
	}
	
	private function set_link($url) {
	    $this->link = 'http://validator.w3.org/check?url='.$url.'&output=soap12';
	}
	
	private function _validate($url, $mod = NULL) {
		$this->set_link($url);
		if($mod == 'fast') {
			$this->http_header($this->link, '');
		}elseif($mod == 'full') {
			$this->http_get_withheader($this->link, '');
		} else {//validazione predefinita
			$this->http_get($this->link, '');
		}
		if($this->curl_info['http_code'] == 200) {
			return true;
		}
		return false;
	}
	
	
	/*questi metodi ritornano un array con vari parametri a seconda del tipo richiesto*/
	
	
	/* ritorna un array come fast validate ma completo degli errori presenti nella risposta xml */ 
	public function validate($url) {
		if($this->_validate($url)) {
			return $this->xml_parsing($this->get_curl_output());
		}
		return false;
	}
	
	//ritorna un array con i contenuti dell'header fornito
	public function fast_validate($url) {
		if($this->_validate($url, 'fast')) {
		 	return $this->header_parsing($this->get_curl_output());
		}
		return false;
	}
	
	/**/
	public function full_validate($url) {
		$this->_validate($url, 'full');
	}
	
	
	private function header_parsing($string) {
		$tmp_arr = explode("\n", trim($string));
		if(count($tmp_arr)) {
			$result_arr = array();
			foreach($tmp_arr as $str_value) {
				if(strstr($str_value, 'X-W3C-Validator-Status:')) {
				$result_arr['status'] = strtolower(trim(str_replace('X-W3C-Validator-Status:', '', $str_value)));
				}
				if(strstr($str_value, 'X-W3C-Validator-Errors:')) {
				$result_arr['err_num'] = intval(trim(str_replace('X-W3C-Validator-Errors:', '', $str_value)));
				}
				if(strstr($str_value, 'X-W3C-Validator-Warnings:')) {
				$result_arr['warn_num'] = intval(trim(str_replace('X-W3C-Validator-Warnings:', '', $str_value)));
				}
			
			}
			unset($tmp_arr);
			return $result_arr;
		}
		return false;
	}
	
	
	private function xml_parsing($string) {
		
		$result_arr = array();
		$xml = new DomDocument();
		@$xml->loadXML($string);
		$xpath = new DOMXpath($xml);
		$xpath->registerNamespace("m", "http://www.w3.org/2005/10/markup-validator");
		
		
		$elements = $xpath->query("//m:validity");
		if($elements->item(0)->nodeValue == 'true') {
			$result_arr['status'] = 'valid';
		}else {
			$result_arr['status'] = 'invalid';
		}
		
		$elements = $xpath->query("//m:errorcount");
		$result_arr['err_num'] = intval($elements->item(0)->nodeValue);
		

		
		$result_arr['errors'] = array();
		$result_arr['warnings'] = array();
		
		if($elements->item(0) && $elements->item(0)->nodeValue > 0) {
			
		  $node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:line");
		  $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['errors'][$i]['line'] = intval($node->nodeValue);
			  $i++;
		  }	
			
		  $node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:col");
		  $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['errors'][$i]['col'] = intval($node->nodeValue);
			  $i++;
		  }	
			
		  $node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:message");
		  $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['errors'][$i]['message'] = $node->nodeValue;
			  $i++;
		  }
		  $node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:messageid");
		   $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['errors'][$i]['messageid'] = $node->nodeValue;
			  $i++;
		  }
		  $node_arr = $xpath->query("//m:errors/m:errorlist/m:error/m:explanation");
		   $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['errors'][$i]['explanation'] = trim($node->nodeValue);
			  $i++;
		  }
		}
		
		$elements = $xpath->query("//m:warningcount");
		$result_arr['warn_num'] = intval($elements->item(0)->nodeValue);
		
		if($elements->item(0) && $elements->item(0)->nodeValue > 0) {
		  $node_arr = $xpath->query("//m:warnings/m:warninglist/m:warning/m:messageid");
		  $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['warnings'][$i]['messageid'] = trim($node->nodeValue);
			  $i++;
		  }
		  $node_arr = $xpath->query("//m:warnings/m:warninglist/m:warning/m:message");
		  $i = 0;
		  foreach ($node_arr as $node) {
			  $result_arr['warnings'][$i]['message'] = trim($node->nodeValue);
			  $i++;
		  }
			
		}
		
		
		return $result_arr;
	}

	public function isValid($url) {//ritorna true o false
		$result = $this->fast_validate($url);
		if(is_array($result) && $result['status'] == 'valid') {
			return true;
		}
		return false;
		//http_code
	}
	
	public function return_xml_string($url) {
	
	}
	
	public function get_ErrNum() {//ritorna il numero degli errori presenti nella pagina
	}
}

?>