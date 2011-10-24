<?php fuel_set_var('sections', array('MY_Model')); ?>
<h1>Table Class Function Reference</h1>
<p>The table class has a number of methods to retrieve, save, delete and validate data with a table.</p>

<h2>$this->examples_model->db()</h2>
<p>Returns the table database object.</p>
<pre class="brush: php">
$db = $this->examples_model->db(); 
</pre>


<h2>$this->examples_model->short_name()</h2>
<p>Returns the name of the model minus the suffix. An optional parameter will lower case it</p>
<pre class="brush: php">
echo $this->examples_model->short_name(TRUE); 
// examples
</pre>


<h2>$this->examples_model->table_name()</h2>
<p>Returns the table name.</p>
<pre class="brush: php">
echo $this->examples_model->table_name(); 
// examples
</pre>


<h2>$this->examples_model->key_field()</h2>
<p>Returns the primary key or keys of the table. An array can be used if the table contains a compound key.</p>
<pre class="brush: php">
echo $this->examples_model->key_field(); 
// id
</pre>


<h2>$this->examples_model->fields()</h2>
<p>Returns an array of the fields of the table.</p>
<pre class="brush: php">
$fields = $this->examples_model->fields(); 
foreach($fields as $field)
{
	echo $field; // field name
}
</pre>


<h2>$this->examples_model->get(<var>force_array</var>, <var>['return_method']</var>, <var>'assoc_key'</var>)</h2>
<p>This is the workhorse method that many other methods on the class will use to retrieve data. The first parameter says whether
to return either one or many. The <dfn>return_method</dfn> determines if it is either an array of arrays or an array of objects.
The method returns the result class and you must call the <kbd>result</kbd> method on it to get the data.
</p>
<pre class="brush: php">
$rows = $this->examples_model->get(TRUE, 'object', FALSE); 
foreach($rows->result() as $row)
{
    echo $row->name;
}
</pre>
<p>The third parameter is the column name to be used as the array key value (if <dfn>$force_array</dfn> is set to <dfn>TRUE</dfn>)</p>
<pre class="brush: php">
$rows = $this->examples_model->get(TRUE, 'object', 'id'); 
foreach($rows->result() as $id => $row)
{
    echo $id;
}
</pre>

<h2>$this->examples_model->map_query_records(<var>query</var>, <var>['assoc_key']</var>)</h2>
<p>Maps a query result object to an array of record objects. The second parameter is optional and if used, should contain the name of the field you want 
to use for the returned array's key value.</p>
<pre class="brush: php">
...
$query = $this->db->query('SELECT * FROM USERS');
$users = $this->examples_model->map_query_records($query, 'id');
foreach($users as $id => $user)
{
    echo $user->name;
}

</pre>

<h2>$this->examples_model->map_to_record_class(<var>row</var>, <var>[fields]</var>)</h2>
<p>Maps an associative record array to the models custom record object. The second  parameter is optional and specifies all the field names the record can contain however, 
the values of those fields are determined by the <dfn>row</dfn> value passed. If no <dfn>fields</dfn> parameter is passed, then the key values of the $row, will be used.</p>
<pre class="brush: php">
$my_user['id'] = 1;
$my_user['name'] = 'Darth Vader';
$my_user['email'] = 'darth@deathstar.com';
$my_custom_record = $this->examples_model->map_to_record_class($my_user); 
echo $my_custom_record->name;
</pre>

<h2>$this->examples_model->find_by_key(<var>key_val</var>, <var>['return_method']</var>)</h2>
<p>Finds a single record based on the tables key value(s). The second  parameter is optional and specifies if the object
should be returned as an array or an object.</p>
<pre class="brush: php">
$this->examples_model->find_by_key(TRUE, 'object', FALSE); 
</pre>


<h2>$this->examples_model->find_one(<var>[where]</var>, <var>['order_by']</var>, <var>['return_method']</var>)</h2>
<p>Finds a single record based on the where parameters passed to the method. The second parameter will sort the values before 
returning the single record. The <dfn>return_method</dfn> determines if it is either an array of arrays or an array of objects.</p>
<pre class="brush: php">
$this->examples_model->find_one(array('published' => 'yes'), 'date_added desc', 'object'); 
</pre>


