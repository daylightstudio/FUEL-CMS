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
 * A form class to render different form inputs
 *
 * An alternative to the form_helper. This integrates with the FUEL Validator
 * Object nicely to display errors with form elements
 *
 * The Form.php class is required if a form object is not passed in the 
 * initialization process.
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/form.html
 */
Class Form {
	
	public $attrs = 'method="post" action=""'; // form html attributes
	public $validator; // the validator object
	public $focus_highlight_cssclass = "field_highlight"; // the focus css class
	public $error_highlight_cssclass = "error_highlight"; // the error highlight class
	
	/**
	 * Constructor - Sets Form preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		}
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
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}		
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Creates the opening form element
	 * 
	 * @access public
	 * @param mixed attrs if array then create string
	 * @param object validator object
	 * @return string
	 */
	public function open($attrs = null, $validator = null)
	{
		if (!empty($attrs)) $this->attrs = $attrs;
		if (!empty($validator)) $this->validator =& $validator;
		return "<form".$this->_create_attrs($this->attrs).">";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the opening form element as a multipart
	 * 
	 * @access public
	 * @param mixed attrs if array then create string
	 * @param object validator object
	 * @return string
	 */
	public function open_multipart($attrs = null, $validator = null)
	{
		$attrs['enctype'] = 'multipart/form-data';
		return $this->open($attrs);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the close form element as a multipart
	 * 
	 * @access public
	 * @param string html to use before the closing form tag
	 * @param string whether to include the csrf field before the closing tag
	 * @return string
	 */
	public function close($html_before_form = '', $add_csrf_field = TRUE)
	{
		// test for get_instance function just to make sure we are using CI, in case we want to use this class elsewhere
		if (function_exists('get_instance') AND $add_csrf_field === TRUE)
		{
			$CI =& get_instance();
			if ($CI->config->item('csrf_protection') === TRUE)
			{
				$CI->security->csrf_set_cookie(); // need to set it again here just to be sure ... on initial page loads this may not be there
				$html_before_form .= $this->hidden($CI->security->csrf_token_name, $CI->security->csrf_hash);
			}
		}
		return $html_before_form.'</form>';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the opening fieldset element 
	 * 
	 * @access public
	 * @param string legend name
	 * @param mixed attrs if array then create string
	 * @return string
	 */
	public function fieldset_open($legend, $attrs = null)
	{
		$str = "<fieldset>\n";
		$str .= "<legend".$this->_create_attrs($attrs).">".$legend."</legend>\n";
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the close fieldset element 
	 * 
	 * @access public
	 * @return string
	 */
	public function fieldset_close()
	{
		return "\n</fieldset>\n";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates <input type="text"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function text($name, $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('text', $name, Form::prep($value), $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="password"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function password($name, $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('password', $name, Form::prep($value), $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="search"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function search($name, $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('search', $name, Form::prep($value), $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="hidden"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function hidden($name, $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('hidden', $name, $value, $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="radio"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function radio($name, $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('radio', $name, $value, $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="checkbox"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function checkbox($name, $value = '', $attrs = ''){
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('checkbox', $name, $value, $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="file"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function file($name, $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('file', $name, '', $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <select> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param array options for the form element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @param string the first option
	 * @return string
	 */
	public function select($name, $options = array(), $value = '', $attrs = '', $first_option = '')
	{
		$attrs = $this->_create_attrs($attrs);
		settype($options, 'array');
		$elem = new Form_select($name, $options, $value, $attrs, $first_option);
		return $this->_create_element($elem);
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates <textarea> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function textarea($name, $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_textarea($name, Form::prep($value), $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="submit"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function submit($value, $name = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('submit', $name, $value, $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="button"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function button($value, $name = '', $attrs = '', $use_input_type = TRUE)
	{
		$attrs = $this->_create_attrs($attrs);
		if ($use_input_type)
		{
			$elem = new Form_input('button', $name, $value, $attrs);
		}
		else
		{
			$elem = new Form_button('button', $name, $value, $attrs);
		}
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="reset"> tag
	 * 
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function reset($value, $name = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		$elem = new Form_input('reset', $name, $value, $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="image"> tag
	 * 
	 * @access public
	 * @param string src of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function image($src, $name = '', $value = '', $attrs = '')
	{
		$src = $this->_create_attrs(array('src' => $src));
		$attrs = $src.' '.$this->_create_attrs($attrs);
		$elem = new Form_input('image', $name, $value, $attrs);
		return $this->_create_element($elem);
	}
	
	// --------------------------------------------------------------------

	/**
	 * A helper method to prepare string for textarea... taken from Kohana
	 * 
	 * @access public
	 * @param string elements value
	 * @return string
	 */
	public static function prep($str, $double_encode = TRUE)
	{
		$str = (string) $str;
		
		// clean the string for utf8
		$CI =& get_instance();
		$str = $CI->utf8->clean_string($str);

		if ($double_encode === TRUE)
		{
			$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
		}
		else
		{
			// Do not encode existing HTML entities
			// From PHP 5.2.3 this functionality is built-in, otherwise use a regex
			if (version_compare(PHP_VERSION, '5.2.3', '>='))
			{
				$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8', FALSE);
			}
			else
			{
				$str = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $str);
				//$str = str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#39;', '&quot;'), $str);
				$str = str_replace(array('<', '>'), array('&lt;', '&gt;'), $str);
			}
		}
		return $str;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Checks a checkbox or radio
	 * 
	 * @access public
	 * @param string elements value
	 * @return string
	 */
	public static function do_checked($val)
	{
		if ($val == 'yes' OR $val == 'y' OR (int) $val === 1 OR $val === TRUE)
		{
			return 'checked="checked"';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Sets disabled attribute of a form element
	 * 
	 * @access public
	 * @param string elements value
	 * @return string
	 */
	public static function do_disabled($val)
	{
		if ($val)
		{
			return 'disabled="disabled"';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Sets read only attribute of a form element
	 * 
	 * @access public
	 * @param string elements value
	 * @return string
	 */
	public static function do_read_only($val)
	{
		if ($val)
		{
			return 'readonly="readonly"';
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the id attribute for the field and label
	 *
	 * @access	public
	 * @param	string name of the field
	 * @return	string
	 */
	public static function create_id($name)
	{
		return str_replace(array('[]', '[', ']', ' '), array('', '_', '','_'), $name);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates form attributes
	 * 
	 * @access protected
	 * @param mixed attrs if array then create string
	 * @return string
	 */
	protected function _create_attrs ($attrs)
	{
		if (is_array($attrs))
		{
			$str = '';
			foreach($attrs as $key => $val)
			{
				if ($val != '') $str .= ' '.$key.'="'.$val.'"';
			}
			return $str;
		}
		else if (!empty($attrs))
		{
			return ' '.$attrs;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Generates the html for the field
	 * 
	 * Creates error html when fields don't validate
	 * 
	 * @access protected
	 * @return string
	 */
	protected function _create_element($elem)
	{
		$str = '';
		$error = FALSE;
		if (is_object($this->validator))
		{
			$errors = $this->validator->get_errors();
			if (!empty($errors))
			{
				$errors = $this->validator->get_errors();
				$error = (isset($errors[$elem->name])) ? TRUE : FALSE;
			}
		}
		if ($error) $str .= "<span class=\"".$this->error_highlight_cssclass."\">";
		$str .= $elem->writeIt();
		if ($error) $str .= "</span>";
		return $str;
	}
	
	
}


// ------------------------------------------------------------------------

/**
 * A form input object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 */
Class Form_input {
	public $type; // type of the input form field
	public $name; // name of the input form field
	public $value; // value of the input form field
	public $attrs; // attributes of the input form field

	/**
	 * Constructor - Sets Form preferences
	 *
	 * @param string type of input element
	 * @param string name of input element attribute
	 * @param string value of select element
	 * @param string misc. attributes of select element
	 */
	public function __construct($type, $name, $value = '', $attrs = '')
	{
		$this->type = $type;
		$this->type = strtolower($this->type);
		$this->name = $name;
		$this->value = $value;
		$this->attrs = $attrs;
	}

	// --------------------------------------------------------------------

	/**
	 * Writes the html output of the form element
	 * 
	 * @access public
	 * @return string
	 */
	public function writeIt ()
	{
		$id = '';
		if (strpos($this->attrs, 'id="') === FALSE)
		{
			$name = Form::create_id($this->name);
			$id = ($this->type == 'radio') ? ' id="'.$name.'_'.str_replace(' ', '_', $this->value).'"' : ' id="'.$name.'"';
		}
		$this->attrs = str_replace('id=""', '', $this->attrs);
		
		$str = "<input type=\"".$this->type."\" name=\"".$this->name."\"".$id." value=\"".$this->value."\"".$this->attrs." />";
		return $str;
	}

}


// ------------------------------------------------------------------------

/**
 * A select form element
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 */
Class Form_select {

	public $name; // name of the select form field
	public $value; // select value(s) of the select form field
	public $options; // value of the select form field
	public $attrs; // attributes of the select form field
	public $default; // default value of the select form field
	public $first_option;
	protected $_selected_already = FALSE; // Used to keep track if something has been selected already

	/**
	 * Select constructor
	 * 
	 * @param string name of select element
	 * @param string value of select element
	 * @param string misc. attributes of select element
	 * @param string default value first line for a select
	 * @param string selected value(s)
	 */
	public function __construct($name, $options, $value = '', $attrs = '', $first_option = '')
	{
		$this->name = $name;
		$this->options = $options;
		$this->value = $value;
		$this->attrs = $attrs;
		$this->first_option = $first_option;
	}

	// --------------------------------------------------------------------

	/**
	 * Writes the html output of the form element
	 * 
	 * @access public
	 * @return string
	 */
	public function writeIt()
	{
		$str = '';
		$id = '';
		if (strpos($this->attrs, 'id="') === FALSE)
		{
			$id = ' id="'.Form::create_id($this->name).'" ';
		}
		$this->attrs = str_replace('id=""', '', $this->attrs);
		$str .= "<select name=\"".$this->name."\"".$id.$this->attrs.">\n";
		if (!empty($this->first_option)) {
			if (is_array($this->first_option))
			{
				foreach($this->first_option as $key => $val)
				{
					$str .= "\t\t<option value=\"".$key."\" label=\"".$val."\">".$val."</option>\n";
				}
			}
			else
			{
					$str .= "\t\t<option value=\"\" label=\"".Form::prep($this->first_option, FALSE)."\">".Form::prep($this->first_option, FALSE)."</option>\n";
			}
		}
		$selected = '';
		$i = 0;
		if (!empty($this->options) AND is_array($this->options))
		{
			foreach ($this->options as $key => $val)
			{
				if (is_array($val))
				{
					$str .= "\t<optgroup label=\"".$key."\">";
					foreach($val as $key2 => $val2){
						$str .= $this->_write_options($key2, $val2);
					}
					$str .= "\t</optgroup>";
				}
				else
				{
					$str .= $this->_write_options($key, $val);
				}
			}
		}
		$str .= "</select>\n";
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Writes the html output of the form element
	 * 
	 * @access protected
	 * @return string
	 */
	protected function _write_options($key, $val)
	{
		$selected = '';
		$key = (string) $key;
		if (isset($this->value))
		{
			if (is_array($this->value))
			{
				foreach ($this->value as $s_val)
				{
					$s_val = (string) $s_val;
					if ($key === $s_val)
					{
						$selected = ' selected="selected"';
						break;
					}
				}
			}
			else
			{
				if ($key == $this->value AND !$this->_selected_already)
				{
					$selected = ' selected="selected"';
					$this->_selected_already = TRUE;
				}
			}
		}
		return "\t\t<option value=\"".Form::prep($key, FALSE)."\" label=\"".Form::prep($val, FALSE)."\"".$selected.">".Form::prep($val, FALSE)."</option>\n";

	}


}


// ------------------------------------------------------------------------

/**
 * A text area form object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 */
Class Form_textarea {
	public $name; // name of the textarea form field
	public $value; // value of the textarea form field
	public $attrs; // attributes of the textarea form field

	/**
	 * Textarea constructor
	 * 
	 * @param string name of textarea element
	 * @param string value of textarea element
	 * @param string misc. attributes of textarea element
	 */
	public function __construct($name, $value, $attrs = '')
	{
		$this->name = $name;
		$this->value = $value;
		$this->attrs = $attrs;
	}

	// --------------------------------------------------------------------

	/**
	 * Writes the html output of the form element
	 * 
	 * @access public
	 * @return string
	 */
	public function writeIt()
	{
		$id = '';
		if (strpos($this->attrs, 'id="') === FALSE)
		{
			$id = ' id="'.Form::create_id($this->name).'" ';
		}
		if (strpos($this->attrs, 'rows="') === FALSE)
		{
			$this->attrs .= ' rows="10"';
		}
		if (strpos($this->attrs, 'cols="') === FALSE)
		{
			$this->attrs .= ' cols="40"';
		}
		
		
		$this->attrs = str_replace('id=""', '', $this->attrs);
		$str = "<textarea name=\"".$this->name."\"".$id.$this->attrs.">".$this->value."</textarea>";
		return $str;
	}

}

// ------------------------------------------------------------------------

/**
 * A form input object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 */
Class Form_button {
	public $type; // type of the input form field
	public $name; // name of the input form field
	public $value; // value of the input form field
	public $attrs; // attributes of the input form field

	/**
	 * Constructor - Sets Form preferences
	 *
	 * @param string type of input element
	 * @param string name of input element attribute
	 * @param string value of select element
	 * @param string misc. attributes of select element
	 */
	public function __construct($type, $name, $value = '', $attrs = '')
	{
		$this->type = $type;
		$this->type = strtolower($this->type);
		$this->name = $name;
		$this->value = $value;
		$this->attrs = $attrs;
	}

	// --------------------------------------------------------------------

	/**
	 * Writes the html output of the form element
	 * 
	 * @access public
	 * @return string
	 */
	public function writeIt ()
	{
		$id = '';
		if (strpos($this->attrs, 'id="') === FALSE)
		{
			$name = Form::create_id($this->name);
			$id = ($this->type == 'radio') ? ' id="'.$name.'_'.str_replace(' ', '_', $this->value).'"' : ' id="'.$name.'"';
		}
		$this->attrs = str_replace('id=""', '', $this->attrs);
		
		$str = "<button type=\"".$this->type."\" name=\"".$this->name."\"".$id." value=\"".$this->value."\"".$this->attrs.">".$this->value."</button>";
		return $str;
	}

}
/* End of file Form.php */
/* Location: ./application/libraries/Form.php */