<h1><strong>Tutorial:</strong> Creating Simple Modules</h1>
<p>The following is a tutorial on creating <a href="<?=user_guide_url('modules/simple')?>"><strong>simple</strong></a> modules in FUEL. 
Don't let the word <em>simple</em> fool you though. You can still create pretty powerful modules without the need of creating your own views and controllers which is what <a href="<?=user_guide_url('modules/advanced')?>"><strong>advanced</strong></a> modules are for.
We will actually create several modules to allow us to create articles and categorize them.
We will be using four data models, and creating three modules. 
We will start with the easiest and progress to more advanced modules.
The first one will be an authors module, followed by the articles module. 
The last we will create is the categories module which links articles to categories.
</p>

<h4><strong>These are the steps in the following tutorial:</strong></h4>
<ol>
	<li><a href="#setup">Download and Setup</a></li>
	<li><a href="#authors_module">The Authors Module</a></li>
	<li><a href="#articles_module">The Articles Module</a></li>
	<li><a href="#categories_module">The Categories Module</a></li>
	<li><a href="#categories_to_articles">Associating Categories to Articles</a></li>
	<li><a href="#permissions">Setting Up Permissions</a></li>
	<li><a href="#polishing">A Little Polishing</a></li>
	<li><a href="#views">Bringing it All Into View</a></li>
</ol>

<h2 id="setup">Download and Setup</h2>
<p>Before we get started with this tutorial, it is recommended that you <a href="<?=site_url(MODULES_WEB_PATH.USER_GUIDE_FOLDER.'/examples/fuel_modules_example.zip')?>"><strong>download the final files of the example</strong></a> and look them over briefly so it is easier to follow along.</p>

<p>We will be creating four database tables to support the three modules &mdash; an authors table, an articles table, a categories table, 
and a categories_to_articles lookup table.</p>

<p>You will need to <a href="<?=user_guide_url('general/db-setup')?>">setup an instance of FUEL including the database</a> with a working admin login.
We will use that FUEL database to store the additional tables required for our models in this 
tutorial.</p>

<h2 id="authors_module">The Authors Module</h2>
<p>We will start this tutorial by creating the authors module first.</p>

<h3>Setup the Authors Table</h3>
<p>The following is the SQL code we will be using for the table used by authors model. Copy this code 
and run this query to generate the table in the FUEL database instance you are using for this tutorial.</p>

<pre class="brush: sql">
CREATE TABLE `authors` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `bio` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `avatar` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
</pre>

<p class="important"><strong>Note the column <kbd>published</kbd>.</strong> The enum columns <kbd>published</kbd> and <kbd>active</kbd>
carry special meaning in FUEL and will determine whether to display the content on the website.
</p>

<h3>Create the Authors Model</h3>
<p>After creating the table, we will create the model that links to that table.
To do that, create a file in your <dfn>fuel/application/model</dfn> directory
and name it <dfn>authors_model.php</dfn>. In that file we will be creating two
classes. The first class, <dfn>Authors_model</dfn>, will extend the <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> 
(which is an extension of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>).
</p>

<p>The second class we will create in the <dfn>authors_model.php</dfn> file is the custom record class, <dfn>Author_model</dfn>.
This class is not required, but it will give us some extra functionality at the record level 
later on.
</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authors_model extends Base_module_model {

    function __construct()
    {
        parent::__construct('authors');
    }
}

class Author_model extends Base_module_record {

}
</pre>

<p>This is really all you need at the moment for the model so we will now focus on getting it
it hooked up to the FUEL admin.
</p>

<h3>Hooking Up the Authors Module</h3>
<p>Now that our table and model have been created, we need
to tell FUEL about it. To do that, open up the <dfn>application/config/MY_fuel_modules.php</dfn> file and add the following line.</p>
<pre class="brush: php">
$config['modules']['authors'] = array(); 
</pre>

<p class="important">The parameters for the module are empty for this example. For a complete list of parameters to pass to the module, 
<a href="<?=user_guide_url('modules/simple')?>">click here</a>.</p>

