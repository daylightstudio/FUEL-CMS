<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Events_model extends Base_module_model {

	// read more about models in the user guide to get a list of all properties. Below is a subset of the most common:
	public $record_class = 'Event'; // the name of the record class (if it can't be determined)
	public $filters = array(); // filters to apply to when searching for items
	public $required = array('name');
	public $foreign_keys = array(); // map foreign keys to table models
	public $linked_fields = array(); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => arry('name' => 'strtolower')));
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array(); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array('description', 'description_formatted', 'excerpt', 'excerpt_formatted');
	public $serialized_fields = array(); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = FALSE;
	public $form_fields_class = '';  // a class that can extend Base_model_fields and manipulate the form_fields method
	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object

	public function __construct()
	{
		parent::__construct('events'); // table name
	}

	public function list_items($limit = null, $offset = null, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, start_date, end_date, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	public function on_before_clean($values)
	{
		$values = parent::on_before_clean($values);
		return $values;
	}
	
	public function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields();
		return $fields;
	}
	
	public function _common_query()
	{
		parent::_common_query();
		$this->db->order_by('start_date asc');
	}
	
}

class Event_model extends Base_module_record {
	
	public function get_start_date_formatted($format = 'F')
	{
		return date($format, strtotime($this->start_date));
	}

	public function get_date_range()
	{
		return date_range_string($this->start_date, $this->end_date);
	}
	
	public function get_image_path()
	{
		return img_path($this->image);
	}
}
?>