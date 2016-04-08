<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/Base_module_model.php');

class Careers_model extends Base_module_model {

	public $required = array('job_title');
	public $display_unpublished_if_logged_in = TRUE;
	
	public function __construct()
	{
		parent::__construct('careers'); // table name
	}

	public function list_items($limit = NULL, $offset = NULL, $col = 'publish_date', $order = 'desc', $just_count = FALSE)
	{
		$this->db->select('id, job_title, publish_date, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}
	
	public function on_before_save($values)
	{
		if($values['publish_date'] == '0000-00-00')
		{
			$values['publish_date'] = datetime_now();
		}
		return $values;
	}
	
	public function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		$fields['publish_date']['comment'] = 'If blank, will default to current date/time'; 
		$fields['publish_date']['value'] = datetime_now(); 
		return $fields;
	}
	
	public function _common_query($display_unpublished_if_logged_in = NULL)
	{
		parent::_common_query($display_unpublished_if_logged_in);
		$this->db->order_by('publish_date', 'desc');
	}
	
	
}

class Career_model extends Base_module_record {
	
	public function get_skillset_requirements_formatted()
	{
		$this->_CI->load->helper('html');
		$lis = explode("\n", $this->skills_needed);
		$lis = array_map('trim', $lis);
		return ul($lis, array('class' => 'ul'));
	}

}