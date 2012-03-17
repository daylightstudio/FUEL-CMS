<h1>Pages &amp; Variables</h1>
<p>FUEL CMS provides several ways for creating pages. To help illustrate, we'll pretend we are creating a simple website. Our website will have the following pages:</p>
<ul>
	<li><a href="#static"><strong>about</strong>: static page</a></li>
	<li><a href="#editable"><strong>about/team</strong>: editable page</a></li>
	<li><a href="#controller"><strong>about/contact</strong>: controller page</a></li>
	<li><a href="#news"><strong>news</strong>: module page</a></li>
</ul>

<p class="important">Make sure the configuration of <kbd>fuel_mode</kbd> is set to <dfn>AUTO</dfn> in the <dfn>application/config/MY_fuel.php</dfn> file.</p>


<a name="static"></a>
<h2>Creating a Static Page - about</h2>

<h3>Opt-In Controller Method</h3>
<p>There are several ways to create a page in FUEL. The easest way is to simply create a view file. We call this method the <a href="<?=user_guide_url('general/opt-in-controllers')?>">Opt-In Controller</a> Method. This allows you to create pages without the need for a controller. A URI path of <strong>about/contact</strong> would have a view file created at <strong>views/about/contact.php</strong>. The method has some extra benefits like having deep path structures and hyphens in the path without the need of routing.</p>

<p>For this tutorial we will make the <dfn>about</dfn> page a static view file. To do that, create the following file at <dfn>application/views/about.php</dfn>: </p>

<pre class="brush: php">
<h1>Hello World</h1>
<p>This is our about page</p>
</pre>

<p>Note how the <dfn>about</dfn> page should automatically have a header and footer applied to it. This is because the <dfn>application/views/_variables/global.php</dfn> file specifies the <dfn>main</dfn> layout file to be applied to it (more about passing variables below). Layout files exist in the <dfn>application/views/_layouts/</dfn> folder (more about layouts below as well).</p>

<p class="important">The <kbd>home</kbd> view file is reserved as the index page for the site.</p>

<h3>Passing Variables to a Page</h3>
<p>FUEL provides several ways to pass variables to view files without the need of creating controllers. By default, the <dfn>$vars</dfn> array variable in the <dfn>application/views/_variables/global.php</dfn> file gets passed to every page created using the <a href="<?=user_guide_url('general/opt-in-controllers')?>">Opt-In Controller</a>
Method. For this tutorial, we would like add an additional variable to be used on each page called <dfn>$bigpic</dfn>. We want this variable to represent the top main picture of the page and change for each section of the site. We add this variable to the <dfn>application/views/_variables/global.php</dfn> file as seen below:</p>

<pre class="brush: php">

// declared here so we don't have to in controller variable files'
$CI =& get_instance();

// generic global page variables used for all pages
$vars = array();
$vars['layout'] = 'main';
$vars['page_title'] = '';
$vars['meta_keywords'] = '';
$vars['meta_description'] = '';
$vars['js'] = '';
$vars['css'] = '';
$vars['body_class'] = $CI-&gt;uri->segment(1).' '.$CI-&gt;uri-&gt;segment(2);

// big pic image at top of the site
$vars['bigpic'] = 'my_bigpic.jpg';

// page specific variables
$pages = array();

</pre>

<p>We then create an <dfn>application/views/_variables/about.php</dfn> variables file and add the following "controller-level" variable which will apply the $bigpic value of <dfn>about_bigpic.jpg</dfn> to all pages in the about section, overriding the global variables file:
</p>

<pre class="brush: php">
// big pic image at top of the site
$vars['bigpic'] = 'about_bigpic.jpg';
</pre>



<a name="controller"></a>
<h2>Using Controllers - about/contact</h2>
<p>FUEL still allows you to create pages using the standard MVC way in CodeIgniter. For the purposes of this tutorial, we will use a controller for the <dfn>about/contact</dfn> because it has a webform we may want to use for processing. We can also leverage the variables specified in the <dfn>application/views/_variables/</dfn> file like so:</p>

