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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * A form creation class
 *
 * The Form_builder class allows you to create forms by passing in configurable 
 * array values. Each field has a base set of parameters that can be set 
 * for it. Other fields have additional parameters you can pass to it 
 * (e.g. the date field). This class works with the 
 * <a href="[user_guide_url]libraries/my_model#func_form_fields">MY_Model form_fields</a> 
 * method which returns table meta information regarding the fields of a 
 * table.
 *
 * The <a href="[user_guide_url]libraries/form">Form.php</a> class is required if a 
 * form object is not passed in the initialization process.
 * 
 * Custom form fields can be configured in the <span class="file">fuel/application/config/custom_fields.php</span> file.
 *
 * <p class="important">Additional information about <a href="[user_guide_url]general/forms">creating forms using Form_builder can be found in the General Topics area</a>.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/form_builder
 */

class Form_builder {

	public $form; // form object used to create the form fields and associate errors with
	public $id = ''; // id to be used for the containing table or div
	public $css_class = 'form'; // css class to be used with the form
	public $form_attrs = 'method="post" action=""'; // form tag attributes
	public $label_colons = FALSE; // add colons to form labels?
	public $textarea_rows = 10; // number of rows for a textarea
	public $textarea_cols = 60; // number of columns for a textarea
	public $text_size_limit = 40; // text size for a text input
	public $submit_name = '';   // submit id and name values
	public $submit_value = 'Submit'; // submit value  (what the button says)
	public $cancel_value = ''; // cancel value (what the button says)
	public $cancel_action = ''; // what the cancel button does
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
	public $js; // javascript files to associate with the form fields to be executed once per render
	public $css; // CSS files to associate with the form fields to be executed once per render
	public $no_css_js = FALSE; // used to not display the CSS and JS when rendering to prevent issues with nested forms and post_processing
	public $template = ''; // the html template view file to use for rendering the form when using "render_template"

	protected $_html; // html string
	protected $_fields; // fields to be used for the form
	protected $_cached; // cached parameters
	protected $_pre_process; // pre_process functions
	protected $_post_process; // post_process functions
	protected $_rendering = FALSE; // used to prevent infinite loops when calling form_builder reference from within a custom form field
	protected $_rendered_field_types = array(); // holds all the fields types rendered
	protected $_is_nested = FALSE; // used to detect nested fields
	protected $CI;
	
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
	public function __construct($params = array())
	{
		$this->CI =& get_instance();
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
			$this->load_custom_fields($this->custom_fields);
		}
		
