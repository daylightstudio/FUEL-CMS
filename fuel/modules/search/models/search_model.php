<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(FUEL_PATH.'models/base_module_model.php');

class Search_model extends Base_module_model {
	
	public $required = array('location', 'scope', 'title', 'content');
	public $record_class = 'Search_item';

	function __construct()
	{
		parent::__construct('search', SEARCH_FOLDER);
		
	}
	function find_by_keyword($q, $limit = NULL, $offset = NULL, $excerpt_limit = 200)
	{
		$CI =& get_instance();
		$full_text_fields = array('title', 'content');
		$full_text_indexed = implode($full_text_fields, ', ');
	
		$q = trim($q); // trim the right and left from whitespace
		$q = preg_replace("#([[:space:]]{2,})#",' ',$q); // remove multiple spaces
		$q_len = strlen($q);
		$q = $this->db->escape($q);
		if ($q_len >= 4 AND strtolower($CI->fuel->search->config('query_type')) == 'match')
		{
			$this->db->where('MATCH('.$full_text_indexed.') AGAINST ('.$q.')');
			$this->db->select('match ('.$full_text_indexed.') against ('.$q.')  AS relevance ', FALSE);
			$this->db->order_by('relevance desc');
		}
		else
		{
			$words = explode(' ', $q);
			foreach($words as $w)
			{
				foreach($full_text_fields as $field)
				{
					$this->db->or_where('LOWER('.$field.') LIKE "%'.$q.'%"');
				}
			}

			$this->db->order_by('date_added desc');
		}
		
		$this->db->select($this->_tables['search'].'.location');
		$this->db->select($this->_tables['search'].'.title');
		$this->db->select($this->_tables['search'].'.date_added');
		$this->db->select('SUBSTRING('.$this->_tables['search'].'.content, 1, '.$excerpt_limit.') AS content_excerpt', FALSE);
		$this->db->limit($limit);
		
		if (!empty($offset))
		{
			$this->db->offset($offset);
		}
		$results = $this->find_all();
		//$this->debug_query();
		return $results;
	}
	
	function find_by_location($location)
	{
		return $this->find_one(array('location' => $location));
	}
	
	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$fields['content']['class'] = 'no_editor';
		return $fields;
	}
	
	
	
}

class Search_item_model extends Base_module_record {
	
	public $content_excerpt = '';
	
	function get_url()
	{
		return site_url($this->location);
	}
	
}