<pre class="brush:php">
class About extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function contact()
	{
		// set your variables
		$vars = array('page_title' => 'Contact : My Website');

		//... form code goes here
		
		// load the fuel_page library class and pass it the view file you want to load
		$this->load->module_library(FUEL_FOLDER, 'fuel_page', array('location' => 'about/contact'));
		$this->fuel_page->add_variables($vars);
		$this->fuel_page->render();
		
	}
}
</pre>


<a name="editable"></a>
<h2>Creating an Editable Page - about/team</h2>
<p>For the create <dfn>about/team</dfn> page we will login to the FUEL admin by going to 
<dfn>http://mysite.com/fuel</dfn> (where <strong>mysite.com</strong> is your domain) and entering the username of <dfn>admin</dfn> with the password (which is probably <dfn>admin</dfn> as well if you haven't changed it). Click on the <dfn>Pages</dfn> menu item on the left and follow the directions 
<a href="<?=user_guide_url('modules/fuel/pages')?>">seen here</a> to create a page. For the <dfn>location</dfn> value type in <strong>about/team</strong> and for the <dfn>body</dfn> value, put in the value below, click save and then view the page:</p>

<pre class="brush: php">
<h1>Team</h1>
<p>This is our team page</p>
</pre>

<a name="news"></a>
<h2>Module Page: news</h2>
<p>A module page is a combination of a static page and a module that is used to generate the contents of that page. This page
will be the most involved
For this tutorial, we will have a news page that will list all the news items and a news detail page that will show the contents of those news items. 
This requires the following:</p>
<ul>
	<li>A database table</li>
	<li>A model</li>
	<li>Modify the <dfn>application/config/MY_fuel_modules.php</dfn> file</li>
	<li>A view file</li>
	<li>A variables file</li>
</ul>

<h3>SQL Database Table</h3>
<p>Run the following SQL statement to create the news table.</p>
<pre class="brush: php">
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `headline` varchar(255) collate utf8_unicode_ci NOT NULL,
  `slug` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `release_date` date NOT NULL,
  `content` text collate utf8_unicode_ci NOT NULL,
  `link` varchar(255) collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `permalink` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
</pre>

<h3>News Model</h3>
<p>The following is the news model we are using. For a tutorial on creating modules specifically <a href="<?=user_guide_url('modules/tutorial')?>">click here</a>.</p>
<pre class="brush: php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class News_model extends Base_module_model {

	public $required = array('headline');
	public $record_class = 'News_item';
	public $parsed_fields = array('content');
	
	function __construct()
	{
		parent::__construct('news'); // table name
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'release_date', $order = 'desc')
	{
		$this-&gt;db-&gt;select('id, headline, slug, published');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}


	function on_before_clean($values)
	{
		if (empty($values['slug'])) $values['slug'] = url_title($values['headline'], 'dash', TRUE);
		if (!intval($values['release_date'])) $values['release_date'] = datetime_now();
		return $values;
	}

	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		$fields['slug']['comment'] = 'If no slug is provided, one will be provided for you';
		$fields['release_date']['comment'] = 'A release date will automatically be created for you of the current date if left blank';
		return $fields;
	}

	function _common_query()
	{
		parent::_common_query(); // to do active and published
		$this-&gt;db-&gt;order_by('release_date desc');
	}
}

class News_item_model extends Base_module_record {

	protected $_date_format = 'F d, Y';
	
	function get_url()
	{
		if (!empty($this-&gt;link)) return $this-&gt;link;
		return site_url('news/'.$this-&gt;slug);
	}
	
	function get_excerpt_formatted($char_limit = NULL, $readmore = '')
	{
		$this-&gt;_CI-&gt;load-&gt;helper('typography');
		$this-&gt;_CI-&gt;load-&gt;helper('text');
		$excerpt = $this-&gt;content;

		if (!empty($char_limit))
		{
			// must strip tags to get accruate character count
			$excerpt = strip_tags($excerpt);
			$excerpt = character_limiter($excerpt, $char_limit);
		}
		$excerpt = auto_typography($excerpt);
		$excerpt = $this-&gt;_parse($excerpt);
		if (!empty($readmore))
		{
			$excerpt .= ' '.anchor($this-&gt;get_url(), $readmore, 'class="readmore"');
		}
		return $excerpt;
	}
	
}
?&gt;
</pre>

