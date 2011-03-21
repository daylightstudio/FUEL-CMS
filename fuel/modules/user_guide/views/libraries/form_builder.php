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
			<td>ID to be used for the form</td>
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