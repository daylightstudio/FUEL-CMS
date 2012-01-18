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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
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
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_navigation
 */

// --------------------------------------------------------------------

class Fuel_navigation extends Fuel_module {
	
	function initialize($params = array())
	{
		parent::initialize($params);
		$this->fuel->load_model('navigation_groups');
		
	}
	
	function render($params = array())
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
			$this->CI->load->helper('array');
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
			if ($this->CI->fuel->config('fuel_mode') == 'views' OR !empty($params['file']))
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
				if ($this->CI->fuel->config('fuel_mode') != 'cms')
				{
					// load in navigation file as a starting poing
					if (file_exists(APPPATH.'views/_variables/'.$p['file'].'.php'))
					{
						$p['root_value'] = NULL;
						include(APPPATH.'views/_variables/'.$p['file'].'.php');
					}
				}
				// now load from FUEL and overwrite
				$this->CI->load->module_model(FUEL_FOLDER, 'navigation_model');

				// grab all menu items by group
				$menu_items = $this->model()->find_all_by_group($p['group_id']);

				// if menu items isn't empty, then we overwrite the variable with those menu items and change any parent value'
				if (!empty($menu_items)) 
				{
					$$p['var'] = $menu_items;

					// if parent exists, then assume it is a uri location and you need to convert it to a database id value
					if (!empty($p['parent']) AND is_string($p['parent']))
					{
						// WARNING... it is possible to have more then one navigation item with the same location so it's best not to location values but instead use ids
						$parent = $this->model()->find_by_location($p['parent']);
						if (!empty($parent['id']))
						{
							$p['parent'] = $parent['id'];
						}
					}

					// if active exists, then assume it is a uri location and you need to convert it to a database id value
					if (!empty($p['active']) AND is_string($p['active']))
					{
						// WARNING... it is possible to have more then one navigation item with the same location so it's best not to location values but instead use ids'
						$active = $this->model()->find_by_location($p['active'], $p['group_id']);
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

		// exclude any items
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
			return $this->CI->menu->normalize_items($items);
		}
		return $this->CI->menu->render($items, $p['active'], $p['parent']);
	}
	
	function breadcrumb($params)
	{
		$params['render_type'] = 'breadcrumb';
		return $this->render($params);
	}

	function collapsible($params)
	{
		$params['render_type'] = 'collapsible';
		return $this->render($params);
	}
	
	function page_title($params)
	{
		$params['render_type'] = 'page_title';
		return $this->render($params);
	}

	function delimited($params)
	{
		$params['render_type'] = 'delimited';
		return $this->render($params);
	}

	function data($params)
	{
		$params['render_type'] = 'array';
		return $this->render($params);
	}
	
	function upload($params)
	{
		$this->CI->load->library('form_builder');
		$this->CI->load->library('menu');
		$this->CI->load->helper('file');
		$this->CI->load->helper('security');
		
		$valid = array( 'file_path' => APPPATH.'views/_variables/nav.php',
						'group_id' => 'main',
						'var' => 'nav',
						'clear_first' => TRUE,
						'var_name' => 'nav',
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
		
		if (!empty($$var_name))
		{
			$nav = $this->CI->menu->normalize_items($nav);
			
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
					
				// fix for homepage links
				if (empty($save['selected']) AND $save['nav_key'] == 'home')
				{
					$save['selected'] = 'home$';
				}
				
				$save['hidden'] = (is_true_val($item['hidden'])) ? 'yes' : 'no';
				$save['published'] = 'yes';
				$save['precedence'] = $cnt;
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
	}
	
	function groups()
	{
		return $this->CI->navigation_groups_model->find_all();
	}
	
	function group($group)
	{
		if (is_int($group))
		{
			return $this->CI->navigation_groups_model->find_by_key($grou);
		}
		else
		{
			return $this->CI->navigation_groups_model->find_one(array('name' => $group));
		}
	}
}

/* End of file Fuel_navigation.php */
/* Location: ./modules/fuel/libraries/Fuel_navigation.php */