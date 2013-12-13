<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Careers_model extends Base_module_model {

	public $required = array('job_title');
	public $display_unpublished_if_logged_in = TRUE;
	
	function __construct()
	{
		parent::__construct('careers'); // table name
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'post_date', $order = 'desc')
	{
		$this->db->select('id, job_title, post_date, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function on_before_save($values)
	{
		if($values['post_date'] == '0000-00-00')
		{
			$values['post_date'] = datetime_now();
		}
		$values['skillset_requirements'] = strip_tags($values['skillset_requirements']);
		return $values;
	}
	
	function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		$fields['post_date']['comment'] = 'If blank, will default to current date/time'; 
		$fields['post_date']['value'] = datetime_now(); 
		$fields['skillset_requirements']['class'] = 'no_editor'; 
		return $fields;
	}
	
	function _common_query()
	{
		parent::_common_query();
		$this->db->order_by('post_date', 'desc');
	}
	
	
}

class Career_model extends Base_module_record {
	
	function get_skillset_requirements_formatted()
	{
		$this->_CI->load->helper('html');
		$lis = explode("\n", $this->skillset_requirements);
		$lis = array_map('trim', $lis);
		return ul($lis, array('class' => 'ul'));
	}

}