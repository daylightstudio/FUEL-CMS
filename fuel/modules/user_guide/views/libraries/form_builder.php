<h1>Form Builder Class</h1>
<p>The Form Builder class creates forms based on an array of field information. FUEL uses this class to create the forms for modules.</p>
<p class="important">This class uses the Form class for rendering form fields.</p>
<h2>Initializing the Class</h2>
<p>Like most other classes in CodeIgniter, the Form Builder class is initialized in your controller using the <dfn>$this->load->library</dfn> function:</p>

<pre class="brush: php">$this->load->library('form_builder');</pre>

<p>Alternatively, you can pass initialization parameters as the second parameter:</p>

<pre class="brush: php">$this->load->library('form_builder', array('id'=>'contact_form','submit_value' => 'Send Message', 'textarea_rows' => '5', 'textarea_cols' => '28'));</pre>

<h2>Configuring Form Builder Information</h2>
<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>form</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Form object to be used</td>
		</tr>
		<tr>
			<td><strong>id</strong></td>
			<td>None</td>
			<td>None</td>
			<td>ID to be used for the containing table or id</td>
		</tr>
		<tr>
			<td><strong>css_class</strong></td>
			<td>form</td>
			<td>None</td>
			<td>CSS class to be used with the form</td>
		</tr>
		<tr>
			<td><strong>form_attrs</strong></td>
			<td>method="post" action=""</td>
			<td>can be array or string</td>
			<td>Form tag attributes</td>
		</tr>
		<tr>
			<td><strong>label_colons</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Add colons to form labels?</td>
		</tr>
		<tr>
			<td><strong>textarea_rows</strong></td>
			<td>10</td>
			<td>None</td>
			<td>Number of rows for a textarea</td>
		</tr>
		<tr>
			<td><strong>textarea_cols</strong></td>
			<td>60</td>
			<td>None</td>
			<td>Number of columns for a textarea</td>
		</tr>
		<tr>
			<td><strong>text_size_limit</strong></td>
			<td>40</td>
			<td>None</td>
			<td>Text size for a text input</td>
		</tr>
		<tr>
			<td><strong>submit_value</strong></td>
			<td>Submit</td>
			<td>None</td>
			<td>Submit value  (what the button says)</td>
		</tr>
		<tr>
			<td><strong>cancel_value</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Cancel value (what the button says)</td>
		</tr>
		<tr>
			<td><strong>cancel_action</strong></td>
			<td>None</td>
			<td>None</td>
			<td>What the cancel button does</td>
		</tr>
		<tr>
			<td><strong>reset_value</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Reset button value  (what the button says)</td>
		</tr>
		<tr>
			<td><strong>other_actions</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Additional actions to be displayed at the bottom of the form</td>
		</tr>
		<tr>
			<td><strong>use_form_tag</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Include the form opening/closing tags in rendered output</td>
		</tr>
		<tr>
			<td><strong>exclude</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Array of fields to exclude from the form</td>
		</tr>
		<tr>
			<td><strong>hidden</strong></td>
			<td>array('id')</td>
			<td>None</td>
			<td>Array of fields to display as hidden</td>
		</tr>
		<tr>
			<td><strong>readonly</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Readonly fields</td>
		</tr>
		<tr>
			<td><strong>disabled</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Disabled fields</td>
		</tr>
		<tr>
			<td><strong>displayonly</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Array of fields for display purposes only</td>
		</tr>
		<tr>
			<td><strong>date_format</strong></td>
			<td>m/d/Y</td>
			<td>m/d/Y</td>
			<td>Date format for date type fields</td>
		</tr>
		<tr>
			<td><strong>section_tag</strong></td>
			<td>h3</td>
			<td>Any HTML tag</td>
			<td>Section html tag</td>
		</tr>
		<tr>
			<td><strong>copy_tag</strong></td>
			<td>p</td>
			<td>Any HTML tag</td>
			<td>Copy html tag</td>
		</tr>
		<tr>
			<td><strong>fieldset</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Field set name</td>
		</tr>
		<tr>
			<td><strong>name_array</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Put the form fields into an array for namespacing</td>
		</tr>
		<tr>
			<td><strong>name_prefix</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Prefix the form fields as an alternatie to an array for namespacing</td>
		</tr>
		<tr>
			<td><strong>key_check</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The keycheck value used for forms that create session unique session variables to prevent spamming. If CSRF protection is enabled in CodeIgniter 2, then this value will be automatically filled out.</td>
		</tr>
		<tr>
			<td><strong>key_check_name</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The keycheck form name used for forms that create session unique session variables to prevent spamming.  If CSRF protection is enabled in CodeIgniter 2, then this value will be automatically filled out.</td>
		</tr>
		<tr>
			<td><strong>tooltip_format</strong></td>
			<td><span title="{?}" class="tooltip">[?]</span></td>
			<td>None</td>
			<td>Tooltip formatting string</td>
		</tr>
		<tr>
			<td><strong>tooltip_labels</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Use tooltip labels?</td>
		</tr>
		<tr>
			<td><strong>single_select_mode</strong></td>
			<td>auto</td>
			<td>auto, enum, select</td>
			<td>Auto will use enum if 2 or less and a single select if greater than 2. Other values are enum or select </td>
		</tr>
		<tr>
			<td><strong>multi_select_mode</strong></td>
			<td>auto</td>
			<td>auto, multi, checkbox</td>
			<td>Auto will use a series of checkboxes if 5 or less and a multiple select if greater than 5. Other values are multi or checkbox</td>
		</tr>
		<tr>
			<td><strong>boolean_mode</strong></td>
			<td>checkbox</td>
			<td>checkbox or enum</td>
			<td>Booleon mode can be checkbox or enum (which will display radio inputs)</td>
		</tr>
		<tr>
			<td><strong>display_errors_func</strong></td>
			<td>display_errors</td>
			<td>None</td>
			<td>The function used to generate errors... usually display_errors is the name</td>
		</tr>
		<tr>
			<td><strong>display_errors</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Displays errors at the top of the form if TRUE</td>
		</tr>
		<tr>
			<td><strong>question_keys</strong></td>
			<td>array('how', 'do', 'when', 'what', 'why', 'where', 'how', 'is', 'which', 'did', 'any')</td>
			<td>None</td>
			<td>adds question marks to the label if has these words in the label</td>
		</tr>
		<tr>
			<td><strong>show_required</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Show the required fields text at the bottom of the form</td>
		</tr>
		<tr>
			<td><strong>required_indicator</strong></td>
			<td>*</td>
			<td>None</td>
			<td>Indicator for a required field</td>
		</tr>
		<tr>
			<td><strong>required_text</strong></td>
			<td><span class="required">{required_indicator}</span> required fields</td>
			<td>None</td>
			<td>The required field text</td>
		</tr>
		<tr>
			<td><strong>label_layout</strong></td>
			<td>left</td>
			<td>left, top</td>
			<td>Where to place the labels when using a table</td>
		</tr>
		<tr>
			<td><strong>has_required</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Has a required field</td>
		</tr>
		<tr>
			<td><strong>render_format</strong></td>
			<td>table</td>
			<td>table, divs</td>
			<td>The HTML structure to render the form</td>
		</tr>
		<tr>
			<td><strong>row_id_prefix</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The id prefix to be used for assigning ids to each row of the form</td>
		</tr>
		<tr>
			<td><strong>lang_prefix</strong></td>
			<td>form_label</td>
			<td>None</td>
			<td>The prefix to be used for looking up associated language strings for form labels. If a field's name is 'last_modified', it will look for a language string of {lang_prefix}last_modified.</td>
		</tr>
		
	</tbody>
