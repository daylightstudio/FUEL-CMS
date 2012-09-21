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
<h2>Static Page - about</h2>

<h3>Opt-In Controller Method</h3>
<p>There are several ways to create a page in FUEL. The easest way is to simply create a view file. We call this method the <a href="<?=user_guide_url('general/opt-in-controllers')?>">Opt-In Controller</a> Method. This allows you to create pages without the need for a controller. A URI path of <strong>about/contact</strong> would have a view file created at <strong>views/about/contact.php</strong>. The method has some extra benefits like having deep path structures and hyphens in the path without the need of routing.</p>

<p>For this tutorial we will make the <dfn>about</dfn> page a static view file. To do that, create the following file at <dfn>application/views/about.php</dfn>: </p>

<pre class="brush: xml">
<h1>Hello World</h1>
<p>This is our about page</p>
</pre>

<p>Note how the <dfn>about</dfn> page should automatically have a header and footer applied to it. This is because the <dfn>application/views/_variables/global.php</dfn> file specifies the <dfn>main</dfn> layout file to be applied to it (more about passing variables below). Layout files exist in the <dfn>application/views/_layouts/</dfn> folder (more about layouts below as well).
Often times you'll see <dfn>fuel_set_var('body', '');</dfn> in a layout. This is because the variable <dfn>$body</dfn> holds the contents of the view file (in this case the 'about' page).
</p>

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
<h2>Controller Page - about/contact</h2>
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
		$this->fuel->pages->render('about/contact', $vars);
	}
}
</pre>


<a name="editable"></a>
<h2>CMS Page - about/team</h2>
<p>For the create <dfn>about/team</dfn> page we will login to the CMS by going to 
<dfn>http://mysite.com/fuel</dfn> (where <strong>mysite.com</strong> is your domain) and entering the username of <dfn>admin</dfn> with the password (which is probably <dfn>admin</dfn> as well if you haven't changed it). Click on the <dfn>Pages</dfn> menu item on the left and follow the directions 
<a href="<?=user_guide_url('modules/fuel/pages')?>">seen here</a> to create a page. For the <dfn>location</dfn> value type in <strong>about/team</strong> and for the <dfn>body</dfn> value, put in the value below, click save and then view the page:</p>

<pre class="brush: php">
<h1>Team</h1>
<p>This is our team page</p>
</pre>

<a name="news"></a>
<h2>Module Page: news</h2>
<p>A module page is a combination of a static page and a module that is used to generate the contents of that page. 
For this example, we will have a news page that will list all the news items and a news detail page that will show the contents of those news items. 
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
<pre class="brush: sql">
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