<p>Now login to fuel (e.g. http://mysite.com/fuel), and you should see the module on the left under the MODULES section.</p>
<img src="<?=img_path('examples/authors_module.png', 'user_guide')?>" class="screen" />

<p class="important">If you do not see the module on the left, make sure you are logged in as an admin,
or that you have the proper <a href="#permissions">permissions</a> to view that module.
</p>

<p>The form fields should look like the following:</p>
<img src="<?=img_path('examples/authors_form.png', 'user_guide')?>" class="screen" />

<h2 id="articles_module">The Articles Module</h2>
<p>The articles module will contain the content of the articles as well as some meta information including a foreign key to the authors table.</p>

<h3>Setup the Articles Table</h3>
<p>The following is the SQL code we will be using to drive the articles model. Copy this code 
and run this query to generate the table in the FUEL database instance you are using for this tutorial.</p>

<pre class="brush: sql">
CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `author_id` tinyint(3) unsigned NOT NULL default '0',
  `title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `permalink` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `content` text collate utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
</pre>

<h3>Create the Articles Model</h3>
<p>Now that the table is created, we will follow similar steps as above to create the articles model. So
create a new file in the <dfn>fuel/application/model</dfn> folder and call it <dfn>articles_model.php</dfn>.
Now add the following code to that file:
</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Articles_model extends Base_module_model {

    function __construct()
    {
        parent::__construct('articles');
    }
}

class Article_model extends Base_module_record {

}
</pre>

<h4>Changing the List View's Columns</h4>
<p>There are a few additional things we need to do to have this module work appropriately. The first
is to change the list_items method so that author_id column will actually display the author's name, the content 
column is truncated to 50 characters, and the date_added column will be in the <dfn>mm/dd/yyyy</dfn> format. This is how we do it:</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Articles_model extends Base_module_model {

    function __construct()
    {
        parent::__construct('articles');
    }
	
    function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
    {
        $this->db->join('authors', 'authors.id = articles.author_id', 'left');
        $this->db->select('articles.id, authors.name AS author, title, SUBSTRING(content, 1, 50) AS content, date_added, articles.published', FALSE);
        $data = parent::list_items($limit, $offset, $col, $order);
        return $data;
    }

}

class Article_model extends Base_module_record {

}
</pre>
<p>Note how we precede some of the column names in the select with the table name to prevent ambiguity between the tables. Lastly,
because we are using MYSQL functions in the select, we need to pass <dfn>FALSE</dfn> as the select's second parameter.
</p>
<p class="important">The key column (e.g. <kbd>id</kbd>) is not seen in the FUEL admin but is important to include because it is used to identify the row. </p>
<p class="important">Although we show default <dfn>$col</dfn> and <dnf>$order</dnf> values, those need to be set on the <a href="<?=user_guide_url('modules/simple')?>">module</a> to properly sort the list items.</p>


<h4>Setting Up the Form</h4>
<p>The next thing we should change is the the form used to create and edit an article. You'll notice that there is a text field for the author_id which
isn't very convenient. Instead, we will put in a dropdown select that will allow the user to select from a list of authors. To do this, we will
add a <dfn>foreign_keys</dfn> property to the model and identify <dfn>author_id</dfn> as a foreign key to the <dfn>authors_model</dfn>. Doing this,
will create a dropdown of authors to appear by default. Also, adding the <dfn>parsed_fields</dfn> property to the model will tell it to parse any template syntax
for the specified fields. The <dfn>content_formatted</dfn> field is a derived field that can be called on the record model so it is added as well.</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {
	
	public $foreign_keys = array('author_id' => 'authors_model');
	public $parsed_fields = array('content', 'content_formatted');
	
    function __construct()
    {
        parent::__construct('articles'); // table name
    }

    function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
    {
        $this->db->join('authors', 'authors.id = articles.author_id', 'left');
        $this->db->select('articles.id, authors.name AS author, title, SUBSTRING(content, 1, 50) AS content, date_added, articles.published', FALSE);
        $data = parent::list_items($limit, $offset, $col, $order);
        return $data;
    }

    function form_fields($values = array())
    {
        $fields = parent::form_fields($values);
		// ******************* ADD CUSOM FORM STUFF HERE ******************* 
        return $fields;
    }
}

class Article_model extends Data_record {

}
</pre>

<p class="important">The <a href="<?=user_guide_url('libraries/my_model/table_class_functions#options_list')?>">options_list</a> method creates a key value array that is used by FUEL's
<a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class to create the field.</p>

<p class="important">The <kbd>date_added</kbd> column is hidden by default in the FUEL admin but will be auto-detected as a field that will automatically 
insert the date. MY_Model will by default hide field names of <kbd>date_added, entry_date, last_modified and last_updated</kbd>.</p>

<p>Although we are not quite done with this model, we are going to go ahead and create the categories module. After we are done with that,
we will revisit this model and associate categories to articles in the Articles_model <dfn>form_fields</dfn> method. Below is what the form should look like in FUEL at this point:</p>

<img src="<?=img_path('examples/articles_form.png', 'user_guide')?>" class="screen" />

<h2 id="categories_module">The Categories Module</h2>
<p>The categories module will be used to group articles together. You will be able to create categories separately 
as well as associate them to the article within the articles module (we'll get to that).</p>

<h3>Setup the Category Tables</h3>
<p>The categories module will be made up of two tables &mdash; one table for the categories
and another to associate categories with articles. Below is the SQL for you to create those tables:</p>

<pre class="brush: sql">
CREATE TABLE  `categories` (
 `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `name` VARCHAR( 100 ) NOT NULL ,
 `published` ENUM(  'yes',  'no' ) NOT NULL DEFAULT  'yes'
) ENGINE = MYISAM ;