		// create form object if not in initialization params
		if (is_null($this->form))
		{
			$this->CI->load->library('form');
			$this->CI->load->library('encrypt');
			$this->form = new Form();
			
			// load localization helper if not already
			if (!function_exists('lang'))
			{
				$this->CI->load->helper('language');
			}

			// CSRF protections
			if ($this->CI->config->item('csrf_protection') === TRUE AND empty($this->key_check))
			{
				$this->CI->security->csrf_set_cookie(); // need to set it again here just to be sure ... on initial page loads this may not be there
				$this->key_check = $this->CI->security->get_csrf_hash();
				$this->key_check_name = $this->CI->security->get_csrf_token_name();
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
	public function set_params($params)
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
		$this->js = array();
		$this->css = array();
		$this->_pre_process = array();
		$this->_post_process = array();
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

		// clear it out first
		$this->_fields = array();
		foreach ($fields as $key => $val)
		{
			$this->add_field($key, $val, $i);
			$i++;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set the fields for the form
	 * 
	 * Check the normalize_params method for possible values
	 *
	 * @access	public
	 * @param	string	The key to associate with the field
	 * @param	array	The field parameters
	 * @param	int		The order value of the parameter
	 * @return	void
	 */
	public function add_field($key, $val, $order = NULL)
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
				$this->_fields[$key] = array('name' => $key, 'value' => $val);
			}
			else
			{
				$this->_fields[$key] = $val;
			}
			
			// set the key value
			if (empty($this->_fields[$key]['key']))
			{
				$this->_fields[$key]['key'] = $key;	
			}
			
			if (empty($val['name']))
			{
				$this->_fields[$key]['name'] = $key;
			}
			
			// set the order of the field
			if (isset($order) AND empty($val['order']))
			{
				$this->_fields[$key]['order'] = $order;
			}
			if (empty($this->_fields[$key]['order']))
			{
				$this->_fields[$key]['order'] = count($this->_fields);
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Removes a field before rendering
	 * 
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function remove_field($key)
	{
		if (isset($this->_fields[$key]))
		{
			unset($this->_fields[$key]);
		}
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
		if (!is_array($values))
		{
			return FALSE;
		}
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
	 * @param	string	'a view path (only used for the template)
	 * @return	string
	 */
	public function render($fields = NULL, $render_format = NULL, $template = NULL)
	{
		if (empty($render_format)) $render_format = $this->render_format;
		if ($render_format == 'divs')
		{
			return $this->render_divs($fields);
		}
		else if ($render_format == 'template')
		{
			if (empty($template))
			{
				$template = $this->template;
			} 
			return $this->render_template($template, $fields);
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
				$str .= $func();
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
				$str .= $this->create_label($val, TRUE);
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
				$str .= $this->create_label($val, TRUE);
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

		$str .= $this->_render_actions();

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

		$str .= $this->_render_actions();


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
	 * Render the HTML output using a specified template.
	 * 
	 * Will provide an array of form fields that can be parsed like so {my_field}
	 * 
	 * @access	public
	 * @param	string the name of the template view file to use
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	string
	 */
	public function render_template($template, $fields = NULL, $parse = TRUE)
	{
		if (!empty($fields)) $this->set_fields($fields);

		// reoarder
		$this->set_field_order();
		
		// pre process field values
		$this->pre_process_field_values();
		
		$this->_html = $this->html_prepend;

		$errors = NULL;
		if ($this->display_errors)
		{
			$func = $this->display_errors_func;
			if (function_exists($func))
			{
				$errors = $func();
			}
		}
		
		$fields = array();
		foreach($this->_fields as $key => $field)
		{
			$fields[$key]['field'] = $this->create_field($field);
			$fields[$key]['label'] = $this->create_label($field);
		}
		
		$vars['fields'] = $fields;
		$vars['errors'] = $errors;

		if (is_array($template))
		{
			$module = key($template);
			$view = current($template);

			$str = $this->CI->load->module_view($module, $view, $vars, TRUE);
		}
		else
		{
			$str = $this->CI->load->view($template, $vars, TRUE);
		}
		
		if ($parse === TRUE)
		{
			$this->CI->load->library('parser');
			$str = $this->CI->parser->parse_simple($str, $vars);
		}

		$str .= '<div class=\"actions\">';

		$str .= $this->_render_actions();

		$str .= "</div>";

		$this->_html = $this->_close_form($str);
		
		return $this->_html;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the opening div element that contains the form fields
	 * 
	 * @access	public
	 * @return	string
	 */
	protected function _open_div()
	{
		$str = '';
		$str .= "<div>\n";
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the opening table element
	 * 
	 * @access	public
	 * @return	string
	 */
	protected function _open_table()
	{
		$str = '';
		$str .= "<table>";
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
	 * Outputs the actions for the form
	 * 
	 * @access	public
	 * @param	string	
	 * @return	void
	 */
	protected function _render_actions()
	{
		$str = '';
		if ( ! empty($this->reset_value))
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

		if ( ! empty($this->cancel_value))
		{
			if (preg_match("/^</i", $this->cancel_value))
			{
				$str .= $this->cancel_value;
			}
			else
			{
				$cancel_attrs = array('class' => 'cancel');

				if ( ! empty($this->cancel_action))
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
				$submit_name = (empty($this->submit_name)) ? $this->submit_value : $this->submit_name;
				$submit_name = (!empty($this->name_prefix) AND $this->names_id_match) ? $this->name_prefix.'--'.$submit_name : $submit_name;
				$submit_id = $submit_name;
				if (!empty($this->name_prefix))
				{
					$submit_id = $this->name_prefix.'--'.$submit_id;
				}
				$str .= $this->form->$submit_btn($this->submit_value, $submit_name, array('class' => 'submit', 'id' => $submit_id));
			}
		}
		if (!empty($this->other_actions)) $str .= $this->other_actions;

		if ( ! empty($this->other_actions)) $str .= $this->other_actions;
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
		
		// wrapper div to apply ID
		$wrapper_open_str = "<div class=\"".$this->css_class."\"";
		if (empty($this->id))
		{
			$this->id = $this->id();
		}
		$wrapper_open_str .= ' id="'.$this->id.'"';
		$wrapper_open_str .= ">\n";
		
		$wrapper_close_str = "</div>";
		
		// apply any CSS first
		$this->_html .= $this->_apply_css();
		$this->_html .= $wrapper_open_str.$str.$wrapper_close_str;
		
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
			'key' => '', // a unique identifier for the field. By default, it will be the the same as the ID. This parameter is used mostly for post processing of a field
			'id' => '', // the ID attribute of the field. This value will be auto generated if not provided. Set to FALSE if you don't want an ID value
			'name' => '', // the name attribute of the field
			'type' => '', // the type attribute of the field (e.g. text, select, password, etc.)
			'default' => '', // the default value of the field
			'max_length' => 0, // the maxlength parameter to associate with the field
			'comment' => '', // a comment to assicate with the field's label'
			'label' => '', // the label to associate with the field
			'required' => FALSE, // puts a required flag next to field label
			'size' => '', // the size attribute of the field
			'class' => '', // the CSS class attribute to associate with the field
			'style' => '', // inline style 
			'value' => '', // the value of the field
			'readonly' => '', // sets readonly attribute on field
			'disabled' => '', // sets disabled attribute on the field
			'tabindex' => '', // adds the tab index attribute to a field
			'label_colons' => NULL, // whether to display the label colons
			'display_label' => TRUE, // whether to display the label
			'order' => NULL, // the display order value to associate with the field
			'before_html' => '', // for HTML before the field
			'after_html' => '', // for HTML after the field
			'displayonly' => FALSE, // only displays the value (no field)
			'pre_process' => NULL, // a pre process function
			'post_process' => NULL, // a post process function run on post
			'js' => '', // js file or script using <script> tag
			'css' => '', // css to associate with the field
			'represents' => '', // specifies what other types of fields that this field should represent
			'ignore_representative' => FALSE, // ignores any representative
			'data' => array(), // data attributes
			'__DEFAULTS__' => TRUE // set so that we no that the array has been processed and we can check it so it won't process it again'
		);
		
		$params = array_merge($defaults, $val);
		
		if (empty($params['orig_name'])) $params['orig_name'] = $params['name']; // for labels in case the name_array is used
		if (empty($params['key']))
		{
			$params['key'] = Form::create_id($params['orig_name']);
		}
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
				if ($this->key_check_name != $params['orig_name'])
				{
					$params['name'] = $this->name_array.'['.$params['orig_name'].']';
				}
				else
				{
					$params['name'] = $params['orig_name'];
				}
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
				if ($this->key_check_name != $params['orig_name'])
				{
					$params['name'] = $this->name_prefix.'--'.$params['orig_name'];
				}
				else
				{
					$params['name'] = $params['orig_name'];				
				}
			}
			
			if (in_array($params['orig_name'], $this->hidden) AND !in_array($params['name'], $this->hidden)) $this->hidden[] = $params['name'];
		}

		// grab options from a model if a model is specified
		if (!empty($params['model']))
		{
			$model_params = (!empty($params['model_params'])) ? $params['model_params'] : array();
			$params['options'] = $this->options_from_model($params['model'], $model_params);
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
			if (!empty($params['hide_if_one']) AND count($params['options']) <= 1)
			{
				$params['type'] = 'hidden';
				$params['display_label'] = FALSE;
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
			$this->add_js($params['js']);
		}

		// take out css so we execute it only once per render
		if (!empty($params['css']))
		{
			$this->add_css($params['css']);
		}
		
		// says whether this field can represent other field types
		if (!empty($params['represents']))
		{
			$this->representatives[$params['type']] = $params['represents'];
		}
		
		// set the field type CSS class
		$type = (!empty($params['type'])) ? $params['type'] : 'text';
		$field_class = $this->class_type_prefix.$type;
		$params['class'] = (!empty($params['class']) AND strpos($params['class'], $field_class) === FALSE) ? $field_class.' '.$params['class'] : $params['class'];

		$this->_cached[$params['name']] = $params;
		return $params;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the id for a form
	 * 
	 * @access	public
	 * @return	string
	 */
	public function id()
	{
		if (empty($this->id))
		{
			$this->id = uniqid('form_');
		}
		return $this->id;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the default values for any field
	 * 
	 * @access	public
	 * @param	array fields values... will overwrite anything done with the set_fields method previously
	 * @return	array
	 */
	public function normalize_params($val, $defaults = array())
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
					$this->add_css($params['css'], $params['type']);
				}

				// same here... but we are looking for CSS on the object
				if (!empty($func->css))
				{
					$this->add_css($func->css, $params['type']);
				}

				// take out javascript so we execute it only once per render
				if (!empty($params['js']))
				{
					$this->add_js($params['js'], $params['type']);
				}

				// same here... but we are looking for js on the object
				if (!empty($func->js))
				{
					$this->add_js($func->js, $params['type']);
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
	 * IMPORTANT! You probably shouldn't call this method from within a custom field type because it may create an infinite loop!
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @param	boolean shoud the normalization be ran again?
	 * @return	string
	 */
	public function create_field($params, $normalize = TRUE)
	{
		// needed to prevent runaway loops from custom fields... actually the template field type wont work with this
		// if ($this->_rendering)
		// {
		// 	return FALSE;
		// }
		if ($normalize) $params = $this->normalize_params($params); // done again here in case you create a field without doing the render method

		// now we look at all the fields that may represent other field types based on parameters
		if (!empty($this->representatives) AND is_array($this->representatives) AND empty($params['ignore_representative']))
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
						$params['type'] = 'text';
						$str = $this->create_text($params);
					}
			}
		}

		// cache the field types being rendered
		$rendered_type = (!empty($matched)) ? $key : $params['type'];
		$this->_rendered_field_types[$rendered_type] = $rendered_type;
		// $this->_rendered_field_types[$params['type']] = $params['type'];
		
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
			if ($lang = $this->label_lang($params['orig_name']))
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
			if (!empty($this->name_prefix))
			{
				$id_name = $this->name_prefix.'--'.end(explode($this->name_prefix.'--', $params['name'])); // ugly... bug needed for nested repeatable fields
			}
			else
			{
				$id_name = $params['orig_name'];
			}
			
			$str .= "<label for=\"".Form::create_id($id_name)."\" id=\"label_".Form::create_id($id_name)."\">";
		}
		if ($this->tooltip_labels)
		{
			$str .= $this->create_tooltip($params);
		} 
		else
		{
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
			'autocomplete' => (!empty($params['autocomplete']) ? $params['autocomplete'] : NULL),
			'placeholder' => (!empty($params['placeholder']) ? $params['placeholder'] : NULL),
			'required' => (!empty($params['required']) ? TRUE : NULL),
			'data' => $params['data'],
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
		);
		
		if (isset($params['attrs']))
		{
			$attrs = array_merge($attrs, $params['attrs']);
		}
		return $this->form->input($params['name'], $params['type'], $params['value'], $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the password input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_password($params)
	{
		$params['type'] = 'password';
		return $this->create_text($params);
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
			'disabled_options' => array(),
		);
		
		$params = $this->normalize_params($params, $defaults);
		
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'required' => (!empty($params['required']) ? TRUE : NULL),
			'data' => $params['data'],
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
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
		
		return $this->form->select($name, $params['options'], $params['value'], $attrs, $params['first_option'], $params['disabled_options']);
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
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
		);
		if ($params['checked'])
		{
			$attrs['checked'] = 'checked';
		}
		if (isset($params['value']) AND $params['value'] == '')
		{
			$params['value'] = 1;
		}
		$str .= $this->form->checkbox($params['name'], $params['value'], $attrs);
		return $str;
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
			'rows' => (!empty($params['rows'])) ? $params['rows'] : $this->textarea_rows, 
			'cols' => (!empty($params['cols'])) ? $params['cols'] : $this->textarea_cols, 
			'readonly' => $params['readonly'], 
			'autocomplete' => (!empty($params['autocomplete']) ? $params['autocomplete'] : NULL),
			'placeholder' => (!empty($params['placeholder']) ? $params['placeholder'] : NULL),
			'required' => (!empty($params['required']) ? TRUE : NULL),
			'data' => $params['data'],
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
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
			'class' => $params['class'],
		);
		return $this->form->hidden($params['name'], $params['value'], $attrs);
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
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
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
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
		);
		$use_input_type = (isset($params['use_input']) AND $params['use_input'] === FALSE) ? FALSE : TRUE;
		return $this->form->button($params['value'], $params['name'], $attrs, $use_input_type);
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
			'checked'       => FALSE, // for radio
			'options'       => array(),
			'mode'          => NULL,
			'model'         => NULL,
			'wrapper_tag'   => 'span',// for checkboxes
			'wrapper_class' => 'multi_field',
			'spacer'        => "&nbsp;&nbsp;&nbsp;",
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
				$str .= '<'.$params['wrapper_tag'].' class="'.$params['wrapper_class'].'">';
				$attrs = array(
					'class' => $params['class'],
					'readonly' => $params['readonly'], 
					'disabled' => $params['disabled'],
					'style' => $params['style'],
					'tabindex' => ((is_array($params['tabindex']) AND isset($params['tabindex'][$i])) ? $params['tabindex'][$i] : NULL),
				);

				if (empty($params['null']) OR (!empty($params['null']) AND (!empty($params['default']) OR !empty($params['value']))))
				{
					if (($i == 0 AND !$default) OR  ($default == $key))
					{
						$attrs['checked'] = 'checked';
					}
				}
				$str .= $this->form->radio($params['name'], $key, $attrs);
				$name = Form::create_id($params['orig_name']);
				//$str .= ' <label for="'.$name.'_'.str_replace(' ', '_', $key).'">'.$val.'</label>';
				$enum_name = $name.'_'.Form::create_id($key);
				$label = ($lang = $this->label_lang($enum_name)) ? $lang : $val;
				if (!empty($this->name_prefix))
				{
					$enum_name = $this->name_prefix.'--'.$enum_name;
				}
				$enum_params = array('label' => $label, 'name' => $enum_name);
				
				$str .= ' '.$this->create_label($enum_params);
				$str .= $params['spacer'];
				$str .= '</'.$params['wrapper_tag'].'>';
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
	 * Creates the multi select input for the form (this is overwritten by the Fuel_custom_fields to give more functionaltity)
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_multi($params)
	{
		$defaults = array(
			'options'       => array(),
			'mode'          => NULL,
			'wrapper_tag'   => 'span',// for checkboxes
			'wrapper_class' => 'multi_field',
			'spacer'        => "&nbsp;&nbsp;&nbsp;",
		);

		$params = $this->normalize_params($params, $defaults);
		
		$str = '';
		$mode = (!empty($params['mode'])) ? $params['mode'] : $this->multi_select_mode;
		if ($mode == 'checkbox' OR ($mode == 'auto' AND (isset($params['options']) AND count($params['options']) <= 5)))
		{
			$value = (isset($params['value'])) ? (array)$params['value'] : array();

			$params['name'] = $params['name'].'[]';
			$i = 1;
			
			if (!empty($params['options']))
			{
				foreach($params['options'] as $key => $val)
				{
					$tabindex_id = $i -1;
					$str .= '<'.$params['wrapper_tag'].' class="'.$params['wrapper_class'].'">';
					$attrs = array(
						'readonly' => $params['readonly'], 
						'disabled' => $params['disabled'],
						'id' => Form::create_id($params['name']).$i,
						'style' => '', // to overwrite any input width styles
						'tabindex' => ((is_array($params['tabindex']) AND isset($params['tabindex'][$i - 1])) ? $params['tabindex'][$i - 1] : NULL),
					);

					if (in_array($key, $value))
					{
						$attrs['checked'] = 'checked';

					}
					$str .= $this->form->checkbox($params['name'], $key, $attrs);

					$label = ($lang = $this->label_lang($attrs['id'])) ? $lang : $val;
					$enum_params = array('label' => $label, 'name' => $attrs['id']);
					$str .= ' '.$this->create_label($enum_params);
					$str .= $params['spacer'];
					$str .= '</'.$params['wrapper_tag'].'>';
					$i++;
				}
			}
		}
		else
		{
			$params['multiple'] = TRUE;
			$str .= $this->create_select($params);
		}
		
		return $str;
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
			'overwrite' => NULL, // sets a paramter to either overwrite or create a new file if one already exists on the server
			'display_overwrite' => TRUE, // displays the overwrite checkbox
			'accept' => 'gif|jpg|jpeg|png|pdf', // specifies which files are acceptable to upload
			'upload_path' => NULL, // the server path to upload the file to
			'folder' => NULL, // the folder name relative to the assets folder to upload the file
			'file_name' => NULL, // for file uploading
			'encrypt_name' => NULL, // determines whether to encrypt the uploaded file name to give it a unique value
		);

		$params = $this->normalize_params($params, $defaults);
	
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'required' => (!empty($params['required']) ? TRUE : NULL),
			'accept' => str_replace('|', ',', $params['accept']),
			'tabindex' => $params['tabindex'],
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
				$file .= ' '. $this->create_label($this->label_lang('overwrite')).'</span>';
			}
			else
			{
				$file .= $this->form->hidden($params['name'].'_overwrite', $overwrite);
			}
		}
		if (isset($params['upload_path']) OR isset($params['folder']))
		{
			if (isset($params['folder']))
			{
				$upload_path = $this->CI->encrypt->encode(assets_server_path($params['folder']));
			}
			else
			{
				$upload_path = $this->CI->encrypt->encode($params['upload_path']);
			}
			$file .= $this->form->hidden($params['name'].'_upload_path', $upload_path);
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
	 * Adds the datepicker so that you can use jquery to 
	 * add the datepicker jquery plugin
	 * 
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_date($params)
	{
		if (empty($params['date_format']))
		{
			if (empty($params['date_format']))
			{
				$params['date_format'] = $this->CI->config->item('date_format');
			}
			else
			{
				$params['date_format'] = $this->date_format;
			}
		}
		
		$defaults = array(
			'date_format' => '', 
			'region' => '', 
			'min_date' => date($params['date_format'], strtotime('01/01/2000')),
			'max_date' =>  date($params['date_format'], strtotime('12/31/2030')),
			'first_day' => 0, 
		);

		$params = $this->normalize_params($params, $defaults);
		
		
		// check date to format it
		if ((!empty($params['value']) AND (int) $params['value'] != 0)
			&& (preg_match("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})#", $params['value'], $regs1) 
			|| preg_match("#([0-9]{1,2})[/\-]([0-9]{1,2})[/\-]([0-9]{4})#", $params['value'], $regs2)))
		{
			$params['value'] = date($params['date_format'], strtotime($params['value']));
		} else {
			$params['value'] = '';
		}
		$params['maxlength'] = 10;
		$params['size'] = 11; // extra room for cramped styling
		
		// create the right format for placeholder display based on the date format
		$date_arr = preg_split('#-|/#', $params['date_format']);
		$format = '';
		
		// order counts here!
		$format = str_replace('m', 'mm', $params['date_format']);
		$format = str_replace('n', 'm', $format);
		$format = str_replace('d', 'dd', $format);
		$format = str_replace('j', 'd', $format);
		$format = str_replace('y', 'yy', $format);
		$format = str_replace('Y', 'yyyy', $format);
		
		// set data parameters to be used by javascript
		$params['data'] = array();
		if (empty($params['region']))
		{
			$params['data']['date_format'] = str_replace('yyyy', 'yy', $format);
		}
		else
		{
			$params['data']['region'] = $params['region'];
		}
		$params['data']['min_date'] = $params['min_date'];
		$params['data']['max_date'] = $params['max_date'];
		$params['data']['first_day'] = $params['first_day'];
		$params['placeholder'] = $format;
		$params['type'] = 'text';
		return $this->create_text($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the time input for the form
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_time($params)
	{
		$params = $this->normalize_params($params);

		if (!isset($params['ampm']))
		{
			$params['ampm'] = TRUE;
		}

		if (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND $params['value'] != '0000-00-00 00:00:00')
		{
			$hour_format = ($params['ampm']) ? 'g' : 'G';
			$time_params['value'] = date($hour_format, strtotime($params['value']));
		}
		$time_params['size'] = 2;
		$time_params['maxlength'] = 2;
		$field_name = (empty($params['is_datetime'])) ? $params['key'] : $params['key'].'_hour';
		$time_params['name'] = str_replace($params['key'], $field_name, $params['orig_name']);
		$time_params['class'] = 'datepicker_hh';
		$time_params['disabled'] = $params['disabled'];
		$time_params['placeholder'] = 'hh';
		if (isset($params['tabindex'][0]))
		{
			$time_params['tabindex'] = $params['tabindex'][0];
		}
		$str = $this->create_text($this->normalize_params($time_params));
		$str .= ":";
		if (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND $params['value'] != '0000-00-00 00:00:00') $time_params['value'] = date('i', strtotime($params['value']));
		$time_params['name'] = str_replace($params['key'], $params['key'].'_min', $params['orig_name']);
		$time_params['class'] = 'datepicker_mm';
		$time_params['placeholder'] = 'mm';

		if (isset($params['tabindex'][1]))
		{
			$time_params['tabindex'] = $params['tabindex'][1];
		}
		$str .= $this->create_text($this->normalize_params($time_params));

		if (!empty($params['ampm']))
		{
			$ampm_params['options'] = array('am' => 'am', 'pm' => 'pm');
			$ampm_params['name'] = str_replace($params['key'], $params['key'].'_am_pm', $params['orig_name']);
			$ampm_params['value'] = (!empty($params['value']) AND is_numeric(substr($params['value'], 0, 1)) AND date('H', strtotime($params['value'])) >= 12) ? 'pm' : 'am';
			$ampm_params['disabled'] = $params['disabled'];

			if (isset($params['tabindex']) AND is_array($params['tabindex']))
			{
				array_shift($params['tabindex']);
				array_shift($params['tabindex']);
				$ampm_params['tabindex'] = $params['tabindex'];
			}
			$str .= $this->create_enum($this->normalize_params($ampm_params));
		}

		$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];

		// create post processer to recreate date value
		$func_str = '
			if (is_array($value))
			{
				foreach($value as $key => $val)
				{
					$hr   = (isset($val["'.$process_key.'"]) AND (int)$val["'.$process_key.'"] > 0 AND (int)$val["'.$process_key.'"] < 24) ? $val["'.$process_key.'"] : "";
					$min  = (isset($val["'.$process_key.'_min"]) AND is_numeric($val["'.$process_key.'_min"]))  ? $val["'.$process_key.'_min"] : "00";
					$ampm = (isset($val["'.$process_key.'_am_pm"]) AND $hr AND $min) ? $val["'.$process_key.'_am_pm"] : "";
					if (!empty($ampm) AND !empty($hr) AND $hr > 12)
					{
						if ($hr > 24) 
						{
							$hr = "00";
						}
						else
						{
							$hr = (int) $hr - 12;
							$ampm = "pm";
						}
					}
					if ($hr !== "")
					{
						$dateval = $hr.":".$min.$ampm;
						$value[$key]["'.$process_key.'"] = date("H:i:s", strtotime($dateval));
					}
				}
				return $value;
			}
			else
			{
				$hr    = (isset($_POST["'.$process_key.'"]) AND (int)$_POST["'.$process_key.'"] > 0 AND (int)$_POST["'.$process_key.'"] < 24) ? $_POST["'.$process_key.'"] : "";
				$min   = (isset($_POST["'.$process_key.'_min"]) AND is_numeric($_POST["'.$process_key.'_min"]))  ? $_POST["'.$process_key.'_min"] : "00";
				$ampm  = (isset($_POST["'.$process_key.'_am_pm"]) AND $hr AND $min) ? $_POST["'.$process_key.'_am_pm"] : "";
				if (!empty($ampm) AND !empty($hr) AND $hr > 12)
				{
					if ($hr > 24) 
					{
						$hr = "00";
					}
					else
					{
						$hr = (int) $hr - 12;
						$ampm = "pm";
					}
				}

				$dateval = "";
				if ($hr !== "")
				{
					$dateval = $hr.":".$min.$ampm;
					$dateval = date("H:i:s", strtotime($dateval));
				}

				return $dateval;
			}
		';

		// needed for post processing
		if (!isset($_POST[$params['key']]))
		{
			$_POST[$time_params['name']] = '';
		}

		if (empty($params['is_datetime']))
		{
			$func = create_function('$value', $func_str);
			$this->set_post_process($params['key'], $func);
		}
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
		$date_params = $params;
		if (isset($params['tabindex']) AND is_array($params['tabindex']))
		{
			$date_params['tabindex'] = current($params['tabindex']);
		}

		$str = $this->create_date($date_params);
		$str .= ' ';
		$params['is_datetime'] = TRUE;
		if (!isset($params['ampm']))
		{
			$params['ampm'] = TRUE;
		}
		$time_params = $params;
		if (isset($params['tabindex']) AND is_array($params['tabindex']))
		{
			array_shift($time_params['tabindex']);
		}
		$str .= $this->create_time($time_params);

		$process_key = (isset($params['subkey'])) ? $params['subkey'] : $params['key'];
		$func_str = '
				
				if (is_array($value))
				{
					foreach($value as $key => $val)
					{

						if (isset($val["'.$process_key.'"]))
						{
							$date = (!empty($val["'.$process_key.'"]) AND is_date_format($val["'.$process_key.'"])) ? current(explode(" ", $val["'.$process_key.'"])) : "";
							$hr   = (!empty($val["'.$process_key.'_hour"]) AND  (int)$val["'.$process_key.'_hour"] > 0 AND (int)$val["'.$process_key.'_hour"] < 24) ? $val["'.$process_key.'_hour"] : "";
							$min  = (!empty($val["'.$process_key.'_min"]) AND is_numeric($val["'.$process_key.'_min"]))  ? $val["'.$process_key.'_min"] : "00";
							$ampm = (isset($val["'.$process_key.'_am_pm"]) AND $hr AND $min) ? $val["'.$process_key.'_am_pm"] : "";

							if (!empty($ampm) AND !empty($hr) AND $hr > 12)
							{
								if ($hr > 24) 
								{
									$hr = "00";
								}
								else
								{
									$hr = (int) $hr - 12;
									$ampm = "pm";
								}
							}

							$dateval = $value[$key]["'.$process_key.'"];
							if ($date != "")
							{
								if (!empty($hr)) $dateval .= " ".$hr.":".$min.$ampm;
							}
							if (!empty($dateval))
							{
								$value[$key]["'.$process_key.'"] = $dateval;	
							}
						}
					}
					return $value;
				}
				else
				{
					$date  = (!empty($_POST["'.$process_key.'"]) AND is_date_format($_POST["'.$process_key.'"])) ? current(explode(" ", $_POST["'.$process_key.'"])) : "";
					$hr    = (!empty($_POST["'.$process_key.'_hour"]) AND (int)$_POST["'.$process_key.'_hour"] > 0 AND (int)$_POST["'.$process_key.'_hour"] < 24) ? $_POST["'.$process_key.'_hour"] : "";
					$min   = (!empty($_POST["'.$process_key.'_min"]) AND is_numeric($_POST["'.$process_key.'_min"]))  ? $_POST["'.$process_key.'_min"] : "00";
					$ampm  = (isset($_POST["'.$process_key.'_am_pm"]) AND $hr AND $min) ? $_POST["'.$process_key.'_am_pm"] : "";
					
					if (!empty($ampm) AND !empty($hr) AND $hr > 12)
					{
						if ($hr > 24) 
						{
							$hr = "00";
						}
						else
						{
							$hr = (int) $hr - 12;
							$ampm = "pm";
						}
					}

					$dateval = $value;
					
					if ($date != "")
					{
						$dateval = $date;
						if (!empty($hr)) $dateval .= " ".$hr.":".$min.$ampm;
						if (!empty($dateval))
						{
							$dateval = date("Y-m-d H:i:s", strtotime($dateval));
						}
					}
					return $dateval;
				}
			';

		$func = create_function('$value', $func_str);
		$this->set_post_process($params['key'], $func);
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
		$defaults = array(
			'min' => '0', // sets the minimum number that can be entered
			'max' => NULL, // sets the maximum number that can be entered
			'step' => NULL, // specifies the increment that gets applied when pressing the up/down increment arrows
			'decimal' => 0, // determines whether to allow for decimal numbers
			'negative' => 0, // determines whether to allow for negative numbers
		);

		$params = $this->normalize_params($params, $defaults);
	
		$attrs = array(
			'id' => $params['id'],
			'class' => $params['class'], 
			'readonly' => $params['readonly'], 
			'disabled' => $params['disabled'],
			'required' => (!empty($params['required']) ? TRUE : NULL),
			'min' => (isset($params['min']) ? $params['min'] : '0'),
			'max' => (isset($params['max']) ? $params['max'] : NULL),
			'step' => (isset($params['step']) ? $params['step'] : NULL),
			'data' => $params['data'],
			'style' => $params['style'],
			'tabindex' => $params['tabindex'],
		);

		$numeric_class = 'numeric';
		$attrs['class'] = (!empty($params['class'])) ? $params['class'].' '.$numeric_class : $numeric_class;
		$params['type'] = 'number';
		$decimal = (!empty($params['decimal'])) ? (int) $params['decimal'] : 0;
		$negative = (!empty($params['negative'])) ? 1 : 0;
		
		if (empty($params['size']))
		{
			$attrs['size'] = 10;
		}

		if (empty($params['maxlength']))
		{
			$attrs['maxlength'] = 10;
		}

		// set data values for jquery plugin to use
		$attrs['data'] = array(
			'decimal' => $decimal,
			'negative' => $negative,
			);
		return $this->form->input($params['name'], $params['type'], $params['value'], $attrs);
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
	 * Creates a range field for the form... supported by modern browsers
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function create_range($params)
	{
		$email_class = 'range';
		$params['type'] = 'range';
		$params['attrs'] = array();
		$params['attrs']['min'] = (isset($params['min'])) ? $params['min'] : 0;
		$params['attrs']['max'] = (isset($params['max'])) ? $params['max'] : 10;
		$params['class'] = (!empty($params['class'])) ? $params['class'].' '.$email_class : $email_class;
		return $this->create_text($params);
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
		$mode = (!empty($params['mode'])) ? $params['mode'] : $this->boolean_mode;
		
		if ($mode == 'checkbox')
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
		$section = $this->simple_field_value($params);
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
		$copy = $this->simple_field_value($params);
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
			$tooltip = htmlentities($tooltip, ENT_QUOTES, config_item('charset'));
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
		$legend = $this->simple_field_value($params);
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
	public function create_nested($params, $return_object = FALSE)
	{
		$this->CI =& get_instance();
		$this->CI->load->library('parser');
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
		$form_builder->cancel_value = '';
		$form_builder->reset_value = '';
		$form_builder->other_actions = '';
		
		$form_builder->name_prefix = $this->name_prefix;
		$form_builder->name_array = $this->name_array;

		$form_builder->custom_fields = $this->custom_fields;
		$form_builder->representatives = $this->representatives;
		$form_builder->set_validator($this->form->validator);
		$form_builder->use_form_tag = FALSE;
		$form_builder->set_field_values($params['value']);
		$form_builder->no_css_js = TRUE; // used to prevent multiple loading of assets
		$form_builder->_is_nested = TRUE; // used to detect if it is a nested form
		$form_builder->auto_execute_js = FALSE;

		// add accumulated js
		$js = $form_builder->get_js();
		$this->add_js($js);

		// add accumulated css
		$css = $form_builder->get_css();
		$this->add_css($css);

		if ($return_object)
		{
			return $form_builder;
		}
		$form = $form_builder->render();

		return $form;
		
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
			$this->add_js($params['js'], $params['type']);
		}
		
		// render
		return call_user_func($func, $params);
	}

	// --------------------------------------------------------------------

	/**
	 * Adds multiple custom fields
	 *
	 * @access	public
	 * @param	array Array of custom fields to load
	 * @return	void
	 */
	public function load_custom_fields($file)
	{
		if (is_string($file))
		{
			if (file_exists($file))
			{
				include($file);
			}
			else
			{
				return FALSE;
			}
		}
		else if (is_array($file))
		{
			$fields = $file;
		}

		if (is_array($fields))
		{
			// setup custom fields
			foreach($fields as $type => $custom)
			{
				$this->register_custom_field($type, $custom);
			}
		}
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
			$this->CI =& get_instance();
			
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

			// if there's a filepath, then load it and instantiate the class
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
					$this->CI->load->module_library($module, $library);
				}
				else
				{
					$library = strtolower($custom_field['class']);
					$this->CI->load->library($library);
				}
				$library = end(explode('/', $library));
				$func = array($this->CI->$library, $custom_field['function']);
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
						$this->CI->load->module_library($module, $helper, $custom_field);
					}
					else 
					{
						$this->CI->load->helper($custom_field['function'], $custom_field);
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
	 * Handles validation for the form builder fields
	 *
	 * If valid it will return TRUE. If not, it will return an array of errors
	 * 
	 * @access	public
	 * @param	object the validator object to use for validating (optional)
	 * @return	mixed
	 */
	public function validate($validator = NULL)
	{
		if (empty($validator))
		{
			$validator = $this->form->validator;
		}
		if ( ! empty($_POST) AND (get_class($validator) == 'Validator'))
		{

			// $this->CI->load->library('validator');
			$this->CI->load->helper('inflector');

			$validator->reset();

			foreach ($this->_fields as $field_name => $params)
			{
				$field_validations = array();
				if (array_key_exists('validation', $params))
				{
					foreach ($params['validation'] as $rule => $args) {
						$field_validations[$rule] = $args;
					}
				}
				// add to validation only if it is in the $_POST
				if (array_key_exists('validation_if_exists', $params))
				{
					if ($this->CI->input->post($field_name))
					{
						foreach ($params['validation_if_exists'] as $rule => $args) {
							$field_validations[$rule] = $args;
						}
					}
				}
				// add required validation if it is set outside of the validation
				if (array_key_exists('required', $params) AND $params['required'] 
					AND ! array_key_exists('required', $field_validations)) {
					$field_validations['required'] = '';
				}

				// add the rules
				$field_value = $params['value'];
				foreach ($field_validations as $rule => $args)
				{
					$field_label = humanize($field_name);

					if (is_array($args) AND array_key_exists('message', $args))
					{
						$msg = $args['message'];
						unset($args['message']);
					}
					else
					{
						$msg = "{$field_label} is {$rule}.";
					}

					$rule_params = array();
					if ( ! empty($args) AND array_key_exists('params', $args) AND is_array($args['params'])) {
						$rule_params = $args['params'];
					}
					$validation_val = ( ! empty($args['validation_val'])) ? $args['validation_val'] : $field_value;
					array_unshift($rule_params, $validation_val);

					$validator->add_rule($field_name, $rule, $msg, $rule_params);
				}
			}

			$validator->validate();
			$errors = $validator->get_errors();

			if (empty($errors))
			{
				return TRUE;
			}
			else
			{
				return $validator->get_errors();	
			}
		}
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
	 * Registers a pre process function for a field
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	public function set_pre_process($field, $func)
	{
		$this->_pre_process[$field][] = $func;
	}

	// --------------------------------------------------------------------

	/**
	 * Registers a post process function for a field
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	public function set_post_process($field, $func)
	{
		$this->_post_process[$field][] = $func;
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
		// combine field pre processes with those already set
		foreach($this->_fields as $key => $field)
		{
			if (!empty($field['pre_process']))
			{
				$this->set_pre_process($key, $field['pre_process']);
			}
		}
		
		if (is_array($this->_pre_process))
		{
			foreach($this->_pre_process as $key => $functions)
			{
				foreach($functions as $function)
				{
					$process = $this->_normalize_process_func($function, $this->_fields[$key]['value']);
					$func = $process['func'];
					$params = $process['params'];
					$this->_fields[$key]['value'] = call_user_func_array($func, $params);
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
 		$this->no_css_js = TRUE; // set no display so that it won't load the JS and CSS

 		// yes... we render the form which is strange, but it executes all the custom field types which may contain post_processing rules
		$form = $this->render();

		$this->no_css_js = FALSE; // then set it back to preven any issues with further use

		if (empty($posted)) $posted = $_POST;
		
		// combine field post processes with those already set
		foreach($this->_fields as $key => $field)
		{
			if (!empty($field['post_process']) AND isset($posted[$key]))
			{
				$this->set_post_process($key, $field['post_process']);
			}
		}
		
		if (is_array($this->_post_process))
		{
			foreach($this->_post_process as $key => $functions)
			{
				foreach($functions as $function)
				{

					if (isset($this->_fields[$key]))
					{
						if (isset($posted[$key]))
						{
							$process = $this->_normalize_process_func($function, $posted[$key]);
							$func = $process['func'];
							$params = $process['params'];
							$posted[$key] = call_user_func_array($func, $params);
							if ($set_post)
							{
								$_POST[$key] = $posted[$key];
							}
						}
					}
				}
			}
		}

		return $posted;
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
	 * Removes a representative for a field type.
	 * 
	 * This removes a representative globally for all fields
	 *
	 * @access	public
	 * @param	string The field type to be the representative
	 * @return	void
	 */
	public function remove_representative($type)
	{
		unset($this->representatives[$type]);
	}

	// --------------------------------------------------------------------

	/**
	 * Adds a js file to be rendered with the form a representative for a field type.
	 * 
	 * @access	public
	 * @param	string A js file name
	 * @param	mixed A key value to associate with the JS file (so it only gets loaded once). Or an associative array of keyed javascript file names
	 * @return	void
	 */
	public function add_js($js = NULL, $key = NULL)
	{
		if (is_null($this->js))
		{
			$this->js = array();
		}

		if (is_array($key))
		{
			foreach($key as $k => $j)
			{
				if (!in_array($j, $this->js))
				{
					$this->js[$k] = $j;
				}
			}
		}
		else
		{
			if (empty($key))
			{
				if (is_array($js))
				{
					$this->js = array_merge($this->js, $js);
				}
				else if (!in_array($js, $this->js))
				{
					$this->js[] = $js;
				}
			}
			else if (!in_array($js, $this->js))
			{
				$this->js[$key] = $js;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Removes a javascript file
	 * 
	 * @access	public
	 * @param	string A js file name or an array of file names
	 * @return	void
	 */
	public function remove_js($key = NULL)
	{
		if (is_null($key))
		{
			$this->js = array();
		}
		else if (is_array($key))
		{
			foreach($key as $k)
			{
				if (in_array($k, $this->js))
				{
					unset($this->js[$k]);
				}
			}
		}
		else if (isset($this->js[$k]))
		{
			unset($this->js[$k]);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the javascript used for the form
	 *
	 * @access	public
	 * @param	string The key name of a javascript file used when adding
	 * @return	array
	 */
	public function get_js($js = NULL)
	{
		if (!empty($js))
		{
			return $this->js[$js];
		}
		return $this->js;
	}

	// --------------------------------------------------------------------

	/**
	 * Adds a CSS file to be rendered with the form a representative for a field type.
	 * 
	 * @access	public
	 * @param	string A CSS file name
	 * @param	mixed A key value to associate with the CSS file (so it only gets loaded once). Or an associative array of keyed javascript file names
	 * @return	void
	 */
	public function add_css($css = NULL, $key = NULL)
	{
		if (is_null($this->css))
		{
			$this->css = array();
		}
		
		if (is_array($key))
		{
			foreach($key as $k => $c)
			{
				if (!in_array($c, $this->css))
				{
					$this->css[$k] = $c;
				}
			}
		}
		else
		{
			if (empty($key))
			{
				if (is_array($css))
				{
					$this->css = array_merge($this->css, $css);
				}
				else if (!in_array($css, $this->css))
				{
					$this->css[] = $css;
				}
				
			}
			else if (!in_array($css, $this->css))
			{
				$this->css[$key] = $css;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the CSS used for the form
	 *
	 * @access	public
 	 * @param	string The key name of a CSS file used when adding
	 * @return	array
	 */
	public function get_css($css = NULL)
	{
		if (!empty($css))
		{
			return $this->css[$css];
		}
		return $this->css;
	}


	// --------------------------------------------------------------------

	/**
	 * Removes a css file
	 * 
	 * @access	public
	 * @param	string A css file name or an array of file names
	 * @return	void
	 */
	public function remove_css($key = NULL)
	{
		if (is_null($key))
		{
			$this->css = array();
		}
		else if (is_array($key))
		{
			foreach($key as $k)
			{
				if (in_array($k, $this->css))
				{
					unset($this->css[$k]);
				}
			}
		}
		else if (isset($this->css[$k]))
		{
			unset($this->css[$k]);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array of options if a model is specified
	 *
	 * @access	public
	 * @param	mixed model, model/method or module/model/method
	 * @return	array
	 */
	public function options_from_model($model, $params = array())
	{
		if (is_array($model))
		{
			$val = current($model);
			$module = key($model);
			// if an array is specified for the value, then we assume the key is the model name and the value is the method
			if (is_array($val))
			{
				$model = key($val);
				$method = current($val);
			}
			else
			{
				$model = $val;
			}
		}
		
		if (!isset($method))
		{
			$method = 'options_list'; // default method'
		}
		
		if (substr($model, strlen($model) - 6) != '_model')
		{
			$model = $model.'_model';
		}

		// if the key is a string, then we assume its the modules name and we load it form the module
		if (isset($module))
		{
			$this->CI->load->module_model($module, $model);
		}
		// if an indexed array is specified, we assume that it is simply a model from the application folder and we will look for an options_list function
		else
		{
			$this->CI->load->model($model);
		}
		$func = array($this->CI->$model, $method);
		$options = call_user_func_array($func, $params);
		return $options;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Looks for value, label, then name as the values
	 *
	 * @access	public
	 * @param	array fields parameters
	 * @return	string
	 */
	public function simple_field_value($params)
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
	 * Helper method that returns the language key value if it exist, otherwise it returns FALSE
	 * 
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	public function label_lang($key)
	{
		if (isset($this->lang_prefix) AND function_exists('lang') AND $lang = lang($this->lang_prefix.$key))
		{
			return $lang;
		}
		return FALSE;
	}
	

	// --------------------------------------------------------------------

	/**
	 * Returns a boolean value as to whether the rendered form_builder instance is nested or not
	 * 
	 * @access	protected
	 * @return	boolean
	 */
	public function is_nested()
	{
		return $this->_is_nested;
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
	 * Renders the javascript for fields (only once)
	 * 
	 * @access	protected
	 * @return	string
	 */
	protected function _render_js()
	{
		if ($this->no_css_js) return '';

		$_js = $this->get_js();
		if (empty($_js)) return '';
		
		$str = '';
		$str_inline = '';
		$str_files = '';
		$js_exec = array();
		$script_regex = '#^<script(.+)src=#U';
		
		$orig_ignore = $this->CI->asset->ignore_if_loaded;
		$this->CI->asset->ignore_if_loaded = TRUE;
		
		$orig_asset_output = $this->CI->asset->assets_output;
		$this->CI->asset->assets_output = FALSE;

		// loop through to generate javascript
		foreach($_js as $type => $js)
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
			// if a string with a slash in it, then we will assume it's just a single file to load'
			else if (strpos($js, '/') !== FALSE AND strpos($js, '<script') === FALSE)
			{
				$str_files .= js($js);
			}
			// if it starts with a script tag and does NOT have a src attribute
			else if (preg_match($script_regex, $js))
			{
				$str_files .= $js;
			}
		
			// if it starts with a script tag and DOES have a src attribute
			else if (strpos($js, '<script') !== FALSE)
			{
				$str_inline .= $js;
			}
		
			// else it will simply call a function if it exists
			else
			{
				$str .= "if (".$js." != undefined){\n";
				$str .= "\t".$js."();\n";
				$str .= "}\n";
			}
		}

		// loop through custom fields to generate any js function calls
		foreach($this->_rendered_field_types as $type => $cs_field)
		{
			if (isset($this->custom_fields[$type]))
			{
				$cs_field = $this->custom_fields[$type];

				// check if the field type has a js function to call 
				if (!empty($cs_field->js_function))
				{
					$js_options = (!empty($cs_field->js_params)) ? $cs_field->js_params : NULL;
					$js_exec_order = (!empty($cs_field->js_exec_order)) ? $cs_field->js_exec_order : 0;
					$js_exec[$type] = array('func' => $cs_field->js_function, 'options' => $js_options, 'order' => $js_exec_order);
				}
			}
		}
		
		
		// change ignore value on asset back to original
		$this->CI->asset->ignore_if_loaded = $orig_ignore;
		$this->CI->asset->assets_output = $orig_asset_output;
		
		// sort the javascript
		$js_exec = $this->_fields_sorter($js_exec, 'order');
		
		$out = $str_files;
		$out .= $str_inline;
		$out .= "<script type=\"text/javascript\">\n";
		$out .= "//<![CDATA[\n";
		$out .= "";
		$out .= $str."\n";
		$out .= 'if (jQuery){ jQuery(function(){';
		$out .= 'if (jQuery.fn.formBuilder) {';
		$out .= 'if (typeof(window[\'formBuilderFuncs\']) == "undefined") { window[\'formBuilderFuncs\'] = {}; };';
		$out .= 'window[\'formBuilderFuncs\'] = jQuery.extend(window[\'formBuilderFuncs\'], '.json_encode($js_exec).');';
		$out .= 'jQuery("#'.$this->id.'").formBuilder(window[\'formBuilderFuncs\']);';
		if ($this->auto_execute_js) $out .= 'jQuery("#'.$this->id.'").formBuilder().initialize();';
		$out .= '}';
		$out .= '})}';
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
		if (empty($this->css) OR $this->no_css_js) return;
		
		// static way but won't work if the form is ajaxed int'
		// $css = $this->CI->load->get_vars('css');
		// foreach($this->css as $c)
		// {
		// 	$css[] = $c;
		// }
		//$this->CI->load->vars(array('css' => $css));
		
		// set as global variable to help with nested forms
		if (empty($GLOBALS['__css_files__']))
		{
			$GLOBALS['__css_files__'] = array();
		}
		$add_css = array();
		$file = '';
		$out = '';
		foreach($this->css as $css)
		{
			if (is_string($css))
			{
				$css = preg_split('#\s*,\s*#', $css);
			}

			foreach($css as $k => $c)
			{
				$module = (is_string($k)) ? $k : NULL;

				if (is_array($c))
				{
					foreach($c as $file)
					{
						$f = css_path($file, $module);
						if (!empty($f) AND !in_array($f, $GLOBALS['__css_files__']))
						{
							array_push($GLOBALS['__css_files__'], $f);
							$add_css[] = $f;
						}
					}
				}
				else
				{
					$file = css_path($c, $module);
					if (!empty($file) AND !in_array($file, $GLOBALS['__css_files__']))
					{
						array_push($GLOBALS['__css_files__'], $file);
						$add_css[] = $file;
					}
				}
			}
		}

		// must use javascript to do this because forms may get ajaxed in and we need to inject their CSS into the head
		if (!empty($add_css))
		{
			$out .= "<script type=\"text/javascript\">\n";
			$out .= "//<![CDATA[\n";
			$out .= 'if (jQuery){ (function($) {
					var cssFiles = '.json_encode($add_css).';
					var css = [];
					$("head link").each(function(i){
						css.push($(this).attr("href"));
					});
					for(var n in cssFiles){
						if ($.inArray(cssFiles[n], css) == -1){
							// for IE 8
							if (document.createStyleSheet){
								var stylesheet = document.createStyleSheet(cssFiles[n])
							}
							else
							{
								var stylesheet = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" + cssFiles[n] + "\" />";
							}
							jQuery("head").append(stylesheet);
						}
					}
				
			})(jQuery)}';
			$out .= "\n//]]>\n";
			$out .= "</script>\n";
		}

		return $out;
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
 * @link		http://docs.getfuelcms.com/libraries/form_builder.html
 * @autodoc		FALSE
 */

class Form_builder_field {
	
	public $type = ''; // the type value of the field (e.g. textare, enum, datetime)
	public $render_func = array();
	public $html = ''; // html output for form field
	public $js = array(); // the name of javascript file(s) to load
	public $js_class = ''; // the CSS class used by the javascript to execute any javascript on the field
	public $js_params = array(); // parameters to pass to the javascript function
	public $js_exec_order = 1; // the order in which the javascript should be executed in relation to other fields... lower the sooner
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
/* Location: ./modules/fuel/libraries/Form_builder.php */
