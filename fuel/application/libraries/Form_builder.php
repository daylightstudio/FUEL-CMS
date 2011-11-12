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
 * An auto form builder
 *
 * This class allows you to create forms by passing in configurable
 * array values. For a list of what those field values can be, look at the
 * _normalize_value method. It works well with the MY_Model form_fields
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
	public $key_check = ''; // the keycheck value used for forms that create session unique session variables to prevent spamming
	public $key_check_name = ''; // the keycheck form name used for forms that create session unique session variables to prevent spamming
	public $tooltip_format = '<span title="{?}" class="tooltip">[?]</span>'; // tooltip formatting string
	public $tooltip_labels = TRUE; // use tooltip labels?
	public $single_select_mode = 'auto'; // auto will use enum if 2 or less and a single select if greater than 2. Other values are enum or select 
	public $multi_select_mode = 'auto'; // auto will use a series of checkboxes if 5 or less and a multiple select if greater than 5. Other values are multi or checkbox 
	public $boolean_mode = 'checkbox'; // booleon mode can be checkbox or enum (which will display radio inputs)
	public $display_errors_func = 'display_errors'; // the function used to generate errors... usually display_errors is the name
	public $display_errors = FALSE; // displays errors at the top of the form if TRUE
	public $question_keys = array('how', 'do', 'when', 'what', 'why', 'where', 'how', 'is', 'which', 'did', 'any'); // adds question marks to the label if has these words in the label
	public $show_required = TRUE; // show the required fields text at the bottom of the form
	public $required_indicator = '*'; // indicator for a required field
	public $required_text = '<span class="required">{required_indicator}</span> required fields'; // the required field text
	public $label_layout = 'left'; // label layout... can be left or top
	public $has_required = FALSE;
	public $render_format = 'table';
	public $row_id_prefix = '';
	public $lang_prefix = 'form_label_';
	
	protected $_html;
	protected $_fields = array();
	
	
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
		
		$CI =& get_instance();
		
		// create form object if not in initialization params
		if (is_null($this->form))
		{
			$CI->load->library('form');
			$this->form = new Form();
			
			// load localization helper if not already
			if (!function_exists('lang'))
			{
				$CI->load->helper('language');
			}
		}
		
		// CSRF protections
		if ($CI->config->item('csrf_protection') === TRUE AND empty($this->key_check))
		{
			$CI->security->csrf_set_cookie(); // need to set it again here just to be sure ... on initial page loads this may not be there
			$this->key_check = $CI->security->csrf_hash;
			$this->key_check_name = $CI->security->csrf_token_name;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Clear fields and html
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear()
	{
		$this->_fields = array();
		$this->_html = '';
	}

	// --------------------------------------------------------------------

	/**
	 * Set the fields for the form
	 * 
	 * Check the _normalize_value method for possible values
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
			if (is_string($val))
			{
				$fields[$key] = array('name' => $key, 'value' => $val);
			}
			if (empty($val['name'])) $fields[$key]['name'] = $key;
			if (empty($fields[$key]['order'])) $fields[$key]['order'] = $i;
			$i++;
		}
		$this->_fields = $fields;
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
		$this->_html = '';
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
		
		$str .= "<div class=\"".$this->css_class."\"";
		$str .= (!empty($this->id)) ? ' id="'.$this->id.'"' : '';
		$str .= ">\n";
		foreach($this->_fields as $key => $val)
		{
			$val = $this->_normalize_value($val);
			if ($val['type'] == 'section')
			{
				$str .= "<div class=\"section\">".$this->create_section($val)."</div>\n";
				continue;
			}
			else if (!empty($val['section']))
			{
				$str .= "<div class=\"section\"><".$this->section_tag.">".$val['section']."</".$this->section_tag."></div>\n";
				continue;
			}
			
			if ($val['type'] == 'copy')
			{
				$str .= "<div class=\"copy\">".$this->create_copy($val)."</div>\n";
				continue;
			}
			else if (!empty($val['copy']))
			{
				$str .= "<div class=\"copy\"><".$this->copy_tag.">".$val['copy']."</".$this->copy_tag."></div>\n";
				continue;
			}
			
			if (!empty($val['custom']))
			{
				$str .= "<div";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= " class=\"field\">";
				$str .= $this->create_label($val, TRUE);
				$str .= $val['before_html'].$val['custom'].$val['after_html'];
				$str .= "</div>\n";
			}
			else if (in_array($val['name'], $this->hidden) OR $val['type'] == 'hidden')
			{
				$end_str .= $this->create_hidden($val);
			}
			else if ((is_array($val['name']) AND in_array($val['name'], $this->displayonly)) OR  $val['displayonly'] OR  (is_string($this->displayonly) AND strtolower($this->displayonly) == 'all'))
			{
				$str .= "<div";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= " class=\"field\">";
				$str .= $this->create_label($val, FALSE);
				$str .= $val['value']."\n";
				$str .= $this->create_hidden($val);
				$str .= "</div>\n";
			}
			else if (!in_array($val['name'], $this->exclude))
			{
				$str .= "<div";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= " class=\"field\">";
				$str .= $this->create_label($val, TRUE);
				$str .= $val['before_html'].$this->create_field($val, FALSE).$val['after_html'];
				$str .= "</div>\n";
			}
		}
		$str .= "<div class=\"actions\">";
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
				$str .= $this->form->$submit_btn($this->submit_value, $this->submit_value, array('class' => 'submit'));
			}
		}
		if (!empty($this->other_actions)) $str .= $this->other_actions;
		$str .= "</div>\n";
		if ($this->has_required AND $this->show_required)
		{
			$str .= "<div class=\"required\">";
			$str .= str_replace('{required_indicator}', $this->required_indicator, $this->required_text);
			$str .= "</div>\n";
		}
		$str .= "</div>\n";
		
		if ($this->use_form_tag) 
		{
			$this->_html .= $this->form->open($this->form_attrs);
		}
		if (!empty($this->fieldset))
		{
			$this->_html .= $this->form->fieldset_open($this->fieldset);
		}
		$this->_html .= $begin_str;
		$this->_html .= $str;
		$this->_html .= $end_str;
		
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
		$this->_html = '';
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
		
		$str .= "<table class=\"".$this->css_class."\"";
		$str .= (!empty($this->id)) ? ' id="'.$this->id.'"' : '';
		$str .= ">\n";
		foreach($this->_fields as $key => $val)
		{
			$val = $this->_normalize_value($val);
			if ($val['type'] == 'section')
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"section\">".$this->create_section($val)."</td>\n</tr>\n";
				continue;
			}
			else if (!empty($val['section']))
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"section\"><".$this->section_tag.">".$val['section']."</".$this->section_tag."></td>\n</tr>\n";
				continue;
			}
			
			if ($val['type'] == 'copy')
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"copy\">".$this->create_copy($val)."</td></tr>\n";
				continue;
			}
			else if (!empty($val['copy']))
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td colspan=\"".$colspan."\" class=\"copy\"><".$this->copy_tag.">".$val['copy']."</".$this->copy_tag."></td>\n</tr>\n";
				continue;
			}
			
			if (!empty($val['custom']))
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td class=\"label\">";
				if ($this->label_layout != 'top')
				{
					$str .= $this->create_label($val, TRUE);
					$str .= "</td>\n\t<td class=\"value\">".$val['before_html'].$val['custom'].$val['after_html']."</td>\n</tr>\n";
				}
				else
				{
					$str .= $this->create_label($val, TRUE)."</td></tr>\n";
					$str .= "<tr";
					if (!empty($this->row_id_prefix))
					{
						$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
					}
					$str .= ">\n\t<td class=\"value\">".$val['before_html'].$val['custom'].$val['after_html']."</td>\n</tr>\n";
				}
			}
			else if (in_array($val['name'], $this->hidden) OR  $val['type'] == 'hidden')
			{
				$end_str .= $this->create_hidden($val);
			}
			else if ((is_array($val['name']) AND in_array($val['name'], $this->displayonly))  OR  $val['displayonly'] OR  (is_string($this->displayonly) AND strtolower($this->displayonly) == 'all') OR $this->displayonly === TRUE)
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td class=\"label\">";
				if ($this->label_layout != 'top')
				{
					$str .= $this->create_label($val, FALSE);
					$str .= "</td>\n\t<td class=\"value\">".$val['before_html'].$val['value'].$val['after_html']."\n".$this->create_hidden($val)."</td>\n</tr>\n";
				}
				else
				{
					$str .= $this->create_label($val, FALSE)."</td></tr>\n";
					$str .= "<tr";
					if (!empty($this->row_id_prefix))
					{
						$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
					}
					$str .= ">\n\t<td class=\"value\">".$val['value']."\n".$this->create_hidden($val)."</td>\n</tr>\n";
				}
			}
			else if (!in_array($val['name'], $this->exclude))
			{
				$str .= "<tr";
				if (!empty($this->row_id_prefix))
				{
					$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
				}
				$str .= ">\n\t<td class=\"label\">";
				if ($this->label_layout != 'top')
				{
					$str .= $this->create_label($val, TRUE);
					$str .= "</td>\n\t<td class=\"value\">".$val['before_html'].$this->create_field($val, FALSE).$val['after_html']."</td>\n</tr>\n";
				}
				else
				{
					$str .= $this->create_label($val, TRUE)."</td></tr>\n";
					$str .= "<tr";
					if (!empty($this->row_id_prefix))
					{
						$str .= ' id="'.$this->row_id_prefix.Form::create_id($val['name']).'"';
					}
					$str .= ">\n\t<td class=\"value\">".$val['before_html'].$this->create_field($val, FALSE).$val['after_html']."</td>\n</tr>\n";
				}
			}
		}
		if ($this->label_layout != 'top')
		{
			$str .= "<tr";
			if (!empty($this->row_id_prefix))
			{
				$str .= ' id="'.$this->row_id_prefix.'actions"';
			}
			$str .= ">\n\t<td></td>\n\t<td class=\"actions\">";
		}
		else
		{
			$str .= "<tr>\n\t<td class=\"actions\">";
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
				$str .= $this->form->$submit_btn($this->submit_value, $this->submit_value, array('class' => 'submit'));
			}
		}
		if (!empty($this->other_actions)) $str .= $this->other_actions;
		$str .= "</td>\n</tr>\n";
		if ($this->has_required AND $this->show_required)
		{
			$str .= "<tr>\n\t<td colspan=\"".$colspan."\" class=\"required\">";
			$str .= str_replace('{required_indicator}', $this->required_indicator, $this->required_text);
			$str .= "</td>\n</tr>\n";
		}
		$str .= "</table>\n";
		
		if ($this->use_form_tag) $this->_html .= $this->form->open($this->form_attrs);
		if (!empty($this->fieldset)) $this->_html .= $this->form->fieldset_open($this->fieldset);
		$this->_html .= $begin_str;
		$this->_html .= $str;
		$this->_html .= $end_str;
		if (!empty($this->key_check)) $this->_html .= $this->create_hidden(array('name' => $this->key_check_name, 'value' => $this->key_check));
		if (!empty($this->fieldset)) $this->_html .= $this->form->fieldset_close();
		if ($this->use_form_tag) $this->_html .= $this->form->close();
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
	protected function _normalize_value($val, $force = FALSE)
	{
		// check to see if the array is already normalized
		if ($this->_is_normalized($val) AND !$force)
		{
			return $val;
		}
		
		if (is_object($val)) $val = get_object_vars($val);
		
		$defaults = array(
			'name' => '',
			'type' => '',
			'default' => '',
			'max_length' => 0,
			'comment' => '',
			'label' => '',
			'required' => FALSE,
			'size' => '',
			'class' => '',
			'options' => array(),
			'checked' => FALSE, // for checkbox/radio
			'value' => '',
			'readonly' => '',
			'disabled' => '',
			'order' => 9999,
			'first_option' => '', // for the select
			'before_html' => '', // for html before the field
			'after_html' => '', // for html after the field
			'displayonly' => FALSE,
			'overwrite' => NULL, // for file uploading
			'accept' => 'gif|jpg|jpeg|png', // for file uploading
			'upload_path' => NULL, // for file uploading
			'filename' => NULL, // for file uploading
			'sorting' => NULL, // for multi selects that may need to keep track of selected options (combo jquery plugin)
			'mode' => NULL, // used for enums and multi fields whether to use selects or radios/checkbox
			'__NORMALIZED__' => TRUE // set so that we no that the array has been processed and we can check it so it won't process it again'
		);
		
		$params = array_merge($defaults, $val);
		
		if (empty($params['orig_name'])) $params['orig_name'] = $params['name']; // for labels in case the name_array is used
		if (!isset($val['value']) AND ($params['type'] != 'checkbox' AND !($params['type'] == 'boolean' AND $this->boolean_mode == 'checkbox')))
		{
			$params['value'] = $params['default'];
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
			$params['name'] = $this->name_array.'['.$params['orig_name'].']';
			if (in_array($params['orig_name'], $this->hidden) AND !in_array($params['name'], $this->hidden)) $this->hidden[] = $params['name'];
		}

		if (!empty($this->name_prefix))
		{
			$params['name'] = $this->name_prefix.'--'.$params['orig_name']; // used double hyphen so easier to explode
			if (in_array($params['orig_name'], $this->hidden) AND !in_array($params['name'], $this->hidden)) $this->hidden[] = $params['name'];
		}
		if (($params['type'] == 'enum' OR  $params['type'] == 'select') AND (empty($params['options']) AND is_array($params['options'])) AND is_array($params['max_length']) AND !empty($params['max_length']))
		{
			$params['options'] = $params['max_length'];
		//	unset($params['max_length']);
		}
		
		// fix common errors
		if (!empty($params['maxlength']) AND empty($params['max_length'])) 
		{
			$params['max_length'] = $params['maxlength'];
			unset($params['maxlength']);
		}
		return $params;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Checks to see if the array to initialize a field is normalized or not
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	protected function _is_normalized($vals)
	{
		return (!empty($vals['__NORMALIZED__']));
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
		if ($normalize) $params = $this->_normalize_value($params); // done again here in case you create a field without doing the render method
		switch($params['type']){
			case 'text' : case 'textarea' : case 'longtext' :  case 'mediumtext' :
				return $this->create_textarea($params);
				break;
			case 'enum' :
				return $this->create_enum($params);
				break;
			case 'boolean' :
				return $this->create_boolean($params);
				break;
 			case 'checkbox' :
				return $this->create_checkbox($params);
				break;
			case 'select' :
				return $this->create_select($params);
				break;
			case 'date' : 
				return $this->create_date($params);
				break;
			case 'datetime': case 'timestamp' :
				$str = $this->create_date($params);
				$str .= ' ';
				$str .= $this->create_time($params);
				return $str;
				break;
			case 'time' :
				$str = $this->create_time($params);
				return $str;
				break;
			case 'multi' : case 'array' :
				return $this->create_multi($params);
				break;
			case 'blob' : case 'file' :
				return $this->create_file($params);
				break;
			case 'submit':
				return $this->create_submit($params);
				break;
			case 'button':
				return $this->create_button($params);
				break;
			case 'none': case 'blank':
				return '';
				break;
			case 'custom':
				$func = (isset($params['func'])) ? $params['func'] : create_function('', '');
				return $this->create_custom($func, $params);
				break;
			default : 
				return $this->create_text($params);
		}
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
		
		$params = $this->_normalize_value($params);
		
		$str = '';
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
		if ($this->label_colons) $str .= ':';
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
		$params = $this->_normalize_value($params);
		
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
			'class' => $params['class'], 
			'maxlength' => $params['max_length'], 
			'size' => $size, 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled']
		);
		if ($params['type'] == 'password')
		{
			return $this->form->password($params['name'], $params['value'], $attrs);
		}
		return $this->form->text($params['name'], $params['value'], $attrs);
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
		$params = $this->_normalize_value($params);
		$attrs = array(
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled']
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
		$params = $this->_normalize_value($params);
		$attrs = array(
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled']
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
		$params = $this->_normalize_value($params);
		
		$attrs = array(
			'class' => $params['class'], 
			'rows' => (!empty($params['rows'])) ? $params['rows'] : $this->textarea_rows, 
			'cols' => (!empty($params['cols'])) ? $params['cols'] : $this->textarea_cols, 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled']
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
		$params = $this->_normalize_value($params);
		
		// need to do check here because hidden is used for key_check
		$attrs = array();
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
		$params = $this->_normalize_value($params);
		
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
					'disabled' => $params['disabled']
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
		$params = $this->_normalize_value($params);
		
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
		$params = $this->_normalize_value($params);
	
		$attrs = array(
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
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
		$params = $this->_normalize_value($params);
	
		$attrs = array(
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
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
			$file .= $this->form->hidden($params['name'].'_overwrite', $overwrite);
		}
		if (isset($params['upload_path']))
		{
			$file .= $this->form->hidden($params['name'].'_path', $params['upload_path']);
		}
		if (isset($params['filename']))
		{
			$file .= $this->form->hidden($params['name'].'_filename', $params['filename']);
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
		$params = $this->_normalize_value($params);
	
		// check date to format it
		if ((!empty($params['value']) AND (int) $params['value'] != 0)
			&& (preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})#", $params['value'], $regs1) 
			|| preg_match("#([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})#", $params['value'], $regs2)))
		{
			$params['value'] = date($this->date_format, strtotime($params['value']));
		} else {
			$params['value'] = '';
		}
		
		$params['class'] = 'datepicker '.$params['class'];
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
		$params = $this->_normalize_value($params);

		if (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND $params['value'] != '0000-00-00 00:00:00')
		{
			$time_params['value'] = date('g', strtotime($params['value']));
		}
		$time_params['size'] = 2;
		$time_params['max_length'] = 2;
		$time_params['name'] = $params['orig_name'].'_hour';
		$time_params['class'] = 'fillin datepicker_hh';
		$time_params['disabled'] = $params['disabled'];
		$str = $this->create_text($this->_normalize_value($time_params));
		$str .= ":";
		if (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND $params['value'] != '0000-00-00 00:00:00') $time_params['value'] = date('i', strtotime($params['value']));
		$time_params['name'] = $params['orig_name'].'_min';
		$time_params['class'] = 'fillin datepicker_mm';
		$str .= $this->create_text($this->_normalize_value($time_params));
		$ampm_params['options'] = array('am' => 'am', 'pm' => 'pm');
		$ampm_params['name'] = $params['orig_name'].'_am_pm';
		$ampm_params['value'] = (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND date('H', strtotime($params['value'])) >= 12) ? 'pm' : 'am';
		$ampm_params['disabled'] = $params['disabled'];
		$str .= $this->create_enum($this->_normalize_value($ampm_params));
		return $str;
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
		$params = $this->_normalize_value($params);

		$str = '';
		$attrs = array(
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled']
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
	public function create_section($params){
		$params = $this->_normalize_value($params);

		$section = '';
		if (is_array($params) AND count($params) > 1)
		{
			if (!empty($params['value']))
			{
				$section = $params['value'];
			}
			else if (!empty($params['label']))
			{
				$section = $params['label'];
			}
			else if (!empty($params['name']))
			{
				$section = $params['name'];
			}
		}
		else
		{
			$section = $params;
		}
		return '<'.$this->section_tag.'>'.$section.'</'.$this->section_tag.'>';
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
	public function create_copy($params){
		$params = $this->_normalize_value($params);
	
		$copy = '';
		if (is_array($params))
		{
			if (!empty($params['value']))
			{
				$copy = $params['value'];
			}
			else if (!empty($params['label']))
			{
				$copy = $params['label'];
			}
			else if (!empty($params['name']))
			{
				$copy = $params['name'];
			}
		}
		else
		{
			$copy = $params;
		}
		return '<'.$this->copy_tag.'>'.$copy.'</'.$this->copy_tag.'>';
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
	public function create_tooltip($params){

		$params = $this->_normalize_value($params);

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
		$params = $this->_normalize_value($params);

		return call_user_func($func, $params);
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
			$this->_fields[$key] = $this->_normalize_value($val);
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
	 * Sorts the fields for the form
	 *
	 * Same as the MY_array_helper array_sorter function
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	protected function _fields_sorter($array, $index, $order = 'asc', $nat_sort = FALSE, $case_sensitive = FALSE)
	{
		if(is_array($array) AND count($array) > 0)
		{
			foreach (array_keys($array) as $key)
			{
				$temp[$key]=$array[$key][$index];
				if (! $nat_sort)
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
}

/* End of file Data_table.php */
/* Location: ./application/libraries/Form_builder.php */