<h2>$this->examples_model->find_one_array(<var>where</var>, <var>['order_by']</var>)</h2>
<p>Finds a single record based on the where parameters which can be passed as either an array or string. 
The returned value is an array. 
Any custom methods or properties on the record class (if used), will not be available.
The second parameter will sort the values before returning the single record.</p>
<pre class="brush: php">
$this->examples_model->find_one_array(array('published' => 'yes'), 'date_added desc'); 
</pre>


<h2>$this->examples_model->find_all(<var>where</var>, <var>['order_by']</var>, <var>[limit]</var>, <var>[offset]</var>, <var>['return_method']</var>, <var>['assoc_key']</var>)</h2>
<p>Finds multiple records based on the where parameters which can be passed as either an array or string.  The second parameter will sort the values before 
returning the single record. The <dfn>limit</dfn> paramter limits the returned results. The <dfn>offset</dfn> parameter will offset the returned results (e.g. like for pagination).
The <dfn>return_method</dfn> determines if it is either an array of arrays or an array of objects.
The <dfn>assoc_key</dfn> will fill the key value of the array with the value of a column instead of the normal 0 index
</p>
<pre class="brush: php">
$this->examples_model->find_all(array('published' => 'yes'), 'date_added desc'); 
</pre>


<h2>$this->examples_model->find_all_array(<var>where</var>, <var>['order_by']</var>, <var>[limit]</var>, <var>[offset]</var>, <var>['assoc_key']</var>)</h2>
<p>Finds multiple records based on the where parameters which can be passed as either an array or string. 
The returned value is an array of array values (mulit-dimensional array). 
Any custom methods or properties on the record class (if used), will not be available.
The second parameter will sort the values before returning the single record. 
The <dfn>limit</dfn> paramter limits the returned results. 
The <dfn>offset</dfn> parameter will offset the returned results (e.g. like for pagination).
The <dfn>assoc_key</dfn> will fill the key value of the array with the value of a column instead of the normal 0 index.
</p>
<pre class="brush: php">
$this->examples_model->find_all_array(array('published' => 'yes'), 'date_added desc'); 
</pre>


<h2>$this->examples_model->find_all_assoc(<var>'assoc_key'</var>, <var>where</var>, <var>['order_by']</var>, <var>[limit]</var>, <var>[offset]</var>, <var>['assoc_key']</var>)</h2>
<p>Finds multiple records based on the where parameters which can be passed as either an array or string. 
The first parameter, <dfn>assoc_key</dfn>, will fill the key value of the array with the value of a column instead of the normal 0 index.
The <dfn>order_by</dfn> parameter will sort the values before returning the single record. 
returning the single record. The <dfn>limit</dfn> paramter limits the returned results. The <dfn>offset</dfn> parameter will offset the returned results (e.g. like for pagination).
</p>
<pre class="brush: php">
$this->examples_model->find_all_assoc(array('published' => 'yes'), 'date_added desc'); 
</pre>


<h2>$this->examples_model->find_all_array_assoc(<var>'assoc_key'</var>, <var>where</var>, <var>['order_by']</var>, <var>[limit]</var>, <var>[offset]</var>)</h2>
<p>Finds multiple records based on the where parameters which can be passed as either an array or string. 
The returned value is an array of array values (mulit-dimensional array). 
Any custom methods or properties on the record class (if used), will not be available.
The first parameter, <dfn>assoc_key</dfn>, will fill the key value of the array with the value of a column instead of the normal 0 index.
The <dfn>order_by</dfn> parameter will sort the values before returning the single record. 
returning the single record. The <dfn>limit</dfn> paramter limits the returned results. The <dfn>offset</dfn> parameter will offset the returned results (e.g. like for pagination).
</p>
<pre class="brush: php">
$this->examples_model->find_all_assoc(array('published' => 'yes'), 'date_added desc'); 
</pre>


<h2>$this->examples_model->query(<var>params</var>)</h2>
<p>This method takes an associative array with the key values that map to CodeIgniter active record methods. 
Below are the key values you can pass:
</p>
<ul>
	<li><strong>select</strong></li>
	<li><strong>from</strong></li>
	<li><strong>join</strong></li>
	<li><strong>where</strong></li>
	<li><strong>or_where</strong></li>
	<li><strong>where_in</strong></li>
	<li><strong>or_where_in</strong></li>
	<li><strong>where_not_in</strong></li>
	<li><strong>or_where_not_in</strong></li>
	<li><strong>like</strong></li>
	<li><strong>or_like</strong></li>
	<li><strong>not_like</strong></li>
	<li><strong>or_not_like</strong></li>
	<li><strong>group_by</strong></li>
	<li><strong>order_by</strong></li>
	<li><strong>limit</strong></li>
	<li><strong>offset</strong></li>
