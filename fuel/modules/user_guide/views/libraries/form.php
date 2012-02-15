<h1>Form Class</h1>
<p>The Form class provides an alternative way to render forms from CodeIgniter's <a href="http://codeigniter.com/user_guide/helpers/form_helper.html" target="_blank">Form Helper</a>.</p>

<h2>Initializing the Class</h2>
<p>Like most other classes in CodeIgniter, the Form class is initialized in your controller using the <dfn>$this->load->library</dfn> function:</p>

<pre class="brush: php">$this->load->library('form');</pre>

<p>Alternatively, you can pass initialization parameters as the second parameter:</p>

<pre class="brush: php">$this->load->library('form', array('attrs' => 'method="get"', 'error_highlight_cssclass' => 'error'));</pre>


<h2>Why a Form Class?</h2>
<p>CodeIgniter has it's own <a href="http://codeigniter.com/user_guide/helpers/form_helper.html" target="_blank">Form helper</a>. There are a couple reasons why FUEL uses the <dfn>Form</dfn> class instead of the <dfn>CI Form helper</dfn>:</p>
<ol>
	<li>The Form class is used in combination with the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> and <a href="<?=user_guide_url('libraries/validator')?>">Validator</a> classes and so code control is important</li>
	<li>The integration with other classes made it crucial to be bundled up as an object that could be shared</li>
</ol>
<p class="important">Unlike CI's helper, this Form class will automatically insert an ID attribute for most of the fields. This can be overwritten with <dfn>id="[custam_value]"</dfn> passed to any of the methods below that have an <dfn>$attrs</dfn> parameter.</p>

<h2>Configuring Form Information</h2>
<p>There are several public properties you can use to configure the Form Class:</p>
<ul>
	<li><strong>attrs</strong> - The form tags attributes. Default is <dfn>method="post" action=""</dfn></li>
	<li><strong>validator</strong> - The <a href="<?=user_guide_url('libraries/validator')?>">validator</a> object to be used during the validation process</li>
	<li><strong>focus_highlight_cssclass</strong> - The focus css class. Default is <dfn>field_highlight</dfn></li>
	<li><strong>error_highlight_cssclass</strong> - The error highlight class. Default is <dfn>error_highlight</dfn></li>
</ul>
<br />

<h1>Function Reference</h1>

<h2>$this->form->open(<var>[attrs]</var>, <var>[validator]</var>)</h2>
<p>Will create a form open tag.
The <dfn>$attrs</dfn> parameter will be the attributes of the form (can be an array also).
The <dfn>$validator</dfn> parameter is a Validator Class object (optional).
</p>

<pre class="brush: php">
$validator = new Validator();
echo $this->form->open('id="my_form"', $validator); 
// will echo the following
&lt;form action="" method="post" id="my_form"&gt;
</pre>


<h2>$this->form->open_multipart(<var>[attrs]</var>, <var>[validator]</var>)</h2>
<p>Will create a form open tag that has multipart attribute.
The <dfn>$attrs</dfn> parameter will be the attributes of the form (can be an array also).
The <dfn>$validator</dfn> parameter is a Validator Class object (optional).
</p>

<pre class="brush: php">
$validator = new Validator();
echo $this->form->open_multipart('id="my_form"', $validator); 
// will echo the following
&lt;form action="" method="post" id="my_form"&gt;
</pre>


<h2>$this->form->close(<var>[html_before_form]</var><var>[add_csrf_field]</var>)</h2>
<p>Will create a form open tag that has multipart attribute.
The <dfn>$html_before_form</dfn> parameter is HTML to insert before closing the tag (optional).
The <dfn>$add_csrf_field</dfn> parameter is whether to include a hidden field of the csrf token if csrf is turned on.
</p>

<pre class="brush: php">
echo $this->form->close('<!--END OF FORM-->');
// will echo the following
<<!--END OF FORM-->&lt;/form&gt;
</pre>


<h2>$this->form->fieldset_open(<var>'legend'</var>, <var>['attrs']</var>)</h2>
<p>Will create a fieldset for the form.
The <dfn>$legend</dfn> parameter is the name to use in the legend.
The <dfn>$attrs</dfn> parameter is optional HTML attributes to use on the legend (optional).
</p>

<pre class="brush: php">
echo $this->form->fieldset_open('MY Form Legend', 'id="my_legend"');
// will echo the following
<fieldset>
&lt;legend id="my_legend"&gt;MY Form Legend&lt;/legend&gt;
</pre>


