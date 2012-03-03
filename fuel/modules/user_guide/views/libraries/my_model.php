<h1>MY_Model Abstract Class</h1>
<p>MY_Model extends the Model class to provide <a href="<?=user_guide_url('libraries/my_model/table_class_functions')?>">common methods</a> for retrieving and manipulating data from the database usually specific to a table.</p>
<p>It was developed to augment the CodeIgniter active record class and <strong>DOES NOT</strong> change any of those methods. 
The class is abstract, meaning that it needs to be extended to work.</p>

<p class="important">MY_Model may require the following classes and helpers (depending on class preferences):</p>
<ul>
	<li><a href="<?=user_guide_url('libraries/validator')?>">Validator Class</a></li>
	<li><a href="<?=user_guide_url('helpers/my_string_helper')?>">String Helper</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper')?>">Date Helper</a></li>
	<li><a href="http://codeigniter.com/user_guide/helpers/security_helper.html">Security Helper</a></li>
</ul>

<h2>Table, Result and Record Classes</h2>
<p>MY_Model is actually made up of 2-3 classes:</p>
<ul>
	<li><a href="<?=user_guide_url('libraries/my_model/table_class_functions')?>"><strong>Table Class</strong> - in charge of retrieving, validating and saving data to the data source</a></li>
	<li><a href="<?=user_guide_url('libraries/my_model/data_set_class_functions')?>"><strong>Data Set Class</strong> - the result object returned by the table class after retrieving data (normally not directly used)</a></li>
	<li><a href="<?=user_guide_url('libraries/my_model/data_record_class_functions')?>"><strong>Record Class (optional)</strong> - the custom object(s) returned by a retrieving query that contains at a minimum the column attibutes of the table</a></li>
</ul>


<h2>Initializing the Class</h2>

<p>The MY_Model class is initialized in your controller using the <dfn>$this->load->model</dfn> function:</p>

<pre class="brush: php">$this->load->model('examples_model');</pre>
<p>Once loaded, the MY_Model object will be available using: <dfn>$this->examples_model</dfn>.</p>

<h2>Configuring MY_Model Information</h2>

<h3>Public Properties</h3>
<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>return_method</strong></td>
			<td>auto</td>
			<td>object, array, query, auto</td>
			<td>Return the results as either a query result, array or an object with the later being custom if there is a Data_record class assigned to the table</td>
		</tr>
		<tr>
			<td><strong>auto_validate</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Automatically validate fields</td>
		</tr>
		<tr>
			<td><strong>auto_validate_fields</strong></td>
			<td><pre>array(
'email|email_address' => 'valid_email',
'phone|phone_number' => 'valid_phone'
)</pre></td>
			<td>None</td>
			<td>Fields to auto validate</td>
		</tr>
		<tr>
			<td><strong>default_required_message</strong></td>
			<td>Please fill out the required field '%1s'</td>
			<td>None</td>
			<td>The default required message</td>
		</tr>
		<tr>
			<td><strong>auto_date_add</strong></td>
			<td><pre>array('date_added', 'entry_date')</pre></td>
			<td>None</td>
			<td>Field names to automatically generate the datetime on insert</td>
		</tr>
		<tr>
			<td><strong>auto_date_update</strong></td>
			<td><pre>array('last_modified', 'last_updated')</pre></td>
			<td>None</td>
			<td>Field names to automatically generate the datetime on update</td>
		</tr>
		<tr>
			<td><strong>date_use_gmt</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Use GMT time</td>
		</tr>
		<tr>
			<td><strong>default_date</strong></td>
			<td>0000-00-00</td>
			<td>None</td>
			<td>The default date to be inserted</td>
		</tr>
		<tr>
			<td><strong>auto_trim</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean), string or an array of field names</td>
			<td>Automatically trim the spaces from data values before inserting</td>
		</tr>
		<tr>
			<td><strong>auto_encode_entities</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean), string or an array of field names</td>
			<td>Automatically encode HTML entities</td>
		</tr>
		<tr>
			<td><strong>xss_clean</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean), string or an array of field names</td>
			<td>Automatically run the xss_clean function on fields</td>
		</tr>
		<tr>
			<td><strong>hidden_fields</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Hidden fields that should appear by default when generating the models form</td>
		</tr>
		<tr>
			<td><strong>parsed_fields</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Fields to automatically parse.</td>
		</tr>
		<tr>
			<td><strong>unique_fields</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Fields that are not IDs but are unique.</td>
		</tr>
		<tr>
			<td><strong>linked_fields</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Fields that are are linked. Key is the field, value is a function name to transform it.</td>
		</tr>
		<tr>
			<td><strong>foreign_keys</strong></td>
			<td>None</td>
			<td>Array of column names with keys being the column and the value being the model. If the model exists
			in a specific module, then use an array value with the key being the module.</td>
			<td>Maps foreign keys in other models </td>
		</tr>
		<tr>
			<td><strong>readonly</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Sets the model to readonly mode and will not allow inserts, updates and deletes</td>
		</tr>
	</tbody>
