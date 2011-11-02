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

class Html_w3c_validator extends Fuel_base_library {	
	
	public $url; // link to check
		
	/**
	 * Constructor - Sets Fuel_backup preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct($params);

		if (!extension_loaded('curl')) 
		{
			$this->_add_error(lang('error_no_curl_lib'));
		}
		
		// initialize object if any parameters
		if (!empty($params))
		{
			$this->initialize($params);
		}
		
	}	
	function set_url($url)
	{
	    $this->url = 'http://validator.w3.org/check?url='.$url.'&output=soap12';
	}
	
	function validate($url, $full = TRUE)
	{
		$this->set_url($url);
		if (!$full)
		{
			$this->CI->fuel->scraper->curl($this->url);
		}
		else
		{
			$this->CI->fuel->scraper->curl($this->url);
		}

		// if($this->curl_info['http_code'] == 200)
		// {
		// 	return true;
		// }
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
	
	
	protected function xml_parsing($string) {
		
		$result_arr = array();
		$xml = new DomDocument();
		@$xml->loadXML($string);
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
		
		return $result_arr;
	}

	public function isValid($url)
	{
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