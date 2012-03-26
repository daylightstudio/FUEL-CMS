<h1>Forms</h1>
<p>One of the biggest features of FUEL CMS is it's ability to create simple to complicated form interfaces for your models and layouts. 
The main engine behind this feature is the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class.
</p>

<h2>Pre &amp; Post Processing Fields</h2>

<h2>Grouping Form Fields</h2>

<h2>Universal Field Attributes</h2>
<p>The following are field attributes that can be used with any field type:</p>
<ul>
	<li><strong>key</strong>: a unique identifier of the field... by default, will be the id. Used mostly for post processing of a field</li>
	<li><strong>id</strong>: the id of the field... will be auto generated if not provided. Set to FALSE if you don't want an ID value'</li>
	<li><strong>name</strong>: the name of the field</li>
	<li><strong>type</strong>: the field type (e.g. text, select, password, etc.)</li>
	<li><strong>default</strong>: the default value of the field</li>
	<li><strong>max_length</strong>: the maxlenth parameter to associate with the field</li>
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
	<li><strong>before_html</strong>: for html before the field</li>
	<li><strong>after_html</strong>: for html after the field</li>
	<li><strong>displayonly</strong>: only displays the value (no field)</li>
	<li><strong>pre_process</strong>: a pre process function</li>
	<li><strong>post_process</strong>: a post process function run on post</li>
	<li><strong>js</strong>: js file or script using &lt;script&gt; tag</li>
	<li><strong>css</strong>: css to associate with the field</li>
	<li><strong>represents</strong>: specifies what other types of fields that this field should represent</li>
	<li><strong>data</strong>: data attributes</li>
</ul>


<h2>Custom Field Types</h2>
<p>FUEL CMS 1.0 has overhauled the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class to make 
it easier to create custom form fields. Custom fields require a function or class method to render the field and an 
association to be made in the <span class="file">fuel/application/config/form_builder.php</span> file.

In fact, there are several custom field types provided by default including:</p>

<ul>
	<li><a href="#template">template</a></li>
	<li><a href="#asset">asset</a></li>
	<li><a href="#wysiwyg">wysiwyg</a></li>
	<li><a href="#file">file</a></li>
	<li><a href="#inline_edit">inline_edit</a></li>
	<li><a href="#linked">linked</a></li>
	<li><a href="#currency">currency</a></li>
	<li><a href="#state">state</a></li>
	<li><a href="#slug">slug</a></li>
	<li><a href="#list_items">list_items</a></li>
</ul>

<h3 id="template">template</h3>
<p>This field type can provide you a lot of flexibility in how you setup your forms by allowing you to nest sub forms and make them repeatable and draggable for reordering. 
This field type can work well for layout sections that have repeatable fields you may want to reorder (e.g. a title, body, image section).</p>

<ul>
	<li><strong>repeatable</strong>: determines whether the template can be repeatable</li>
	<li><strong>min</strong>: the minimum number of times you can repeat the template</li>
	<li><strong>max</strong>: the maximum number of times you can repeat the template</li>
	<li><strong>fields</strong>: the fields to pass to the template (you can only nest the "template" field 2 levels deep total)</li>
	<li><strong>view</strong>: the view file to use to for rendering the fields. If no view is provided, it will create a nested form builder object and render it.</li>
	<li><strong>template</strong>: a string value of the template as opposed to a view file to load in. </li>
	<li><strong>add_extra</strong>: determines whether to display the "Add" button for repeatable templates</li>
	<li><strong>depth</strong>: specifies the depth of the template (can only be 2 deep)</li>
	<li><strong>dblclick</strong>: determines whether a double click is required to open up the set of fields. Options are "accordian" and "toggle"</li>
	<li><strong>init_display</strong>: determines whether to open just the first repeatable set of fields, none of them, or all of them (default). Options are first, and none or closed (they are the same)</li>
	<li><strong>title_field</strong>: the field to be used {__title__} placeholder </li>
	<li><strong>parse</strong>: determines whether to parse the view or template file before rendering</li>
</ul>