</table>

<h3>Protected Properties</h3>
<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>table_name</strong></td>
			<td>Will try and derive from class name minus the suffix</td>
			<td>None</td>
			<td>The name of the table</td>
		</tr>
		<tr>
			<td><strong>key_field</strong></td>
			<td>id</td>
			<td>None</td>
			<td>The primary key field</td>
		</tr>
		<tr>
			<td><strong>normalized_save_data</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The original normalized data before it is cleaned for saving</td>
		</tr>
		<tr>
			<td><strong>cleaned_data</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The data after it has been cleaned for saving</td>
		</tr>
		<tr>
			<td><strong>dsn</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>An alternative DSN value for connection</td>
		</tr>
		<tr>
			<td><strong>has_auto_increment</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Uses auto_increment for table IDs</td>
		</tr>
		<tr>
			<td><strong>suffix</strong></td>
			<td>_model</td>
			<td>None</td>
			<td>The Data_record suffix before</td>
		</tr>
		<tr>
			<td><strong>record_class</strong></td>
			<td>Will default to the singular version of the Table Class (e.g. <dfn>examples_model</dfn> would have default record class of <dfn>example_model</dfn>)</td>
			<td>None</td>
			<td>The name of the Data_record class</td>
		</tr>
		<tr>
			<td><strong>rules</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>Validation rules</td>
		</tr>
		<tr>
			<td><strong>required</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>Required fields</td>
		</tr>
		<tr>
			<td><strong>fields</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>Table fields</td>
		</tr>
		<tr>
			<td><strong>use_common_query</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Use the common query method for query</td>
		</tr>
		<tr>
			<td><strong>validator</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The validator object used for validation</td>
		</tr>
	</tbody>
</table>

<h2>Extending MY_Model</h2>
<p>When extending MY_Model, it is recommended to use a plural version of the objects name, in
this example <strong>Examples</strong>, with the suffix of <strong>_model</strong> (e.g.<kbd>Examples_model</kbd>). 
The plural version is recommended because the singular version is often used for the custom record class (see below for more). 
</p>

<p>The <strong>__construct</strong> method requires at least the name of the table to map to in the first  argument and then you can optionally
set other properties in the second constructor argument (see above for additional properties).
</p>
<pre class="brush: php">
class Examples_model extends MY_Model {

    function __construct()
    {
        parent::__construct('example', array('required' => 'name')); // table name, initialization params
    }
}
</pre>
<p class="important">Most modules in FUEL extend the <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> class,
a child of <dfn>MY_Model</dfn>, but has some extended functionality needed for modules.</p>


<p>You can also define any of the class properties listed above like so:</p>
<pre class="brush: php">
class Examples_model extends MY_Model {

    public $required = array('name');
    public $record_class = 'Example_record';

    function __construct()
    {
        parent::__construct('example'); //table name
    }
}
</pre>

<h2>Custom Record Objects</h2>
<p>MY_Model adds another layer of control for your models by allowing you to define custom return objects from active record. 
This gives you more flexibility at the record level for your models. With custom record objects you can:</p>
<ul>
	<li><strong>Create Derived Attributes</strong></li>
	<li><strong>Lazy Load Other Objects</strong></li>
	<li><strong>Manipulate and Save at the Record Level</strong></li>
</ul>
<p>When creating a custom record class, it is recommended to use the singular version of the parent model class (so parent models should be plural).</p>

<pre class="brush: php">
class Examples_model extends MY_Model {

    public $required = array('name');

    function __construct()
    {
        parent::__construct('example'); //table name
    }
}


class Example_model extends MY_Model {

    Custom record model methods go here....

}
</pre>
<p class="important">Custom record objects are not required. MY_Model will intelligently try and figure out the class name if one is not defined in the parent table model.
If the class is not found it will return an <strong>array of arrays</strong> or an <strong>array of standard objects</strong> depending on the <dfn>return_method</dfn> property of the parent model class.
<kbd>Custom record objects must be defined in the same file that the parent table model is defined.</kbd>
</p>




