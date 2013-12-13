<h1>Navigation</h1>
<p>Navigation in FUEL CMS can be used to create various parent/child hierarchical menu structures such as dropdown menus, collapsible menus, breadcrumbs, site maps and page titles.</p>

<p>Programmatically, you can use the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_nav')?>">fuel_nav</a> helper function to render those structures
or you can access the instantiated <a href="<?=user_guide_url('libraries/fuel_navigation')?>">Fuel_navigation</a> object like so:</p>
<pre class="brush:php">
// short way
echo fuel_nav(array('render_type' => 'breadcrumb'));

// long way 
echo $this->fuel->navigation->render(array('render_type' => 'page_title'));

// additional shortcuts
echo $this->fuel->navigation->basic(); // default
echo $this->fuel->navigation->breadcrumb();
echo $this->fuel->navigation->collapsible();
echo $this->fuel->navigation->page_title();
echo $this->fuel->navigation->delimited();
echo $this->fuel->navigation->data();
</pre>

<h2>Navigation Structures</h2>
<p>There are two ways to create navigation structures in FUEL. The first way, is to create a 
<dfn>$nav</dfn> array in the file located at <span class="file">fuel/applicaiton/views/_variables/nav.php</span> like below:</p>
 
<span class="file">fuel/application/views/_variables/nav.php</span>
<pre class="brush: php">
$nav['about'] = 'About';
$nav['showcase'] = array('label' => 'Showcase', 'active' => 'showcase$|showcase/:any');
$nav['blog'] = array('label' => 'Blog', 'active' => 'blog$|blog/:any');
$nav['contact'] = 'Contact';

// about sub menu
$nav['about/services'] = array('label' => 'Services', 'parent_id' => 'about');
$nav['about/team'] = array('label' => 'Team', 'parent_id' => 'about');
$nav['about/what-they-say'] = array('label' => 'What They Say', 'parent_id' => 'about');
</pre>

<p class="important">For more on the array syntax you can use to create a navigation structure, visit the <a href="http://www.getfuelcms.com/user_guide/libraries/menu" target="_blank">Menu Class Library.</a></p>

<br />

<p>The second way, is to input that information in the CMS. Alternatively, you can upload a nav file (<span class="file">fuel/application/views/_variables/nav.php</span>) in the CMS to save you some input time.
By default, the <dfn>fuel_nav()</dfn> function will first check to see if there is any navigation items saved if the CMS is being used, and if not will use the 
<span class="file">nav.php</span> file.</p>

<p class="important">Although FUEL provides a way for you to create a navigation item when creating a page, 
it's important to note that navigation in FUEL is not directly associated with pages which can give you greater flexibility in your structures.</p>

<h2>Usage Examples</h2>
<p>The most common way to render a navigation structure is to use the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_nav')?>">fuel_nav()</a> function which is a wrapper around the 
<a href="http://www.getfuelcms.com/user_guide/libraries/menu" target="_blank">Menu class</a>.</p>

<h3 id="dr_page_title">Page Titles</h3>
<p>One of the first places you may want to apply a navigation structure is as a default for the page titles used throughout your site.
You can do just that by adding it to the <a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">global variables file</a>:</p>