</ul>

<p>This method returns a query result object.</p>
<pre class="brush: php">
$where['select'] = 'id, name, published';
$where['where'] = array('published' => 'yes');
$where['order_by'] = 'name asc';
$where['limit'] = 10;

$query = $this->examples_model->query($where); 
$results = $query->result(); 
</pre>

<a name="options_list"></a>
<h2>$this->examples_model->options_list(<var>['key']</var>, <var>['val']</var>, <var>[where]</var>, <var>['order']</var>)</h2>
<p>The options_list method is predominately used with form select option lists. 
It returns an associative array.
The first parameter is the the column to be used as the key of the associative array. If not provided, the first column is used.
The second parameter is the column to be used as the label.If not provided, the second column is used.
The <dfn>$where</dfn> and <dfn>$order</dfn> parameter are the where and ordering parameters for the query.
</p>
<pre class="brush: php">
$where['published'] = 'yes'; 
$order = 'name, desc'; 
 
$this->examples_model->options_list('id', 'name', $where, $order); 
</pre>


<h2>$this->examples_model->record_exists(<var>where</var>)</h2>
<p>The record_exists method determines if a record exists based on the <dfn>$where</dfn> condition.
</p>
<pre class="brush: php">
$where['type'] = 'A'; 
 
$this->examples_model->record_exists($where); 
</pre>


<h2>$this->examples_model->create(<var>['values']</var>)</h2>
<p>If a custom record object exists, the <dfn>create</dfn> method will create an empty record object.
Passing the <dfn>$values</dfn> to the method will fill objects field values.
</p>
<pre class="brush: php">
$this->examples_model->create($_POST); // Be sure to always clean your $_POST variables before using them
</pre>


<h2>$this->examples_model->clean(<var>'values'</var>)</h2>
<p>The clean method will clean the values passed to it <strong>before</strong> saving. It will format date fields based on the
<dfn>auto_date_add</dfn> and <dfn>auto_date_update</dfn> model properties. It will also encode entities if the auto_encode_entities
property is set on the model.
</p>
<pre class="brush: php">
$this->examples_model->clean($_POST); // Be sure to always clean your $_POST variables before using them
</pre>


<h2>$this->examples_model->cleaned_data()</h2>
<p>Returns the data cleaned.</p>
<pre class="brush: php">
$this->examples_model->cleaned_data($_POST); // Be sure to always clean your $_POST variables before using them
</pre>


<h2>$this->examples_model->record_count(<var>where</var>)</h2>
<p>Returns the number of records based on the where condition.</p>
<pre class="brush: php">
$where['published'] = 'yes'; 

$this->examples_model->record_count($where);
</pre>


<h2>$this->examples_model->total_record_count()</h2>
<p>Returns the <strong>total</strong> number of records in a table.</p>
<pre class="brush: php">
$this->examples_model->total_record_count();
</pre>


<h2>$this->examples_model->paginate(<var>'per_page'</var>, <var>['offset']</var>)</h2>
<p>Returns a an array of records based on the limit (<strong>$per_page</strong>) and <var>'offset'</var> parameters of the method.</p>
<pre class="brush: php">
$this->examples_model->paginate(20, 0);
</pre>


<h2>$this->examples_model->save(<var>'record'</var>, <var>['validate']</var>, <var>['ignore_on_insert']</var>)</h2>
<p>Saves record object, or array of data to the database. 
The <dfn>$validate</dfn> parameter will validate the data before saving if set to <dfn>TRUE</dfn> (default behavior).
The <dfn>$ignore_on_insert</dfn> if set to <dfn>TRUE</dfn> (default behavior), will update data records if a key value in the table already exists.
</p>
<pre class="brush: php">
$this->examples_model->save($_POST, TRUE, TRUE); // Be sure to always clean your $_POST variables before using them
</pre>
<p>There are a number of hooks that allow you to perform various actions during the saving process. 
<a href="<?=user_guide_url('libraries/my_model#hooks')?>">Click here to view the hooks</a>.
</p>