CREATE TABLE  `categories_to_articles` (
 `category_id` SMALLINT UNSIGNED NOT NULL ,
 `article_id` INT UNSIGNED NOT NULL ,
PRIMARY KEY (  `category_id` ,  `article_id` )
) ENGINE = MYISAM ;
</pre>

<h3>Create the Category Models</h3>
<p>We will now create two models, one for each table. However, the category_to_articles table will only
be used to help associate categories to groups and will not be visible in the FUEL admin.</p>

<h4>The Categories Model</h4>
<p>Similar to above, we will create the categories model. So
create a new file in the <dfn>fuel/application/model</dfn> folder and call it <dfn>categories_model.php</dfn>.
Now add the following code to that file:
</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Categories_model extends Base_module_model {
	
	public $record_class = 'Category';
	
	function __construct()
	{
		parent::__construct('categories');
	}

	
	// cleanup category to articles
	function on_after_delete($where)
	{
		$CI =& get_instance();
		$CI->load->model('categories_to_articles_model');
		if (is_array($where) && isset($where['id']))
		{
			$where = array('category_id' => $where['id']);
			$CI->categories_to_articles_model->delete($where);
		}
	}

}

class Category_model extends Base_module_record {
}
</pre>
<p>In this model we are doing a couple extra things. The first is we add the class property of <dfn>$record_class</dfn> to specify the name of the 
record class to associate with this model. We do this because the singular version of <dfn>categories</dfn> cannot easily be determined 
by removing the "s". The second thing we add is a hook to delete all <dfn>categories_to_articles</dfn> records with the category_id upon deletion of a category.
This will clean up any strays in the lookup table associated with the category that was just deleted.
</p>

<h4>The Categories to Articles Lookup Model</h4>
<p>Now we will create a new file in the <dfn>fuel/application/model</dfn> folder and call it <dfn>categories_to_articles.php</dfn>.
Now add the following code to that file:
</p>

<pre class="brush: php">

&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categories_to_articles_model extends MY_Model {

	public $record_class = 'Category_to_article';

	function __construct()
	{
		parent::__construct('categories_to_articles');
	}

	function _common_query()
	{
        $this->db->select('categories_to_articles.*, articles.title, categories.name AS category_name, articles.author_id, categories.published');
        $this->db->join('articles', 'categories_to_articles.article_id = articles.id', 'left');
        $this->db->join('categories', 'categories_to_articles.category_id = categories.id', 'left');
        $this->db->join('authors', 'authors.id = articles.author_id', 'left');
	}

}

class Category_to_article_model extends Data_record {
	public $category_name = '';
	public $title = '';
	public $author_id;
}
</pre>
<p>Similar to the <dfn>Categories_model</dfn>, we need to specify the record_class name property. 
We also take advantage of the <dfn>_common_query</dfn> method to automatically join and select the article title, category name and author_id.
We also will add three properties (which are record fields), to the record class to capture the article title, category name and author_id.</p>

