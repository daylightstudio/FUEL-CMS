<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
require_once(MODULES_PATH.'/blog/config/blog_constants.php');

class Blog_posts_model extends Base_module_model {

	public $category = null;
	public $required = array('title', 'content');
	public $hidden_fields = array('content_filtered');
	public $filters = array('title', 'content_filtered', 'fuel_users.first_name', 'fuel_users.last_name');
	public $unique_fields = array('permalink');
	public $linked_fields = array('permalink' => array('title' => 'url_title'));

	function __construct()
	{
		parent::__construct('blog_posts', BLOG_FOLDER); // table name
	}
	
	// used for the FUEL admin
	function list_items($limit = NULL, $offset = NULL, $col = 'date_added', $order = 'desc')
	{
		// set the filter again here just in case the table names are different
		$this->filters = array('title', 'content_filtered', $this->_tables['users'].'.first_name', $this->_tables['users'].'.last_name');
		
		$this->db->select($this->_tables['blog_posts'].'.id, title, CONCAT('.$this->_tables['users'].'.first_name, " ", '.$this->_tables['users'].'.last_name) AS author, '.$this->_tables['blog_posts'].'.date_added, '.$this->_tables['blog_posts'].'.published', FALSE);
		$this->db->join($this->_tables['users'], $this->_tables['users'].'.id = '.$this->_tables['blog_posts'].'.author_id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order);
		foreach($data as $key => $val)
		{
			$data[$key]['date_added'] = english_date($data[$key]['date_added'], TRUE);
		}
		return $data;
	}
	
	function tree($just_published = FALSE)
	{
		$CI =& get_instance();
		$CI->load->module_model(BLOG_FOLDER, 'blog_categories_model');
		$CI->load->module_model(BLOG_FOLDER, 'blog_posts_to_categories_model');
		$CI->load->helper('array');

		$return = array();
		
		$where = ($just_published) ? $where = array('published' => 'yes') : array();
		$categories = $CI->blog_categories_model->find_all($where, 'id asc');
		$posts_to_categories = $CI->blog_posts_to_categories_model->find_all($where, 'title asc');
		if (empty($posts_to_categories)) return array();
		
		foreach($categories as $category)
		{
			$return[$category->id] = array('id' => $category->id, 'parent_id' => 0, 'label' => $category->name, 'location' => fuel_url('blog/categories/edit/'.$category->id));
		}
		
		foreach($posts_to_categories as $val)
		{
			$attributes = ($val->published == 'no') ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
			$return['p_'.$val->post_id] = array('label' => $val->title, 'parent_id' => $val->category_id, 'location' => fuel_url('blog/posts/edit/'.$val->post_id), 'attributes' => $attributes);
		}

		$return = array_sorter($return, 'parent_id', 'asc');
		return $return;
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		$CI =& get_instance();
		$CI->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$CI->load->module_model(BLOG_FOLDER, 'blog_users_model');
		$CI->load->module_model(BLOG_FOLDER, 'blog_categories_model');
		$CI->load->module_model(BLOG_FOLDER, 'blog_posts_to_categories_model');
		$blog_config = $CI->config->item('blog');
		
		$category_options = $CI->blog_categories_model->options_list('id', 'name', array('published' => 'yes'), 'name');
		$category_values = (!empty($values['id'])) ? array_keys($CI->blog_posts_to_categories_model->find_all_array_assoc('category_id', array('post_id' => $values['id'], $this->_tables['blog_categories'].'.published' => 'yes'))) : array();
		
		$fields['categories'] = array('label' => 'Categories', 'type' => 'array', 'options' => $category_options, 'class' => 'add_edit blog/categories combo', 'value' => $category_values, 'mode' => 'multi');
		
		$user_options = $CI->blog_users_model->options_list();
		$user = $this->fuel_auth->user_data();
		
		$user_value = (!empty($values['author_id'])) ? $values['author_id'] : $user['id'];
		$author_comment = $fields['author_id']['comment'];
		$fields['author_id'] = array('label' => 'Author', 'type' => 'select', 'options' => $user_options, 'first_option' => 'Select an author...', 'value' => $user_value, 'comment' => $author_comment);
		if (!isset($values['allow_comments']))
		{
			$fields['allow_comments']['value'] = ($CI->fuel_blog->settings('allow_comments')) ? 'yes' : 'no';
		} 
		
		if (!empty($blog_config['formatting']) )
		{
			$blog_config['formatting'] = (array) $blog_config['formatting'];
			if (count($blog_config['formatting']) == 1)
			{
				$fields['formatting'] = array('type' => 'hidden', 'options' => current($blog_config['formatting']), 'default' => $fields['formatting']['default']);
			}
			else
			{
				$fields['formatting'] = array('type' => 'select', 'options' => $blog_config['formatting'], 'default' => $fields['formatting']['default']);
			}
		}
		
		$fields['published']['order'] = 10000;
		
		if (!is_true_val($CI->fuel_blog->settings('allow_comments')))
		{
			unset($fields['allow_comments']);
		}
		
		$fields['upload_images'] = array('type' => 'file', 'class' => 'multifile', 'order' => 6, 'upload_path' => assets_server_path($CI->fuel_blog->settings('asset_upload_path')), 'comment' => 'Upload images to be used with your blog posts');
		
		
		unset($fields['content_filtered']);
		//$fields['date_added']['type'] = 'hidden'; // so it will auto add
		$fields['date_added']['type'] = 'datetime'; // so it will auto add
		$fields['last_modified']['type'] = 'hidden'; // so it will auto add
		$fields['permalink']['order'] = 2; // for older versions where the schema order was different
		
		// Check if a date added value has been selected
		if( ! isset($fields['date_added']['value']))
		{
			// Set a default date
			$fields['date_added']['value'] = date('m/d/Y h:i:s a', time());
		}
		
		return $fields;
	}
	
	function on_before_clean($values)
	{
		$values['permalink'] = (empty($values['permalink']) && !empty($values['title'])) ? url_title($values['title'], 'dash', TRUE) : url_title($values['permalink'], 'dash');

		// create author if it doesn't exists'
		$CI =& get_instance();
		$id = (!empty($values['author_id'])) ? $values['author_id'] : $CI->fuel_auth->user_data('id');
		$CI->load->module_model(BLOG_FOLDER, 'blog_users_model');
		$author = $CI->blog_users_model->find_by_key($id);
		if (!isset($author->id))
		{
			$author = $CI->blog_users_model->create();
			$author->fuel_user_id = $CI->fuel_auth->user_data('id');

			// determine a display name if one isn't provided'
			if (trim($author->display_name) == '')
			{
				$display_name = $CI->fuel_auth->user_data('first_name').' '.$this->fuel_auth->user_data('last_name');
				if (trim($display_name) == '') $display_name = $CI->fuel_auth->user_data('email');
				if (empty($display_name)) $display_name = $CI->fuel_auth->user_data('user_name');
				$author->display_name = $display_name;
			}

			// save author
			$author->save();
			$values['author_id'] = $author->fuel_user_id;
		}

		return $values;
	}
	
	function on_before_save($values)
	{
		$values['title'] = strip_tags($values['title']);
		$values['content_filtered'] = strip_tags($values['content']);
		return $values;
	}
	
	function on_after_save($values)
	{
		$CI =& get_instance();
		$CI->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$CI->load->module_model(BLOG_FOLDER, 'blog_posts_to_categories_model');

		// remove cache
		$CI->fuel_blog->remove_cache();

		$saved_data = $this->normalized_save_data;
		
		$post_id = $values['id'];
		$categories = (!empty($saved_data['categories'])) ? $saved_data['categories'] : array(1); // 1 = Uncategorized

		// first remove all the categories
		$CI->blog_posts_to_categories_model->delete(array('post_id' => $post_id));
		
		// then readd them
		foreach($categories as $val)
		{
			$post_category = $CI->blog_posts_to_categories_model->create();
			$post_category->post_id = $post_id;
			$post_category->category_id = $val;
			$post_category->save();
		}
	}

	// cleanup posts to categories
	function on_after_delete($where)
	{
		$CI =& get_instance();
		$CI->load->module_model('blog', 'blog_posts_to_categories_model');
		if (is_array($where) && isset($where['id']))
		{
			$where = array('post_id' => $where['id']);
			$CI->blog_posts_to_categories_model->delete($where);
		}
	}


	function _common_query()
	{
		$this->db->select($this->_tables['blog_posts'].'.*, '.$this->_tables['blog_users'].'.display_name, CONCAT('.$this->_tables['users'].'.first_name, " ", '.$this->_tables['users'].'.last_name) as author_name', FALSE);
		$this->db->join($this->_tables['blog_posts_to_categories'], $this->_tables['blog_posts_to_categories'].'.post_id = '.$this->_tables['blog_posts'].'.id', 'left');
		$this->db->join($this->_tables['blog_users'], $this->_tables['blog_users'].'.fuel_user_id = '.$this->_tables['blog_posts'].'.author_id', 'left');
		$this->db->join($this->_tables['blog_categories'], $this->_tables['blog_categories'].'.id = '.$this->_tables['blog_posts_to_categories'].'.category_id', 'left');
		$this->db->join($this->_tables['users'], $this->_tables['users'].'.id = '.$this->_tables['blog_posts'].'.author_id', 'left');
		$this->db->group_by($this->_tables['blog_posts'].'.id');
	}

}

class Blog_post_model extends Base_module_record {