<span class="file">application/views/_variables/global.php (example)</span>
<pre class="brush: php">
...
$vars[&#x27;page_title&#x27;] = fuel_nav(array(&#x27;render_type&#x27; =&gt; &#x27;page_title&#x27;, &#x27;delimiter&#x27; =&gt; &#x27; : &#x27;, &#x27;order&#x27; =&gt; &#x27;desc&#x27;, &#x27;home_link&#x27; =&gt; &#x27;WidgiCorp - Fine Makers of Widgets&#x27;));
...
</pre>

<p>By adding it to the <a href="http://www.getfuelcms.com/user_guide/general/opt-in-controllers" target="_blank">global variables file</a> you can use it across all pages easily. 
You specify the <dfn>render_type</dfn> parameter to be <dfn>page_title</dfn>.
The <dfn>delimiter</dfn> parameter is the separator used between each level of the page title hierarchy. 
The <dfn>order</dfn> parameter tells it which direction to render the hierarchy (bottom-to-top or top-to-bottom). 
Lastly, the <dfn>home_link</dfn> parameter specifies the text to display when on the homepage.</p>


<h3 id="hdr_topmenu">Top Menu</h3>
<p>The second place you may want to use the navigation structure is for perhaps a top menu of your layout:</p>

<p><strong>application/views/_blocks/header.php (example)</strong></p>
<pre class="brush: php">
...
&lt;?php echo fuel_nav(array(&#x27;container_tag_id&#x27; =&gt; &#x27;topmenu&#x27;, &#x27;item_id_prefix&#x27; =&gt; &#x27;topmenu_&#x27;)); ?&gt;
...
</pre>

<p>You set the <dfn>container_tag_id</dfn> parameter which sets the <dfn>id</dfn> value for the container tag (in this case, a &lt;ul&gt;). This allows you to easily target
the menu for styling. Additionally, we set the <dfn>item_id_prefix</dfn> which sets an id for each menu item, which again can be used for styling. Although the demo site doesn't take advantage of the <dfn>item_id_prefix</dfn> in the CSS, we will often use it to set the active state if the menu is using an image sprite and the <dfn>.active</dfn> class isn't specific enough.</p>

<h3 id="hdr_sidemenu">Side Menu</h3>
<p>The side menu works very similar to the top menu. You can set a variable of <dfn>$sidemenu</dfn> using the <dfn>fuel_nav()</dfn> function again. 
Alternatively, you could simply use <dfn>fuel_nav()</dfn> in your layout file. In some cases, you may want to include <dfn>container_tag_id</dfn> parameter like the top menu, but you also include the <dfn>parent_id</dfn> in which to render from. For example, if you have a menu structure that
includes an about page with several child page (e.g. about/contact, about/history) the <dfn>parent_id</dfn> would be the first URI segment of "about".
Additionally, if the menu structure had more levels, you could specify the <dfn>depth</dfn> parameter to limit
how far down the hierarchy you want to display. You could also specify a <dfn>render_type</dfn> of <dfn>'collapsible'</dfn> so that it would display a collapsible menu 
structure based on where you are in the hierarchy.</p>

<span class="file">application/views/_variables/global.php (example)</span>
<pre class="brush: php">
$vars[&#x27;sidemenu&#x27;] = fuel_nav(array(&#x27;container_tag_id&#x27; =&gt; &#x27;sidemenu&#x27;, &#x27;parent&#x27; =&gt; uri_segment(1)));
</pre>

<p>You can merge the <dfn>$sidemenu</dfn> variable in using a normal merge in your layouts. 
You do not use the <a href="http://www.getfuelcms.com/user_guide/helpers/fuel_helper" target="_blank">fuel_var()</a> function because it is not an editable variable:</p>

<span class="file">application/views/_layouts/main.php (example)</span>
<pre class="brush: php">
&lt;?php if (!empty($sidemenu)) : ?&gt;
&lt;?php echo $sidemenu; ?&gt;
&lt;?php endif ?&gt;
</pre>

<h3 id="hdr_footermenu">Footer Menu</h3>
<p>Another place you may want to add a list of menu items is at the footer of your layout. This can be done similar to both the <a href="#hdr_topmenu">Top Menu</a> and 
the <a href="#sidemenu">Side Menu</a>:</p>

<span class="file">application/views/_variables/global.php (example)</span>
<pre class="brush: php">
$vars[&#x27;footermenu&#x27;] = fuel_nav(array(&#x27;container_tag_id&#x27; =&gt; &#x27;footermenu&#x27;, &#x27;render_type&#x27; =&gt; 'delimited'));
</pre>

<span class="file">application/views/_blocks/footer.php (example)</span>
<pre class="brush: php">
&lt;footer&gt;
	&lt;?php echo $footermenu; ?&gt;
&lt;/footer&gt;
</pre>



<h3>sitemap.xml</h3>
<p>Hopefully, you are starting to see the benefits of creating your menu structures in this format. The last thing we'll cover is creating the sitemap.xml. FUEL comes with a
sitemap.xml view that you can use. You will need to uncomment the line in the <span class="file">fuel/application/config/routes.php</span> file for it to work. You will 
also need to add any dynamic pages in the section commented out. For example, you may want to add all the project pages from a project module you created to appear in the sitemap.xml:</p>

<span class="file">application/views/sitemap_xml.php</span>
<pre class="brush: php">
&lt;?php
/***************************************************************
ATTENTION: To use this dynamic sitemap, you must uncomment the 
line in the application/config/routes.php regarding the sitemap
**************************************************************/

fuel_set_var(&#x27;layout&#x27;, &#x27;&#x27;);
$default_frequency = &#x27;Monthly&#x27;;
$nav = fuel_nav(array(&#x27;return_normalized&#x27; =&gt; TRUE));

/***************************************************************
Add any dynamic pages and associate them to the $nav array here:
**************************************************************/

$projects = fuel_model('projects'); // won't filter on published because they all should be'

// add project pages
foreach($projects as $project)
{
	$key = 'showcase/project/'.$project->slug;
	$nav[$key] = array('location' => $key);
}


/**************************************************************/

if (empty($nav)) show_404();
header(&#x27;Content-type: text/xml&#x27;);
echo &#x27;&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;&#x27;;
?&gt;
&lt;urlset xmlns=&quot;http://www.sitemaps.org/schemas/sitemap/0.9&quot;
		xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot;
		xsi:schemaLocation=&quot;http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd&quot;&gt;
&lt;?php foreach($nav as $uri=&gt;$page) { ?&gt;
	&lt;?php if(isset($page[&#x27;location&#x27;])): ?&gt; 
		&lt;url&gt;
			&lt;loc&gt;&lt;?=site_url($page[&#x27;location&#x27;])?&gt;&lt;/loc&gt;
			&lt;?php if (empty($page[&#x27;frequency&#x27;])) : ?&gt;&lt;changefreq&gt;&lt;?=$default_frequency?&gt;&lt;/changefreq&gt;&lt;?php endif; ?&gt; 
		&lt;/url&gt;	
	&lt;?php elseif (is_string($page)): ?&gt;
	&lt;url&gt;
		&lt;loc&gt;&lt;?=site_url($page)?&gt;&lt;/loc&gt;
		&lt;changefreq&gt;&lt;?=$default_frequency?&gt;&lt;/changefreq&gt;
	&lt;/url&gt;
	&lt;?php endif; ?&gt;

&lt;?php } ?&gt;
&lt;/urlset&gt;

</pre>

<p>Here, we again use the <dfn>fuel_nav()</dfn> function to create the <dfn>$nav</dfn> array variable. We then add to that <dfn>$nav</dfn> array
all the dynamic project pages. Additionally, you can change the <dfn>$default_frequency</dfn> variable at the top to change the frequency value for each location.</p>