<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL navigation object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_navigation
 */

// --------------------------------------------------------------------

class Fuel_navigation extends Fuel_module {

	protected $module = 'navigation'; // value is set to "navigation"
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a navigation structure using the <a href="[user_guide_url]libraries/menu">Menu class</a>.
	 *
	 * The <a href="[user_guide_url]helpers/fuel_helper">fuel_nav helper</a> function is an alias to this method.
	 *
	<ul>
		<li><strong>items</strong> - the navigation items to use. By default, this is empty and will look for the nav.php file or the records in the Navigation module</li>
		<li><strong>file</strong> - the name of the file containing the navigation information</li>
		<li><strong>var</strong> - the variable name in the file to use</li>
		<li><strong>parent</strong> - the parent id you would like to start rendering from. This is either the database ID or the nav array key of the menu item</li>
		<li><strong>root</strong> - the equivalent to the root_value attribute in the Menu class. It states what the root value of the menu structure should be. Normally you don't need to worry about this</li>
		<li><strong>group_id</strong> - the group ID in the database to use. The default is <dfn>1</dfn>. Only applies to navigation items saved in the admin</li>
		<li><strong>exclude</strong> - nav items to exclude from the menu. Can be an array or a regular expression string</li>
		<li><strong>return_normalized</strong> - returns the raw normalized array that gets used to generate the menu</li>
		<li><strong>render_type</strong>: options are basic, breadcrumb, page_title, collapsible, delimited, array. Default is 'basic'</li>
		<li><strong>active_class</strong>: the active css class. Default is 'active'</li>
		<li><strong>active</strong>: the active menu item</li>
		<li><strong>styles</strong>: css class styles to apply to menu items... can be a nested array</li>
		<li><strong>first_class</strong>: the css class for the first menu item. Default is first</li>
		<li><strong>last_class</strong>: the css class for the last menu item. Default is last</li>
		<li><strong>depth</strong>: the depth of the menu to render at</li>
		<li><strong>use_titles</strong>: use the title attribute in the links. Default is FALSE</li>
		<li><strong>container_tag</strong>: the html tag for the container of a set of menu items. Default is ul</li>
		<li><strong>container_tag_attrs</strong>: html attributes for the container tag</li>
		<li><strong>container_tag_id</strong>: html container id</li>
		<li><strong>container_tag_class</strong>: html container class</li>
		<li><strong>cascade_selected</strong>: cascade the selected items. Default is TRUE</li>
		<li><strong>include_hidden</strong>: include menu items with the hidden attribute. Default is FALSE</li>
		<li><strong>item_tag</strong>: the html list item element. Default is 'li'</li>
		<li><strong>item_id_prefix</strong>: the prefix to the item id</li>
		<li><strong>item_id_key</strong>: either id or location. Default is 'id'</li>
		<li><strong>use_nav_key</strong>: determines whether to use the nav_key or the location for the active state. Default is "AUTO"</li>
		<li><strong>pre_render_func</strong>: function to apply to menu labels before rendering</li>
		<li><strong>delimiter</strong>: the html element between the links </li>
		<li><strong>arrow_class</strong>: the class for the arrows used in breadcrumb type menus</li>
		<li><strong>display_current</strong>: determines whether to display the current active breadcrumb item</li>
		<li><strong>home_link</strong>: the root home link</li>
		<li><strong>append</strong>: appends additional menu items to those items already set (e.g. from the $nav array or from the navigation module). Good to use on dynamic pages where you need to dynamically set a navigation item for a page</li>
		<li><strong>order</strong>: the order to display... for page_title ONLY</li>
		<li><strong>language</strong>: select the appropriate language</li>
		<li><strong>include_default_language</strong>: will merge in the default language with the results. Default is FALSE</li>
		<li><strong>language_default_group</strong>: the default group to be used when including a default language. Default is FALSE</li>
	</ul>
		
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function render($params = array())
	{
		$this->CI->load->library('menu');
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
						'use_titles' => FALSE,
						'container_tag' => 'ul',
						'container_tag_attrs' => '',
						'container_tag_id' => '',
						'container_tag_class' => '',
						'cascade_selected' => TRUE,
						'include_hidden' => FALSE,
						'item_tag' => 'li',
						'item_id_prefix' => '',
						'item_id_key' => 'id',
						'use_nav_key' => 'AUTO',
						'pre_render_func' => '',
						'delimiter' => FALSE,
						'arrow_class' => 'arrow',
						'display_current' => TRUE,
						'home_link' => 'Home',
						'order' => 'asc',
						'exclude' => array(),
						'return_normalized' => FALSE,
						'append' => array(),
						'language' => NULL,
						'include_default_language' => FALSE,
						'language_default_group' => FALSE,
						'cache' => FALSE,
						);

		if (!is_array($params))
		{
			$this->CI->load->helper('array');
			$params = parse_string_to_array($params);
		}

		$p = array();
		foreach($valid as $param => $default)
		{
			$p[$param] = (isset($params[$param])) ? $params[$param] : $default;
		}

