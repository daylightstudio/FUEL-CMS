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
	protected $fuel;
	
	function __construct()
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
	}
	
	function wysiwyg($params)
	{
		$form_builder =& $params['instance'];
		return $form_builder->create_textarea($params);
	}
	
	function file($params)
	{
		$form_builder =& $params['instance'];
		if (!empty($params['multiple']))
		{
			$params['class'] = 'multifile '.$params['class'];
		}
		return $form_builder->create_file($params);
	}

	function asset($params)
	{
		$this->CI->load->helper('file');
		$this->CI->load->helper('html');
		
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
		
		// set the image preview containing class
		if (empty($params['img_display_class']))
		{
			$params['img_container_styles'] = 'overflow: auto; height: 200px; width: 400px; margin-top: 5px;';
		}

		// set the styles specific to the image
		if (empty($params['img_styles']))
		{
			$params['img_styles'] = 'float: right; width: 100px;';
		}
		
		// folders and intended contents
		$editable_filetypes = $this->fuel->config('editable_asset_filetypes');
		
		if (!empty($params['value']))
		{
			$preview = '<br /><div style="'.$params['img_container_styles'].'">';
			
			if (is_string($params['value']))
			{
				// unserialize if it is a serialized string
				if (is_serialized_str($params['value']))
				{
					$assets = unserialize($params['value']);
				}
				else
				{
					$assets = preg_split('#\s*,\s*#', $params['value']);
				}
				
				// loop through all the assets and concatenate them
				foreach($assets as $caption => $asset)
				{
					foreach($editable_filetypes as $folder => $regex)
					{
						if (!is_http_path($asset))
						{
							if (preg_match('#'.$regex.'#i', $asset))
							{
								$asset_path = assets_path($folder.'/'.$asset);
								break;
							}
						}
						else
						{
							$asset_path = $asset;
						}
					}

					$preview .= '<a href="'.$asset_path.'" target="_blank">';
						
					if (is_image_file($asset))
					{
						$preview .= '<img src="'.$asset_path.'" style="'.$params['img_styles'].'"/>';
					}
					else
					{
						$preview .= $asset;
					}
					$preview .= '</a>';
				}
				
			}
			$preview .= '</div><div class="clear"></div>';
			$params['after_html'] = $preview;
		}
		$params['type'] = '';
		$str = $form_builder->create_text($params);
		$str .= $params['after_html'];
		return $str;
	}

	function inline_edit($params)
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

	function linked($params)
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

	function fillin($params)
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

	function number($params)
	{
		$form_builder =& $params['instance'];
		return $form_builder->create_number($params);
	}
	
	function email($params)
	{
		$form_builder =& $params['instance'];
		return $form_builder->create_email($params);
	}
	
	function template($params)
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
		
		$params['depth'] = (empty($params['depth'])) ? 0 : $params['depth']++;
		
		if (is_serialized_str($params['value']))
		{
			$params['value'] = unserialize($params['value']);
		}
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
				$field['value'] = (!empty($value[$key])) ? $value[$key] : '';
				
				// Sorry... field type can't be'another template at the moment... would be cool though
				if (isset($field['type']) AND $field['type'] == 'template')
				{
					continue;
				}
				
				if ($repeatable)
				{
					$field_name_key = (!empty($form_builder->name_array)) ? 'name' : 'orig_name';
					
					if (isset($field['type']) AND $field['type'] == 'file')
					{
						$field['name'] = $params[$field_name_key].'_'.$i.'_'.$key;
					}
					else
					{
						$field['name'] = $params[$field_name_key].'['.$i.']['.$key.']';
					}

					$field['id'] = preg_replace('#(.+)\[\d\](.+)#U', '$1[{index}]$2', $field['name']);
					$field['id'] = Form::create_id($field['id']);
					
					// need IDS for some plugins like CKEditor... not sure yet how to clone an element with a different ID
					//$field['id'] = FALSE;
					
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
	
	function nested($params)
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