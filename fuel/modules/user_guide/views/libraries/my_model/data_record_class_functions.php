<h1>Data Record Class Function Reference</h1>
<p>The Data_record class is the custom record object for the Table Class parent model (MY_Model). 
This provides a greater level of flexibility with your models by allowing you to create not only
methods on your model to retreive records from your datasource, but also the ability to create
derived attributes and lazy load other objects with each record returned.
This class is <strong>optional</strong>. If it it doesn't exist, then the Table Class parent model
will use either a standard generic class or an array depending on the return method specified.
</p>

<p class="important">Most module records in FUEL extend the <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_record</a>
class which has some extended functionality for module records.</p>


<h2>$example_record->is_initialized()</h2>
<p>This method returns either <dfn>TRUE</dfn> or <dfn>FALSE</dfn> depending on if the record class has been properly intialized.</p>

<pre class="brush: php">
$record = $this->examples_model->create(); 
$record->is_initialized(); // Returns TRUE
</pre>

<h2>$example_record->set_fields(<var>fields</var>)</h2>
<p>Sets the fields of the oject ignoring those fields prefixed with an underscore.</p>

<pre class="brush: php">
$fields = array('id', 'name', 'email');
$record = $this->examples_model->set_fields($fields); 
</pre>

<h2>$example_record->id()</h2>
<p>Returns the id field name.</p>

<pre class="brush: php">
$record->id(); // Returns id
</pre>


<h2>$example_record->fill(<var>'values'</var>)</h2>
<p>This method fills the records with values.</p>

<pre class="brush: php">
$record = $this->examples_model->create(); 
$record->fill($_POST);  // Be sure to always clean your $_POST variables before using them
</pre>


<h2>$example_record->values(<var>[include_derived]</var>)</h2>
<p>Returns an array of the record's values. 
If the <dfn>$include_derived</dfn> option is TRUE, then all <dfn>get_</dfn> methods that don't require additional parameters
will also be included.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$values = $record->values()
echo $values['email']; // vader@deathstar.com 
</pre>


<h2>$example_record->duplicate()</h2>
<p>Duplicates the record with all it's current values.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$duplicate_record = $record->duplicate()
echo $duplicate_record->email; // vader@deathstar.com 
</pre>


<h2>$example_record->save(<var>['validate']</var>, <var>['ignore_on_insert']</var>)</h2>
<p>Saves all the properties of the object. 
The <dfn>$validate</dfn> parameter is set to <dfn>TRUE</dfn> by default and will validate the data before saving.
The <dfn>$ignore_on_insert</dfn> is set to <dfn>TRUE</dfn> by default and will update any records with the same key information.
Saving the record calls the parent model's save method passing it's own record values to be saved.
</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->email = 'hsolo@milleniumfalcon.com';
$record->save();
</pre>


<h2>$example_record->validate()</h2>
<p>Validates the values of the object to makes sure they are valid to be saved.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->email = 'hsolomilleniumfalcon.com'; // note the invalid email address
if ($record->validate()) 
{ 
    echo 'VALID'; 
} 
else 
{ 
    echo 'Please fill out a valid email address'; 
} 
</pre>


<h2>$example_record->is_valid()</h2>
<p>Returns <dfn>TRUE</dfn> or <dfn>FALSE</dfn> depending on if validation has been run and is valid.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->email = 'hsolomilleniumfalcon.com'; // note the invalid email address
$record->validate();

... other code ...

if ($record->is_valid()) 
{ 
    echo 'VALID'; 
} 
else 
{ 
    echo 'Please fill out a valid email address'; 
} 
</pre>

<p class="important">The validate <strong>method</strong> must be called before calling <strong>is_valid</strong>.</p>


<h2>$example_record->errors()</h2>
<p>Returns an array of error messages if there were any found after validating. 
This is commonly called after saving because validation will occur automatically on save.
</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->email = 'hsolomilleniumfalcon.com'; // note the invalid email address
if (!$record->save())
{ 
    foreach($record->errors as $error)
    {
        echo $error;	
    }
} 
</pre>


<h2>$example_record->delete()</h2>
<p>Deletes the record. Similar to the save method, it will call the parent model's delete method passing itself as the where condition to delete.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->delete(); // note the invalid email address
</pre>


<h2>$example_record->refresh()</h2>
<p>Refreshes the object from the data source.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->email = 'hsolo@milleniumfalcon.com';
$record->refresh();
echo $record->email; // dvader@deathstar.com
</pre>


<h2>$example_record->lazy_load(<var>where</var>, <var>'model'</var>, <var>[multiple]</var>, <var>['cache_key']</var>)</h2>
<p>Lazy load will load another model's record object and is often used in custom derived attributes.
You can pass a key =&gt; $value array as the <dfn>$model</dfn> parameter with the key representing the $module to load the <dfn>$model</dfn> from.
To return mulitple record object set the <dfn>$multiple</dfn> to <dfn>TRUE</dfn>.
The <dfn>$cache_key</dfn> is the records property to store the value. Normally you will not need to worry about this value
because one is created by default.
</p>

<pre class="brush: php">
function get_spaceship()
{
    $ship = $this->lazy_load(array('email' => 'hsolo@milleniumfalcon.com'), 'spacehips_model', FALSE);
    return $ship;
}
</pre>


<h2>$example_record->prop_exists(<var>'prop'</var>)</h2>
<p>Tests whether a property exists on the record.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
if ($record->prop_exists('email')))
{
    echo $record->email;
}
</pre>


<h2>$example_record->debug_query()</h2>
<p>Prints to the screen the last query run by the parent model. An alias to the parent model's debug_query method.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->debug_query()))
</pre>


<h2>$example_record->debug_data()</h2>
<p>Prints to the screen the property values of the of the object.</p>

<pre class="brush: php">
$record = $this->examples_model->find_one(array('email' => 'dvader@deathstar.com')); 
$record->debug_data()))
</pre>