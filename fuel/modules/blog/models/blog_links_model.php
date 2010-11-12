<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Blog_links_model extends Base_module_model {

	public $required = array('url');
	
	function __construct()
	{
		parent::__construct('fuel_blog_links', BLOG_FOLDER); // table name
	}

	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, url, published');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function form_fields()
	{
		$fields = parent::form_fields();
		$fields['url']['label'] = 'URL';
		return $fields;
	}
	
	function _common_query()
	{
	}

}

class Blog_link_model extends Base_module_record {
	
	function get_link()
	{
		$url = $this->url;
		if (!is_http_path($url))
		{
			$url = 'http://'.$url;
		}
		$label = (!empty($this->name)) ? $this->name : $this->url;
		$attrs = (!empty($this->target)) ? 'target="_'.$this->target.'"' : '';
		return anchor($url, $label, $attrs);
	}
}
?>