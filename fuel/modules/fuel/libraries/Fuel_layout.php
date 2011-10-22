<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

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
	
	function set_fields($fields)
	{
		$this->fields = $fields;
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
/* End of file Fuel_layout.php */
/* Location: ./application/libraries/fuel/Fuel_layout.php */