<pre class="brush:php">
$field['sections'] = array('display_label' => FALSE, 
							'add_extra' => FALSE, 
							'init_display' => 'none', 
							'dblclick' => 'accordian', 
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


<h3 id="assets">asset</h3>
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
</ul>


<h3 id="wysiwyg">wysiwyg</h3>
<p>This field type is used for textareas and will use FUEL's <dfn>$config['text_editor']</dfn> configuration to determine which editor to display in the field by default.
The following additional parameters can be passed to this field type:</p>
<ul>
	<li><strong>editor</strong>: determines which editor to display in the field. Options are <dfn>markitup</dfn>, <dfn>ckeditor</dfn> and <dfn>FALSE</dfn> with the default being <dfn>markitup</dfn></li>
	<li><strong>class</strong>: although all fields can have the <dfn>class attribute</dfn>, passing the values of <dfn>markitup</dfn>, <dfn>ckeditor</dfn> or <dfn>no_editor</dfn> will have the same effect as explicitly adding the <dfn>editor</dfn> attribute</li>
</ul>

<h3 id="file">file</h3>
<p>This field type is used for uploading files. The following additional parameter can be passed to this field type:</p>
<ul>
	<li><strong>multiple</strong>: determines whether multiple files can be uploaded with the field</li>
</ul>


<h4>Image Specific</h4>
<ul>
	<li><strong>img_container_styles</strong>: styles to associate with the image preview container (only applies to image assets)</li>
	<li><strong>img_styles</strong>: styles applied to the actual image that is being previewed</li>
	<li><strong>create_thumb</strong>: determines whether to create a thumbnail</li>
	<li><strong>width</strong>: sets the width of the uploaded image</li>
	<li><strong>height</strong>: sets the height of the uploaded image</li>
	<li><strong>maintain_ratio</strong>: determines whether to maintain the images aspect ratio when resizing</li>
	<li><strong>master_dimension</strong>: sets the dimension (height or width) to be the master dimension when resizing and maintaining aspect ratio</li>
	<li><strong>hide_options</strong>: hides all of the upload options</li>
	<li><strong>hide_image_options</strong>: hides only the image specific upload options</li>
</ul>

<h3 id="inline_edit">inline_edit</h3>
<p>This field type is used for associating a separate module's data with your own.
The following additional parameter can be passed to this field type:</p>
<ul>
	<li><strong>module</strong>: the module to inline edit</li>
	<li><strong>multiple</strong>: whether to display multi field to associate the inline edited data with</li>
</ul>

<h3 id="linked">linked</h3>
<p>This field type will take the value from one field and apply it to another field after passing it through a function (e.g. a slug field being based on a title field)
The following additional parameter can be passed to this field type:</p>
<ul>
	<li><strong>linked_to</strong>: the field whose value to use if no value is provided. By default, the value will be processed through the <dfn>url_title</dfn> function.
		If an array is provided, the key is the name of the field to link to and the value is the function to transform the value (e.g. array('name' => 'url_title'))</li>
</ul>

<h3 id="currency">currency</h3>
<p>This field type can be used for inputting currency values.
The following additional parameter can be passed to this field type:</p>
<ul>
	<li><strong>currency</strong>: the currency value to display next to the field. The default is '$'</li>
	<li><strong>separator</strong>: the separator to use for the grouping of numbers</li>
	<li><strong>decimal</strong>: the decimal separator. The default is "." </li>
	<li><strong>grouping</strong>: the number of ...err numbers to group by</li>
	<li><strong>min</strong>: the min number to allow for the field (including negative numbers)</li>
	<li><strong>max</strong>: the max number to allow for the field</li>
</ul>


<h3 id="state">state</h3>
<p>This field displays a dropdown of states to select from. It automatically pulls it's options from the <span class="file">fuel/application/config/states.php</span> config file.</p>

<h3 id="slug">slug</h3>
<p>This field type can be used for creating slug or permalink values for a field using the <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">url_title</a> function.
The following additional parameter can be passed to this field type:</p>
<ul>
	<li><strong>link_to</strong>: the field whose value to use if no value is provided</li>
</ul>

<h3 id="list_items">list_items</h3>
<p>This field type allows you to create bulletted list items by separating each line by a return.</p>



<p>These custom field types are defined in the <span class="file">fuel/modules/fuel/libraries/Fuel_custom_fields.php</span> class.</p>

<p>In addition, you can overwrite or augment existing field types, by adding field type associations in the 
<span class="file">fuel/application/config/form_builder.php</span>. By default, there are several field types that do this to associate javascript with the fields:</p>
<ul>
	<li>date</li>
	<li>datetime</li>
	<li>multi</li>
	<li>file</li>
</ul>

<p class="important">The <span class="file">fuel/application/config/form_builder.php</span> file is included by the <span class="file">fuel/modules/fuel/config/form_builder.php</span> file.</p>


<h2>Representatives</h2>
<p>Also new to the <a href="<?=user_guide_url('libraries/form_builder')?>"> Form_builder</a> class is the concept of <dfn>representatives</dfn>.
Representatives allow you to assign a field with certain attributes (e.g. type, name, etc) to a specific field type. 
For example, FUEL will automatically set a field with a name of "pwd" or "passwd" to be the password type field. 
Or, if you have a field of type "int", "smallint", "mediumint", "bigint", it will be assigned the "number" field type. When assigning
a representative, the key is the field type to be the representative and the value is either an array or string/regular expression to match for fields to represent.</p>

<p>There are several ways to assign representatives to a field type:</p>

<p>The first is to add them to the $config['representatives'] array in the <span class="file">fuel/application/config/form_builder.php</span>:</p>
<pre class="brush:php">
// will assign any field of type 'int', 'smallint', 'mediumint' or 'bigint' to be represented by the 'my_field' type
$config['representatives']['my_field'] =  => array('int', 'smallint', 'mediumint', 'bigint');

// will assign any field with the name of 'pwd' or 'passwd' to the type of 'my_field'. This method is using the name attribute as the key. 
// If no key then it will assume the attribute is 'type'
$config['representatives']['my_field'] =  => array('name' => array('pwd', 'passwd'));
</pre>
<br />
<p>The second way is to assign them using the 'represents' attribute when making a custom fiel type association. For example, both the <dfn>datetime</dfn> and <dfn>wysiwyg</dfn> use this method by default as shown below:</p>

<pre class="brush:php">
$config['custom_fields'] = array(
	'datetime' => array(
		'css_class' => 'datepicker',
		'css' 		=> array(FUEL_FOLDER => 'fuel-theme/jquery-ui-1.8.17.custom'),
		'js'		=> array(FUEL_FOLDER => array('jquery/plugins/jquery-ui-1.8.17.custom.min',)),
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
$this->form_builder->set_representative('my_field', array('blob'))
</pre>