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

Class MY_Form_builder extends Form_builder {

	public $form; // form object used to create the form fields and associate errors with
	public $id = ''; // id to be used for the form
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

}

/* End of file Data_table.php */
/* Location: ./application/libraries/Form_builder.php */