<h3>Create Derived Attributes</h3>
<p>With a custom record object, you can derive attributes which means you can create new values from the existing fields or even
other models. For example, you have a table with a text field named <dfn>content</dfn> that you need to filter and encode html entities 
and sometimes strip images before displaying on your webpage. Instead of applying the <dfn>htmlentities</dfn> and <dfn>strip_image_tags</dfn> 
function each time it is displayed, you can create a new derived attribute on your custom record object like so:
</p>
<pre class="brush: php">
function get_content_formatted($strip_images = FALSE)
{
    $CI =& get_instance();
    if ($strip_images)
    {
        $CI->load->helper('security');
        $content = strip_image_tags($this->content);
    }
    $content = htmlentities($this->content);
    return $content;
}
</pre>

<h3>Lazy Load Other Objects</h3>
<p>Lazy loading of object is used when you don't want the overhead of queries to generate sub objects. For example, if you have a book, model
which has a foreign key to an author model, you could create a method on the record class to lazy load that object like this:</p>

<pre class="brush: php">
function get_spaceship()
{
    $ship = $this->lazy_load(array('email' => 'hsolo@milleniumfalcon.com'), 'spacehips_model', FALSE);
    return $ship;
}
</pre>

<h3>Manipulate and Save at the Record Level</h3>
<p>With custom record objects, you can update attributes and save the record object like so:</p>
<pre class="brush: php">
$foo = $this->examples_model->find_by_key(1);
$foo->bar = 'This is a test';
$foo->save();
</pre>

<p class="important"><a href="<?=user_guide_url('libraries/my_model/data_record_class_functions')?>">Click here to view the function reference for custom record objects</a></p>


<h2>Magic Methods</h2>
<p>MY_Model uses PHP's magic methods extensively. This allows for dynamically creating methods that aren't originally defined by the class. 
Magic methods are used both in the table and custom record classes. In custom record classes, any method prefixed with <dfn>get_</dfn>
can also be syntactically written to look like an instance property:

<pre class="brush: php">
$record->get_content()

// can also be written as...
$record->content
</pre>

In the table class, magic methods are used to find records in interesting ways. For example, you can do something like this (where <var>{}</var> enclose areas where the developer should change to a proper field name):</p>
<pre class="brush: php">
// to find multiple items 
$this->examples_model->find_all_by_{column1}_and_{column2}('column1_val', 'column2_val');

// to find one item 
$this->examples_model->find_one_by_{column1}_or_{column2}('column1_val', 'column2_val');
</pre>

<h2>Working with Active Record</h2>
<p>MY_Model works alongside active record like so:</p>
<pre class="brush: php">
...
$this->db->where(array('published' => 'yes'))
$this->db->find_all()

// Is the same as...
$this->db->find_all(array('published' => 'yes'))

</pre>

<a name="hooks"></a>
<h2>Hooks</h2>
<p>MY_Model provides hooks that you can overwrite with your own custom code to extend the functionality of your model.</p>
<p>Table class hooks</p>
<ul>
	<li><strong>on_before_clean</strong> - executed right before cleaning of values</li>
	<li><strong>on_before_validate</strong> - executed right before validate of values</li>
	<li><strong>on_before_insert</strong> - executed before inserting values</li>
	<li><strong>on_after_insert</strong> - executed after insertion</li>
	<li><strong>on_before_update</strong> - executed before updating</li>
	<li><strong>on_after_update</strong> - executed after updating</li>
	<li><strong>on_before_save</strong> - executed before saving</li>
	<li><strong>on_after_save</strong> - executed after saving</li>
	<li><strong>on_before_delete</strong> - executed before deleting</li>
	<li><strong>on_after_delete</strong> - executed after deleting</li>
	<li><strong>on_before_post</strong> - to be called from within your own code right before processing post data.</li>
	<li><strong>on_after_post</strong> - to be called from within your own code after posting data.</li>
</ul>

<p>Record class hooks</p>
<ul>
	<li><strong>before_set</strong> - executed before setting a value</li>
	<li><strong>after_get</strong> - executed after setting a value</li>
	<li><strong>on_insert</strong> - executed after inserting</li>
	<li><strong>on_update</strong> - executed after updating</li>
	<li><strong>on_init</strong> - executed upon initialization</li>
</ul>








