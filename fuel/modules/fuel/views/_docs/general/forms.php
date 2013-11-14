<?=css('jquery.supercomboselect, markitup, jquery-ui-1.8.17.custom', 'fuel')?>
<script>
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header_jqx'); ?>
</script>
<?=js('jqx/jqx', 'fuel')?>
<?=js('fuel/fuel.min', 'fuel')?>
<?php 
$CI->load->library('form_builder');
$CI->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');

//echo js('fuel/custom_fields.js', 'fuel');
echo js('jquery/plugins/jquery-ui-1.8.17.custom.min', 'fuel');

function form_builder_example($field, $params = NULL, $include_submit = FALSE)
{
	$CI =& get_instance();
	if (is_array($field))
	{
		$fields = $field;
	}
	else
	{
		$fields[$field] = $params;
	}
	if (!$include_submit)
	{
		$CI->form_builder->submit_value = '';
	} else {
		$CI->form_builder->submit_value = lang('btn_save');
	}
	$CI->form_builder->id = uniqid('form_');
	$CI->form_builder->cancel_value = '';
	$CI->form_builder->set_fields($fields);
	echo $CI->form_builder->render();
}

 ?>
<h1>Forms</h1>
<p>One of the biggest features of FUEL CMS is it's ability to create simple to complicated form interfaces for your models and layouts. 
The main engine behind this feature is the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class.
</p>

<ul>
	<li><a href="#examples">Examples</a></li>
	<li><a href="#universal_attributes">Universal Field Attributes</a></li>
	<li><a href="#form_field_types">Form Field Types</a></li>
	<li><a href="#association_parameters">Custom Field Type Association Parameters</a></li>
	<li><a href="#representatives">Representatives</a></li>
	<li><a href="#pre_post_processing">Pre &amp; Post Processing Fields</a></li>
</ul>


<h2 id="examples">Examples</h2>
<pre class="brush:php">

// load Form_builder;
$this->load->library('form_builder');

// load the custom form fields
$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');

// create fields
$fields['linked'] = array('type' => 'linked', 'linked_to' => array('name' => 'url_title'));
$fields['datetime'] = array('type' => 'datetime', 'first_day' => 2, 'date_format' => 'dd-mm-yyyy', 'min_date' => '01-01-2012', 'max_date' => '31-12-2012');
$fields['test_image'] = array('upload' => TRUE);
$fields['number'] = array('type' => 'number', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => TRUE, 'decimal' => TRUE);
$fields['currency'] = array('type' => 'currency', 'negative' => TRUE, 'decimal' => '.');
$fields['phone'] = array('type' => 'phone', 'required' => TRUE);
$fields['file_example'] = array('type' => 'file', 'overwrite' => TRUE, 'display_overwrite' => TRUE, 'multiple' => FALSE);
$fields['state'] = array('type' => 'state');
$fields['list_items'] = array('type' => 'list_items');
$fields['sections'] = array(
				'display_label' => FALSE,
				'type' => 'template', 
				'label' => 'Page sections', 
				'fields' => array(
						'layout' => array('type' => 'select', 'options' => array('img_right' => 'Image Right', 'img_left' => 'Image Left', 'img_right_50' => 'Image Right 50%', 'img_left_50' => 'Image Left 50%')),
						'title' => '',
						'action' => '',
						'content' => array('type' => 'textarea'),
						'image' => array('type' => 'asset', 'multiple' => FALSE, 'img_styles' => 'float: left; width: 100px;'),
						'images' => array('type' => 'template', 'repeatable' => TRUE, 'view' => '_admin/fields/images', 'limit' => 3, 'fields' => 
																array('image' => array('type' => 'asset', 'multiple' => TRUE))
																),
					),
				'view' => '_admin/fields/section', 
				'add_extra' => FALSE,
				'repeatable' => TRUE,
				);
// set the fields
$this->form_builder->set_fields($fields);

// render the page
$vars['form'] = $this->form_builder->render();
$this->load->view('page', $vars);
</pre>

<p class="important">Many of the custom field types may throw javascript errors if used outside of the CMS because they 
rely on javascript configuration values which are set by <a href="<?=user_guide_url('general/javascript#jqx')?>">jQX</a>. These config values 
would need to be created on the frontend (e.g. jqx.config.fuelPath, jqx.config.imgPath, ...etc), otherwise, you may see errors in the console like "jqx is not defined".
</p>


<h2 id="universal_attributes">Universal Field Attributes</h2>
<p>The following are field parameters that can be used with any field type:</p>
<ul>
	<li><strong>key</strong>: a unique identifier for the field. By default, it will be the the same as the ID. This parameter is used mostly for post processing of a field</li>
	<li><strong>id</strong>: the ID attribute of the field. This value will be auto generated if not provided. Set to FALSE if you don't want an ID value</li>
	<li><strong>name</strong>: the name attribute of the field</li>
	<li><strong>type</strong>: the type attribute of the field (e.g. text, select, password, etc.)</li>
	<li><strong>default</strong>: the default value of the field</li>
	<li><strong>max_length</strong>: the maxlength parameter to associate with the field</li>
	<li><strong>comment</strong>: a comment to assicate with the field's label'</li>
	<li><strong>label</strong>: the label to associate with the field</li>
	<li><strong>required</strong>: puts a required flag next to field label</li>
	<li><strong>size</strong>: the size attribute of the field</li>
	<li><strong>class</strong>: the CSS class attribute to associate with the field</li>
	<li><strong>style</strong>: inline style</li>
	<li><strong>value</strong>: the value of the field</li>
	<li><strong>readonly</strong>: sets readonly attribute on field</li>
	<li><strong>disabled</strong>: sets disabled attribute on the field</li>
	<li><strong>label_colons</strong>: whether to display the label colons</li>
	<li><strong>display_label</strong>: whether to display the label</li>
	<li><strong>order</strong>: the display order value to associate with the field</li>
	<li><strong>before_html</strong>: displays HTML before the field</li>
	<li><strong>after_html</strong>: displays HTML after the field</li>
	<li><strong>displayonly</strong>: only displays the value (no field)</li>
	<li><strong>pre_process</strong>: a pre process function that will be run on the value of the field before display</li>
	<li><strong>post_process</strong>: a post process function run on post</li>
	<li><strong>js</strong>: javascript file or script using &lt;script&gt; tag</li>
	<li><strong>css</strong>: CSS to associate with the field</li>
	<li><strong>represents</strong>: specifies what other types of fields that this field should represent</li>
	<li><strong>data</strong>: an array of data attribute fields. For example, an array of array('type' => 'my_type', 'title' => 'my_title') would yield
		field attributes of data-type="my_type" data-title="my_title". This parameter is handy for adding attributes you need to use with your javascript
	</li>
	<li><strong>row_class</strong>: sets a class on the containing table row or container div (depending on the rendering method)</li>
	<li><strong>tabindex</strong>: sets the tabindex value of a field. If using a mutli select, datetime, time, or enum, the value needs to be an array</li>