</table>

<h2>Field Options</h2>
<ul>
	<li><strong>name</strong> - the name of the field</li>
	<li><strong>type</strong> - the type of the field. If no type is specified it will default to a text input. Options are , 
								<dfn>hidden</dfn>, <dfn>textarea/text</dfn>, <dfn>enum</dfn>,
								<dfn>multi</dfn>, <dfn>select</dfn>, <dfn>file</dfn>
								<dfn>date</dfn>, <dfn>time</dfn>, <dfn>checkbox</dfn>
								<dfn>boolean</dfn>, <dfn>section</dfn>, <dfn>copy</dfn>,
								<dfn>custom</dfn>
								<div class="important">You'll notice above that a type of 'text' is a textarea and NOT a 'text' input. This is due to integration with database table types. If you want a normal text field, don't specify a type value or specify one not on the list.</div>
		
	</li>
	<li><strong>default</strong> - the default value of the field if no value is specified</li>
	<li><strong>max_length</strong> - the max length value of the field</li>
	<li><strong>comment</strong> - a comment for the field</li>
	<li><strong>label</strong> - the label of the field</li>
	<li><strong>required</strong> - is it required</li>
	<li><strong>size</strong> - the size of the field</li>
	<li><strong>class</strong> - the css class</li>
	<li><strong>options</strong> - select and enum options</li>
	<li><strong>checked</strong> - whether the input value is checked (for checkbox and radios)</li>
	<li><strong>value</strong> - the value of the field</li>
	<li><strong>readonly</strong> - the readonly attribute of the field</li>
	<li><strong>disabled</strong> - the disabled attribute of the field</li>
	<li><strong>order</strong> - the order in the form the field should appear</li>
	<li><strong>first_option</strong> - (for the select)</li>
	<li><strong>before_html</strong> - HTML to display before the form field</li>
	<li><strong>after_html</strong> - HTML to display after the form field</li>
	<li><strong>displayonly</strong> - will show the value text instead of a disabled field?</li>
	<li><strong>overwrite</strong> - overwrite the file when uploading</li>
	<li><strong>accept</strong> - file types to accept for uploading</li>
	<li><strong>upload_path</strong> - the server path to upload the file to</li>
	<li><strong>filename</strong> - the file name to convert the upload to</li>
	<li><strong>sorting</strong> - for multi selects that may need to keep track of selected options (combo jquery plugin)</li>
	<li><strong>mode</strong> - used for enums and multiple select fields whether to use selects or radios/checkbox</li>
