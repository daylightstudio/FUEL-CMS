<?php 
$CI->load->library('inspection');
$CI->inspection->initialize(array('file' => FUEL_PATH.'core/MY_Model.php'));
$classes = $CI->inspection->classes();
$my_model = $classes['MY_Model'];
?>

<h1>Models</h1>
<p>There are two main classes FUEL provides for creating models. The first, <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>, 
extends the <a href="http://codeigniter.com/user_guide/general/models.html" target="_blank">CI_Model class</a> to provide <a href="<?=user_guide_url('libraries/my_model')?>">common methods</a> for retrieving and manipulating data from the database usually specific to a table.
It was developed to augment the <a href="http://codeigniter.com/user_guide/database/active_record.html" target="_blank">CodeIgniter active record</a> class and <strong>DOES NOT</strong> change any of those methods.
The second, <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a>, extends <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a> and provides 
<a href="<?=user_guide_url('modules/simple')?>">simple module</a> specific methods and is the class you will most likely want to extend for your models. </p>

<p>Both classes allow you to create <a href="<?=user_guide_url('general/forms')?>">simple to complex form interfaces</a> with the model's <dfn>MY_Model::form_fields()</dfn>
method, which gets passed to the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> object to generate the forms in the CMS.</p>

<p>There's a lot to cover for model's. Below is an overview of the main features but it is recommended that you look at the <a href="#table_result_record">API documentation for both table and record model classes</a> (explained below).</p>
<ul>
	<li><a href="#example">Example</a></li>
	<li><a href="#table_result_record_field">Table, Result, Record and Field Classes</a></li>
	<li><a href="#initializing">Initializing</a></li>
	<li><a href="#extending">Extending</a></li>
	<li><a href="#custom_records">Custom Record Objects</a></li>
	<li><a href="#custom_fields">Custom Field Objects</a></li>
	<li><a href="#magic_methods">Magic Methods</a></li>
	<li><a href="#active_record">Working with Active Record</a></li>
	<li><a href="#relationships">Relationships</a></li>
	<li><a href="#serialized">Serialized Fields</a></li>
	<li><a href="#hooks">Hooks</a></li>
	<li><a href="#formatters">Formatters</a></li>
</ul>

<h2 id="example">Example</h2>
<p>Below is an example of a quotes model (a simple module in the CMS), and the SQL for the table to house the quotes data.</p>