</ul>

<h2 id="form_field_types">Form Field Types</h2>
<p>FUEL CMS 1.0 has added several new field types as well as made it easier to create custom form fields.
In previous versions, to create a custom field type, you needed to create a custom function or class method 
and use the '<a href="#custom">custom</a>' field type to render it. In FUEL CMS 1.0, you can register those custom 
field types which means you don't need to make those associations for every form. It also allows you to 
associate them with their own <a href="<?=user_guide_url('general/javascript#forms')?>">javavascript</a> files and functions to execute upon rendering. 
In addition, you can overwrite or augment existing field types, by adding field type associations in the 
<span class="file">fuel/application/config/custom_fields.php</span>. For example, we use this method to 
associate the the datetime field type with the jQuery UI datepicker.

<p>Custom fields require a function or class method to render the field and an association to be made in the <span class="file">fuel/application/config/custom_fields.php</span> file (<a href="#association_parameters">this file is explained below</a>). 
Custom field types are not automatically load but can be done so by one of the following ways:
</p>
<pre class="brush:php">
// loads from a config file
$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');

// registers a single custom field
$this->form_builder->register_custom_field($key, $custom_field);
</pre>
	
<p>By default, FUEL CMS 1.0 provides several custom field types which are defined in the <span class="file">fuel/modules/fuel/libraries/Fuel_custom_fields.php</span> class.</p>

<div class="float_left" style="margin-right: 60px;">
	<h3>Built-in Form_builder Field Types</h3>
	<ul>
		<li><a href="#text">text</a></li>
		<li><a href="#password">password</a></li>
		<li><a href="#select">select</a></li>
		<li><a href="#checkbox">checkbox</a></li>
		<li><a href="#textarea">textarea</a></li>
		<li><a href="#hidden">hidden</a></li>
		<li><a href="#submit">submit</a></li>
		<li><a href="#button">button</a></li>
		<li><a href="#enum">enum</a></li>
		<li><a href="#multi">multi</a></li>
		<li><a href="#file">file</a></li>
		<li><a href="#date">date</a></li>
		<li><a href="#time">time</a></li>
		<li><a href="#datetime">datetime</a></li>
		<li><a href="#number">number</a></li>
		<li><a href="#email">email</a></li>
		<li><a href="#range">range</a></li>
		<li><a href="#boolean">boolean</a></li>
		<li><a href="#section">section</a></li>
		<li><a href="#fieldset">fieldset</a></li>
		<li><a href="#copy">copy</a></li>
		<li><a href="#nested">nested</a></li>
		<li><a href="#custom">custom</a></li>
	</ul>
</div>
<div class="float_left">
	<h3>FUEL Custom Field Types</h3>
	<ul>	
		<li><a href="#template">template</a></li>
		<li><a href="#block">block</a></li>
		<li><a href="#asset">asset</a></li>
		<li><a href="#url">url</a></li>
		<li><a href="#wysiwyg">wysiwyg</a></li>
		<li><a href="#file">file</a></li>
		<li><a href="#inline_edit">inline_edit</a></li>
		<li><a href="#linked">linked</a></li>
		<li><a href="#currency">currency</a></li>
		<li><a href="#state">state</a></li>
		<li><a href="#slug">slug</a></li>
		<li><a href="#list_items">list_items</a></li>
		<li><a href="#langauge">langauge</a></li>
		<li><a href="#keyval">keyval</a></li>
		<li><a href="#multi">multi</a> (overwritten for more functionality)</li>
	</ul>
</div>
<div class="clear"></div>

<h3 id="text" class="toggle">text</h3>
<div class="toggle_block_off">
	<p>This field type is the standard text field.</p>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['text_example'] = array('type' => 'text');
	</pre>
	<?php form_builder_example('text', array()); ?>
	
	<p class="important">The default field type if no "type" parameter is passed is a text input field.</p>
	
</div>

<h3 id="password" class="toggle">password</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard password field.</p>

	<h4>Representations</h4>
	<pre class="brush: php">
	'name' => array('pwd', 'passwd') // targets any field with the name of pwd, passwd
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['pwd_example'] = array('type' => 'password', 'label' => 'Password');
	// OR with the representative
	$fields['pwd'] = array('label' => 'Password');
	</pre>
	<?php form_builder_example('pwd', array('label' => 'Password')); ?>

</div>


<h3 id="select" class="toggle">select</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard select dropdown box.
	The following additional parameters can be passed to this field type:
	</p>
	<ul>
		<li><strong>options</strong>: an array of select options</li>
		<li><strong>first_option</strong>: The first option of which will have a blank value (e.g. Select one...)</li>
		<li><strong>model</strong>: The name of a model to use. The default method it will use is <dfn>options_list</dfn>. You can specify
		an array where the key is the name of the module and the value is either string value for the name of the model, or an array value where the key is
		the name of the model and the value is the method (see below). The '_model' suffix is
		not required when specifying the name of the model.
		</li>
		<li><strong>model_params</strong>: Additional parameters to pass to the model method that retrieves the options</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$options = array('a' => 'option A', 'b' => 'option B', 'c' => 'option C');
	$fields['select_example'] = array('type' => 'select', 'options' => $options, 'model' => 'people', 'first_option' => 'Select one...'); // will use the options_list method from the people_model
	$fields['select_example'] = array('type' => 'select', 'options' => $options, 'model' => array('my_module' => array('people' => 'people_options')), 'first_option' => 'Select one...'); // will use the people_options method from modules/my_module/people_model
	</pre>
	<?php form_builder_example('select_example',  array('type' => 'select', 'options' => array('a' => 'option A', 'b' => 'option B', 'c' => 'option C'), 'first_option' => 'Select one...')); ?>
	
</div>

<h3 id="checkbox" class="toggle">checkbox</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard checkbox field.
	The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>checked</strong>: determines whether to check the field selected or not</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['checkbox_example'] = array('type' => 'checkbox', 'checked' => TRUE);
	</pre>
	<?php form_builder_example('checkbox', array('type' => 'checkbox', 'checked' => TRUE)); ?>
	
</div>

<h3 id="textarea" class="toggle">textarea</h3>
<div class="toggle_block_off">
	<p>This field type creates a textarea field. Passing the 'class' parameter with a value of <dfn>no_editor</dfn> will render the field without the text editor (e.g. markitUp! or CKEditor depending on your settings).</p>
	<ul>
		<li><strong>rows</strong>: determines the number of rows to display. The default is the form builder objects <dfn>textarea_rows</dfn> property which by default is 10</li>
		<li><strong>cols</strong>: determines the number of columns to display. The default is the form builder objects <dfn>textarea_cols</dfn> property which by default is 60</li>
	</ul>
	
	<h4>Representations</h4>
	<p>This field is represented by <a href="#wysiwyg">wysiwyg</a> by default.</p>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['textarea_example1'] = array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'no_editor');
	$fields['textarea_example2'] = array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'markitup');
	$fields['textarea_example3'] = array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'wysiwyg'); //ckeditor
	</pre>
	<table class="form">
		<tbody>
			<tr>
				<td><?php form_builder_example('textarea_example1', array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'no_editor')); ?></td>
				<td><?php form_builder_example('textarea_example2', array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'markitup')); ?></td>
				<td><?php form_builder_example('textarea_example3', array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'wysiwyg')); ?></td>
			</tr>
		</tbody>
	</table>
