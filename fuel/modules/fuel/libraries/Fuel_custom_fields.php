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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
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
 * @link		http://docs.getfuelcms.com/library/form_builder
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
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->fuel =& $this->CI->fuel;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a richer text editor for textarea fields
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function wysiwyg($params)
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
		
		if (!isset($params['data']))
		{
			$params['data'] = $params['data'] = array();
		}
		
		if (isset($params['preview']))
		{
			$params['data']['preview'] = $params['preview'];
		}
		
		// the dimensions for the preview window		
		$params['data']['preview_options'] = (!empty($params['preview_options'])) ? $params['preview_options'] : 'width=1024,height=768';
		
		// set the image folder for inserting assets
		if (isset($params['img_folder']))
		{
			$params['data']['img_folder'] = $params['img_folder'];
		}

		// set the image order in the dropdown select (name or last_updated)
		if (isset($params['img_order']))
		{
			$params['data']['img_order'] = $params['img_order'];
		}

		// adds list of PDFs when selecting a PDF link
		if (isset($params['link_pdfs']))
		{
			$params['data']['link_pdfs'] = 1;
		}

		// adds markdown controlls to the markItUp!  editor
		if (isset($params['markdown']) AND $params['markdown'] === TRUE)
		{
			$params['data']['markdown'] = 1;
		}

		// set markitup config
		if (isset($params['editor_config']) AND is_array($params['editor_config']))
		{
			foreach($params['editor_config'] as $key => $val)
			{
				$params['data'][$key] = $val;
			}
		}

		// set ckeditor configs
		if (isset($params['ckeditor_config']) AND is_array($params['ckeditor_config']))
		{
			foreach($params['ckeditor_config'] as $key => $val)
			{
				$params['data'][$key] = $val;
			}
		}
		
		$js = '<script type="text/javascript">
			myMarkItUpSettings.previewParserPath = "'.fuel_url().'/preview";
		</script>';
		$form_builder->add_js($js, 'markitup_preview_path');
		
		return $form_builder->create_textarea($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a file upload field but has the option to allow multiple fields
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function file($params)
	{
		$form_builder =& $params['instance'];
		if (!empty($params['multiple']))
		{
			$params['class'] = 'multifile '.$params['class'];
		}
		return $form_builder->create_file($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates an asset select/upload field
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function asset($params)
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
		if (!isset($params['multiple']))
		{
			$multiple = !empty($params['multiple']) OR strpos($params['value'], ',') !== FALSE;
		}
		else
		{
			$multiple = $params['multiple'];
		}
		
		// set the separator based on if it is multiple lines or just a single line
		$separator = (isset($params['multiline']) AND $params['multiline'] === TRUE) ? "\n" : ', ';

		$params['data'] = array(
			'multiple' => $multiple,
			'separator' => $separator,
			'folder' => $params['folder'],
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
				else if ($multiple)
				{
					// create assoc array with key being the image and the value being either the image name again or the caption
					$assets = preg_split('#\s*,\s*|\n#', $params['value']);
					
				}
				else
				{
					$assets = array($params['value']);
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
							if (isset($params['is_image']) OR (!isset($params['is_image']) AND is_image_file($asset)))
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
			$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];

			// create an array with the key being the image name and the value being the caption (if it exists... otherwise the image name is used again)
			$func_str = '
				if (is_array($value))
				{
					foreach($value as $key => $val)
					{
						if (isset($val["'.$process_key.'"]))
						{
							$val = trim($val["'.$process_key.'"]);
							$assets = array();
							$assets_arr = preg_split("#\s*,\s*|\n#", $val);
							if (count($assets_arr) > 1)
							{
								$value[$key]["'.$process_key.'"] = json_encode($assets_arr);
							}
							else
							{
								$value[$key]["'.$process_key.'"] = $val;
							}
						}
					}
					return $value;
				}
				else
				{
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
				}
				';
			

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
		$data_params['unzip'] = (isset($params['unzip'])) ? (bool)$params['unzip'] : TRUE;
		$data_params['create_thumb'] = (isset($params['create_thumb'])) ? (bool)$params['create_thumb'] : FALSE;
		$data_params['maintain_ratio'] = (isset($params['maintain_ratio'])) ? (bool)$params['maintain_ratio'] : FALSE;
		$data_params['width'] = (isset($params['width'])) ? (int)$params['width'] : '';
		$data_params['height'] = (isset($params['height'])) ? (int)$params['height'] : '';
		$data_params['master_dim'] = (isset($params['master_dim'])) ? $params['master_dim'] : '';
		$data_params['resize_and_crop'] = (isset($params['resize_and_crop'])) ? $params['resize_and_crop'] : '';
		$data_params['resize_method'] = (isset($params['resize_method'])) ? $params['resize_method'] : 'maintain_ratio';
		$data_params['hide_options'] = (isset($params['hide_options'])) ? (bool)$params['hide_options'] : FALSE;
		$data_params['accept'] = (isset($params['accept'])) ? $params['accept'] : '';
		
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

	// --------------------------------------------------------------------

	/**
	 * Creates an inline edit field type useful for drop down selects that reference data from another module
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function inline_edit($params)
	{
		$form_builder =& $params['instance'];
		if (!empty($params['module']))
		{
			// hackalicious... used to check for a model's module
			$modules = $this->CI->fuel->modules->get(NULL, FALSE);
			foreach($modules as $key => $mod)
			{
				$mod_name = preg_replace('#(\w+)_model$#', '$1', strtolower($mod->info('model_name')));
				if (strtolower($params['module']) == $mod_name)
				{
					$params['module'] = $key;
					break;
				}
			}

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

			$permission = (!empty($module)) ? $module->permission : $uri;
			if ($this->fuel->auth->has_permission($permission))
			{
				$inline_class = 'add_edit '.$uri;
				$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$inline_class : $inline_class;
				$params['data'] = array(
					'module' => $uri,
					);
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

	// --------------------------------------------------------------------

	/**
	 * Creates a linked field which will use another fields value as the basis for it's own and will usually apply some sort of transform (e.g. url_title, lowercase, etc)
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	 public function linked($params)
	{
		$form_builder =& $params['instance'];
		$str = '';
		if (!empty($params['linked_to']))
		{
			$linked_class = 'linked';
			$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$linked_class : $linked_class;
			$fields = $form_builder->fields();

			$linked_to_field = (is_array($params['linked_to'])) ? key($params['linked_to']) : $params['linked_to'];

			if ($form_builder->is_nested())
			{
				$linked_to = $fields[$linked_to_field]['key'];
				$linked_to = end(explode('vars--', $linked_to));
			}
			else
			{
				$linked_to = (is_string($params['linked_to']) AND !empty($fields[$linked_to_field])) ? Form::create_id($fields[$linked_to_field]['name']) : $params['linked_to'];
			}
			unset($fields);
			$str .= "<div class=\"linked_info\" style=\"display: none;\">";
			$str .= json_encode($linked_to);
			$str .= "</div>\n";
		}
		$params['type'] = 'text';


		if (!empty($params['formatter']))
		{
			$params['data'] = array('formatter' => $params['formatter']);
		}
		$str .= $form_builder->create_text($params);
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a a template field type... This baby has a ton of options including repeatable and sortable fields. Get's stored as JSON string
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function template($params, $return_fields = FALSE)
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
				
				if (empty($field['label']))
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
					
					$index = (!isset($params['index'])) ? $i : $params['index'];

					// set file name field types to not use array syntax for name so they can be processed automagically
					if (isset($field['type']) AND $field['type'] == 'file')
					{
						$field['name'] = $params[$field_name_key].'_'.$index.'_'.$key;
					}
					else
					{
						$field['name'] = $params[$field_name_key].'['.$index.']['.$key.']';
					}
					
					// set the key to be the same of the parent... so post processing will work
					$field['key'] = $params['key'];
					$field['subkey'] = $key;
					$field['depth'] = $params['depth'] + 1;
					$depth_css_class = ' field_depth_'.$params['depth'];
					$field['class'] = (!empty($field['class'])) ? $field['class'].' '.$depth_css_class : $depth_css_class;
					
					// set placeholders in field ids for javascript to translate... must be last occurence of the digit
					$field['id'] = preg_replace('#([-_a-zA-Z0-9\[\]]+)\[\d+\](\[[-_a-zA-Z0-9]+\])$#U', '$1[{index}]$2', $field['name']);
					$field['id'] = $field['name'];
					$field['id'] = Form::create_id($field['id']);
					$field['display_label'] = $params['display_sub_label'];
					$field['data']['orig_name'] = $params['name'];
					$field['data']['index'] = $index;
					$field['data']['key'] = $key;
					$field['data']['field_name'] = $params['key'];

					// need IDS for some plugins like CKEditor... not sure yet how to clone an element with a different ID
					//$field['id'] = FALSE;
					$_f[$i][$key] = $field;
					$fields[$i][$key] = $form_builder->create_field($field);
					$fields[$i]['__index__'] = $i;
					$fields[$i]['__num__'] = $i + 1;
					$fields[$i]['__title__'] = (isset($params['title_field']) AND !empty($value[$params['title_field']]) AND is_string($value[$params['title_field']])) ? strip_tags($value[$params['title_field']]) : '';
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

		// if just return FIELDs then do it...
		if ($return_fields OR !empty($params['return_fields']))
		{
			return $fields;
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

		// must set $_POST parameter below or else the post_process won't run the serialization'
		if (!isset($_POST[$params['key']]))
		{
			$_POST[$params['key']] = '';
		}

		if (!empty($params['serialize']))
		{
			
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
			$form_builder->set_post_process($params['key'], $func);
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
				$container_class = array('repeatable_container');
				$container_class[] = (!empty($params['depth'])) ? ' child' :  '';
				if (!empty($params['container_class']))
				{
					if (is_array($params['container_class']))
					{
						$container_class = $container_class + $params['container_class'];
					}
					else
					{
						$container_class[] = $params['container_class'];
					}
				}
				$container_class[] = (!empty($params['condensed']) AND $params['condensed']) ? 'repeatable_container_condensed' : '';
				$container_class[] = (!empty($params['non_sortable']) AND $params['non_sortable']) ? 'non_sortable' : '';
				$dblclick = (!empty($params['dblclick'])) ? $params['dblclick'] : 0;
				$init_display = (!empty($params['init_display'])) ? $params['init_display'] : '';
				$title_field = (!empty($params['title_field'])) ? $params['title_field'] : '';
				$str .= '<div class="'.implode(' ', $container_class).'" data-depth="'.$params['depth'].'" data-max="'.$params['max'].'" data-min="'.$params['min'].'" data-dblclick="'.$dblclick.'" data-init_display="'.$init_display.'" data-title_field="'.$title_field.'">';
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
								$header_value = (is_array($f[$params['title_field']]['value'])) ? current($f[$params['title_field']]['value']) : $f[$params['title_field']]['value'];
								if (is_string($header_value))
								{
									$heading = str_replace('{__title__}', $header_value, $value);		
								}
								
							}
							else
							{
								$heading = $value;	
							}
							unset($f[$kk]);
							//$f[$kk]['label'] = $heading;
						}
					}
					$form_params['fields'] = $f;

					$form_params['value'] = '';
					if ( ! empty($params['value'][$k])) 
					{
						$form_params['value'] = $params['value'][$k];
					}

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
						$str .= '<span class="title'.$depth_suffix.'">'.$heading.'</span>';
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
	
	// --------------------------------------------------------------------

	/**
	 * Creates a currency field type
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function currency($params)
	{
		$this->CI->load->helper('format');

		$form_builder =& $params['instance'];
		
		if (empty($params['size']))
		{
			$params['size'] = '10';
		}

		if (empty($params['max_length']))
		{
			$params['max_length'] = '10';
		}
		
		if (empty($params['separator']))
		{
			$params['separator'] = ',';
		}

		if (empty($params['decimal']))
		{
			$params['decimal'] = '.';
		}

		$currency = (isset($params['currency'])) ? $params['currency'] : '$';
		
		$data_vals = array('separator', 'decimal', 'grouping', 'min', 'max');

		if (!isset($params['data']))
		{
			$params['data'] = array();	
		}
		foreach($data_vals as $val)
		{
			if (isset($params[$val]))
			{
				$params['data'][$val] = $params[$val];
			}
		}
		
		$params['class'] = (!empty($params['class'])) ? 'currency '.$params['class'] : 'currency';
		
		$params['type'] = 'text';

		if (!isset($_POST[$params['key']]))
		{
			$_POST[$params['key']] = '';
		}

		$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];

		// check if it's a nested form
		$func_str = '
			if (is_array($value))
			{
				foreach($value as $key => $val)
				{
					$curval = $val;
					if ($val["'.$process_key.'"] == "")
					{
						$val = NULL;
					}
					else
					{
						$value_parts = explode("'.$params['decimal'].'", $val["'.$process_key.'"]);
						$curval = current($value_parts);
						$decimal = "00";
						if (count($value_parts) > 1)
						{
							$decimal = end($value_parts);
						}
						$curval = str_replace("'.$params['separator'].'", "", $curval);
						$curval = (float) $curval.".".$decimal;
					}
					$value[$key]["'.$process_key.'"] = $curval;
				}
				return $value;
			}
			else
			{
				if ($value == "")
				{
					$value = NULL;
				}
				else
				{
					$value_parts = explode("'.$params['decimal'].'", $value);
					$value = current($value_parts);
					$decimal = "00";
					if (count($value_parts) > 1)
					{
						$decimal = end($value_parts);
					}
					$value = str_replace("'.$params['separator'].'", "", $value);
					$value = (float) $value.".".$decimal;
				}
				return $value;
			}
			';
		
		// unformat number
		$func = create_function('$value', $func_str);	
		$form_builder->set_post_process($params['key'], $func);

		// preformat the currency 
		$params['value'] = (!isset($params['value'])) ? NULL : currency($params['value'], '', TRUE, $params['decimal'], $params['separator']);
		//$params['value'] = (!isset($params['value'])) ? NULL : $params['value'];

		// set data values for jquery plugin to use
		return $currency.' '.$form_builder->create_text($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a state field type
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function state($params)
	{
		include(APPPATH.'config/states.php');
		$form_builder =& $params['instance'];
		
		if (isset($params['format']))
		{
			if (strtolower($params['format']) == 'short')
			{
				$abbrs = array_keys($states);
				$states = array_combine($abbrs, $abbrs);
			}
			else if (strtolower($params['format']) == 'long')
			{
				$names = array_values($states);
				$states = array_combine($names, $names);
			}

		}
		$params['options'] = $states;
		
		// set data values for jquery plugin to use
		return $form_builder->create_select($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a slug field type that has the url_title function applied to it
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function slug($params)
	{
		$form_builder =& $params['instance'];
	
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
			else
			{
				$params['linked_to'] = NULL;			
			}
		}

		$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];

		$func_str = '
			if (is_array($value))
			{
				foreach($value as $key => $val)
				{
					if (isset($val["'.$params['linked_to'].'"]))
					{
						$v = url_title($val["'.$params['linked_to'].'"], "dash", TRUE);
						$value[$key]["'.$process_key.'"] = $v;
					}
				}
				return $value;
			}
			else
			{
				$CI =& get_instance();
				$slug_val = $CI->input->post("'.$params['name'].'");
				$linked_value = $CI->input->post("'.$params['linked_to'].'");
				if ( ! $slug_val AND $linked_value)
				{
					return url_title($linked_value, "dash", TRUE);
				}
				return $slug_val;
			}
			';
		$func = create_function('$value', $func_str);
		$form_builder->set_post_process($params['key'], $func);
		
		$params['type'] = 'text';
		
		// set data values for jquery plugin to use
		return $this->linked($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a text area that will automatically create unordered list items off of each new line
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function list_items($params)
	{
		$form_builder =& $params['instance'];
		
		// ugly... just strips the whitespace on multilines ... http://stackoverflow.com/questions/1655159/php-how-to-trim-each-line-in-a-heredoc-long-string
		$params['value'] = trim_multiline(strip_tags($params['value']));
		
		$output_class = (!empty($params['output_class'])) ? 'class="'.$params['output_class'].'"' : '';
		
		$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];

		$list_type = (!empty($params['list_type']) AND $params['list_type'] == 'ol') ? 'ol' : 'ul';

		$func_str = '
			if (is_array($value))
			{
				foreach($value as $key => $val)
				{
					if (isset($val["'.$process_key.'"]))
					{
						$lis = explode("\n", $value);
						$lis = array_map("trim", $lis);
						$val = '.$list_type.'($lis, "'.$output_class.'");
						$value[$key]["'.$process_key.'"] = $val;
					}
				}
				return $value;
			}
			else
			{
				$lis = explode("\n", $value);
				$lis = array_map("trim", $lis);
				return '.$list_type.'($lis, "'.$output_class.'");
			}
			';
		
		
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
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function multi($params)
	{
		$form_builder =& $params['instance'];
		
		$defaults = array(
			'sorting'       => NULL,
			'options'       => array(),
			'mode'          => NULL,
			'model'         => NULL,
			'model_params'  => NULL,
			'wrapper_tag'   => 'span',// for checkboxes
			'wrapper_class' => 'multi_field',
			'module'        => NULL,
			'spacer'        => "&nbsp;&nbsp;&nbsp;",
		);

		$params = $form_builder->normalize_params($params, $defaults);
		
		// force to multi if sorting is selected
		if ($params['sorting'] === TRUE)
		{
			$params['mode'] = 'multi';
		}

		// grab options from a model if a model is specified
		if (!empty($params['model']))
		{
			$model_params = (!empty($params['model_params'])) ? $params['model_params'] : array();
			$params['options'] = $form_builder->options_from_model($params['model'], $model_params);
		}
		if (!empty($params['module']))
		{
			// // hackalicious... used to check for a model's module
			$modules = $this->CI->fuel->modules->get(NULL, FALSE);
			foreach($modules as $key => $mod)
			{
				$mod_name = preg_replace('#(\w+)_model$#', '$1', strtolower($mod->info('model_name')));
				if (strtolower($params['module']) == $mod_name)
				{
					$params['module'] = $key;
					break;
				}
			}
			if (strpos($params['module'], '/') === FALSE)
			{
				$module = $this->CI->fuel->modules->get($params['module'], FALSE);
				$uri = (!empty($module)) ? $module->info('module_uri') : '';
			}
			else
			{
				$uri = $params['module'];
			}

			// check for modules with fuel_ prefix
			$permission = (!empty($module)) ? $module->permission : $uri;
			if (!empty($params['module']) AND $this->fuel->auth->has_permission($permission))
			{
				$inline_class = 'add_edit '.$uri;
				$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$inline_class : $inline_class;
				$params['data'] = array(
					'module' => $uri,
					);
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
					$str .= $params['spacer'];
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

			// needed to detect when none exists
			$exists_params['name'] = 'exists_'.$params['orig_name'];
			$exists_params['value'] = 1;
			$exists_params['type'] = 'hidden';
			$exists_params['ignore_representative'] = TRUE;
			$str .= $form_builder->create_field($exists_params);

		}
		
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the url input select
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function url($params)
	{
		$form_builder =& $params['instance'];
		
		$url_class = 'url_select';
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$url_class : $url_class;
		if (empty($params['size']))
		{
			$params['size'] = '90';
		}

		$data = array();
		if (isset($params['input']))
		{
			$data['input'] = $params['input'];
		}

		if (isset($params['target']))
		{
			$data['target'] = $params['target'];
		}
		if (isset($params['title']))
		{
			$data['title'] = $params['title'];
		}
		if (isset($params['pdfs']))
		{
			$data['pdfs'] = 1;	
		}
		if (isset($params['filter']))
		{
			$data['filter'] = rawurlencode($params['filter']);	
		}
		if (!empty($params['data']))
		{
			$params['data'] = array_merge($params['data'], $data);	
		}
		else
		{
			$params['data'] = $data;
		}
		$params['type'] = 'text';
		return $form_builder->create_text($params);

	}	

	// --------------------------------------------------------------------

	/**
	 * Creates a dropdown select of languages identified in the MY_fuel.php file
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function language($params)
	{
		$form_builder =& $params['instance'];
		if ((isset($this->CI->language_col) AND $params['key'] == $this->CI->language_col)
			OR 
			(!isset($this->CI->language_col) AND $params['key'] == 'language')
			)
		{
			$params['type'] = 'select';
			$params['options'] = $this->CI->fuel->language->options();
		}
		return $form_builder->create_select($params);

	}

	// --------------------------------------------------------------------

	/**
	 * Creates a key / value associative array
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function keyval($params)
	{
		$form_builder =& $params['instance'];
		if (!isset($params['delimiter']))
		{
			$params['delimiter'] = ":";
		}

		if (!isset($params['numeric_indexes']))
		{
			$params['allow_numeric_indexes'] = FALSE;
		}

		if (!isset($params['allow_empty_values']))
		{
			$params['allow_empty_values'] = FALSE;
		}

		$split_delimiter = "\s*".$params['delimiter']."\s*";

		$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];

		// create an array with the key being the image name and the value being the caption (if it exists... otherwise the image name is used again)
		$func_str = '
			if (is_array($value))
			{
				
				foreach($value as $key => $val)
				{
					if (isset($val["'.$process_key.'"]))
					{

						$json = array();
						$rows = preg_split("\s*#\n|,\s*#", $val);
						foreach($rows as $r)
						{
							$vals = preg_split("#'.$split_delimiter.'#", $r);
							if (isset($vals[1]))
							{
								$val = $vals[1];
								$key = $vals[0];
								$json[$key] = $val;
							}
							else
							{
								$json[] = $vals[0];
							}
						}
						$first_item = current($json);
						$value[$key]["'.$process_key.'"] = (!empty($first_item)) ? json_encode($json) : "";
					}
				}
				return $value;
			}
			else
			{
				$json = array();
				$rows = preg_split("#\s*\n|,\s*#", $value);
				foreach($rows as $r)
				{
					$vals = preg_split("#'.$split_delimiter.'#", $r);
					if (isset($vals[1]))
					{
						$val = $vals[1];
						$key = $vals[0];
						$json[$key] = $val;
					}
					else
					{
						$json[] = $vals[0];
					}
				}
				$first_item = current($json);
				return  (!empty($first_item)) ? json_encode($json) : "";
			}
			';
		
		$func = create_function('$value', $func_str);
		$form_builder->set_post_process($params['key'], $func);

		if (!empty($params['value']))
		{
			if (is_json_str($params['value']))
			{
				$params['value'] = json_decode($params['value'], TRUE);
			}

			if (is_array($params['value']))
			{
				$new_value = array();
				foreach($params['value'] as $key => $val)
				{
					if (!empty($val) OR ($params['allow_empty_values'] === TRUE AND empty($val)))
					{
						if (is_numeric($key) AND $params['allow_numeric_indexes'] === FALSE)
						{
							$new_value[] = $val;	
						}
						else
						{
							$new_value[] = $key.$params['delimiter'].$val;	
						}
					}
				}
				if (!empty($new_value))
				{
					$params['value'] = implode("\n", $new_value);	
				}
				else
				{
					$params['value'] = '';
				}
			}
		}
		else
		{
			$params['value'] = '';
		}

		$params['type'] = 'textarea';
		$params['class'] = 'no_editor';
		return $form_builder->create_field($params);

	}

	// --------------------------------------------------------------------

	/**
	 * Creates a dropdown select of blocks and when selected, will display fields related to that block
	 *
	 * @access	public
	 * @param	array Fields parameters
	 * @return	string
	 */
	public function block($params)
	{
		$form_builder =& $params['instance'];

		// check to make sure there is no conflict between page columns and layout vars
		if (!empty($params['block_name']))
		{
			// check to make sure there is no conflict between page columns and layout vars
			$layout = $this->fuel->layouts->get($params['block_name'], 'block');

			if (!$layout)
			{
				return;
			}

			if (!isset($params['context']))
			{
				$params['context'] = $params['key'];
			}

			$layout->set_context($params['context']);
			$fields = $layout->fields();

			$fb = new Form_builder();
			$fb->load_custom_fields(APPPATH.'config/custom_fields.php');
			
			$fb->question_keys = array();
			$fb->submit_value = '';
			$fb->cancel_value = '';
			$fb->use_form_tag = FALSE;
			$fb->name_prefix = 'vars';
			$fb->set_fields($fields);
			$fb->display_errors = FALSE;

			if (!empty($params['value']))
			{
				$fb->set_field_values($params['value']);	
			}
			
			$form = $fb->render();
			return $form;
		}
		else
		{
			$params['type'] = 'select';
			if (!isset($params['options']))
			{
				// if a folder is specified, we will look in that directory to get the list of blocks
				if (isset($params['folder']))
				{
					if (!isset($params['where']))
					{
						$params['where'] = array();
					}
					if (!isset($params['filter']))
					{
						$params['filter'] = '^_(.*)|\.html$';
					}
					if (!isset($params['order']))
					{
						$params['order'] = TRUE;
					}

					// set options_list to not be recursive since block layout names won't have slashes in them (e.g. sections/right_image... it would just be right_image)			
					if (!isset($params['recursive']))
					{
						$params['recursive'] = FALSE;
					}

					$params['options'] = $this->CI->fuel->blocks->options_list($params['where'], $params['folder'], $params['filter'], $params['order'], $params['recursive']);
				}

				// otherwise we use the ones found on the MY_fuel_layouts.php
				else
				{
					if (!isset($params['group']))
					{
						$params['group'] = '';
					}
					$params['options'] = $this->CI->fuel->layouts->options_list(TRUE, $params['group']);
				}
			}

			$select_class = 'block_layout_select';
			$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$select_class : $select_class;

			if (!empty($params['ajax_url']))
			{
				$params['data']['url'] = rawurlencode($params['ajax_url']);
			}
			$value = (is_array($params['value']) AND isset($params['value']['block_name'])) ? $params['value']['block_name'] : ((isset($params['value'])) ? $params['value'] : '');
			$params['value'] = $value;
			$params['style'] = 'margin-bottom: 10px;';
			$field = $form_builder->create_select($params);
			$field = $field.'<div class="block_layout_fields"></div><div class="loader hidden"></div>';
			return $field;
		}
	}

}



/* End of file Fuel_custom_fields.php */
/* Location: ./modules/fuel/libraries/Fuel_custom_fields.php */