	private $_tables;
	public $author_name;
	
	function on_init()
	{
		$this->_tables = $this->_CI->config->item('tables');
	}
	
	function get_content_formatted($strip_images = FALSE)
	{
		$this->_CI->load->module_helper(FUEL_FOLDER, 'fuel');
		$content = $this->content;
		if ($strip_images)
		{
			$CI->load->helper('security');
			$content = strip_image_tags($this->content);
		}
		$content = $this->_format($content);
		$content = $this->_parse($content);
		return $content;
	}

	function get_excerpt_formatted($char_limit = NULL, $readmore = '')
	{
		$this->_CI->load->helper('text');
		$excerpt = (empty($this->excerpt)) ? $this->content : $this->excerpt;

		if (!empty($char_limit))
		{
			// must strip tags to get accruate character count
			$excerpt = strip_tags($excerpt);
			$excerpt = character_limiter($excerpt, $char_limit);
		}
		if (!empty($readmore))
		{
			$excerpt .= ' '.anchor($this->url, $readmore, 'class="readmore"');
		}
		$excerpt = $this->_format($excerpt);
		$excerpt = $this->_parse($excerpt);
		return $excerpt;
	}
	
	function is_published()
	{
		return ($this->published === 'yes');
	}
	
	function get_comments($order = 'date_added asc', $limit = NULL)
	{
		$this->_CI->load->module_model('blog', 'blog_comments_model');
		$where = array('post_id' => $this->id, $this->_tables['blog_comments'].'.published' => 'yes');
		$order = $this->_tables['blog_comments'].'.'.$order;
		$comments = $this->_CI->blog_comments_model->find_all($where, $order, $limit);
		return $comments;
	}
	
