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
 * FUEL cache object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_console
 */

// --------------------------------------------------------------------

class Fuel_layouts extends Fuel_base_library {
	
	public $default_layout = 'main'; // default layout folder
	public $layouts_folder = '_layouts'; // layout folder
	public $layouts = array(); // layout object initialization parametres

	protected $_layouts = array(); // layout objects
	
	function __construct($params = array())
	{
		parent::__construct($params);
		
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
	function initialize($config = array())
	{
		// setup any intialized variables
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		
		// initialize layout objects
		foreach($this->layouts as $name => $init)
		{
			$init['name'] = $name;
			$init['folder'] = $this->layouts_folder;
			if (!empty($init['fields']))
			{
				$fields = $init['fields'];
				$order = 1;
				foreach($fields as $key => $f)
				{
					$fields[$key] = $this->CI->form_builder->normalize_params($f);
					$fields[$key]['order'] = $order;
					unset($fields[$key]['__DEFAULTS__']);
					$order++;
				}
				
				$init['fields'] = $fields;
				
				if (!empty($init['class']))
				{
					if (!isset($init['filename']))
					{
						$init['filename'] = $init['class'].EXT;
					}

					if (!isset($init['filepath']))
					{
						$init['filepath'] = 'libraries';
					}
					$custom_class_path = APPPATH.$init['filepath'].'/'.$init['filename'];
					require_once(APPPATH.$init['filepath'].'/'.$init['filename']);
				}
				else
				{
					$init['class'] = 'Fuel_layout';
				}
				
				$this->create($name, $init, $init['class']);
			}
		}
	}
	
	function get($layout)
	{
		if (!empty($this->_layouts[$layout]))
		{
			return $this->_layouts[$layout];
		}
		return FALSE;
	}
	
	function options_list()
	{
		$options = array();
		$layouts = $this->_layouts;
		foreach($layouts as $layout)
		{
			$options[$layout->name] = $layout->name;
		}
		return $options;
	}
	
	function create($name, $init = array(), $class = 'Fuel_layout')
	{
		if (empty($init['name']))
		{
			$init['name'] = $name;
		}
		$this->_layouts[$name] = new $class($init);
		return $this->_layouts[$name];
	}
}


class Fuel_layout extends Fuel_base_library {
	
	public $name = '';
	public $file = '';
	public $hooks = array();
	public $fields = array();
	public $field_values = array();
	public $folder = '_layouts';
	
	function __construct($params = array())
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
	function initialize($params = array())
	{
		if (is_string($params))
		{
			$params = array('name' => $params);
		}

		// setup any intialized variables
		foreach ($params as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		
		if (empty($this->file))
		{
			$this->file = $this->name;
		}
	}
	
	function set_file($layout)
	{
		$this->file = $layout;
	}
	
	function view_path()
	{
		return $this->folder.'/'.$this->file;
	}

	function set_name($name)
	{
		$this->name = $name;
	}

	function name($name)
	{
		return $name;
	}
	
	function set_fields($fields)
	{
		$this->fields = $fields;
	}
	
	function fields()
	{
		return $this->fields;
	}
	
	function add_field($key, $val)
	{
		$this->fields[$key] = $val;
	}
	
	function set_field_values($values)
	{
		$this->field_values = $values;
	}

	function set_field_value($key, $value)
	{
		$this->field_values[$key] = $value;
	}
	
	function field_values()
	{
		return $this->field_values;
	}
	
	function field_value($key)
	{
		return $this->field_value[$key];
	}
	
	function set_hook($type, $hook)
	{
		$this->hooks[$type] = $hook;
	}
	
	function call_hook($hook = 'pre_render', $params = array())
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
	function pre_process($vars)
	{
		return $vars;
	}

}



/* End of file Fuel_layouts.php */
/* Location: ./application/libraries/fuel/Fuel_layouts.php */