</ul>

<h1>Function Reference</h1>

<h2>$this->form_builder->set_fields(<var>fields</var>)</h2>
<p>Set the fields for the form. Check the field table above for possible array values.</p>

<pre class="brush: php">
$fields['name'] = array('type' => 'text', 'label' => 'Full Name', 'required' => TRUE);
$fields['email'] = array('type' => 'text', 'label' => 'Email', 'required' => TRUE);
$fields['password'] = array('type' => 'password', 'label' => 'Password', 'required' => TRUE);
$fields['active'] = array('type' => 'enum', 'options' => array('yes' => 'yes', 'no' => 'no'), 'required' => TRUE);
$this->form_builder->set_fields($fields);
</pre>


<h2>$this->form_builder->set_field_values(<var>values</var>)</h2>
<p>Sets the values of the fields. This must be called AFTER the set_fields() method if used.</p>

<pre class="brush: php">
$values['name'] = 'Darth Vader';
$values['email'] = 'dvader@deathstar.com';
$values['password'] = 'd@rks1d3';
$values['active'] = 'yes';
$this->form_builder->set_field_values($values);
</pre>


<h2>$this->form_builder->render(<var>[fields]</var>, <var>['render_format']</var>)</h2>
<p>Renders the form.
The <dfn>$fields</dfn> value is optional and will set the fields before rendering.
The <dfn>$render_format</dfn> can be <dfn>table</dfn> or <dfn>divs</dfn>.
</p>

<pre class="brush: php">
$fields['name'] = array('type' => 'text', 'label' => 'Full Name', 'required' => TRUE);
$fields['email'] = array('type' => 'text', 'label' => 'Email', 'required' => TRUE);
$fields['password'] = array('type' => 'password', 'label' => 'Password', 'required' => TRUE);
$fields['active'] = array('type' => 'enum', 'options' => array('yes' => 'yes', 'no' => 'no'), 'required' => TRUE);
$this->form_builder->render($field, 'divs');
</pre>


<h2>$this->form_builder->render_divs(<var>[fields]</var>)</h2>
<p>Renders the form using <dfn>divs</dfn> for the HTML structure
The <dfn>$fields</dfn> value is optional and will set the fields before rendering.
</p>

<pre class="brush: php">
$fields['name'] = array('type' => 'text', 'label' => 'Full Name', 'required' => TRUE);
$fields['email'] = array('type' => 'text', 'label' => 'Email', 'required' => TRUE);
$fields['password'] = array('type' => 'password', 'label' => 'Password', 'required' => TRUE);
$fields['active'] = array('type' => 'enum', 'options' => array('yes' => 'yes', 'no' => 'no'), 'required' => TRUE);
$this->form_builder->render_divs($field);
</pre>


