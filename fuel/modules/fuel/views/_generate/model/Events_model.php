<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_posts_model.php');

class Events_model extends Base_posts_model {

	// read more about models in the user guide to get a list of all properties. Below is a subset of the most common:
	public $record_class = 'Event'; // the name of the record class (if it can't be determined)
	public $filters = array(); // filters to apply to when searching for items
	public $required = array('name');
	public $foreign_keys = array(); // map foreign keys to table models
	public $linked_fields = array(); // fields that are linked meaning one value helps to determine another. Key is the field, value is a function name to transform it. (e.g. array('slug' => 'title'), or array('slug' => array('name' => 'strtolower')));
	public $boolean_fields = array(); // fields that are tinyint and should be treated as boolean
	public $unique_fields = array(); // fields that are not IDs but are unique. Can also be an array of arrays for compound keys
	public $parsed_fields = array('description', 'description_formatted');
	public $serialized_fields = array(); // fields that contain serialized data. This will automatically serialize before saving and unserialize data upon retrieving
	public $has_many = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $belongs_to = array(); // keys are model, which can be a key value pair with the key being the module and the value being the model, module (if not specified in model parameter), relationships_model, foreign_key, candidate_key
	public $formatters = array(); // an array of helper formatter functions related to a specific field type (e.g. string, datetime, number), or name (e.g. title, content) that can augment field results
	public $display_unpublished_if_logged_in = FALSE;
	public $form_fields_class = '';  // a class that can extend Base_model_fields and manipulate the form_fields method
	protected $friendly_name = ''; // a friendlier name of the group of objects
	protected $singular_name = ''; // a friendly singular name of the object

	public $order_by_field = 'start_date'; // field to order by
	public $order_by_direction = 'asc'; // direction to order results
	public $name = 'events';
	public $img_width = 200; // default image width dimensions
	public $img_height = 200; // default image height dimensions

	public function __construct()
	{
		parent::__construct('events'); // table name
	}

	public function list_items($limit = null, $offset = null, $col = 'start_date', $order = 'desc', $just_count = FALSE)
	{
		$this->db->select('events.id, events.name, start_date, end_date, events.published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);

		if (!$just_count)
		{
			foreach($data as $key => $val)
			{
				$data[$key]['start_date'] = date_formatter($val['start_date'], 'm/d/Y g:ia');
				$data[$key]['end_date'] = date_formatter($val['start_date'], 'm/d/Y g:ia');
			}
		}
		return $data;
	}

	public function related_items($params = array())
	{
		if (!empty($params['id']))
		{
			$this->record = $this->find_by_key($params['id']);
			$map_link = $this->record->get_map_link();
			$str = '';
			if (!empty($map_link))
			{
				$str = '<br><a href="'.$map_link.'" target="_blank"><img src="'.$this->record->get_map_image().'" alt="" style="display: block; width: 100%; margin: auto;"/></a>';
				return $str;
			}
			return $str;
		}
		return array();
	}

	public function find_upcoming($limit = NULL)
	{
		return $this->find_all(array('start_date >=' => datetime_now()), NULL, $limit);
	}
	
	public function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$fields['image']['folder'] = 'images/events'; // requires images/events folder to be created
		unset($fields['link']); // comment out if you want a Link field
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

class Event_model extends Base_post_item_model {
	
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
		return img_path('events/'.$this->image);
	}

	public function get_google_map_url($params = array())
	{
		// normalize map params
		$params = $this->_map_params($params);
		//https://maps.google.com/maps?q=37.771008,+-122.41175+(You+can+insert+your+text+here)&amp;hl=en&amp;t=v&amp;vpsrc=0&amp;ie=UTF8&amp;z=14&amp;iwloc=A&amp;ll=38.287602,-122.036186&amp;output=embed
		$url = 'https://maps.google.com/maps?q=';
		$url .= $this->location;
		$url .= '&amp;hl=en&amp;ie=UTF8&amp;vpsrc=0&amp;iwloc=&amp;';
		$url .= '&amp;t='.$this->_google_map_type($params['map_type']);
		$url .= '&amp;z='.$params['zoom'];
		$url .= '&amp;om='.$params['overview'];
		$url .= '&amp;output=embed';
		return $url;
	}
	
	protected function _map_params($params)
	{
		// if a query string is passed, then we parse it into an array form
		if (is_string($params))
		{
			parse_str($params, $params);
		}
		
		// defaults
		$return = array();
		$return['zoom'] = 14;
		$return['startpoint'] = NULL;
		$return['map_type'] = 'roadmap';
		$return['overview'] = TRUE;
		
		// merge params with defaults
		foreach($return as $r)
		{
			if (isset($params[$r]))
			{
				$return[$r] = $params[$r];
			}
		}
		return $return;
	}
	
	protected function _google_map_type($type)
	{
		// "k" satellite, "h" hybrid, "p" terrain
		switch($type)
		{
			case 'satellite':
					return 'k';
				break;
			case 'hybrid':
					return 'h';
				break;
			case 'terrain':
					return 'p';
				break;
			default:
				return 'v';
		}
	}
	
	public function get_map_link()
	{
		if ($this->has_location())
		{
			$gmap = 'https://maps.google.com/maps?q='.$this->location;
			return $gmap;
		}
		return FALSE;
	}

	public function get_map_image($width = 190, $height = 140, $type = 'roadmap', $zoom = '13')
	{
		if ($this->has_location())
		{
			//https://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&sensor=true
			$gmap = 'https://maps.googleapis.com/maps/api/staticmap?center='.$this->location.'&amp;size='.$width.'x'.$height.'&amp;maptype='.$type.'&amp;zoom='.$zoom.'&amp;markers='.$this->location.'&amp;sensor=false';
			return $gmap;
		}
		return FALSE;
	}
}