<pre class="brush:sql">
CREATE TABLE `quotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `precedence` int(11) NOT NULL DEFAULT '0',
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
</pre>

<pre class="brush:php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Quotes_model extends Base_module_model {

	public $required = array('content');

	function __construct()
	{
		parent::__construct('quotes'); // table name
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, SUBSTRING(content, 1, 200) as content, name, title, precedence, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function _common_query()
	{
		parent::_common_query(); // to do active and published
		$this->db->order_by('precedence desc');
	}
}

class Quote_model extends Base_module_record {
	
	function get_quote_formatted()
	{
		return quote($this->_fields['content'], $this->name, $this->title);
	}

}
?&gt;
</pre>

<p class="important">For a tutorial on creating simple modules, <a href="<?=user_guide_url('modules/tutorial')?>">click here</a>.</p>


<p class="important">MY_Model requires the following classes and helpers:</p>
<ul>
	<li><a href="<?=user_guide_url('libraries/validator')?>">Validator Class</a></li>
	<li><a href="<?=user_guide_url('helpers/my_string_helper')?>">String Helper</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper')?>">Date Helper</a></li>
	<li><a href="http://codeigniter.com/user_guide/helpers/security_helper.html">Security Helper</a></li>
</ul>

<h2 id="table_result_record_field">Table, Result, Record and Field Classes</h2>
<p>Models are actually made up of 2-3 classes:</p>
<ul>
	<li><a href="<?=user_guide_url('libraries/my_model#my_model')?>"><strong>Table Class</strong> - in charge of retrieving, validating and saving data to the data source</a></li>
	<li><a href="<?=user_guide_url('libraries/my_model#data_set')?>"><strong>Data Set Class</strong> - the result object returned by the table class after retrieving data (normally not directly used)</a></li>
	<li><a href="<?=user_guide_url('libraries/my_model#data_record')?>"><strong>Record Class (optional)</strong> - the custom object(s) returned by a retrieving query that contains at a minimum the column attibutes of the table</a></li>
	<li><a href="<?=user_guide_url('libraries/my_model#data_field')?>"><strong>Field Class (optional)</strong> - the custom object for a specific field. By default, it will simply return the value in the database</a></li>
</ul>


<h2 id="initializing">Initializing the Class</h2>

<p>The Model class is initialized using the <dfn>$this->load->model</dfn> function:</p>

<pre class="brush: php">$this->load->model('examples_model');</pre>
<p>Once loaded, the Model object will be available using: <dfn>$this->examples_model</dfn>.</p>

<h2 id="configuring">Configuring Model Information</h2>
<?php 
$vars['class'] = $my_model;
echo user_guide_block('properties', $vars);
?>


<h2 id="extending">Extending Your Model</h2>
<p>When extending <a href="<?=user_guide_url('libraries/my_model#my_model')?>">MY_Model</a> or <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a>, it is recommended to use a plural version of the objects name, in
this example <strong>Examples</strong>, with the suffix of <strong>_model</strong> (e.g.<dfn>Examples_model</dfn>). 
The plural version is recommended because the singular version is often used for the custom record class (see below for more). 
</p>

<p>The <strong>__construct</strong> method can be passed the name of the table to map to as the first argument and then you can optionally
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

<h2 id="custom_records">Custom Record Objects</h2>
<p>MY_Model adds another layer of control for your models by allowing you to define custom return objects from active record. 
This gives you more flexibility at the record level for your models. With custom record objects you can:</p>
<ul>
	<li><strong>Create Derived Attributes</strong></li>
	<li><strong>Lazy Load Other Objects</strong></li>
	<li><strong>Manipulate and Save at the Record Level</strong></li>
	<li><strong>Automatically Parse Field Values</strong></li>
	<li><strong>Link and Display Related Data</strong></li>
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
<p>Lazy loading of object is used when you don't want the overhead of queries to generate sub objects. For example, if you have a books model
which has a foreign key to an authors model, you could create a method on the record class to lazy load that object like this:</p>

<pre class="brush: php">
function get_author()
{
    $author = $this->lazy_load(array('email' => 'hsolo@milleniumfalcon.com'), 'authors_model', FALSE);
    return $author;
}
</pre>

<h3>Manipulate and Save at the Record Level</h3>
<p>With custom record objects, you can update attributes and save the record object like so:</p>
<pre class="brush: php">
$foo = $this->examples_model->find_by_key(1);
$foo->bar = 'This is a test';
$foo->save();
</pre>

<h3>Automatically Parse Field Values</h3>
<p>In some cases, you may be saving text data that you want to parse the <a href="<?=user_guide_url('general/template-parsing')?>">templating syntax</a> upon retrieval. 
This can be done automatically by setting the <dfn>$parsed_fields</dfn> array property on the table class like so:</p>

<pre class="brush:php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Quotes_model extends Base_module_model {

	public $required = array('content');
	public $parsed_fields = array('content', 'content_formatted');
	
	function __construct()
	{
		parent::__construct('quotes'); // table name
	}

</pre>
<p class="important">Note that both "content" and "content_formatted" were added to the $parsed_fields array. This is because they are treated as 2 separate fields even though the latter is 
magic method property.</p>

<p class="important"><a href="<?=user_guide_url('libraries/my_model/data_record_class_functions')?>">Click here to view the function reference for custom record objects</a></p>



<h2 id="custom_fields">Custom Fields Objects</h2>
<p>Sometimes, but not as often, you may want a field to be a specific object instead just a string or integer value. 
MY_Model provides a <dfn>custom_fields</dfn> property that allows you to map specific classes to fields. 
The value can be an array with the keys being the field name, and the values can either be string values, referencing the class name,
or an array specfying the module (key) and the class name (value). You can also pass in initialization parameters to the class with the <dfn>init</dfn>
parameter. Below are some examples:</p>

<pre class="brush:php">
// loading from the fuel/application/libraries/custom_fields/My_asset_field.php
'image' => 'My_asset_field', 

// loading from the fuel/modules/my_module/libraries/custom_fields/My_text_field.php
'name' => array('my_module' => My_text_field'), 

// loading from the fuel/application/libraries/custom_fields/My_url_field.php and passing ina prefix_path of 'test'
'url' => array('model' => 'My_url_field', 'init' => array('prefix_path' => 'test')), 
</pre>

<p>Once created by a record, you will be able to reference the fields value as normal, but will also be able to call any methods on the custom field's object like so:</p>
<pre class="brush:php">
echo $record->image; // my_img.jpg

echo $record->image->my_func(); // my_func method's output from the My_asset_field object
</pre>

<p class="important">By default, FUEL will look in the "{module}/libraries/custom_fields" folder for the the classes to load.</p>



<h2 id="magic_methods">Magic Methods</h2>
<p>MY_Model uses PHP's magic methods extensively. This allows for dynamically creating methods that aren't originally defined by the class. 
Magic methods are used both in the table and custom record classes. In custom record classes, any method prefixed with <dfn>get_</dfn>
can also be syntactically written to look like an instance property:</p>

<pre class="brush: php">
$record->get_content()

// can also be written as...
$record->content
</pre>

<p>There are also two additional variations you can use on a record property &mdash; <dfn>{property}_formatted</dfn> and <dfn>{property}_stripped</dfn>.</p>
<p>Using <dfn>{property}_formatted</dfn> will apply the <a href="http://codeigniter.com/user_guide/helpers/typography_helper.html" target="_blank">auto_typography</a>
function if the property is a string value and if it is a date value will format the date by default to whatever is specified in the <span class="file">fuel/application/config/MY_config.php</span>.
</p>
<pre class="brush: php">
// returns content with &lt;p&gt; tags applied
echo $record->content_formatted;
</pre>

<p>Using <dfn>{property}_stripped</dfn> will apply the <a href="http://php.net/manual/en/function.strip-tags.php" target="_blank">strip_tags</a> PHP function and only applies to string values.</p>
<pre class="brush: php">
// returns content with HTML tags stripped (only applies to fields that return strings)
echo $record->content_stripped;
</pre>


<p>Additionally, you can use <dfn>is_{property}()</dfn> on any property that is a boolean type value or an enum value with 2 options.</p>
<pre class="brush: php">
if ($record->is_published())
{
	echo 'Published';
}
else
{
	echo 'Not Published';
}
</pre>

<p>To determine if a value exists for a certain property you can use <dfn>has_{property}()</dfn>.</p>
<pre class="brush: php">
if ($record->has_image())
{
	echo '<img src="'.$record->image.'" />';
}
else
{
	echo 'No Image';
}
</pre>




<p>In the table class, magic methods are used to find records in interesting ways. For example, you can do something like this (where <var>{}</var> enclose areas where the developer should change to a proper field name):</p>
<pre class="brush: php">
// to find multiple items 
$this->examples_model->find_all_by_{column1}_and_{column2}('column1_val', 'column2_val');

// to find one item 
$this->examples_model->find_one_by_{column1}_or_{column2}('column1_val', 'column2_val');
</pre>

<h2 id="active_record">Working with Active Record</h2>
<p>MY_Model works alongside active record like so:</p>
<pre class="brush: php">
...
$this->db->where(array('published' => 'yes'))
$this->db->find_all()

// Is the same as...
$this->db->find_all(array('published' => 'yes'))

</pre>

<h2 id="relationships">Relationships</h2>
<p>FUEL CMS 1.0 provides several ways to form model relationships by adding one or more of the following properties to your model:</p>
<ul>
	<li><strong>foreign_keys</strong>: one-to-many relationship. If attached to a CMS module, it is as a dropdown select to the model the foreign key belongs to.</li>
	<li><strong>has_many</strong>: many-to-many relationship. If attached to a CMS module, it is a multiple select field.</li>
	<li><strong>belongs_to</strong>: many-to-many relationship. If attached to a CMS module, it is a multiple select field.</li>
</ul>
<p class="important">Note that both the <dfn>has_many</dfn> and <dfn>belongs_to</dfn> relationships use the <dfn>on_after_save</dfn> model hook to store relationship information. So, if you overwrite
this method in your model and have one of these relationships assigned to your model, you must call parent::on_after_save($values); to properly save the relationship information.
</p>
<h3>Foreign Keys</h3>
<p>The <dfn>foreign_key</dfn> model property will dynamically bind the foriegn key's record object to the declared model's record object. So for example,
say you have a products model. Each product can belong to only one category. In this case, we can use FUEL's built in <a href="<?=user_guide_url('general/tags-categories#categories')?>">Categories</a>
module to make the relationship like so:</p>
<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $foreign_keys  = array('category_id' => array(FUEL_FOLDER => 'categories_model')));
  
  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>
<p class="important">The constant <dfn>FUEL_FOLDER</dfn> is used as the array key above to point to the module in which to load the model (e.g. array(FUEL_FOLDER => 'categories_model'))).
You can alternatively just use the name of the model as a string if the model exists in your application directory or you can use <dfn>'app'</dfn> instead of <dfn>FUEL_FOLDER</dfn>
</p>
<br />

<p>If your site uses categories within different contexts, you can add a where condition to target that context like so:</p>
<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $foreign_keys  = array('category_id' => array(FUEL_FOLDER => 'categories_model', 'where' => array('context' => 'products')));
  
  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>
<p>Then in your front end code, you can access the object like so:</p>
<pre class="brush:php">
$id = uri_segment(3); // assumption here that the 3rd segment has the product id
$product = fuel_model('products', 'key', $id);
if (isset($product->id))
{
	echo $product->category->name;
}
</pre>
<p class="important">Foreign Keys are automatically set as required fields when inputting int the CMS.</p>


<h3>Has Many Relationship</h3>
<p>The <dfn>has_many</dfn> model property allows you to assign many-to-many relationships between models without needing to setup a separate lookup table. FUEL will automatically
save this relationship in the <dfn>fuel_relationships</dfn> table. If we continue with the products example above, a product may also need to be associated with multiple attributes, 
or tags, like regions your product belongs to, or specific features. For this, you can use FUEL's built in <a href="<?=user_guide_url('general/tags-categories#tags')?>">Tags</a> module to create relationships like so:</p>

<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $has_many = array('attributes' => array(array(FUEL_FOLDER => 'fuel_tags_model'));

  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>
<p class="important">The constant <dfn>FUEL_FOLDER</dfn> is used as the array key above to point to the module in which to load the model (e.g. array(FUEL_FOLDER => 'categories_model'))).
You can alternatively just use the name of the model as a string if the model exists in your application directory or you can use <dfn>'app'</dfn> instead of <dfn>FUEL_FOLDER</dfn>
</p>
<br />

<p>The long way:</p>
<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $has_many = array('attributes' => array('model' => array(FUEL_FOLDER => 'fuel_tags_model'));

  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>
<p>Even longer:</p>
<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $has_many = array('attributes' => array('model' => 'fuel_tags_model', 'module' => FUEL_FOLDER)); //NOTE THE 'module' key

  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>

<p>In this example, if you want to target only a specific set of tags that belong to a particular category, you can use the <dfn>where</dfn> condition to further filter the list of tags like so:</p>
<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $has_many = array('attributes' => array('model' => array(FUEL_FOLDER => 'fuel_tags_model', 'where' => 'category_id = 1'));

  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>

<p>Then in your front end code, you can access the object like so:</p>
<pre class="brush:php">
$id = uri_segment(3); // assumption here that the 3rd segment has the product id
$product = fuel_model('products', 'key', $id);
if (isset($product->id))
{
	foreach($product->attributes as $attribute)
	{
		echo $attribute->name;
	}
}
</pre>
<p>If you would like to do further active record filtering on the relationship before retrieving the data, you can call the property using it's full method form and passing 
<dfn>TRUE</dfn> to it. This will return the model object with the "find within" part of the query already set by active record:</p>
<pre class="brush:php">
$id = uri_segment(3); // assumption here that the 3rd segment has the product id
$product = fuel_model('products', 'key', $id);
if (isset($product->id))
{
	$tags_model = $product->get_attributes(TRUE); // NOTE THE DIFFERENCE HERE
	$attributes = $tags_model->find_all('category_id = 1');
	foreach($attributes as $attribute)
	{
		echo $attribute->name;
	}
}
</pre>

<h3>Belongs To Relationship</h3>
<p>The <dfn>belongs_to</dfn> model attribute is very similar to the <dfn>has_many</dfn>, but is done from the opposite perspective, which in this case would be from FUEL's <a href="<?=user_guide_url('general/tags-categories#tags')?>">Tags</a> module. 
FUEL automatically creates the <dfn>belongs_to</dfn> relationship for the Tags module. This allows you to see which products belong to a specific tag in this example:</p>
<pre class="brush:php">
$slug = uri_segment(3); // assumption here that the 3rd segment has the slug
$tag = fuel_model('products', 'one', array('slug', $slug));
if (isset($tag->id))
{
	$tags_model = $product->get_attributes(TRUE); // NOTE THE DIFFERENCE HERE
	$products = $tag->products;
	foreach($products as $product)
	{
		echo $product->name;
	}
}
</pre>

<p class="important">
	By default, FUEL uses the built-in relationships model to save relationship data. However, if you want to change the model, as well as the field names it uses to store the key information, 
	you can pass the following	parameters in your <dfn>has_many</dfn> or <dfn>belongs_to</dfn> properties array: <dfn>fuel_relationships_model</dfn>, <dfn>foreign_key</dfn>, <dfn>candidate_key</dfn>. An Example is below:
</p>
<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $has_many = array('attributes' => array('model' => array(FUEL_FOLDER => 'fuel_tags_model'), 'fuel_relationships_model' => 'my_relationship_model', 'foreign_key' => 'my_foreign_key', 'candidate_key' => 'candidate_key');

  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>

<h2 id="serialized">Serialized Fields</h2>
<p>FUEL CMS provides a way for you to automatically serialize and unserialize data in a field. To do this, you can assign the field name to the <dfn>serialized_fields</dfn> array. This will automatically 
serialize the data upon saving and unserialize it upon retrieval via a "find_*" method on the model. The default encoding is JSON but can be changed to use the PHP serialize/unserialize function by changing the 
<dfn>default_serialization_method</dfn> value to <dfn>serialize</dfn>.</p>

<pre class="brush:php">
class Products_model extends Base_module_model
{
  public $serialized_fields = array('attributes');

  function __construct()
  {
    parent::__construct('products'); // table name
  }
}
</pre>
<pre class="brush:php">
// then in your controller or elsewhere
$product = $this->products_model->create();
$product->attributes = array('color' => 'black', 'material' => 'metal');
$product->save(); // saves and serializes the data
</pre>


<h2 id="hooks">Hooks</h2>
<p>MY_Model provides hooks that you can overwrite with your own custom code to extend the functionality of your model. In most cases, the hooks accept
an array of values to be processed and should return the processed array.</p>
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
	<li><strong>on_before_post</strong> - to be called from within your own code right before processing post data</li>
	<li><strong>on_after_post</strong> - to be called from within your own code after posting data</li>
	<li><strong>on_duplicate</strong> - executed when a record class is duplicated</li>
	<li><strong>on_created</strong> - executed when a record class is created</li>
</ul>
<br />
<p>Record class hooks</p>
<ul>
	<li><strong>before_set</strong> - executed before setting a value</li>
	<li><strong>after_get</strong> - executed after setting a value</li>
	<li><strong>on_insert</strong> - executed after inserting</li>
	<li><strong>on_update</strong> - executed after updating</li>
	<li><strong>on_init</strong> - executed upon initialization</li>
</ul>


<h2 id="formatters">Formatters</h2>
<p>Formatters allow you to easily modify a value by mapping a function to a suffix value of a property. 
An example of this is to use the "_formatted" suffix on a string type field which would apply the <a href="http://codeigniter.com/user_guide/helpers/typography_helper.html" target="_blank">auto_typography</a>.</p>

<pre class="brush:php">
echo $record->content;
// A long time ago...

echo $record->content_fornatted;
// &lt;p&gt;A long time ago...&lt;/p&gt;
</pre>

<p>Formatter function that can have additional parameters passed to them can be called as methods. An example would be the "wrap" filter:</p>
<pre class="brush:php">
$record->content_wrap(50);
</pre>

<p>Formatters can be applied to specific field types like datetime fields or string type fields, or they can be applied to field names (e.g. image, img, thumb).

You can also apply multiple formatters to a field type by using the <dfn>format function like so:</p>
<pre class="brush:php">
$record->format('content', 'stripped|formatted');
//OR
$record->format('content', 'stripped, formatted');
//OR
$record->format('content', array('stripped', 'formatted');
</pre>

<p>MY_Model has a <dfn>formatters</dfn> property in which <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> extends to 
	provide you by default with the following formatters for different field types:
</p>


<h3>Datetime and Date Field Examples</h3>
<p>Below are examples of formatters that can be applied to a date field and links to the functions that they use.</p>
<ul>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_date_formatter')?>">{datetime}_formatted</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_timestamp')?>">{datetime}_timestamp</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_month')?>">{datetime}_month</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_day')?>">{datetime}_day</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_weekday')?>">{datetime}_weekday</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_year')?>">{datetime}_year</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_hour')?>">{datetime}_hour</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_minute')?>">{datetime}_minute</a></li>
	<li><a href="<?=user_guide_url('helpers/my_date_helper#func_second')?>">{datetime}_second</a></li>
</ul>

<h3>String Field Examples</h3>
<p>Below are examples of formatters that can be applied to a string fields (char, varchar, text) and links to the functions that they use.</p>
<ul>
	<li><a href="http://codeigniter.com/user_guide/helpers/typography_helper.html" target="_blank">{string}_formatted</a> (auto_typography CI function)</li>
	<li><a href="http://php.net/manual/en/function.strip-tags.php" target="_blank">{string}_stripped</a></li>
	<li><a href="<?=user_guide_url('helpers/markdown_helper')?>">{string}_markdown</a></li>
	<li><a href="<?=user_guide_url('helpers/my_string_helper#func_parse_template_syntax')?>">{string}_parsed</a></li>
	<li><a href="http://codeigniter.com/user_guide/helpers/text_helper.html" target="_blank">{string}_excerpt</a> (word_limiter CI function)</li>
	<li><a href="http://codeigniter.com/user_guide/helpers/text_helper.html" target="_blank">{string}_ellipsize</a> (ellipsize CI function)</li>
	<li><a href="http://codeigniter.com/user_guide/helpers/text_helper.html" target="_blank">{string}_highlight</a> (highlight_phrase CI function)</li>
	<li><a href="http://us2.php.net/manual/en/function.htmlentities.php" target="_blank">{string}_entities</a></li>
	<li><a href="http://us2.php.net/manual/en/function.htmlspecialchars.php" target="_blank">{string}_specialchars</a></li>
	<li><a href="http://codeigniter.com/user_guide/helpers/inflector_helper.html" target="_blank">{string}_humanize</a> (humanize CI function)</li>
	<li><a href="http://codeigniter.com/user_guide/helpers/inflector_helper.html" target="_blank">{string}_underscore</a> (underscore CI function)</li>
	<li><a href="http://codeigniter.com/user_guide/helpers/inflector_helper.html" target="_blank">{string}_camelize</a> (camelize CI function)</li>
</ul>

<h3>Number Examples</h3>
<p>Below are examples of formatters that can be applied to a number type fields (tinyint, smallint, int, bigint) and links to the functions that they use.</p>
<ul>
	<li><a href="<?=user_guide_url('helpers/format_helper')?>" target="_blank">{string}_dollar</a></li>
</ul>

<h3>Special Field Name Examples</h3>
<p>Below are examples of formatters that can be applied to a number type fields (tinyint, smallint, int, bigint) and links to the functions that they use.</p>
<ul>
	<li><a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">{url|link}_path</a> (site_url CI function)</li>
	<li><a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">{url|link}_prep</a> (prep_url CI function)</li>
	<li><a href="<?=user_guide_url('helpers/asset_helper#func_assets_path')?>">{img|image|thumb|pdf}_path</a></li>
	<li><a href="<?=user_guide_url('helpers/asset_helper#func_assets_server_path')?>">{img|image|thumb|pdf}_serverpath</a></li>
	<li><a href="<?=user_guide_url('helpers/asset_helper#func_asset_filesize')?>">{img|image|thumb|pdf}_filesize</a></li>
	<li><a href="<?=user_guide_url('helpers/asset_helper#func_asset_exists')?>">{img|image|thumb|pdf}_exists</a></li>
</ul>



<p class="important">Formatter aliases must <strong>not</strong> contain "_" (underscores). So for example, the function "word_limiter" has an alias of "wordlimiter".</p>