<h2>$this->form_builder->render_table(<var>[fields]</var>)</h2>
<p>Renders the form using <dfn>table</dfn> for the HTML structure
The <dfn>$fields</dfn> value is optional and will set the fields before rendering.
</p>

<pre class="brush: php">
$fields['name'] = array('type' => 'text', 'label' => 'Full Name', 'required' => TRUE);
$fields['email'] = array('type' => 'text', 'label' => 'Email', 'required' => TRUE);
$fields['password'] = array('type' => 'password', 'label' => 'Password', 'required' => TRUE);
$fields['active'] = array('type' => 'enum', 'options' => array('yes' => 'yes', 'no' => 'no'), 'required' => TRUE);
$this->form_builder->render_table($field);
</pre>

<h2>Other Functions</h2>
<p class="important">Although the following functions can be used, it is important to note that the <dfn>render</dfn> methods above
will call the approprate create field function based on the type of the field and therefore, these functions are rarely used.</p>

<h2>$this->form_builder->clear()</h2>
<p>Resets the html and fields applied to the current instance.</p>

<h2>$this->form_builder->create_field(<var>params</var>, <var>[normalize]</var>)</h2>
<p>Looks at the field type attribute and determines which form field to render.
The <dfn>$normalize</dfn> value is optional and will normalize the params to a common array structure. Default is <dfn>TRUE</dfn>
</p>

<pre class="brush: php">
$fields['name'] = array('type' => 'text', 'label' => 'Full Name', 'required' => TRUE);
$this->form_builder->create_field($field, TRUE);
</pre>


<h2>$this->form_builder->create_label(<var>params</var>, <var>[use_label]</var>)</h2>
<p>Creates the label for the form.
By default, if no label value is given, the method will generate one based on the name of the field.
The <dfn>$use_label</dfn> value is optional and will wrap the label in an HTML label tag. Default is <dfn>TRUE</dfn>
</p>

<pre class="brush: php">
$params = array('name' => 'name', 'type' => 'text', 'label' => 'Full Name', 'required' => TRUE);
$this->form_builder->create_label($params);
</pre>


<h2>$this->form_builder->create_text(<var>params</var>)</h2>
<p>Creates the text input for the form.</p>

<pre class="brush: php">
$params = array('name' => 'name', 'label' => 'Full Name', 'required' => TRUE);
$this->form_builder->create_text($params);
</pre>

<h2>$this->form_builder->create_submit(<var>params</var>)</h2>
<p>Creates a submit button.</p>

<pre class="brush: php">
$params = array('value' => 'My Submit');
$this->form_builder->create_submit($params);
</pre>

<h2>$this->form_builder->create_button(<var>params</var>)</h2>
<p>Creates a button. The <dfn>use_input</dfn> parameter determines whether to use the input type of button or to use the &lt;button&gt; tag.</p>

<pre class="brush: php">
$params = array('value' => 'My Button', 'use_input' => FALSE);
$this->form_builder->create_button($params);
</pre>


<h2>$this->form_builder->create_textarea(<var>params</var>)</h2>
<p>Creates a textarea input for the form.</p>

<pre class="brush: php">
$params = array('name' => 'content', 'label' => 'Content', 'value' => 'This is content for the textarea field');
$this->form_builder->create_textarea($params);
</pre>


<h2>$this->form_builder->create_hidden(<var>params</var>)</h2>
<p>Creates a hidden input for the form.</p>

<pre class="brush: php">
$params = array('name' => 'id', 'value' => '1');
$this->form_builder->create_hidden($params);
</pre>


<h2>$this->form_builder->create_enum(<var>params</var>)</h2>
<p>Creates an enum input for the form. If the class paramenter <dfn>boolean_mode</dfn> is set to auto and their are less than 2 options, then it will render radio inputs. Otherwise it will render a select input</p>

<pre class="brush: php">
$options = array(
	'a' => 'Option A',
	'b' => 'Option B',
)
$params = array('name' => 'my_options', 'options' => $options);
$this->form_builder->create_enum($params);
</pre>


<h2>$this->form_builder->create_multi(<var>params</var>)</h2>
<p>Creates a multi-select input for the form.</p>

<pre class="brush: php">
$options = array(
	'a' => 'Option A',
	'b' => 'Option B',
)
$params = array('name' => 'my_multi', 'options' => $options);
$this->form_builder->create_multi($params);
</pre>


<h2>$this->form_builder->create_select(<var>params</var>)</h2>
<p>Creates a select input for the form.</p>

