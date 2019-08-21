<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');

abstract class Base_posts_model extends Base_module_model {

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
	
	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object

	// special field names
	public $order_by_field = 'publish_date'; // field to order by
	public $order_by_direction = 'desc'; // direction to order results
	public $slug_field = 'slug'; // field name for the single unique identifier ("slug")

	// base_posts_model specific properties
	public $name = ''; // this property is usually just the same as the table name but can be different and is used for image folders and tag and category contexts
	public $img_width = 200; // default image width dimensions
	public $img_height = 200; // default image height dimensions

	public function __construct($table = null)
	{
		if (empty($table)) $table = $this->name;
		parent::__construct($table); // table name
		if (empty($this->record_class)) $this->record_class = ucfirst($table).'_item';

		if (!empty($this->has_many['tags']))
		{
			$this->has_many['tags']['where'] = '(FIND_IN_SET("'.$this->name.'", '.$this->_tables['fuel_tags'].'.context) OR '.$this->_tables['fuel_tags'].'.context="")';
		}
		if (!empty($this->foreign_keys['category_id']) AND empty($this->foreign_keys['category_id']['where']))
		{
			$this->foreign_keys['category_id']['where'] = '(FIND_IN_SET("'.$this->name.'", '.$this->_tables['fuel_categories'].'.context) OR '.$this->_tables['fuel_categories'].'.context="")';
		}
	}

	public function list_items($limit = NULL, $offset = NULL, $col = NULL, $order = NULL, $just_count = FALSE)
	{
		if (empty($col))
		{
			$col = $this->order_by_field;
		}

		if (empty($order))
		{
			$order = $this->order_by_direction;
		}

		if (!empty($this->foreign_keys['category_id']))
		{
			$this->db->join('fuel_categories', 'fuel_categories.id = '.$this->table_name.'.category_id', 'LEFT');
		}

		//$this->db->select($this->table_name.'.id, title, fuel_categories.name as category, SUBSTRING(content, 1, 50) as content, '.$this->table_name.'.published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		// foreach($data as $key => $val)
		// {
		// 	$data[$key]['content'] = htmlentities($val['content']);
		// }
		return $data;
	}

	public function form_fields($values = array(), $related = array())
	{	
		$CI =& get_instance();
		$fields = parent::form_fields($values, $related);

		if (is_object($fields) && ($fields instanceof Base_model_fields))
		{
			$fields =& $fields->get_fields();
		}

		if (isset($fields['slug']))
		{
			$fields[$this->slug_field]['size'] = 100;	
		}

		if (isset($fields['title']))
		{
			$fields['title']['size'] = 100;	
		}
		
		if (isset($fields['pdf']))
		{
			$fields['pdf']['type'] = 'asset';
			$fields['pdf']['folder'] = 'pdf';
			$fields['pdf']['comment'] = 'If the PDF field is filled in AND both the content AND link field are empty, this will be the URL for the post (3rd link priority)';
		}

		if (isset($fields['link']))
		{
			$fields['link']['comment'] = 'If the link field is filled out and no content is entered, this will be the URL for the post (2nd link priority)';	
		}
		
		if (isset($fields['content']))
		{
			$fields['content']['comment'] = 'If the PDF field is filled in AND the link field is empty, this will be the URL for the post (1st link priority)';
			$fields['content']['img_folder'] = $this->name;
		}
		
		if (isset($fields['excerpt']))
		{
			$fields['excerpt']['img_folder'] = $this->name;
		}
		
		if (isset($fields['category_id']))
		{
			//$fields['category_id']['add_params'] = 'context='.$this->name;
			$fields['category_id']['type'] = 'toggler';
			$fields['category_id']['prefix'] = 'toggle_';
			$fields['category_id']['equalize_key_value'] = FALSE;
			$fields['category_id']['mode'] = 'select';
			$fields['category_id']['module'] = 'categories';
		}

		$possible_image_fields = array('image', 'main_image', 'list_image', 'thumbnail_image');
		foreach($possible_image_fields as $img_field)
		{
			if (isset($fields[$img_field]))
			{
				$fields[$img_field]['folder'] = 'images/'.$this->name;
			}
		}

		if (isset($fields['image']))
		{
			$fields['image']['hide_options'] = TRUE;
			$fields['image']['height'] = $this->img_height;
			$fields['image']['width'] = $this->img_width;
			$fields['image']['resize_method'] = 'resize_and_crop';
		}

		// add the context value automatically if creating a new tag
		// if (!empty($fields['tags']))
		// {
		// 	$fields['tags']['add_params'] = 'context='.$this->name;	
		// }
		
		return $fields;
	}
	
	public function find_by_month($limit = NULL, $where = array())
	{
		$order_by_field= $this->order_by_field;
		$items = $this->find_all_array($where, $order_by_field.' desc', $limit);
		
		$return = array();
		foreach($items as $item)
		{
			$key = date('F Y', strtotime($item->$order_by_field));
			if (!isset($return[$key]))
			{
				$return[$key] = array();
			}
			$return[$key][] = $item;
		}
		return $return;
	}

