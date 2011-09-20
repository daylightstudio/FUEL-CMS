<?php 
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
 * FUEL Helper
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/helpers/fuel_helper
 */


// --------------------------------------------------------------------

/**
 * Allows you to load a view and pass data to it
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
function fuel_block($params)
{
	$CI =& get_instance();
	$CI->load->library('parser');
	
	$valid = array( 'view' => '',
					'view_string' => FALSE,
					'model' => '', 
					'find' => 'all',
					'select' => NULL,
					'where' => '', 
					'order' => '', 
					'limit' => NULL, 
					'offset' => 0, 
					'return_method' => 'auto', 
					'assoc_key' => '',
					'data' => array(),
					'editable' => TRUE,
					'parse' => 'auto',
					'vars' => array(),
					'cache' => FALSE,
					);

	// for convenience
	if (!is_array($params))
	{
		$new_params = array();
		if (strpos($params, '=') === FALSE)
		{
			$new_params['view'] = $params;
		}
		else
		{
			$CI->load->helper('array');
			$new_params = parse_string_to_array($params);
		}
		$params = $new_params;
	}

	$p = array();
	foreach($valid as $param => $default)
	{
		$p[$param] = (isset($params[$param])) ? $params[$param] : $default;
	}
	
	// pull from cache if cache is TRUE and it exists
	if ($p['cache'] === TRUE)
	{
		$CI->load->library('cache');
		$cache_group = $CI->config->item('page_cache_group', 'fuel');
		$cache_id = (!empty($p['view_string'])) ? $p['view_string'] : $p['view'];
		$cache_id = md5($cache_id);
		$cache = $CI->cache->get($cache_id, $cache_group);
		if (!empty($cache))
		{
			return $cache;
		}
	}
	
	// load the model and data
	$vars = (array) $p['vars'];
	if (!empty($p['model']))
	{
		$data = fuel_model($p['model'], $p);
		$module_info = $CI->fuel_modules->info($p['model']);
		if (!empty($module_info))
		{
			$var_name = $CI->$module_info['model_name']->short_name(TRUE, FALSE);
			$vars[$var_name] = $data;
		}
	}
	else
	{
		$vars['data'] = $p['data'];
	}
	
	$output = '';
	
	// load proper view to parse. If a view is given then we first look up the name in the DB
	$view = '';
	if (!empty($p['view_string']))
	{
		$view = $p['view_string'];
	}
	else if (!empty($p['view']))
	{
		$view_file = APPPATH.'views/_blocks/'.$p['view'].EXT;
		if ($CI->config->item('fuel_mode', 'fuel') != 'views')
		{
			$CI->load->module_model(FUEL_FOLDER, 'blocks_model');
			
			// find the block in FUEL db
			$block = $CI->blocks_model->find_one_by_name($p['view']);
			if (isset($block->id))
			{
				if (strtolower($p['parse']) == 'auto')
				{
					$p['parse'] = TRUE;
				}

				$view = $block->view;
				
				if ($p['editable'] === TRUE)
				{
					$view = fuel_edit($block->id, 'Edit Block: '.$block->name, 'blocks').$view;
				}
			}
			else if (file_exists(APPPATH.'views/_blocks/'.$p['view'].EXT))
			{
				// pass in reference to global CI object
				$vars['CI'] =& get_instance();
				
				// pass along these since we know them... perhaps the view can use them
				$view = $CI->load->view("_blocks/".$p['view'], $vars, TRUE);
			}
		}
		else if (file_exists($view_file))
		{
			// pass along these since we know them... perhaps the view can use them
			$view = $CI->load->view("_blocks/".$p['view'], $vars, TRUE);
		}
	}
	
	// parse the view again to apply any variables from previous parse
	$output = ($p['parse'] === TRUE) ? $CI->parser->parse_string($view, $vars, TRUE) : $view;
	
	if ($p['cache'] === TRUE)
	{
		$CI->cache->save($cache_id, $output, $cache_group, $CI->config->item('page_cache_ttl', 'fuel'));
	}
	
	return $output;
}


// --------------------------------------------------------------------

/**
 * Loads a module model and creates a variable in the view that you can use to merge data 
 *
 * @access	public
 * @param	string
 * @param	mixed
 * @return	string
 */
