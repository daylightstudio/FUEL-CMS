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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * A menu builder
 *
 * This class takes an array of elements that you can create a parent
 * child relationship by creating an array like so:
 * 
 * $nav['about/history'] = array('label' => 'About', 'parent_id' => 'about');
 * OR
 * $nav['about/history'] = array('location' => 'about_us/history', label' => 'About', 'parent_id' => 'about');
 * The documentation gives more detail
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/menu
 */

class Menu {
	
	public $active_class = 'active'; // the active css class
	public $active = ''; // the active menu item
	public $styles = array(); // css class styles to apply to menu items... can be a nested array
	public $first_class = 'first'; // the css class for the first menu item
	public $last_class = 'last';  // the css class for the last menu item
	public $depth = NULL; // the depth of the menu to render at
	public $use_titles = FALSE; // use the title attribute in the links
	public $root_value = NULL; // the root parent value... can be NULL or 0
	public $container_tag = 'ul'; // the html tag for the container of a set of menu items
	public $container_tag_attrs = ''; // html attributes for the container tag
	public $container_tag_id = ''; // html container id
	public $container_tag_class = ''; // html container class
	public $cascade_selected = TRUE; // cascade the selected items
	public $include_hidden = FALSE; // include menu items with the hidden attribute
	public $item_tag = 'li'; // the html list item element
	public $item_id_prefix = ''; // the prefix to the item id
	public $item_id_key = 'id'; // either id or location
	public $use_nav_key = 'AUTO'; // use the nav_key value to match active
	public $render_type = 'basic'; // basic, breadcrumb, page_title, collapsible, delimited, array
	public $pre_render_func = ''; // function to apply to menu labels before rendering
	
	// for breadcrumb AND/OR page_title
	public $delimiter = FALSE; // the html element between the links 
	public $display_current = TRUE; // display the current active breadcrumb item?
	public $home_link = 'Home'; // the root home link

	// for breadcrumb ONLY
	public $arrow_class = 'arrow'; // the class for the arrows
	
	// for page_title ONLY
	public $order = 'asc'; // the order to display... for page_title ONLY
	
	
	protected $_items = array(); // the items in the menu
	protected $_active_items = array(); // the active menu items
	protected $_reset_params = array(); // reset params
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		$CI =& get_instance();
		$CI->load->helper('url');

		$ignore = array('_reset_params');
		$class_vars = get_class_vars(get_class($this));
		foreach($class_vars as $key => $val)
		{
			if (!in_array($key, $ignore)) $this->_reset_params[$key] = $val;
		}
		$this->initialize($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($params = array())
	{
		$this->reset();
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function set_params($params)
	{
		if (is_array($params) AND count($params) > 0)
		{
			$valid_null = array('root_value', 'depth');
			foreach ($params as $key => $val)
			{
				if (isset($this->$key) OR in_array($key, $valid_null))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Same as reset
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear()
	{
		$this->reset();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clear class values
	 *
	 * @access	public
	 * @return	void
	 */
	public function reset()
	{
		foreach ($this->_reset_params as $key => $val)
		{
			$this->$key = $val;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Normalizes the menu data
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @param	boolean wether or not to use the nav_key field instead of the array key for the unique ID
	 * @return	array
	 */
	public function normalize_items($items)
	{
		$return = array();
		if (is_array($items))
		{
			$active = $this->active;
			$selected = array();

			$auto_nav_key = FALSE;
			if (is_string($this->use_nav_key) AND strtoupper($this->use_nav_key) == 'AUTO')
			{
				$this->use_nav_key = TRUE;
				$auto_nav_key = TRUE;
			}

			foreach($items as $key => $val)
			{
 				$id = (is_array($val) AND !empty($val['id'])) ? $val['id'] : trim($key);
				$defaults = array('id' => $id, 'label' => '', 'location' => $key, 'attributes' => array(), 'active' => NULL, 'parent_id' => $this->root_value, 'hidden' => FALSE);
				if (!is_array($val)) 
				{
					$val = array('id' => $key, 'label' => $val);
				}
				$return[$id] = array_merge($defaults, $val);
				
				// check to make sure parent_id does not equal id to prevent infinite loops
				if ($return[$id]['id'] == $return[$id]['parent_id'])
				{
					if ($return[$id]['id'] === 0)
					{
						unset($return[$id]);
					}
					else
					{
						$return[$id]['parent_id'] = $this->root_value;
					}
				}

				// Capture all that have selected states so we can loop through later
				if (!empty($return[$id]['active']) OR !empty($return[$id]['selected']))
				{
					$selected[$id] = (isset($return[$id]['active'])) ? $return[$id]['active'] :  $return[$id]['selected'];
				}
				
				if ($auto_nav_key AND !is_numeric($id)) 
				{
					$this->use_nav_key = FALSE;
				}
			}

			if ($this->use_nav_key !== FALSE AND isset($return[$this->active]['nav_key']))
			{
				$active = $return[$this->active]['nav_key'];
			}
			
			// now loop through the selected states
			foreach($selected as $s_id => $active_regex)
			{
				$location = $return[$s_id]['location'];
				$match = str_replace(':children', $location.'$|'.$location.'/.+', $active_regex);
				$match = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $match));

				if (empty($active))
				{
					$this->active = 'home';
				}

				// Does the RegEx match?
				else if (preg_match('#^'.$match.'$#', $active))
				{
					$this->active = $s_id;
				}
			}
		}
		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders the menu output
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @return	string
	 */
	protected function _render($root_items)
	{
		switch($this->render_type)
		{
			case 'collapsible':
				$output = $this->_render_collabsible($root_items);
				break;
			case 'breadcrumb':
				$output = $this->_render_breadcrumb($root_items);
				break;
			case 'page_title':
				$output = $this->_render_page_title($root_items);
				break;
			case 'delimited':
				$output = $this->_render_delimited($root_items);
				break;
			case 'array': case 'data':
				$output = $this->_render_array($root_items);
				break;
			default:
				$output = $this->_render_basic($root_items);
		}
		return $output;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders the menu output
	 *
	 * @access	public
	 * @param	array menu item data
	 * @param	string the active menu item
	 * @param	mixed int or string of the parent id to begin rendering the menu items
	 * @param	string basic, breadcrumb, page_title, collapsible
	 * @return	string
	 */
	public function render($items, $active = NULL, $parent_id = NULL, $render_type = NULL)
	{
		if (empty($render_type))
		{
			$render_type = $this->render_type;
		}
		else
		{
			$this->render_type = $render_type;
		}

		if (!empty($active)) $this->active = $active;
		if (!isset($parent_id)) $parent_id = $this->root_value;
		
		$this->_items = $this->normalize_items($items);
		
		$root_items = $this->_get_menu_items($parent_id);
		$this->_active_items = $this->get_items_in_path($this->active);
		return $this->_render($root_items);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders collapsible menu menu output
	 *
	 * @access	public
	 * @param	array menu item data
	 * @param	string the active menu item
	 * @param	mixed int or string of the parent id to begin rendering the menu items
	 * @return	string
	 */
	public function render_collapsible($items, $active = NULL, $parent_id = NULL)
	{
		return $this->render($items, $active, $parent_id, 'collapsible');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders breadcrumb menu output
	 *
	 * @access	public
	 * @param	array menu item data
	 * @param	string the active menu item
	 * @param	mixed int or string of the parent id to begin rendering the menu items
	 * @return	string
	 */
	public function render_breadcrumb($items, $active = NULL, $parent_id = NULL)
	{
		return $this->render($items, $active, $parent_id, 'breadcrumb');
	}

	// --------------------------------------------------------------------

	/**
	 * Renders page_title menu output
	 *
	 * @access	public
	 * @param	array menu item data
	 * @param	string the active menu item
	 * @param	mixed int or string of the parent id to begin rendering the menu items
	 * @return	string
	 */
	public function render_page_title($items, $active = NULL, $parent_id = NULL)
	{
		return $this->render($items, $active, $parent_id, 'page_title');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders page_title menu output
	 *
	 * @access	public
	 * @param	array menu item data
	 * @param	string the active menu item
	 * @param	mixed int or string of the parent id to begin rendering the menu items
	 * @return	string
	 */
	public function render_delimited($items, $active = NULL, $parent_id = NULL)
	{
		return $this->render($items, $active, $parent_id, 'delimited');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders page_title menu output
	 *
	 * @access	public
	 * @param	array menu item data
	 * @param	string the active menu item
	 * @param	mixed int or string of the parent id to begin rendering the menu items
	 * @return	string
	 */
	public function render_array($items, $active = NULL, $parent_id = NULL)
	{
		return $this->render($items, $active, $parent_id, 'array');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the active items in the navigation. A render must be performed first
	 *
	 * @access	public
	 * @return	array
	 */
	public function active_items()
	{
		return $this->_active_items;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders a basic menu
	 *
	 * @access	protected
	 * @param	array nested array of menu elements
	 * @param	int depth of menu to render
	 * @return	string
	 */
	protected function _render_basic($menu, $level = -1)
	{
		$str = '';
		if (!empty($menu) AND (isset($this->depth) AND $level < $this->depth) OR !isset($this->depth))
		{
			// filter out hidden ones first. Need to do in seperate loop in case there is a hidden one at the end
			$menu = $this->_filter_hidden($menu);

			if (!empty($menu))
			{
				if (!empty($this->container_tag)) $str .= "\n".str_repeat("\t", ($level + 1))."<".$this->container_tag.$this->_get_attrs($this->container_tag_attrs);
				if (!empty($this->container_tag_id) AND $level == -1) $str .= " id=\"".$this->container_tag_id."\"";
				if (!empty($this->container_tag_class) AND $level == -1) $str .= " class=\"".$this->container_tag_class."\"";
				if (!empty($this->container_tag)) $str .= ">\n";
				$active_index = (count($this->_active_items) -1) - $level;
				$level = $level + 1;
				$i = 0;
				
				foreach($menu as $key => $val)
				{
					$str .= $this->_create_open_li($val, $level, $i, ($i == (count($menu) -1)));
					$subitems = $this->_get_menu_items($val['id']);

					if (!empty($subitems))
					{
						$str .= $this->_render_basic($subitems, $level);
					}
					if (!empty($this->item_tag))
					{
						$str .= "</".$this->item_tag.">\n";
					}
					$i++;
				}
				if (!empty($this->container_tag)) $str .= str_repeat("\t", $level)."</".$this->container_tag.">\n".str_repeat("\t", $level);
				
			}
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders the menu output in a collapsible format
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @param	int level to render menu data at
	 * @return	string
	 */
	protected function _render_collabsible($menu, $level = 0)
	{
		// filter out hidden ones first. Need to do in seperate loop in case there is a hidden one at the end
		$menu = $this->_filter_hidden($menu);
		
		$str = '';
		
		if (!empty($menu))
		{
			if (!empty($this->container_tag)) $str .= "\n".str_repeat("\t", $level)."<".$this->container_tag.$this->_get_attrs($this->container_tag_attrs);
			if (!empty($this->container_tag_id) AND $level == 0) $str .= " id=\"".$this->container_tag_id."\"";
			if (!empty($this->container_tag_class) AND $level == 0) $str .= " class=\"".$this->container_tag_class."\"";
			if (!empty($this->container_tag)) $str .= ">\n";
			
			$i = 0;
			
			// find start index
			$active_index = 0;
			if (!empty($this->_active_items))
			{
				foreach($this->_active_items as $index => $item)
				{
					if (!empty($menu[$item]))
					{
						$active_index = $index;
						break;
					}
				}
			}
			
			// loop through base menu items and start drill down
			foreach($menu as $key => $val)
			{
				$label = $this->_get_label($val);

				if ($active_index > -1 AND $key == $this->_active_items[$active_index])
				{
					$level = $level + 1;
					$subitems = $this->_get_menu_items($key);

					$str .= str_repeat("\t", $level);
					if (!empty($this->item_tag))
					{
						$str .= "<".$this->item_tag;
						$str .= $this->_get_li_classes($key, $val['id'], $level, ($i == (count($menu) -1)));
						
						// set id
						if (!empty($this->item_id_prefix))
						{
							$str .= ' id="'.$this->_get_id($val).'"';
						}
						
						$str .= '>';
					}
					$str .= anchor($val['location'], $label, $val['attributes']);
					if (!empty($subitems))
					{
						$str .= $this->_render_collabsible($subitems, $level);
					}
					if (!empty($this->item_tag))
					{
						$str .= "</".$this->item_tag.">\n";
					}
				}
				else
				{
					$str .= $this->_create_open_li($val, ($level -1), $i, ($i == (count($menu) -1)));
					if (!empty($this->item_tag))
					{
						$str .= "</".$this->item_tag.">\n";
					}
				}
				$i++;
			}
			if (!empty($this->container_tag))
			{
				if ($level > 1) $str .= str_repeat("\t", ($level -1));
				$str .= "</".$this->container_tag.">\n";
				if ($level > 1) $str .= str_repeat("\t", ($level -1));
			}
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders the menu output in a breadcrumb format
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @return	string
	 */
	protected function _render_breadcrumb($menu)
	{
		if (empty($this->delimiter))
		{
			$this->delimiter = ' &gt; ';
		}

		$str = '';
		$num = count($this->_active_items) -1;
		if (!empty($this->home_link))
		{
			if (is_array($this->home_link))
			{
				$home_link = each($this->home_link);
				$home_anchor = anchor($home_link['key'], $home_link['value']);
			}
			else
			{
				$home_anchor = anchor('', $this->home_link);
			}
			
			
			if (!empty($this->item_tag))
			{
				$str .= "\t<".$this->item_tag.">";
			}
			$str .= $home_anchor;
			if ($num >= 0) $str .= ' <span class="'.$this->arrow_class.'">'.$this->delimiter.'</span> ';
			if (!empty($this->item_tag))
			{
				$str .= "</".$this->item_tag.">\n";
			}
		}
		for ($i = $num; $i >= 0; $i--)
		{
			$val = $this->_active_items[$i];
			$label = $this->_get_label($this->_items[$val]);
			if (!empty($this->item_tag))
			{
				$str .= "\t<".$this->item_tag.">";
			}
			if ($i != 0)
			{
				$str .= anchor($this->_items[$val]['location'], $label);
				$str .= ' <span class="'.$this->arrow_class.'">'.$this->delimiter.'</span> ';
			}
			else if ($this->display_current) 
			{
				$str .= $label;
			}
			if (!empty($this->item_tag))
			{
				$str .= "</".$this->item_tag.">\n";
			}
		}

		$return = '';
		if (!empty($str))
		{
			if (!empty($this->container_tag)) $return .= "\n<".$this->container_tag.$this->_get_attrs($this->container_tag_attrs);
			if (!empty($this->container_tag_id)) $return .= " id=\"".$this->container_tag_id."\"";
			if (!empty($this->container_tag_class)) $return .= " class=\"".$this->container_tag_class."\"";
			if (!empty($this->container_tag)) $return .= ">\n";
			
			$return .= $str;
			if (!empty($this->container_tag)) 	$return .= "</".$this->container_tag.">\n";
		}
		return $return;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders the menu output in a page title format
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @return	string
	 */
	protected function _render_page_title($menu)
	{
		if (empty($this->delimiter))
		{
			$this->delimiter = ' &gt; ';
		}

		$str = '';
		$num = count($this->_active_items) -1;
		$home_link = '';
		if (!empty($this->home_link)){
			if (is_array($this->home_link))
			{
				$home_link = reset($this->home_link);
			}
			else
			{
				$home_link = $this->home_link;
			}
		}

		if ($this->order == 'desc')
		{
			for ($i = 0; $i <= $num; $i++)
			{
				$val = $this->_active_items[$i];
				$label = strip_tags($this->_get_label($this->_items[$val]));
				if ($i != 0)
				{
					$str .= $this->delimiter;
				}
				$str .= $label;
			}
			if (($num >= 0 AND !empty($home_link)) OR (empty($home_link) AND $num > 0)) $str .= $this->delimiter;
			$str .= strip_tags($home_link);
		}
		else
		{
			$str .= $home_link;
			if (($num >= 0 AND !empty($home_link)) OR (empty($home_link) AND $num > 0)) $str .= $this->delimiter;
			for ($i = $num; $i >= 0; $i--)
			{
				$val = $this->_active_items[$i];
				$label = strip_tags($this->_get_label($this->_items[$val]));
				$str .= $label;
				if ($i != 0)
				{
					$str .= $this->delimiter;
				}
			}
			
		}

		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders the menu output in a delimited format
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @return	string
	 */
	protected function _render_delimited($menu)
	{
		// filter out hidden ones first. Need to do in seperate loop in case there is a hidden one at the end
		$menu = $this->_filter_hidden($menu);
		
		if ($this->container_tag !== FALSE)
		{
			$this->container_tag = 'div';
		}
		if (empty($this->delimiter))
		{
			$this->delimiter = ' &nbsp;|&nbsp; ';
		}

		$links = array();
		foreach($menu as $val)
		{
			if (!empty($this->item_id_prefix))
			{
				if (is_array($val['attributes']))
				{
					if (!in_array('id', $val['attributes']))
					{
						$val['attributes']['id'] = $this->_get_id($val);
					}
				}
				else if (strpos($val['id'], 'id=') === FALSE)
				{
					$val['attributes'] .= ' id="'.$this->_get_id($val).'"';
				}
			}
			$links[] = $this->_create_link($val, $val['id']);
		}
		$str = implode($this->delimiter, $links);
		
		$return = '';
		if (!empty($str))
		{
			if (!empty($this->container_tag)) $return .= "\n<".$this->container_tag.$this->_get_attrs($this->container_tag_attrs);
			if (!empty($this->container_tag_id)) $return .= " id=\"".$this->container_tag_id."\"";
			if (!empty($this->container_tag_class)) $return .= " class=\"".$this->container_tag_class."\"";
			if (!empty($this->container_tag)) $return .= ">\n";
			
			$return .= $str;
			if (!empty($this->container_tag)) 	$return .= "</".$this->container_tag.">\n";
		}
		
		return $return;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Outputs a nested array
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @return	string
	 */
	protected function _render_array($menu, $level = 0)
	{
		$return = array();
		if (!empty($menu) AND (isset($this->depth) AND $level < $this->depth) OR !isset($this->depth))
		{
			foreach($menu as $key => $val)
			{
				$subitems = $this->_get_menu_items($val['id']);
				$new_key = (isset($val['nav_key'])) ? $val['nav_key'] : $val['location'];
				$return[$new_key] = $val;
				
				if (!empty($subitems))
				{
					$level = $level + 1;
					$return[$new_key]['children'] = $this->_render_array($subitems, $level);
				}
			}
		}
		return $return;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get attributes of of the container tag elment
	 *
	 * @access	protected
	 * @param	mixed takes a string or an array
	 * @return	string
	 */
	protected function _get_attrs($attrs)
	{
		$str = '';
		if (is_array($attrs))
		{
			foreach($this->container_tag_attrs as $key => $val)
			{
				$str .= ' '.$key.'="'.$val.'"';
			}
		}
		else
		{
			if (!empty($attrs)) $str .= " ".$attrs;
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Create the ID for a item element
	 *
	 * @access	protected
	 * @param	mixed takes a string or an array
	 * @return	string
	 */
	protected function _get_id($val)
	{
		if (empty($this->item_id_prefix)) return;
		$id = strtolower(str_replace('/', '_', $val[$this->item_id_key]));
		if (empty($id))
		{
			$id = 'home';
		}
		return $this->item_id_prefix.$id;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates an open list item element
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @param	int current level
	 * @param	int current item index
	 * @param	boolean whether the item is the last in the list
	 * @return	string
	 */
	protected function _create_open_li($val, $level, $i, $is_last = FALSE)
	{
		$str = '';
		$str .= str_repeat("\t", ($level + 1));

		if (!empty($this->item_tag))
		{
			$str .= "<".$this->item_tag;

			// set id
			if (!empty($this->item_id_prefix))
			{
				$str .= ' id="'.$this->_get_id($val).'"';
			}
		
			// set styles
			$str .= $this->_get_li_classes($val, $level, $i, $is_last);
			$str .= '>';
		}
		$str .= $this->_create_link($val);
		return $str;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Creates a link element
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @param	string the active path if you want the active class rendered on the anchor (optional)
	 * @return	string
	 */
	protected function _create_link($val, $active = NULL)
	{
		$str = '';
		$label = $this->_get_label($val);
		
		$attrs = '';
		if (!empty($val['location']))
		{
			if (!empty($val['attributes']))
			{
				if (is_array($val['attributes']))
				{
					foreach($val['attributes'] as $key2 => $val2) 
					{
						$attrs .= ' '.$key2.'="'.$val2.'"';
					}
				} else {
					$attrs .= ' '.$val['attributes'];
				}
			}
			if ($this->use_titles AND (empty($attrs) OR strpos($attrs, 'title=') === FALSE))
			{
				$attrs .= ' title="'.strip_tags($val['label']).'"';
			}
			
			if (!empty($active) AND $this->active == $active)
			{
				$attrs .= ' class="'.$this->active_class.'"';
			}

			$location = (preg_match('/^#/', $val['location'])) ? $val['location'] : site_url($val['location']);
			$str .= '<a href="'.$location.'"'.$attrs.'>'.$label.'</a>';
		}
		else
		{
			if (!empty($active) AND $this->active == $active)
			{
				$str .= '<span class="'.$this->active_class.'">';
				$has_active = TRUE;
			}
			$str .= $label;
			if (!empty($has_active))
			{
				$str .= '</span>';
			}
			
		}
		return $str;
	}
	// --------------------------------------------------------------------

	/**
	 * Gets the css classes for the list item styling
	 *
	 * @access	protected
	 * @param	array menu item data
	 * @param	int current level
	 * @param	int current item index
	 * @param	boolean whether the item is the last in the list
	 * @return	string
	 */
	protected function _get_li_classes($val, $level, $i, $is_last = FALSE)
	{
		$str = '';
		$css_classes = array();
		$active = (is_array($val)) ? $val['id'] : $val;

		if (!empty($this->first_class) AND $i == 0) $css_classes[] = $this->first_class;
		if (!empty($this->last_class) AND $is_last) $css_classes[] = $this->last_class;
		
		if ($this->active == $active OR ($this->cascade_selected AND 
				is_array($this->_active_items) AND 
				in_array($active, $this->_active_items))) 
				{
					$css_classes[] = $this->active_class;
				}

		if (!empty($this->styles[$level]))
		{
			if (is_array($this->styles[$level]) AND !empty($this->styles[$level][$i]))
			{
				$css_classes[] = $this->styles[$level][$i];
			}
			else if (is_string($this->styles[$level]))
			{
				$css_classes[] = $this->styles[$level];
			}
		}

		if (!empty($css_classes))
		{
			$str .= ' class="';
			$str .= implode(' ', $css_classes);
			$str .= '"';
		}
		return $str;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Generates the label of the menu item and will call any pre_render_func before returning value
	 *
	 * @access	protected
	 * @param	array menu item values
	 * @return	string
	 */
	protected function _get_label($label)
	{
		if (!empty($this->pre_render_func)) 
		{
			$label = call_user_func($this->pre_render_func, $label);
		}

		// if it is an array, we will assume they want the 'label' key
		if (is_array($label))
		{
			$label = $label['label'];
		}
		return $label;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets the items in the active menu path
	 *
	 * @access	public
	 * @param	string active element
	 * @param	boolean first time iterating through?
	 * @return	string
	 */
	public function get_items_in_path($active, $first_time = TRUE){
		static $active_items = array();
		
		// reset it if is called more then once
		if ($first_time)
		{
			$active_items = array();
		}

		if (isset($this->_items[$active]))
		{
			$active_parent = $this->_items[$active]['parent_id'];

			// to normalize so we can do a strict comparison
			if (ctype_digit($active_parent))
			{
				$active_parent = (int) $active_parent;
			}
		}
		else
		{
			return;
		}
		
		if (!in_array($active, $active_items)) $active_items[] = $active;

		foreach($this->_items as $key => $val)
		{
			// to normalize so we can do a strict comparison
			if (ctype_digit($key))
			{
				$key = (int) $key;
			}
			if ($key === $active_parent AND !empty($key))
			{
				//echo $key .' - '.$active_parent.'<br>';
				if (isset($this->_items[$key]))
				{
					$active_items[] = $key;
				}
				$this->get_items_in_path($key, FALSE);
			}
		}
		
		return $active_items;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Gets the menu items based on the parent
	 *
	 * @access	protected
	 * @param	mixed parent id
	 * @param	array menu items
	 * @return	array
	 */
	protected function _get_menu_items($parent, $items = NULL)
	{
		if (empty($items)) $items = $this->_items;
		$str = '';
		$subitems = array();
		foreach($items as $key => $val)
		{
			// force numbers to be integers if numeric so we can do a strict comparison
			if (is_numeric($val['parent_id'])) $val['parent_id'] = (int) $val['parent_id'];
			if (is_numeric($parent)) $parent = (int) $parent;
			
			if ($parent === $val['parent_id'])
			{
				$subitems[$key] = $val;
			}
		}
		return $subitems;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Filter out hidden menu items
	 *
	 * @access	protected
	 * @param	array menu items
	 * @return	array
	 */
	protected function _filter_hidden($menu)
	{
		$filtered_menu = array();
		if (!$this->include_hidden)
		{
			foreach($menu as $key => $val)
			{
				if (!$val['hidden'] OR strtolower($val['hidden']) == 'no')
				{
					$filtered_menu[$key] = $val;
				}
			}
		}
		else
		{
			$filtered_menu = $menu;
		}
		
		return $filtered_menu;
		
	}


}

/* End of file Menu.php */
/* Location: ./modules/fuel/libraries/Menu.php */