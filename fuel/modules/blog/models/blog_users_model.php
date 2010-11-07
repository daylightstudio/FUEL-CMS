<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Blog_users_model extends Base_module_model {

	public $required = array('username', 'email');
	public $filter_fields = array('about');
	protected $key_field = 'fuel_user_id';
	
	function __construct()
	{
		parent::__construct('fuel_blog_users', BLOG_FOLDER); // table name
		$this->add_validation('email', 'valid_email', 'Please enter in a valid email');
	}

	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('fuel_user_id, CONCAT(first_name, " ", last_name) as name, fuel_blog_users.active', FALSE);
		$this->db->join('fuel_users', 'fuel_users.id = fuel_blog_users.fuel_user_id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function options_list($key = 'fuel_user_id', $val = 'display_name', $where = array(), $order = 'display_name')
	{
		if ($key == 'id')
		{
			$key = $this->table_name.'.fuel_user_id';
		}
		if ($val == 'display_name')
		{
			$val = 'IF(display_name = "", fuel_users.email, display_name) AS name';
		}
		$this->db->join('fuel_users', 'fuel_users.id = fuel_blog_users.fuel_user_id', 'left');
		$return = parent::options_list($key, $val, $where, $order);
		return $return;
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'users_model');
		$CI->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$options = $CI->users_model->options_list();
		$upload_image_path = assets_server_path($CI->fuel_blog->settings('asset_upload_path'));
		$fields['fuel_user_id'] = array('label' => 'User', 'type' => 'select', 'options' => $options);

		// put all project images into a projects suboflder.
		$fields['avatar_image_upload']['upload_path'] = assets_server_path($CI->fuel_blog->settings('asset_upload_path'));

		// fix the preview by adding projects in front of the image path since we are saving it in a subfolder
		if (!empty($values['avatar_image']))
		{
			$fields['avatar_image_upload']['before_html'] = '<img src="'.assets_path($CI->fuel_blog->settings('asset_upload_path').$values['avatar_image']).'" style="float: right;"/>';
		}
		return $fields;
	}
	
	function _common_query()
	{
		$this->db->select('fuel_blog_users.*,  fuel_users.id, CONCAT(first_name, " ", last_name) as name, fuel_users.first_name, fuel_users.last_name, fuel_users.email, fuel_users.user_name, fuel_users.active', FALSE);
		$this->db->join('fuel_users', 'fuel_users.id = fuel_blog_users.fuel_user_id', 'left');
		$this->db->join('fuel_blog_posts', 'fuel_blog_posts.author_id = fuel_users.id', 'left'); // left or inner????
		$this->db->group_by('fuel_users.id');
	}

}

class Blog_user_model extends Base_module_record {
	
	public $id;
	public $first_name;
	public $last_name;
	public $name;
	public $email;
	public $user_name;
	public $active;
	protected $_parsed_fields = array('about');
	
	function get_url()
	{
		return $this->_CI->fuel_blog->url('authors/'.$this->id);
	}

	function get_posts()
	{
		return $this->lazy_load(array('author_id' => $this->id), array(BLOG_FOLDER => 'blog_posts_model'), TRUE);
	}

	function get_posts_url($full_path = TRUE)
	{
		$url = 'authors/posts/'.$this->id;
		if ($full_path)
		{
			return $this->_CI->fuel_blog->url($url);
		}
		return $url;
	}
	
	function get_avatar_image_path()
	{
		$this->_CI->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$image_path = $this->_CI->fuel_blog->settings('asset_upload_path');
		return assets_path($image_path.$this->avatar_image);
	}

	function get_avatar_img_tag($attrs = array())
	{
		$CI =& get_instance();
		$CI->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$CI->load->helper('html');
		$image_path = $CI->fuel_blog->settings('asset_upload_path');
		$src = assets_path($image_path.$this->avatar_image);
		$attrs = html_attrs($attrs);
		if (!empty($this->avatar_image))
		{
			return '<img src="'.$src.'"'.$attrs.' />';
		}
		return '';
	}
	
}