</div>


<h3 id="hidden" class="toggle">hidden</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard hidden field.</p>
</div>

<h3 id="submit" class="toggle">submit</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard submit button.</p>

	<h4>Example</h4>
	<pre class="brush:php">
	$fields['submit_example'] = array('type' => 'submit', 'value' => 'Save');
	</pre>
	<?php form_builder_example('submit_example', array('type' => 'submit', 'value' => 'Save')); ?>

</div>

<h3 id="button" class="toggle">button</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard form button.
	The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>use_input</strong>: determines whether to use either a &lt;button&gt; or &lt;input type="button"&gt;. The default is TRUE</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['my_button'] = array('type' => 'button', 'value' => 'My Button');
	</pre>
	<?php form_builder_example('my_button', array('type' => 'button', 'value' => 'My Button')); ?>

</div>

<h3 id="enum" class="toggle">enum</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard checkbox field.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>checked</strong>: </li>
		<li><strong>options</strong>: an array of select options</li>
		<li><strong>model</strong>: The name of a model to use. The default method it will use is <dfn>options_list</dfn>. You can specify
		an array where the key is the name of the module and the value is either string value for the name of the model, or an array value where the key is
		the name of the model and the value is the method (see below). The '_model' suffix is
		not required when specifying the name of the model.
		</li>
		<li><strong>model_params</strong>: Additional parameters to pass to the model method that retrieves the options</li>
		<li><strong>mode</strong>: Options are 'auto', 'radios' and 'select'. Auto will show radio buttons if there are 2 or less, and will use a single select field if there are more.</li>
		<li><strong>wrapper_tag</strong>: The HTML tag to wrapper around the radio and label. Default is the 'span' tag.</li>
		<li><strong>wrapper_class</strong>: The CSS class to add to the to wrapper HTML element. Default is 'multi_field'.</li>
		<li><strong>spacer</strong>: The amount of space to put between each checkbox (if checkboxes are used). The default is 3 blank spaces.</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$options = array('yes' => 'yes', 'no' => 'no');
 	$fields['enum_example'] = array('type' => 'enum', 'mode' => 'radios', 'options' => $options);
 	$fields['enum_example'] = array('type' => 'enum', 'mode' => 'select', 'options' => $options);
	</pre>
	
	<?php 
	$options = array('yes' => 'yes', 'no' => 'no');
	form_builder_example('enum_example', array('type' => 'enum', 'mode' => 'radios', 'options' => $options));
	form_builder_example('enum_example', array('type' => 'enum', 'mode' => 'select', 'options' => $options)); ?>
	
	
</div>

<h3 id="multi" class="toggle">multi</h3>
<div class="toggle_block_off">
	<p>This field type creates either a series of checkboxes or a multiple select field.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>sorting</strong>: determines whether to allow for sorting of selected options. Default is FALSE.</li>
		<li><strong>options</strong>: an array of select options</li>
		<li><strong>model</strong>: The name of a model to use. The default method it will use is <dfn>options_list</dfn>. You can specify
		an array where the key is the name of the module and the value is either string value for the name of the model, or an array value where the key is
		the name of the model and the value is the method (see below). The '_model' suffix is
		not required when specifying the name of the model.
		</li>
		<li><strong>model_params</strong>: Additional parameters to pass to the model method that retrieves the options</li>
		<li><strong>mode</strong>: Options are 'auto', 'checkbox' and 'multi'. Auto will show checkboxes if there are 5 or less, and will use a multi select field if there are more.</li>
		<li><strong>wrapper_tag</strong>: The HTML tag to wrapper around the chexbox and label. Default is the 'span' tag.</li>
		<li><strong>wrapper_class</strong>: The CSS class to add to the to wrapper HTML element. Default is 'multi_field'.</li>
		<li><strong>spacer</strong>: The amount of space to put between each checkbox (if checkboxes are used). The default is 3 blank spaces.</li>
	</ul>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'type' => array('array') // targets any field with the type of array
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$options = array('a' => 'option A', 'b' => 'option B', 'c' => 'option C');
	$fields['multi_example'] = array('type' => 'multi', 'options' => $options, 'value' => 'a');
	</pre>
	<?php 
	$options = array('a' => 'option A', 'b' => 'option B', 'c' => 'option C');
	form_builder_example('multi_example1', array('type' => 'multi', 'options' => $options, 'value' => 'a', 'mode' => 'checkbox'));
	form_builder_example('multi_example2', array('type' => 'multi', 'options' => $options, 'value' => 'a', 'mode' => 'multi')); 
	?>
	
</div>

<h3 id="file" class="toggle">file</h3>
<div class="toggle_block_off">
	<p>This field type creates a standard file upload field.
	The following additional parameters can be passed to this field type:</p>

	<ul>
		<li><strong>overwrite</strong>: sets a paramter to either overwrite or create a new file if one already exists on the server. Default will overwrite</li>
		<li><strong>display_overwrite</strong>: determines if the overwrite checkbox appears next to the file upload field. Default value is TRUE</li>
		<li><strong>accept</strong>: specifies which files are acceptable to upload. The default is 'gif|jpg|jpeg|png'</li>
		<li><strong>upload_path</strong>: the server path to upload the file to. Default will be the asset images folder.</li>
		<li><strong>file_name</strong>: the new file name you want to assign</li>
		<li><strong>encrypt_name</strong>: determines whether to encrypt the uploaded file name to give it a unique value. The default is FALSE</li>
		<li><strong>multiple</strong>: determines whether to allow multiple files to be uploaded by the same field. The default is FALSE</li>
	</ul>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'type' => 'blob' // targets any field with the type of blog
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['file_example'] = array('type' => 'file', 'overwrite' => TRUE, 'display_overwrite' => TRUE, 'multiple' => FALSE, 'file_name' => 'my_file_{id}');
	</pre>
	<p class="important">Note the us of <dfn>"{id}"</dfn> in the <dfn>file_name</dfn> parameter. This will automatically merge in form field values for the name of the file.</p>
	<?php form_builder_example('file_example', array('type' => 'file', 'overwrite' => TRUE, 'display_overwrite' => TRUE, 'multiple' => FALSE)); ?>

</div>