<pre class="brush: php">
$options = array(
	'a' => 'Option A',
	'b' => 'Option B',
)
$params = array('name' => 'my_select', 'options' => $options);
$this->form_builder->create_select($params);
</pre>


<h2>$this->form_builder->create_file(<var>params</var>)</h2>
<p>Creates a file input for the form.</p>

<pre class="brush: php">
$params = array('name' => 'my_file', 'accept' => 'gif|jpg|png|jpeg', 'upload_path' => '/myuploads/', 'overwrite' => TRUE);
$this->form_builder->create_file($params);
</pre>


<h2>$this->form_builder->create_date(<var>params</var>)</h2>
<p>Creates an text field sized for date input with css classes of<dfn>datepicker</dfn> and <dfn>fillin</dfn> assigned to the field.
jQuery is used to transform those fields into datepickers using those classes.
</p>

<pre class="brush: php">
$params = array('name' => 'my_time', 'value' => '2010-01-01 12:00');
$this->form_builder->create_date($params);
</pre>


<h2>$this->form_builder->create_time(<var>params</var>)</h2>
<p>Creates two text fields and some am/pm radio buttons with css classes of<dfn>datepicker_hh</dfn>, <dfn>datepicker_mm</dfn>, <dfn>datepicker_am_pm</dfn> assigned to each field respectively. Additionally, each text field also has the class of <dfn>fillin</dfn>.</p>

<pre class="brush: php">
$params = array('name' => 'my_time', 'value' => '2010-01-01 12:00');
$this->form_builder->create_time($params);
</pre>


<h2>$this->form_builder->create_checkbox(<var>params</var>)</h2>
<p>Creates a checkbox input for the form.</p>

<pre class="brush: php">
$params = array('name' => 'my_checkbox', 'value' => 'yes', 'checked' => TRUE);
$this->form_builder->create_checkbox($params);
</pre>


<h2>$this->form_builder->create_boolean(<var>params</var>)</h2>
<p>Creates either a checkbox or a radio input for the form. This method check 
the boolean_mode attribute to determine what type of field, either checkbox or radio, to render.</p>

<pre class="brush: php">
$options = array(
	'y' => 'Yes',
	'n' => 'No',
)
$params = array('name' => 'my_checkbox', 'options' => $options);
$this->form_builder->create_boolean($params);
</pre>


<h2>$this->form_builder->create_section(<var>params</var>)</h2>
<p>Creates a section in the form. First checks the value, then the label, then the name attribute 
then wraps it in the copy_tag.</p>

<pre class="brush: php">
$params = 'This is a Section Heading';
$this->form_builder->create_section($params);
</pre>


<h2>$this->form_builder->create_copy(<var>params</var>)</h2>
<p>Creates a copy area for for the form. First checks the value, then the label, then the name attribute 
then wraps it in the copy_tag.</p>

<pre class="brush: php">
$params = 'This is a copy area';
$this->form_builder->create_copy($params);
</pre>


<h2>$this->form_builder->create_tooltip(<var>params</var>)</h2>
<p>Creates a tooltip on the label. Uses the tooltip_format class attribute to determine how to render tooltip.</p>

<pre class="brush: php">
$params = array('comment' => 'this is the comment for the tooltip', 'label' => 'My Tooltip example');
$this->form_builder->create_tooltip($params);
</pre>

<h2>$this->form_builder->create_custom(<var>func</var>, <var>params</var>)</h2>
<p>Creates a custom input form field. Calls a function and passes it the field params.</p>

<pre class="brush: php">
function my_custom_field($params)
{
	... code here
}

$this->form_builder->create_custom('my_custom_field', $params);
</pre>


<h2>$this->form_builder->set_validator(<var>validator</var>)</h2>
<p>Sets the validator object on the form object. 
The validator object is used to determine if the fields have been filled out properly and will display any errors at the top of the form.</p>

<pre class="brush: php">
$validator = new Validator();
$this->form_builder->set_validator($validator);
</pre>


<h2>$this->form_builder->set_field_order(<var>[order_arr]</var>)</h2>
<p>Sets the order of the fields. If an array is passed to the method, then that will be used as opposed to the field values <dfn>order</dfn> attribute.</p>

<pre class="brush: php">
$order = array('name', 'email', 'password');
$this->form_builder->set_field_order($order);
</pre>