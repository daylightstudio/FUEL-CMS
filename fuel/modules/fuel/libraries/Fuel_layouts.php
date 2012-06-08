<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_layouts {
	
	public $default_layout = 'main';
	public $layouts = array();
	public $layout_fields = array();

	public $layouts_path = '_layouts';
	
	function __construct($params = array())
	{
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
	}

	function layouts_list($no_builtin = FALSE)
	{
		$layouts = array();
		if (!empty($this->layouts) AND is_array($this->layouts))
		{
			foreach($this->layouts as $key => $val)
			{
				// check if builtin is set in the fuel_layouts.php for the layout
				if (!is_array($val)) $val = array($val, 'builtin' => FALSE);
				if (!$no_builtin || !isset($val['builtin']) || ($no_builtin && !$val['builtin']))
				{
					$layouts[$key] = $key;
				}
			}
		}
		return $layouts;
	}
	
	function fields($layout, $include_value = TRUE)
	{
		$vars = array();
		$parts = $this->parts($layout);
		if (is_array($parts))
		{
			foreach($parts as $key => $val)
			{
				if (!empty($this->layout_fields[$key])) 
				{
					$part_fields = $this->part_fields($key, $include_value);
					if (is_array($part_fields)) $vars = array_merge($vars, $part_fields);
				}
			}
		}
		else if (!empty($this->layout_fields[$layout])) 
		{
			$vars = $this->part_fields($layout, $include_value);
		}
		return $vars;
	}

	function parts($layout)
	{
		if (!empty($this->layouts[$layout]))
		{
			if (is_string($this->layouts[$layout]))
			{
				return $this->layouts[$layout];
			}
			else
			{
				return $this->layouts[$layout]['parts'];
			}
		}
		return null;
	}
	
	function hooks($layout)
	{
		return (is_array($this->layouts[$layout]) && !empty($this->layouts[$layout]['hooks'])) ? $this->layouts[$layout]['hooks'] : array();
	}
	
	function part_fields($layout_part, $include_value = TRUE)
	{
		$return = array();
		if (!empty($this->layout_fields[$layout_part])){
			foreach($this->layout_fields[$layout_part] as $key => $val)
			{
				$return[$key] = $this->layout_field($val, $include_value);
			}
		}
		return $return;
	}

	function part_field_values($layout_part, $include_value = TRUE){
		$return = array();
		
		if (!empty($this->layout_fields[$layout_part])){
			foreach($this->layout_fields[$layout_part] as $key => $val)
			{
				$field_data = $this->layout_field($val, $include_value);
				$return[$key] = $field_data['value'];
			}
		}
		return $return;
	}
	
	function layout_field($value, $include_value = TRUE)
	{
		$defaults = array('value' => '', 'type' => 'string');
		if (is_string($value))
		{
			$value = array('value' => $value);
		}
		$return = array_merge($defaults, $value);
		if (!$include_value && ($return['type'] != 'boolean' && $return['type'] != 'checkbox')) unset($return['value']); // need values still 
		return $return;
	}
	
	function call_hook($layout, $hook = 'pre_render', $vars = array())
	{
		$CI =& get_instance();
		$ok_hooks = array('pre_render', 'post_render');
		
		if (!in_array($hook, $ok_hooks)) return;
		
		// execute pre layout hooks
		$hooks = $this->hooks($layout);
		if (!empty($hooks) && !empty($hooks[$hook]))
		{
			$hook_class = strtolower($hooks[$hook][0]);
			$hook_method = $hooks[$hook][1];
			$CI->load->library($hook_class);
			$hook_vars = $CI->$hook_class->$hook_method($vars);
			$CI->load->library('template');
			$CI->template->assign_global($hook_vars);
		}
	}
}
/* End of file Fuel_layout.php */
/* Location: ./application/libraries/fuel/Fuel_layout.php */