<h3 id="date" class="toggle">date</h3>
<div class="toggle_block_off">
	<p>This field type creates a text field with a <a href="http://jqueryui.com/demos/datepicker" target="_blank">date picker</a>.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>date_format</strong>: The PHP date format in which you want the date to appear. This can be set in the <span class="file">fuel/application/config/MY_config.php</span> as well as on the Form_builder object. The default is <dfn>'m/d/Y'</dfn></li>
		<li><strong>region</strong>: You can specify a region to pull in the localization strings for the date picker. This requires the inclusion of an additional javascript file as explained <a href="http://docs.jquery.com/UI/Datepicker/Localization" target="_blank">here</a></li>
		<li><strong>min_date</strong>: The default is 01/01/2000</li>
		<li><strong>max_date</strong>: The default is 12/31/2100</li>
		<li><strong>first_day</strong>: Defalt is 0</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['date_example'] = array('type' => 'date', 'first_day' => 2, 'date_format' => 'd-m-Y', 'min_date' => '01-01-2012', 'max_date' => '31-12-2012');
	</pre>
	
	<?php form_builder_example('date_example', array('type' => 'date', 'first_day' => 2, 'date_format' => 'd-m-Y', 'min_date' => '01-01-2012', 'max_date' => '31-12-2012')); ?>
	
</div>

<h3 id="time" class="toggle">time</h3>
<div class="toggle_block_off">
	<p>This field type creates a hour and minute fields.</p>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['time_example'] = array('type' => 'time');
	</pre>
	<?php form_builder_example('time_example', array('type' => 'time')); ?>
</div>

<h3 id="datetime" class="toggle">datetime</h3>
<div class="toggle_block_off">
	<p>This field type combines the date and time fields and has the same additional parameters as the date field.</p>
	<p>This field type creates a text field with a <a href="http://jqueryui.com/demos/datepicker" target="_blank">date picker</a>.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>ampm</strong>: Determines whether to use am / pm radio buttons or 24 hour time. Default is TRUE</li>
	</ul>
	<h4>Representations</h4>
	<pre class="brush: php">
	'type' => 'datetime|timestamp' // targets any field with the type of datetime or timestampe (equivalent to array('datetime', 'timestamp'))
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['datetime_example'] = array('type' => 'datetime', 'first_day' => 2, 'date_format' => 'd-m-Y', 'min_date' => '01-01-2012', 'max_date' => '31-12-2012', 'ampm' => TRUE);
	</pre>
	
	<?php form_builder_example('datetime_example', array('type' => 'datetime', 'first_day' => 2, 'date_format' => 'd-m-Y', 'min_date' => '01-01-2012', 'max_date' => '31-12-2012', 'ampm' => TRUE)); ?>
	
</div>

<h3 id="number" class="toggle">number</h3>
<div class="toggle_block_off">
	<p>This field type creates a number field which is supported by <a href="http://html5doctor.com/html5-forms-input-types/" target="_blank">some modern browsers</a> checkbox field.
	The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>decimal</strong>: determines whether to allow decimals or not. The default is FALSE</li>
		<li><strong>negative</strong>: determines whether negative numbers can be inputted. The default is FALSE</li>
		<li><strong>min</strong>: the minimum number that can be inputted by clicking the number increment buttons</li>
		<li><strong>max</strong>: the maximum number that can be inputted by clicking the number increment buttons</li>
		<li><strong>step</strong>: determines the step value when increasing or decreasing the number</li>
	</ul>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'type' => array('int', 'smallint', 'mediumint', 'bigint') // targets any field with the type of int, smallint, mediumint or bigint
	</pre>
	
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['number_example'] = array('type' => 'number', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => TRUE, 'decimal' => TRUE);
	</pre>

	<?php form_builder_example('number_example', array('type' => 'number', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => TRUE, 'decimal' => TRUE)); ?>
	
</div>

<h3 id="email" class="toggle">email</h3>
<div class="toggle_block_off">
	<p>This field type creates an input field of type "email" which is <a href="http://html5doctor.com/html5-forms-input-types/" target="_blank">supported by some modern browsers</a> and will automatically validate the email address.</p>

	<h4>Example</h4>
	<pre class="brush:php">
	$fields['email_example'] = array('type' => 'email');
	</pre>
	
	<?php form_builder_example('email_example', array('type' => 'email'), TRUE); ?>
	
</div>

<h3 id="range" class="toggle">range</h3>
<div class="toggle_block_off">
	<p>This field type creates an input field of type "range" which is supported by <a href="http://html5doctor.com/html5-forms-input-types/" target="_blank">some modern browsers</a> and creates a slider.
	The following additional parameters can be passed to this field type:
	</p>
	<ul>
		<li><strong>min</strong>: the minimum value of the slider</li>
		<li><strong>max</strong>: the maximum value of the slider</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['range_example'] = array('type' => 'range', 'min' => 1, 'max' => 10);
	</pre>
	<?php form_builder_example('range_example', array('type' => 'range', 'min' => 1, 'max' => 10)); ?>
</div>

<h3 id="boolean" class="toggle">boolean</h3>
<div class="toggle_block_off">
	<p>This field type creates either a checkbox or an enum type field (e.g. published with a "yes" and "no" radio OR simply a "yes" checkbox). The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>options</strong>: an array of select options</li>
		<li><strong>mode</strong>: determines whether to display enum fields or a checkbox. Options are 'enum' or 'checkbox'. The default is 'checkbox'</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$options = array('a' => 'option A', 'b' => 'option B', 'c' => 'option C');
 	$fields['boolean_example'] = array('type' => 'boolean', 'mode' => 'radios', 'options' => $options);
	</pre>
	
	<?php form_builder_example('boolean_example', array('type' => 'boolean', 'mode' => 'checkbox', 'options' => $options)); ?>
	<?php form_builder_example('boolean_example', array('type' => 'boolean', 'mode' => 'enum', 'options' => $options)); ?>
	
</div>

<h3 id="nested" class="toggle">nested</h3>
<div class="toggle_block_off">
	<p>This field type creates a nested Form_builder object.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>fields</strong>: the fields to pass to the nested Form_builder object</li>
		<li><strong>init</strong>: an array of initialization parameters to be passed to the Form_builder object</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$f['nested_textarea'] = array('type' => 'textarea');
	$f['nested_boolean'] = array('type' => 'boolean');
	$fields['nested_example'] = array('type' => 'nested', 'fields' => $f, 'display_label' => FALSE);
	</pre>
	
	<?php 
	$fields = array();
	$f = array();
	$f['nested_textarea'] = array('type' => 'textarea');
	$f['nested_boolean'] = array('type' => 'boolean');
	form_builder_example('nested_example', array('type' => 'nested', 'fields' => $f, 'display_label' => FALSE)); ?>
	
</div>

<h3 id="fieldset" class="toggle">fieldset</h3>
<div class="toggle_block_off">
	<p>This field type groups fields together. If the form is rendered in the CMS, you can assign the 'class' parameter the value of
	<dfn>'tab'</dfn> or <dfn>"collapsible"</dfn> and the fields will grouped under tabs or collapsible headers respectively.</p>
