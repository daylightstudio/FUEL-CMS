<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Test_custom_records_model extends MY_Model {

	public $required = array('email');
	
	function __construct()
	{
		parent::__construct('users');
	}
	
	function _common_query()
	{
		$this->db->select('users.*, CONCAT(first_name, " ", last_name) as full_name', FALSE);
	}
}


class Test_custom_record_model extends Data_record {

	public $full_name;
	
	function get_full_name($title = '')
	{
		$full_name = $this->first_name.' '.$this->last_name;
		if (!empty($title)) $full_name = $title.' '.$full_name;
		return $full_name;
	}
	
}
