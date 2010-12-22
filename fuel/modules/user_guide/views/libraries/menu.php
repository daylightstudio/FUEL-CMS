<h1>Menu Class</h1>
<p>The Menu class is used to create hierchical html structures ideal for menu structures.</p>

<h2>Initializing the Class</h2>
<p>Like most other classes in CodeIgniter, the Menu class is initialized in your controller using the <dfn>$this->load->library</dfn> function:</p>

<pre class="brush: php">$this->load->library('menu');</pre>

<p>Alternatively, you can pass initialization parameters as the second parameter:</p>

<pre class="brush: php">$this->load->library('menu', array('active_class'=>'on', 'render_type' => 'collapsible'));</pre>

<h2>Configuring Validator Information</h2>
<p>There are several public properties you can use to configure the Menu Class:</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>active_class</strong></td>
			<td>active</td>
			<td>None</td>
			<td>The active css class</td>
		</tr>
		<tr>
			<td><strong>active</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The active menu item</td>
		</tr>
		<tr>
			<td><strong>styles</strong></td>
			<td></td>
			<td></td>
			<td>CSS class styles to apply to menu items... can be a nested array</td>
		</tr>
		<tr>
			<td><strong>first_class</strong></td>
			<td>first</td>
			<td>None</td>
			<td>The css class for the first menu item</td>
		</tr>
		<tr>
			<td><strong>last_class</strong></td>
			<td>last</td>
			<td>None</td>
			<td>The css class for the last menu item</td>
		</tr>
		<tr>
			<td><strong>depth</strong></td>
			<td>0</td>
			<td>None</td>
			<td>The depth of the menu to render at</td>
		</tr>
		<tr>
			<td><strong>use_titles</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Use the title attribute in the links</td>
		</tr>
		<tr>
			<td><strong>root_value</strong></td>
			<td>NULL</td>
			<td>NULL or 0</td>
			<td>The root parent value</td>
		</tr>
		<tr>
			<td><strong>container_tag</strong></td>
			<td>ul</td>
			<td>None</td>
			<td>The html tag for the container of a set of menu items</td>
		</tr>
		<tr>
			<td><strong>container_tag_attrs</strong></td>
			<td>None</td>
			<td>None>
			<td>HTML attributes for the container tag</td>
		</tr>
		<tr>
			<td><strong>container_tag_id</strong></td>
			<td>None</td>
			<td>None</td>
			<td>HTML container id</td>
		</tr>
		<tr>
			<td><strong>container_tag_class</strong></td>
			<td>None</td>
			<td>None</td>
			<td>HTML container class</td>
		</tr>
		<tr>
			<td><strong>cascade_selected</strong></td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Cascade the selected items</td>
		</tr>
		<tr>
			<td><strong>include_hidden</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Include menu items with the hidden attribute</td>
		</tr>
		<tr>
			<td><strong>item_tag</strong></td>
			<td>li</td>
			<td>None</td>
			<td>The html list item element</td>
		</tr>
		<tr>
			<td><strong>item_id_prefix</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The prefix to the item id</td>
		</tr>
		<tr>
			<td><strong>item_id_key</strong></td>
			<td>id</td>
			<td>either 'id' or 'location'</td>
			<td>The key value to use for creating an id for an item</td>
		</tr>
		<tr>
			<td><strong>use_nav_key</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Use the nav_key value to match active state instead of the id value</td>
		</tr>
		<tr>
			<td><strong>render_type</strong></td>
			<td>basic</td>
			<td>basic, breadcrumb, collapsible,  page_title</td>
			<td>The type of menu to render</td>
		</tr>
		<tr>
			<td><strong>pre_render_func</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>function to apply to menu labels before rendering</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Specific to Breadcrumb AND/OR Page Title Menus</h4></td>
		</tr>
		<tr>
			<td>delimiter</td>
			<td>' &gt; '</td>
			<td>None</td>
			<td>The HTML element between the links</td>
		</tr>
		<tr>
			<td>display_current</td>
			<td>TRUE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>Display the current active breadcrumb item?</td>
		</tr>
		<tr>
			<td>home_link</td>
			<td>Home</td>
			<td>None</td>
			<td>The root home link. If value is an array, the key of array is the link and the value is the label</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Specific to Breadcrumb Menus</h4></td>
		</tr>
		<tr>
			<td>arrow_class</td>
			<td>arrow</td>
			<td>None</td>
			<td>The class for the arrows</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Specific to Page TItle Display</h4></td>
		</tr>
		<tr>
			<td>order</td>
			<td>'asc'</td>
			<td>asc,desc</td>
			<td>The direction the page title should build</td>
		</tr>
	</tbody>