<h2>$this->examples_model->save_related(<var>'model'</var>, <var>key_field</var>, <var>data</var>)</h2>
<p>Saves related data to a many to many table. To be used in on_after_save hook. 
The <dfn>key_field</dfn> parameter is an associative array with the key being the name of the field and the value being the value to save.
The <dfn>key_field</dfn> also acts as where condition on the delete query that is used to clear out all data before saving.
The <dfn>data</dfn> parameter is an associative array as well with the key being the name of the other field in which to save and the value is the data to iterate over and save.
</p>
<pre class="brush: php">
$this->examples_model->save_related('examples_to_categories', array('example_id' => $obj->id), array('categories_id' => $_POST['categories']));
</pre>

<h2>$this->examples_model->insert(<var>'values'</var>)</h2>
<p>Performs a simple insert to the database record. 
Does <srong>NOT</srong> perform any validation but <strong>DOES</strong> execute 
<dfn>before</dfn> and <dfn>after</dfn> insert hooks like the save method.
</p>
<pre class="brush: php">
$values['name'] = 'Darth Vader'; 
$values['email'] = 'dvader@deathstar.com'; 

$this->examples_model->insert($values);
</pre>

<h2>$this->examples_model->update(<var>'values'</var>, <var>where</var>)</h2>
<p>Performs a simple update to the database record based on the <dfn>$where</dfn> condition passed. 
Does <srong>NOT</srong> perform any validation but <strong>DOES</strong> execute 
<dfn>before</dfn> and <dfn>after</dfn> update hooks like the save method.
</p>
<pre class="brush: php">
$where['id'] = 1;

$this->examples_model->update($_POST, $where); // Be sure to always clean your $_POST variables before using them
</pre>


<h2>$this->examples_model->delete(<var>where</var>)</h2>
<p>Deletes database record(s) based on the <dfn>$where</dfn> condition passed. Does execute <dfn>before</dfn> and <dfn>after</dfn> delete hooks.</p>
<pre class="brush: php">
$where['id'] = 1; 

$this->examples_model->delete($where);
</pre>

<h2>$this->examples_model->delete_related(<var>'model'</var>, <var>'key_field'</var>, <var>where</var>)</h2>
<p>Delete related data. To be used in on_before/on_after delete hooks.</p>
<pre class="brush: php">
$obj = $this->examples_model->create();
$obj->name = 'Darth Vader';
$obj->save();
$this->examples_model->delete_related('examples_to_categories', 'example_id', $obj);
</pre>


<h2>$this->examples_model->truncate()</h2>
<p>Truncates the data in the table.</p>
<pre class="brush: php">
$this->examples_model->truncate();
</pre>


<h2>$this->examples_model->is_new(<var>'val'</var>, <var>'key'</var>)</h2>
<p>Creates a where condition and queries the model's table to see if the column (<dfn>$key</dfn>) already contains the <dfn>$val</dfn> value.
Usually used for validation to check if a unique key already exists.
</p>
<pre class="brush: php">
$this->examples_model->is_editable('location', 'id');
</pre>


<h2>$this->examples_model->is_editable(<var>'val'</var>, <var>'key'</var>, <var>'id'</var>)</h2>
<p>Creates a where condition and queries the model's table to see if the column (<dfn>$key</dfn>) already contains the <dfn>$val</dfn> value but compares it to the tables key column with the <dfn>$id</dfn> value.

Usually used for validation. The example below uses this method to validate on the model before saving.
</p>
<pre class="brush: php">
$this->examples_model->is_editable('location', 'id', 1);
</pre>

<p>The <dfn>is_new</dfn> and <dfn>is_editable</dfn> methods are usually used during the models validation process like so:</p>
<pre class="brush: php">
function on_before_validate($values) 
{ 
    if (!empty($values['id'])) 
    { 
        $this->add_validation('location', array(&$this, 'is_editable'), 'The location value already exists.' , array('location', $values['id'])); 
    } 
    else 
    { 
        $this->add_validation('location', array(&$this, 'is_new'), 'The location value already exists.', 'location'); 
    } 
    return $values; 
} 
</pre>


<h2>$this->examples_model->validate(<var>'record'</var>)</h2>
<p>Can pass either a custom record object or an associative array of table column values.
Will run all validation rules, including required and autovalidation (if auto_validation is set on the model)
and return <dfn>TRUE</dfn> if it passes validation and <dfn>FALSE</dfn> if it fails.
</p>

