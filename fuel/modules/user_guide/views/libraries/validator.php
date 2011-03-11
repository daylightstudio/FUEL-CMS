<h1>Validator Class</h1>
<p>The Validator class provides an alternative validation library from CodeIgniter's <a href="http://codeigniter.com/user_guide/libraries/form_validation.html" target="_blank">Form Validation Class</a>.</p>

<h2>Initializing the Class</h2>
<p>Like most other classes in CodeIgniter, the Validator class is initialized in your controller using the <dfn>$this->load->library</dfn> function:</p>

<pre class="brush: php">$this->load->library('validator');</pre>

<p>Alternatively, you can pass initialization parameters as the second parameter:</p>

<pre class="brush: php">$this->load->library('validator', array('load_helpers' => FALSE));</pre>


<p class="important">There is a <a href="<?=user_guide_url('helpers/validator_helper')?>">validator_helper</a> that will automatically get loaded with this class.
This helper contains common validation functions that can be used with the Validator Class.</p>

<h2>Why a Different Validation Class?</h2>
<p>CodeIgniter has it's own <a href="http://codeigniter.com/user_guide/libraries/form_validation.html" target="_blank">Form Validation class</a>. There are a couple reasons why FUEL uses the <dfn>Validator</dfn> class instead of the <dfn>CI Validation Class</dfn>:</p>
<ol>
	<li>Validator is used in combination with the <a href="<?=user_guide_url('libraries/form')?>">Form</a> and <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a> classes and so code control is important</li>
	<li>The CI <dfn>Validation Class </dfn>relies on $_POST whereas the <dfn>Validator</dfn> is a little more generic in that sense and does not</li>
</ol>


<h2>Configuring Validator Information</h2>
<p>There are several public properties you can use to configure the Validator Class:</p>
<ul>
	<li><strong>field_error_delimiter</strong> - delimiter for rendering multiple errors for a field. Default is <dfn>"\n"</dfn></li>
	<li><strong>stack_field_errors</strong> - stack multiple field errors if any or just replace with the newest. Default is <dfn>FALSE</dfn></li>
	<li><strong>register_to_global_errors</strong> - will add to the globals error array. Default is <dfn>TRUE</dfn></li>
	<li><strong>load_helpers</strong> - will automatically load the validator helpers. Default is <dfn>TRUE</dfn></li>
</ul>

<h2>The Validator Rule</h2>
<p>Validator rules are simple objects that have a <dfn>func</dfn> and <dfn>msg</dfn> property and a <dfn>rund</dfn> method. You most likely won't work with the objects directly but instead use the functions below.</p>

<h1>Function Reference</h1>

<h2>$this->validator->add_rule(<var>'field'</var>, <var>'func'</var>, <var>'msg'</var>, <var>'params'</var>)</h2>
<p>Will add a processing rule (function) to an input variable. 
The <dfn>$field</dfn> parameter will most like be the name of your form field you want to validate.
The <dfn>$func</dfn> parameter is the name of the function to execute. The function must return <dfn>TRUE</dfn> or <dfn>FALSE</dfn>.
The <dfn>$msg</dfn> parameter is the error message to display if the <dfn>$func</dfn> returns <dfn>FALSE</dfn>.
The <dfn>$params</dfn> parameter are the parameters to pass to the <dfn>$func</dfn>. If nothing is provided, it will look for <dfn>$_POST[$field]</dfn>.
To pass multiple arguments to the processing <dfn>$func</dfn>, turn <dfn>$params</dfn> into an array with the additional arguments being added after the value to be processed (see example below for <dfn>length_max</dfn>).
</p>

<pre class="brush: php">
$posted = $_POST;
$this->validator->add_rule('name', 'required', 'The name field is required', $posted['name']);
$this->validator->add_rule('name', 'length_max', 'The name field must be no greater then 20 characters', array($posted['name'], 20));
</pre>


<h2>$this->validator->remove_rule(<var>'field'</var>, <var>'func'</var>)</h2>
<p>Removes a rule from a field.
The <dfn>$field</dfn> specifies which field to remove the rule from.
If a <dfn>$func</dfn> parameter is passed, then it will limit the removal to just that function if there happens to be multiple rules assigned to a field.</dfn>.
</p>

<pre class="brush: php">
$this->validator->remove_rule('name', 'required');
</pre>

<h2>$this->validator->validate()</h2>
<p>Runs the validation rules and returns <dfn>TRUE</dfn> when all rules are valid and <dfn>FALSE</dfn> otherwise.</p>

<pre class="brush: php">
if ($this->validator->validate())
{
	echo 'We are valid!';
}
</pre>


<h2>$this->validator->is_valid()</h2>
<p>Returns <dfn>TRUE</dfn> if the validation has been run and all rules are valid and <dfn>FALSE</dfn> otherwise.</p>

<pre class="brush: php">
$this->validator->validate();
if ($this->validator->is_valid())
{
	echo 'We are valid!';
}
</pre>


<h2>$this->validator->catch_error(<var>'key'</var>, <var>'msg'</var>)</h2>
<p>Will catch an error message.</p>

<pre class="brush: php">
if ($A != $B)
{
	$this->validator->catch_error('my_field', 'A does not equal B');
}
</pre>

<h2>$this->validator->catch_errors(<var>errors</var>)</h2>
<p>Catches multiple errors  where <dfn>errors</dfn> is an array of error messages.</p>

<pre class="brush: php">
if (!$this->my_model->save())
{
	$errors = $this->my_model->get_errors();
	$this->validator->catch_errors($errors);
}
</pre>

<h2>$this->validator->get_errors()</h2>
<p>Returns an array of error messages if they exist.</p>

<pre class="brush: php">
if ($A != $B)
{
	$this->validator->catch_error('my_field', 'A does not equal B');
}

foreach($this->validator->get_errors() as $error)
{
	echo $error; // 'A does not equal B'
}
</pre>

<h2>$this->validator->get_last_error()</h2>
<p>Returns an the last error if multiple errors.</p>

<pre class="brush: php">
if ($A != $B)
{
	$this->validator->catch_error('my_field', 'A does not equal B');
}

echo $this->validator->get_last_error(); // 'A does not equal B'
</pre>


<h2>$this->validator->get_error(<var>'key'</var>)</h2>
<p>Returns a specific error message based on the <dfn>$key</dfn> parameter.</p>

<pre class="brush: php">
if ($A != $B)
{
	$this->validator->catch_error('my_field', 'A does not equal B');
}

echo $this->validator->get_errors('my_field'); // A does not equal B
</pre>


<h2>$this->validator->fields()</h2>
<p>Returns an array of fields and rules assigned to those fields.</p>

<pre class="brush: php">
$fields = $this->validator->fields();
foreach($fields as $key => $field)
{
	foreach($field as $rule)
	{
		echo $rule->msg; // A does not equal B
	}
}
</pre>

<h2>$this->validator->reset([<var>reset_fields</var>])</h2>
<p>Resets the rules and errors of a Validator object. The optional <dfn>reset_fields</dfn> parameter will reset the fields to validate. The default value for <dfn>reset_fields</dfn> is <dfn>TRUE</dfn> which empties all the fields set by <dfn>add_rule</dfn> on the object.</p>

<pre class="brush: php">
$posted = $_POST;
$this->validator->add_rule('name', 'required', 'The name field is required', $posted['name']);
$this->validator->add_rule('email', 'valid_email', 'Please enter in a valid email', $posted['email']);

$fields = $this->validator->fields();
echo count($fields); // 2

$this->validator->reset();
$fields = $this->validator->fields();
echo count($fields); // 0
</pre>