<h3>Hooking Up the Categories Module</h3>
<p>Now that our tables and model have been created, we need
to tell FUEL about it. To do that, open up the <dfn>applicaitons/config/MY_fuel_modules.php</dfn> file
and add the following line.
</p>
<pre class="brush: php">
$config['modules']['categories'] = array(); 
</pre>

<p>The categories form should look like the screen below:</p>
<img src="<?=img_path('examples/categories_form.png', 'user_guide')?>" class="screen" />

<h2 id="categories_to_articles">Associating Categories to Articles</h2>
<p>Now that the category and the categories to articles models are created, we need to add a field to the article models form to allow you to associate one or more
categories to an article. So go back to the articles model and add the following to the <dfn>form_fields</dfn> method:</p>

<pre class="brush: php">
...
function form_fields($values = array())
{
	
	// ******************* NEW RELATED CATEGORY FIELD BEGIN ******************* 
	$related = array('categories' => 'categories_to_articles_model');
	// ******************* NEW RELATED CATEGORY FIELD END ******************* 

	$fields = parent::form_fields($values, $related);

	$CI =& get_instance();
	$CI->load->model('authors_model');
	$CI->load->model('categories_model');
	$CI->load->model('categories_to_articles_model');

	$author_options = $CI->authors_model->options_list('id', 'name', array('published' => 'yes'));
	$fields['author_id'] = array('type' => 'select', 'options' => $author_options);

	return $fields;
}
...
</pre>

<p>This will add a multi-select field to your form to associate categories to your article.</p>

<h3>Adding an After Save Hook</h3>
<p>We aren't quite done yet though. We need to make sure that the categories to articles information gets saved after we hit the save button. 
To do that we add an <dfn>on_after_save</dfn> hook to the <dfn>Articles_model</dfn>. We call the save_related method that will
save data used for a many to many lookup table:</p>

<pre class="brush: php">
...
function on_after_save($values)
{
	$data = (!empty($this->normalized_save_data['categories'])) ? $this->normalized_save_data['categories'] : array();
	$this->save_related('categories_to_articles_model', array('article_id' => $values['id']), array('category_id' => $data));
}
...
</pre>

<h2 id="permissions">Creating the Permissions</h2>
<img src="<?=img_path('examples/manage_permissions.png', 'user_guide')?>" class="screen img_right" />
<p>Now that the modules are created, you will probably want to allow others to use them. To do this,
you will need to create permissions so users can be allowed to access those modules in FUEL.
To do that, </p>
<ol>
	<li>click on the permissions link on the left and create the permission <dfn>authors</dfn>. This will create a permission for users to be subscribed to. </li>
	<li>To assign the permission to a user, click the users link on the left menu, then select a user. </li>
	<li>Once in the edit user screen, check the box for the new authors permission you just created.</li>
</ol>
<p>Repeat these step to create the articles permissions.</p>

<h2 id="polishing">A Little Polishing</h2>
<p>Now that we have the authors, articles and categories modules created and working, it's time to add a little polish.
</p>

<h3>Polishing the Authors Module</h3>
<p>To improve the authors module, we are going to do the following.</p>

<ol>
	<li>Add required fields</li>
	<li>Add image upload functionality to the form</li>
	<li>Add an avatar_image method to the record object</li>
</ol>

<h4>Required Fields</h4>
<p>The first thing we will do is add some required fields to the model like so:</p>

<pre class="brush: php">
... 
class Authors_model extends Base_module_model {
	