<h3>MY_fuel_modules.php Configuration Change</h3>
<p>Add the following module to the <dfn>application/config/MY_fuel_modules.php</dfn> file: </p>
<pre class="brush: php">
$config['modules']['news'] = array(
	'preview_path' => 'news/{slug}'
);
</pre>


<h3>View File</h3>
<p>You could use a controller to do the url segment logic but for this tutorial, we will just use a single view. 
The following view file uses the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_model</a> and 
<a href="<?=user_guide_url('libraries/my_model/data_record_class_functions')?>">custom record objects</a>.</p>
<pre class="brush: php">
&lt;?php 
$slug = uri_segment(2);
if (!empty($slug))
{
	$news_item = fuel_model('news', array('find' => 'one', 'where' => array('slug' => $slug)));
	if (empty($news_item)) show_404();
}
else
{
	$news = fuel_model('news');
}
?&gt;

<h1>News</h1>

&lt;?php if (!empty($news_item)) : ?&gt;

&lt;?php fuel_set_var('page_title', $news_item->headline); ?&gt;

&lt;div class="news_item"&gt;
	&lt;h2&gt;&lt;?=$news_item-&gt;headline?&gt;&lt;/h2&gt;
	&lt;div class="date"&gt;&lt;?=$news_item-&gt;release_date_formatted?&gt;&lt;/div&gt;
	&lt;?=$news_item-&gt;content_formatted?&gt;
&lt;/div&gt;

&lt;?php else: ?&gt;


&lt;?php foreach($news as $item) : ?&gt;

&lt;div class="news_item"&gt;
	&lt;h2&gt;&lt;a href="&lt;?=$item-&gt;url?&gt;"&gt;&lt;?=$item-&gt;headline?&gt;&lt;/a&gt;&lt;/h2&gt;
	&lt;div class="date"&gt;&lt;?=$item-&gt;release_date_formatted?&gt;&lt;/div&gt;
	&lt;?=$item-&gt;get_excerpt_formatted(300, 'Read Full Story &raquo;')?&gt;

	&lt;hr /&gt;
&lt;/div&gt;

&lt;?php endforeach; ?&gt;

&lt;?php endif; ?&gt;

</pre>

<h3>News Variables File</h3>
<p>The last thing we will do is create a news variables file and use the special variable <dfn>view</dfn>
to specify the news view file for any page under the news section. This will allow us to use the same view file for 
both the list view and detail view of the news.</p>

<pre class="brush: php">
&lt;?php 
$vars['news'] = array('view' => 'news');
?&gt;
</pre>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>