<p>Using an array of values:</p>
<pre class="brush: php">
$values['name'] = 'Mr. Jones';
$values['email'] = 'jones@example.com';
$values['active'] = 'yes';

$this->examples_model->validate($values);
</pre>

<p>Using a custom record:</p>
<pre class="brush: php">
$example = $this->examples_model->create();
$example->name = 'Mr. Jones';
$example->email = 'jones@example.com';
$example->active = 'yes';

$this->examples_model->validate($example);

// you can also just call the validate method on the custom record object
$example->validate();
</pre>


<h2>$this->examples_model->field_type(<var>'field'</var>)</h2>
<p>Returns a field type of <dfn>string</dfn>, <dfn>number</dfn>, 
<dfn>date</dfn>, <dfn>datetime</dfn>, <dfn>time</dfn>, <dfn>blob</dfn>, <dfn>enum</dfn>
</p>

<pre class="brush: php">
$type = $this->examples_model->field_type('email');
echo $type; // string
</pre>


<h2>$this->examples_model->is_valid()</h2>
<p>Returns a boolean value if all validation rules that have been run have passed.
</p>

<p>Using an array of values:</p>
<pre class="brush: php">
$values['name'] = 'Mr. Jones';
$values['email'] = 'jones@example.com';
$values['active'] = 'yes';

$this->examples_model->validate($values);
if ($this->examples_model->is_valid($values))
{
    echo 'We are valid!';
}
</pre>


<h2>$this->examples_model->add_validation(<var>'field'</var>, <var>'rule'</var>, <var>'msg'</var>, <var>['rule_params']</var>)</h2>
<p>Adds a validation rule to the model. 
The <dfn>$field</dfn> parameter is the the field name you want to assign the rule to.
The <dfn>$rule</dfn> parameter is the function to be executed for the rule. The function <strong>must</strong> return a boolean.
The <dfn>$msg</dfn> is the error message to be displayed upon an error.
The optional <var>'rule_params'</var> are passed as extra arguments to the validation rule if they are required. You may create your
own functions or use one of the existing functions from the <a href="<?=user_guide_url('helpers/validator_helper')?>">validator_helper</a>.
</p>

<pre class="brush: php">
$this->examples_model->add_validation('email', 'valid_email', 'The email is invalid.', 'jones@example.com'); 
</pre>


<h2>$this->examples_model->add_error(<var>'msg'</var>, <var>['key']</var>)</h2>
<p>Adds an error message to the models validation object. 
The optional <dfn>$key</dfn> argument is used as an identifier of the error message.
</p>

<pre class="brush: php">
$this->examples_model->add_error('There was an error in processing the data.', 'my_key'); 
</pre>


<h2>$this->examples_model->get_validation()</h2>
<p>Returns all the models validation object. The validation object is the same from the <a href="<?=user_guide_url('libraries/validator')?>">Validator library class</a>.</p>

<pre class="brush: php">
$validation = $this->examples_model->get_validation(); 
if ($validation->is_valid())
{
    echo 'YEAH!'; 
}
</pre>

<h2>$this->examples_model->register_to_global_errors(<var>register</var>)</h2>
<p>Sets the validation to register to the global scope.</p>

<pre class="brush: php">
$this->examples_model->register_to_global_errors(TRUE); 

...// code goes here

$errors = get_errors(); // validator_helper function to get global errors
foreach($errors as $error) 
{ 
    echo $error; 
    // There was an error in processing your data 
} 
</pre>


<h2>$this->examples_model->get_errors()</h2>
<p>Returns the errors found in the validator object.</p>

<pre class="brush: php">
$errors = $this->examples_model->get_errors(); 

foreach($errors as $error) 
{ 
    echo $error; 
    // There was an error in processing your data 
} 
</pre>


<h2>$this->examples_model->remove_all_validation()</h2>
<p>Removes all validation rules from the models validator object.</p>

<pre class="brush: php">
$this->examples_model->remove_all_validation(); 

</pre>


