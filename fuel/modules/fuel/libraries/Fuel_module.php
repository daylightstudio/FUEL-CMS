<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_module {
	
	protected $module = '';
	protected $CI;
	protected $_init = array();
	protected $_info = array();
	
	function __construct($params = array())
	{
		$this->CI =& get_instance();
		if (count($params) > 0)
		{
			$this->initialize($params);
		}
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
	public function initialize($config = array(), $init = array())
	{
		// setup any intialized variables
		if (is_array($config))
		{
			$this->module = $config['module'];
			if (!empty($config['init']))
			{
				$this->_init = $config['init'];
			}
		}
		else
		{
			$this->module = $config;
			$this->_init = $init;
		}
		
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieve the info for a module
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	array
	 */	
	public function info($prop = NULL)
	{
		if (empty($this->_info))
		{
			$this->CI->load->helper('inflector');
			$this->CI->load->helper('string');

			$defaults = array(
				'module_name' => humanize($this->module),
				'module_uri' => $this->module,
				'model_name' => $this->module.'_model',
				'model_location' => '',
				'view_location' => '',
				'display_field' => '',
				'preview_path' => '',
				'views' => array(
					'list' => 'modules/module_list', 
					'create_edit' => 'modules/module_create_edit', 
					'delete' => 'modules/module_delete'),
				'permission' => $this->module,
				'js_controller' => 'BaseFuelController',
				'js_controller_params' => array(),
				'js_localized' => array(),
				'js' => '',
				'edit_method' => 'find_one_array',
				'instructions' => lang('module_instructions_default', $this->module),
				'filters' => array(),
				'archivable' => TRUE,
				'table_headers' => array(),
				'table_actions' => array('EDIT', 'VIEW', 'DELETE'),
				'item_actions' => array('save', 'view', 'publish', 'activate', 'delete', 'duplicate', 'create'),
				'list_actions' => array(),
				'rows_selectable' => TRUE,
				'precedence_col' => 'precedence',
				'clear_cache_on_save' => TRUE,
				'create_action_name' => lang('btn_create'),
				'configuration' => '',
				'nav_selected' => NULL,
				'default_col' => NULL,
				'default_order' => NULL,
				'sanitize_input' => TRUE,
				'sanitize_images' => TRUE,
				'displayonly' => FALSE,
				'language' => '',
				'hidden' => FALSE,
				'icon_class' => '',
				);
			$info = array();

			foreach ($defaults as $key => $val)
			{
				if (isset($this->_init[$key]))
				{
					$info[$key] = $this->_init[$key];
				}
				else
				{
					$info[$key] = $val;
				}
			}
			
			// icon class for module
			if (empty($info['icon_class']))
			{
				$info['icon_class'] = 'ico_'.url_title(str_replace('/', '_', $info['module_uri']),'_', TRUE);
			}
			
			// localize certain fields
			if ($module_name = lang('module_'.$module))
			{
				$info['module_name'] = $module_name;
			}

			// set proper jqxController name
			if (is_array($info['js_controller']))
			{
				if (empty($info['js_controller_path']))
				{
					$info['js_controller_path'] = js_path('', key($info['js_controller']));
				}
				$info['js_controller'] = current($info['js_controller']);
			}
			else if (is_string($info['js_controller']) AND strpos($info['js_controller'], '.') === FALSE)
			{
				$info['js_controller'] = 'fuel.controller.'.$info['js_controller'];
			}

			// convert slashes to jqx object periods
			$info['js_controller'] = str_replace('/', '.', $info['js_controller']);

			// set the base path to the controller file if still empty
			if (empty($info['js_controller_path']))
			{
				$info['js_controller_path'] = js_path('', FUEL_FOLDER);
			}



			if ($create_action_name = lang('module_'.$module.'_create'))
			{
				$info['create_action_name'] = $create_action_name;
			}
			$this->_info = $info;
		}
		
		if (empty($prop))
		{
			return $this->_info;
		}
		else if (isset($this->_info[$prop]))
		{
			return $this->_info[$prop];
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the pages of a module
	 *
	 * @access	public
	 * @return	array
	 */	
	function set_info($key, $prop)
	{
		$info = $this->info();
		if (is_array($key))
		{
			foreach($key as $k => $v)
			{
				if (isset($this->_info[$k]))
				{
					$this->_info[$k] = $v;
				}
				else
				{
					throw new Exception(lang('error_class_property_does_not_exist', $k));
				}
			}
		}
		else
		{
			if (isset($this->_info[$key]))
			{
				return $this->_info[$key];
			}
			else
			{
				throw new Exception(lang('error_class_property_does_not_exist', $key));
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the pages of a module
	 *
	 * @access	public
	 * @return	array
	 */	
	function __get($var)
	{
		$info = $this->info();
		if (isset($info[$var]))
		{
			return $info[$var];
		}
		else
		{
			throw new Exception(lang('error_class_property_does_not_exist', $var));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the pages of a module
	 *
	 * @access	public
	 * @return	array
	 */	
	function pages()
	{
		$pages = array();
		$info = $this->info();
		if (!empty($info['model_location']))
		{
			$this->CI->load->module_model($info['model_location'], $info['model_name']);
		}
		else
		{
			$this->CI->load->model($info['model_name']);
		}
		
		$model = $info['model_name'];
		
		$records = array();
		if (method_exists($model, 'find_all_array'))
		{
			$records = $this->CI->$model->find_all_array();
		}
		
		foreach($records as $record)
		{
			// need to put in global namesapce for preg_replace_callback to access
			preg_match_all('#{(\w+)}#', $info['preview_path'], $matches);
			$page = $info['preview_path'];
			$replaced = FALSE;
			if (!empty($matches[1]))
			{
				foreach($matches[1] as $match)
				{
					if (!empty($record[$match]))
					{
						$page = str_replace('{'.$match.'}', $record[$match], $page);
						$replaced = TRUE;
					}
				}
			}

			if (!empty($replaced))
			{
				$pages[$page] = $page;
			}
		}
		return $pages;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * The server path to a module
	 *
	 * @access	public
	 * @param	string	module name
	 * @return	string
	 */	
	static function module_path($module)
	{
		return MODULES_PATH.$this->module.'/';
	}

	

}
/* End of file Fuel_modules.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_modules.php */