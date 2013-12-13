<h1><strong>Tutorial:</strong> Creating Simple Modules</h1>

<p>The following is a tutorial on creating <a href="<?=user_guide_url('modules/simple')?>"><strong>simple</strong></a> modules in FUEL. 
Don't let the word <em>simple</em> fool you though. 
You can still create pretty powerful modules without the need of creating your own views and controllers which is what <a href="<?=user_guide_url('modules/advanced')?>"><strong>advanced</strong></a> modules are for.
We will actually create several simple modules to allow us to create articles and categorize them in the CMS.
We will be using 3 data models (<dfn>articles_model</dfn>, <dfn>authors_model</dfn>, <dfn>fuel_tags_model</dfn>), and creating 2 modules (articles, authors).</p>


<h4><strong>These are the steps in the following tutorial:</strong></h4>
<ol>
  <li><a href="#setup">Download and Setup</a></li>
  <li><a href="#authors_module">The Authors Module</a></li>
  <li><a href="#articles_module">The Articles Module</a></li>
  <li><a href="#categories_module">The FUEL Tags Module</a></li>
  <li><a href="#permissions">Setting Up Permissions</a></li>
  <li><a href="#polishing">A Little Polishing</a></li>
  <li><a href="#views">Bringing it All Into View</a></li>
</ol>

<h2 id="setup">Download and Setup</h2>

<p>Before we get started with this tutorial, it is recommended that you <a href="<?=assets_path('fuel_modules_example.zip', 'docs', FUEL_FOLDER)?>"><strong>download the final files of the example</strong></a> and look them over briefly so it is easier to follow along.</p>

<p>You will need to <a href="<?=user_guide_url('installation/db-setup')?>">setup an instance of FUEL including the database</a> with a working admin login.
We will use that FUEL database to store the additional tables required for our models in this tutorial.</p>

<p>We will be creating two database tables to support the 2 modules &mdash; an authors table and an articles table. The fuel_tags table is already created for us.</p>


<h2 id="authors_module">The Authors Module</h2>
<p>We will start this tutorial by creating the authors module first. The authors module will be used to contain the list of article's authors.</p>
<p>In many cases, to get started creating a simple module, you will want to use the <a href="<?=user_guide_url('modules/generate#simple')?>">generate command line tool</a> to create a simple module like so:</p>
<pre class="brush: php">
php index.php fuel/generate/simple authors
</pre>
<p>The generate command will do the following:<p>
<ul>
  <li><strong>create table</strong>: creates a generic table based on the name (last parameter) in the database for you with basic fields you will need to edit to fit your model's requirements.</li>
  <li><strong>created model</strong>: creates a basic model as a starting point to add your code.</li>
  <li><strong>add entry to MY_fuel_modules</strong>: will add an entry in the fuel/application/config/MY_fuel_modules.php file so it will appear in the CMS under the Modules area in the left menu.</li>
  <li><strong>add permissions</strong>: permissions for the module get automically generated.</li>
</ul>

<p>However, since this is a tutorial to learn about the creation of simple modules, we will skip the generate functionality and do these tasks by manually.</p>

<h3>Setup the Authors Table</h3>
<p>The following is the SQL code for the table used by authors model. Copy this code and run this query to generate the table in the FUEL database instance you are using for this tutorial.</p>

<pre class="brush: sql">
CREATE TABLE `authors` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `bio` text collate utf8_unicode_ci NOT NULL default '',
  `avatar_image` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
</pre>

<p class="important"><strong>Note the column <kbd>published</kbd>.</strong> The enum columns <kbd>published</kbd> and <kbd>active</kbd>
carry special meaning in FUEL and will determine whether to display the content on the website.
</p>

<h3>Create the Authors Model</h3>
<p>After creating the table, we will create the model that links to that table. To do that, create a file in your 
<dfn>fuel/application/model</dfn> directory and name it <dfn>authors_model.php</dfn>. In that file we will be 
creating two classes. The first class, <dfn>Authors_model</dfn>, will extend the <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> 
(which is an extension of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>).</p>

