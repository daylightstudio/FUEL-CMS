<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Blog_posts_to_categories_model extends MY_Model {

	public $required = array();
	public $record_class = 'Blog_post_to_category';
	public $key_field = array('post_id', 'category_id');
	private $_tables = array();
	
	function __construct()
	{
		$CI =& get_instance();
		$CI->config->module_load(BLOG_FOLDER, BLOG_FOLDER);
		$this->_tables = $CI->config->item('tables');
		parent::__construct($this->_tables['blog_posts_to_categories']); // table name
	}

	function _common_query()
	{
		$this->db->select($this->_tables['blog_posts_to_categories'].'.*, '.$this->_tables['blog_posts'].'.title, '.$this->_tables['blog_posts'].'.permalink as post_permalink, '.$this->_tables['blog_categories'].'.name as category_name, '.$this->_tables['blog_categories'].'.permalink as category_permalink, 
		(SELECT COUNT(post_id) FROM '.$this->_tables['blog_posts_to_categories'].' WHERE '.$this->_tables['blog_posts_to_categories'].'.category_id = '.$this->_tables['blog_categories'].'.id GROUP BY category_id) AS posts_count', FALSE);
		$this->db->join($this->_tables['blog_posts'], $this->_tables['blog_posts_to_categories'].'.post_id = '.$this->_tables['blog_posts'].'.id', 'left');
		$this->db->join($this->_tables['blog_categories'], $this->_tables['blog_posts_to_categories'].'.category_id = '.$this->_tables['blog_categories'].'.id', 'left');
	}

}

class Blog_post_to_category_model extends Data_record {
	
	public $category_name = '';
	public $title = '';
	public $post_permalink = '';
	public $category_permalink = '';
	public $posts_count = 0;
	
	function get_category()
	{
		return $this->lazy_load('category_id', array(BLOG_FOLDER => 'blog_categories_model'));
	}
	
	function get_post()
	{
		return $this->lazy_load('post_id', array(BLOG_FOLDER => 'blog_posts_model'));
	}
	
	function get_category_url()
	{
		return $this->_CI->fuel_blog->url('categories/'.$this->category_permalink);
	}

	function get_post_url()
	{
		return $this->_CI->fuel_blog->url('posts/article/'.$this->post_permalink);
	}
}
?>