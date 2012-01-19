<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 */

// ------------------------------------------------------------------------

/**
 * A form creation class
 *
 * This class allows you to create forms by passing in configurable
 * array values. For a list of what those field values can be, look at the
 * normalize_params method. It works well with the MY_Model form_fields
 * method which returns table meta information regarding the fields of a 
 * table
 *
 * The Form.php class is required if a form object is not passed in the 
 * initialization process.
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/form_builder.html
 */

Class Form_builder {

	public $form; // form object used to create the form fields and associate errors with
	public $id = ''; // id to be used for the containing table or div
	public $css_class = 'form'; // css class to be used with the form
	public $form_attrs = 'method="post" action=""'; // form tag attributes
	public $label_colons = FALSE; // add colons to form labels?
	public $textarea_rows = 10; // number of rows for a textarea
	public $textarea_cols = 60; // number of columns for a textarea
	public $text_size_limit = 40; // text size for a text input
	public $submit_value = "Submit"; // submit value  (what the button says)
	public $cancel_value = ""; // cancel value (what the button says)
	public $cancel_action = ""; // what the cancel button does
	public $reset_value = ''; // reset button value  (what the button says)
	public $other_actions = ''; // additional actions to be displayed at the bottom of the form
	public $use_form_tag = TRUE; // include the form opening/closing tags in rendered output
	public $exclude = array(); // exclude these fields from the form
	public $hidden = array('id'); // hidden fields
	public $readonly = array(); // readonly fields
	public $disabled = array();  // disabled fields
	public $displayonly = array(); // for display purposes only
	public $date_format = 'm/d/Y'; // date format for date type fields
	public $section_tag = 'h3'; // section html tag
	public $copy_tag = 'p'; // copy html tag
	public $fieldset = ''; // field set name
	public $name_array = ''; // put the form fields into an array for namespacing
	public $name_prefix = ''; // prefix the form fields as an alternatie to an array for namespacing
	public $class_type_prefix = 'field_type_'; // the CSS class prefix to associate with each field type 
	public $names_id_match = TRUE; // determines if the names and ids match if using a name_prefix or name_array
	public $key_check = ''; // the keycheck value used for forms that create session unique session variables to prevent spamming
	public $key_check_name = ''; // the keycheck form name used for forms that create session unique session variables to prevent spamming
	public $tooltip_format = '<span title="{?}" class="tooltip">[?]</span>'; // tooltip formatting string
	public $tooltip_labels = TRUE; // use tooltip labels?
	public $single_select_mode = 'auto'; // auto will use enum if 2 or less and a single select if greater than 2. Other values are enum or select 
	public $multi_select_mode = 'auto'; // auto will use a series of checkboxes if 5 or less and a multiple select if greater than 5. Other values are multi or checkbox 
	public $boolean_mode = 'checkbox'; // booleon mode can be checkbox or enum (which will display radio inputs)
	public $display_errors_func = 'display_errors'; // the function used to generate errors... usually display_errors is the name
	public $display_errors = FALSE; // displays errors at the top of the form if TRUE
	public $question_keys = array('how', 'do', 'when', 'what', 'why', 'where', 'how', 'is', 'which', 'did', 'any','would', 'should', 'could'); // adds question marks to the label if has these words in the label
	public $show_required = TRUE; // show the required fields text at the bottom of the form
	public $required_indicator = '*'; // indicator for a required field
	public $required_text = '<span class="required">{required_indicator}</span> required fields'; // the required field text
	public $label_layout = 'left'; // label layout... can be left or top
	public $has_required = FALSE; // does the form have required fields
	public $render_format = 'table'; // default render format
	public $row_id_prefix = ''; // the row id prefix
	public $lang_prefix = 'form_label_'; // language prefix to be applied before a label
	public $custom_fields = array(); // custom fields
	public $auto_execute_js = TRUE; // autmoatically execute the javascript for the form
	public $html_prepend = ''; // prepended HTML to the form HINT: Can include JS script tags
	public $html_append = ''; // appended HTML to the form HINT: Can include JS script tags
	public $representatives = array(); // an array of fields that have arrays or regular expression values to match against different field types (e.g. 'number'=>'bigint|smallint|tinyint|int')
	
	protected $_html; // html string
	protected $_fields; // fields to be used for the form
	protected $_cached; // cached parameters
	protected $_js; // to be executed once per render
	protected $_css; // to be executed once per render
	protected $_rendering = FALSE; // used to prevent infinite loops when calling form_builder reference from within a custom form field

	/**
	 * Constructor - Sets Form_builder preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		$this->initialize($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($params = array())
	{
		// clear out any data before initializing
		$this->reset();
		$this->set_params($params);
		
		// setup custom fields
		if (!empty($this->custom_fields))
		{
			foreach($this->custom_fields as $type => $custom)
			{
				$this->register_custom_field($type, $custom);
			}
		}
		
		// create form object if not in initialization params
		if (is_null($this->form))
		{
			$CI =& get_instance();
			$CI->load->library('form');
			$this->form = new Form();
			
			// load localization helper if not already
			if (!function_exists('lang'))
			{
				$CI->load->helper('language');
			}

			// CSRF protections
			if ($CI->config->item('csrf_protection') === TRUE AND empty($this->key_check))
			{
				$CI->security->csrf_set_cookie(); // need to set it again here just to be sure ... on initial page loads this may not be there
				$this->key_check = $CI->security->csrf_hash;
				$this->key_check_name = $CI->security->csrf_token_name;
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function set_params($params)
	{
		if (is_array($params) AND count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$method = 'set_'.$key;

					if (method_exists($this, $method))
					{
						$this->$method($val);
					}
					else
					{
						$this->$key = $val;
					}
				}
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Same as reset
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear()
	{
		$this->reset();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clear class values
	 *
	 * @access	public
	 * @return	void
	 */
	public function reset()
	{
		$this->_fields = array();
		$this->_html = '';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set the fields for the form
	 * 
	 * Check the normalize_params method for possible values
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function set_fields($fields)
	{
		$i = 1;
		foreach ($fields as $key => $val)
		{
			
			// __FORM_BUILDER__ allows you to set properties on the class
			// convenient for models setting values
			if (strtoupper($key) == '__FORM_BUILDER__')
			{
				$this->set_params($val);
			}
			else
			{
				if (is_string($val))
				{
					$fields[$key] = array('name' => $key, 'value' => $val);
				}
				if (empty($val['name'])) $fields[$key]['name'] = $key;
				if (empty($fields[$key]['order'])) $fields[$key]['order'] = $i;
				$i++;
			}
		}
		$this->_fields = $fields;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the fields for the form
	 * 
	 * @access	public
	 * @return	array
	 */
	public function fields()
	{
		return $this->_fields;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the value attribute for the fields of the form
	 * 
	 * Often times this is database or post data
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function set_field_values($values)
	{
		// set values for fields that are arrays
		foreach($values as $key => $val)
		{
			if (is_array($values[$key]))
			{
				foreach($values[$key] as $k => $v)
				{
					$values[$key.'['.$k.']'] = $v;
				}
			}
		}
		
		if (!empty($this->_fields))
		{
			foreach($this->_fields as $key => $val)
			{
				if (isset($values[$key]))
				{
					if (empty($val['type']))
					{
						$is_checkbox = FALSE;
					}
					
					// don't set the values of these form types'
					else if ($val['type'] == 'submit' OR $val['type'] == 'button')
					{
						continue;
					}
					else
					{
						$is_checkbox = (($val['type'] == 'checkbox') OR ($val['type'] == 'boolean' AND $this->boolean_mode == 'checkbox'));
					}
					if (!$is_checkbox)
					{
						$this->_fields[$key]['value'] = $values[$key];
					}
					
					if (!empty($val['type']))
					{
						if ($is_checkbox)
						{
							$this->_fields[$key]['checked'] = ((isset($this->_fields[$key]['value']) AND $values[$key] == $this->_fields[$key]['value']) OR 
								$values[$key] === TRUE OR  
								$values[$key] === 1 OR  
								$values[$key] === 'y' OR  
								$values[$key] === 'yes') ? TRUE : FALSE;
						}
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Render the HTML output
	 * 
	 * @access	public
	 * @param	array	fields values... will overwrite anything done with the set_fields method previously
	 * @param	string	'divs or table
	 * @return	string
	 */
	public function render($fields = NULL, $render_format = NULL)
	{
		if (empty($render_format)) $render_format = $this->render_format;
		if ($render_format == 'divs')
		{
			return $this->render_divs($fields);
		}
		else
		{
			return $this->render_table($fields);
		}

	}	

	// --------------------------------------------------------------------

	/**
	 * Render the HTML output
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	string
	 */
	public function render_divs($fields = NULL)
	{
		if (!empty($fields)) $this->set_fields($fields);

		// reoarder
		$this->set_field_order();
		
		// pre process field values
		$this->pre_process_field_values();
		
		$this->_html = $this->html_prepend;
		$str = '';
		$begin_str = '';
		$end_str = '';
		if ($this->display_errors)
		{
			$func = $this->display_errors_func;
			if (function_exists($func))
			{
				$str .= $func($this->validator->get_errors());
			}
		}

		$colspan = ($this->label_layout == 'top') ? '1' : '2';
		
		$str .= $this->_open_div(TRUE);

		$fieldset_on = FALSE;
		
		foreach($this->_fields as $key => $val)
		{
			$val = $this->normalize_params($val);
			
			if ($val['type'] == 'fieldset' OR !empty($val['fieldset']))
			{
				// close any existing field sets
				$str .= $this->_close_div();
				if ($fieldset_on)
				{
					$fieldset_val['open'] = FALSE;
					$str .= $this->create_fieldset($fieldset_val);
				}
				$fieldset_val['open'] = TRUE;
				if (!empty($val['fieldset']))
				{
					$fieldset_val['value'] = $val['fieldset'];
				}
				else
				{
					$fieldset_val = $val;
				}
				$str .= $this->create_fieldset($fieldset_val);
				$str .= $this->_open_div();
				$fieldset_on = TRUE;
				
				// continue if the fieldset is part of the field values and not the "type"
				if (empty($val['fieldset']))
				{
					continue;
				}
			}
			
			if ($val['type'] == 'section')
			{
				$str .= "<div".$this->_open_row_attrs($val).'>';
				$str .= "<span class=\"section\">".$this->create_section($val)."</span>\n";
				$str .= "</div>\n";
				continue;
			}
			else if (!empty($val['section']))
			{
				$str .= "<div class=\"section\"><".$this->section_tag.">".$val['section']."</".$this->section_tag."></div>\n";
			}
			
			if ($val['type'] == 'copy')
			{
				$str .= "<div".$this->_open_row_attrs($val).'>';
				$str .= "<span class=\"copy\">".$this->create_copy($val)."</span>\n";
				$str .= "</div>\n";
				continue;
			}
			else if (!empty($val['copy']))
			{
				$str .= "<div".$this->_open_row_attrs($val).'>';
				$str .= "<span class=\"copy\"><".$this->copy_tag.">".$val['copy']."</".$this->copy_tag."></span>\n";
				$str .= "</div>\n";
			}
			
			if (!empty($val['custom']))
			{
				$str .= "<div".$this->_open_row_attrs($val).'>';
				$str .= "<span class=\"label\">";
				$str .= $this->create_label($val, FALSE);
				$str .= "</span>";
				$str .= "<span class=\"field\">";
				$str .= $val['custom'];
				$str .= "</span>";
				$str .= "</div>\n";
			}
			else if (in_array($val['name'], $this->hidden) OR $val['type'] == 'hidden')
			{
				$end_str .= $this->create_hidden($val);
			}
			else if ((is_array($val['name']) AND in_array($val['name'], $this->displayonly)) OR  $val['displayonly'] OR  (is_string($this->displayonly) AND strtolower($this->displayonly) == 'all'))
			{
				$str .= "<div".$this->_open_row_attrs($val).'>';
				$str .= "<span class=\"label\">";
				$str .= $this->create_label($val, FALSE);
				$str .= "</span>";
				$str .= "<span class=\"field\">";
				$str .= $this->create_readonly($val, FALSE)."\n";
				$str .= "</span>";
				$str .= "</div>\n";
			}
			else if (!in_array($val['name'], $this->exclude))
			{
				$str .= "<div".$this->_open_row_attrs($val).'>';
				$str .= "<span class=\"label\">";
				$str .= $this->create_label($val, FALSE);
				$str .= "</span>";
				$str .= "<span class=\"field\">";
				$str .= $this->create_field($val, FALSE);
				$str .= "</span>";
				$str .= "</div>\n";
			}
		}

		// close any open fieldsets
		if ($fieldset_on)
		{
			$str .= $this->_close_table();
			$val['open'] = FALSE;
			$str .= $this->create_fieldset($val);
			$str .= $this->_open_table();
		}
		
		$str .= "<div class=\"actions\"><div class=\"actions_inner\">";
		if (!empty($this->reset_value))
		{
			if (preg_match("/^</i", $this->reset_value))
			{
				$str .= $this->reset_value;
			}
			else
			{
				$str .= $this->form->reset($this->reset_value, '', array('class' => 'reset'));
			}
		}
		if (!empty($this->cancel_value))
		{
			if (preg_match("/^</i", $this->cancel_value))
			{
				$str .= $this->cancel_value;
			}
			else
			{
				$cancel_attrs = array('class' => 'cancel');
				if (!empty($this->cancel_action))
				{
					$cancel_attrs['onclick'] = $this->cancel_action;
				}

				$str .= $this->form->button($this->cancel_value, '', $cancel_attrs);
			}
		}
		if (!empty($this->submit_value) AND $this->displayonly != 'all')
		{
			// check if the string has a tag and if so just pump in the string
			if (preg_match("/^</i", $this->submit_value))
			{
				$str .= $this->submit_value;
			}
			else
			{
				$submit_btn = (preg_match("/(.)+\\.(jp(e){0,1}g$|gif$|png$)/i", $this->submit_value)) ? 'image' : 'submit';
				$submit_name = (!empty($this->name_prefix) AND $this->names_id_match) ? $this->name_prefix.'--'.$this->submit_value : $this->submit_value;
				$submit_id = $this->submit_value;
				if (!empty($this->name_prefix))
				{
					$submit_id = $this->name_prefix.'--'.$submit_id;
				}
				$str .= $this->form->$submit_btn($this->submit_value, $submit_name, array('class' => 'submit', 'id' => $submit_id));
			}
		}

		if (!empty($this->other_actions)) $str .= $this->other_actions;
		$str .= "</div></div>\n";
		if ($this->has_required AND $this->show_required)
		{
			$str .= "<div class=\"required\">";
			$str .= str_replace('{required_indicator}', $this->required_indicator, $this->required_text);
			$str .= "</div>\n";
		}
		$str .= "</div>\n";
		
		$str = $begin_str . $str . $end_str;
		$this->_close_form($str);
		
		return $this->_html;
	}
	// --------------------------------------------------------------------

	/**
	 * Render the HTML output
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	string
	 */
	public function render_table($fields = NULL)
	{
		if (!empty($fields)) $this->set_fields($fields);

		// reoarder
		$this->set_field_order();
		
		// pre process field values
		$this->pre_process_field_values();
		
		$this->_html = $this->html_prepend;
		$str = '';
		$begin_str = '';
		$end_str = '';
		if ($this->display_errors)
		{
			$func = $this->display_errors_func;
			if (function_exists($func))
			{
				$str .= $func();
			}
		}

		$colspan = ($this->label_layout == 'top') ? '1' : '2';
		$str .= $this->_open_table(TRUE);

		$fieldset_on = FALSE;

		foreach($this->_fields as $key => $val)
		{
			$val = $this->normalize_params($val);
		
			if ($val['type'] == 'fieldset' OR !empty($val['fieldset']))
			{
				// close any existing field sets
				$str .= $this->_close_table();
				if ($fieldset_on)
				{
					$fieldset_val['open'] = FALSE;
					$str .= $this->create_fieldset($fieldset_val);
				}
				$fieldset_val['open'] = TRUE;
				if (!empty($val['fieldset']))
				{
					$fieldset_val['value'] = $val['fieldset'];
				}
				else
				{
					$fieldset_val = $val;
				}
				$str .= $this->create_fieldset($fieldset_val);
				$str .= $this->_open_table();
				$fieldset_on = TRUE;
				
				// continue if the fieldset is part of the field values and not the "type"
				if (empty($val['fieldset']))
				{
					continue;
				}
			}
			
			if ($val['type'] == 'section')
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"section\">".$this->create_section($val)."</td>\n</tr>\n";
				continue;
			}
			else if (!empty($val['section']))
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"section\"><".$this->section_tag.">".$val['section']."</".$this->section_tag."></td>\n</tr>\n";
			}
			
			if ($val['type'] == 'copy')
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"copy\">".$this->create_copy($val)."</td></tr>\n";
				continue;
			}
			else if (!empty($val['copy']))
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"copy\"><".$this->copy_tag.">".$val['copy']."</".$this->copy_tag."></td>\n</tr>\n";
			}
			
			if (!empty($val['custom']))
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t";
				if ($val['display_label'] !== FALSE)
				{
					$str .= "<td class=\"label\">";
					if ($this->label_layout != 'top')
					{
						$str .= $this->create_label($val, TRUE);
						$str .= "</td>\n\t<td class=\"value\">".$val['custom']."</td>\n</tr>\n";
					}
					else
					{
						$str .= $this->create_label($val, TRUE)."</td></tr>\n";
						$str .= "<tr".$this->_open_row_attrs($val);
						$str .= ">\n\t<td class=\"value\">".$val['custom']."</td>\n</tr>\n";
					}
				}
				else
				{
					$str .= "<td class=\"value\" colspan=\"2\">".$val['custom']."</td>\n</tr>\n";
				}
			}
			else if (in_array($val['name'], $this->hidden) OR  $val['type'] == 'hidden')
			{
				$end_str .= $this->create_hidden($val);
			}
			else if ((is_array($val['name']) AND in_array($val['name'], $this->displayonly))  OR  $val['displayonly'] OR  (is_string($this->displayonly) AND strtolower($this->displayonly) == 'all') OR $this->displayonly === TRUE)
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t<td class=\"label\">";
				if ($this->label_layout != 'top')
				{
					$str .= $this->create_label($val, FALSE);
					$str .= "</td>\n\t<td class=\"value\">".$val['before_html'].$val['value'].$val['after_html']."\n".$this->create_hidden($val)."</td>\n</tr>\n";
				}
				else
				{
					$str .= $this->create_label($val, FALSE)."</td></tr>\n";
					$str .= "<tr".$this->_open_row_attrs($val);
					$str .= ">\n\t<td class=\"value\">".$this->create_readonly($val, FALSE)."</td>\n</tr>\n";
				}
			}
			else if (!in_array($val['name'], $this->exclude))
			{
				$str .= "<tr".$this->_open_row_attrs($val);
				$str .= ">\n\t";
				if ($val['display_label'] !== FALSE)
				{
					$str .= "<td class=\"label\">";
					if ($this->label_layout != 'top')
					{
						$str .= $this->create_label($val, TRUE);
						$str .= "</td>\n\t<td class=\"value\">".$this->create_field($val, FALSE)."</td>\n</tr>\n";
					}
					else
					{
						$str .= $this->create_label($val, TRUE)."</td></tr>\n";
						$str .= "<tr".$this->_open_row_attrs($val);
						$str .= ">\n\t<td class=\"value\">".$this->create_field($val, FALSE)."</td>\n</tr>\n";
					}
				}
				else
				{
					$str .= "<td class=\"value\" colspan=\"2\">".$this->create_field($val, FALSE)."</td>\n</tr>\n";
				}

			}
		}

		// close any open fieldsets
		if ($fieldset_on)
		{
			$str .= $this->_close_table();
			$val['open'] = FALSE;
			$str .= $this->create_fieldset($val);
			$str .= $this->_open_table();
		}

		if ($this->label_layout != 'top')
		{
			$str .= "<tr";
			if (!empty($this->row_id_prefix))
			{
				$str .= ' id="'.$this->row_id_prefix.'actions"';
			}
			$str .= ">\n\t<td></td>\n\t<td class=\"actions\"><div class=\"actions_inner\">";
		}
		else
		{
			$str .= "<tr>\n\t<td class=\"actions\"><div class=\"actions\">";
		}
		if (!empty($this->reset_value))
		{
			if (preg_match("/^</i", $this->reset_value))
			{
				$str .= $this->reset_value;
			}
			else
			{
				$str .= $this->form->reset($this->reset_value, '', array('class' => 'reset'));
			}
		}
		if (!empty($this->cancel_value))
		{
			if (preg_match("/^</i", $this->cancel_value))
			{
				$str .= $this->cancel_value;
			}
			else
			{
				$cancel_attrs = array('class' => 'cancel');
				if (!empty($this->cancel_action))
				{
					$cancel_attrs['onclick'] = $this->cancel_action;
				}

				$str .= $this->form->button($this->cancel_value, '', $cancel_attrs);
			}
		}

		if (!empty($this->submit_value) AND $this->displayonly != 'all')
		{
			// check if the string has a tag and if so just pump in the string
			if (preg_match("/^</i", $this->submit_value))
			{
				$str .= $this->submit_value;
			}
			else
			{
				$submit_btn = (preg_match("/(.)+\\.(jp(e){0,1}g$|gif$|png$)/i", $this->submit_value)) ? 'image' : 'submit';
				$submit_name = (!empty($this->name_prefix) AND $this->names_id_match) ? $this->name_prefix.'--'.$this->submit_value : $this->submit_value;
				$submit_id = $this->submit_value;
				if (!empty($this->name_prefix))
				{
					$submit_id = $this->name_prefix.'--'.$submit_id;
				}
				$str .= $this->form->$submit_btn($this->submit_value, $submit_name, array('class' => 'submit', 'id' => $submit_id));
			}
		}
		if (!empty($this->other_actions)) $str .= $this->other_actions;
		$str .= "</div></td>\n</tr>\n";
		if ($this->has_required AND $this->show_required)
		{
			$str .= "<tr>\n\t<td colspan=\"".$colspan."\" class=\"required\">";
			$str .= str_replace('{required_indicator}', $this->required_indicator, $this->required_text);
			$str .= "</td>\n</tr>\n";
		}
		$str .= $this->_close_table();

		$str = $begin_str . $str . $end_str;
		$this->_close_form($str);

		return $this->_html;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the opening div element that contains the form fields
	 * 
	 * @access	public
	 * @return	string
	 */
	protected function _open_div($create_unique_id = FALSE)
	{
		$str = '';
		$str .= "<div class=\"".$this->css_class."\"";
		
		// must have an id for javascript to execute on initialization
		if ($create_unique_id AND empty($this->id))
		{
			$this->id = uniqid('form_');
		}
		$str .= ' id="'.$this->id.'"';
		$str .= ">\n";
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the opening table element
	 * 
	 * @access	public
	 * @return	string
	 */
	protected function _open_table($create_unique_id = FALSE)
	{
		$str = '';
		$str .= "<table class=\"".$this->css_class."\"";
		if ($create_unique_id AND empty($this->id))
		{
			$this->id = uniqid('form_');
		}
		$str .= ' id="'.$this->id.'"';
		$str .= ">\n";
		
		$str .= "<tbody>\n";
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the closing element
	 * 
	 * @access	public
	 * @return	string
	 */
	protected function _close_div()
	{
		$str = '';
		$str .= "</div>\n";
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the closing table elements
	 * 
	 * @access	public
	 * @return	string
	 */
	protected function _close_table()
	{
		$str = '';
		$str .= "</tbody>\n";
		$str .= "</table>\n";
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the opening row TR or div with attrs
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	string
	 */
	protected function _open_row_attrs($val)
	{
		$str = '';
		if (!empty($this->row_id_prefix))
		{
			$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
		}
		if (!empty($val['row_class']))
		{
			$str .= ' class="'.$val['row_class'].'"';
		}
		return $str;
	}
	
	// --------------------------------------------------------------------
	/**
	 * Outputs the last part of the form rendering for both a table and div
	 * 
	 * @access	public
	 * @param	string	
	 * @return	void
	 */
	protected function _close_form($str)
	{
		if ($this->use_form_tag) 
		{
			$this->_html .= $this->form->open($this->form_attrs);
		}
		if (!empty($this->fieldset))
		{
			$this->_html .= $this->form->fieldset_open($this->fieldset);
		}
		$this->_html .= $str;
		
		if (!empty($this->key_check))
		{
			$this->_html .= $this->create_hidden(array('name' => $this->key_check_name, 'value' => $this->key_check));
		}
		if (!empty($this->fieldset))
		{
			$this->_html .= $this->form->fieldset_close();
		}
		
		if ($this->use_form_tag)
		{
			$this->_html .= $this->form->close('', FALSE); // we set the token above just in case form tags are turned off	
		}
		$this->_html .= $this->_render_js();
		$this->_html .= $this->html_append;
		
		// apply any CSS 
		$this->_apply_css();
		return $this->_html;
	}
	

	// --------------------------------------------------------------------

	/**
	 * Normalize the fields so that the other methods can expect certain field attributes
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	array
	 */
	protected function _default($val)
	{
		if (is_object($val)) $val = get_object_vars($val);
		
		$defaults = array(
			'id' => '',
			'name' => '',
			'type' => '',
			'default' => '',
			'max_length' => 0,
			'comment' => '',
			'label' => '',
			'required' => FALSE,
			'size' => '',
			'class' => '',
			'value' => '',
			'readonly' => '',
			'disabled' => '',
			'label_colons' => NULL,
			'display_label' => TRUE,
			'order' => 9999,
			'before_html' => '', // for html before the field
			'after_html' => '', // for html after the field
			'displayonly' => FALSE,
			'pre_process' => NULL,
			'post_process' => NULL,
			'js' => '',
			'css' => '',
			'represents' => '',
			'data' => array(),
			'__DEFAULTS__' => TRUE // set so that we no that the array has been processed and we can check it so it won't process it again'
		);
		
		$params = array_merge($defaults, $val);
		
		if (empty($params['orig_name'])) $params['orig_name'] = $params['name']; // for labels in case the name_array is used
		if (!isset($val['value']) AND ($params['type'] != 'checkbox' AND !($params['type'] == 'boolean' AND $this->boolean_mode == 'checkbox')))
		{
			$params['value'] = $params['default'];
		}
		
		if (!isset($params['label_colons']))
		{
			$params['label_colons'] = $this->label_colons;
		}
		
		if (!empty($val['name']))
		{
			if ((is_array($this->readonly) AND in_array($val['name'], $this->readonly)) OR  (is_string($this->readonly) AND strtolower($this->readonly) == 'all'))
			{
				$params['readonly'] = 'readonly';
			}

			if ((is_array($this->disabled) AND in_array($val['name'], $this->disabled)) OR  (is_string($this->disabled) AND strtolower($this->disabled) == 'all'))
			{
				$params['disabled'] = 'disabled';
			}
		}
		if (!empty($this->name_array) AND strpos($params['name'], '[') === FALSE)
		{
			if (!$this->names_id_match)
			{
				if ($params['id'] !== FALSE)
				{
					$params['id'] = $this->name_array.'['.$params['orig_name'].']';
				}
				$params['name'] = $params['orig_name'];
			}
			else
			{
				$params['name'] = $this->name_array.'['.$params['orig_name'].']';
			}
			if (in_array($params['orig_name'], $this->hidden) AND !in_array($params['name'], $this->hidden)) $this->hidden[] = $params['name'];
		}

		if (!empty($this->name_prefix))
		{
			if (!$this->names_id_match)
			{
				if ($params['id'] !== FALSE)
				{
					$params['id'] = $this->name_prefix.'--'.$params['orig_name'];  // used double hyphen so easier to explode
				}
				$params['name'] = $params['orig_name'];
			}
			else
			{
				$params['name'] = $this->name_prefix.'--'.$params['orig_name'];
			}
			
			if (in_array($params['orig_name'], $this->hidden) AND !in_array($params['name'], $this->hidden)) $this->hidden[] = $params['name'];
		}
		
		if ($params['type'] == 'enum' OR  $params['type'] == 'select')
		{
			if (!isset($params['options']))
			{
				$params['options'] = array();
			}
			if ((empty($params['options']) AND is_array($params['options'])) AND is_array($params['max_length']) AND !empty($params['max_length']))
			{
				$params['options'] = $params['max_length'];
			}
		}
		
		// fix common errors
		if (!empty($params['maxlength']) AND empty($params['max_length'])) 
		{
			$params['max_length'] = $params['maxlength'];
			unset($params['maxlength']);
		}
		
		// take out javascript so we execute it only once per render
		if (!empty($params['js']))
		{
			$this->_js[$params['type']] = $params['js'];
		}

		// take out css so we execute it only once per render
		if (!empty($params['css']))
		{
			$this->_css[$params['type']] = $params['css'];
		}
		
		// says whether this field can represent other field types
		if (!empty($params['represents']))
		{
			$this->representatives[$params['type']] = $params['represents'];
		}
		
		// set the field type CSS class
		$type = (!empty($params['type'])) ? $params['type'] : 'text';
		$field_class = $this->class_type_prefix.$type;
		$params['class'] = (!empty($params['class'])) ? $field_class.' '.$params['class'] : $field_class;

		$this->_cached[$params['name']] = $params;
		return $params;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the default values for any field
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	array
	 */
	function normalize_params($val, $defaults = array())
	{
		if ($val == '')
		{
			$val = array();
		}

		// check to see if the array is already normalized
		if (!$this->_has_defaults($val))
		{
			$val = $this->_default($val);
		}
		
		// set up defaults
		$params = array_merge($defaults, $val);
		
		return $params;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders the custom field
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	array
	 */
	protected function _render_custom_field($params)
	{
		$field = FALSE;
		if (is_array($params) AND isset($this->custom_fields[$params['type']]))
		{
			$func = $this->custom_fields[$params['type']];

			if (is_a($func, 'Form_builder_field'))
			{
				// give custom fields a reference to the current object
				$params['instance'] =& $this;
				$this->_rendering = TRUE;
				
				// take out CSS so we execute it only once per render
				if (!empty($params['css']))
				{
					$this->_css[$params['type']] = $params['css'];
				}

				// same here... but we are looking for CSS on the object
				if (!empty($func->css))
				{
					$this->_css[$params['type']] = $func->css;
				}

				// take out javascript so we execute it only once per render
				if (!empty($params['js']))
				{
					$this->_js[$params['type']] = $params['js'];
				}

				// same here... but we are looking for js on the object
				if (!empty($func->js))
				{
					$this->_js[$params['type']] = $func->js;
				}
				
				$field = $func->render($params);
			}
			else if (is_callable($func))
			{
				$this->_rendering = TRUE;
				$field = $this->create_custom($func, $params);
			}
		}
		else if (is_string($params))
		{
			$field = $params;
		}
		
		$this->_rendering = FALSE;
		return $field;
	}


	// --------------------------------------------------------------------

	/**
	 * Checks to see if the array to initialize a field is normalized or not
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	protected function _has_defaults($vals)
	{
		return (!empty($vals['__DEFAULTS__']));
	}

	// --------------------------------------------------------------------

	/**
	 * Looks at the field type attribute and determines which form field to render
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @param	boolean shoud the normalization be ran again?
	 * @return	string
	 */
	public function create_field($params, $normalize = TRUE)
	{
		// needed to prevent runaway loops from custom fields
		if ($this->_rendering)
		{
			return FALSE;
		}
		
		if ($normalize) $params = $this->normalize_params($params); // done again here in case you create a field without doing the render method

		// now we look at all the fields that may represent other field types based on parameters
		if (!empty($this->representatives) AND is_array($this->representatives))
		{
			foreach($this->representatives as $key => $val)
			{
				$matched = FALSE;
				
				// if the represntative is an associative array with keys being parameters to match (e.g. type and name), then we loop through those parameters to find a match
				if (is_array($val) AND is_string(key($val)))
				{
					foreach($val as $k => $v)
					{
						$matched = (is_array($v) AND in_array($params[$k], $v) OR (is_string($v) AND preg_match('#'.$v.'#', $params[$k]))) ? TRUE : FALSE;
						if (!$matched)
						{
							break;
						}
					}
				}
				
				// if the representative is an array and the param type is in that array then we are a match
				else if (is_array($val) AND in_array($params['type'], $val))
				{
					$matched = TRUE;
				}
				
				// if the representative is a string, then we do a regex to see if we are a match
				else if (is_string($val) AND preg_match('#'.$val.'#', $params['type']))
				{
					$matched = TRUE;
				}
				
				// if we matched,then set the param type to it's representative and we break the loop and continue on
				if ($matched)
				{
					$params['type'] = $key;
					break;
				}
			}
		}
		
		$str = $this->_render_custom_field($params);
		
		if (!$str)
		{
			switch($params['type'])
			{
				case 'none': case 'blank' :
					$str = '';
					break;
				case 'custom':
					$func = (isset($params['func'])) ? $params['func'] : create_function('$params', 'return (isset($params["value"])) ? $params["value"] : "" ;');
					$str = $this->create_custom($func, $params);
					break;
				default : 
					$method = 'create_'.$params['type'];
					if (method_exists($this, $method))
					{
						$str = $this->$method($params);
					}
					else
					{
						$str = $this->create_text($params);
					}
			}
		}
		
		// add before/after html 
		$str = $params['before_html'].$str.$params['after_html'];
		return $str;
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the label for the form
	 *
	 * By default, if no label value is given, the method will generate one
	 * based on the name of the field
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @param	boolean shoud the label be displayed?
	 * @return	string
	 */
	public function create_label($params, $use_label = TRUE)
	{
		if (is_string($params))
		{
			$params = array('label' => $params);
		}
		
		$params = $this->normalize_params($params);
		
		$str = '';
		if (isset($params['display_label']) AND $params['display_label'] === FALSE) return $str;
		
		if (empty($params['label']))
		{
			if ($lang = $this->_label_lang($params['orig_name']))
			{
				$params['label'] = $lang;
			}
			else
			{
				$params['label'] = ucfirst(str_replace('_', ' ', $params['orig_name']));
				
			}
			$label_words = explode(' ', $params['label']);
			if (in_array(strtolower($label_words[0]), $this->question_keys))
			{
				$params['label'] .= '?';
			}
			
		}
		if ($use_label AND ($params['type'] != 'enum' AND $params['type'] != 'multi' AND $params['type'] != 'array'))
		{
			$str .= "<label for=\"".Form::create_id($params['orig_name'])."\" id=\"label_".Form::create_id($params['orig_name'])."\">";
		}
		if ($this->tooltip_labels)
		{
			$str .= $this->create_tooltip($params);
		} else {
			$str .= $params['label'];
		}
		if ($params['required'])
		{
			$str .= '<span class="required">'.$this->required_indicator.'</span>';
			$this->has_required = TRUE;
		}
		if ($params['label_colons']) $str .= ':';
		if ($use_label AND ($params['type'] != 'enum' AND $params['type'] != 'multi' AND $params['type'] != 'array'))
		{
			$str .= "</label>";
		}
		return $str;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Creates the text input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_text($params)
	{
		$params = $this->normalize_params($params);
		
		if (empty($params['size']))
		{
			if (!empty($params['max_length']))
			{
				$size = ($params['max_length'] > $this->text_size_limit) ? $this->text_size_limit : $params['max_length'];
			}
			else
			{
				$size = $this->text_size_limit;
			}
		}
		else
		{
			$size = $params['size'];
		}
		
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'maxlength' => $params['max_length'], 
			'size' => $size, 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'type' => (!empty($params['type']) ? $params['type'] : NULL), 
			'autocomplete' => (!empty($params['autocomplete']) ? $params['autocomplete'] : NULL),
			'placeholder' => (!empty($params['placeholder']) ? $params['placeholder'] : NULL),
			'required' => (!empty($params['required']) ? $params['required'] : NULL),
			'data' => $params['data'],
		);
		return $this->form->input($params['name'], $params['type'], $params['value'], $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a submit button input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_submit($params)
	{
		$params = $this->normalize_params($params);
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'data' => $params['data'],
		);
		return $this->form->submit($params['value'], $params['name'], $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a basic form button for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_button($params)
	{
		$params = $this->normalize_params($params);
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'data' => $params['data'],
		);
		$use_input_type = (!empty($params['use_input'])) ? TRUE : FALSE ;
		return $this->form->button($params['value'], $params['name'], $attrs, $use_input_type);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the textarea input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_textarea($params)
	{
		$params = $this->normalize_params($params);
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'rows' => $this->textarea_rows, 
			'cols' => $this->textarea_cols, 
			'readonly' => $params['readonly'], 
			'autocomplete' => (!empty($params['autocomplete']) ? $params['autocomplete'] : NULL),
			'placeholder' => (!empty($params['placeholder']) ? $params['placeholder'] : NULL),
			'required' => (!empty($params['required']) ? $params['required'] : NULL),
			'data' => $params['data'],
		);
		return $this->form->textarea($params['name'], $params['value'], $attrs);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the hidden input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_hidden($params)
	{
		$params = $this->normalize_params($params);
		
		// need to do check here because hidden is used for key_check
		$attrs = array(
			'id' => $params['id'],
			'data' => $params['data'],
		);
		if (!empty($params['class']))
		{
			$attrs['class'] = $params['class'];
		}
		return $this->form->hidden($params['name'], $params['value'], $attrs);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the either select field or a set of radio buttons
	 *
	 * If set to auto and their are less than 2 options, then it will render
	 * radio inputs. Otherwise it will render a select input
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_enum($params)
	{
		$defaults = array(
			'checked' => FALSE, // for radio
			'options' => array(),
			'mode' => NULL
			);
		$params = $this->normalize_params($params, $defaults);
		
		$i = 0;
		$str = '';
		$mode = (!empty($params['mode'])) ? $params['mode'] : $this->single_select_mode;
		if ($mode == 'radios' OR ($mode == 'auto' AND count($params['options']) <= 2))
		{
			$default = (isset($params['value'])) ? $params['value'] : FALSE;
			foreach($params['options'] as $key => $val)
			{
				$attrs = array(
					'readonly' => $params['readonly'], 
					'disabled' => $params['disabled'],
				);
				if (($i == 0 AND !$default) OR  ($default == $key))
				{
					$attrs['checked'] = 'checked';
				}
				$str .= $this->form->radio($params['name'], $key, $attrs);
				$name = Form::create_id($params['orig_name']);
				//$str .= ' <label for="'.$name.'_'.str_replace(' ', '_', $key).'">'.$val.'</label>';
				$enum_name = $name.'_'.Form::create_id($key);
				$label = ($lang = $this->_label_lang($enum_name)) ? $lang : $val;
				$enum_params = array('label' => $label, 'name' => $enum_name);
				$str .= ' '.$this->create_label($enum_params);
				$str .= "&nbsp;&nbsp;&nbsp;";
				$i++;
			}
		}
		else
		{
			$params['equalize_key_value'] = TRUE;
			$str .= $this->create_select($params);
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the multi select input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_multi($params)
	{
		$defaults = array(
			'sorting' => NULL,
			'mode' => NULL
		);
		$params = $this->normalize_params($params, $defaults);
		
		$str = '';
		$mode = (!empty($params['mode'])) ? $params['mode'] : $this->multi_select_mode;
		if ($mode == 'checkbox' OR ($mode == 'auto' AND count($params['options']) <= 5))
		{
			$value = (isset($params['value'])) ? (array)$params['value'] : array();

			$params['name'] = $params['name'].'[]';
			$i = 1;
			foreach($params['options'] as $key => $val)
			{
				$str .= '<span class="multi_field">';
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
				$str .= $this->form->checkbox($params['name'], $key, $attrs);
				
				$label = ($lang = $this->_label_lang($attrs['id'])) ? $lang : $val;
				$enum_params = array('label' => $label, 'name' => $attrs['id']);
				$str .= ' '.$this->create_label($enum_params);
				$str .= "&nbsp;&nbsp;&nbsp;";
				$str .= '</span>';
				$i++;
			}
		}
		else
		{
			$params['multiple'] = TRUE;
			$str .= $this->create_select($params);
			if (!empty($params['sorting']))
			{
				if ($params['sorting'] === TRUE && is_array($params['value']))
				{
					$params['sorting'] = $params['value'];
				}
				$sorting_params['name'] = 'sorting_'.$params['orig_name'];
				$sorting_params['value'] = rawurlencode(json_encode($params['sorting']));

				$str .= $this->create_hidden($sorting_params);
			}
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the select input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_select($params)
	{
		$defaults = array(
			'options' => array(),
			'first_option' => '',
		);
		
		$params = $this->normalize_params($params, $defaults);
	
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'required' => (!empty($params['required']) ? $params['required'] : NULL),
			'data' => $params['data'],
		);
		$name = $params['name'];
		if (!empty($params['multiple']))
		{
			$attrs['multiple'] = 'multiple';
			$name = $params['name'].'[]';
		}
		
		if (!empty($params['options']) AND !empty($params['equalize_key_value']))
		{
			$options = array();
			
			foreach($params['options'] as $key => $val)
			{
				$options[$val] = $val;
			}
			$params['options'] = $options;
		}
		
		return $this->form->select($name, $params['options'], $params['value'], $attrs, $params['first_option']);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the file input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_file($params)
	{
		$defaults = array(
			'overwrite' => NULL, // for file uploading
			'display_overwrite' => TRUE, // displays the overwrite checkbox
			'accept' => 'gif|jpg|jpeg|png', // for file uploading
			'upload_path' => NULL, // for file uploading
			'file_name' => NULL, // for file uploading
			'encrypt_name' => NULL,
			'data' => $params['data'],
		);

		$params = $this->normalize_params($params, $defaults);
	
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'required' => (!empty($params['required']) ? $params['required'] : NULL),
			'accept' => str_replace('|', ',', $params['accept']),
		);
		
		if (is_array($this->form_attrs))
		{
			$this->form_attrs['enctype'] = 'multipart/form-data';
		}
		else if (is_string($this->form_attrs) AND strpos($this->form_attrs, 'enctype') === FALSE)
		{
			$this->form_attrs .= ' enctype="multipart/form-data"';
		}
		
		$file = $this->form->file($params['name'], $attrs);
		if (isset($params['overwrite']))
		{
			$overwrite = ($params['overwrite'] == 1 OR $params['overwrite'] === TRUE OR $params['overwrite'] === 'yes' OR $params['overwrite'] === 'y') ? '1' : '0';
			if (!empty($params['display_overwrite']))
			{
				$file .= ' &nbsp; <span class="overwrite_field">'.$this->form->checkbox($params['name'].'_overwrite', 1, Form::do_checked($overwrite));
				$file .= ' '. $this->create_label($this->_label_lang('overwrite')).'</span>';
			}
			else
			{
				$file .= $this->form->hidden($params['name'].'_overwrite', $overwrite);
			}
		}
		if (isset($params['upload_path']))
		{
			$file .= $this->form->hidden($params['name'].'_path', $params['upload_path']);
		}
		if (isset($params['file_name']) OR isset($params['filename']))
		{
			$file_name = (isset($params['file_name'])) ? $params['file_name'] : $params['filename'];
			$file .= $this->form->hidden($params['name'].'_file_name', $file_name);
		}
		if (isset($params['encrypt']) OR isset($params['encrypt_name']))
		{
			$encrypt_name = (isset($params['encrypt_name'])) ? $params['encrypt_name'] : $params['encrypt'];
			$file .= $this->form->hidden($params['name'].'_encrypt_name', $encrypt_name);
		}
		return $file;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the date input for the form
	 *
	 * Adds the datepicker and fillin classes so that you can use jquery to 
	 * add the datepicker and fillin jquery plugins
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_date($params)
	{
		$params = $this->normalize_params($params);
		if (empty($params['date_format']))
		{
			$params['date_format'] = $this->date_format;
		}
		
		// check date to format it
		if ((!empty($params['value']) AND (int) $params['value'] != 0)
			&& (preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})#", $params['value'], $regs1) 
			|| preg_match("#([0-9]{1,2})[/\-]([0-9]{1,2})[/\-]([0-9]{4})#", $params['value'], $regs2)))
		{
			$params['value'] = date($params['date_format'], strtotime($params['value']));
		} else {
			$params['value'] = '';
		}
		
		//$params['class'] = 'datepicker '.$params['class'];
		$params['maxlength'] = 10;
		$params['size'] = 10;
		return $this->create_text($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the time input for the form
	 *
	 * Adds the fillin class so that you can use fillin jquery plugins
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_time($params)
	{
		$params = $this->normalize_params($params);

		if (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND $params['value'] != '0000-00-00 00:00:00')
		{
			$time_params['value'] = date('g', strtotime($params['value']));
		}
		$time_params['size'] = 2;
		$time_params['max_length'] = 2;
		$time_params['name'] = $params['orig_name'].'_hour';
		$time_params['class'] = 'fillin datepicker_hh';
		$time_params['disabled'] = $params['disabled'];
		$str = $this->create_text($this->normalize_params($time_params));
		$str .= ":";
		if (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND $params['value'] != '0000-00-00 00:00:00') $time_params['value'] = date('i', strtotime($params['value']));
		$time_params['name'] = $params['orig_name'].'_min';
		$time_params['class'] = 'fillin datepicker_mm';
		$str .= $this->create_text($this->normalize_params($time_params));
		$ampm_params['options'] = array('am' => 'am', 'pm' => 'pm');
		$ampm_params['name'] = $params['orig_name'].'_am_pm';
		$ampm_params['value'] = (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND date('H', strtotime($params['value'])) >= 12) ? 'pm' : 'am';
		$ampm_params['disabled'] = $params['disabled'];
		$str .= $this->create_enum($this->normalize_params($ampm_params));
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the date/time input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_datetime($params)
	{
		$str = $this->create_date($params);
		$str .= ' ';
		$str .= $this->create_time($params);
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a number field for the form... basically a text field
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_number($params)
	{
		$numeric_class = 'numeric';
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$numeric_class : $numeric_class;
		$params['type'] = 'number';
		$decimal = (!empty($params['decimal'])) ? (int) $params['decimal'] : 0;
		$negative = (!empty($params['negative'])) ? 1 : 0;
		
		if (empty($params['size']))
		{
			$params['size'] = 10;
		}

		if (empty($params['max_length']))
		{
			$params['max_length'] = 10;
		}

		// set data values for jquery plugin to use
		$params['data'] = array(
			'decimal' => $decimal,
			'negative' => $negative,
			);
		return $this->create_text($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates an email field for the form... supported by modern browsers
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_email($params)
	{
		$email_class = 'email';
		$params['type'] = 'email';
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$email_class : $email_class;
		return $this->create_text($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates an telphone field for the form... supported by modern browsers
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_phone($params)
	{
		$email_class = 'phone';
		$params['type'] = 'tel';
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$email_class : $email_class;
		return $this->create_text($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the checkbox input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_checkbox($params)
	{
		$defaults = array(
			'checked' => FALSE // for checkbox/radio
		);
		$params = $this->normalize_params($params, $defaults);

		$str = '';
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'],
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'data' => $params['data'],
		);
		if ($params['checked'])
		{
			$attrs['checked'] = 'checked';
		}
		$str .= $this->form->checkbox($params['name'], $params['value'], $attrs);
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates either a checkbox or a radio input for the form
	 *
	 * This method check the boolean_mode class attribute to determine
	 * what type of field, either checkbox or radio, to render
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_boolean($params)
	{
		if ($this->boolean_mode == 'checkbox')
		{
			return $this->create_checkbox($params);
		}
		else
		{
			return $this->create_enum($params);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a section in the form
	 *
	 * First checks the value, then the label, then the name attribute
	 * then wraps it in the section_tag
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_section($params)
	{
		$params = $this->normalize_params($params);
		$section = $this->_simple_field_value($params);
		$tag = (empty($params['tag'])) ? $this->section_tag : $params['tag'];
		return '<'.$tag.'>'.$section.'</'.$tag.'>';
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a copy area for for the form
	 *
	 * First checks the value, then the label, then the name attribute
	 * then wraps it in the copy_tag
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_copy($params)
	{
		$params = $this->normalize_params($params);
		$copy = $this->_simple_field_value($params);
		$tag = (empty($params['tag'])) ? $this->copy_tag : $params['tag'];
		return '<'.$tag.'>'.$copy.'</'.$tag.'>';
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a tooltip on the label
	 *
	 * Creates a tooltip on the label. Uses the tooltip_format class attribute
	 * to determine how to render tooltip
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_tooltip($params)
	{
		$params = $this->normalize_params($params);

		$str = '';
		if (!empty($params['comment']))
		{
			$tooltip = $params['comment'];
		}
		else if (!empty($params['description']))
		{
			$tooltip = $params['description'];
		}
		if (!empty($tooltip))
		{
			$str = str_replace(array('{?}', '[?]'), array($tooltip, $params['label']), $this->tooltip_format);
		}
		else
		{
			$str = $params['label'];
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a read only field
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_readonly($params)
	{
		$params = $this->normalize_params($params);
		$str = $params['value']."\n".$this->create_hidden($val);
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates a legend for a form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_fieldset($params)
	{
		$params = $this->normalize_params($params);
		$attrs = array(
			'class' => $params['class'], 
		);
		$str = '';
		$legend = $this->_simple_field_value($params);
		$str = "\n";
		if (isset($params['open']) AND $params['open'] === FALSE)
		{
			$str .= $this->form->fieldset_close();
		}
		else
		{
			$str .= $this->form->fieldset_open($legend, $attrs);
		}
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a nested form_builder object and renders it
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_nested($params)
	{
		$CI =& get_instance();
		$CI->load->library('parser');
		$fb_class = get_class($this);
		
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
		
		$form_builder = new $fb_class($params['init']);
		$form_builder->set_fields($params['fields']);
		$form_builder->submit_value = '';
		$form_builder->set_validator($this->form->validator);
		$form_builder->use_form_tag = FALSE;
		$form_builder->set_field_values($params['value']);
		return $form_builder->render();
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a custom input form field
	 *
	 * Calls a function and passes it the field params
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_custom($func, $params)
	{
		$params = $this->normalize_params($params);
		
		// give custom fields a reference to the current object
		$params['instance'] =& $this;
		
		if (!empty($params['js']))
		{
			$this->_js[$params['type']] = $params['js'];
		}
		
		// render
		return call_user_func($func, $params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a custom field type
	 *
	 * @access	public
	 * @param	string key used to identify the field type
	 * @param	string function or class/method array to use for rendering
	 * @return	void
	 */
	public function register_custom_field($key, $custom_field)
	{
		// if an array, then we will assess the properties of the array and load classes/helpers appropriately
		if (is_array($custom_field))
		{
			$CI =& get_instance();
			
			// must have at least a function value otherwise you get nada
			if (empty($custom_field['function']))
			{
				if (method_exists($this, 'create_'.$key))
				{
					$custom_field['function'] = array($this, 'create_'.$key);
				}
				else
				{
					return FALSE;
				}
			}

			// if there's a filepath, then load it and instantiate the any class '
			if (!empty($custom_field['filepath']))
			{
				require_once($custom_field['filepath']);
				if (!empty($custom_field['class']))
				{
					$class = new $custom_field['class']();
					$func = array($class, $custom_field['function']);
				}
				else
				{
					$func = $custom_field['function'];
				}
			}
			
			// if class parameter is set, then it will assume it's a library'
			else if (!empty($custom_field['class']))
			{
				if (is_array($custom_field['class']))
				{
					$module = key($custom_field['class']);
					$library = strtolower(current($custom_field['class']));
					$CI->load->module_library($module, $library);
				}
				else
				{
					$CI->load->library($custom_field['class']);
					$library = strtolower($custom_field['class']);
				}
				$func = array($CI->$library, $custom_field['function']);
			}
			
			// if no class parameter is set, then it will assume it's a helper'
			else 
			{
				if (!is_callable($custom_field['function']))
				{
					if (is_array($custom_field['function']) AND is_string(key($custom_field['function'])))
					{
						$module = key($custom_field['function']);
						$helper = current($custom_field['function']);
						$CI->load->module_library($module, $helper, $custom_field);
					}
					else 
					{
						$CI->load->helper($custom_field['function'], $custom_field);
					}
				}
				$func = $custom_field['function'];
			}
			$params = $custom_field;
		}
		
		// if it's a simple function, then we'll just use this for rendering
		else if (is_callable($custom_field))
		{
			$func = $custom_field;
		}
		else
		{
			return FALSE;
		}
		
		$params['render_func'] = $func;
		
		$field = new Form_builder_field($params);
		
		$this->custom_fields[$key] = $field;
		
		
		// says whether this field can represent other field types
		if (!empty($params['represents']))
		{
			$this->representatives[$key] = $params['represents'];
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the validator object on the form object
	 *
	 * The validator object is used to determine if the fields have been
	 * filled out properly and will display any errors at the top of the form
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function set_validator(&$validator)
	{
		$this->form->validator = $validator;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the order of the fields
	 *
	 * An array value with the keys as the order for the field
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function set_field_order($order_arr = array())
	{
		// normalize
		foreach($this->_fields as $key => $val)
		{
			$this->_fields[$key] = $this->normalize_params($val);
		}
		if (!empty($order_arr))
		{
			$num = count($order_arr) - 1;
			for ($i = 0; $i < $num; $i++)
			{
				if (isset($this->_fields[$order_arr[$i]]))
				{
					$this->_fields[$order_arr[$i]]['order'] = $i;
				}
			}
		}
		$this->_fields = $this->_fields_sorter($this->_fields, 'order');
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Appends HTML to the form
	 *
	 * Used to append HTML after the form... good for Javascript files
	 * 
	 * @access	public
	 * @param	string HTML to append
	 * @return	void
	 */
	public function append_html($html)
	{
		$this->html_append .= $html;
	}

	// --------------------------------------------------------------------

	/**
	 * Prepends HTML to the form
	 *
	 * Used to prepend HTML before the form... good for Javascript files
	 * 
	 * @access	public
	 * @param	string HTML to prepend
	 * @return	void
	 */
	public function prepend_html($html)
	{
		$this->html_prepend .= $html;
	}

	// --------------------------------------------------------------------

	/**
	 * Alters all the field values that have pre_process attribute specified
	 *
	 * @access	public
	 * @return	void
	 */
	public function pre_process_field_values()
	{
		foreach($this->_fields as $key => $field)
		{
			if (!empty($field['pre_process']))
			{
				$process = $this->_normalize_process_func($field['post_process'], $field['value']);
				$func = $process['func'];
				$params = $process['params'];
				if ($func == 'remove' OR $func == 'clear')
				{
					$func = create_function('$value, $field', 'return "";');
				}
				if (is_array($func) AND isset($func[0]))
				{
					$params = $func;
					
					// the first parameter is the function name. This will return it and remove it from the array
					$func = array_shift($params);
					
					// now add the current value as the first parameter
					$func_params[] = $field['value'];
					$func_params = array_merge($func_params, $params);
					$this->_fields[$key]['value'] = call_user_func_array($func, $func_params);
				}
				else
				{
					$this->_fields[$key]['value'] = call_user_func($func, $field['value'], $field);
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Alters all the field post values that have post_process attribute specified
	 *
	 * @access	public
	 * @return	void
	 */
	public function post_process_field_values($posted = array(), $set_post = TRUE)
	{
		if (empty($posted)) $posted = $_POST;
		foreach($this->_fields as $key => $field)
		{
			if (!empty($field['post_process']) AND isset($posted[$key]))
			{
				$func = $field['post_process'];
				if ($func == 'remove' OR $func == 'clear')
				{
					$func = create_function('$value', 'return "";');
				}
				
				if (is_array($func) AND isset($func[0]))
				{
					$params = $func;
					
					// the first parameter is the function name. This will return it and remove it from the array
					$func = array_shift($params);
					
					// now add the current value as the first parameter
					$func_params[] = $posted[$key];
					$func_params = array_merge($func_params, $params);
					$posted[$key] = call_user_func_array($func, $func_params);
				}
				else
				{
					$posted[$key] = call_user_func($func, $posted[$key], $field);
				}
				
				if ($set_post)
				{
					$_POST[$key] = $posted[$key];
				}
			}
		}
		return $posted;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets a representative for a field type.
	 * 
	 * This allows for one field type to represent several field types (e.g. number = int, bigint, smallint, tinyint,...etc)
	 *
	 * @access	public
	 * @param	string The field type to be the representative
	 * @param	mixed Either an array or a regex that other fields must match
	 * @return	void
	 */
	public function set_representative($type, $match = '')
	{
		$this->representatives[$type] = $match;
	}

	// --------------------------------------------------------------------

	/**
	 * Looks for value, label, then name as the values
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function _simple_field_value($params)
	{
		if (is_array($params))
		{
			if (!empty($params['value']))
			{
				$str = $params['value'];
			}
			else if (!empty($params['label']))
			{
				$str = $params['label'];
			}
			else if (!empty($params['name']))
			{
				$str = $params['name'];
			}
		}
		else
		{
			$str = $params;
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Normalizes the processing function to be used in post/pre processing of a field
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function _normalize_process_func($process, $value)
	{
		$params = array($value);
		if (is_array($process))
		{
			if (isset($process['func']))
			{
				$func = $process['func'];

				// set any additional parameters
				if (isset($process['params']))
				{
					$params = array_merge($params, $process['params']);
				}
			}
			else
			{
				$func = current($process);
				array_shift($process);
				$params = array_merge($params, $process);
			}
		}
		else
		{
			$func = $process;
		}
		
		// shorthand if the function name is remove or clear, then we return an empty string
		if ($func == 'remove' OR $func == 'clear')
		{
			$func = create_function('$value', 'return "";');
		}
		
		return array('func' => $func, 'params' => $params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sorts the fields for the form
	 *
	 * Same as the MY_array_helper array_sorter function
	 * 
	 * @access	protected
	 * @param	array fields parameters
	 * @return	string
	 */
	protected function _fields_sorter($array, $index, $order = 'asc', $nat_sort = FALSE, $case_sensitive = FALSE)
	{
		if (is_array($array) AND count($array) > 0)
		{
			foreach (array_keys($array) as $key)
			{
				$temp[$key] = $array[$key][$index];
				if (!$nat_sort)
				{
					($order=='asc') ? asort($temp) : arsort($temp);
				} 
				else
				{
					($case_sensitive) ? natsort($temp) : natcasesort($temp);
				}
				if ($order != 'asc') $temp = array_reverse($temp,TRUE);
			}
			
			foreach(array_keys($temp) as $key)
			{
				(is_numeric($key)) ? $sorted[] = $array[$key] : $sorted[$key] = $array[$key];
			}
			return $sorted;
		}
		return $array;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the language key value if it exist, otherwise it returns FALSE
	 * 
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _label_lang($key)
	{
		if (isset($this->lang_prefix) AND function_exists('lang') AND $lang = lang($this->lang_prefix.$key))
		{
			return $lang;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders the javascript for fields (only once)
	 * 
	 * @access	protected
	 * @return	string
	 */
	protected function _render_js()
	{
		if (empty($this->_js)) return '';
		
		$str = '';
		$str_inline = '';
		$str_files = '';
		$js_exec = array();
		$script_regex = '#^<script(.+)src=#U';

		foreach($this->_js as $type => $js)
		{
			// if $js is a PHP array and the js asset function exists, then we'll use that to render'
			if (is_array($js))
			{
				$j = current($js);
				
				// TODO if the value is another array, then the key is the name of the function and the value is the name of a file to load
				if (is_array($j))
				{
					
				}
				$str_files .= js($js);
			}
			// if it starts with a script tag and does NOT have a src attribute
			else if (preg_match($script_regex, $js))
			{
				$str_files .= $js;
			}
			
			// if it starts with a script tag and DOES have a src attribute
			else if (!preg_match($script_regex, $js))
			{
				$str_inline .= $js;
			}
			else
			{
				$str .= "if (".$js." != undefined){\n";
				$str .= "\t".$js."();\n";
				$str .= "}\n";
			}
			
			// check if the field type has a js function to call 
			if (isset($this->custom_fields[$type], $this->custom_fields[$type]->js_function))
			{
				$cs_field = $this->custom_fields[$type];
				$js_options = (!empty($cs_field->js_params)) ? $cs_field->js_params : NULL;
				$js_exec_order = (!empty($cs_field->js_exec_order)) ? $cs_field->js_exec_order : 0;
				$js_exec[$type] = array('func' => $cs_field->js_function, 'options' => $js_options, 'order' => $js_exec_order);
				
			}
			
		}
		
		// sort the javascript
		$js_exec = $this->_fields_sorter($js_exec, 'order');
		
		$out = $str_files;
		$out .= $str_inline;
		$out .= "<script type=\"text/javascript\">\n";
		$out .= "//<![CDATA[\n";
		$out .= "";
		$out .= $str."\n";
		$out .= 'if (jQuery.fn.formBuilder) {';
		$out .= '$("#'.$this->id.'").formBuilder('.json_encode($js_exec).');';
		if ($this->auto_execute_js) $out .= '$("#'.$this->id.'").formBuilder().initialize();';
		$out .= '}';
		$out .= "\n//]]>\n";
		$out .= "</script>\n";
		return $out;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Applies the CSS for the fields. A variable of $css must exist for the page to render
	 * 
	 * @access	protected
	 * @return	string
	 */
	protected function _apply_css()
	{
		// echo "<pre style=\"text-align: left;\">";
		// print_r($this->_css);
		// echo "</pre>";
		if (empty($this->_css)) return;

		foreach($this->_css as $type => $js)
		{
			// if $js is a PHP array and the js asset function exists, then we'll use that to render'
			// if (is_array($js))
			// {
			// 	$j = current($js);
			// 	
			// 	// TODO if the value is another array, then the key is the name of the function and the value is the name of a file to load
			// 	if (is_array($j))
			// 	{
			// 		
			// 	}
			// 	$str_files .= js($js);
			// }
		}
	}
}


// ------------------------------------------------------------------------

/**
 * A custom field class for Form_builder
 *
 * The Form.php class is required if a form object is not passed in the 
 * initialization process.
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/form_builder.html
 */

Class Form_builder_field {
	
	public $type = ''; // the type value of the field (e.g. textare, enum, datetime)
	public $render_func = array();
	public $js_class = ''; // the CSS class used by the javascript to execute any javascript on the field
	public $js_params = array(); // parameter to pass to the javascript function
	public $js_exec_order = 1; // the order in which the javascript should be executed in relation to other fields... lower the sooner
	public $html = ''; // html output for form field
	public $js = array(); // the name of javascript file(s) to laod
	public $js_function = ''; // the name of the javascript function to execute for the form field
	public $represents = ''; // the field types this form field will represent  (e.g. 'number'=>'bigint|smallint|tinyint|int')
	public $css = ''; // a CSS file to load for this form field
	public $css_class = ''; // a CSS class to automatically apply to the form field... very convenient for simply adding JS functionality to an existing field type
	
	/**
	 * Constructor - Sets Form_builder preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		$this->initialize($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($params = array())
	{
		foreach ($params as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

	}
	
	// --------------------------------------------------------------------

	/**
	 * Overwrite to display
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function render($params = array())
	{
		// add the CSS any css class as an additional parameter
		if (!empty($this->css_class))
		{
			$params['class'] = $this->css_class.' '.$params['class'];
		}
		
		if (!empty($this->render_func))
		{
			return call_user_func($this->render_func, $params);
		}
		else
		{
			return (string)$this->html;
		}
	}
}

/* End of file Form_builder.php */
/* Location: ./application/libraries/Form_builder.php */
