<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class News_model extends Base_module_model {

	public $required = array('title');
	public $record_class = 'News_item';
	public $parsed_fields = array('content', 'content_formatted');
	
	function __construct()
	{
		parent::__construct('news'); // table name
	}

	function list_items($limit = null, $offset = null, $col = 'title', $order = 'asc')
	{
		$this->db->select('id, title, slug, DATE_FORMAT(date_added,"%m/%d/%Y") as date_added, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function find_by_month($limit = NULL, $where = array())
	{
		$items = $this->find_all($where, 'date_added desc', $limit);
		
		$return = array();
		foreach($items as $item)
		{
			$key = date('F Y', strtotime($item->date_added));
			if (!isset($return[$key]))
			{
				$return[$key] = array();
			}
			$return[$key][] = $item;
		}
		return $return;
	}
	
	function on_before_clean($values)
	{
		if (empty($values['slug']))
		{
			$values['slug'] = url_title($values['title'], 'dash', TRUE);
		}
		return $values;
	}

	function _common_query()
	{
		parent::_common_query();
		$this->db->order_by('date_added desc');
	}
	
	function form_fields()
	{
		$fields = parent::form_fields();
		$fields['date_added']['type'] = 'datetime';
		return $fields;
	}
}

class News_item_model extends Base_module_record {
	
	function get_date_added_formatted()
	{
		return date('m.d.y', strtotime($this->date_added));
	}
	
	function get_url()
	{
		if (!empty($this->link)) return prep_url($this->link);
		return site_url('company/news/news-article/'.$this->slug);
	}
	
	function get_excerpt_formatted($char_limit = NULL, $readmore = '')
	{
		$this->_CI->load->helper('typography');
		$this->_CI->load->helper('text');
		
		$excerpt = $this->content;

		if (!empty($char_limit))
		{
			// must strip tags to get accruate character count
			$excerpt = strip_tags($excerpt);
			$excerpt = character_limiter($excerpt, $char_limit);
		}
		$excerpt = auto_typography($excerpt);
		$excerpt = $this->_parse($excerpt);
		if (!empty($readmore))
		{
			$attrs = array('class' => 'readmore');
			if ($this->type == 'news') $attrs['target'] = '_blank';
			$excerpt .= ' '.anchor($this->get_url(), $readmore, $attrs);
		}
		return $excerpt;
	}
	
}