	public $required = array('name', 'email');

...
</pre>

<h4>Uploading an Image</h4>
<p>Next, we will add the ability to upload an image to the module. To do this, we will alter the <dfn>form_fields</dfn> method like so:</p>

<pre class="brush: php">
... 
function form_fields($values = array())
{
	$fields = parent::form_fields($values);
	
	$upload_path = assets_server_path('authors/', 'images');
	$fields['avatar_upload'] = array('type' => 'file', 'upload_path' => $upload_path, 'overwrite' => TRUE);
	$fields['published']['order'] = 1000;
	return $fields;
}
...
</pre>
<p class="important">A field name that ends in <kbd>image</kbd> or <kbd>img</kbd> will automatically be turned into an image select field/upload field for a module (not for layouts though. For layouts you must create the 2 fields manually).</p>
<p class="important">The suffix of <kbd>_upload</kbd> triggers FUEL to upload the image and assign the value to the <kbd>avatar</kbd> field. 
The <kbd>upload_path</kbd> array parameter tells FUEL where to upload the file. In this case we will upload path to the <kbd>assets/images/authors</kbd> folder
<kbd>(the folder must have writable permissions)</kbd>.</p>

<p class="important">By adding the <kbd>order</kbd> parameter to published, you can change where it appears in the form.</p>

<h4>Adding to the Record Object</h4>
<p>The last addition we will make to the authors model will be to add a new <strong>record method</strong> that will create the image tag with the path to the avatar image:</p>

<pre class="brush: php">
... 
class Author_model extends Data_record {
	
	public function get_avatar_image()
	{
		return '&lt;img src="'.img_path($this->avatar).'" /&gt;';
	}
}
...
</pre>
<br />


<h3>Polishing the Articles Module</h3>
<p>To improve the articles module, we are going to do the following:</p>
<ol>
	<li>Add required fields</li>
	<li>Integrate with the authors module</li>
	<li>Add a tree method to the model</li>
	<li>Improve the form</li>
	<li>Add an on_after_delete hook</li>
	<li>Add to the record object</li>
</ol>

<h4>Required Fields</h4>
<p>As with the authors module, the first thing we will do is add some required fields to the model like so:</p>


<pre class="brush: php">
... 
class Articles_model extends Base_module_model {
	
	public $required = array('title', 'content');

...
</pre>


<h4>Integrate With the Authors Module</h4>
<p>FUEL allows one click access to another module's form. For the articles module, we should allow users to add and edit authors directly in the articles module.
To do this, you can take advantage of a <a href="<?=user_guide_url('modules/forms')?>">special CSS class name</a>, <dfn>add_edit</dfn>, that you can 
place on a field to integrate with an existing module. Note that the example below has a <dfn>class</dfn> name added to the <dfn>author_id</dfn>. The first
being the <dfn>add_edit</dfn> class and the second being the module key that you want to integrate with. The screen below the code example shows what it should look like in the form.
</p>

<pre class="brush: php">
... 
function form_fields($values = array())
{
    $fields = parent::form_fields($values);
    $CI =& get_instance();

    if ($CI->fuel_auth->has_permission('authors'))
    {
        $fields['author_id']['class'] = 'add_edit authors';
    }

    return $fields;
}
... 
</pre>
<img src="<?=img_path('examples/add_edit.png', 'user_guide')?>" class="screen" />

<h4>The Tree Method</h4>
<p>FUEL allows you to create a tree method that will display the list data in a hierarchical tree format as opposed to the list view.
For the articles module, we will create a tree view that uses the category names. The method must return an array that follows the
<a href="<?=user_guide_url('libraries/menu')?>">Menu</a> hierarchical structure. The class name will change based on its published status.</p>

<pre class="brush: php">
...
function tree()
{
	$CI =& get_instance();
	$CI->load->model('categories_model');
	$CI->load->model('categories_to_articles_model');

	$return = array();
	$categories = $CI->categories_model->find_all(array(), 'id asc');
	$categories_to_articles = $CI->categories_to_articles_model->find_all('', 'categories.name asc');

	$cat_id = -1;
	foreach($categories as $category)
	{
		$cat_id = $category->id;
		$return[] = array('id' => $category->id, 'label' => $category->name, 'parent_id' => 0, 'location' => fuel_url('categories/edit/'.$category->id));
	}
	$i = $cat_id +1;

	foreach($categories_to_articles as $val)
	{
		$attributes = ($val->published == 'no') ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
		$return[$i] = array('id' => $i, 'label' => $val->title, 'parent_id' => $val->category_id, 'location' => fuel_url('articles/edit/'.$val->article_id), 'attributes' =>  $attributes);
		$i++;
	}
	return $return;
}
... 
</pre>
<p class="important">Note that we are <strong>not</strong> including uncategorized articles in the tree view. To do that you would need to create another
root level category for uncategorized items.</p>

<p>The tree method will render like the following in the FUEL admin:</p>
<img src="<?=img_path('examples/articles_tree.png', 'user_guide')?>" class="screen" />


<h4>Adding an After Delete Hook</h4>
<p>FUEL's models allow you to add <dfn>hooks</dfn> which are functions that get executed 
when certain events occur. For the articles model, we want to add hook that upon record deletion, 
will also remove all records from the categories_to_articles table associated with that record to keep that table clean.
Below is a method we add to the Articles_model class:
</p>

<pre class="brush: php">
... 
// cleanup articles from categories to articles table
function on_after_delete($where)
{
	$this->delete_related('categories_to_articles_model', 'article_id', $where);
}
... 
</pre>

<h4>Lazy Loading the Author to the Record Object</h4>
<p>By adding the <dfn>foreign_keys</dfn> property to the <dfn>Articles_model</dfn> in one of our first steps, 
the <dfn>Article_model</dfn> record class will automatically 'lazy load' the <dfn>Author_model</dfn> record object
associated with the table record. This means you can access authors like this: <dfn>$article->author->name</dfn>.
</p>

<p>You may also use the data record class's <dfn>lazy_load</dfn> method to load in record level objects manually. 
If we were to do this manually for the Article_model class, it would look like the following:.</p>
<pre class="brush: php">
class Article_model extends Data_record {
	
