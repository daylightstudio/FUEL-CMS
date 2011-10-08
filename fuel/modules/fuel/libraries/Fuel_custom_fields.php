<?php
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
 * FUEL master admin object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel
 */

// --------------------------------------------------------------------

class Fuel_custom_fields {
	

	protected $CI;
	public $datetime_js = 'datetime_field';
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	public function datetime($params)
	{
		$form_builder =& $params['instance'];
		$params['class'] = 'datepicker '.$params['class'];
		$str = $form_builder->create_date($params);
		$str .= ' ';
		$str .= $form_builder->create_time($params);
		return $str;
	}

	public function multi($params)
	{
		$form_builder =& $params['instance'];
		return $form_builder->create_multi($params);
	}
	
	public function wysiwyg($params)
	{
		$form_builder =& $params['instance'];
		return $form_builder->create_textarea($params);
		
	}
	
	public function file($params)
	{
		$form_builder =& $params['instance'];
		return $form_builder->create_file($params);
	}

	public function asset($params)
	{
		$form_builder =& $params['instance'];
		if (empty($params['folder']))
		{
			$params['folder'] = 'images';
		}
		$asset_class = 'asset_select '.$params['folder'];
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$asset_class : $asset_class;
		return $form_builder->create_text($params);
	}

	public function inline_edit($params)
	{
		$form_builder =& $params['instance'];
		
		if (!empty($params['module']))
		{
			if (strpos($params['module'], '/') === FALSE)
			{
				$CI =& get_instance();
				$module = $CI->fuel->modules->get($params['module']);
				$uri = $module->info('module_uri');
			}
			else
			{
				$uri = $params['module'];
			}
			$inline_class = 'add_edit '.$uri;
			$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$inline_class : $inline_class;
		}
		
		if (!empty($params['multiple']))
		{
			$params['mode'] = 'multi';
			$field = $form_builder->create_multi($params);
		}
		else
		{
			$field = $form_builder->create_select($params);
		}
		
		return $field;
		
	}

	public function linked($params)
	{
		$form_builder =& $params['instance'];
		$str = '';
		if (!empty($params['linked_to']))
		{
			$linked_class = 'linked';
			$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$linked_class : $linked_class;
			$str .= "<div class=\"linked_info\" style=\"display: none;\">";
			$str .= json_encode($params['linked_to']);
			$str .= "</div>\n";
		}
		$str .= $form_builder->create_text($params);
		return $str;
	}

	public function fillin($params)
	{
		$form_builder =& $params['instance'];
		
		$fillin_class = 'fillin';
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$fillin_class : $fillin_class;
		
		$placeholder = (!empty($params['placeholder'])) ? $params['placeholder'] : $params['default'];
		if (!empty($placeholder))
		{
			$params['placeholder'] = rawurlencode($placeholder);
		}
		
		// requires placeholder value to be set to trigger
		return $form_builder->create_text($params);
	}
	
}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */