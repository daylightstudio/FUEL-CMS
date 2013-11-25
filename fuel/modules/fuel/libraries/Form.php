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
 * @link		http://docs.getfuelcms.com/libraries/form
 */
class Form {
	
	public $attrs = 'method="post" action=""'; // form html attributes
	public $validator; // the validator object
	public $focus_highlight_cssclass = "field_highlight"; // the focus css class
	public $error_highlight_cssclass = "error_highlight"; // the error highlight class
	
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

		// automatically set the validator object to the $CI-validator object if it is load as a default
		$CI =& get_instance();
		if (isset($CI->validator))
		{
			$this->validator =& $CI->validator;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the validator object on the form object
	 * 
	 * @access public
	 * @param object validator object
	 * @return void
	 */
	public function set_validator(&$validator)
	{
		$this->validator = $validator;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the opening form element
	 * 
	<code>
	$validator = new Validator();
	echo $this->form->open('id="my_form"', $validator); 
	// will echo the following
	<form action="" method="post" id="my_form">
	</code>
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
	<code>
	$validator = new Validator();
	echo $this->form->open_multipart('id="my_form"', $validator); 
	// will echo the following
	<form action="" method="post" id="my_form">
	</code>
	 * @access public
	 * @param mixed attrs if array then create string
	 * @param object validator object
	 * @return string
	 */
	public function open_multipart($attrs = null, $validator = null)
	{
		$attrs['enctype'] = 'multipart/form-data';
		return $this->open($attrs, $validator);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the close form element as a multipart
	 * 
	<code>
	echo $this->form->close('<!--END OF FORM-->');
	// will echo the following
	<!--END OF FORM--></form>
	</code>
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
				$html_before_form .= $this->hidden($CI->security->get_csrf_token_name(), $CI->security->get_csrf_hash());
			}
		}
		return $html_before_form.'</form>';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the opening fieldset element 
	 * 
	<code>
	echo $this->form->fieldset_open('MY Form Legend', 'id="my_legend"');
	// will echo the following
	<fieldset>
	<legend id="my_legend">MY Form Legend</legend>
	</code>
	 * @access public
	 * @param string legend name
	 * @param mixed attrs if array then create string
	 * @return string
	 */
	public function fieldset_open($legend, $attrs = NULL, $fieldset_id = NULL)
	{
		if (!empty($fieldset_id))
		{
			$fieldset_id = " id=\"".$fieldset_id."\"";
		}
		$str = "<fieldset".$fieldset_id." ".$this->_create_attrs($attrs).">\n";
		$str .= "<legend>".$legend."</legend>\n";
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates the close fieldset element 
	 * 
	<code>
	echo $this->form->fieldset_close();
	// will echo the following
	</fieldset>
	</code>
	 * @access public
	 * @return string
	 */
	public function fieldset_close()
	{
		return "\n</fieldset>\n";
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates <input type="{text}"> tag
	 * 
	<code>
	// will echo the following
	echo $this->form->input('email','text', 'dvader@deathstar.com', 'class="txt_field"');
	<input type="text" name="email" id="email" value="dvader@deathstar.com" class="txt_field" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string type of input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function input($name, $type = 'text', $value = '', $attrs = '')
	{
		$attrs = $this->_create_attrs($attrs);
		if (empty($type)) $type = 'text';
		$elem = new Form_input($type, $name, Form::prep($value), $attrs);
		return $this->_create_element($elem);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="text"> tag
	 * 
	<code>
	echo $this->form->text('email', 'dvader@deathstar.com', 'class="txt_field"');
	// will echo the following
	<input type="text" name="email" id="email" value="dvader@deathstar.com" class="txt_field" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function text($name, $value = '', $attrs = '')
	{
		return $this->input($name, 'text', Form::prep($value), $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="password"> tag
	 * 
	<code>
	echo $this->form->hidden('password', 'abc134', 'class="txt_field"');
	// will echo the following
	<input type="passowrd" name="pwd" id="pwd"  value="" class="txt_field" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function password($name, $value = '', $attrs = '')
	{
		return $this->input($name, 'password', Form::prep($value), $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="search"> tag
	 * 
	<code>
	echo $this->form->search('searh', 'Search...', 'class="txt_field"');
	// will echo the following
	<input type="searh" name="searh" id="searh" value="Search..." class="txt_field" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function search($name, $value = '', $attrs = '')
	{
		return $this->input($name, 'search', Form::prep($value), $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="hidden"> tag
	 * 
	<code>
	echo $this->form->hidden('id', '1', 'class="txt_field"');
	// will echo the following
	<input type="hidden" name="id" id="id" value="1" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function hidden($name, $value = '', $attrs = '')
	{
		return $this->input($name, 'hidden', $value, $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="radio"> tag
	 * 
	<code>
	echo $this->form->radio('yesno', 'yes', '');
	echo $this->form->radio('eitheror', 'no', '');

	// will echo the following
	<input type="radio" name="yesno" id="eitheror" value="yes" />
	<input type="radio" name="yesno" id="eitheror" value="no" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function radio($name, $value = '', $attrs = '')
	{
		return $this->input($name, 'radio', $value, $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="checkbox"> tag
	 * 
	<code>
	echo $this->form->checkbox('yesno', 'yes', '');
	// will echo the following
	<input type="checkbox" name="yesno" id="yesno" value="yes" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function checkbox($name, $value = '', $attrs = ''){
		return $this->input($name, 'checkbox', $value, $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="file"> tag
	 * 
	<code>
	echo $this->form->file('myfile', 'class="file_class"');
	// will echo the following
	<input type="file" name="myfile" id="myfile" value="" class="file_class" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function file($name, $attrs = '')
	{
		return $this->input($name, 'file', '', $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <select> tag
	 * 
	<code>
	$options = array();
	$options['a'] = 'Option A';
	$options['b'] = 'Option B';
	$options['c'] = 'Option C';
	echo $this->form->select('my_options', $options, 'b', 'class="select_class"', 'Select one of these...');
	// will echo the following
	<select name="my_options" id="my_options" class="select_class">
		<option value="" label="Select one of these...">Select one of these...</option>
		<option value="a" label="A">A</option>
		<option value="b" label="B" selected="selected">B</option>
		<option value="b" label="C">C</option>
	</select>
	</code>
	* @access public
	 * @param string name of the input element
	 * @param array options for the form element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @param string the first option
	 * @param array an array of disabled options
	 * @return string
	 */
	public function select($name, $options = array(), $value = '', $attrs = '', $first_option = '', $disabled = array())
	{
		$attrs = $this->_create_attrs($attrs);
		settype($options, 'array');
		$elem = new Form_select($name, $options, $value, $attrs, $first_option, $disabled);
		return $this->_create_element($elem);
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates <textarea> tag
	 * 
	<code>
	echo $this->form->textarea('mytextfield', 'My text goes here.', 'class="txt_field"');
	// will echo the following
	<textarea name="mytextfield" id="mytextfield" class="txt_field">
	My text goes here.
	</textarea>
	</code>
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
	<code>
	echo $this->form->submit('Submit', 'submit', 'class="submit"');
	// will echo the following
	<input type="submit" name="submit" id="submit" value="Submit" class="submit" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function submit($value, $name = '', $attrs = '')
	{
		return $this->input($name, 'submit', $value, $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="button"> tag
	 * 
	<code>
	echo $this->form->button('mybutton', 'Click Me', 'class="btn"', FALSE);
	// will echo the following
	<button type="button" name="mybutton" id="mybutton" value="Click Me" class="btn" />mybutton</button>

	// with the last parameter as TRUE
	echo $this->form->button('mybutton', 'Click Me', 'class="btn"', TRUE);
	// will echo the following
	<input type="button" name="mybutton" id="mybutton" value="Click Me" class="btn" />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function button($value, $name = '', $attrs = '', $use_input_type = TRUE)
	{
		if ($use_input_type)
		{
			if (empty($name))
			{
				$name = Form::create_id($value);
			}
			$elem = $this->input($name, 'button', $value, $attrs);
		}
		else
		{
			$attrs = $this->_create_attrs($attrs);
			$btn = new Form_button('button', $name, $value, $attrs);
			$elem = $btn->render();
		}
		return $elem;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="reset"> tag
	 * 
	<code>
	echo $this->form->reset('Reset', 'reset', 'class="reset_field"');
	// will echo the following
	<input type="reset" name="reset" id="reset" value="Reset" 'class="reset_field"' />
	</code>
	 * @access public
	 * @param string name of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function reset($value, $name = '', $attrs = '')
	{
		return $this->input($name, 'reset', $value, $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Creates <input type="image"> tag
	 * 
	<code>
	echo $this->form->image('assets/images/my_img.jpg', 'go_button', 'go', 'class="img_field"');
	// will echo the following
	<input type="image" src="assets/images/my_img.jpg" name="go_button" value="go" id="go_button" 'class="img_field"' />
	</code>
	 * @access public
	 * @param string src of the input element
	 * @param string value for the input element
	 * @param mixed html attributes for the input element
	 * @return string
	 */
	public function image($src, $name = '', $value = '', $attrs = '')
	{
		$src = $this->_create_attrs(array('src' => $src));
		$attrs = $src.$this->_create_attrs($attrs);
		return $this->input($name, 'image', $value, $attrs);
	}
	
	// --------------------------------------------------------------------

	/**
	 * A helper method to prepare string for textarea... taken from Kohana
	 * 
	<code>
	echo Form::prep('This will safely prep the form field text with entities like &amp;amp; in it');
	// will echo the following
	This will safely prep the form field text with entities like &amp;amp; in it
	</code>
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
			$str = htmlspecialchars($str, ENT_QUOTES, config_item('charset'));
		}
		else
		{
			// Do not encode existing HTML entities
			// From PHP 5.2.3 this functionality is built-in, otherwise use a regex
			if (version_compare(PHP_VERSION, '5.2.3', '>='))
			{
				$str = htmlspecialchars($str, ENT_QUOTES, config_item('charset'), FALSE);
			}
			else
			{
				$str = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $str);
				//$str = str_replace(array(''\'', '"'), array('&#39;', '&quot;'), $str);
			}
		}
		return $str;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Checks a checkbox or radio
	 * 
	<code>
	$myval = (!empty($_POST['myval'])) ? $_POST['myval'] : '';
	echo $this->form->checkbox('mycheckbox', 'yes', Form::do_checked($myval=='yes'));
	// will echo the following
	<input type="checkbox" name="mycheckbox" id="mycheckbox" value="yes" checked="checked" />
	</code>
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
	<code>
	$myval = (!empty($_POST['myval'])) ? $_POST['myval'] : '';
	echo $this->form->text('mytext', '', Form::do_disabled($myval==''));
	// will echo the following
	<input type="text" name="mytext" id="mytext" value="" disabled="disabled" />
	</code>
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
	<code>
	$myval = (!empty($_POST['myval'])) ? $_POST['myval'] : '';
	echo $this->form->text('mytext', '', Form::do_read_only($myval==''));
	// will echo the following
	<input type="text" name="mytext" id="mytext" value="" readonly="readonly" />
	</code>
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
	<code>
	echo Form::create_id('my_array_field[1]');
	// will echo the following
	my_array_field_1
	</code>
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
	protected function _create_attrs($attrs)
	{
		if (is_array($attrs))
		{
			$str = '';
			$only_key = array('required', 'readonly', 'disabled', 'checked');
			foreach($attrs as $key => $val)
			{
				// the key must be a string value
				if (is_numeric($key))
				{
					continue;
				}

				// create data fields 
				if ($key == 'id' AND $val === FALSE)
				{
					// this gets stripped out upon rendering if the id is blank so we set it so if the value is FALSE
					$str .= ' id=""';
				}
				else if (is_array($val))
				{
					if ($key == 'data')
					{
						foreach($val as $k => $v)
						{
							if ($v !== '')
							{
								$str .= ' data-'.$k.'="'.$v.'"';
							}
						}
					}
					else
					{
						$str .= ' '.$key.'="'.implode(',', $val).'"';
					}
				}
				else if (!is_array($val))
				{
					if ($val != '')
					{
						if ($val === TRUE AND in_array($key, $only_key))
						{
							$str .= ' '.$key;		
						}
						else
						{
							$str .= ' '.$key.'="'.$val.'"';
						}
						
					}
				}
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
		if (is_object($this->validator) AND is_a($this->validator, 'Validator')) 
		{
			$errors = $this->validator->get_errors();
			if (!empty($errors))
			{
				$errors = $this->validator->get_errors();
				$error = (isset($errors[$elem->name])) ? TRUE : FALSE;
			}
		}
		if ($error) $str .= "<span class=\"".$this->error_highlight_cssclass."\">";
		$str .= $elem->render();
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
 * @autodoc		FALSE
 */
class Form_input {
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
	public function render ()
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
 * @autodoc		FALSE
 */
class Form_select {

	public $name; // name of the select form field
	public $value; // select value(s) of the select form field
	public $options; // value of the select form field
	public $attrs; // attributes of the select form field
	public $default; // default value of the select form field
	public $first_option; // the first option to display (e.g. Select one...)
	public $disabled; // an array of disabled options
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
	public function __construct($name, $options, $value = '', $attrs = '', $first_option = '', $disabled = array())
	{
		$this->name = $name;
		$this->options = $options;
		$this->value = $value;
		$this->attrs = $attrs;
		$this->first_option = $first_option;
		$this->disabled = $disabled;
	}

	// --------------------------------------------------------------------

	/**
	 * Writes the html output of the form element
	 * 
	 * @access public
	 * @return string
	 */
	public function render()
	{
		$str = '';
		$id = '';
		if (strpos($this->attrs, 'id="') === FALSE)
		{
			$id = ' id="'.Form::create_id($this->name).'" ';
		}
		$this->attrs = str_replace('id=""', '', $this->attrs);
		$str .= "<select name=\"".$this->name."\"".$id.$this->attrs.">\n";
		if (!empty($this->first_option)) 
		{
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
		$disabled = (is_array($this->disabled) AND in_array($key, $this->disabled)) ? ' disabled="disabled"' : '';
		return "\t\t<option value=\"".Form::prep($key, FALSE)."\" label=\"".Form::prep($val, FALSE)."\"".$selected.$disabled.">".Form::prep($val, FALSE)."</option>\n";

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
 * @autodoc		FALSE
 */
class Form_textarea {
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
	public function render()
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
 * A form button object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Form_button {
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
	public function render()
	{
		$id = '';

		if (strpos($this->attrs, 'id="') === FALSE)
		{
			$name = Form::create_id($this->name);
		}
		$this->attrs = str_replace('id=""', '', $this->attrs);
		
		$str = "<button type=\"".$this->type."\" name=\"".$this->name."\"".$id." value=\"".$this->value."\"".$this->attrs.">".$this->value."</button>";
		return $str;
	}

}
/* End of file Form.php */
/* Location: ./modules/fuel/libraries/Form.php */
