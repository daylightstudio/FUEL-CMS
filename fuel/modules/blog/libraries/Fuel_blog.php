<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * The Main Library class used in the blog
 *
 * @package		FUEL BLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/blog/fuel_blog
 */

class Fuel_blog extends Fuel_advanced_module {
	
	protected $_settings = NULL;

	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();
		
		if (empty($params))
		{
			$params['name'] = 'blog';
		}
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params = array())
	{
		parent::initialize($params);
		
		if (!empty($params))
		{
			foreach ($params as $key => $val)
			{
				$sans_blog_key = substr($key, count('blog_'));
				if (isset($this->$key))
				{
					$this->$sans_blog_key = $val;
				}
			}
		}
		
		if ($this->CI->config->item('blog_use_db_table_settings'))
		{
			$this->CI->load->module_model(BLOG_FOLDER, 'blog_settings_model');
			$this->_settings = $this->CI->blog_settings_model->find_all_array_assoc('name');
		}
		else
		{
			$this->_settings = $this->CI->config->item('blog');
		}
		
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the title of the blog specified in the settings
	 *
	 * @access	public
	 * @return	string
	 */
	function title()
	{
		return $this->settings('title');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the descripton of the blog specified in the settings
	 *
	 * @access	public
	 * @return	string
	 */
	function description()
	{
		return $this->settings('description');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the language abbreviation currently used in CodeIgniter
	 *
	 * @access	public
	 * @param	boolean
	 * @return	string
	 */
	function language($code = FALSE)
	{
		$language = $this->CI->config->item('language');
		if ($code)
		{
			$this->CI->config->module_load(BLOG_FOLDER, 'language_codes');
			$codes = $this->CI->config->item('lang_codes');
			$flipped_codes = array_flip($codes);
			if (isset($flipped_codes[$language]))
			{
				return $flipped_codes[$language];
			}
			return FALSE;
		}
		else
		{
			return $language;
		}
	}

	/**
	 * Returns the domain to be used for the blog based on the FUEL configuration. 
	 * If empty it will return whatever $_SERVER['SERVER_NAME']. Needed for Atom feeds
	 *
	 * @access	public
	 * @param	boolean
	 * @return	string
	 */
	function domain()
	{
		if ($this->CI->config->item('domain', 'fuel'))
		{
			return $this->CI->config->item('domain', 'fuel');
		}
		else
		{
			return $_SERVER['SERVER_NAME'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the blog specific URL
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function url($uri = '')
	{
		return site_url($this->settings('uri').$uri);
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the blog specific RSS feed URL
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function feed($type = 'rss', $category = '')
	{
		if (empty($category))
		{
			if ($this->CI->uri->rsegment(1) == 'categories' AND $this->CI->uri->rsegment(2))
			{
				$category = $this->CI->uri->rsegment(2);
			}
		}
		$uri = (!empty($category)) ? 'categories/'.$category.'/feed/' : 'feed/';
		return $this->url($uri.$type);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the HTTP headers needed for the RSS feed
	 *
	 * @access	public
	 * @return	string
	 */
	function feed_header()
	{
		header('Content-Type: application/xml; charset=UTF-8');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the output for the RSS feed
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function feed_output($type = 'rss', $category = NULL)
	{
		$this->CI->load->helper('xml');
		$this->CI->load->helper('date');
		$this->CI->load->helper('text');
		
		$vars = $this->feed_data($category);
		if ($type == 'atom')
		{
			$output = $this->CI->load->module_view(BLOG_FOLDER, 'feed/atom_posts', $vars, TRUE);
		}
		else
		{
			$output = $this->CI->load->module_view(BLOG_FOLDER, 'feed/rss_posts', $vars, TRUE);
		}
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the data need for the blog feed
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function feed_data($category = NULL, $limit = 10)
	{
		$data['title'] = $this->title();
		$data['link'] = $this->url();
		$data['description'] = $this->description();
		$data['last_updated'] = $this->last_updated();
		$data['language'] = $this->language();
		
		if (!empty($category))
		{
			$data['posts'] = $this->get_category_posts($category, 'sticky, post_date desc', $limit);
		}
		else
		{
			$data['posts'] = $this->get_posts(array(), 'sticky, post_date desc', $limit);
		}
		return $data;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns last updated blog post
	 *
	 * @access	public
	 * @return	string
	 */
	function last_updated()
	{
		$post = $this->get_posts(array(), 'post_date desc', 1);
		if (!empty($post[0])) return $post[0]->atom_date;
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the path to the theme view files
	 *
	 * @access	public
	 * @return	string
	 */
	function theme_path()
	{
		return $this->settings('theme_path');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns name of the theme layout file to use
	 *
	 * @access	public
	 * @return	string
	 */
	function layout()
	{
		return '_layouts/'.$this->settings('theme_layout');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an image based on the assets upload path
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	string
	 */
	function image_path($image, $variable = NULL, $is_server = FALSE)
	{
		$base_path = $this->CI->fuel_blog->settings('asset_upload_path');
		$base_path = preg_replace('#(\{.+\})#U', $variable, $base_path);

		if ($is_server)
		{
			$folder = assets_server_path($base_path);
		}
		else
		{
			$folder = assets_path($base_path);
		}
		return $folder.$image;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the setting(s) information
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	function settings($key = NULL)
	{
		if (isset($key))
		{
			if (isset($this->_settings[$key]))
			{
				if (is_numeric($this->_settings[$key]))
				{
					return (int) $this->_settings[$key];
				}
				else
				{
					return $this->_settings[$key];
				}
			}
		}
		else if ($key == 'all')
		{
			return $this->_settings;
		}
		return NULL;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns header of the blog
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function header($vars = array(), $return = TRUE)
	{
		return $this->view('_blocks/header', $vars, $return);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns a view for the blog
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	boolean
	 * @return	string
	 */
	function view($view, $vars = array(), $return = TRUE)
	{
		$view_folder = $this->theme_path();
		$block = $this->CI->load->module_view($this->settings('theme_module'), $view_folder.$view, $vars, TRUE);
		if ($return)
		{
			return $block;
		}
		echo $block;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a block view file for the blog
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	boolean
	 * @return	string
	 */
	function block($block, $vars = array(), $return = TRUE)
	{
		$view = '_blocks/'.$block;
		return $this->view($view, $vars, $return);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns a the specified blog model object. Options are posts, categories, comments, settings and links
	 *
	 * @access	public
	 * @param	string
	 * @return	object
	 */
	function &model($model)
	{
		$model_name = 'blog_'.strtolower($model).'_model';
		if (isset($this->CI->$model_name))
		{
			return $this->CI->$model_name;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the sidemenu for the blog
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function sidemenu($blocks = array('search', 'categories'))
	{
		return $this->block('sidemenu', array('blocks' => $blocks));
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the most recent posts
	 *
	 * @access	public
	 * @param	int
	 * @return	array
	 */
	function get_recent_posts($limit = 5, $where = array())
	{
		$posts = $this->get_posts($where, 'post_date desc', $limit);
		return $posts;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the most popular posts
	 *
	 * @access	public
	 * @param	int
	 * @return	array
	 */
	function get_popular_posts($limit = 5, $where = array())
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$tables = $this->CI->config->item('tables');
		$this->CI->blog_posts_model->db()->select('(SELECT COUNT(*) FROM '.$tables['blog_comments'].' WHERE '.$tables['blog_posts'].'.id = '.$tables['blog_comments'].'.post_id GROUP BY fuel_blog_comments.post_id) AS num_comments', FALSE);
		$this->CI->blog_posts_model->db()->limit($limit);
		$where = $this->_publish_status('blog_posts', $where);
		$this->CI->blog_posts_model->db()->where($where);
		$this->CI->blog_posts_model->db()->order_by('num_comments desc');
		$query = $this->CI->blog_posts_model->get();
		$posts = $query->result();
		return $posts;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the most recent posts for a given category
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_category_posts($category = '', $order_by = 'post_date desc', $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$this->CI->blog_posts_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where = $this->_publish_status('blog_posts');
		
		if (is_numeric($category))
		{
			$where[$tables['blog_categories'].'.id'] = $category;
		}
		else
		{
			$where[$tables['blog_categories'].'.slug'] = $category;
		}
		$posts = $this->CI->blog_posts_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns posts by providing a given date
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_posts_by_date($year = NULL, $month = NULL, $day = NULL, $slug = NULL, $limit = NULL, $offset = NULL, $order_by = 'sticky, post_date desc', $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$this->CI->blog_posts_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where = $this->_publish_status('blog_posts');
		$this->CI->blog_posts_model->db()->where($where);
		if (!empty($year)) $this->CI->blog_posts_model->db()->where('YEAR('.$tables['blog_posts'].'.post_date) = '.$year);
		if (!empty($month)) $this->CI->blog_posts_model->db()->where('MONTH('.$tables['blog_posts'].'.post_date) = '.$month);
		if (!empty($day)) $this->CI->blog_posts_model->db()->where('DAY('.$tables['blog_posts'].'.post_date) = '.$day);
		if (!empty($slug)) $this->CI->blog_posts_model->db()->where($tables['blog_posts'].'.slug = "'.$slug.'"');
		$return_arr = (!empty($slug)) ? FALSE : TRUE;
		$this->CI->blog_posts_model->db()->limit($limit);
		$this->CI->blog_posts_model->db()->offset($offset);
		$this->CI->blog_posts_model->db()->order_by($order_by);
		$posts = $this->CI->blog_posts_model->get($return_arr)->result();
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns posts based on specific query parameters
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_posts($where = array(), $order_by = 'sticky, post_date desc', $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$this->CI->blog_posts_model->readonly = TRUE;
		$where = $this->_publish_status('blog_posts', $where);
		$posts = $this->CI->blog_posts_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the number of posts
	 *
	 * @access	public
	 * @param	mixed
	 * @return	array
	 */
	function get_posts_count($where = array())
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$where = $this->_publish_status('blog_posts', $where);
		$count = $this->CI->blog_posts_model->record_count($where);
		return $count;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns posts to be displayed for a specific page. Used for pagination mostly
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_posts_by_page($limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$this->CI->blog_posts_model->readonly = TRUE;
		$posts = $this->get_posts('', 'sticky, post_date desc', $limit, $offset, $return_method, $assoc_key);
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns posts grouped by the year/month
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	function get_post_archives($limit = NULL, $offset = NULL)
	{
		$posts = $this->get_posts(array(), 'post_date desc');
		$return = array();
		foreach($posts as $post)
		{
			$key = date('Y/m', strtotime($post->post_date));
			// if ($key != date('Y/m', time()))
			// {
				if (!isset($return[$key]))
				{
					$return[$key] = array();
				}
				$return[$key][] = $post;
			//}
		}
		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a single post
	 *
	 * @access	public
	 * @param	mixed	can be id or slug
	 * @param	string
	 * @param	string
	 * @return	object
	 */
	function get_post($post, $order_by = NULL, $return_method = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_model');
		$this->CI->blog_posts_model->readonly = TRUE;
		$where = $this->_publish_status('blog_posts');
		$tables = $this->CI->config->item('tables');
		if (is_int($post))
		{
			$where[$tables['blog_posts'].'.id'] = $post;
		}
		else
		{
			$where[$tables['blog_posts'].'.slug'] = $post;
		}
		$post = $this->CI->blog_posts_model->find_one($where, $order_by, $return_method);
		return $post;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the next post (if any) from a given date
	 *
	 * @access	public
	 * @param	mixed	can be id or slug
	 * @return	object
	 */
	function get_next_post($current_post, $return_method = NULL)
	{
		$posts = $this->get_posts(array('post_date >=' => $current_post->post_date, 'id !=' => $current_post->id), 'post_date, id asc', 1, NULL, $return_method);
		if (!empty($posts))
		{
			return $posts[0];
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the previous post (if any) from a given date
	 *
	 * @access	public
	 * @param	mixed	can be id or slug
	 * @return	object
	 */
	function get_prev_post($current_post, $return_method = NULL)
	{
		$posts = $this->get_posts(array('post_date <=' => $current_post->post_date, 'id !=' => $current_post->id), 'post_date, id desc', 1, NULL, $return_method);
		if (!empty($posts))
		{
			return $posts[0];
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a list of blog categories
	 *
	 * @access	public
	 * @param	mixed
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_categories($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_categories_model');
		$this->CI->blog_categories_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where[$tables['blog_categories'].'.published'] = 'yes';
		
		$categories = $this->CI->blog_categories_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $categories;
	}


	// --------------------------------------------------------------------

	/**
	 * Returns a single blog category
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	object
	 */
	function get_category($category, $order_by = NULL, $return_method = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_categories_model');
		$this->CI->blog_categories_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where = $tables['blog_categories'].'.slug = "'.$category.'" OR '.$tables['blog_categories'].'.name = "'.$category.'" AND '.$tables['blog_categories'].'.published = "yes"';
		$categories = $this->CI->blog_categories_model->find_one($where, $order_by, $return_method);
		return $categories;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a the posts associated with categories
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_posts_to_categories($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_posts_to_categories_model');
		$this->CI->blog_posts_to_categories_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where[$tables['blog_categories'].'.published'] = 'yes';
		$where[$tables['blog_posts'].'.published'] = 'yes';
		$where = $this->_publish_status('blog_posts');
		$this->CI->blog_posts_to_categories_model->db()->group_by('category_id');
		$posts_to_categories = $this->CI->blog_posts_to_categories_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $posts_to_categories;
	}

	// --------------------------------------------------------------------

	/**
	 * Searches posts for a specific term
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	function search_posts($term, $order_by = 'post_date desc', $limit = NULL, $offset = NULL)
	{
		$this->CI->load->module_model('blog', 'blog_posts_model');
		$this->CI->blog_posts_model->readonly = TRUE;
		
		$tables = $this->CI->config->item('tables');

		// can't use this because of the need to group with parenthesis'
		// $this->CI->blog_posts_model->db()->like('title', $t);
		// $this->CI->blog_posts_model->db()->or_like('content', $t);
		
		$terms = explode(' ', $term);
		$where = '(';
		$cnt = count($terms);
		$i = 0;
		foreach($terms as $t)
		{
			$where .= "(title LIKE '%".$t."%' OR content LIKE '%".$t."%' OR content_filtered LIKE '%".$t."%')";
			if ($i < $cnt - 1) $where .= " AND ";
			$i++;
		}
		$where .= ") AND ".$tables['blog_posts'].".published = 'yes'";
		$posts = $this->CI->blog_posts_model->find_all($where, $order_by, $limit, $offset);
		return $posts;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns comments. Usually specify a post in the where parameter
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_comments($where = array(), $order_by = 'post_date desc', $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_comments_model');
		$this->CI->blog_comments_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where[$tables['blog_comments'].'.published'] = 'yes';
		$where[$tables['blog_posts'].'.published'] = 'yes';
		$comments = $this->CI->blog_comments_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $comments;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a single comment
	 *
	 * @access	public
	 * @param	int
	 * @return	array
	 */
	function get_comment($id)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_comments_model');
		$this->CI->blog_comments_model->readonly = TRUE;
		$comment = $this->CI->blog_comments_model->find_by_key($id);
		return $comment;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns links
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_links($where = array(), $order_by = 'precedence desc', $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_links_model');
		$this->CI->blog_links_model->readonly = TRUE;
		$tables = $this->CI->config->item('tables');
		$where[$tables['blog_links'].'.published'] = 'yes';
		$links = $this->CI->blog_links_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $links;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a FUEL author/user
	 *
	 * @access	public
	 * @param	int
	 * @return	object
	 */
	function get_user($id)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_users_model');
		$this->CI->blog_users_model->readonly = TRUE;
		$where['active'] = 'yes';
		$where['fuel_user_id'] = $id;
		$user = $this->CI->blog_users_model->find_one($where);
		return $user;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns FUEL users/authors
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_users($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL, $return_method = NULL, $assoc_key = NULL)
	{
		$this->CI->load->module_model(BLOG_FOLDER, 'blog_users_model');
		$this->CI->blog_users_model->readonly = TRUE;
		$where['active'] = 'yes';
		$users = $this->CI->blog_users_model->find_all($where, $order_by, $limit, $offset, $return_method, $assoc_key);
		return $users;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the logged in information array of the currently logged in FUEL user
	 *
	 * @access	public
	 * @return	mixed
	 */
	function logged_in_user()
	{
		$this->CI->load->module_library(FUEL_FOLDER, 'fuel_auth');
		$valid_user = $this->CI->fuel->auth->valid_user();
		if (!empty($valid_user))
		{
			return $valid_user;
		}
		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns whether you are logged into FUEL or not
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function is_logged_in()
	{
		$this->CI->load->module_library(FUEL_FOLDER, 'fuel_auth');
		return $this->CI->fuel->auth->is_logged_in();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns whether cache should be used based on the blog settings
	 *
	 * @access	public
	 * @return	boolean
	 */
	function use_cache()
	{
		$use_cache = (int) $this->settings('use_cache');
		return !(empty($use_cache));
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a cached file if it exists
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function get_cache($cache_id)
	{
		if ($this->use_cache())
		{
			$cache_options =  array('default_ttl' => $this->settings('cache_ttl'));
			$this->CI->load->library('cache', $cache_options);
			$cache_group = $this->CI->config->item('blog_cache_group');

			if ($this->use_cache() AND $this->CI->cache->get($cache_id, $cache_group, FALSE))
			{
				$output = $this->CI->cache->get($cache_id, $cache_group);
				return $output;
			}
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Saves output to the cache
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	function save_cache($cache_id, $output)
	{
		if ($this->use_cache())
		{
			$cache_options =  array('default_ttl' => $this->settings('cache_ttl'));
			$this->CI->load->library('cache', $cache_options);

			$cache_group = $this->CI->config->item('blog_cache_group');

			// save to cache
			$this->CI->cache->save($cache_id, $output, $cache_group, $this->settings('cache_ttl'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Removes page from cache
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function remove_cache($cache_id = NULL)
	{
		if ($this->use_cache())
		{
			$this->CI->load->library('cache');

			$cache_group = $this->CI->config->item('blog_cache_group');

			// save to cache
			if (!empty($cache_id))
			{
				$this->CI->cache->remove($cache_id, $cache_group);
			}
			else
			{
				$this->CI->cache->remove_group($cache_group);
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the page title
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function page_title($title = '', $sep = '&laquo;', $order = 'right')
	{
		$title_arr = array();
		if ($order == 'left') $title_arr[] = $this->settings('title');
		if (is_array($title))
		{
			foreach($title as $val)
			{
				$title_arr[] = $val;
			}
		}
		else if (!empty($title))
		{
			array_push($title_arr, $title);
		}
		if ($order == 'right') $title_arr[] = $this->settings('title');
		return implode($sep, $title_arr);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Runs a specific blog hook
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	function run_hook($hook, $params = array())
	{
		// call module specific hook
		$hook_name = 'blog_'.$hook;
		$GLOBALS['EXT']->_call_hook($hook_name, $params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Convenience magic method if you want to drop the "get" from the method
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	function __call($name, $args)
	{
		$method = 'get_'.$name;
		if (method_exists($this, $method))
		{
			return call_user_func_array(array($this, $method), $args);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns where condition based on the users logged in state
	 *
	 * @access	protected
	 * @param	string
	 * @param	mixed
	 * @return	array
	 */
	protected function _publish_status($t = 'blog_posts', $where = array())
	{
		$this->CI->load->module_helper(FUEL_FOLDER, 'fuel');
		$tables = $this->CI->config->item('tables');
		
		if (!is_fuelified())
		{
			if (empty($where) OR is_array($where))
			{
				$where[$tables[$t].'.published'] = 'yes';

				// don't show posts in the future'
				if ($t == 'blog_posts')
				{
					$where[$tables[$t].'.post_date <= '] = datetime_now();
				}
			}
			else
			{
			//	$where .= ' AND '.$tables[$t].'.published = "yes"';

				// don't show posts in the future'
				if ($t == 'blog_posts')
				{
				//	$where .= ' AND '.$tables[$t].'.post_date <= "'.datetime_now().'"';
				}
			}
		}
		return $where;
	}
	
	
	
}

/* End of file Fuel_blog.php */
/* Location: ./modules/blog/libraries/Fuel_blog.php */