</table>


<?php /* ?>
<ul>
	<li><strong>active_class</strong> - the active css class. Default is <dfn>active</dfn></li>
	<li><strong>active</strong> - the active menu item.</li>
	<li><strong>styles</strong> - css class styles to apply to menu items... can be a nested array.</li>
	<li><strong>first_class</strong> - the css class for the first menu item. Default is <dfn>first</dfn></li>
	<li><strong>last_class</strong> - the css class for the last menu item. Default is <dfn>last</dfn></li>
	<li><strong>depth</strong> - the depth of the menu to render at. Default is <dfn>FALSE</dfn></li>
	<li><strong>use_titles</strong> - use the title attribute in the links. Default is <dfn>FALSE</dfn></li>
	<li><strong>root_value</strong> - the root parent value... can be <dfn>NULL</dfn> or <dfn>0</dfn>. Default is <dfn>NULL</dfn></li>
	<li><strong>container_tag</strong> - the html tag for the container of a set of menu items. Default is <dfn>FALSE</dfn></li>
	<li><strong>container_tag_attrs</strong> - html attributes for the container tag. Default is <dfn>FALSE</dfn></li>
	<li><strong>container_tag_id</strong> - html container id. Default is <dfn>FALSE</dfn></li>
	<li><strong>cascade_selected</strong> - i=cascade the selected items. Default is <dfn>TRUE</dfn></li>
	<li><strong>include_hidden</strong> - include menu items with the hidden attribute. Default is <dfn>FALSE</dfn></li>
	<li><strong>item_tag</strong> - the html list item element. Default is <dfn>FALSE</dfn></li>

	<li><strong>item_id_prefix</strong> - the prefix to the item id. Default is <dfn>FALSE</dfn></li>
	<li><strong>render_type</strong> - values can be basic, breadcrumb, collapsible and page_title. Default is <dfn>basic</dfn></li>
	<li><strong>pre_render_func</strong> - function to apply to menu labels before rendering</li>
</ul>

<h3>Specific to Breadcrumb Menus:</h3>
<ul>	
	<li><strong>delimiter</strong> - the html element between the links. Default is <dfn>' &gt; '</dfn></li>
	<li><strong>arrow_class</strong> - the class for the arrows. Default is <dfn>arrow</dfn></li>
	<li><strong>display_current</strong> - display the current active breadcrumb item?. Default is <dfn>TRUE</dfn></li>
	<li><strong>home_link</strong> - the root home link. Default is <dfn>Home</dfn></li>
</ul>

<?php */ ?>

<h2>The Menu Array Structure</h2>
<p>The menu array can hav the following key/value pair:</p>

<ul>
	<li><strong>id</strong> - the id value of the menu item. If none is specified, then the array key value will be used</li>
	<li><strong>parent_id</strong> - the parent's id value that the menu item should reside under. Top level (also called root items), should have a root value of NULL or 0 depending on what is specified for the <dfn>root_value</dfn></li>
	<li><strong>label</strong> - the text label to display for the menu item</li>
	<li><strong>location</strong> -  the link location of the menu item. If none is specified, the array key value will be used</li>
	<li><strong>attributes</strong> - additional HTML link attributes (e.g. target="_blank") to assign to the menu item</li>
	<li><strong>hidden</strong> - whether to display the menu item or not.</li>
	<li><strong>active</strong> - a regular expression that determines whether it should be given active status. If none is specified, it will be the same as the location value (e.g. about/history$|about/contact$)</li>
</ul>

<p>Below is an common example:</p>
<pre class="brush: php">
$nav = array();
$nav['about'] = 'About';
$nav['about/history'] = array('label' => 'History', 'parent_id' => 'about');
$nav['about/contact'] = array('label' => 'Contact', 'parent_id' => 'about');

$nav['products'] = 'Products';
$nav['products/X3000'] = array('label' => 'X3000', 'parent_id' => 'products');
</pre>




<h1>Function Reference</h1>

<h2>$this->menu->render(<var>items</var>, <var>['active']</var>, <var>['parent_id']</var>, <var>'render_type'</var>)</h2>
<p>Will create the HTML for the menu.
The <dfn>$items</dfn> the array of items to display. See above for format of the array.
The <dfn>$active</dfn> parameter specifies which menu item is active.
The <dfn>$parent_id</dfn> parameter specifies where in the $items to start the heirarchy.
The <dfn>$render_type</dfn> parameter is the type of menu you want to render (<dfn>basic</dfn>, <dfn>breadcrumb</dfn>, <dfn>collapsible</dfn>).
</p>

