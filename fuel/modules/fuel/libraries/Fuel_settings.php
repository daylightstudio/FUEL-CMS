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
 * FUEL settings object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_sitevariables
 */

// --------------------------------------------------------------------

class Fuel_settings extends Fuel_base_library {
	
	protected $settings = array();
	
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->fuel->load_model('settings', '', 'fuel_settings_model');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of settings related to a particular module
	 *
	 * @access public
	 * @param string $module, Module name
	 * @param string $key, Key name
	 * @return array
	 */
	function get($module, $key = '')
	{
		if ( ! array_key_exists($module, $this->settings)) {
			$this->settings[$module] = $this->CI->fuel_settings_model->options_list('fuel_settings.key', 'fuel_settings.value', array('module' => $module), 'key');
		}
		if ( ! empty($key) AND array_key_exists($key, $this->settings[$module]))
		{
			return $this->settings[$module][$key];
		}
		else
		{
			return $this->settings[$module];
		}
	}

	function process($module, $settings)
	{
		if ( ! empty($_POST['settings']) AND ! empty($module))
		{
			// clear out old settings
			$this->CI->fuel_settings_model->delete(array('module' => $module));
			
			// format data for saving
			$save = array();
			$new_settings = $this->CI->input->post('settings', TRUE);
			foreach ($settings as $key => $field_config)
			{
				$new_value = '';
				// set checkbox settings to 0 by default
				if (array_key_exists('type', $field_config) AND ($field_config['type'] == 'checkbox') AND ! array_key_exists($key, $new_settings))
				{
					$new_value = 0;
				}
				else
				{
					$new_value = trim($new_settings[$key]);
					if (empty($new_value)) {
						continue;
					}
				}
				$save[] = array(
					'module' => $module,
					'key'    => $key,
					'value'  => $new_value,
					);
			}
			$this->CI->fuel_settings_model->save($save);
			return TRUE;
		}
		return FALSE;
	}

	function get_validation()
	{
		$validation = &$this->CI->fuel_settings_model->get_validation();
		return $validation;
	}

}

/* End of file Fuel_settings.php */
/* Location: ./modules/fuel/libraries/Fuel_settings.php */