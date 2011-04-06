<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Blog_categories_model extends Base_module_model {

	public $required = array('name');
	public $record_class = 'Blog_category';
	public $unique_fields = array('permalink', 'name');
	public $linked_fields = array('permalink' => array('name' => 'url_title'));
	
	function __construct()
	{
		parent::__construct('blog_categories', BLOG_FOLDER); // table name
	}

	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->where(array('id !=' => 1)); // Uncategorized category
		$this->db->select('id, name, published');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function on_before_clean($values)
	{
		if (empty($values['permalink']) && !empty($values['name'])) $values['permalink'] = url_title($values['name'], 'dash', TRUE);
		return $values;
	}
	
	// check if it is the "Uncategorized" category so we don't delete it'
	function on_before_delete($where)
	{
		$CI =& get_instance();
		
		$CI->load->module_model('blog', 'blog_posts_to_categories_model');
		$CI->load->module_language('blog', 'blog');
		if (is_array($where) && isset($where['id']))
		{
			if ($where['id'] == 1)
			{
				$this->add_error(lang('blog_error_delete_uncategorized'));
				$CI->session->set_flashdata('error', lang('blog_error_delete_uncategorized'));
				return;
			}
		}
	}
	
	
	// cleanup category to posts
	function on_after_delete($where)
	{
		$CI =& get_instance();
		$CI->load->module_model('blog', 'blog_posts_to_categories_model');
		if (is_array($where) && isset($where['id']))
		{
			$where = array('category_id' => $where['id']);
			$CI->blog_posts_to_categories_model->delete($where);
		}
	}

	function form_fields()
	{
		$fields = parent::form_fields();
		$CI =& get_instance();
		return $fields;
	}
	
	function _common_query()
	{
	}

}

class Blog_category_model extends Base_module_record {
	
	private $_tables;
	
	function on_init()
	{
		$this->_tables = $this->_CI->config->item('tables');
	}

	function get_posts()
	{
		$this->_CI->load->module_model('blog', 'blog_posts_to_categories_model');
		$where = array('category_id' => $this->id, $this->_tables['blog_posts'].'.published' => 'yes');
		$posts = $this->_CI->blog_posts_to_categories_model->find_all($where);
		return $posts;
	}

	function get_posts_count()
	{
		$this->_CI->load->module_model('blog', 'blog_posts_to_categories_model');
		$where = array('category_id' => $this->id, $this->_tables['blog_posts'].'.published' => 'yes');
		$cnt = $this->_CI->blog_posts_to_categories_model->record_count($where);
		return $cnt;
	}
	
	function get_url($full_path = TRUE)
	{
		$url = 'categories/'.$this->permalink;
		if ($full_path)
		{
			return $this->_CI->fuel_blog->url($url);
		}
		return $url;
	}
	
}
?>