	function get_comments_count($order = 'date_added asc', $limit = NULL)
	{
		$this->_CI->load->module_model('blog', 'blog_comments_model');
		$where = array('post_id' => $this->id, $this->_tables['blog_comments'].'.published' => 'yes');
		$cnt = $this->_CI->blog_comments_model->record_count($where, $order, $limit);
		return $cnt;
	}
	
	function get_categories($order = 'name asc')
	{
		$this->_CI->load->module_model('blog', 'blog_posts_to_categories_model');
		$where = array('post_id' => $this->id, $this->_tables['blog_categories'].'.published' => 'yes');
		$categories = $this->_CI->blog_posts_to_categories_model->find_all_array_assoc('category_name', $where, $order);
		return array_keys($categories);
	}

	function get_categories_linked($order = 'name asc', $join = ', ')
	{
		$this->_CI->load->module_model('blog', 'blog_posts_to_categories_model');
		$where = array('post_id' => $this->id, $this->_tables['blog_categories'].'.published' => 'yes');
		$posts_to_categories = $this->_CI->blog_posts_to_categories_model->find_all($where, $order);
		
		$categories_linked = array();
		foreach($posts_to_categories as $p2c)
		{
			$categories_linked[] = anchor($this->_CI->fuel_blog->url('categories/'.$p2c->category_permalink), $p2c->category_name);
		}
		$return = implode($categories_linked, $join);
		return $return;
	}
	
	function get_author()
	{
		$author = $this->lazy_load('author_id', array(BLOG_FOLDER => 'blog_users_model'));
		return $author;
	}
	
	function get_url($full_path = TRUE)
	{
		$this->_CI->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$year = date('Y', strtotime($this->date_added));
		$month = date('m', strtotime($this->date_added));
		$day = date('d', strtotime($this->date_added));
		$url = $year.'/'.$month.'/'.$day.'/'.$this->permalink;
		if ($full_path)
		{
			return $this->_CI->fuel_blog->url($url);
		}
		return $url;
	}
	
	function get_rss_date()
	{
		return standard_date('DATE_RSS', strtotime($this->date_added));
	}
	
	function get_atom_date()
	{
		return standard_date('DATE_ATOM', strtotime($this->date_added));
	}

	function get_date_formatted($format = 'M d, Y')
	{
		return date($format, strtotime($this->date_added));
	}
	
	function get_allow_comments()
	{
		$CI =& get_instance();
		if (is_null($this->_fields['allow_comments']))
		{
			return is_true_val($this->_CI->fuel_blog->settings('allow_comments'));
		}
		else
		{
			return is_true_val($this->_fields['allow_comments']);
		}
	}
	
	function is_within_comment_time_limit()
	{
		$time_limit = (int) $this->_CI->fuel_blog->settings('comments_time_limit') * (24 * 60 * 60);
		if (!empty($time_limit))
		{
			$post_date = strtotime($this->date_added);
			return (time() - $post_date < $time_limit);
		}
		return TRUE;
	}
	
	function get_social_bookmarking_links()
	{
		return social_bookmarking_links($this->url, $this->title);
	}
	
	function get_facebook_recommend()
	{
		return social_facebook_recommend($this->url);
	}

	function get_digg($size = 'Icon')
	{
		return social_digg($this->url, $this->title, $size);
	}

	function get_tweetme()
	{
		return social_tweetme($this->url);
	}
	
	private function _format($content)
	{
		$this->_CI->load->helper('typography');
		$this->_CI->load->helper('markdown');
		if (!empty($this->formatting) && !function_exists($this->formatting))
		{
			$this->_CI->load->helper(strtolower($this->formatting));
		}
		if (function_exists($this->formatting))
		{
			$content = call_user_func($this->formatting, $content);
		}
		return $content;
	}
}
?>