<h2>$this->examples_model->field_info(<var>'field'</var>)</h2>
<p>Returns an associative array of a specific field's meta information which includes the following:</p>
<ul>
	<li><strong>name</strong> - the name of the field</li>
	<li><strong>type</strong> - the type of field (e.g. int, varchar, datetime... etc)</li>
	<li><strong>default</strong> - the default value of the field</li>
	<li><strong>options/max_length</strong> - if it is an enum field, then the enum options will be displayed. Otherwise, it will show the max length of the field which may not be relevant for some field types.</li>
	<li><strong>primary_key</strong> - the primary key column</li>
	<li><strong>comment</strong> - the comment</li>
	<li><strong>collation</strong> - the collation method</li>
	<li><strong>extra</strong> - extra field meta information like auto_increment</li>
	<li><strong>null</strong> - a boolean value if the field is <dfn>NULL</dfn> or not </li>
</ul>

<pre class="brush: php">
$field_meta = $this->examples_model->field_info('email'); 

echo $field_meta['name']; // email 
echo $field_meta['type']; // varchar 
</pre>


<h2>$this->examples_model->table_info()</h2>
<p>Returns an associative array of table field meta information with each key representing a table field:</p>

<pre class="brush: php">
$table_meta = $this->examples_model->table_info(); 

echo $table_meta['id']['type']; // int 
echo $table_meta['id']['primary_key']; // 1 (TRUE) 
echo $table_meta['email']['type']; // varchar 
echo $table_meta['first_name']['type']; // varchar 
echo $table_meta['description']['type']; // text 
echo $table_meta['active']['type']; // enum 
print_r($table_meta['active']['options']); // array('yes', 'no') 
</pre>


<h2>$this->examples_model->form_fields(<var>[values]</var>, <var>[related]</var>)</h2>
<p>Somewhat similar to the table_info method with difference being that the returned array has information for creating a form.
The related parameter is used to conveniently map other model information with this form to create a many to many multi-select form element.
This method is usally used with the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class.
</p>

<pre class="brush: php">
$form_info = $this->examples_model->form_fields(); 

echo $form_fields['id']['type']; // hidden 
echo $table_meta['email']['type']; // text 
echo $table_meta['email']['required']; // 1 (TRUE) 
echo $table_meta['first_name']['type']; // text 
echo $table_meta['description']['type']; // textfield 
echo $table_meta['active']['type']; // select or enum 
echo $table_meta['date_added']['type']; // datetime (a special field type in the form_builder class) 
</pre>


<h2>$this->examples_model->record_class_name()</h2>
<p>Returns the custom record class name if it exists. 
If a name does not exist, it will try to intelligently find a class with a singular version of the parent table model's name (e.g. <dfn>examples_model</dfn> = <dfn>example_model</dfn>)
</p>

<pre class="brush: php">
$echo = $this->examples_model->record_class_name(); 
// example_model (note the singular version of the parent table classes name)

</pre>


<h2>$this->examples_model->set_return_method()</h2>
<p>Set's the default return method for methods like <dfn>get()</dfn>, <dfn>find_one</dfn> and <dfn>find_all</dfn>.
Values can be <dfn>object</dfn>, <dfn>array</dfn>, <dfn>query</dfn>, <dfn>auto</dfn>.
</p>

<pre class="brush: php">
$this->examples_model->set_return_method('object'); 
$examples = $this->examples_model->find_all(); 
// an array of custom objects (if a custom object is defined. If not, a standard object will be used) 

$this->examples_model->set_return_method('array'); 
$examples = $this->examples_model->find_all(); 
// an array of associative arrays is returned and will ignore any custom object

</pre>


<h2>$this->examples_model->get_return_method()</h2>
<p>Returns the return method used for querying (<dfn>object</dfn>, <dfn>array</dfn>, <dfn>query</dfn>, <dfn>auto</dfn>).
</p>

<pre class="brush: php">
$return_method = $this->examples_model->get_return_method(); 
// object 

</pre>


<h2>$this->examples_model->debug_data()</h2>
<p>Prints out to the screen the last query results ran by the model.
</p>

<pre class="brush: php">
$this->examples_model->debug_data(); 
// prints out an array of information to the screen

</pre>

<h2>$this->examples_model->debug_query()</h2>
<p>Prints out to the screen the last query SQL ran by the model.
</p>

<pre class="brush: php">
$this->examples_model->debug_query(); 
// prints out the last query run by the model

</pre>


<h2>$this->examples_model->normalize_save_values(<var>'record'</var>)</h2>
<p>Normalizes the saving information into a common array format.
</p>

<pre class="brush: php">
$record = $this->examples_model->create(); 
$record->name = 'John Smith'; 

$values = $this->examples_model->normalize_save_values($record); 
echo $values['name'] = 'John Smith';

</pre>