</div>

<h3 id="section" class="toggle">section</h3>
<div class="toggle_block_off">
	<p>This field type creates heading in the form. You can can pass a <dfn>'value'</dfn>, <dfn>'label'</dfn> or <dfn>'name'</dfn> with the value of the
	what you want to display for the section. The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>tag</strong>: the HTML tag wrap around the heading (without the &lt;&gt;). The default is &lt;h3&gt;</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['section_example'] = array('type' => 'section', 'tag' => 'h3', 'value' => 'This is a section header example');
	</pre>
	
	<?php form_builder_example('section_example', array('type' => 'section', 'tag' => 'h3', 'value' => 'This is a section header')); ?>
</div>

<h3 id="copy" class="toggle">copy</h3>
<div class="toggle_block_off">
	<p>This field type creates copy block in the form. You can can pass a <dfn>'value'</dfn>, <dfn>'label'</dfn> or <dfn>'name'</dfn> with the value of the
	what you want to display for the section. The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>tag</strong>: the HTML tag to wrap around the copy (without the &lt;&gt;). The default is &lt;p&gt;</li>
	</ul>

	<h4>Example</h4>
	<pre class="brush:php">
	$fields['copy_example'] = array('type' => 'copy', 'tag' => 'p', 'value' => 'This is the copy example');
	</pre>
	
	<?php form_builder_example('copy_example', array('type' => 'copy', 'tag' => 'p', 'value' => 'This is the copy example')); ?>
</div>

<h3 id="custom" class="toggle">custom</h3>
<div class="toggle_block_off">
	<p>This field type can be used to create custom field types. To create reusable custom field types, visit the next section. 
	The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>func</strong>: the name of a function or an array of an object and method to be called to render the field.</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	function my_custom_field($params)
	{
		$form_builder =& $params['instance'];
		$str = $form_builder->create_checkbox($params).' <label for="custom_example">Select this checkbox</label>';
		return $str;
	}
	$fields['custom_example'] = array('type' => 'custom', 'func' => 'my_custom_field', 'display_label' => FALSE);
	</pre>
	
	<?php 
	function my_custom_field($params)
	{
		$form_builder =& $params['instance'];
		$str = $form_builder->create_checkbox($params).' <label for="custom_example">Select this checkbox</label>';
		return $str;
	}
	form_builder_example('custom_example', array('type' => 'custom', 'func' => 'my_custom_field', 'display_label' => FALSE)); ?>
	
</div>


<h3 id="template" class="toggle">template</h3>
<div class="toggle_block_off">
	<p>This field type can provide you a lot of flexibility in how you setup your forms by allowing you to nest sub forms and make them repeatable and draggable for reordering. 
	This field type can work well for layout sections that have repeatable fields you may want to reorder (e.g. a title, body, image section).</p>

	<ul>
		<li><strong>repeatable</strong>: determines whether the template can be repeatable</li>
		<li><strong>min</strong>: the minimum number of times you can repeat the template</li>
		<li><strong>max</strong>: the maximum number of times you can repeat the template</li>
		<li><strong>fields</strong>: the fields to pass to the template (you can only nest the "template" field 2 levels deep total)</li>
		<li><strong>view</strong>: the view file to use to for rendering the fields. If no view is provided, it will create a nested form builder object and render it</li>
		<li><strong>template</strong>: a string value of the template as opposed to a view file to load in. </li>
		<li><strong>add_extra</strong>: determines whether to display the "Add" button for repeatable templates</li>
		<li><strong>depth</strong>: specifies the depth of the template (can only be 2 deep)</li>
		<li><strong>dblclick</strong>: determines whether a double click is required to open up the set of fields. Options are "accordion" and "toggle"</li>
		<li><strong>init_display</strong>: determines whether to open just the first repeatable set of fields, none of them, or all of them (default). Options are <dfn>false</dfn>, <dfn>first</dfn>, and <dfn>none</dfn> or <dfn>closed</dfn> (they are the same)</li>
		<li><strong>title_field</strong>: the field to be used {__title__} placeholder </li>
		<li><strong>parse</strong>: determines whether to parse the view or template file before rendering</li>
		<li><strong>display_sub_label</strong>: determines whether to display the labels for the fields in the the sub form created (if no view is specified and it is using a nested form_builder instance)</li>
		<li><strong>condensed</strong>: if TRUE, this will update there repeatable field to use a condensed styling</li>
		<li><strong>non_sortable</strong>: if TRUE, this will hide the sorting grabber for repeatable fields</li>
	</ul>

	<h4>Example</h4>
	<pre class="brush:php">
	$fields['template_example'] = array('display_label' => FALSE, 
								'add_extra' => FALSE, 
								'init_display' => 'none', 
								'dblclick' => 'accordion', 
								'repeatable' => TRUE, 
								'style' => 'width: 900px;', 
								'type' => 'template', 
								'label' => 'Page sections', 
								'title_field' => 'title',
								'fields' => array(
													'sections' => array('type' => 'section', 'label' => '{__title__}'),
													'title' => array('style' => 'width: 800px'),
													'content' => array('type' => 'textarea', 'style' => 'width: 800px; height: 500px;'),
												)
											);
	</pre>
	<p class="important">You can nest a template field in the fields parameter but only one level deep.</p>
	<p class="important">You can use the {__num__}, {__index__} and {__title__} as placeholders in your view or template files.</p>
	
	<?php 
	$fields = array();
	$fields['template_example'] = array('display_label' => FALSE, 
								'add_extra' => FALSE, 
								'init_display' => 'all', 
								'dblclick' => 'accordion', 
								'repeatable' => TRUE, 
								'style' => 'width: 800px;', 
								'type' => 'template', 
								'label' => 'Page sections', 
								'title_field' => 'title',
								'fields' => array(
													'sections' => array('type' => 'section', 'label' => '{__title__}'),
													'title' => array('style' => 'width: 500px'),
													'content' => array('type' => 'textarea', 'style' => 'width: 500px; height: 100px;'),
												)
											);
	form_builder_example('template_example', $fields['template_example']); 
	?>
	
</div>

