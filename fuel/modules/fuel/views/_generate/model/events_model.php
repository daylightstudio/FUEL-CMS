<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Events_model extends Base_module_model {

	public $required = array('name');
	public $parsed_fields = array('description', 'description_formatted', 'excerpt', 'excerpt_formatted');
	
	function __construct()
	{
		parent::__construct('events'); // table name
	}

	function list_items($limit = null, $offset = null, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, start_date, end_date, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function on_before_clean($values)
	{
		if (empty($value['slug']))
		{
			$values['slug'] = url_title($values['name'], 'dash', TRUE);
		}
		return $values;
	}
	
	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields();
		return $fields;
	}
	
	function _common_query()
	{
		parent::_common_query();
		$this->db->order_by('start_date asc');
	}
	
}

class Event_model extends Base_module_record {
	
	function get_start_date_formatted($format = 'F')
	{
		return date($format, strtotime($this->start_date));
	}
	

	function get_date_range()
	{
		return date_range_string($this->start_date, $this->end_date);
	}
	
	function get_image_path()
	{
		return img_path($this->image);
	}
}
?>