	function get_author()
	{
		return $this->lazy_load('author_id', 'authors_model');
	}
}
</pre>
<p>Similarly, we add the following <dfn>foreign_keys</dfn> to the categories_to_articles_model:</p>
<pre class="brush: php">
class Categories_to_articles_model extends MY_Model {
 
    public $record_class = 'Category_to_article';
	public $foreign_keys = array('category_id' => 'categories_model', 'article_id' => 'articles_model', 'author_id' => 'authors_model');
...
</pre>


<br />

<h3>Polishing the Categories Module</h3>
<p>To improve the categories module, we are going to add required fields, add an on_after_delete hook and improve model validation.</p>
<ol>
	<li>Add required fields</li>
	<li>Add an on_after_delete hook</li>
	<li>Improve model validation</li>
</ol>


<h4>Required Fields</h4>
<p>As with the previous modules, the first thing we will do is add the <dfn>name</dfn> field to the required fields array like so:</p>

<pre class="brush: php">
... 
class Categories_model extends Base_module_model {
	
	public $required = array('name');

...
</pre>

<h4>Adding an After Delete Hook</h4>
<p>Similar to the articles model, we should add an on_after_delete hook to the Categories_model to remove any records that have the 
category associated with it in the <dfn>categories_to_articles</dfn> table. Below is that method:</p>
<pre class="brush: php">
... 
// cleanup articles from categories to articles table
function on_after_delete($where)
{
	$this->delete_related('categories_to_articles_model', 'category_id', $where);
}
... 
</pre>

<h4>Adding a Before Validation Hook</h4>
<p>Another common form of validation we may want to add involves maintaining unique field values across records.
For the categories module, we want to ensure all records have a unique <strong>name</strong> value. This means that a record cannot
have its name value changed to a name value that already exists in an another record.
Similarly, we want to ensure that all new records contain name values that don't exist in other records.
To do this we add an <dfn>on_before_validate</dfn> hook that checks to see if the record already exists and then adds
either the <dfn>is_editable</dfn> or <dfn>is_new</dfn> validation.
</p>

<pre class="brush: php">
... 
function on_before_validate($values)
{
	if (!empty($values['id']))
	{
		$this->add_validation('name', array(&$this, 'is_editable'), lang('error_val_empty_or_already_exists', 'name'), array('name', $values['id']));
	}
	else
	{
		$this->add_validation('name', array(&$this, 'is_new'), lang('error_val_empty_or_already_exists', 'name'), 'name');
	}
	return $values;
}
...
</pre>

<p class="important">The methods <kbd>is_editable</kbd> and <kbd>is_new</kbd> are methods on the MY_Model class. For the second parameter
of <kbd>add_validation</kbd>, we first add the class instance and then the method name in an array as opposed to a simple string name of a function.</p>

<h2 id="views">Bringing it All Into View</h2>
<p>Now that our modules are created for FUEL, we now can add the module's model data to our views.
Although we could use controllers to marshall the data to the views, for our example, we will just incorporate that logic in the view itself 
(<a href="<?=user_guide_url('general/opt-in-controllers')?>">read more about our opt-in controller philosophy</a>). In our view we will have a page that will either display articles based on a category or just list all. To start, create a file called 
<dfn>example.php</dfn> and place it in your <dfn>application/views</dfn> folder. Open it up and put the following code in it.
</p>
<pre class="brush: php">
&lt;?php 
$category = $CI-&gt;uri-&gt;segment(2);
if (!empty($category))
{
	$CI-&gt;load-&gt;model('categories_model');
	
	// remember, all categories that have a published value of 'no' will automatically be excluded
	$category = $CI-&gt;categories_model-&gt;find_one_by_name($category);
	$articles = $category-&gt;articles;
}
else
{
	$CI-&gt;load-&gt;model('articles_model');

	// remember, all articles that have a published value of 'no' will automatically be excluded
	$articles = $CI-&gt;articles_model-&gt;find_all();
}
?&gt;

&lt;h1&gt;Articles &lt;?php if (!empty($category)) : ?&gt; : &lt;?=$category-&gt;name?&gt; &lt;?php endif; ?&gt;&lt;/h1&gt;
&lt;?php foreach($articles as $article) : ?&gt;

&lt;h2&gt;&lt;?=fuel_edit($article-&gt;id, 'Edit: '.$article-&gt;name, 'articles')?&gt;&lt;?=$article-&gt;title?&gt;&lt;/h2&gt;
&lt;p&gt;
&lt;?=$article-&gt;content_formatted?&gt;
&lt;/p&gt;
&lt;div class="author"&gt;&lt;?=$article-&gt;author-&gt;name?&gt;&lt;/div&gt;
&lt;?php endforeach; ?&gt;
</pre>
<p>Next, for this example, we will have FUEL automatically find the view file. To do that, we will overwrite the fuel <dfn>auto_search_views</dfn>
variable in the FUEL configuration. This is done by changing <dfn>$config['auto_search_views'] = TRUE</dfn> in the <dfn>application/config/MY_fuel.php</dfn> file.</p>

<p class="important"><kbd>$CI</kbd> is a the CodeIgniter super object that FUEL automatically gets passed to the view as the variable <kbd>$CI</kbd></p>
<p class="important"><kbd>content_formatted</kbd> is a magic method. By appending <kbd>_formatted</kbd> to the end of a string type field, it will format
the field using CI's <a href="http://codeigniter.com/user_guide/helpers/typography_helper.html" target="_blank">auto_typography</a></p>
<p class="important">Check out <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_model and fuel_block</a> helper functions that could be used instead.</p>


<h3>Inline Editing</h3>
<p>The very last thing we will do is add inline editing to our view. To do this we use the <a href="helpers/fuel_helper">fuel_edit</a> function. We'll
place that function right inside the &lt;h2&gt; tag. </p>

<pre class="brush: php">
...
&lt;h2&gt;&lt;?=fuel_edit($article-&gt;id, 'Edit: '.$article-&gt;name, 'articles')?&gt;&lt;?=$article-&gt;title?&gt;&lt;/h2&gt;
...
</pre>

<p>Additionally, you may add items in the context of a page. To do that, you pass <dfn>create</dfn> as the first parameter.</p>
&lt;h2&gt;&lt;?=fuel_edit('create', 'Create', 'articles')?&gt;&lt;?=$article-&gt;title?&gt;&lt;/h2&gt;

<p class="important"><a href="<?=user_guide_url('general/inline-editing')?>">Click here for more on inline editing</a></p>

<p><strong>That's it!</strong></p>

<?php /* ?>
<h2>Now What?</h2>
<p>If you are wanting to create more advanced modules, <a href="<?=user_guide_url('modules/advanced')?>">click here</a>.</p>
<p><a href="<?=site_url(USER_GUIDE_FOLDER.'/examples/fuel_modules_example.zip')?>">Click here to download the files to the example.</a></p>
<?php */ ?>