<p>
Pages in FUEL CMS are a combination of <a href="#">variables</a>, <a href="#">views</a>, <a href="#">layouts</a> and <a href="#">blocks</a>, and represent a specific URI location (e.g. http://mysite.com/about). 
They can be generated one of several ways including:
</p>

<ol>
	<li><strong>"Opt-in Controller Development" method</strong> - a static view file located in the <dfn>fuel/application/views</dfn> folder</li>
	<li><strong>Controller</strong> - Using the normal <a href="http://codeigniter.com/user_guide/general/views.html" target="_blank">CodeIgniter Controller/View</a></li>
	<li><strong>CMS</strong> - created in the CMS</li>
</ol>


<h2>Creating Pages with Views</h2>
<p></p>
<ol>
	<li><strong>"Opt-in Controller" method</strong> - a static view file located in the <dfn>fuel/application/views</dfn> folder</li>
	<li><strong>Controller</strong> - Using the normal <a href="http://codeigniter.com/user_guide/general/views.html" target="_blank">CodeIgniter Controller/View</a></li>
	<li><strong>Using a </strong> - created in the CMS</li>
</ol>

<h3>"Opt-in Controller" Method</h3>


<p>When creating pages, we normally use the <a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">Opt-in Controller Development</a> method to create pages. In short, this method maps a URI location to a view file that acts as the <dfn>body</dfn> of the page without the need of creating an additional corresponding controller method. We like to use controllers only for processing more complicated logic like submitting forms.</p>

<p class="important"><strong>TIP:</strong> putting an underscore before your file or folder name tells FUEL to ignore that file/folder when searching for a view to render.</p>

<h2>Creating Pages with the CMS</h2>


<h2>Site Structure</h2>
<p>To help give us some context, let's review the <a href="http://www.getfuelcms.com/demo/" target="_blank">WidgiCorp</a> site structure and how the pages are implemented.</p>
<ul>
	<li><a href="http://www.getfuelcms.com/demo/" target="_blank">Home</a> - static view file with no layout</li>
	<li><a href="http://www.getfuelcms.com/demo/about" target="_blank">About</a> - static view file that uses the <dfn>main</dfn> layout
		<ul>
			<li><a href="http://www.getfuelcms.com/demo/about/services" target="_blank">Services</a> - static view file that uses the <dfn>main</dfn> layout</li>
			<li><a href="http://www.getfuelcms.com/demo/about/team" target="_blank">Team</a> - static view file that uses the <dfn>main</dfn> layout</li>
			<li><a href="http://www.getfuelcms.com/demo/about/what-they-say" target="_blank">What They Say</a> - static view file that uses the <dfn>main</dfn> layout</li>
		</ul>
	</li>
	<li><a href="http://www.getfuelcms.com/demo/blog" target="_blank">Blog</a> - powered by the blog module (<span class="file">fuel/modules/blog</span>)</li>
	<li><a href="http://www.getfuelcms.com/demo/showcase" target="_blank">Showcase</a> - contains a static list view and detail view that pulls data from the projects table</li>
	<li><a href="http://www.getfuelcms.com/demo/contact" target="_blank">Contact</a> - uses contact controller (<span class="file">fuel/application/controllers/contact.php</span>)</li>
</ul>

<p class="important">
	All the static view file pages above can be imported into the CMS. We will show this in <em>Part 4: Using the CMS</em>.
</p>

<h2>Creating Pages</h2>
<p>Pages are a combination of views, layouts and blocks, and represent a specific URI location (e.g. http://mysite.com/about). They can be either a static view file located in the <dfn>fuel/application/views</dfn> folder, or it can be created in the CMS. For the purpose of the first part of this tutorial, we will only be dealing with the static view files. We'll discuss the latter when we get to <em>Part 4: Using the CMS</em> but if you are really wanting to know how to create pages in the CMS you can glean some information from the <a href="http://www.getfuelcms.com/user_guide/modules/fuel/pages" target="_blank">user guide</a>. The reason we usually create static view files and then import them later is because it's much quicker and more comfortable for us to work in our favorite text editor.</p>

<p>When creating pages, we normally use the <a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">Opt-in Controller Development</a> method to create pages. In short, this method maps a URI location to a view file that acts as the <dfn>body</dfn> of the page without the need of creating an additional corresponding controller method. We like to use controllers only for processing more complicated logic like submitting forms.</p>

<p class="important"><strong>TIP:</strong> putting an underscore before your file or folder name tells FUEL to ignore that file/folder when searching for a view to render.</p>


<h3 id="page_home">The Home Page</h3>
<p>The WidgiCorp homepage HTML can be viewed in the <span class="file">fuel/application/views/home</span> file. Typically, the homepage layout is unique to a website. Therefore, we normally don't use a layout for the homepage and this is the case for the <a href="http://www.getfuelcms.com/demo/" target="_blank">WidgiCorp</a> site. There are a couple of ways to do this in FUEL. The first way is to create a <a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">variables file</a> and specify an empty value for the layout like so:</p>

<span class="file">fuel/application/views/_variables/home.php</strong> (this page doesn't exist in the demo)</span>
<pre class="brush: php">
$vars['layout'] = '';
</pre>

<p>The second way, and the way we've implemented it with WidgiCorp, is to specify the layout to be a blank variable from within the view file using <dfn>fuel_set_var()</dfn> like so:</p>

<span class="file">fuel/application/views/home.php</span>
<pre class="brush: php">
&lt;?=fuel_set_var('layout', '')?&gt;
</pre>

<p>Continuing the examination of the home view file, you'll see that below the <dfn>&lt;?=fuel_set_var('layout', '')?&gt;</dfn> there is logic to display the install page which we can ignore. Below the install logic, we simply load the header block file like so (we will talk more about blocks, including the header block, in a bit):</p>

<pre class="brush: php">
&lt;?php $this-&gt;load-&gt;view('_blocks/header')?&gt;
</pre>

<p> Further down, you'll see where we load in the latest blog posts from FUEL. No need for <a href="http://simplepie.org" target="_blank">SimplePie</a> here!</p>
	
<pre class="brush: php">
&lt;?php $posts = fuel_model('blog_posts', array('find' =&gt; 'all', 'limit' =&gt; 3, 'order' =&gt; 'sticky, date_added desc', 'module' =&gt; 'blog')) ?&gt;
&lt;?php if (!empty($posts)) : ?&gt;
&lt;h2&gt;The Latest from our Blog&lt;/h2&gt;
&lt;ul&gt;
&lt;?php foreach($posts as $post) : ?&gt;
&lt;li&gt;
	&lt;h4&gt;&lt;a href=&quot;&lt;?php echo $post-&gt;url; ?&gt;&quot;&gt;&lt;?php echo $post-&gt;title; ?&gt;&lt;/a&gt;&lt;/h4&gt;
	&lt;?php echo $post-&gt;get_excerpt(200, 'Read More'); ?&gt;
&lt;/li&gt;
&lt;?php endforeach; ?&gt;
&lt;/ul&gt;

&lt;?php endif; ?&gt;
</pre>

<p>Lastly, similar to the header, we load the footer block:</p>
<pre class="brush: php">
&lt;?php $this-&gt;load-&gt;view('_blocks/footer')?&gt;
</pre>

<p class="important">The view file named <dfn>home</dfn> has special meaning in FUEL and should only be used for the homepage. This can be configured in your <span class="file">fuel/application/config/MY_fuel.php</span> by changing the <var>$config['default_home_view']</var> configuration parameter.</p>

<h3 id="page_about">About Pages</h3>
<p>The <a href="http://www.getfuelcms.com/demo/about" target="_blank">About</a> section of the WidgiCorp site contains the following sub pages:</p>
<ul>
	<li><a href="http://www.getfuelcms.com/demo/about" target="_blank">About</a> - static view file that uses the <dfn>main</dfn> layout
		<ul>
			<li><a href="http://www.getfuelcms.com/demo/about/services" target="_blank">Services</a> - static view file that uses the <dfn>main</dfn> layout</li>
			<li><a href="http://www.getfuelcms.com/demo/about/team" target="_blank">Team</a> - static view file that uses the <dfn>main</dfn> layout</li>
			<li><a href="http://www.getfuelcms.com/demo/about/what-they-say" target="_blank">What They Say</a> - static view file that uses the <dfn>main</dfn> layout</li>
		</ul>
	</li>
</ul>

<p>The <dfn>About</dfn> section pages, can be found in the <span class="file">fuel/application/views/</span> folder. These pages all have the <span class="file">fuel/application/views/_layouts/main.php</span> layout applied to them (along with most of the other pages on the site) and is why you don't see the header and footer in the files. This allows us to keep the view files clean and only include the things that change from page to page.</p>

<p>The <a href="http://www.getfuelcms.com/demo/about/what-they-say" target="_blank">What They Say</a> page is slightly different then the others though. It uses the <a href="http://www.getfuelcms.com/user_guide/helpers/fuel_helper" target="_blank">fuel_model()</a> function to include quotes that were entered in the CMS. This is seen at the top of the view file:</p>

<span class="file">application/views/about/what-they-say.php</span>
<pre class="brush: php">
&lt;?php $quotes = fuel_model('quotes', array('find' =&gt; 'all', 'precedence desc')); ?&gt;
</pre>

<p>The above retrieves the quotes from the database and then below we loop through them to display the quotes:</p>

<pre class="brush: php">
&lt;div id=&quot;quotes&quot;&gt;
&lt;?php echo fuel_edit('create', 'Add Quote', 'quotes'); ?&gt;

&lt;?php foreach($quotes as $quote) : ?&gt;
	&lt;?php echo fuel_edit($quote-&gt;id, 'Edit Quote', 'quotes'); ?&gt;
	&lt;?php echo quote($quote-&gt;content, $quote-&gt;name, $quote-&gt;title); ?&gt;
&lt;?php endforeach; ?&gt;
&lt;/div&gt;
</pre>

<p>Another thing to point out here is the use of the <a href="http://www.getfuelcms.com/user_guide/helpers/fuel_helper" target="_blank">fuel_edit()</a> function. This is used to create the <a href="http://www.getfuelcms.com/user_guide/general/inline-editing" target="_blank">inline editing</a> for the page.</p>

<p>Lastly, because this section has sub pages, there is a sub menu on the right side of the page. This will be covered in the <em>Part 2: Creating Menus</em>, but briefly, the <var>$sidemenu</var> variable is created in the <span class="file">views/_variables/global.php</span> file by using the <a href="http://www.getfuelcms.com/user_guide/helpers/fuel_helper" target="_blank">fuel_nav()</a> function.</p>

<h3 id="page_showcase">Showcase Pages</h3>
<p>The showcase section is really made up of 2 pages&mdash;a list view of projects and a detail view.  The <a href="http://www.getfuelcms.com/demo/showcase" target="_blank">list view</a> simply displays all the projects in the showcase. The detail pages are created by passing the project <dfn>slug</dfn> as a URI segment. For the detail page to work, we need to explicitly specify the view file to use for all showcase detail pages. To do that, we apply a <dfn>views</dfn> variable to all pages with a URI that matches <span class="file">showcase/project</span> OR <span class="file">showcase/project/{literal}{project_slug}{/literal}</span> in the showcase variables file as shown below:</p>

<span class="file">fuel/application/views/_variables/showcase.php</span>
<pre class="brush: php">
$vars['blocks'] = array('quote');
$pages['showcase/project$|showcase/project/:any'] = array('view' =&gt; 'showcase/project');
</pre>

<p>The <var>$vars['blocks']</var> array value specifies which blocks to display for all pages with <dfn>showcase</dfn> as the first URI segment.</p>

<p class="important"><a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">For more about passing variables to views, click here.</a></p>


<h3 id="page_blog">Blog Pages</h3>
<p>The <a href="http://www.getfuelcms.com/demo/blog" target="_blank">blog pages</a> are located in the blog module in the fuel/modules/blog/views/theme/default folder. There you will find the view, layout and block files that you can edit. There are several block files ready for you to use but you can create your own (e.g. twitter, flickr feeds etc) and include them in the side menu by editing the blog layout. Additionally, there is a <span class="file">separate assets/css/blog.css</span> file you can use to style the blog.</p>

<p class="important"><a href="http://www.getfuelcms.com/user_guide/modules/blog" target="_blank">For more about the Blog module and creating your own themes, click here.</a></p>

<h3 id="page_contact">Contact Page</h3>
<p>The Contact page uses the standard controller method to display the page. There is a contact controller that takes the information from the contact form and emails it (you must specify an email address for the <var>$config['dev_email']</var> parameter in the <span class="file">fuel/application/config/MY_config.php</span> for it to work). The one thing different you may see, is that we use the library Fuel_page to render the page instead of <var>$this-&gt;load-&gt;view('contact')</var>. This is so we can load in the variable and layout information that may be associated with the page in it's corresponding <a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">variables file</a>. </p>

<span class="file">fuel/application/controllers/contact.php</span>
<pre class="brush: php">
$page_init = array('location' = 'contact');
$this-&gt;load-&gt;module_library(FUEL_FOLDER, 'fuel_page', $page_init);
$fuel_page-&gt;add_variables($vars);
$fuel_page-&gt;render();
</pre>

<p class="important"><a href="http://www.getfuelcms.com/user_guide/general/creating-pages" target="_blank">For more on creating pages, click here.</a></p>
