<h1>Tags &amp; Categories</h1>
<p>FUEL CMS 1.0 provides a generic tags and categories modules that can you incorporate into your own modules. <strong>They are hidden by default. </strong>
To turn them on, you must comment out the lines in the <span class="file">fuel/application/config/MY_fuel_modules.php</span> making them hidden. 
The differences between tags and categories can be a little confusing at first but the best way to think of their differences is that
tags provide a flat many-to-many relationship between things whereas categories provide a one-to-many relationship and can often be
hierarchical. In fact, the tags module uses categories to group tags together.</p>
</p>

<h2 id="tags">Tags</h2>
<p>To utilize the tags module, add a <dfn>has_many</dfn> property to your module's model. 
This will use FUEL's relationship table to store tag relationships and will create a multi-select for the module form in the CMS. 
It will also automatically assign the <dfn>belongs_to</dfn> relationship to your tag. </p>
<pre class="brush:php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Widgets_model extends Base_module_model {
	....
	public $has_many = array('tags' => 'fuel_tags_model');
	....
</pre>

<p>This will allow you to do something like the following from the widget record object:</p>

<pre class="brush:php">
$widgets = fuel_model('widgets');
foreach($widgets as $widget)
{
	$widget_tags = $widget->tags;
	foreach($widget_tags as $tag)
	{
		echo $tag->name;
	}
}
</pre>

<p>This will allow you to do something like the following from the tag object:</p>

<pre class="brush:php">
$tag = $this->fuel->tags->find_by_tag('my_tag');
foreach($tag->widgets as $widget) {
	echo $widget->name;
}
</pre>


<h2 id="categories">Categories</h2>
<p>The category module is helpful for categorizing records. A catgory's <dfn>context</dfn> value can be used to further group categories together.
You can use the a model's <dfn>foreign_key</dfn> property as well as the addition of the <dfn>where</dfn> parameter to target 
a particular context (if needed) and associate a single category to a particular record. This will create a dropdown select for the module form in the CMS:</p>

<pre class="brush:php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Widgets_model extends Base_module_model {
	....
	public $foreign_keys  = array('category_id' => array(FUEL_FOLDER => 'fuel_categories_model', 'where' => array('context' => 'product_categories')));
	....
</pre>


<p>From the widget records perspective, you can do something like the following:</p>
<pre class="brush:php">
// also can use $this->widgets_model->find_all();
$widgets = fuel_model('widgets');
foreach($widgets as $widget) {
	echo $widget->category->name;
}
</pre>

<p>From the category perspective, you can do something like the following:</p>
<pre class="brush:php">
// also could use $this->fuel_categories_model->find_one(array('slug' => 'my_category')); assuming the model is loaded
$category = $this->fuel->categories->find_by_slug('my_category'); 

foreach($category->widgets as $widget) {
	echo $widget->name;
}
</pre>