<h2>$this->form->fieldset_close()</h2>
<p>Will create a closing fieldset for the form.</p>

<pre class="brush: php">
echo $this->form->fieldset_close();
// will echo the following
&lt;/fieldset&gt;
</pre>


<h2>$this->form->text(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a text form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->text('email', 'dvader@deathstar.com', 'class="txt_field"');
// will echo the following
&lt;input type="text" name="email" id="email" value="dvader@deathstar.com" class="txt_field" /&gt;
</pre>


<h2>$this->form->password(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a password form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->password('pwd', 'abc134', 'class="txt_field"');
// will echo the following
&lt;input type="password" name="pwd" id="pwd"  value="" class="txt_field" /&gt;
</pre>


<h2>$this->form->search(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a search form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->search('searh', 'Search...', 'class="txt_field"');
// will echo the following
&lt;input type="searh" name="searh" id="searh" value="Search..." class="txt_field" /&gt;
</pre>


<h2>$this->form->hidden(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a hidden form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional). Hidden
fields don't normally have attributes.
</p>

<pre class="brush: php">
echo $this->form->hidden('id', '1', 'class="txt_field"');
// will echo the following
&lt;input type="hidden" name="id" id="id" value="1" /&gt;
</pre>


<h2>$this->form->radio(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a radio form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->radio('yesno', 'yes', '');
echo $this->form->radio('eitheror', 'no', '');

// will echo the following
&lt;input type="radio" name="yesno" id="eitheror" value="yes" /&gt;
&lt;input type="radio" name="yesno" id="eitheror" value="no" /&gt;
</pre>


<h2>$this->form->checkbox(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a checkbox form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->checkbox('yesno', 'yes', '');
// will echo the following
&lt;input type="checbock" name="yesno" id="yesno" value="yes" /&gt;
</pre>


<h2>$this->form->file(<var>'name'</var>, <var>[attrs]</var>)</h2>
<p>Creates a file upload form field (notice there is no value attribute).
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->file('myfile', 'class="file_class"');
// will echo the following
&lt;input type="file" name="myfile" id="myfile" value="" class="file_class" /&gt;
</pre>


<h2>$this->form->select(<var>'name'</var>, <var>[options]</var>, <var>['value']</var>, <var>[attrs]</var>, <var>['first_option']</var>)</h2>
<p>Creates a select form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$options</dfn> parameter is a key value array for the options. A nested array will yield option groups.
The <dfn>$value</dfn> parameter is the value attribute of the field and will select the appropriate option.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
The <dfn>$first_option</dfn> parameter is the first option value (e.g. "Select one of these...").
</p>

<pre class="brush: php">
$options = array();
$options['a'] = 'Option A';
$options['b'] = 'Option B';
$options['c'] = 'Option C';
echo $this->form->select('my_options', $options, 'b', 'class="select_class"', 'Select one of these...');
// will echo the following
&lt;select name="my_options" id="my_options" class="select_class"&gt;
	&lt;option value="" label="Select one of these..."&gt;Select one of these...&lt;/option&gt;
	&lt;option value="a" label="A"&gt;A&lt;/option&gt;
	&lt;option value="b" label="B" selected="selected"&gt;B&lt;/option&gt;
	&lt;option value="b" label="C"&gt;C&lt;/option&gt;
&lt;/select&gt;
</pre>


<h2>$this->form->textarea(<var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a textarea form field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->textarea('mytextfield', 'My text goes here.', 'class="txt_field"');
// will echo the following
&lt;textarea name="mytextfield" id="mytextfield" class="txt_field"&gt;
My text goes here.
&lt;/textarea&gt;
</pre>


<h2>$this->form->button(<var>'value'</var>, <var>'name'</var>, <var>[attrs]</var>, <var>'use_input_type'</var>)</h2>
<p>Creates a button form field. (note how the <dfn>$value</dfn> and <dfn>$name</dfn> are flip-flopped which is different from most of the other field creation methods).
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
The <dfn>$use_input_type</dfn> parameter determines whether a button or input type of button is  used. Default is <dfn>TRUE</dfn>.
</p>

<pre class="brush: php">
echo $this->form->button('mybutton', 'Click Me', 'class="btn"', FALSE);
// will echo the following
&lt;button type="button" name="mybutton" id="mybutton" value="Click Me" class="btn" /&gt;mybutton&lt;/button&gt;

// with the last parameter as TRUE
echo $this->form->button('mybutton', 'Click Me', 'class="btn"', TRUE);
// will echo the following
&lt;input type="button" name="mybutton" id="mybutton" value="Click Me" class="btn" /&gt;
</pre>


<h2>$this->form->submit(<var>'value'</var>, <var>'name'</var>, <var>[attrs]</var>)</h2>
<p>Creates a submit button. (note how the <dfn>$value</dfn> and <dfn>$name</dfn> are flip-flopped which is different from most of the other field creation methods).
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->submit('Submit', 'submit', 'class="submit"');
// will echo the following
&lt;input type="submit" name="submit" id="submit" value="Submit" class="submit" /&gt;
</pre>


<h2>$this->form->reset(<var>'value'</var>, <var>'name'</var>, <var>[attrs]</var>)</h2>
<p>Creates a password form field (note how the <dfn>$value</dfn> and <dfn>$name</dfn> are flip-flopped which is different from most of the other field creation methods).
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->reset('Reset', 'reset', 'class="reset_field"');
// will echo the following
&lt;input type="reset" name="reset" id="reset" value="Reset" 'class="reset_field"' /&gt;
</pre>


<h2>$this->form->image(<var>'src'</var>, <var>'name'</var>, <var>['value']</var>, <var>[attrs]</var>)</h2>
<p>Creates a password form field.
The <dfn>$src</dfn> the image src value.
The <dfn>$name</dfn> will also produce the id value of the field.
The <dfn>$value</dfn> parameter is the value attribute of the field.
The <dfn>$attrs</dfn> parameter can be either an array or a string(optional).
</p>

<pre class="brush: php">
echo $this->form->image('assets/images/my_img.jpg', 'go_button', 'go', 'class="img_field"');
// will echo the following
&lt;input type="image" src="assets/images/my_img.jpg" name="go_button" value="go" id="go_button" 'class="img_field"' /&gt;

</pre>
<h2>Form::prep(<var>'str'</var>)</h2>
<p>Static method that preps a form fields values.
The <dfn>$str</dfn> is the value to be prepped.
</p>

<pre class="brush: php">
echo Form::prep('This will safely prep the form field text with entities like &amp;amp; in it');
// will echo the following
This will safely prep the form field text with entities like &amp;amp; in it

</pre>


<h2>Form::do_checked(<var>'val'</var>)</h2>
<p>Static method that checks a checkbox or radio. 
If a <dfn>$val</dfn> is <dfn>'yes'</dfn>, <dfn>'y'</dfn>, <dfn>1</dfn>, or <dfn>TRUE</dfn>, then the checked attribute will be created
</p>

<pre class="brush: php">
$myval = (!empty($_POST['myval'])) ? $_POST['myval'] : '';
echo $this->form->checkbox('mycheckbox', 'yes', Form::do_checked($myval=='yes'));
// will echo the following
&lt;input type="checbock" name="mycheckbox" id="mycheckbox" value="yes" checked="checked" /&gt;
</pre>


<h2>Form::do_disabled(<var>'val'</var>)</h2>
<p>Static method that sets disabled attribute of a form element.
The <dfn>$str</dfn> will prep the form fields text value.
</p>

<pre class="brush: php">
$myval = (!empty($_POST['myval'])) ? $_POST['myval'] : '';
echo $this->form->text('mytext', '', Form::do_disabled($myval==''));
// will echo the following
&lt;input type="text" name="mytext" id="mytext" value="" disabled="disabled" /&gt;
</pre>


<h2>Form::do_read_only(<var>'val'</var>)</h2>
<p>Static method that sets read only attribute of a form element
The <dfn>$str</dfn> will prep the form fields text value.
</p>

<pre class="brush: php">
$myval = (!empty($_POST['myval'])) ? $_POST['myval'] : '';
echo $this->form->text('mytext', '', Form::do_read_only($myval==''));
// will echo the following
&lt;input type="text" name="mytext" id="mytext" value="" readonly="readonly" /&gt;
</pre>


<h2>Form::create_id(<var>'name'</var>)</h2>
<p>Static method that creates the id attribute for the field and label.
This will safely create an id field based on the name of a field.
</p>

<pre class="brush: php">
echo Form::create_id('my_array_field[1]');
// will echo the following
my_array_field_1
</pre>