<p>The second class we will create in the <dfn>authors_model.php</dfn> file is the custom record class, <dfn>Author_model</dfn> (note the singular name).
This class is not required, but it will give us some extra functionality at the record level later on. If this class is not included, the data will be 
returned in an array format by default.</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Authors_model extends Base_module_model {

    function __construct()
    {
        parent::__construct('authors');
    }
}

class Author_model extends Base_module_record {

}
</pre>
  
<p>This is really all you need at the moment for the authors model so we will now focus on getting it it hooked up to the FUEL admin.</p>

<h3>Hooking Up the Authors Module</h3>
<p>Now that our table and model have been created, we need to tell FUEL about it. 
To do that, open up the <dfn>application/config/MY_fuel_modules.php</dfn> file and add the following line.</p>
<pre class="brush: php">
$config['modules']['authors'] = array(); 
</pre>

<p class="important">The parameters for the module are empty for this example. For a complete list of parameters to pass to the module, 
<a href="<?=user_guide_url('modules/simple')?>">click here</a>.</p>

<p>Now login to fuel (e.g. http://mysite.com/fuel), and you should see the module on the left under the MODULES section.</p>

<p class="important">If you do not see the module on the left, make sure you are logged in as an admin,
or that you have the proper <a href="#permissions">permissions</a> to view that module.
</p>

<p>The form fields should look like the following:</p>
<img src="<?=img_path('screens/authors_form.png', FUEL_FOLDER)?>" class="screen" />

<h2 id="articles_module">The Articles Module</h2>
<p>The articles module will contain the content of the articles as well as some meta information including a foreign key to the authors table.</p>

<h3>Setup the Articles Table</h3>
<p>The following is the SQL code we will be using to drive the articles model. Copy this code 
and run this query to generate the table in the FUEL database instance you are using for this tutorial.</p>

<pre class="brush: sql">
CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `slug` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `author_id` tinyint(3) unsigned NOT NULL default '0',
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

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {

    function __construct()
    {
        parent::__construct('articles');
    }
}

class Article_model extends Base_module_record {

}
</pre>

<h4>Adding a Foreign Key Relationship to Authors</h4>
<p>Now that we have our articles model, let's start adding relationships. The first relationship we'll add is a 
foreign key relationship to the authors model. To do this, add a <dfn>$foreign_keys</dfn> property to the Articles_model 
with the key being the the field name in the table and the value being the model it relates to like so:</p>
<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {
    
    public $foreign_keys = array('author_id' => 'authors_model');

    function __construct()
    {
        parent::__construct('articles');
    }
}

class Article_model extends Base_module_record {

}
</pre>
<p>This will do a couple things for us. The first is that it will 
automatically create a select list in the admin with the different options. The second thing it will do is allow
your record objects to magically return foreign objects like so:</p>
<pre class="brush: php">
$articles->author->name;
</pre>

<p class="important">An additional <kbd>where</kbd> parameter can be added to filter the select list by using an array syntax with a key of <kbd>where</kbd>
like so:<br /> <strong>public $foreign_keys = array('author_id' => array('authors_model', 'where' => array('published' => 'yes'));</strong></p>


<h4>Adding a Many-to-Many Relationship With Tags</h4>
<p>The next relationship we want to add to our articles model is a many-to-many relationship with FUEL's built in <a href="<?=user_guide_url('general/tags-categories')?>">Tags module</a>. 
  This means that an article can belong to many different tags and vice-versa.   To do this, we will take advantage of <a href="<?=user_guide_url('general/models#relationships')?>">FUEL 1.0's relationships</a> and
create a <dfn>has_many</dfn> property on the model. Below is an example of how to do this:</p>
<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {
    
    public $foreign_keys = array('author_id' => 'authors_model');
    public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model'));

    function __construct()
    {
        parent::__construct('articles');
    }
}

class Article_model extends Base_module_record {

}
</pre> 
<p>The value should be an array whose keys are the field name you want to associate with the relationship
and the value being either a string with the model's name or an array with the key being the advanced module name the model belongs in and the value being the model's name.
We will use the latter since this relationship is with a model in the <dfn>fuel</dfn> advanced module. By adding this <dfn>has_many</dfn> relationship, it will do a couple things.
First, it will create a <a href="<?=user_guide_url('general/forms#multi')?>">multi</a> select box in the admin to associate tags with the articles.
Additionally, it will allow your record objects to return a list of related data like so:</p>
<pre class="brush: php">
foreach($articles->tags as $tag)
{
  echo $tag->name;
}

// OR return the model object with the "where in" condition already applied
$tags_model = $articles->get_tags(TRUE);
$tags = $tags_model->find_all_assoc('id');
foreach($tags as $key => $tag)
{
  echo $tag->name;
}
</pre>
<p class="important">In the latter example above, the record property <kbd>tags</kbd> can be called as a method as well by pre-pending "get_" in front of the properties name (e.g. get_tags()).
This allows us to pass additional parameters.</p>


<h3>Hooking Up the Articles Module</h3>
<p>Similar to the authors module, we will now need to make an entry in the <dfn>application/config/MY_fuel_modules.php</dfn> file and add the following lines.</p>
<pre class="brush: php">
$config['modules']['articles'] = array(
  'preview_path'   =>  'articles/{slug}',
  'display_field'  => 'title',
  'sanitize_input' => array('template','php'),
); 
</pre>
<ul>
  <li><strong>preview_path</strong>: The <dfn>preview_path</dfn> parameter when added will display a <strong>View</strong> button in the admin which will allow you to click 
  to the page on the site. The <var>{slug}</var> acts as a placeholder and will be swapped out with the <var>slug</var> value of the record being edited.</li>
  <li><strong>display_field</strong>: The <dfn>display_field</dfn> is the main field used to identify the record in the CMS. By default, FUEL will choose the
    second field in your table, so in our case, we really don't need to specify this value since "title" is the second field in our table but we do so 
    for demonstration purposes.</li>
    <li><strong>sanitize_input</strong>: The <dfn>sanitize_input</dfn> parameter is used to clean input before it gets inserted into the database table.
    The default value is <var>TRUE</var> which means it will apply the <a href="http://ellislab.com/codeigniter/user-guide/helpers/security_helper.html" target="_blank">xss_clean</a> 
    function. In some cases, this default value is too restrictive and will not allow you to input javascript or iframe code. So instead, you 
    can specify an array of options that are available from FUEL's <a href="<?=user_guide_url('installation/configuration')?>">module_sanitize_funcs</a> config parameter. In this case, we will use
    the <var>template</var> and <var>php</var> value that maps to FUEL's <a href="<?=user_guide_url('helpers/my_string_helper#func_php_to_template_syntax')?>">php_to_template_syntax</a> and <a href="http://php.net/manual/en/function.htmlentities.php" target="_blank">htmlentities</a> function respectively.</li>
</ul>
<p class="important">For additional list of module parameters, <a href="<?=user_guide_url('modules/simple')?>">click here</a>.</p>


<h3>Changing the List View's Columns</h3>
<p>There are a few additional things we need to do to have this module work appropriately. The first
is to change the list_items method which is used to display the list of data in the admin. This method is 
inherited by the <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> class.
Below we will use CI's active record syntax and change the method so that author_id column will actually display the author's name and the
content column will be truncated to 50 characters and html encoded. This is how we do it:</p>

<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {

    public $foreign_keys = array('author_id' => 'authors_model');
    public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model'));

    function __construct()
    {
        parent::__construct('articles');
    }
  
    function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc', $just_count = FALSE)
    {
        $this->db->join('authors', 'authors.id = articles.author_id', 'left');
        $this->db->select('articles.id, title, SUBSTRING(content, 1, 50) AS content, authors.name AS author, date_added, articles.published', FALSE);
        $data = parent::list_items($limit, $offset, $col, $order, $just_count);

        // check just_count is FALSE or else $data may not be a valid array
        if (empty($just_count))
        {
          foreach($data as $key => $val)
          {
            $data[$key]['content'] = htmlentities($val['content'], ENT_QUOTES, 'UTF-8');
          }
        }
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
<p class="important">Although we show default <kbd>$col</kbd> and <kbd>$order</kbd> values, those need to be set on the <kbd>default_col</kbd> and <kbd>default_order</kbd> <a href="<?=user_guide_url('modules/simple')?>">module</a> to properly sort the list items.</p>
<br/>
<p>Now login to the CMS and click on the "Articles" module in the left hand menu to see the list view of the module.</p>


<h3>Setting Up the Articles Module Form</h3>
<p>Now that we've edited the method for the list view in the admin, click on the Create button from the articles list view you will see the form fields for your module.
The form fields are generated by the articles's <dfn>form_fields</dfn> method. This method is inherited from <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> 
class and gets passed to a <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> instance, 
and should return an array with keys being the form field names and values being an array of form field parameters. 
More on different form field types and parameters can be found under the <a href="<?=user_guide_url('general/forms')?>">forms documentation</a>.
</p>

<p>Below is the default field type mapping generated by the <dfn>form_fields</dfn> method.</p>

<ul>
  <li><strong>id</strong>: <a href="<?=user_guide_url('general/forms#hidden')?>">hidden field</a> (Form_builder is set by default to render all fields with the name of "id" as hidden)</li>
  <li><strong>title</strong>: <a href="<?=user_guide_url('general/forms#hidden')?>">title field</a> (a varchar field type gets mapped to a normal input text field)</li>
  <li><strong>slug</strong>: <a href="<?=user_guide_url('general/forms#slug')?>">slug field</a> (a field with the name of "slug" is <a href="<?=user_guide_url('general/forms#representatives')?>">represented</a> by the field type "slug"). </li>
  <li><strong>author_id</strong>: <a href="<?=user_guide_url('general/forms#select')?>">select field</a> (the select field is set by the model's <dfn>foriegn_keys</dfn> property</li>
  <li><strong>content</strong>: <a href="<?=user_guide_url('general/forms#wysiwyg')?>">wysiwyg field</a> (text field type gets <a href="<?=user_guide_url('general/forms#representatives')?>">represented</a> by the field type "wysiwyg")</li>
  <li><strong>image</strong>: <a href="<?=user_guide_url('general/forms#asset')?>">asset field</a> (a field containing "image" or "img" in it's name gets <a href="<?=user_guide_url('general/forms#representatives')?>">represented</a> by the field type "asset")</li>
  <li><strong>thumb_image</strong>: <a href="<?=user_guide_url('general/forms#asset')?>">asset field</a> (a field containing "image" or "img" in it's name gets <a href="<?=user_guide_url('general/forms#representatives')?>">represented</a> by the field type "asset")</li>
  <li><strong>date_added</strong>: <a href="<?=user_guide_url('general/forms#hidden')?>">hidden field</a> (<a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a> automatically sets a model's <dfn>auto_date_add</dfn> and <dfn>auto_date_update</dfn> fields to hidden. <dfn>date_added</dfn> is a default for auto_date_add)</li>
  <li><strong>published</strong>: <a href="<?=user_guide_url('general/forms#enum')?>">enum field</a> (an enum column type gets mapped to a Form_builders enum field type)</li>
</ul>

<p>There are a few modifications we'd like to make. The first thing we'd like to change is the image selection/upload button on the content field's wysiwyg editor to
  pull from the folder <span class="file">assets/images/articles</span> instead of the default <span class="file">assets/images</span> folder. To make this change,
  you'll need to create that folder and make sure it is writable by PHP. Then you can add the following parameter to the content field in hte form_fields method:</p>
<pre class="brush: php">
  function form_fields($values = array())
    {
        $fields = parent::form_fields($values);
        
        // ******************* ADD CUSTOM FORM STUFF HERE ******************* 
        $fields['content']['img_folder'] = 'articles/';
        return $fields;
    }
  
</pre>

<br/ >

<p>The second modification is changing the folder in which the <dfn>image</dfn> and <dfn>thumb_image</dfn> select and/or upload to. To do that
we add the <var>folder</var> parameter with the name of the asset folder:</p>
<pre class="brush: php">
  $fields['image']['folder'] = 'images/articles/';
  $fields['thumb_image']['folder'] = 'images/articles/thumbs/';
</pre>
<p class="important">Again, these folders must be writable or it will default to the images folder.</p>

<br />
<p>Below is what the model looks like now:</p>

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

    function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc', $just_count = FALSE)
    {
        $this->db->join('authors', 'authors.id = articles.author_id', 'left');
        $this->db->select('articles.id, title, SUBSTRING(content, 1, 50) AS content, authors.name AS author, date_added, articles.published', FALSE);
        $data = parent::list_items($limit, $offset, $col, $order, $just_count);

        // check just_count is FALSE or else $data may not be a valid array
        if (empty($just_count))
        {
          foreach($data as $key => $val)
          {
            $data[$key]['content'] = htmlentities($val['content'], ENT_QUOTES, 'UTF-8');
          }
        }
        return $data;
    }

    function form_fields($values = array())
    {
        $fields = parent::form_fields($values);
        
        // ******************* ADD CUSTOM FORM STUFF HERE ******************* 
        $fields['content']['img_folder'] = 'images/articles/';
        $fields['image']['folder'] = 'images/articles/';
        $fields['thumb_image']['folder'] = 'images/articles/thumbs/';

        return $fields;
    }
}

class Article_model extends Data_record {

}
</pre>
<p>Below is what the articles form should look like:</p>
<img src="<?=img_path('screens/articles_form.png', FUEL_FOLDER)?>" class="screen" />


<h2 id="fuel_tags_module">The FUEL Tags Module</h2>
<p>As we alluded to earlier, we will use FUEL's built in <a href="<?=user_guide_url('general/tags-categories')?>">tags module</a> to create the many-to-many relationships model with the articles module.
The <dfn>fuel_tags_model</dfn>, along with it's cousin the <dfn>fuel_tags_categories</dfn> model are part of the FUEL <a href="<?=user_guide_url('general/tags-categories')?>">tags and categories</a> modules that
are new to 1.0. These modules allow you group related models together without the need to create  additional models.
These modules are not visible by default and must be to enabled to be visible in the CMS. To do so, go to the 
<span class="file">fuel/application/config/MY_fuel_modules.php</span> and comment out or remove the following module overwrites:</p>

<pre class="brush: php">
$config['module_overwrites']['categories']['hidden'] = FALSE; // change to TRUE if you want to use the generic categories module
$config['module_overwrites']['tags']['hidden'] = FALSE; // change to TRUE if you want to use the generic categories module
</pre>


<p>Now that you've unhidden the categories module, go back to the admin and refresh. You should see a "Tags" module appear on the left menu. 
  Click on it and click the "Create" button. You will see that a FUEL tag has the following fields:</p>
<img src="<?=img_path('screens/tags_create.png', FUEL_FOLDER)?>" class="screen img_right" />
<ul>
  <li><strong>name</strong>: The name of the tag</li>
  <li><strong>category_id</strong>: The FUEL category it belongs to. This can come in handy when you want to group tags together. For example, you could create an "articles" category to group the tags together.</li>
  <li><strong>slug</strong>: A unique slug value associated with the tag.</li>
  <li><strong>precedence</strong>: A sorting value to associate with the tag.</li>
  <li><strong>published</strong>: Determines whether the tag should be displayed or not on the front end.</li>
</ul>
<p>It will also show a multi select field for all models that have a has_many link to the tags. So in this case, you should see a Belongs to Articles multi select field too.</p>

<p>That's it for tags.</p>

<br clear="both" />

<h2 id="permissions">Creating the Permissions</h2>
<img src="<?=img_path('screens/manage_permissions.png', FUEL_FOLDER)?>" class="screen img_right" />
<p>Now that the modules are created, you will probably want to allow others to use them. To do this,
you will need to access to the permissions module to create permissions so users can be allowed to access those modules in FUEL.
To do that</p>
<ol>
  <li>Click on the permissions link on the left and create the permission <dfn>authors</dfn>. 
    Keep the "create", "edit", "publish" and "delete" checkboxes checked for the Generate simple module permissions and click "Save".
    This will create a permission for users to be subscribed to. </li>
  <li>To assign the permission to a user, click the users link on the left menu, then select a user. </li>
  <li>Once in the edit user screen, check the box for the new authors permission you just created.</li>
</ol>
<p>Repeat these step to create the articles permissions.</p>



<h2 id="polishing">A Little Polishing</h2>
<p>Now that we have the authors, articles and tags modules setup, it's time to add a little polish. This will include adding the following:</p>
<ol>
  <li>Required fields</li>
  <li>Unique fields</li>
  <li>Parsed fields</li>
  <li>Filter fields</li>
  <li>Tree view</li>
  <li>Record specific methods</li>
</ol>

<h3>Required Fields</h3>
<p>The first thing we will do is add some required fields to both the Authors and Articles model like so:</p>

<p>For the authors model, we will make the <dfn>name</dfn> and <dfn>email</dfn> fields required.</p>
<pre class="brush: php">
... 
class Authors_model extends Base_module_model {
  
  public $required = array('name', 'email');

...
</pre>

<p>For the articles model, we will make the <dfn>title</dfn> field required. The slug is also required however, it will be automatically generated if left blank so it is not necessary to add it.</p>
<pre class="brush: php">
... 
class Articles_model extends Base_module_model {
  
  public $required = array('title');
  public $foreign_keys = array('author_id' => 'authors_model');
  public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model'));

...
</pre>

<h3>Unique Fields</h3>
<p>FUEL model's can also declare fields that need to be unique in addition to the primary key. In this example, the article's <dfn>slug</dfn> value needs to be unique across all articles.</p>
<pre class="brush: php">
... 
class Articles_model extends Base_module_model {
  
  public $required = array('title');
  public $unique_fields = array('slug');
  public $foreign_keys = array('author_id' => 'authors_model');
  public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model'));

...
</pre>
<p class="important">If you have a combination of fields that need to be unique (e.g. a compound key), you can specify an array value instead containing all the fields that need to be unique.</p>


<h3>Parsed FIelds</h3>
<p>By default, FUEL will not parse the data for <a href="<?=user_guide_url('general/template-parsing')?>">templating syntax</a>. 
  To fix this you add the <dfn>$parsed_fields</dfn> model array property with the array values the names of fields that should be parsed upon retrieval.
  In this example, we'll want the article model's <dfn>content</dfn> field to be parsed so we add the following:
</p>
<pre class="brush: php">
... 
class Articles_model extends Base_module_model {
  
  public $required = array('title');
  public $unique_fields = array('slug');
  public $parsed_fields = array('content');
  public $foreign_keys = array('author_id' => 'authors_model');
  public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model'));

...
</pre>

<h3>Filtered FIelds</h3>
<p>The <dfn>filters</dfn> property allows you to specify additional fields that will be search from the list view in the admin. There is also a <dfn>filters_join</dfn> property
you can specify either "and" or "or" which will determine how the where condition in the search query is formed (e.g. title AND content vs. title OR content). For more complicated 
where conditions you can specify a key value array with the key being the filter field and the value being either "or" or "and" (e.g. array('title' => 'or', 'content' => 'and')). 
For this example, we will leave it as the default "or":</p>
<pre class="brush: php">
... 
class Articles_model extends Base_module_model {
  
  public $filters = array('content');
  public $filters_join = 'or';
  public $required = array('title');
  public $unique_fields = array('slug');
  public $parsed_fields = array('content');
  public $foreign_keys = array('author_id' => 'authors_model');
  public $has_many = array('tags' => array(FUEL_FOLDER => 'fuel_tags_model'));

...
</pre>

<h3>Tree View</h3>
<p>You may have noticed that in some of the modules like Pages and Navigation, there is a "Tree" button on the list view. This button appears when a <dfn>tree</dfn> method
has been implemented on the model. The tree method will display the data in a hierarchical tree format as opposed to the list view. 
The method must return an array that follows the <a href="<?=user_guide_url('libraries/menu')?>">Menu</a> hierarchical structure. The Base_module_model can make this
tree view easy if you have implemented a <dfn>foreign_keys</dfn>, <dfn>has_many</dfn> or <dfn>belongs_to</dfn> property on your model. There is a protected <dfn>_tree</dfn>
method (note the preceding underscore) that can be called in which you pass it one of those values. For this example, we want a tree view that groups the articles
based on the tags. </p>
<pre class="brush: php">
... 
function tree()
{
    return $this->_tree('has_many');
}
...
</pre>
<p></p>
<p class="important">You could create a tree view grouped by authors if you pass the <dfn>_tree</dfn> method "foreign_keys" instead.</p>

<p>The tree method will render like the following in the FUEL admin:</p>
<img src="<?=img_path('screens/articles_tree.png', FUEL_FOLDER)?>" class="screen" />


<h3>Adding to the Record Object</h3>
<p>The last addition we will make to the articles model will be to add several <strong>record methods</strong>.
The first will be <dfn>get_url</dfn> which will give us a single place to control URLs to articles.
The second and third methods will be to generate proper image paths for the a view:</p>

<pre class="brush: php">
... 
class Article_model extends Data_record {
  
  public function get_url()
  {
    return site_url('articles/'.$this->slug);
  }

  public function get_image_path()
  {
    return img_path('images/articles/'.$this->image);
  }

  public function get_thumb_image_path()
  {
    return img_path('images/articles/'.$this->thumb_image);
  }

}
...
</pre>

<p>In a view the code may look like this:</p>
<pre class="brush: php">
&lt;img src="&lt;?=$article->image_path?&gt;" alt="&lt;?=$article->title_entities?&gt;
</pre>
<p class="important">Note that <dfn>title_entities</dfn> isn't actually a field. This is using <a href="<?=user_guide_url('general/models#formatters')?>">model formatters</a> to generate entitied output.</p>

<br />

<h2 id="views">Bringing it All Into View</h2>
<p>Now that our modules are created for FUEL, we now can add the module's model data to our views.
Although we could use controllers to marshall the data to the views, for our example, we will just incorporate that logic in the view itself 
(<a href="<?=user_guide_url('general/opt-in-controllers')?>">read more about the opt-in controller philosophy</a>). 
In our view we will have a page that will either display articles based on a slug or list all of them grouped by tags.
To start, create a file called <dfn>articles.php</dfn> and place it in your <dfn>application/views</dfn> folder. Open it up and put the following code in it.
</p>
<pre class="brush: php">
&lt;?php 
$slug = uri_segment(2);

if ($slug) :

  $article = fuel_model(&#039;articles&#039;, array(&#039;find&#039; =&gt; &#039;one&#039;, &#039;where&#039; =&gt; array(&#039;slug&#039; =&gt; $slug)));

  if (empty($article)) :
    redirect_404();
  endif;

else:

  $tags = fuel_model(&#039;tags&#039;);

endif;

if (!empty($article)) : ?&gt;
  
  &lt;h1&gt;&lt;?=fuel_edit($article)?&gt;&lt;?=$article-&gt;title?&gt;&lt;/h1&gt;
  &lt;div class=&quot;author&quot;&gt;&lt;?=$article-&gt;author-&gt;name?&gt;&lt;/div&gt;
  &lt;img src=&quot;&lt;?=$article-&gt;image_path?&gt;&quot; alt=&quot;&lt;?=$article-&gt;title_entities?&gt;&quot; class=&quot;img_right&quot; /&gt;
  &lt;article&gt;&lt;?=$article-&gt;content_formatted?&gt;&lt;/article&gt;


&lt;?php else: ?&gt;

  &lt;h1&gt;Articles&lt;/h1&gt;
  &lt;?=fuel_edit(&#039;create&#039;, &#039;Create Article&#039;, &#039;articles&#039;)?&gt;
  &lt;?php foreach($tags as $tag) : ?&gt;
  &lt;h2&gt;&lt;?=$tag-&gt;name?&gt;&lt;/h2&gt;
  &lt;ul&gt;
    &lt;?php foreach($tag-&gt;articles as $article) : ?&gt;
    &lt;li&gt;&lt;?=fuel_edit($article)?&gt;&lt;a href=&quot;&lt;?=$article-&gt;url?&gt;&quot;&gt;&lt;?=$article-&gt;title?&gt;&lt;/a&gt;&lt;/li&gt;
    &lt;?php endforeach; ?&gt;
  &lt;/ul&gt;
  &lt;?php endforeach; ?&gt;

&lt;?php endif; ?&gt;

</pre>


<h3>Analyzing the View Code</h3>
<p>The first line of code grabs the slug value from the URI path. We then check to see if that slug value exists and if it does, we use 
<a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_model')?>">fuel_model</a> to retrieve the article object. This line is the equivalent to:</p>
<pre class="brush: php">
$CI->load->model('articles_model');
$article = $CI->articles_model->find_one(array('slug' => $slug));
</pre>

<p>If an article with the passed slug value doesn't exist in the table, then it will call the <a href="<?=user_guide_url('helpers/my_url_helper#redirect_404')?>">redirect_404 function</a> which will first look for any <a href="<?=user_guide_url('general/redirects')?>">redirects</a> and if none are
found, will display the 404 error page. If an article does exist, then it continues down the page and displays the contents of the article by outputing the title, image, author name and content.</p>

<p>If no slug value is passed to the page, then a simple <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_model')?>">fuel_model</a> call will be used which will automatically grab all tags associated with articles.
We then loop through those tags and because of the <dfn>has_many</dfn> association with the tags module, we are able to easily grab all articles associated with that tag which allows us to create 
a linked list item for each article.
</p>


<h3>Finding the View</h3>
<p>You may have noticed that you will still get a 404 error page if you go to articles/{slug} (where "slug" is a published records slug value in the admin).
This is because we still need to tell FUEL to use this view file for any URI location of <strong>articles/{slug}</strong>. 
There are several ways to do this <strong>and you only need to implement one of them below</strong>:
<ol>
  <li><strong>max_page_params</strong>: you can add the following to FUEL configuration parameter to <span class="file">fuel/application/views/_variables/MY_fuel.php</span>
    <pre class="brush: php">
    $config['max_page_params'] = array('articles' => 1);
    </pre>
  </li>
  <li><strong>auto_search_views</strong>: you can add the following to FUEL configuration parameter to <span class="file">fuel/application/views/_variables/MY_fuel.php</span>
    <pre class="brush: php">
    $config['auto_search_views'] = TRUE;
    </pre>

  </li>
  <li><strong>global variables file</strong>: you can add the following to <span class="file">fuel/application/views/_variables/global.php</span>
    <pre class="brush: php">
    $pages['articles/:any'] = array('view' => 'articles');
    </pre>
  </li>
  <li><strong>articles variables file</strong>: since this view file exists at the first URI segment of "articles" you can create a separate <span class="file">fuel/application/views/_variables/articles.php</span> variables file add the following:
    <pre class="brush: php">
    $vars['view'] = 'articles';
    </pre>
  </li>
</ol>


<h3>Inline Editing</h3>
<p>The very last thing we will do is add <a href="<?=user_guide_url('general/inline-editing')?>">inline editing</a> to our view. To do this we use the <a href="helpers/fuel_helper">fuel_edit</a> function. We'll
place that function right inside the &lt;h1&gt; tag in the article view and inside the &lt;li&gt; in the list view. </p>

<pre class="brush: php">
...
&lt;h2&gt;&lt;?=fuel_edit($article)?&gt;&lt;?=$article-&gt;title?&gt;&lt;/h2&gt;
...
</pre>

<p>Additionally, you may add items in the context of a page. To do that, you pass <dfn>create</dfn> as the first parameter.</p>
<pre class="brush: php">
&lt;h2&gt;&lt;?=fuel_edit('create', 'Create', 'articles')?&gt;&lt;?=$article-&gt;title?&gt;&lt;/h2&gt;
</pre>
<p>Next, make sure the FUEL bar located in the upper right of the page has the pencil icon toggled on. You should now see pencil icons that when clicked, will allow you to edit or add article data from within the context of the page.</p>
<img src="<?=img_path('screens/page_inline_editing.jpg', FUEL_FOLDER)?>" class="screen" />
<p class="important"><a href="<?=user_guide_url('general/inline-editing')?>">Click here for more on inline editing</a></p>
<br />


<p><strong>That's it!</strong></p>


<h2>Now What?</h2>
<p>If you are wanting to create more advanced modules, <a href="<?=user_guide_url('modules/advanced')?>">click here</a>. If you have any question, <a href="http://www.getfuelcms.com/forums">meet us in the forum</a>.</p>