	public function on_before_save($values)
	{
		parent::on_before_save($values);
		if (empty($values[$this->order_by_field]))
		{
			$values[$this->order_by_field] = datetime_now();
		}
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
		$rel_join = $this->_tables['fuel_relationships'].'.candidate_key = '.$this->table_name.'.'.$this->key_field.' AND ';
		$rel_join .= $this->_tables['fuel_relationships'].'.candidate_table = "'.$this->table_name.'" AND ';
		$rel_join .= $this->_tables['fuel_relationships'].'.foreign_table = "'.$this->_tables['fuel_tags'].'"';
		$this->db->join($this->_tables['fuel_relationships'], $rel_join, 'left');
		$this->db->join($this->_tables['fuel_tags'], $this->_tables['fuel_tags'].'.id = '.$this->_tables['fuel_relationships'].'.foreign_key', 'left');
		$this->db->select($this->table_name.'.*');
		$this->db->select('YEAR('.$this->table_name.'.'.$this->order_by_field.') as year, DATE_FORMAT('.$this->table_name.'.'.$this->order_by_field.', "%m") as month, DATE_FORMAT('.$this->table_name.'.'.$this->order_by_field.', "%d") as day,', FALSE);
		$this->db->order_by($this->order_by_field.' '.$this->order_by_direction);
		$this->db->group_by($this->table_name.'.'.$this->key_field);

		if (!empty($this->foreign_keys['category_id']))
		{
			$this->db->join('fuel_categories', 'fuel_categories.id = '.$this->table_name.'.category_id', 'LEFT');
			$this->db->select($this->table_name.'.*, fuel_categories.slug as category_slug, fuel_categories.id as category_id', FALSE);
		}
	}

	public function preview_path($values, $path = NULL)
	{
		if ($values instanceof Data_record)
		{
			$values = $values->values();
		}
		$module = $this->get_module();

		$url = '';
		if (!empty($values[$this->key_field]))
		{
			$rec = $this->find_by_key($values[$this->key_field]);
			$url = $rec->url;
		}

		if (empty($url))
		{
			$url = $module->url($values);
		}
		return $url;
	}
}

class Base_post_item_model extends Base_module_record {

	public $day = NULL;
	public $month = NULL;
	public $year = NULL;

	public function get_excerpt($char_limit = NULL, $end_char = '&#8230;')
	{
		$this->_CI->load->helper('text');
		$excerpt = (empty($this->_fields['excerpt'])) ? $this->content : $this->_fields['excerpt'];

		if (!empty($char_limit))
		{
			// must strip tags to get accurate character count
			$excerpt = strip_tags($excerpt);
			$excerpt = character_limiter($excerpt, $char_limit, $end_char);
		}
		return $excerpt;
	}

	public function get_excerpt_formatted($char_limit = NULL, $readmore = '')
	{
		$excerpt = $this->get_excerpt($char_limit);
		if (!empty($readmore))
		{
			$excerpt .= ' '.anchor($this->url, $readmore, 'class="readmore"');
		}
		$excerpt = $this->_format($excerpt);
		$excerpt = $this->_parse($excerpt);
		return $excerpt;
	}
	
	public function is_future_post()
	{
		$order_by_field = $this->_parent_model->order_by_field;
		return strtotime($this->$order_by_field) > time();
	}

	public function get_url()
	{
		$url = '';
		if ($this->has_content())
		{
			$module = $this->_parent_model->get_module();
			$data = $this->values();
			$url = $module->url($data);
			$url = site_url($url);
		}
		elseif($this->has_link())
		{
			$url = site_url($this->link);
		}
		elseif($this->has_pdf())
		{
			$url = $this->pdf_path();
		}

		return $url;
	}

	public function get_categories_linked($order = 'name asc', $join = ', ')
	{
		$category = $this->category;
		if ( ! empty($category))
		{
			$url = $this->_fuel->posts->url('category/'.$category->slug);
			return anchor($url, $category->name);
		}
		return NULL;
	}
	
	public function get_tags_linked($order = 'name asc', $join = ', ')
	{
		$tags = $this->tags;
		if ( ! empty($tags))
		{
			$tags_linked = array();
			foreach ($tags as $tag)
			{
				$url = $this->_fuel->posts->url('tag/'.$tag->slug);
				$tags_linked[] = anchor($url, $tag->name);
			}
			$return = implode($tags_linked, $join);
			return $return;
		}
		return NULL;
	}

	public function get_image_path()
	{
		$CI =& get_instance();
		$path = $this->_parent_model->name.'/'.$this->image;
		return img_path($path);
	}
	
	public function get_link_title($attrs = array())
	{
		return anchor($this->url, $this->title, $attrs);
	}
	
	public function get_prev()
	{
		return $this->_CI->fuel->posts->prev_post($this);
	}

	public function get_prev_url()
	{
		$prev = $this->prev_post;
		if ($prev)
		{
			return $prev->url;	
		}
	}

	public function get_next()
	{
		return $this->_CI->fuel->posts->next_post($this);
	}

	public function get_next_url()
	{
		$next = $this->next_post;
		if ($next)
		{
			return $next->url;	
		}
	}

	protected function _format($content)
	{
		return auto_typography($content);
	}

}