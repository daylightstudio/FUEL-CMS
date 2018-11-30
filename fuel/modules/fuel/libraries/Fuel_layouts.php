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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL layouts object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_layouts
 */

// --------------------------------------------------------------------
// to prevent errors if 'blocks' are set in MY_fuel_layouts.php
//require_once('Fuel_blocks.php');

class Fuel_layouts extends Fuel_base_library {
	
	public $default_layout = 'main'; // default layout folder
	public $layouts_folder = '_layouts'; // layout folder 
	public $layouts = array(); // layout object initialization parameters
	public $blocks = array(); // block object initialization parameters
	public $hidden = array(); // an array of layouts to not display in the CMS dropdown

	protected $_layouts = array(); // layout objects
	
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
		parent::__construct();
		
		@include(FUEL_PATH.'config/fuel_layouts'.EXT);
		
		$this->CI->load->library('form_builder');

		if (!empty($config)) $params = $config;
		if (empty($params)) show_error('You are missing the fuel_layouts.php config file.');

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
	public function initialize($config = array())
	{
		// setup any initialized variables
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		// grab layouts from the directory if layouts auto is true in the fuel_layouts config
		$this->CI->load->helper('file');
		$this->CI->load->helper('directory');
		$layout_path = APPPATH.'views/'.$this->layouts_folder;
		$layouts = get_filenames($layout_path);
		
		$layout_files = directory_to_array($layout_path, TRUE);

		if (!empty($layout_files))
		{
			foreach($layout_files as $file)
			{
				$file_parts = explode('/', $file);
				$layout = end($file_parts);
				$layout = substr($layout, 0, -4);
				$file_dir = ltrim(dirname($file), '/');
				
				if ($file_dir != ltrim($layout_path, '/'))
				{
					$file_dir_parts = explode('/', $file_dir);
					$group = end($file_dir_parts);
				}
				else
				{
					$group = '';
				}
				
				// we won't show those that have underscores in front of them'
				if (substr($group, 0, 1) != '_')
				{
					if (empty($this->layouts[$layout]) AND substr($layout, 0, 1) != '_')
					{
						$this->layouts[$layout] = array('class' => 'Fuel_layout', 'group' => $group);
					}
					else if (!empty($this->layouts[$layout]))
					{
						if (!is_object($this->layouts[$layout]) AND empty($this->layouts[$layout]['group']))
						{
							$this->layouts[$layout]['group'] = $group;
						}
					}
				}
			}
		}

		// grab layouts from advanced module
		$advanced_modules = $this->CI->fuel->modules->advanced(FALSE);
		foreach($advanced_modules as $mod)
		{
			$path = $mod->path().'config/'.$mod->name().'_layouts.php';
			if (file_exists($path))
			{
				include($path);
				if (!empty($config['layouts']))
				{
					$this->layouts = array_merge($this->layouts, $config['layouts']);
				}
				if (!empty($config['blocks']))
				{
					$this->blocks = array_merge($this->blocks, $config['blocks']);
				}
			}
			elseif (method_exists($mod, 'setup_layouts'))
			{
				$mod->setup_layouts();
			}
		}

		// initialize layout objects
		foreach($this->layouts as $name => $init)
		{
			$this->add($name, $init);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds a layout object
	 *
	 * @access	public
	 * @param	string	The name of the layout
	 * @param	object	A layout object or an array of initialization parameters
	 * @return	mixed 	Returns the Fuel_layouts object instance for method chaining
	 */	
	public function add($name, $layout, $type = NULL)
	{
		if (is_array($layout))
		{
			if ($type == 'block')
			{
				$layout['type'] = 'block';
			}
			$layout = $this->create($name, $layout);
		}
		if ($layout)
		{
			if ($type == 'block')
			{
				$this->blocks[$name] = $layout;		
			}
			else
			{
				$this->_layouts[$name] = $layout;		
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Removes a layout object
	 *
	 * @access	public
	 * @param	string	The name of the layout
	 * @return	void
	 */	
	public function remove($name)
	{
		unset($this->_layouts[$name]);
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a layout object
	 *
	 * @access	public
	 * @param	string	The name of the layout
	 * @param	string	The type of layout to return. Options are "page" or "block"
	 * @return	mixed 	Returns either an array of Fuel_Layout objects or a single Fuel_layout object
	 */	
	public function get($layout = NULL, $type = 'page')
	{
		if (isset($layout))
		{
			if ($type == 'block')
			{
				if (!empty($this->blocks[$layout]))
				{
					if (is_array($this->blocks[$layout]))
					{
						$init = $this->blocks[$layout];
						$init['type'] = 'block';
						$layout = $this->create($layout, $init);
						return $layout;
					}
					return $this->blocks[$layout];
				}
			}
			else if (!empty($this->_layouts[$layout]))
			{
				return $this->_layouts[$layout];
			}
			return FALSE;
		}

		if ($type == 'block')
		{
			$return = array();
			foreach($this->blocks as $key => $val)
			{
				$return[$key] = $this->get($key, 'block');
			}
			return $return;
		}
		else
		{
			return $this->_layouts;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a key/value array good for creating form select options
	 *
	 * @access	public
	 * @param	boolean use block layouts or page (optional)
	 * @param	string the name of the group to filter the options by (optional)
	 * @return	array
	 */	
	public function options_list($blocks = FALSE, $group = '')
	{
		$options = array();
		$layouts = array();

		if ($blocks)
		{
			foreach($this->blocks as $key => $block)
			{
				$layouts[$key] = $this->get($key, 'block');
			}
		}
		else
		{
			$layouts = $this->_layouts;
		}
		
		// add all layouts without a group first
		if (empty($group))
		{
			foreach($layouts as $k => $layout)
			{
				if (empty($layout->group) AND !$layout->is_hidden())
				{
					$options[$layout->name] = $layout->label;
					// reduce array down
					unset($layouts[$k]);
				}
			}
		}

		//ksort($options);

		if (!empty($group))
		{
			foreach($layouts as $k => $layout)
			{
				if ((is_string($layout->group) AND $layout->group == $group) OR is_array($layout->group) AND in_array($group, $layout->group))
				{
					$options[$layout->name] = $layout->label;
					unset($layouts[$k]);
				}
			}
		}
		else
		{
			// create groups first
			foreach($layouts as $k => $layout)
			{
				if (!empty($layout->group))
				{
					$group = $layout->group;
					if (is_string($group))
					{
						$group = array($group);
					}
					foreach($group as $g)
					{
						if (!isset($options[$g]))
						{
							$options[$g] = array();
						}
						$options[$g][$layout->name] = $layout->label;
						unset($layouts[$k]);
					}
				}
			}
		}
		return $options;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Creates a new layout object
	 *
	 * @access	public
	 * @param	string	The name of the layout
	 * @param	array	Layout object initialization parameters (optional)
	 * @return	object
	 */	
	public function create($name, $init = array())
	{
		$default_class = 'Fuel_layout';
		$loaded_classes = array('Fuel_layout', 'Fuel_module_layout', 'Fuel_block_layout');

		if (is_array($init))
		{
			// modifications for block layouts
			if (!empty($init['type']) AND $init['type'] == 'block')
			{
				if (!isset($init['class']) OR $init['class'] == $default_class)
				{
					$init['class'] = 'Fuel_block_layout';
					$init['model'] =  (isset($init['model'])) ? $init['model'] : NULL;
					$init['method'] = (isset($init['method'])) ? $init['method'] : NULL;
				}
				$init['folder'] = $this->fuel->blocks->blocks_folder;
			}

			$init['name'] = $name;
			$init['folder'] = $this->layouts_folder;
			$init['class'] =  (isset($init['class'])) ? $init['class'] : $default_class;
			$init['label'] = (isset($init['label'])) ? $init['label'] : $name;
			$init['description'] = (isset($init['description'])) ? $init['description'] : NULL;
			$init['group'] = (isset($init['group'])) ? $init['group'] : NULL;
			$init['hooks'] = (isset($init['hooks'])) ? $init['hooks'] : NULL;
			$init['fields'] = (isset($init['fields'])) ? $init['fields'] : array();
			$init['import_field'] = (isset($init['import_field'])) ? $init['import_field'] : NULL;
			$init['module'] = (isset($init['module'])) ? $init['module'] : 'app';
			$init['double_parse'] = (isset($init['double_parse'])) ? $init['double_parse'] : NULL;
			$init['hidden'] = (isset($init['hidden'])) ? $init['hidden'] : FALSE;
			$init['parser'] = (isset($init['parser'])) ? $init['parser'] : NULL;

			// load custom layout classes
			if (!empty($init['class']) AND !in_array($init['class'], $loaded_classes))
			{
				if (!isset($init['filename']))
				{
					$init['filename'] = $init['class'].EXT;
				}

				if (!isset($init['filepath']))
				{
					$init['filepath'] = 'libraries';
				}

				if (isset($init['module']) AND ($init['module'] != 'app' AND $init['module'] != 'application'))
				{
					$custom_class_path = MODULES_PATH.$init['module'].'/'.$init['filepath'].'/'.$init['filename'];
				}
				else
				{
					$custom_class_path = APPPATH.$init['filepath'].'/'.$init['filename'];
				}
				require_once($custom_class_path);
			}
			$class = $init['class'];
			$layout = new $class($init);
		}
		else if (is_a($init, $default_class))
		{
			if ($init->label() == '')
			{
				$init->set_label($name);
			}
			$layout =& $init;
		}
		else
		{
			return FALSE;
		}
		return $layout;
	}
}

// ------------------------------------------------------------------------

/**
 * Base FUEL layout object.
 *
 * Can be retrieved by $this->fuel->layouts->get('{location}')
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @prefix		$layout->
 */
class Fuel_layout extends Fuel_base_library {
	
	public $name = ''; // The name of the layout
	public $label = ''; // The label to display with the layout in the select list as seen in the CMS
	public $description = ''; // A description of the layout which will be rendered as a copy field in the form
	public $file = ''; // The layout view file name
	public $module = 'app'; // The module that the layout's view file belongs to
	public $hooks = array(); // Hooks to run before and after the rendering of a page. Options are "pre_render" and "post_render"
	public $fields = array(); // The fields to associate with the layout. Must be in the Form_builder array format
	public $field_values = array(); // The values to assign to the fields
	public $folder = '_layouts'; // The folder to look in for the layout view files
	public $group = ''; // The group name to associate with the layout
	public $import_field = 'body'; // The field to be used when importing a view file
	public $include_pagevar_object = FALSE; // Determines whether to include a single variable of object of $pagevar that includes all the pages variables
	public $preview_image = ''; // An image for previewing the layout
	public $double_parse = NULL; // Double parse pages created in the CMS to allow for variables set in the CMS to cascade up to the layout. Valid options are TRUE/FALSE (AUTO only applies to the global FUEL configuration)
	public $hidden = FALSE; // Determines if the layout should be hidden from the layout dropdown select in the CMS
	public $parser = NULL; // The parsing engine to use
	
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
		parent::__construct();
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
	public function initialize($params = array())
	{
		if (!isset($this->CI->form_builder))
		{
			$this->CI->load->library('form_builder');
		}
		
		if (is_string($params))
		{
			$params = array('name' => $params);
		}

		// setup any initialized variables
		foreach ($params as $key => $val)
		{
			if (isset($this->$key) AND isset($val))
			{
				$this->$key = $val;
			}
		}
		
		if (empty($this->file))
		{
			$this->file = $this->name;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the layout view file. Do not include the '_layout' folder with the name
	 *
	 * @access	public
	 * @param	string	The name of the layout view file
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_file($layout)
	{
		$this->file = $layout;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the view file path to the layout
	 *
	 * @access	public
	 * @return	string
	 */	
	public function view_path()
	{
		return $this->folder.'/'.$this->file;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the layout name. Usually the same as the layout view file.
	 *
	 * @access	public
	 * @param	string	The name of the layout.
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_name($name)
	{
		$this->name = $name;
		return $this;		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layout name.
	 *
	 * @access	public
	 * @return	string
	 */	
	public function name()
	{
		return $this->name;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the layout label which is usually a friendlier version of the name (e.g. if the layout's name is "main", the layout may be "Main")
	 *
	 * @access	public
	 * @param	string	The name of the layout.
	 * @return	void
	 */	
	public function set_label($label)
	{
		$this->label = $label;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layout label
	 *
	 * @access	public
	 * @return	string
	 */	
	public function label()
	{
		return $this->label;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the layouts description which will be displayed when editing a page in the CMS
	 *
	 * @access	public
	 * @param	string	The layout's description
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_description($description)
	{
		$this->description = $description;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layouts description
	 *
	 * @access	public
	 * @return	string
	 */	
	public function description()
	{
		return $this->description;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the layout's fields
	 *
	 * @access	public
	 * @param	string	The name of the layout
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_fields($fields)
	{
		$this->fields = $fields;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layout's fields
	 *
	 * @access	public
	 * @return	array
	 */
	public function fields()
	{
		$fields = array();
		if (!empty($this->description))
		{
			$fields['description'] = array('type' => 'copy', 'label' => $this->description);
		}

		$fields = array_merge($fields, $this->fields);
		$fields = $this->process_fields($fields);
		return $fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Processes the layout's fields
	 *
	 * @access	public
	 * @param	array	The new fields to process
	 * @return	array
	 */
	public function process_fields($fields = array())
	{
		$order = 1;
		// create a new object so we don't conflict with the main form_builder object on CI'
		$fb = new Form_builder();
		foreach($fields as $key => $f)
		{
			$fields[$key] = $fb->normalize_params($f);
			if (empty($fields[$key]['name']))
			{
				$fields[$key]['name'] = $key;
			}
			
			if (!isset($fields[$key]['order']))
			{
				$fields[$key]['order'] = $order;
			}

			// must remove this so that the values can be normalized again
			unset($fields[$key]['__DEFAULTS__']);
			$order++;
		}
		return $fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the views folder the layout exists in. Default is the views/_layouts folder
	 *
	 * @access	public
	 * @param	string	The name of the folder
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_folder($folder)
	{
		$this->folder = $folder;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the views folder the layout exists in
	 *
	 * @access	public
	 * @return	string
	 */
	public function folder()
	{
		return $this->folder;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the module the layout belongs to
	 *
	 * @access	public
	 * @param	string	The name of the folder
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_module($module)
	{
		$this->module = $module;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the module the layout belongs to
	 *
	 * @access	public
	 * @return	string
	 */
	public function module()
	{
		if (empty($this->module))
		{
			$this->module = 'app';
		}
		return $this->module;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the group the layout belongs to
	 *
	 * @access	public
	 * @param	string	The name of the folder
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_group($group)
	{
		$this->group = $group;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the group the layout is associated with
	 *
	 * @access	public
	 * @return	string
	 */
	public function group()
	{
		return $this->group;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the field to import the main content of the page into
	 *
	 * @access	public
	 * @return	string
	 */	
	public function import_field()
	{
		return $this->import_field;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the field to import the main content of the page into
	 *
	 * @access	public
	 * @param	string	The name of the field to use
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_import_field($key)
	{
		$this->import_field = $key;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a boolean value as to whether to include the $pagevar object when rendering a page
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function include_pagevar_object()
	{
		return (bool) $this->include_pagevar_object;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a boolean value as to whether to include the $pagevar object when rendering a page
	 *
	 * @access	public
	 * @param	boolean Determines whether to include the pagevar object or not
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_include_pagevar_object($bool)
	{
		$this->include_pagevar_object = (bool) $bool;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds a single field to the layout (See the <a href="[user_guide_url]libraries/form_builder">Form_builder</a>) class for more info
	 *
	 * @access	public
	 * @param	string	The name of the layout field
	 * @param	string	The array of field configuration values
	 * @return	object 	reference to this Fuel_layout object
	 */
	public function add_field($key, $val)
	{
		static $fb;

		if (is_null($fb)) 
		{
			$fb = new Form_builder();
		}

		$val = $fb->normalize_params($val);
		if (!isset($val['name']))
		{
			$val['name'] = $key;
		}
		$val['key'] = $key;
		unset($val['__DEFAULTS__']);
		$this->fields[$key] = $val;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds multiple form fields to the layout (See the <a href="[user_guide_url]libraries/form_builder">Form_builder</a>) class for more info
	 *
	 * @access	public
	 * @param	string	The name of the layout field
	 * @param	string	The array of field configuration values
	 * @return	object 	reference to this Fuel_layout object
	 */
	public function add_fields($fields)
	{
		foreach($fields as $key => $val)
		{
			$this->add_field($key, $val);
		}
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the field values for the fields
	 *
	 * @access	public
	 * @param	array	A key/value array of field values
	 * @return	object 	reference to this Fuel_layout object
	 */
	public function set_field_values($values)
	{
		$this->field_values = $values;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets a field value
	 *
	 * @access	public
	 * @param	key		The name of the field
	 * @param	array	The value of the field
	 * @return	object 	reference to this Fuel_layout object
	 */
	public function set_field_value($key, $value)
	{
		$this->field_values[$key] = $value;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns layout's field values
	 *
	 * @access	public
	 * @return	array
	 */
	public function field_values()
	{
		return $this->field_values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a single field value
	 *
	 * @access	public
	 * @param	key		The name of the field
	 * @return	void
	 */
	public function field_value($key)
	{
		return $this->field_value[$key];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets a callback hook to be run via "pre" or "post" rendering of the page
	 *
	 * @access	public
	 * @param	key		The type of hook (e.g. "pre_render" or "post_render")
	 * @param	array	An array of hook information including the class/callback function. <a href="https://www.codeigniter.com/user_guide/general/hooks.html" target="blank">More here</a>
	 * @return	object 	reference to this Fuel_layout object
	 */
	public function set_hook($type, $hook)
	{
		$this->hooks[$type] = $hook;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Calls a specified hook to be run
	 *
	 * @access	public
	 * @param	hook	The type of hook (e.g. "pre_render" or "post_render")
	 * @param	array	An array of additional parameters to pass to the hook method/function
	 * @return	void
	 */
	public function call_hook($hook = 'pre_render', $params = array())
	{
		// call hooks set in hooks file
		$hook_name = $hook.'_'.$this->name;
	
		// run any hooks set on the object
		if (!empty($this->hooks[$hook]))
		{
			if (!is_array($GLOBALS['EXT']->hooks[$hook_name]))
			{
				$GLOBALS['EXT']->hooks[$hook_name] = array($GLOBALS['EXT']->hooks[$hook_name]);
			}
			$GLOBALS['EXT']->hooks[$hook_name][] = $this->hooks[$hook];
		}
		$hook_vars = $GLOBALS['EXT']->_call_hook($hook_name, $params);
	
		// load variables
		if (!empty($hook_vars))
		{
			$CI->load->vars($hook_vars);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - used for processing variables specific to a layout
	 *
	 * @access	public
	 * @param	array	variables for the view
	 * @return	array
	 */	
	public function pre_process($vars)
	{
		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - used for processing the final output one last time
	 *
	 * @access	public
	 * @param	string	final processed output
	 * @return	string
	 */	
	public function post_process($output)
	{
		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - used for processing the saved values of the layout
	 *
	 * @access	public
	 * @param	array	process values array
	 * @return	array
	 */	
	public function process_saved_values($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - used for processing the saved values of the layout after the page has been saved and has an ID
	 *
	 * @access	public
	 * @param	array	process values array
	 * @return	array
	 */	
	public function post_process_saved_values($values)
	{
		return $values;
	}
	// --------------------------------------------------------------------

	/**
	 * Placeholder - used for validating layout variables
	 *
	 * @access	public
	 * @param	array	variables to validate
	 * @return	boolean
	 */	
	public function validate($vars)
	{
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the image for the layout
	 *
	 * @access	public
	 * @return	string
	 */	
	public function preview_image()
	{
		return $this->preview_image;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the preview image for the layout
	 *
	 * @access	public
	 * @param	string	the preview image
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_preview_image($image)
	{
		$this->preview_image = $image;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the double parse values
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_double_parse()
	{
		if (is_null($this->double_parse))
		{
			return $this->fuel->config('double_parse');
		}
		return (boolean) $this->double_parse;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets whether CMS pages should be double parsed to allow for variables set in the CMS fields to bubble up to the layout
	 *
	 * @access	public
	 * @param	boolean	
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_double_parse($parse)
	{
		$this->double_parse = (boolean) $parse;
		return $this;
	}

		// --------------------------------------------------------------------

	/**
	 * Returns whether this layout should be hidden from the layout dropdown select
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function is_hidden()
	{
		if ($this->hidden == TRUE OR in_array($this->name, $this->fuel->layouts->hidden))
		{
			return TRUE;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets whether this layout should be hidden from the layout dropdown select
	 *
	 * @access	public
	 * @param	boolean	
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_hidden($hidden)
	{
		$this->hidden = (boolean) $hidden;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the parsing engine name (not the object)
	 *
	 * @access	public
	 * @return	string
	 */	
	public function parser()
	{
		if ($this->parser !== FALSE && is_null($this->parser))
		{
			return $this->fuel->config('parser_engine');
		}
		return $this->parser;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the parsing engine
	 *
	 * @access	public
	 * @param	string	the parser type of either "dwoo" or "twig"
	 * @return	object 	reference to this Fuel_layout object
	 */	
	public function set_parser($parser = NULL)
	{
		$valid = array('dwoo', 'twig');
		if (!in_array($parser, $valid))
		{
			$parser = $this->fuel->config('parser_engine');
		}
		$this->parser = strtolower($parser);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Parses the template based on the parsing engine
	 *
	 * @access	public
	 * @param	string	the string to parse
	 * @return	void
	 */	
	public function parse($str, $vars = array())
	{
		if ($this->parser() !== FALSE)
		{
			return $this->fuel->parser->set_engine($this->parser())->parse_string($str, $vars);	
		}
		return $str;
	}
}



// ------------------------------------------------------------------------

/**
 * Base FUEL module layout object.
 *
 * Can be retrieved by $this->fuel->layouts->get('{location}')
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @prefix		$layout->
 */
class Fuel_module_layout extends Fuel_layout {
	
	public $model = NULL; // the model to use for retrieving data
	public $list_block = NULL; // the block name to use for the list view
	public $item_block = NULL; // the block name for the detailed item view
	public $key_field = 'slug'; // the key field to use for querying a single record
	public $segment = 3; // the segment to use as the parameter to query
	public $item_where = array(); // additional item query where parameters 
	public $list_where = array(); // additional list query where parameters
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets a model value
	 *
	 * @access	public
	 * @param	string	The model
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_model($model)
	{
		$this->model = $model;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the list block
	 *
	 * @access	public
	 * @param	string	The list block
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_list_block($block)
	{
		$this->list_block = $block;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the item block
	 *
	 * @access	public
	 * @param	string	The item block
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_item_block($block)
	{
		$this->item_block = $block;
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the key field for querying
	 *
	 * @access	public
	 * @param	string	The key field for querying (e.g. 'slug')
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_key_field($field)
	{
		$this->key_field = $field;
		return $this;		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the segment index that will contain the URI slug value
	 *
	 * @access	public
	 * @param	int	The index that will contain the slug value
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_segment($segment)
	{
		$this->segment = (int) $segment;
		return $this;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets additional item query where parameters 
	 *
	 * @access	public
	 * @param	int	The index that will contain the slug value
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_item_where($where)
	{
		$this->item_where = $where;
		return $this;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets additional list query where parameters 
	 *
	 * @access	public
	 * @param	int	The index that will contain the slug value
	 * @return	object 	reference to this Fuel_module_layout object
	 */
	public function set_list_where($where)
	{
		$this->list_where = $where;
		return $this;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Placeholder hook - used for processing variables specific to a layout
	 *
	 * @access	public
	 * @param	array	variables for the view
	 * @return	array
	 */	
	public function pre_process($vars)
	{
		$_vars = array('model', 'list_block', 'item_block', 'key_field', 'segment', 'item_where', 'list_where');
		foreach($_vars as $v)
		{
			if (!isset($vars[$v]))
			{
				$vars[$v] = $this->$v;
			}
		}
		return $vars;
	}
	
}

// ------------------------------------------------------------------------

/**
 * Base FUEL layout object.
 *
 * Can be retrieved by $this->fuel->layouts->get('{location}', TRUE)
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @prefix		$layout->
 */
class Fuel_block_layout extends Fuel_layout 
{

	public $context = NULL;
	public $model = '';
	public $method = '';
	public $field = '';

	// --------------------------------------------------------------------
	
	/**
	 * Sets the context of the form fields (e.g. $block[0])
	 *
	 * @access	public
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_context($context)
	{
		$this->context = $context;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the context of the form fields (e.g. $block[0])
	 *
	 * @access	public
	 * @return	array
	 */
	public function context()
	{
		return $this->context;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the model o retrieve the value data
	 *
	 * @access	public
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_model($model)
	{
		$this->model = $model;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the model used to retrieve the value data
	 *
	 * @access	public
	 * @return	mixed
	 */
	public function model()
	{
		return $this->model;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Sets the method used retrieve the data from the model
	 *
	 * @access	public
	 * @return	object 	reference to this Fuel_block_layout object
	 */
	public function set_method($method)
	{
		$this->method = $method;
		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the method used to retrieve the data from the model
	 *
	 * @access	public
	 * @return	array
	 */
	public function method()
	{
		return $this->method;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the layout's fields
	 *
	 * @access	public
	 * @return	array
	 */
	public function fields()
	{
		$fields = parent::fields();
		$fields = $this->process_fields($fields);
		return $fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Processes the layout's fields
	 *
	 * @access	public
	 * @param	array	The new fields to process
	 * @return	array
	 */
	public function process_fields($fields = array())
	{

		// automatically add a field for the block name
		$fields['block_name'] = array('type' => 'hidden', 'value' => $this->name, 'class' => 'block_name');
		$fb = new Form_builder();

		if (!empty($this->context))
		{
			foreach($fields as $key => $val)
			{
				$fields[$key]['name'] = $this->context.'['.$key.']';
				if (empty($val['label']))
				{
					$fields[$key]['label'] = ($lang = $fb->label_lang($key)) ? $lang : ucfirst(str_replace('_', ' ', $key));
				}
			}
		}
		return $fields;
	}
}

/* End of file Fuel_layouts.php */
/* Location: ./modules/fuel/libraries/Fuel_layouts.php */