function fuel_model($model, $params = array())
{
	$CI =& get_instance();
	$CI->load->module_library(FUEL_FOLDER, 'fuel_modules');
	$valid = array( 'find' => 'all',
					'select' => NULL,
					'where' => array(), 
					'order' => '', 
					'limit' => NULL, 
					'offset' => 0, 
					'return_method' => 'auto', 
					'assoc_key' => '',
					'var' => '',
					'module' => ''
					);
					

	if (!is_array($params))
	{
		$CI->load->helper('array');
		$params = parse_string_to_array($params);
	}

	foreach($valid as $p => $default)
	{
		$$p = (isset($params[$p])) ? $params[$p] : $default;
	}

	// load the model
	$module_info = $CI->fuel_modules->info($model);
	$model_name = $module_info['model_name'];
	
	// return NULL if model_name is empty
	if (empty($model_name)) return NULL;

	//echo $model_name;
	if (!empty($module))
	{
		$CI->load->module_model($module, $model_name);
	}
	else
	{
		$CI->load->model($model_name);
	}
	
	 // to get around escapinng issues we need to add spaces after =
	if (is_string($where))
	{
		$where = preg_replace('#([^>|<|!])=#', '$1 = ', $where);
	}
	
	// run select statement before the find
	if (!empty($select))
	{
		$CI->$model_name->db()->select($select, FALSE);
	}
	
	// retrieve data based on the method
	if ($find === 'key')
	{
		$data = $CI->$model_name->find_by_key($where, $return_method);
		$var = $CI->$model_name->short_name(TRUE, TRUE);
	}
	else if ($find === 'one')
	{
		$data = $CI->$model_name->find_one($where, $order, $return_method);
		$var = $CI->$model_name->short_name(TRUE, TRUE);
	}
	else
	{
		$data = $CI->$model_name->find_all($where, $order, $limit, $offset, $return_method, $assoc_key);
		$var = $CI->$model_name->short_name(TRUE, FALSE);
	}

	$vars[$var] = $data;
	
	// load the variable for the view to use
	$CI->load->vars($vars);
	
	// set the model to readonly so no data manipulation can't occur
	$CI->$model_name->readonly = TRUE;
	return $data;
}

// --------------------------------------------------------------------

/**
 * Creates a menu structure
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
function fuel_nav($params = array())
{
	$CI =& get_instance();
	$CI->load->library('menu');
	$valid = array( 'items' => array(),
					'file' => 'nav',
					'var' => 'nav',
					'root' => NULL,
					'group_id' => 1,
					'parent' => NULL, 
					'render_type' => 'basic', 
					'active_class' => 'active', 
					'active' => (uri_path(FALSE) !== '') ? uri_path(FALSE) : 'home',
					'styles' => array(),
					'first_class' => 'first', 
					'last_class' => 'last', 
					'depth' => NULL, 
					'use_titles' => TRUE,
					'container_tag' => 'ul',
					'container_tag_attrs' => '',
					'container_tag_id' => '',
					'container_tag_class' => '',
					'cascade_selected' => TRUE,
					'include_hidden' => FALSE,
					'item_tag' => 'li',
					'item_id_prefix' => '',
					'item_id_key' => 'id',
					'pre_render_func' => '',
					'delimiter' => FALSE,
					'arrow_class' => 'arrow',
					'display_current' => TRUE,
					'home_link' => 'Home',
					'order' => 'asc',
					'exclude' => array(),
					'return_normalized' => FALSE,
					);

	if (!is_array($params))
	{
		$CI->load->helper('array');
		$params = parse_string_to_array($params);
	}

	$p = array();
	foreach($valid as $param => $default)
	{
		$p[$param] = (isset($params[$param])) ? $params[$param] : $default;
	}
	
	if (empty($p['items']))
	{
		// get the menu data based on the FUEL mode or if the file parameter is specified use that
		if ($CI->config->item('fuel_mode', 'fuel') == 'views' OR !empty($params['file']))
		{
			if (file_exists(APPPATH.'views/_variables/'.$p['file'].'.php'))
			{
				include(APPPATH.'views/_variables/'.$p['file'].'.php');
			}
			else
			{
				$$p['var'] = array();
			}
		}

		// using FUEL admin
		else
		{
			if ($CI->config->item('fuel_mode', 'fuel') != 'cms')
			{
				// load in navigation file as a starting poing
				if (file_exists(APPPATH.'views/_variables/'.$p['file'].'.php'))
				{
					$p['root_value'] = NULL;
					include(APPPATH.'views/_variables/'.$p['file'].'.php');
				}
			}
			// now load from FUEL and overwrite
			$CI->load->module_model(FUEL_FOLDER, 'navigation_model');
			
			// grab all menu items by group
			$menu_items = $CI->navigation_model->find_all_by_group($p['group_id']);

			// if menu items isn't empty, then we overwrite the variable with those menu items and change any parent value'
			if (!empty($menu_items)) 
			{
				$$p['var'] = $menu_items;
				
				// if parent exists, then assume it is a uri location and you need to convert it to a database id value
				if (!empty($p['parent']) AND is_string($p['parent']))
				{
					// WARNING... it is possible to have more then one navigation item with the same location so it's best not to location values but instead use ids
					$parent = $CI->navigation_model->find_by_location($p['parent']);
					if (!empty($parent['id']))
					{
						$p['parent'] = $parent['id'];
					}
				}

				// if active exists, then assume it is a uri location and you need to convert it to a database id value
				if (!empty($p['active']) AND is_string($p['active']))
				{
					// WARNING... it is possible to have more then one navigation item with the same location so it's best not to location values but instead use ids'
					$active = $CI->navigation_model->find_by_location($p['active'], $p['group_id']);
					if (!empty($active['id']))
					{
						$p['active'] = $active['id'];
					}
				}
				$p['root_value'] = 0;
			}
		}
	}
	else
	{
		$$p['var'] = $p['items'];
	}
	if (!empty($params['root'])) $p['root_value'] = $params['root'];

	$CI->menu->reset();
	$CI->menu->initialize($p);
	
	$items = (!empty($$p['var'])) ? $$p['var'] : array();
	if (!empty($p['exclude']))
	{
		$p['exclude'] = (array) $p['exclude'];
		foreach($items as $key => $item)
		{
			if (is_int($key) AND !empty($item['location']))
			{
				$check = $item['location'];
			}
			else
			{
				$check = $key;
			}
			if (in_array($check, $p['exclude']))
			{
				unset($items[$key]);
			}
			
		}
	}
	
	if ($p['return_normalized'] !== FALSE)
	{
		return $CI->menu->normalize_items($items);
	}
	return $CI->menu->render($items, $p['active'], $p['parent']);
}

// --------------------------------------------------------------------

/**
 * Sets a variable for all views to use no matter what view it is declared in
 *
 * @access	public
 * @param	string
 * @param	array
 * @return	string
 */