<h3 id="block" class="toggle">block</h3>
<div class="toggle_block_off">
	<p>This field type is used for dynamically pulling in <a href="<?=user_guide_url('general/layouts#layouts_block_layouts')?>">block layout fields</a>:</p>

	<h4>Upload Specific</h4>
	<ul>
		<li><strong>folder</strong>: determines which <span class="file">fuel/application/views/_blocks</span> subfolder to look in for displaying</li>
		<li><strong>filter</strong>: an array or regular expression string to filter out certain files (e.g. those beginning with underscores). The default value is <dfn>^_(.*)|\.html$</dfn></li>
		<li><strong>recursive</strong>: determines whether to recursively look for subfolders within the <span class="file">fuel/application/views/_blocks</span> folder</li>
		<li><strong>options</strong>: the options to display for the block selection. If left empty, it will default to using the Fue_blocks::options_list() method.</li>
		<li><strong>where</strong>: the where condition to be used for querying blocks stored in the CMS database</li>
		<li><strong>order</strong>: the order clause to be used for sorting the list option items obtained from the CMS stored blocks.</li>
		<li><strong>ajax_url</strong>: the AJAX URL used to get the form fields. Default is <dfn>'/blocks/layout_fields/{layout}/{page_id}/english/'</dfn></li>
		<li><strong>block_name</strong>: if specified, it will automatically return the fields of that block as opposed to a dropdown list to select from</li>
		<li><strong>group</strong>: if specified, will filter the options list to only those block layouts with that group name ('folder' must not be specified)</li>
	</ul>


	<h4>Example</h4>
	<pre class="brush:php">
	$fields['block_example'] = array('type' => 'block', 'folder' => 'sections');
	</pre>
	
</div>