		// get language value... possible to get menu items from different languages so commented out for now
		// if ($this->fuel->language->has_multiple() AND empty($p['language']))
		// {
		// 	$p['language'] = (!empty($p['language'])) ? $p['language'] : $this->fuel->language->detect();
		// }
		if ($p['cache'] === TRUE)
		{
			// cache id and group
			$cache_id = md5(json_encode($params));
			$cache_group = $this->CI->fuel->config('page_cache_group');
			$cache = $this->CI->fuel->cache->get($cache_id, $cache_group);
			if (!empty($cache))
			{
				return $cache;
			}
		}
		
		if (empty($p['items']))
		{
			// get the menu data based on the FUEL mode or if the file parameter is specified use that
			if ($this->CI->fuel->navigation->mode() == 'views' OR !empty($params['file']))
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
				if ($this->CI->fuel->navigation->mode() != 'cms')
				{
					// load in navigation file as a starting poing
					if (file_exists(APPPATH.'views/_variables/'.$p['file'].'.php'))
					{
						$p['root_value'] = NULL;
						include(APPPATH.'views/_variables/'.$p['file'].'.php');
					}
				}
				// now load the models
				$this->fuel->load_model('fuel_navigation');

				// grab all menu items by group
				$menu_items = $this->model()->find_all_by_group($p['group_id'], $p['language'], 'nav_key');

				// if you want to simply augment the navigation, you can grab the defaults and merge in the menu items
				if (!empty($params['include_default_language']))
				{
					// get the default language value for the query
					$default_lang = $this->fuel->language->default_option();
				
					// grab all menu items by group
					$group = (!empty($params['language_default_group'])) ? $params['language_default_group'] : $p['group_id'];
					$default_menu_items = $this->model()->find_all_by_group($group, $default_lang, 'nav_key');

					// loop through and add defaults if there is no matching nav_key
					foreach($default_menu_items as $key => $item)
					{
						if (!isset($menu_items[$item['nav_key']]))
						{
							$menu_items[$key] = $default_menu_items[$item['nav_key']];	
						}
					}
				}

				// if menu items isn't empty, then we overwrite the variable with those menu items and change any parent value'
				if (!empty($menu_items)) 
				{
					$$p['var'] = $menu_items;

					// if parent exists, then assume it is a uri location and you need to convert it to a database id value
					if (!empty($p['parent']) AND is_string($p['parent']))
					{
						$parent = $this->model()->find_by_nav_key($p['parent'], $p['group_id'], $p['language']);
						if (!empty($parent['id']))
						{
							$p['parent'] = $parent['id'];
						}
					}

					// if active exists, then assume it is a uri location and you need to convert it to a database id value
					if (!empty($p['active']) AND is_string($p['active']))
					{
						$active = $this->model()->find_by_nav_key($p['active'], $p['group_id'], $p['language']);
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

		$this->CI->menu->reset();
		$this->CI->menu->initialize($p);
		

		$items = (!empty($$p['var'])) ? $$p['var'] : array();
		
		// append additional values
		if (!empty($p['append']))
		{
			$items = array_merge($items, $p['append']);
		}
		
		// exclude any items
		if (!empty($p['exclude']))
		{
			foreach($items as $key => $item)
			{
				if (is_int($key))
				{
					$check = (!empty($item['nav_key'])) ? $item['nav_key'] : $item['location'];
				}
				else
				{
					$check = $key;
				}

				if (is_string($p['exclude']))
				{
					$p['exclude'] = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $p['exclude']));
					if (preg_match('#'.$p['exclude'].'#', $check))
					{
						unset($items[$key]);
					}
				}
				else if (is_array($p['exclude']))
				{
					if (in_array($check, $p['exclude']))
					{
						unset($items[$key]);
					}
				}

			}
		}

		if ($p['return_normalized'] !== FALSE)
		{
			$return = $this->CI->menu->normalize_items($items);
		}
		else
		{
			$return = $this->CI->menu->render($items, $p['active'], $p['parent']);
		}

		if ($p['cache'] === TRUE)
		{
			$this->CI->fuel->cache->save($cache_id, $return, $cache_group, $this->CI->fuel->config('page_cache_ttl'));
		}
		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a basic unordered list navigation menu
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function basic($params)
	{
		$params['render_type'] = 'basic';
		return $this->render($params);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders a breadcrumb navigation menu
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function breadcrumb($params)
	{
		$params['render_type'] = 'breadcrumb';
		return $this->render($params);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders a collapsible navigation menu
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function collapsible($params)
	{
		$params['render_type'] = 'collapsible';
		return $this->render($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Renders a page title using the navigation structure
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function page_title($params)
	{
		$params['render_type'] = 'page_title';
		return $this->render($params);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders a delimited menu (e.g. Home | About | Products | Contact)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function delimited($params)
	{
		$params['render_type'] = 'delimited';
		return $this->render($params);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a nested array representing the navigation structure
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	string
	 */	
	public function data($params)
	{
		$params['render_type'] = 'array';
		return $this->render($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Uploads a static'navigation structure which is most like the <span class="file">fuel/application/views/_variables/nav.php</span> file
	 *
	 * @access	public
	 * @param	array	config preferences (optional)
	 * @return	boolean
	 */	
	public function upload($params = array())
	{
		$this->CI->load->library('menu');
		$this->CI->load->helper('file');
		$this->CI->load->helper('security');
		
		$valid = array( 'file_path' => APPPATH.'views/_variables/nav.php',
						'group_id' => 'main',
						'var' => 'nav',
						'clear_first' => TRUE,
						'language'	=> 'english'
						);		

		if (!is_array($params))
		{
			$this->CI->load->helper('array');
			$params = parse_string_to_array($params);
		}

		$p = array();
		foreach($valid as $param => $default)
		{
			$p[$param] = (isset($params[$param])) ? $params[$param] : $default;
		}

		// no longer needed
		unset($params);
		
		// extract out params to make it easier below
		extract($p);

		$error = FALSE;
		
		// read in the file so we can filter it
		$file = read_file($file_path);
		
		if (empty($file))
		{
			return FALSE;
		}

		// strip any php tags
		$file = str_replace('<?php', '', $file);
		
		// run xss_clean on it 
		$file = xss_clean($file);
		
		// now evaluate the string to get the nav array
		@eval($file);

		if (!empty($$var))
		{
			$nav = $this->CI->menu->normalize_items($$var);
			
			if (is_true_val($clear_first))
			{
				$this->model()->delete(array('group_id' => $group_id));
			}
			
			// save navigation group
			$group = $this->group($group_id);
			
			// set default navigation group if it doesn't exist'
			if (!isset($group->id))
			{
				$group->name = 'main';
				$id = $group->save();
				$group_id = $group->id;
			}
			
			// convert string ids to numbers so we can save... must start at last id in db
			$ids = array();
			$i = $this->model()->max_id() + 1;
			foreach($nav as $key => $item)
			{
				// if the id is empty then we assume it is the homepage
				if (empty($item['id']))
				{
					$item['id'] = 'home';
					$nav[$key]['id'] = 'home';
				}
				$ids[$item['id']] = $i;
				$i++;
			}

			// now loop through and save
			$cnt = 0;

			foreach($nav as $key => $item)
			{
				$save = array();
				$save['id'] = $ids[$item['id']];
				$save['nav_key'] = (empty($key)) ? 'home' : $key;
				$save['group_id'] = $group_id;
				$save['label'] = $item['label'];
				$save['parent_id'] = (empty($ids[$item['parent_id']])) ? 0 : $ids[$item['parent_id']];
				$save['location'] = $item['location'];
				$save['selected'] = (!empty($item['selected'])) ? $item['selected'] : $item['active']; // must be different because "active" has special meaning in FUEL
				$save['language'] = (!empty($item['language'])) ? $item['language'] : $language;

				// fix for homepage links
				if (empty($save['selected']) AND $save['nav_key'] == 'home')
				{
					$save['selected'] = 'home$';
				}
				
				$save['hidden'] = (is_true_val($item['hidden'])) ? 'yes' : 'no';
				$save['published'] = 'yes';
				$save['precedence'] = (!empty($item['precedence'])) ? $item['precedence'] : $cnt;
				if (is_array($item['attributes']))
				{
					$attr = '';
					foreach($item['attributes'] as $key => $val)
					{
						$attr .= $key .'="'.$val.'" ';
					}
					$attr = trim($attr);
				}
				else
				{
					$save['attributes'] = $item['attributes'];
				}
				if (!$this->model()->save($save))
				{
					$error = TRUE;
					break;
				}
				$cnt++;
			}
		}
		else
		{
			$error = TRUE;
		}
		return !$error;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the menu groups as an array of objects
	 *
	 * @access	public
	 * @return	array
	 */	
	public function groups()
	{
		$this->_load_nav_group_model();
		return $this->CI->fuel_navigation_groups_model->find_all();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a specific menu group object
	 *
	 * @access	public
	 * @param	mixed	Can be a groups name or ID value
	 * @return	object
	 */	
	public function group($group)
	{
		$this->_load_nav_group_model();
		if (is_numeric($group))
		{
			$group = $this->CI->fuel_navigation_groups_model->find_by_key($group);
		}
		else
		{
			$group = $this->CI->fuel_navigation_groups_model->find_one(array('name' => $group));
		}
		return $group;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the rendering mode for the navigation module
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function mode()
	{
		$fuel_mode = $this->fuel->config('fuel_mode');
		if (is_array($fuel_mode))
		{
			if (isset($fuel_mode['navigation']))
			{
				return $fuel_mode['navigation'];
			}
			else
			{
				return 'auto';
			}
		}
		return $fuel_mode;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads the group model
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _load_nav_group_model()
	{
		if (!isset($this->CI->fuel_navigation_groups_model))
		{
			$this->fuel->load_model('fuel_navigation_groups');
		}
	}
}

/* End of file Fuel_navigation.php */
/* Location: ./modules/fuel/libraries/Fuel_navigation.php */