function fuel_set_var($key, $val = NULL)
{
	$CI =& get_instance();
	if (is_array($key))
	{
		$CI->load->vars($key);
	}
	else if (is_string($key))
	{
		$vars[$key] = $val;
		$CI->load->vars($vars);
	}
}

// --------------------------------------------------------------------

/**
 * Returns a variable and allows for a default value
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @param	boolean
 * @return	string
 */
function fuel_var($key, $default = '', $edit_module = 'pages', $evaluate = TRUE)
{
	$CI =& get_instance();
	
	$CI->config->module_load('fuel', 'fuel', TRUE);
	$CI->load->helper('string');
	$CI->load->helper('inflector');

	if (isset($GLOBALS[$key]))
	{
		$val = $GLOBALS[$key];
	}
	else if (isset($CI->load->_ci_cached_vars[$key]))
	{
		$val = $CI->load->_ci_cached_vars[$key];
	}
	else
	{
		$val = $default;
	}
	if (is_string($val) AND $evaluate)
	{
		$val = eval_string($val);
	}
	else if (is_array($val) AND $evaluate)
	{
		foreach($val as $k => $v)
		{
			$val[$k] = eval_string($v);
		}
	}
	
	if ($edit_module === TRUE) $edit_module = 'pages';
	if (!empty($edit_module) AND $CI->config->item('fuel_mode', 'fuel') != 'views' AND !defined('USE_FUEL_MARKERS') OR (defined('USE_FUEL_MARKERS') AND USE_FUEL_MARKERS))
	{
		$marker = fuel_edit($key, humanize($key), $edit_module);
	}
	else
	{
		$marker = '';
	}
	
	if (is_string($val))
	{
		return $marker.$val;
	}
	else
	{
		if (!empty($marker))
		{
			// used to help with javascript positioning
			$marker = '<span>'.$marker.'</span>';
		}
		return $marker;
	}
}

// --------------------------------------------------------------------

/**
 * Appends a value to an array variable
 *
 * @access	public
 * @param	string
 * @param	mixed
 * @return	void
 */
function fuel_var_append($key, $value)
{
	$CI =& get_instance();
	$vars = $CI->load->_ci_cached_vars;
	
	if (isset($vars[$key]) AND is_array($vars[$key]))
	{
		if (is_array($value))
		{
			$vars[$key] = array_merge($vars[$key], $value);
		}
		else
		{
			array_push($vars[$key], $value);
		}
		fuel_set_var($key, $vars[$key]);
	}
}

// --------------------------------------------------------------------

/**
 * Sets a variable marker in a layout which can be used in editing mode
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @param	int
 * @param	int
 * @return	string
 */