<h3 id="asset" class="toggle">asset</h3>
<div class="toggle_block_off">
	<p>This field type is used for uploading and assigning asset values to fields. It can provide two buttons next to the field. One to select an asset and the other to upload.
	The following additional parameter can be passed to this field type:</p>

	<h4>Upload Specific</h4>
	<ul>
		<li><strong>upload</strong>: determines whether to display the upload button next to the field</li>
		<li><strong>folder</strong>: the asset folder to upload the asset (only applies if the upload parameter is not set to FALSE)</li>
		<li><strong>multiple</strong>: determines whether you can assign more then one asset to the field which would be separated by a comma</li>
		<li><strong>multiline</strong>: determines whether to use a textarea instead of a normal input field (good if using multiple parameter)</li>
		<li><strong>subfolder</strong>: a subfolder to upload the images to (will create one if it doesn't exist)</li>
		<li><strong>file_name</strong>: the new name to assign the uploaded file</li>
		<li><strong>overwrite</strong>: determines whether to overwrite the uploaded file or create a new file</li>
		<li><strong>unzip</strong>: determines whether to unzip zip files automatically or not</li>
		<li><strong>accept</strong>: specifies which files are acceptable to upload. It will default to what is specified in your fuel configuration for "editable_asset_filetypes"</li>
	</ul>
	
	<h4>Image Specific</h4>
	<ul>
		<li><strong>is_image</strong>: will provide an image preview no matter if the image does not end with jpg, png, gif etc.</li>
		<li><strong>img_container_styles</strong>: styles to associate with the image preview container (only applies to image assets)</li>
		<li><strong>img_styles</strong>: styles applied to the actual image that is being previewed</li>
		<li><strong>create_thumb</strong>: determines whether to create a thumbnail</li>
		<li><strong>width</strong>: sets the width of the uploaded image</li>
		<li><strong>height</strong>: sets the height of the uploaded image</li>
		<li><strong>maintain_ratio</strong>: determines whether to maintain the images aspect ratio when resizing</li>
		<li><strong>resize_and_crop</strong>: determines whether to crop the image to be forced into the dimensions</li>
		<li><strong>resize_method</strong>: values can be "maintain_ratio" or "resize_and_crop". This value will trump any value set for the "maintain_ratio" and "resize_and_crop"</li>
		<li><strong>master_dim</strong>: sets the dimension (height or width) to be the master dimension when resizing and maintaining aspect ratio</li>
		<li><strong>hide_options</strong>: hides all of the upload options</li>
		<li><strong>hide_image_options</strong>: hides only the image specific upload options</li>
	</ul>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'name' => '.*image$|.*img$' // targets any field with the name ending with "image" or "img"
	</pre>

	<h4>Example</h4>
	<pre class="brush:php">
	$fields['image'] = array('type' => 'asset', 'folder' => 'images/my_folder', 'hide_options' => TRUE);
	</pre>
	
</div>

<h3 id="url" class="toggle">url</h3>
<div class="toggle_block_off">
	<p>This field type is used for links/URLs.
	The following additional parameters can be passed to this field type:</p>

	<ul>
		<li><strong>input</strong>: provides an input field for the link. This field is not displayed by default</li>
		<li><strong>target</strong>: sets the target of the link. Options are <dfn>_self</dfn> or <dfn>_blank</dfn>. This field is not displayed by default</li>
		<li><strong>title</strong>: sets the title attribute of the link. This field is not displayed by default</li>
		<li><strong>pdfs</strong>: determines whether to display PDFs along with the list of URLs. Default it is set to not show PDFs (note that special logic will need to be created in the layouts to use either <dfn>site_url</dfn> or <dfn>pdf_path</dfn> functions)</li>
		<li><strong>filter</strong>: a regular expression value that can be used to filter the page list down to only the pages you need</li>
	</ul>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'name' => 'url|link' // targets any field with the name of "url" or "link"
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['url'] = array();
	</pre>

</div>

<h3 id="wysiwyg" class="toggle">wysiwyg</h3>
<div class="toggle_block_off">
	<p>This field type is used for textareas and will use FUEL's <dfn>$config['text_editor']</dfn> configuration to determine which editor to display in the field by default.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>editor</strong>: determines which editor to display in the field. Options are <dfn>markitup</dfn>, <dfn>wysiwyg</dfn> and <dfn>FALSE</dfn> with the default being <dfn>markitup</dfn> and wysiwyg being <a href="http://www.ckeditor.com" target="_blank">CKEditor</a></li>
		<li><strong>class</strong>: although all fields can have the <dfn>class attribute</dfn>, passing the values of <dfn>markitup</dfn>, <dfn>wysiwyg</dfn> or <dfn>no_editor</dfn> will have the same effect as explicitly adding the <dfn>editor</dfn> attribute</li>
		<li><strong>preview</strong>: the view file to use for previewing the content (only for markItUp! editor)</li>
		<li><strong>preview_options</strong>: preview popup window options (used as the third parameter of <a href="http://www.w3schools.com/jsref/met_win_open.asp" target="_blank">window.open</a> . The default is <dfn>width=1024,height=768</dfn></li>
		<li><strong>img_folder</strong>: the image folder to pull from when inserting an image</li>
		<li><strong>img_order</strong>: the image order displayed in the dropdown select. Options are <dfn>name</dfn> and <dfn>last_updated</dfn>. Default is <dfn>name</dfn></li>
		<li><strong>link_pdfs</strong>: a boolean value that determines whether to display PDFs along with the list of URLs when inserting a link. Default is set to FALSE which will not show PDFs (note that special logic will need to be created in the layouts to use either <dfn>site_url</dfn> or <dfn>pdf_path</dfn> functions)</li>
		<li><strong>editor_config</strong>: sets the editor's (markItUp! or CKEditor) configuration values for a particular field. Camel-cased attributes need to be converted to lowercase with hyphens (e.g. extraPlugins should be extra-plugins). These configuration values are attached to the textarea field so you can use
			Javascript to set more complex object values as long they are set on the textarea field before markItUp! or CKEditor initialization (e.g. $('.mytextarea').data('toolbar', [['Bold','Italic','Strike']]).</li>
		<li><strong>markdown</strong>: changes toolbar to use <a href="http://daringfireball.net/projects/markdown/" target="_blank">Markdown</a> formatting instead of HTML. Must have editor set to use markItUp!</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['wysiwyg_example1'] = array('type' => 'wysiwyg', 'editor' => 'markitup');
	$fields['wysiwyg_example2'] = array('type' => 'wysiwyg', 'editor' => 'wysiwyg'); //ckeditor
	</pre>
	<table class="form">
		<tbody>
			<tr>
				<td><?php form_builder_example('wysiwyg_example1', array('type' => 'wysiwyg', 'editor' => 'markitup')); ?></td>
				<td><?php form_builder_example('wysiwyg_example2', array('type' => 'wysiwyg', 'editor' => 'wysiwyg')); ?></td>
			</tr>
		</tbody>
	</table>

</div>

<h3 id="inline_edit" class="toggle">inline_edit</h3>
<div class="toggle_block_off">
	<p>This field type is used for associating a separate module's data with your own.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>module</strong>: the module to inline edit</li>
		<li><strong>multiple</strong>: whether to display a multi field to associate the inline edited data with</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
		$fields['inline_edit_example'] = array('type' => 'inline_edit', 'module' => 'projects');
	</pre>
	
	<?php 
	$fields = array();
	$fields['inline_edit_example'] = array('type' => 'inline_edit', 'options' => array('a' => 'a', 'b' => 'b', 'c' => 'c'), 'class' => 'add_edit projects');
	form_builder_example($fields);
	?>
</div>

<h3 id="linked" class="toggle">linked</h3>
<div class="toggle_block_off">
	<p>This field type will take the value from one field and apply it to another field after passing it through a function (e.g. a slug field being based on a title field).
	The default filter functions are <dfn>mirror</dfn>, <dfn>url_title</dfn>, <dfn>strtolower</dfn>, and <dfn>strtoupper</dfn>.
	The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>linked_to</strong>: the field whose value to use if no value is provided. By default, the value will be processed through the <dfn>url_title</dfn> function.
			If an array is provided, the key is the name of the field to link to and the value is the function to transform the value (e.g. array('name' => 'url_title'))</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
		$fields['name'] = array(); // field to link to
		$fields['linked_example'] = array('type' => 'linked', 'linked_to' => array('name' => 'strtoupper'));
	</pre>
	
	<?php 
	$fields = array();
	$fields['name'] = array(); // field to link to
	$fields['linked_example'] =  array('type' => 'linked', 'linked_to' => array('name' => 'strtoupper'));
	form_builder_example($fields);
	?>

</div>

<h3 id="currency" class="toggle">currency</h3>
<div class="toggle_block_off">
	<p>This field type can be used for inputting currency values.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>currency</strong>: the currency value to display next to the field. The default is '$'</li>
		<li><strong>separator</strong>: the separator to use for the grouping of numbers. The default is 3</li>
		<li><strong>decimal</strong>: the decimal separator. The default is "." </li>
		<li><strong>grouping</strong>: the number of ...err numbers to group by. The default is 3</li>
		<li><strong>min</strong>: the min number to allow for the field (including negative numbers). The default is 0.00</li>
		<li><strong>max</strong>: the max number to allow for the field. The default is 999999999.99</li>
	</ul>
	
	<p class="important">You must specify a default value for the field which is greater than or equal to the minimum value and less than or equal to the maximum value</p>
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['currency'] = array('type' => 'currency', 'decimal' => '.', 'currency' => '€', 'min' => -1000, 'max' => 1000);
	</pre>
	
	<?php form_builder_example('currency_example', array('type' => 'currency', 'decimal' => '.', 'currency' => '€', 'min' => -1000, 'max' => 1000)); ?>
	
</div>

<h3 id="state" class="toggle">state</h3>
<div class="toggle_block_off">
	<p>This field displays a dropdown of states to select from. It automatically pulls it's options from the <span class="file">fuel/application/config/states.php</span> config file.
	The following additional parameters can be passed to this field type:</p>
	<ul>
		<li><strong>format</strong>: the value can be either "short" or "long". Default is none in which the saved value will be the state abbreviation but the displayed option value will be the states name</li>
	</ul>

	<h4>Representations</h4>
	<pre class="brush: php">
	'name' => 'state' // targets any field with the name of state
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['state'] = array('type' => 'state');
	</pre>
	
	<?php form_builder_example('state_example', array('type' => 'state', 'format' => 'short')); ?>
	<?php form_builder_example('state_example', array('type' => 'state', 'format' => 'long')); ?>	
</div>

<h3 id="slug" class="toggle">slug</h3>
<div class="toggle_block_off">
	<p>This field type can be used for creating slug or permalink values for a field using the <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">url_title</a> function.
	The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>linked_to</strong>: the field whose value to use if no value is provided.
			If another field of 'title' or 'name' exists in the $_POST, then that value will be used as a default.
		</li>
	</ul>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'name' => 'slug|permalink' // targets any field with the name of slug or permalink (e.g. could also use array('slug', 'permalink'))
	</pre>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['title'] = array(); // field to link to
	$fields['slug_example'] = array('type' => 'slug', 'linked_to' => 'title');
	</pre>
	
	<?php 
	$fields = array();
	$fields['title_example'] = array();
	$fields['slug_example'] = array('type' => 'slug', 'linked_to' => 'title_example');
	form_builder_example($fields);
	?>
	
</div>

<h3 id="list_items" class="toggle">list_items</h3>
<div class="toggle_block_off">
	<p>This field type allows you to create bulletted list items by separating each line by a return. 
	The data saved in the database will be either an unordered or ordered HTML list. The following additional parameter can be passed to this field type:</p>
	<ul>
		<li><strong>list_type</strong>: the list type. Options are either "ol" or "ul". Default is "ul".</li>
	</ul>
	<h4>Example</h4>
	<pre class="brush:php">
	$value = "line1\nline2\nline3";
	$fields['list_items'] = array('type' => 'list_items', 'value' => $value, 'list_type' => 'ol');
	</pre>
	
	<?php form_builder_example('list_items_example', array('type' => 'list_items', 'value' => "line1\nline2\nline3", 'list_type' => 'ol')); ?>
	
</div>

<h3 id="language" class="toggle">language</h3>
<div class="toggle_block_off">
	<p>This field type generates a dropdown select with the language values specified in MY_fuel.php.</p>
	
	<h4>Representations</h4>
	<pre class="brush: php">
	'name' => 'language' // targets any field with the name of language
	</pre>

	<h4>Example</h4>
	<pre class="brush:php">
	$fields['language_example'] = array('type' => 'language');
	</pre>

	
</div>

<h3 id="keyval" class="toggle">keyval</h3>
<div class="toggle_block_off">
	<p>This field allows you go create a key / value array by separating keys and values with a delimiter. Each key/value goes on it's own line. The
		post-processed result is a JSON encoded string:</p>
	<ul>
		<li><strong>delimiter</strong>: the delimiter used to separate between a key and a value. The default is a ":" (colon).</li>
	</ul>
	
	<h4>Example</h4>
	<pre class="brush:php">
	$fields['keyval_example'] = array('type' => 'keyval');
	</pre>
	
	<?php 
	$fields = array();
	$fields['keyval_example'] = array('type' => 'keyval', 'value' => "english:English\ngerman:German\nspanish:Spanish");
	form_builder_example($fields);
	?>
	
</div>

<h2 id="association_parameters">Custom Field Type Association Parameters</h2>
<p>Creating a custom field type requires an association be made in the <span class="file">fuel/application/config/custom_fields.php</span>
to the <dfn>$config['custom_fields']</dfn> initialization parameter. The following parameters can be used in the association:
</p>
<ul>
	<li><strong>class</strong>: key is the module, and the value is the class</li>
	<li><strong>function</strong>: the method to execute. If no class is specified, then it will call it like a normal function</li>
	<li><strong>filepath</strong>: the path to the class or function. If no file path is provided, it will look in libraries folder</li>
	<li><strong>js</strong>: the javascript file to include. Can be a string or an array (uses the assets <a href="<?=user_guide_url('helpers/asset_helper#js')?>">js()</a> function)</li>
	<li><strong>js_function</strong>: the name of the javascript function to execute upon rendering of the form</li>
	<li><strong>js_exec_order</strong>: the execution order of the javascript function</li>
	<li><strong>css</strong>: the CSS file to include. Can be a string or an array (uses the assets <a href="<?=user_guide_url('helpers/asset_helper#css')?>">css()</a> function)</li>
</ul>

<h3>Example</h3>
<pre class="brush:php">
'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'), // key is the module, and the value is the class
'function'	=> 'template', // the method to execute. If no class is specified, then it will call it like a normal function
'filepath'	=> '', // if no file path is provided, it will look in libraries folder
'js'		=> array(
					FUEL_FOLDER => // the module in which assets/js folder to look in 
						'jquery/plugins/jquery.repeatable', // the path to the javascript file relative to the assets/js folder
						),
'js_function' => 'fuel.fields.template_field', // the name of the javascript function to execute upon rendering of the form
'js_exec_order' => 0, // the execution order of the javascript function
'css' => '', // the path to the css file relative to the assets/css folder
</pre>

<h2 id="representatives">Representatives</h2>
<p>Also new to the <a href="<?=user_guide_url('libraries/form_builder')?>"> Form_builder</a> class is the concept of <dfn>representatives</dfn>.
Representatives allow you to assign a field with certain attributes (e.g. type, name, etc) to a specific field type. 
For example, FUEL will automatically set a field with a name of 'pwd' or 'passwd' to be the 'password' type field. 
Or, if you have a field of type 'int', 'smallint', 'mediumint', 'bigint', it will be assigned the 'number' field type. When assigning
a representative, the key is the field type to be the representative and the value is either an array or string/regular expression to match for fields to represent.</p>

<p>There are several ways to assign representatives to a field type:</p>

<p>The first is to add them to the $config['representatives'] array in the <span class="file">fuel/application/config/custom_fields.php</span>:</p>

<pre class="brush:php">
// will assign any field of type 'int', 'smallint', 'mediumint' or 'bigint' to be represented by the 'my_field' type
$config['representatives']['my_field'] =  => array('int', 'smallint', 'mediumint', 'bigint');

// will assign any field with the name of 'pwd' or 'passwd' to the type of 'my_field'. This method is using the name attribute as the key. 
// If no key then it will assume the attribute is 'type'
$config['representatives']['my_field'] =  => array('name' => array('pwd', 'passwd'));
</pre>
<br />
<p>The second way is to assign them using the 'represents' attribute when making a custom field type association. For example, both the <dfn>datetime</dfn> and <dfn>wysiwyg</dfn> use this method by default as shown below:</p>

<pre class="brush:php">
$config['custom_fields'] = array(
	'datetime' => array(
		'css_class' => 'datepicker',
		'js_function' => 'fuel.fields.datetime_field',
		// 'js_params' => array('format' => 'mm-dd-yyyy'),
		'represents' => 'datetime|timestamp',
	),

	'wysiwyg' => array(
		'class'		=> array(FUEL_FOLDER => 'Fuel_custom_fields'),
		'function'	=> 'wysiwyg',
		'filepath'	=> '',
		'css' 		=> array(FUEL_FOLDER => 'markitup'),
		'js'		=> array(
							FUEL_FOLDER => array(
								'editors/markitup/jquery.markitup',
								'editors/markitup/jquery.markitup.set',
								'editors/ckeditor/ckeditor.js',
								'editors/ckeditor/config.js',
							)
		),
		'css' => array(FUEL_FOLDER => 'markitup'),
		'js_function' => 'fuel.fields.wysiwyg_field',
		'represents' => array('text', 'textarea', 'longtext', 'mediumtext'),
	),

</pre>
<br />
<p>The third way is to assign it when creating a field like so:</p>
<pre class="brush:php">
$fields['my_field'] = array('type' => 'my_field', 'represents' => 'blob');
</pre>
<br />
<p>The fourth way of setting a representative is to simply use the <dfn>set_representative</dfn> method on the <dfn>form_builder</dfn> object like so:</p>

<pre class="brush:php">
$this->form_builder->set_representative('my_field', array('blob'));
</pre>

<h3>Removing Representatives</h3>
<p>Sometimes a field may be using a representative that you don't won't. For example, you may have a field that has "url" in the name and it is using the url field type which
you don't want. To fix that you can use the <dfn>ignore_representative</dfn> parameter like so:</p>
<pre class="brush:php">
$fields['my_field'] = array('type' => 'my_field', 'ignore_representative' => TRUE);
</pre>
<br />
<p>If you'd like to remove a representative completely from the <dfn>form_builder</dfn> instance, you can use the <dfn>remove_representative</dfn> like so:</p>
<pre class="brush:php">
$this->form_builder->remove_representative('url');
</pre>


<h2 id="pre_post_processing">Pre &amp; Post Processing Fields</h2>
<p>If you need a field that does additional processing before being set as the value of the field or after posting, you can create a pre-processing or post-processing function to handle it. 
	To register that function with the field, you specify the <dfn>pre_process</dfn> or <dfn>post_process</dfn> parameter respectively.
	The value assigned to the the pre/post_process parameters is the name of the function (as a string), a lambda function (from create_function), 
	or an array with the first value being the instance of an object and the second value being the name of the method on that object. There are several
	custom functions that take advantage of this feature including the <a href="#asset">asset</a>, <a href="#slug">slug</a> <a href="#template">template</a>, <a href="#currency">currency</a> and <a href="#keyval">keyval</a> field types.</p>