<pre class="brush: php">
$nav = array();
$nav['about'] = 'About';
$nav['about/history'] = array('label' => 'History', 'parent_id' => 'about');
$nav['about/contact'] = array('label' => 'Contact', 'parent_id' => 'about');

$nav['products'] = 'Products';
$nav['products/X3000'] = array('label' => 'X3000', 'parent_id' => 'products');

$active = 'about/history';
$menu = $this->menu->render($nav, $active, NULL, 'basic');

echo $menu;
/* echoed output looks something like this
<ul>
	<li class="first active"><a href="/about" title="About">About</a>
		<ul>
			<li class="first"><a href="/about/history" title="History">History</a></li>
			<li class="last active"><a href="/about/contact" title="Contact">Contact</a></li>
		</ul>
	</li>
	<li class="last"><a href="/products" title="Products">Products</a></li>
</ul>
*/
</pre>

<h2>$this->menu->render_collapsible(<var>items</var>, <var>['active']</var>, <var>['parent_id']</var>)</h2>
<p>Will create the HTML for a collapsible menu.
The <dfn>$items</dfn> the array of items to display. See above for format of the array.
The <dfn>$active</dfn> parameter specifies which menu item is active.
The <dfn>$parent_id</dfn> parameter specifies where in the $items to start the heirarchy
</p>

<pre class="brush: php">
$nav = array();
$nav['about'] = 'About';
$nav['about/history'] = array('label' => 'History', 'parent_id' => 'about');
$nav['about/contact'] = array('label' => 'Contact', 'parent_id' => 'about');

$nav['products'] = 'Products';

$active = 'about/history';
$menu = $this->menu->render($nav, $active, NULL, 'basic');

echo $menu;
/* echoed output looks something like this. Note the difference in the product items not have a sub ul
<ul>
	<li class="first active"><a href="/about" title="About">About</a>
		<ul>
			<li class="first"><a href="/about/history" title="History">History</a></li>
			<li class="last active"><a href="/about/contact" title="Contact">Contact</a></li>
		</ul>
	</li>
	<li class="last"><a href="/products" title="Products">Products</a></li>
</ul>
*/
</pre>

<h2>$this->menu->render_breadcrumb(<var>items</var>, <var>['active']</var>, <var>['parent_id']</var>)</h2>
<p>Will create the HTML for a breadcrumb menu.
The <dfn>$items</dfn> the array of items to display. See above for format of the array.
The <dfn>$active</dfn> parameter specifies which menu item is active.
The <dfn>$parent_id</dfn> parameter specifies where in the $items to start the heirarchy
</p>

<pre class="brush: php">
$nav = array();
$nav['about'] = 'About';
$nav['about/history'] = array('label' => 'History', 'parent_id' => 'about');
$nav['about/contact'] = array('label' => 'Contact', 'parent_id' => 'about');

$nav['products'] = 'Products';

$active = 'about/history';
$menu = $this->menu->render($nav, $active, NULL, 'basic');

echo $menu;
/* echoed output looks something like this.
<ul>
	<li><a href="/">Home</a> <span class="arrow"> &gt; </span> </li>
	<li><a href="/about">About</a> <span class="arrow"> &gt; </span> </li>
	<li>History</li>
</ul>
*/
</pre>

<h2>$this->menu->render_page_title(<var>items</var>, <var>['active']</var>, <var>['parent_id']</var>)</h2>
<p>Will create a page title based on the nav structure.
The <dfn>$items</dfn> the array of items to display. See above for format of the array.
The <dfn>$active</dfn> parameter specifies which menu item is active.
The <dfn>$parent_id</dfn> parameter specifies where in the $items to start the heirarchy
</p>

<pre class="brush: php">
$nav = array();
$nav['about'] = 'About';
$nav['about/history'] = array('label' => 'History', 'parent_id' => 'about');
$nav['about/contact'] = array('label' => 'Contact', 'parent_id' => 'about');

$nav['products'] = 'Products';

$active = 'about/history';
$menu = $this->menu->render($nav, $active, NULL, 'basic');

echo $menu;
/* echoed output looks something like this.
Home &gt; About &gt; History
*/
</pre>


<h2>$this->menu->reset()</h2>
<p>Will reset the parameters back to their default values. Good to use when multiple menus are generated.</p>

<pre class="brush: php">
$menu = $this->menu->clear();
</pre>