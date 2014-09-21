<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {

	// read more about models in the user guide to get a list of all properties. Below is a subset of the most common:

	public $filters = array('content'); // filters to apply to when searching for items
	public $filter_join = 'or'; // how to combine the filters in the query (and or or)
	public $required = array('title'); // an array of required fields. If a key => val is provided, the key is name of the field and the value is the error message to display
	public $foreign_keys = array('author_id' => 'authors_model'); // map foreign keys to table models
	public $linked_fields = array(); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => arry('name' => 'strtolower')));
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array('slug'); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array(); // fields to automatically parse
	public $serialized_fields = array(); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model')); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = FALSE;
	
	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object


	function __construct()
	{
		parent::__construct('articles'); // table name
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc', $just_count = FALSE)
	{
		$this->db->join('authors', 'authors.id = articles.author_id', 'left');
		$this->db->select('articles.id, title, SUBSTRING(content, 1, 50) AS content, authors.name AS author, date_added, articles.published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		if (empty($just_count))
		{
			foreach($data as $key => $val)
			{
				$data[$key]['content'] = htmlentities($val['content'], ENT_QUOTES, 'UTF-8');
			}
		}
		return $data;
	}

	function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);

		// ******************* ADD CUSTOM FORM STUFF HERE ******************* 
		$fields['content']['img_folder'] = 'articles/';
		$fields['image']['folder'] = 'images/articles/';
		$fields['thumb_image']['folder'] = 'images/articles/thumbs/';

		return $fields;
	}

	function tree()
	{
		return $this->_tree('has_many');
	}
}

class Article_model extends Base_module_record {
	
	public function get_url()
	{
		return site_url('articles/'.$this->slug);
	}

	public function get_image_path()
	{
		return img_path('images/articles/'.$this->image);
	}

	public function get_thumb_image_path()
	{
		return img_path('images/articles/'.$this->thumb_image);
	}
}