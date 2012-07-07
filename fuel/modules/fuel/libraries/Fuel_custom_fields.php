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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * FUEL custom fields object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/general/forms
 * @autodoc		FALSE
 */

// --------------------------------------------------------------------

class Fuel_custom_fields {
	

	protected $CI;
	protected $fuel;
	
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
	function __construct()
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
	}
	
	function wysiwyg($params)
	{
		$form_builder =& $params['instance'];
		if (isset($params['editor']))
		{
			if ($params['editor'] === FALSE)
			{
				$params['class'] = 'no_editor';
			}
			else if (strtolower($params['editor']) == 'ckeditor' OR strtolower($params['editor']) == 'wysiwyg')
			{
				$params['class'] = 'wysiwyg '.$params['class'];
			}
			else if (strtolower($params['editor']) == 'markitup')
			{
				$params['class'] = 'markitup '.$params['class'];
			}
		}
		
		if (isset($params['preview']))
		{
			$params['data'] = array('preview' => $params['preview']);
		}
		
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
		if (empty($params['img_container_styles']))
		{
			$params['img_container_styles'] = 'overflow: auto; height: 200px; width: 400px; margin-top: 5px;';
		}

		// set the styles specific to the image
		if (!isset($params['img_styles']))
		{
			$params['img_styles'] = 'float: left; width: 100px;';
		}
		
		// folders and intended contents
		$editable_filetypes = $this->fuel->config('editable_asset_filetypes');
		
		// set data parameters so that we can use them with the JS
		
		// set multiple and separator data attributes so can be used by javascript
		$multiple = (!empty($params['multiple']) OR strpos($params['value'], ',') !== FALSE) ? 1 : 0;
		
		// set the separator based on if it is multiple lines or just a single line
		$separator = (isset($params['multiline']) AND $params['multiline'] === TRUE) ? "\n" : ', ';

		$params['data'] = array(
			'multiple' => $multiple,
			'separator' => $separator,
			);
			
		if (!empty($params['value']))
		{
			if (is_string($params['value']))
			{
				// unserialize if it is a serialized string
				if (is_json_str($params['value']))
				// if (is_serialized_str($params['value']))
				{
					$assets = json_decode($params['value'], TRUE);
					//$assets = unserialize($params['value']);
				}
				else
				{
					// create assoc array with key being the image and the value being either the image name again or the caption
					$assets = preg_split('#\s*,\s*|\n#', $params['value']);
					
				}
				$preview_str = '';

				// loop through all the assets and concatenate them
				foreach($assets as $asset)
				{
					if (!empty($asset))
					{
						$asset_path = '';

						foreach($editable_filetypes as $folder => $regex)
						{
							if (!is_http_path($asset))
							{
								if (preg_match('#'.$regex.'#i', $asset))
								{
									$path = trim($params['folder'], '/').'/'.$asset;
									$asset_path = assets_path($path);
									break;
								}
							}
							else
							{
								$asset_path = $asset;
							}
						}

						if (!empty($asset_path))
						{
							$preview_str .= '<a href="'.$asset_path.'" target="_blank">';

							if (is_image_file($asset))
							{
								$preview_str .= '<img src="'.$asset_path.'" style="'.$params['img_styles'].'"/>';
							}
							else
							{
								$preview_str .= $asset;
							}
							$preview_str .= '</a>';
						}
					}
				}
			}
			$preview = '';
			if (!empty($preview_str))
			{
				$img_container_styles = $params['img_container_styles'];
				if ($multiple == FALSE AND !empty($params['img_styles']))
				{
					$img_container_styles = $params['img_styles'];
				}
				
				$preview = '<br /><div class="noclone" style="'.$img_container_styles.'">';

				$preview .= $preview_str;
				$preview .= '</div><div class="clear"></div>';
			}
			$params['after_html'] = $preview;
		}
		$params['type'] = '';
		
		if ($multiple)
		{
			// create an array with the key being the image name and the value being the caption (if it exists... otherwise the image name is used again)
			if (isset($params['subkey']))
			{
				$func_str = '
					if (is_array($value))
					{
						foreach($value as $key => $val)
						{
							if (isset($val["'.$params['subkey'].'"]))
							{
								$val = trim($val["'.$params['subkey'].'"]);
								$assets = array();
								$assets_arr = preg_split("#\s*,\s*|\n#", $val);
								if (count($assets_arr) > 1)
								{
									$value[$key]["'.$params['subkey'].'"] = json_encode($assets_arr);
								}
								else
								{
									$value[$key]["'.$params['subkey'].'"] = $val;
								}
							}
						}
						return $value;
					}
					';
			}
			else
			{
				
				$func_str = '
					$value = trim($value);
					$assets_arr = preg_split("#\s*,\s*|\n#", $value);
					if (count($assets_arr) > 1)
					{
						return json_encode($assets_arr);
					}
					else
					{
						return $value;
					}
					';
			}

			$func = create_function('$value', $func_str);
			$form_builder->set_post_process($params['key'], $func);
		}
		
		// unserialize value if it's serialized
		//$value = (is_serialized_str($params['value'])) ? unserialize($params['value']) : $params['value'];
		$value = (is_string($params['value']) AND is_json_str($params['value'])) ? json_decode($params['value'], TRUE) : $params['value'];

		if (is_array($value))
		{
			$params['value'] = '';
			foreach($value as $key => $val)
			{
				if (!empty($val))
				{
					$params['value'] .= $val.$separator;
				}
			}
		}
		$params['value'] = trim($params['value'], ",\n ");
		
		//$params['comment'] = 'Add a caption value to your image by inserting a colon after the image name and then enter your caption like so: my_img.jpg:My caption goes here.';
		
		
		// data params
		$data_params['asset_folder'] = $params['folder'];
		$data_params['subfolder'] = (isset($params['subfolder'])) ? $params['subfolder'] : '';
		$data_params['userfile_file_name'] = (isset($params['file_name'])) ? $params['file_name'] : '';
		$data_params['overwrite'] = (isset($params['overwrite'])) ? (bool)$params['overwrite'] : TRUE;
		$data_params['create_thumb'] = (isset($params['create_thumb'])) ? (bool)$params['create_thumb'] : FALSE;
		$data_params['maintain_ratio'] = (isset($params['maintain_ratio'])) ? (bool)$params['maintain_ratio'] : FALSE;
		$data_params['width'] = (isset($params['width'])) ? (int)$params['width'] : '';
		$data_params['height'] = (isset($params['height'])) ? (int)$params['height'] : '';
		$data_params['master_dimension'] = (isset($params['master_dimension'])) ? $params['master_dimension'] : '';
		$data_params['hide_options'] = (isset($params['hide_options'])) ? (bool)$params['hide_options'] : FALSE;
		
		if (isset($params['hide_image_options']))
		{
			$data_params['hide_image_options'] = (isset($params['hide_image_options'])) ? (bool)$params['hide_image_options'] : FALSE;
		}
		else if (!isset($params['hide_image_options']) AND !preg_match('#^images#', $params['folder']))
		{
			$data_params['hide_image_options'] = TRUE;
		}

		$params['data']['params'] = http_build_query($data_params);
		
		if (!empty($params['multiline']))
		{
			$params['class'] = 'no_editor '.$params['class'];
			if (empty($params['style']))
			{
				$params['style'] = 'float: left; width: 400px; height: 60px';
			}
			$str = $form_builder->create_textarea($params);
		}
		else
		{
			$str = $form_builder->create_text($params);
		}
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
				$module = $CI->fuel->modules->get($params['module'], FALSE);
				$uri = (!empty($module)) ? $module->info('module_uri') : '';
			}
			else
			{
				$uri = $params['module'];
			}

			if ($this->fuel->auth->has_permission($uri))
			{
				$inline_class = 'add_edit '.$uri;
				$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$inline_class : $inline_class;
			}
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

	function template($params)
	{
		$this->CI->load->library('parser');
		$form_builder =& $params['instance'];
		
		$str = '';
		if (empty($params['fields']))
		{
			return $str;
		}

		// set the ID to have a placehoder that the js can handle
		$repeatable = (isset($params['repeatable']) AND $params['repeatable'] === TRUE) ? TRUE : FALSE;
		$add_extra = (isset($params['add_extra']) AND $params['add_extra'] === TRUE) ? TRUE : FALSE;
		
		$fields = array();
		$i = 0;
		
		if (!isset($params['depth']))
		{
			$params['depth'] = 0;
		}

		// set the value
		if (is_string($params['value']) AND is_json_str($params['value']))
		{
			$params['value'] = json_decode($params['value'], TRUE);
		}
		
		// set maximum limit
		if (!isset($params['max']))
		{
			$params['max'] = NULL;
		}
		
		// set minimum limit
		if (!isset($params['min']))
		{
			$params['min'] = NULL;
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
		
		if (isset($params['min']) AND $num < $params['min'])
		{
			$num = $params['min'];
		}

		if (!isset($params['display_sub_label']))
		{
			$params['display_sub_label']  = TRUE;
		}
		
		if ($num == 0) $num = 1;
		
		$_f = array();
		
		for ($i = 0; $i < $num; $i++)
		{
			$value = (isset($params['value'][$i])) ? $params['value'][$i] : $params['value'];
			
			foreach($params['fields'] as $key => $field)
			{
				if (!empty($value[$key]))
				{
					$field['value'] = $value[$key];
				}
				else if (empty($field['value']))
				{
					$field['value'] = '';
				}

				// Sorry... template can only be nested once... which should be all you need
				if (isset($field['type']) AND $field['type'] == 'template' AND $params['depth'] > 1)
				{
					continue;
				}
				
				if (!isset($field['label']))
				{
					if ($lang = $form_builder->label_lang($key))
					{
						$field['label'] = $lang;
					}
					else
					{
						$field['label'] = ucfirst(str_replace('_', ' ', $key));
						
					}
				}
				
				if ($repeatable)
				{
					$field_name_key = (!empty($form_builder->name_array)) ? 'name' : 'orig_name';
					
					// set file name field types to not use array syntax for name so they can be processed automagically
					if (isset($field['type']) AND $field['type'] == 'file')
					{
						$field['name'] = $params[$field_name_key].'_'.$i.'_'.$key;
					}
					else
					{
						$field['name'] = $params[$field_name_key].'['.$i.']['.$key.']';
					}
					
					// set the key to be the same of the parent... so post processing will work
					$field['key'] = $params['key'];
					$field['subkey'] = $key;
					$field['depth'] = $params['depth'] + 1;
					$depth_css_class = ' field_depth_'.$params['depth'];
					$field['class'] = (!empty($field['class'])) ? $field['class'].' '.$depth_css_class : $depth_css_class;
					
					// set placeholders in field ids for javascript to translate... must be last occurence of the digit
					$field['id'] = preg_replace('#([-_a-zA-Z0-9\[\]]+)\[\d+\](\[[-_a-zA-Z0-9]+\])$#U', '$1[{index}]$2', $field['name']);
					$field['id'] = Form::create_id($field['id']);
					$field['display_label'] = $params['display_sub_label'];
					
					// need IDS for some plugins like CKEditor... not sure yet how to clone an element with a different ID
					//$field['id'] = FALSE;
					$_f[$i][$key] = $field;
					$fields[$i][$key] = $form_builder->create_field($field);
					$fields[$i]['__index__'] = $i;
					$fields[$i]['__num__'] = $i + 1;
					$fields[$i]['__title__'] = (isset($params['title_field']) AND !empty($value[$params['title_field']])) ? strip_tags($value[$params['title_field']]) : '';
					
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
					$field['display_label'] = $params['display_sub_label'];
					$_f[$key] = $field;
					$fields[$key] = $form_builder->create_field($field);
				}
			}
		}
		
		$vars['values'] = $params['value'];
		$vars['fields_config'] = $params['fields'];
		if ($repeatable)
		{
			$vars['fields'] = $fields;
		}
		else
		{
			$vars = $fields;
		}
		
		
		if (!empty($params['serialize']))
		{
			
			// must set $_POST parameter below or else the post_process won't run the serialization'
			if (!isset($_POST[$params['key']]))
			{
				$_POST[$params['key']] = '';
			}

			$func_str = '$CI =& get_instance();
			$val = $CI->input->post("'.$params['key'].'");
			if (isset($_POST["'.$params['key'].'"]) AND is_array($val))
			{
				//return serialize($val); // issues with multibyte characters
				// foreach($_POST["'.$params['key'].'"] as $key => $val)
				// {
				// 	$CI->form_builder->post_process_field_values($val);
				// }
				return json_encode($val);
			}
			else
			{
				$_POST["'.$params['key'].'"] = "";
				return "";
			}
			';

			$func = create_function('$value', $func_str);
//			$form_builder->set_post_process($params['key'], $func);
		}
		
		
		if (empty($params['template']) AND !empty($params['view']))
		{
			$str = $this->CI->load->view($params['view'], $vars, TRUE);
		}
		else if (!empty($params['template']))
		{
			$str = $params['template'];
		}
		else
		{
			$form_params['init'] = (!empty($params['form_builder_params'])) ? $params['form_builder_params'] : array();
			
			// auto fill properties from parent form builder
			$init = array('name_array', 'name_prefix');
			foreach($init as $in)
			{
				if (!isset($form_params['init'][$in]))
				{
					$form_params['init'][$in] = $form_builder->$in;
				}
			}

			if ($repeatable)
			{
				$css_class = (!empty($params['depth'])) ? ' child':  '';
				$dblclick = (!empty($params['dblclick'])) ? $params['dblclick'] : 0;
				$init_display = (!empty($params['init_display'])) ? $params['init_display'] : '';
				$title_field = (!empty($params['title_field'])) ? $params['title_field'] : '';
				$str .= '<div class="repeatable_container'.$css_class.'" data-depth="'.$params['depth'].'" data-max="'.$params['max'].'" data-min="'.$params['min'].'" data-dblclick="'.$dblclick.'" data-init_display="'.$init_display.'" data-title_field="'.$title_field.'">';
				$i = 0;

				foreach($_f as $k => $f)
				{
					foreach($f as $kk => $ff)
					{
						if (isset($ff['type']) AND $ff['type'] == 'section')
						{
							$value = $form_builder->simple_field_value($ff);
							if (isset($params['title_field'], $f[$params['title_field']]))
							{
								$heading = str_replace('{__title__}', $f[$params['title_field']]['value'], $value);
							}
							unset($f[$kk]);
							//$f[$kk]['label'] = $heading;
						}
					}
					$form_params['fields'] = $f;
					$form_obj = $form_builder->create_nested($form_params, TRUE);
					$form = $form_obj->render();
					$css_class = ($i > 0) ? ' noclone' : '';
					if (!empty($params['float']) AND $params['depth'] == 0)
					{
						$css_class = ' float_left';
					}

					$depth_suffix = ($params['depth'] > 0) ? '_'.$params['depth'] : '';

					$style = (!empty($params['style'])) ? ' style="'.$params['style'].'"' : '';
					
					$str .= '<div class="repeatable'.$css_class.'" data-index="'.$i.'"'.$style.'>';
					$str .= '<h3 class="grabber" title="'.lang('tooltip_dbl_click_to_open').'">';
					if (!empty($heading)) 
					{
						$str .= '<span class="title'.$depth_suffix.'"></span>';
					}
					$str .= '</h3>';
					$str .= '<div class="repeatable_content">';
					$str .= $form;
					$str .= '</div>';
					$str .= '</div>';
					$i++;
				}
				$str .= '<div class="clear"></div></div>';
			}
			else
			{
				$form_params['fields'] = $_f;
				$form = $form_builder->create_nested($form_params);
				$str .= $form;
				$str .= '</div></div>';
			}
			return $str;
		}
		
		// parse the string
		if (!isset($params['parse']) OR $params['parse'] === TRUE)
		{
			$str = $this->CI->parser->parse_simple($str, $vars);
		}
		
		return $str;
	}
	
	function currency($params)
	{
		$form_builder =& $params['instance'];
		
		if (empty($params['size']))
		{
			$params['size'] = 10;
		}

		if (empty($params['max_length']))
		{
			$params['max_length'] = 10;
		}
		
		$currency = (isset($params['currency'])) ? $params['currency'] : '$';
		
		$data_vals = array('separator', 'decimal', 'grouping', 'min', 'max');
		$params['data'] = array();
		foreach($data_vals as $val)
		{
			if (isset($params[$val]))
			{
				$params['data'][$val] = $params[$val];
			}
		}
		
		$params['class'] = (!empty($params['class'])) ? 'currency '.$params['class'] : 'currency';
		
		$params['type'] = 'text';

		// set data values for jquery plugin to use
		return $currency.' '.$form_builder->create_text($params);
	}
	
	function state($params)
	{
		include(APPPATH.'config/states.php');
		$form_builder =& $params['instance'];
		
		$params['options'] = $states;
		
		// set data values for jquery plugin to use
		return $form_builder->create_select($params);
	}
	
	function slug($params)
	{
		$form_builder =& $params['instance'];
		
		// create an array with the key being the image name and the value being the caption (if it exists... otherwise the image name is used again)
		if (isset($params['subkey']))
		{
			$func_str = '
				if (is_array($value))
				{
					foreach($value as $key => $val)
					{
						if (isset($val["'.$params['subkey'].'"]))
						{
							$val = url_title($val["'.$params['subkey'].'"], "dash", TRUE);
							$value[$key]["'.$params['subkey'].'"] = $val;
						}
					}
					return $value;
				}
				';
				$func = create_function('$value', $func_str);
		}
		else
		{
			$func = array('url_title', 'dash', TRUE);
		}
		
		$form_builder->set_post_process($params['key'], $func);
		
		// assume a default is either name or title
		if (empty($params['linked_to']))
		{
			$fields = $form_builder->fields();
			if (isset($fields['title']))
			{
				$params['linked_to'] = 'title';
			}
			else if (isset($fields['name']))
			{
				$params['linked_to'] = 'name';
			}
		}
		$params['type'] = 'text';
		
		// set data values for jquery plugin to use
		return $this->linked($params);
	}

	function list_items($params)
	{
		$form_builder =& $params['instance'];
		
		// ugly... just strips the whitespace on multilines ... http://stackoverflow.com/questions/1655159/php-how-to-trim-each-line-in-a-heredoc-long-string
		$params['value'] = trim_multiline(strip_tags($params['value']));
		
		$output_class = (!empty($params['output_class'])) ? 'class="'.$params['output_class'].'"' : '';
		
		if (isset($params['subkey']))
		{
			$func_str = '
				if (is_array($value))
				{
					foreach($value as $key => $val)
					{
						if (isset($val["'.$params['subkey'].'"]))
						{
							$lis = explode("\n", $value);
							$lis = array_map("trim", $lis);
							$val = ul($lis, "'.$output_class.'");
							$value[$key]["'.$params['subkey'].'"] = $val;
						}
					}
					return $value;
				}
				';
		}
		else
		{
			$func_str = '$lis = explode("\n", $value);
				$lis = array_map("trim", $lis);
				return ul($lis, "'.$output_class.'");';
		}
		
		$func = create_function('$value', $func_str);
		$form_builder->set_post_process($params['key'], $func);
		$params['class'] = 'no_editor';
		return $form_builder->create_textarea($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the multi select input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	function multi($params)
	{
		$form_builder =& $params['instance'];
		
		$defaults = array(
			'sorting' => NULL,
			'options' => array(),
			'mode' => NULL,
			'model' => NULL,
			'model_params' => NULL,
			'wrapper_tag' => 'span',// for checkboxes
			'wrapper_class' => 'multi_field',
			'module' => NULL,
		);

		$params = $form_builder->normalize_params($params, $defaults);
		
		// grab options from a model if a model is specified
		if (!empty($params['model']))
		{
			$model_params = (!empty($params['model_params'])) ? $params['model_params'] : array();
			$params['options'] = $form_builder->options_from_model($params['model'], $model_params);
		}
		
		if (!empty($params['module']))
		{
			if (strpos($params['module'], '/') === FALSE)
			{
				$CI =& get_instance();
				$module = $CI->fuel->modules->get($params['module'], FALSE);
				$uri = (!empty($module)) ? $module->info('module_uri') : '';
			}
			else
			{
				$uri = $params['module'];
			}
			if (!empty($params['module']) AND $this->fuel->auth->has_permission($uri))
			{
				$inline_class = 'add_edit '.$uri;
				$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$inline_class : $inline_class;
			}
		}
		
		$str = '';
		$mode = (!empty($params['mode'])) ? $params['mode'] : $form_builder->multi_select_mode;
		if ($mode == 'checkbox' OR ($mode == 'auto' AND (isset($params['options']) AND count($params['options']) <= 5)))
		{
			$value = (isset($params['value'])) ? (array)$params['value'] : array();

			$params['name'] = $params['name'].'[]';
			$i = 1;
			
			if (!empty($params['options']))
			{
				foreach($params['options'] as $key => $val)
				{
					$str .= '<'.$params['wrapper_tag'].' class="'.$params['wrapper_class'].'">';
					$attrs = array(
											'readonly' => $params['readonly'], 
											'disabled' => $params['disabled'],
											'id' => Form::create_id($params['name']).$i,
											'style' => '' // to overwrite any input width styles
					
										);
					
										if (in_array($key, $value))
										{
											$attrs['checked'] = 'checked';
					
										}
										$str .= $form_builder->form->checkbox($params['name'], $key, $attrs);

					$label = ($lang = $form_builder->label_lang($attrs['id'])) ? $lang : $val;
					$enum_params = array('label' => $label, 'name' => $attrs['id']);
					$str .= ' '.$form_builder->create_label($enum_params);
					$str .= "&nbsp;&nbsp;&nbsp;";
					$str .= '</'.$params['wrapper_tag'].'>';
					$i++;
				}
			}
		}
		else
		{
			$params['multiple'] = TRUE;
			$str .= $form_builder->create_select($params);
			if (!empty($params['sorting']))
			{
				if ($params['sorting'] === TRUE AND is_array($params['value']))
				{
					$params['sorting'] = $params['value'];
				}
				$sorting_params['name'] = 'sorting_'.$params['orig_name'];
				$sorting_params['value'] = rawurlencode(json_encode($params['sorting']));
				$sorting_params['class'] = 'sorting';
				$str .= $form_builder->create_hidden($sorting_params);
			}
		}
		
		return $str;
	}

		// --------------------------------------------------------------------

	/**
	 * Creates the url input select
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	function url($params)
	{
		$form_builder =& $params['instance'];
		
		$defaults = array(
		);
		$url_class = 'url_select';
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$url_class : $url_class;
		if (empty($params['size']))
		{
			$params['size'] = 90;
		}

		$data = array();
		if (isset($params['input']))
		{
			$data['target'] = $params['input'];
		}

		if (isset($params['target']))
		{
			$data['target'] = $params['target'];
		}
		if (isset($params['title']))
		{
			$data['title'] = $params['title'];
		}
		$params['data'] = $data;
		return $form_builder->create_text($params);

	}
}



/* End of file Fuel_custom_fields.php */
/* Location: ./modules/fuel/libraries/Fuel_custom_fields.php */