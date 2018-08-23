<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');

class {Model_name}_model extends Base_module_model {

	// read more about models in the user guide to get a list of all properties. Below is a subset of the most common:

	public $record_class = '{Model_record}'; // the name of the record class (if it can't be determined)
	public $filters = array(); // filters to apply to when searching for items
	public $required = array(); // an array of required fields. If a key => val is provided, the key is name of the field and the value is the error message to display
	public $foreign_keys = array(); // map foreign keys to table models
	public $linked_fields = array(); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => array('name' => 'strtolower')));
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array(); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array(); // fields to automatically parse
	public $serialized_fields = array(); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = FALSE;
	public $form_fields_class = '';  // a class that can extend Base_model_fields and manipulate the form_fields method
	public $validation_class = ''; // a class that can extend Base_model_validation and manipulate the validate method by adding additional validation to the model
	public $related_items_class = ''; // a class that can extend Base_model_related_items and manipulate what is displayed in the related items area (right side of page)
	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object


	public function __construct()
	{
		parent::__construct('{table}'); // table name
	}

	public function list_items($limit = NULL, $offset = NULL, $col = 'precedence', $order = 'asc', $just_count = FALSE)
	{
		// $this->db->select('{table}.id, {table}.title, {table}.published....'); uncomment and  change to the fields to be displayed
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);

		if (!$just_count)
		{
			foreach($data as $key => $val)
			{
				// format with PHP instead of MySQL so that ordering will still work with MySQL
				if (!empty($val['publish_date']))
				{
					$data[$key]['publish_date'] = date_formatter($val['publish_date'], 'm/d/Y h:ia');	
				}
			}
		}
		return $data;
	}

	public function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		return $fields;
	}
	
	public function on_before_save($values)
	{
		$values = parent::on_before_save($values);
		return $values;
	}

	public function on_after_save($values)
	{
		parent::on_after_save($values);
		return $values;
	}

	public function _common_query($display_unpublished_if_logged_in = NULL)
	{
		parent::_common_query($display_unpublished_if_logged_in);

		// remove if no precedence column is provided
		$this->db->order_by('precedence asc');
	}

}

class {Model_record}_model extends Base_module_record {
	
	// put your record model code here
}