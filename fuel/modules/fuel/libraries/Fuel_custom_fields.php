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
		$this->CI->load->helper('file');
		
		$form_builder =& $params['instance'];
		if (empty($params['folder']))
		{
			$params['folder'] = 'images';
		}
		$asset_class = 'asset_select';
		if (!isset($params['upload']) OR (isset($params['upload']) AND $params['upload'] !== FALSE))
		{
			$asset_class .= ' asset_upload';
		}
		$asset_class .= ' '.$params['folder'];
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$asset_class : $asset_class;
		
		if (!empty($params['value']) AND is_image_file($params['value']))
		{
			$img = '<div class="img_display"><img src="'.img_path($params['value']).'" style="float: right;"/></div><div class="clear"></div>';
			$params['after_html'] = $img;
		}
		$params['type'] = '';
		return $form_builder->create_field($params);
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

	// public function number($params)
	// {
	// 	$form_builder =& $params['instance'];
	// 	
	// 	$numeric_class = 'numeric';
	// 	$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$numeric_class : $numeric_class;
	// 	
	// 	$decimal = (!empty($params['decimal'])) ? 1 : 0;
	// 	$negative = (!empty($params['negative'])) ? 1 : 0;
	// 	
	// 	if (empty($params['size']))
	// 	{
	// 		$params['size'] = 10;
	// 	}
	// 
	// 	if (empty($params['max_length']))
	// 	{
	// 		$params['max_length'] = 10;
	// 	}
	// 
	// 	// set data values for jquery plugin to use
	// 	$params['data'] = array(
	// 		'decimal' => $decimal,
	// 		'negative' => $negative,
	// 		);
	// 	return $form_builder->create_text($params);
	// }
	// 
	// public function email($params)
	// {
	// 	$form_builder =& $params['instance'];
	// 
	// 	$email_class = 'email';
	// 	$params['type'] = 'email';
	// 	$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$email_class : $email_class;
	// 	return $form_builder->create_text($params);
	// }
	// 
	public function template($params)
	{
		$this->CI->load->library('parser');
		$form_builder =& $params['instance'];
		
		$str = '';
		if (empty($params['fields']) OR (empty($params['template']) AND empty($params['view'])))
		{
			return $str;
		}
		
		if (empty($params['template']) AND !empty($params['view']))
		{
			$str = $this->CI->load->view($params['view'], $params['value'], TRUE);
		}
		else
		{
			$str = $params['template'];
		}
		
		// set the ID to have a placehoder that the js can handle
		$repeatable = (isset($params['repeatable']) AND $params['repeatable'] === TRUE) ? TRUE : FALSE;
		$add_extra = (isset($params['add_extra']) AND $params['add_extra'] === TRUE) ? TRUE : FALSE;
		
		$fields = array();
		$i = 0;
		
		if (!is_array($params['value']))
		{
			$params['value'] = array();
		}
		
		if ($params['value'] == '')
		{
			$params['value'] = array();
		}
		
		$num = ($add_extra) ? count($params['value']) + 1 : count($params['value']);
		if ($num == 0) $num = 1;
		
		for ($i = 0; $i < $num; $i++)
		{
			$value = (isset($params['value'][$i])) ? $params['value'][$i] : $params['value'];
			foreach($params['fields'] as $key => $field)
			{
				$field['value'] = (isset($value[$key])) ? $value[$key] : $value;
				
				if ($repeatable)
				{
					if (!empty($form_builder->name_array))
					{
						$field['name'] = $params['name'].'['.$i.']['.$key.']';
					}
					else
					{
						$field['name'] = $params['orig_name'].'['.$i.']['.$key.']';
					}
					
					$field['id'] = preg_replace('#(.+)\[\d\](.+)#U', '$1[{index}]$2', $field['name']);
					$field['id'] = Form::create_id($field['id']);
					
					$fields[$i][$key] = $form_builder->create_field($field);
					$fields[$i]['index'] = $i;
					$fields[$i]['num'] = $i + 1;
				}
				else
				{
					if (!empty($form_builder->name_array))
					{
						$field['name'] = $params['name'].'['.$key.']';
					}
					else
					{
						$field['name'] = $params['orig_name'].'['.$key.']';
					}
					$fields[$key] = $form_builder->create_field($field);
				}
			}
		}
		
		if ($repeatable)
		{
			$vars['fields'] = $fields;
		}
		else
		{
			$vars = $fields;
		}
		
		// parse the string
		if (!isset($params['parse']) OR $params['parse'] === TRUE)
		{
			$str = $this->CI->parser->parse_simple($str, $vars);
		}
		
		return $str;
	}
	
	public function nested($params)
	{
		$this->CI->load->library('parser');
		$form_builder =& $params['instance'];
		
		$fb_class = get_class($form_builder);
		
		if (empty($params['fields']) OR !is_array($params['fields']))
		{
			return '';
		}
		if (empty($params['init']))
		{
			$params['init'] = array();
		}
		
		if (empty($params['value']))
		{
			$params['value'] = array();
		}
		
		$fb = new $fb_class($params['init']);
		$fb->set_fields($params['fields']);
		$fb->submit_value = '';
		$fb->set_validator($form_builder->form->validator);
		$fb->use_form_tag = FALSE;
		$fb->set_field_values($params['value']);
		return $fb->render();
	}
	
	function currency($params)
	{
		$form_builder =& $params['instance'];
		
		if (empty($params['size']))
		{
			$params['size'] = 7;
		}

		if (empty($params['max_length']))
		{
			$params['max_length'] = 7;
		}
		
		$currency = (isset($params['currency'])) ? $params['currency'] : '$';
		
		// set data values for jquery plugin to use
		return $currency.' '.$form_builder->create_number($params);
	}
}



/* End of file Fuel.php */
/* Location: ./modules/fuel/libraries/Fuel.php */