<h1>Opt-in Controller Development</h1>

<p>Do you ever feel that its a little silly to create a controller method to render even the simplest page? 
Opt-in Controller Development speeds up development by allowing you to create your pages without using controllers. 
Variables can be passed to view files similar to how CI's routes work or can be set in the page itself and 
will cascade to all subviews of the page. You can even load in a library, model and helper.
This method also allows for deeper website structures without the need for special routing to controllers.
</p>

<h2>How it Works</h2>
<p>The CodeIgniter Router is modified to route all requests that don't map to a controller method to a default controller 
named <dfn>page_router</dfn>. This controller will do one of two things, depending on the <dfn>fuel_mode</dfn> value set in the FUEL config file:
</p>

<ol>
	<li>render pages saved in the fuel database (not what we are doing here)</li>
	<li>find a corresponding view file to the uri requested and map variables to the view file found the <dfn>views/_variables</dfn> folder</li>
</ol>

<h2>What is a Variables File?</h2>
<p>A variables file is an array of variables that get passed to pages. The files are located in the <dfn>views/_variables</dfn> folder</p>

<p>There are three levels of variables:</p>
<ul>
	<li><strong>global</strong> - variables loaded for every controller-less page.</li>
	<li><strong>controller</strong> - variables loaded at what would normally be a controller level. A request of 
	<dfn>http://www.mywebsite.com/about</dfn> would load in the <strong>about</strong> controller variables. This level is good
	for setting all template variables for a section of your website (e.g. about).
	</li>
	<li><strong>page</strong> - variables loaded for just a specific page</li>
</ul>

<h2>Global Variables</h2>
<p><strong>Global variables</strong> can be set in the <dfn>views/_variables/global.php</dfn> file using the <dfn>$vars</dfn> array variable. Often times you will set default page variables in this file.
By default, the following global variables are provided:
</p>
<pre class="brush:php">
$vars['layout'] = 'main';
$vars['page_title'] = '';
$vars['meta_keywords'] = '';
$vars['meta_description'] = '';
$vars['js'] = '';
$vars['css'] = '';
$vars['body_class'] = uri_segment(1).' '.uri_segment(2);
</pre>

<h2>Controller Variables</h2>
<p><strong>Controller variables</strong> are applied to what would normally be the controller level of a website. For example,
If you have an <strong>about</strong> section of your website at http://www.mysite.com/about, you can create a <dfn>views/_variables/about.php</dfn>
a variables file at <dfn>views/_variables/about.php</dfn> can be used. Variables in this file will overwrite any global variable with the same key.
The variables will be extened automatically to all sub pages of the about section (e.g. http://www.mysite.com/about/contact) .
Variables at the controller level use the same <dfn>$vars</dfn> variable as global variables.
</p>
<pre class="brush:php">
$vars['layout'] = 'level2';
$vars['page_title'] = 'About : My Website';
$vars['body_class'] = 'about';
</pre>

<h2>Page Variables</h2>
<p><strong>Page variables</strong> are applied to a specific URI path. They will overwrite any global or controller 
variable set with the same key. As an example, you may want to overwrite the <dfn>page_title</dfn> 
of <strong>About : My Website</strong> to <strong>Contact : My Website.</strong>. To do this, you can create a
<dfn>$pages</dfn> array variable in your global or controller variables file (e.g. <dfn>views/_variables/about.php</dfn>) 
with the key being the URI of the page or a <strong>regular expression</strong> to match pages (just like routing files). 
The value is an array of variables to pass to the page. 
</p>
<pre class="brush:php">
// controller level variables
$vars['layout'] = 'level2';
$vars['page_title'] = 'About : My Website';
$vars['body_class'] = 'about';

// page specific
$pages['about/contact'] = array('page_title' => 'Contact : My Website');
$pages['about/contact$|about/careers$'] = array('layout' => 'special');
</pre>

<h2>Adding Variables from Within the View</h2>
<p>An alternative to creating <dfn>$pages</dfn> variables is to simply declare the variable in the view file using the 
fuel helper function <dfn>fuel_set_var</dfn>. By setting the varialbe this way, you will ensure that the variable will also get
passed to the layout file containing the view file.
</p>

<pre class="brush:html">
&lt;?php fuel_set_var('page_title', 'About : My Website'); ?&gt;

&lt;h1&gt;About My Website&lt;/h1&gt;
&lt;p&gt;A long, long, time ago, in a galaxy far, far away...&lt;/p&gt;
</pre>

<h2>Using Variable Files Within a Controller</h2>
<p>If perhaps you need to use the variables in a controller (e.g. you have a form located at <strong>about/contact</strong> and an <strong>about</strong> controller variables file),
you can load it like so:
</p>
<pre class="brush:php">
class About extends CI_Controller {
	
	function About()
	{
		parent::__construct();
	}
	
	function contact()
	{
		// set your variables
		$vars = array('page_title' => 'Contact : My Website');

		//... form code goes here
		
		// use Fuel_page to render so it will grab all opt-in variables and do any necessary parsing
		$this->fuel->pages->render('about/contact', $vars);
		
		
	}
</pre>


<h2>Special Variables</h2>
<p>The following are special variable keys that can be used in the variables files:</p>
<ul>
	<li><strong>helpers</strong> - a string or array of helpers to load.</li>
	<li><strong>libraries</strong> - a string or array of library classes to load.</li>
	<li><strong>models</strong> -  a string or array of models to load.</li>
	<li><strong>layout</strong> - the path to the layout view. The <dfn>$body</dfn> variable is used within the layout to load the view file contents.</li>
	<li><strong>view</strong> - a specific view file to use for the page. By default it will look for a view file that matches the uri path. 
		If it doesn't find one, then it will search the uri path for a corresponding view.</li>
	<li><strong>parse_view</strong> - determines whether the view file should be <a href="<?=user_guide_url('parsing/overview')?>">parsed</a>.</li>
	<li><strong>allow_empty_content</strong> - determines whether to allow empty view content without showing the 404 page.</li>
	<li><strong>CI</strong> - the CodeIgniter super object variable</li>
	
</ul>