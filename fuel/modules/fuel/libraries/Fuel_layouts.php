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
	
	public $default_layout = 'main';
	public $layouts = array();
	public $layouts_path = '_layouts';
	public $layout_fields = array();

	protected $_layouts = array();
	
	function __construct($params = array())
	{
		parent::__construct($params);
		
		@include(FUEL_PATH.'config/fuel_layouts'.EXT);
		
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
		$CI =& get_instance();
		
		// setup any intialized variables
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		
		// grab layouts from the directory if layouts auto is true in the fuel_layouts config
		if ($this->layouts === TRUE OR (is_string($this->layouts) AND strtoupper($this->layouts) == 'AUTO'))
		{
			$CI->load->helper('file');
			$layouts = get_filenames(APPPATH.'views/'.$this->layouts_path);

			$this->layouts = array();
			if (!empty($layouts))
			{
				foreach($layouts as $layout)
				{
					$layout = substr($layout, 0, -4);
					$this->layouts[$layout] = $this->layouts_path.$layout;
				}
			}
		}
		
		// initialize layout objects
		foreach($this->layouts as $layout => $path)
		{
			$init = array();
			$init['name'] = $layout;
			$init['file'] = $path;

			if (!empty($this->layout_fields[$layout]))
			{
				$init['fields'] = $this->layout_fields[$layout];
			}
			if (!empty($this->layout_hooks[$layout]))
			{
				$init['hooks'] = $this->layout_hooks[$layout];
			}
			$this->_layouts[$layout] = new Fuel_layout($init);
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
}


class Fuel_layout {
	
	public $name = '';
	public $file = '';
	public $hooks = array();
	public $fields = array();
	public $field_values = array();
	public $folder = '_layouts';
	
	function __construct($params = array())
	{
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
		$CI =& get_instance();
		
		// setup any intialized variables
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
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
	
	function set_hook($type, $hook)
	{
		$this->hooks[$type] = $hook;
	}
	
	function call_hook($hook, $params = array())
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
		$hook_vars = $GLOBALS['EXT']->_call_hook($hook_name1, $params);

		// load variables
		if (!empty($hook_vars))
		{
			$CI->load->vars($hook_vars);
		}
		
	}

}



/* End of file Fuel_layouts.php */
/* Location: ./application/libraries/fuel/Fuel_layouts.php */