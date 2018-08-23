<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_posts_model.php');

class News_model extends Base_posts_model {

	//read more about models in the user guide to get a list of all properties. Below is a subset of the most common:
	public $filters = array('title', 'content'); // filters to apply to when searching for items
	public $required = array('title', 'content'); // an array of required fields. If a key => val is provided, the key is name of the field and the value is the error message to display
	public $foreign_keys = array('category_id' => array(FUEL_FOLDER => 'fuel_categories_model')); // map foreign keys to table models
	public $linked_fields = array(); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => array('name' => 'strtolower')));
	public $boolean_fields = array('featured'); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array('slug'); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array('content', 'content_formatted', 'excerpt', 'excerpt_formatted'); // fields to automatically parse
	public $serialized_fields = array(); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array(
		'tags' => array(
			'model' => array(FUEL_FOLDER => 'fuel_tags_model'),
			), // added in constructor so that the the table name isn't hard coded in query
		); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = TRUE;
	
	// can extend Base_model_fields with your own class to manipulate the form fields
	//public $form_fields_class = 'Base_model_fields';
	public $form_fields_class = '';

	// special field names
	public $publish_date_field = 'publish_date'; // field name for the publish date
	public $order_by_field = 'publish_date'; // field to order by
	public $order_by_direction = 'desc'; // direction to order results

	// base_posts_model specific properties
	public $name = 'news'; // this property is usually just the same as the table name but can be different and is used for image folders and tag and category contexts
	public $img_width = 200; // default image width dimensions
	public $img_height = 200; // default image height dimensions

	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object
	
	public function __construct()
	{
		parent::__construct('news'); // table name
	}

	public function list_items($limit = null, $offset = null, $col = 'name', $order = 'asc', $just_count = FALSE)
	{
		$this->db->select('news.id, news.title, news.publish_date, news.published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
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
		$fields['image']['folder'] = 'images/news';  // requires images/news folder to be created
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
	}
}

class News_item_model extends Base_post_item_model {
	
	public function get_image_path()
	{
		return img_path('news/'.$this->image);
	}
	
}