function fuel_edit($id, $label = NULL, $module = 'pages', $xoffset = NULL, $yoffset = NULL)
{
	$CI =& get_instance();
	$CI->load->module_library(FUEL_FOLDER, 'fuel_page');
	if (!empty($id) AND (!defined('FUELIFY') OR defined('FUELIFY') AND FUELIFY !== FALSE))
	{
		$marker['id'] = $id;
		$marker['label'] = $label;
		$marker['module'] = $module;
		$marker['xoffset'] = $xoffset;
		$marker['yoffset'] = $yoffset;

		$key = $CI->fuel_page->add_marker($marker);

		return '<!--'.$key.'-->';
	}
	return '';
}

// --------------------------------------------------------------------

/**
 * Creates the cache ID for the FUEL page based on the URI
 *
 * @access	public
 * @param	string
 * @return	string
 */
function fuel_cache_id($location = NULL)
{
	if (empty($location)) {
		$CI =& get_instance();
		$segs = $CI->uri->segment_array();
	
		if (empty($segs)) 
		{
			return 'home';
		}
		return implode('.', $segs);
	}
	return str_replace('/', '.', $location);
}

// --------------------------------------------------------------------

/**
 * Creates the admin URL for FUEL (e.g. http://localhost/MY_PROJECT/fuel/admin)
 *
 * @access	public
 * @param	string
 * @return	string
 */
function fuel_url($uri = '')
{
	$CI =& get_instance();
	return site_url($CI->config->item('fuel_path', 'fuel').$uri);
}

// --------------------------------------------------------------------

/**
 * Returns the FUEL admin URI path
 *
 * @access	public
 * @param	string
 * @return	string
 */
function fuel_uri($uri = '')
{
	$CI =& get_instance();
	return $CI->config->item('fuel_path', 'fuel').$uri;
}

// --------------------------------------------------------------------

/**
 * Returns the uri segment based on the FUEL admin path
 *
 * @access	public
 * @param	int
 * @param	boolean
 * @return	string
 */
function fuel_uri_segment($seg_index = 0, $rerouted = FALSE)
{
	$CI =& get_instance();
	if ($rerouted)
	{
		return $CI->uri->rsegment(fuel_uri_index($seg_index));
	}
	else
	{
		return $CI->uri->segment(fuel_uri_index($seg_index));
	}
}

// --------------------------------------------------------------------

/**
 * Returns the uri index number based on the FUEL admin path
 *
 * @access	public
 * @param	int
 * @return	int
 */
function fuel_uri_index($seg_index = 0)
{
	$CI =& get_instance();
	$fuel_path = $CI->config->item('fuel_path', 'fuel');
	$start_index = count(explode('/', $fuel_path)) - 1;
	return $start_index + $seg_index;
}

// --------------------------------------------------------------------

/**
 * Returns the uri string based on the FUEL admin path
 *
 * @access	public
 * @param	int
 * @param	int
 * @param	boolean
 * @return	string
 */
function fuel_uri_string($from = 0, $to = NULL, $rerouted = FALSE)
{
	$CI =& get_instance();
	$fuel_index = fuel_uri_index($from);
	
	if ($rerouted)
	{
		$segs = $CI->uri->rsegment_array($fuel_index);
	}
	else
	{
		$segs = $CI->uri->segment_array($fuel_index);
	}
	$from = fuel_uri_index($from);
	if (isset($to)) {
		$to = fuel_uri_index($to);
		if ($from < $to)
		{
			$to = $from;
		}
	}
	$segs = array_slice($segs, $from, $to);
	$uri = implode('/', $segs);
	return $uri;
}

// --------------------------------------------------------------------

/**
 * Check to see if you are logged in and can use inline editing
 *
 * @access	public
 * @return	booleanocal
 */
function is_fuelified()
{
	$CI =& get_instance();
	$CI->config->module_load('fuel', 'fuel', TRUE);
	$CI->load->helper('cookie');
	$CI->load->module_library(FUEL_FOLDER, 'fuel_auth');
	return (get_cookie($CI->fuel_auth->get_fuel_trigger_cookie_name()));
}

// --------------------------------------------------------------------

/**
 * Returns the user language of the person logged in... used for inline editing
 *
 * @access	public
 * @return	booleanocal
 */
function fuel_user_lang()
{
	$CI =& get_instance();
	$CI->config->module_load('fuel', 'fuel', TRUE);
	$CI->load->helper('cookie');
	$CI->load->module_library(FUEL_FOLDER, 'fuel_auth');
	$cookie_val = get_cookie($CI->fuel_auth->get_fuel_trigger_cookie_name());
	$cookie_val = unserialize($cookie_val);
	if (empty($cookie_val['language']))
	{
		$cookie_val['language'] = $CI->config->item('language');
	}
	return $cookie_val['language'];
}
