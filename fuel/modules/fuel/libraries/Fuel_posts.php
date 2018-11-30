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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * The Main Library class used for the posts module
 *
 * @package		FUEL POSTS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/modules/posts/fuel_posts
 */

class Fuel_posts extends Fuel_base_library {
	
	protected $module = NULL;
	protected $_settings = NULL;
	protected $_current_post = NULL;
	protected $matched_segments = array();
	protected $page_type = NULL;

	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values.
	 */
	public function __construct($params = array())
	{
		parent::__construct();
		
		if (empty($params))
		{
			$params['name'] = 'posts';
		}
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the current model object to be used for posts.
	 *
	 * @access	public
	 * @param	string 	The simple module name or object
	 * @return	object
	 */
	public function set_module($module)
	{
		if (is_string($module))
		{
			$module = $this->fuel->modules->get($module);
		}
		$this->module = $module;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the module for the posts.
	 *
	 * @access	public
	 * @return	object
	 */
	public function get_module()
	{
		return $this->module;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the base URI for posts.
	 *
	 * @access	public
	 * @return	string
	 */
	public function base_uri()
	{
		return $this->_clean_segment($this->module_config('base_uri'));
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a URL based on the base_uri value
	 *
	 * @access	public
	 * @return	string
	 */
	public function url($uri = '')
	{
		if (!empty($uri))
		{
			return site_url($this->base_uri().'/'.$uri);
		}
		return site_url($this->base_uri());

	}

	// --------------------------------------------------------------------

	/**
	 * Returns the all the segments as a string without the base_uri() value. 
	 *
	 * @access	public
	 * @param	string 	The key value in the $config['posts']['my_module'][$key] (optional)
	 * @param	string 	The module's name to grab the config information from (optional)
	 * @return	mixed
	 */
	public function module_config($key = NULL, $module = NULL)
	{
		if (!isset($module))
		{
			$module = $this->get_module();
		}
		elseif(is_string($module))
		{
			$module = $this->fuel->modules->get($module, FALSE);
		}

		$config = $module->info('pages');

		if (empty($config))
		{
			return FALSE;
		}

		if (isset($key))
		{
			if (array_key_exists($key, $config))
			{
				return $config[$key];
			}
			return NULL;
		}

		return $config;
	}

	// --------------------------------------------------------------------

	/**
	 * The view to use based on the routes and the current URI.
	 *
	 * @access	public
	 * @return	string
	 */
	public function page_type()
	{
		if ($this->page_type)
		{
			return $this->page_type;
		}

		$uri = uri_string();

		$routes = $this->routes();

		// set default page type first
		$this->page_type = 'list';

		// loop through the routes to see if there are any matches
		foreach($routes as $key => $route)
		{
			if (preg_match('#^'.$this->_normalize_route($route).'$#', $uri, $matches))
			{
				$this->matched_segments = $this->_set_matched_segments($route, $matches);
				$this->page_type = $key;
				break;
			}
		}
		return $this->page_type;
	}


	// --------------------------------------------------------------------

	/**
	 * Returns an array of all the routes that can be used for this module
	 *
	 * @access	public
	 * @return	array
	 */
	public function routes($module = NULL)
	{
		$base_uri = $this->base_uri();
		$default_routes = array(
			'archive' => $base_uri.'/archive(/$year:\d{4})(/$month:\d{1,2})?(/$day:\d{1,2})?',
			'tag' => $base_uri.'/tag/($tag:.+)',
			'category' => $base_uri.'/category/($category:.+)',
			'search' => $base_uri.'/search(/$q:.+)?',
			'list' => $base_uri,
			'post' => $base_uri.'/(\d{4})/(\d{2})/(\d{2})/($slug:.+)',
			'slug' => $base_uri.'/($slug:.+)'
		);

		$config = $this->module_config(NULL, $module);
		$routes = array();

		$route_keys = array_keys($config);
		$invalid_keys = array('base_uri', 'layout', 'vars', 'per_page');

		// first add custom routes to give the higher precedence
		foreach($config as $key => $c)
		{
			if (is_array($c) AND !empty($c['route']) AND !in_array($key, $invalid_keys))
			{
				$routes[$key] = $c['route'];
			}
		}

		// add default routes if they aren't overwritten
		foreach($default_routes as $key => $route)
		{
			if (empty($routes[$key]))
			{
				$routes[$key] = $route;
			}
		}
		return $routes;
	}

	// --------------------------------------------------------------------

	/**
	 * Finds and returns all the matched segments in a route.
	 *
	 * @access	protected
	 * @param	string 	The route
	 * @param	string 	The matches found when simply matching the route for the page_type variable
	 * @return	array
	 */
	protected function _set_matched_segments($route, $matches)
	{
		// matchup variable names
		preg_match_all('#\(.+\)#U', $route, $captures);

		$captured_vars = array();
		if (!empty($captures[0]))
		{
			foreach($captures[0] as $index => $capture)
			{
				if (preg_match('#\(.*\$(\w+):.+#U', $capture, $match))
				{
					$captured_vars[$index] = $match[1];
				}
			}
		}

		// now that we've captured the names of the variables, we'll remove their placeholders in the route to match against the real URI
		$vars = array();
		array_shift($matches);
		$matches = array_map(array($this, '_clean_segment'), $matches);
		$matched_segments = $matches;
		foreach($matches as $k => $v)
		{
			// add any named captures as keys to the matched segments array
			if (isset($captured_vars[$k]))
			{
				$matched_segments[$captured_vars[$k]] = $v;
			}
		}

		return $matched_segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Cleans a segment of any preceding or trailing slashes
	 *
	 * @access	protected
	 * @param	string 	The segment
	 * @return	string
	 */
	protected function _clean_segment($seg)
	{
		return trim($seg, '/');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Normalizes the route by removing placeholders (e.g. :any and ($name:....)).
	 *
	 * @access	protected
	 * @param	string 	The route to normalize
	 * @return	string
	 */
	protected function _normalize_route($route)
	{
		// convert wild-cards to regular expressions
		$route = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $route));

		// remove variable name placeholders
		$route = preg_replace('#(.*)(\$\w+:)(.*)#U', '$1$3', $route);
		return $route;
	}

	// --------------------------------------------------------------------

	/**
	 * This is a subset of the segments method that only includes those segments that were matched in the routes regular expression string(e.g. with "(...)").
	 *
	 * @access	public
	 * @return	array
	 */
	public function matched_segments()
	{
		return $this->matched_segments;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns a single matched segment.
	 *
	 * @access	public
	 * @param	int 	The matched segment number to return
	 * @return	string
	 */
	public function matched_segment($n)
	{
		if (is_int($n)) $n = $n - 1;
		return (isset($this->matched_segments[$n])) ? $this->matched_segments[$n] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the pagination string.
	 *
	 * @access	public
	 * @param	int 	The total number of rows that the pagination should use
	 * @param	mixed 	The base URL for the pagination
	 * @return	string
	 */
	public function pagination($total_rows, $base_url)
	{
		$this->CI->load->library('pagination');
		$config = $this->module_config('pagination');
		$config['per_page'] = $this->per_page();
		$config['total_rows'] = $total_rows;
		$config['base_url'] = $base_url;
		$config['page_query_string'] = TRUE;
		$this->CI->pagination->initialize($config);
		return $this->CI->pagination->create_links();
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a boolean value whether it is the home page.
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function is_home()
	{
		if (uri_path(FALSE) == trim($this->module_config('base_uri'), '/'))
		{
			return TRUE;
		}
		return FALSE;
	}

	
	// --------------------------------------------------------------------

	/**
	 * Returns a the specified posts model object.
	 *
	 * @access	public
	 * @return	object
	 */
	public function model()
	{
		return $this->module->model();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the current post for the page. Only set when viewing a single post.
	 *
	 * @access	public
	 * @return	object
	 */
	public function current_post()
	{
		return $this->_current_post;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the most recent posts.
	 *
	 * @access	public
	 * @param	int 	The limit of results (optional)
	 * @param	mixed 	The where condition to limit the results by -- can be a string or an array (optional)
	 * @return	array
	 */
	public function get_recent_posts($limit = 5, $where = array())
	{
		$order_field = $this->get_order_by_field();
		$order_by = $this->get_order_by_direction();
		$posts = $this->get_posts($where, $order_field.' '.$order_by, $limit);
		return $posts;
	}
	// --------------------------------------------------------------------

	/**
	 * Returns posts based on specific query parameters.
	 *
	 * @access	public
	 * @param	mixed 	The where condition to limit the results by -- can be a string or an array (optional)
	 * @param	string 	The order of the results (optional)
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array
	 */
	public function get_posts($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		$model = $this->model();

		// use the method on the model if it exists
		if (method_exists($model, 'get_posts'))
		{
			return $model->get_posts($where, $order_by, $limit, $offset);
		}

		if (empty($order_by))
		{
			$order_by = $this->get_order_by_field().' '.$this->get_order_by_direction();
		}

		// first check the model if it has it's own method
		if (method_exists($model, 'get_posts'))
		{
			return $model->get_posts($where, $order_by,  $limit, $offset);
		}

		$posts = $model->find_all($where, $order_by, $limit, $offset);
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the number of posts
	 *
	 * @access	public
	 * @param	mixed 	The where condition to limit the results by -- can be a string or an array (optional)
	 * @return	array
	 */
	public function get_posts_count($where = array())
	{
		$model = $this->model();

		// use the method on the model if it exists
		if (method_exists($model, 'get_posts_count'))
		{
			return $model->get_posts_count($where);
		}
		
		$model->_common_query($model->display_unpublished_if_logged_in);
		$count = $model->record_count($where);
		return $count;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns a single post.
	 *
	 * @access	public
	 * @param	mixed 	Can be id or slug
	 * @param	string 	The order of the results (optional)
	 * @return	object
	 */
	public function get_post($slug, $order_by = NULL)
	{
		$model = $this->model();

		// use the method on the model if it exists
		if (method_exists($model, 'get_post'))
		{
			return $model->get_post($slug, $order_by);
		}

		// first check the model if it has it's own method
		if (method_exists($model, 'get_post'))
		{
			return $model->get_archives($slug, $order_by);
		}

		$table_name = $model->table_name();
		if (is_int($slug))
		{
			$where[$table_name.'.id'] = $slug;
		}
		else
		{
			$where[$table_name.'.slug'] = $slug;
		}
		$model->db()->where($where);
		$post = $model->get(FALSE)->result();
		$this->_current_post = $post;
		return $post;

	}


	// --------------------------------------------------------------------

	/**
	 * Returns the most recent posts for a given category
	 *
	 * @access	public
	 * @param	string 	The category slug or ID value to search -- can also be a Fuel_category_model object (optional)
	 * @param	string 	The order of the results (optional)
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array
	 */
	public function get_category_posts($category, $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		$model = $this->model();

		// use the method on the model if it exists
		if (method_exists($model, 'get_category_posts'))
		{
			return $model->get_category_posts($category, $order_by, $limit, $offset);
		}
		$tables = $model->tables();
		
		if (empty($order_by))
		{
			$order_by = $this->get_order_by_field().' '.$this->get_order_by_direction();
		}

		if (is_int($category))
		{
			$where[$tables['fuel_categories'].'.id'] = $category;
		}
		else
		{
			if (is_object($category))
			{
				$category = $category->slug;
			}
			$where[$tables['fuel_categories'].'.slug'] = $category;	
		}

		$posts = $model->find_all($where, $order_by, $limit, $offset);
		return $posts;
	}

	
	// --------------------------------------------------------------------

	/**
	 * Returns the posts for a given tag
	 *
	 * @access	public
	 * @param	string 	The tag slug or ID value to search -- can also be a Fuel_tag_model object (optional)
	 * @param	string 	The order of the results
	 * @param	int The limit of results
	 * @param	int The offset of the results
	 * @return	array
	 */
	public function get_tag_posts($tag, $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		$model = $this->model();

		// use the method on the model if it exists
		if (method_exists($model, 'get_tag_posts'))
		{
			return $model->get_tag_posts($tag, $order_by, $limit, $offset);
		}

		$tables = $model->tables();

		if (is_int($tag))
		{
			$where[$tables['fuel_tags'].'.id'] = $tag;
		}
		else
		{
			if (is_object($tag))
			{
				$tag = $tag->slug;
			}
			$where[$tables['fuel_tags'].'.slug'] = $tag;	
		}
		
		$posts = $model->find_all($where, $order_by, $limit, $offset);
		return $posts;
	}
	// --------------------------------------------------------------------

	/**
	 * Returns posts by providing a given date
	 *
	 * @access	public
	 * @param	int 	The 4 digit year
	 * @param	int 	The year as an integer with 1 being January and 12 being December
	 * @param	int 	The day of the month
	 * @param	int 	The limit of results
	 * @param	int 	The offset of the results
	 * @param	string 	The order of the results
	 * @return	array
	 */
	public function get_archives($year = NULL, $month = NULL, $day = NULL,  $limit = NULL, $offset = NULL, $order_by = NULL)
	{
		$model = $this->model();

		// use the method on the model if it exists
		if (method_exists($model, 'get_archives'))
		{
			return $model->get_archives($year, $month, $day,  $limit, $offset, $order_by);
		}

		$order_by_field = $this->get_order_by_field();
		if (empty($order_by))
		{
			$order_by = $order_by_field.' '.$this->get_order_by_direction();
		}


		// first check the model if it has it's own method
		if (method_exists($model, 'get_archives'))
		{
			return $model->get_archives($year, $month, $day, $limit, $offset, $order_by);
		}

		// pass a key value pair for parameters
		if (is_array($year))
		{
			extract($year);
		}
		$table_name = $model->table_name();

		if (!empty($year)) $model->db()->where('YEAR('.$table_name.'.'.$order_by_field.') = '.$year);
		if (!empty($month)) $model->db()->where('MONTH('.$table_name.'.'.$order_by_field.') = '.$month);
		if (!empty($day)) $model->db()->where('DAY('.$table_name.'.'.$order_by_field.') = '.$day);
		if (!empty($limit))
		{
			$model->db()->limit($limit);
		}
		if (!empty($order_by))
		{
			$model->db()->order_by($order_by);	
		}
		$model->db()->offset($offset);
		$posts = $model->get(TRUE)->result();
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the next post (if any) from a given date.
	 *
	 * @access	public
	 * @param	object	The current post
	 * @return	object
	 */
	public function get_next_post($current_post)
	{
		$model = $this->model();
		$order_by_field = $this->get_order_by_field();

		$posts = $this->get_posts(array($model->table_name().'.'.$order_by_field.' >=' => $current_post->$order_by_field, $model->table_name().".id !=" => $current_post->id), $order_by_field.' asc, id asc', 1);
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
	 * @param	object	The current post
	 * @return	object
	 */
	public function get_prev_post($current_post)
	{
		$model = $this->model();
		$order_by_field = $this->get_order_by_field();

		$posts = $this->get_posts(array($model->table_name().'.'.$order_by_field.' <=' => $current_post->$order_by_field, $model->table_name().".id !=" => $current_post->id), $order_by_field.' desc, id desc', 1);
		if (!empty($posts))
		{
			return $posts[0];
		}
		return FALSE;

	}

	// --------------------------------------------------------------------

	/**
	 * Returns all the categories from published posts. (optional)
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_published_categories()
	{	
		$model = $this->model();
		$categories_model = $this->fuel->categories->model();
		$posts = $model->find_all_array_assoc('category_id');
		$published_categories = array_keys($posts);
		$tables = $model->tables();

		$categories_query_params = array();
		if (!empty($published_categories))
		{
			$categories_query_params = array('where_in' => array($tables['fuel_categories'].'.id' => $published_categories), 'where' => $tables['fuel_categories'].'.published = "yes"');
			if (!empty($language))
			{
				$categories_query_params['where'] .= ' AND '.$tables['fuel_categories'].'.language="'.$language.'" OR '.$tables['fuel_categories'].'.language=""';
			}
			$categories_query = $categories_model->query($categories_query_params);
			return $categories_query->result();
		}
		return array();
	}

	// --------------------------------------------------------------------

	/**
	 * Returns all the tags from published posts. (optional)
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_published_tags()
	{	
		$CI =& get_instance();
		$model = $this->model();
		$tags_model = $this->fuel->tags->model();
		$tables = $model->tables();

		$published_tags = $model->get_related_keys('tags', array(), $model->has_many['tags'], 'has_many');
		$tags_query_params = array();
		if (!empty($published_tags))
		{
			$tags_query_params = array('where_in' => array($tables['fuel_tags'].'.id' => $published_tags), 'where' => $tables['fuel_tags'].'.published = "yes"');
			if (!empty($language))
			{
				$categories_query_params['where'] .= ' AND '.$tables['fuel_categories'].'.language="'.$language.'" OR '.$tables['fuel_categories'].'.language=""';
			}
			$tags_query = $tags_model->query($tags_query_params);
			return $tags_query->result();
		}
		return array();
	}

	// --------------------------------------------------------------------

	/**
	 * Returns posts grouped by the year/month.
	 *
	 * @access	public
	 * @param	mixed 	The where condition to limit the results by. Can be a string or an array. (optional)
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array
	 */
	public function get_post_archives($where = array(), $limit = NULL, $offset = NULL)
	{
		$order_by_field = $this->get_order_by_field();
		$order_by = $this->get_order_by_direction();

		$posts = $this->get_posts($where, $order_by_field.' '.$order_by, $limit, $offset);
		$return = array();
		foreach($posts as $post)
		{
			$key = date('Y/m', strtotime($post->$order_by_field));
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
	 * Searches posts for a specific term.
	 *
	 * @access	public
	 * @param	mixed 	The term to search for
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @param	string 	The order of the results (optional)
	 * @return	array
	 */
	public function search($term, $limit = NULL, $offset = NULL, $order_by = NULL)
	{
		if (empty($order_by))
		{
			$order_by = $this->get_order_by_field().' '.$this->get_order_by_direction();	
		}

		$model = $this->model();
		$table = $model->table_name();
		$search_fields = $model->filters;

		$terms = explode(' ', $term);
		$where = '(';
		$cnt = count($terms);
		$i = 0;
		foreach($terms as $t)
		{
			$t = $this->CI->db->escape_str($t);
			$where .= "(";
			$where_or = array();
			foreach($search_fields as $field)
			{
				$where_or []= $field." LIKE '%".$t."%'";
			}

			$where .= implode(' OR ', $where_or);
			$where .= ")";
			if ($i < $cnt - 1) $where .= " AND ";
			$i++;
		}
		$where .= ")";
		$posts = $model->find_all($where, $order_by, $limit, $offset);
		return $posts;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the per page pagination value.
	 *
	 * @access	public
	 * @return	int 	The number of posts to limit per pagination view
	 */
	public function per_page()
	{
		$per_page = $this->page_config('per_page');
		$limit = (!empty($per_page)) ? $per_page : $this->module_config('per_page');
		return $limit;
	}

	// --------------------------------------------------------------------

	/**
	 * The view name.
	 *
	 * @access	public
	 * @return	string
	 */
	public function view()
	{
		return $this->page_config('view');
	}
	
	// --------------------------------------------------------------------

	/**
	 * The name of the layout to use for rendering
	 *
	 * @access	public
	 * @return	string
	 */
	public function layout()
	{
		// first check the page config
		$layout = $this->page_config('layout');
		if (empty($layout))
		{
			$layout = $this->module_config('layout');
		}
		if (empty($layout))
		{
			$layout = $this->fuel->layouts->default_layout;
		}
		return $layout;
	}

	// --------------------------------------------------------------------

	/**
	 * A custom method on the model to use for variables to pass to the view
	 *
	 * @access	public
	 * @return	string
	 */
	public function vars_method()
	{
		$method = $this->page_config('method');
		return $method;
	}

	// --------------------------------------------------------------------

	/**
	 * Will return the rendered and optionally display the module content. 
	 *
	 * @access	public
	 * @param	boolean
	 * @param	boolean
	 * @return	mixed 	If return is TRUE, then it will return the output
	 */
	public function page_config($key)
	{
		$config = $this->module_config($this->page_type());
		if (is_string($config))
		{
			$config = array('view' => $config);
		}
		if (!empty($config) AND array_key_exists($key, $config))
		{
			return $config[$key];	
		}
		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Will return the rendered and optionally display the module content. 
	 *
	 * @access	public
	 * @param	boolean
	 * @param	boolean
	 * @return	mixed 	If return is TRUE, then it will return the output
	 */
	public function find_module()
	{
		static $module;
		$modules = $this->fuel->modules->get(NULL, FALSE);
		foreach($modules as $name => $module)
		{
			$pages_config = $this->module_config(NULL, $module);
			if (empty($pages_config))
			{
				continue;
			}

			$base_uri = (!empty($pages_config['base_uri'])) ? $pages_config['base_uri'] : $module->name();

			// check if there is a base uri set and check if the current URI string matches
			if (!empty($base_uri))
			{
				if (preg_match('#^'.$base_uri.'#U', uri_string()))
				{
					return $module;
				}
			}

			// if no module found, then we will look at the different routes in the module config
			foreach($pages_config as $page_type => $page_config)
			{
				if (!empty($page_config['route']))
				{
					$route = $page_config['route'];
					if (preg_match('#^'.$this->_normalize_route($route).'$#U', uri_string()))
					{
						return $module;
					}
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns TRUE/FALSE as to whether a posts page should be displayed.
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function is_allowed()
	{
		return $this->page_config('view');
	}

	// --------------------------------------------------------------------

	/**
	 * Will either display a 404 error or will simply continue on to the view.
	 *
	 * @access	public
	 * @return	void
	 */
	public function show_404()
	{
		if ($this->page_config('empty_data_show_404') !== FALSE)
		{
			show_404();
		}
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Variables to be merged in with the view
	 *
	 * @access	public
	 * @return	string
	 */
	public function vars()
	{
		// first check the page config
		$page_vars = (array) $this->page_config('vars');
		$module_vars = (array) $this->module_config('vars');
		

		// find the module which have a pages configuration that matches the current URI
		if (!isset($this->module))
		{
			$module = $this->module;
		}
		else
		{
			$module = $this->find_module();	
		}

		// if no module is found... redirect
		if (empty($module))
		{
			return $vars;
			//redirect_404();
		}

		// set the module on the main Fuel_posts class to kickstart everything
		$this->set_module($module);
	
		$page_type = $this->page_type();

		$limit = ($this->per_page()) ? $this->per_page() : NULL;
		$offset = $this->CI->input->get('per_page');
		$order_by = $this->get_order_by_field().' '.$this->get_order_by_direction();

		if (method_exists($this->model(), $this->vars_method()))
		{
			$method = $this->vars_method();
			$vars = $this->vars_custom($method, $limit, $offset);
		}
		else
		{
			// check if the page is allowed
			if (!$this->is_allowed())
			{
				$this->show_404();
			}
			switch($page_type)
			{
				case 'post': case 'slug':
					$vars = $this->vars_post($this->matched_segment('slug'));
					break;
				case 'tag':
					$vars = $this->vars_tag($this->matched_segment('tag'), $order_by, $limit, $offset);
					break;
				case 'category':
					$vars = $this->vars_category($this->matched_segment('category'), $order_by, $limit, $offset);
					break;
				case 'archive':
					$year = $this->matched_segment('year');
					$month = $this->matched_segment('month');
					$day = $this->matched_segment('day');
					$vars = $this->vars_archive($year, $month, $day);
					break;
				case 'search':
					$vars = $this->vars_search($this->matched_segment('q'));
					break;
				case 'list':
					$vars = $this->vars_list($limit, $offset);
					break;
				default:
					$method = $this->vars_method();
					$vars = (!empty($method)) ? $this->vars_custom($method, $limit, $offset) : $this->vars_list($limit, $offset);
			}
		}

		$vars = array_merge($this->_common_vars(), $module_vars, $page_vars, $vars);
		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for a view using the custom method defined for the page.
	 *
	 * @access	protected
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array 
	 */
	protected function vars_custom($method = NULL, $limit = NULL, $offset = 0)
	{
		if (empty($method))
		{
			$method = $this->vars_method();	
		}
		$matched = $this->matched_segments();
		foreach($matched as $key => $val)
		{
			if (!is_int($key))
			{
				$params[$key] = $val;
			}
		}
		if (!empty($limit)) $params['limit'] = $limit;
		if (!empty($offset))$params['offset'] = $offset;
		$returned = call_user_func_array(array($this->model(), $method), $params);
		if (isset($returned[0]))
		{
			$vars['posts'] = $returned;
		}
		else if ($returned instanceof Data_record)
		{
			$vars['post'] = $returned;
		}
		else
		{
			$vars = $returned;
		}
		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for a list view.
	 *
	 * @access	protected
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array 
	 */
	protected function vars_list($limit = NULL, $offset = 0)
	{
		$order_by = $this->get_order_by_field().' '.$this->get_order_by_direction();
		$posts = $this->get_posts(array(), $order_by, $limit, $offset);
		$total_rows = $this->get_posts_count();
		$vars['posts'] = $posts;
		$vars['pagination'] = $this->pagination($total_rows, $this->url('?'));

		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for a single post view.
	 *
	 * @access	protected
	 * @param	string 	The slug value to find a post (optional)
	 * @return	array 
	 */
	protected function vars_post($slug = NULL)
	{
		$post = $this->get_post($slug);
		if (!isset($post->id))
		{
			$this->show_404();
		}
		$vars['post'] = $post;

		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for an archive view.
	 *
	 * @access	protected
	 * @param	int 	The year parameter (optional)
	 * @param	int 	The month parameter (optional)
	 * @param	int 	The day parameter (optional)
	 * @return	array 
	 */
	protected function vars_archive($year = NULL, $month = NULL, $day = NULL)
	{
		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;
		$posts = $this->get_archives($year, $month, $day);

		$display_date = $year;
		$ts = mktime(0, 0, 0, $month, 1, $year);
		if (!empty($month))
		{
			$display_date = date('F Y', $ts);
		}
		$vars['year'] = $year;
		$vars['month'] = $month;
		$vars['day'] = $day;
		$vars['display_date'] = $display_date;
		$vars['timestamp'] = $display_date;
		$vars['posts'] = $posts;

		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for a tag view.
	 *
	 * @access	protected
	 * @param	string 	The tag slug parameter (optional)
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array 
	 */
	protected function vars_tag($tag, $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		if (empty($tag))
		{
			$this->show_404();
		}

		// set the slug = to the tag value passed
		$slug = $tag;

		// set the tag object
		$tag = $this->fuel->tags->find_by_tag($tag);
		if (empty($tag->slug))
		{
			$this->show_404();
		}

		$posts = $this->get_tag_posts($slug, $order_by, $limit, $offset);
		if (empty($posts))
		{
			$this->show_404();
		}

		$total_rows = count($this->get_tag_posts($slug));

		$vars['pagination'] = $this->pagination($total_rows, $this->url('tag/'.$slug.'/?'));
		$vars['posts'] = $posts;
		$vars['tag'] = $tag;
		$vars['slug'] = $slug;
		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for a category view.
	 *
	 * @access	protected
	 * @param	string 	The category slug parameter (optional)
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array 
	 */
	protected function vars_category($category = NULL, $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		if (empty($category))
		{
			$this->show_404();
		}

		// set the slug = to the category value passed
		$slug = $category;

		// set category object
		$category = $this->fuel->categories->find_by_slug($slug);
		if (empty($category->slug))
		{
			$this->show_404();
		}

		$posts = $this->get_category_posts($slug, $order_by, $limit, $offset);

		if (empty($posts))
		{
			$this->show_404();
		}

		$total_rows = count($this->get_category_posts($slug));

		$vars['pagination'] = $this->pagination($total_rows, $this->url('category/'.$category->slug.'/?'));
		$vars['posts'] = $posts;
		$vars['category'] = $category;
		$vars['slug'] = $slug;

		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Will grab the variables for a search view.
	 *
	 * @access	protected
	 * @param	string 	The term slug parameter (optional)
	 * @param	int 	The limit of results (optional)
	 * @param	int 	The offset of the results (optional)
	 * @return	array 
	 */
	protected function vars_search($q = NULL, $limit = NULL, $offset = NULL)
	{
		$this->load->helper('text');

		if (empty($q))
		{
			$q = $this->input->get('q');
		}

		$total_rows = count($this->search($q));
		$vars['posts'] = $this->search($q, $limit, $offset);
		$vars['pagination'] = $this->pagination($total_rows, $this->url('search?q='.$q));
		$vars['q'] = $q;

		return $vars;
	}


	// --------------------------------------------------------------------

	/**
	 * Returns the field to order by. Default will return "publish_date".
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_order_by_field()
	{
		return $this->_model_prop_value('order_by_field', 'publish_date');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the direction in which to order data. Default will return "desc".
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_order_by_direction()
	{
		return $this->_model_prop_value('order_by_direction', 'desc');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the slug field for the posts. Default will return "slug".
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_slug_field()
	{
		return $this->_model_prop_value('slug_field', 'slug');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the model property value.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _model_prop_value($prop, $default)
	{
		if (property_exists($this->module->model(), $prop))
		{
			return $this->model()->$prop;
		}
		else
		{
			return $default;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of common variables that can be used for all pages
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function _common_vars()
	{
		$vars['CI'] =& get_instance();
		$vars['is_home'] = $this->is_home();
		$vars['module'] = $this->get_module();
		$vars['model'] = $this->model();
		$vars['page_type'] = $this->page_type();
		$vars['layout'] = $this->layout();
		$vars['view'] = $this->view();
		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Convenience magic method if you want to drop the "get" from the method.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __call($name, $args)
	{
		$method = 'get_'.$name;
		if (method_exists($this, $method))
		{
			return call_user_func_array(array($this, $method), $args);
		}
	}
}

/* End of file Fuel_posts.php */
/* Location: ./modules/blog/libraries/Fuel_posts.php */
