<h1>Links</h1>
<p>The Blog Links module stores link information to be displayed on your blog.
Links are powered by a <dfn>blog_links_model</dfn> and have a grandparent class of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>.
</p>

<h2>Link Fields</h2>
<p>Below are the fields to fill out for a link:</p>
<ul>
	<li><strong>name</strong> - The display name of the category</li>
	<li><strong>permalink</strong> - The URL identifier for the category</li>
	<li><strong>target</strong> - The target attribute of the link. Determines whether the link should open up in a new page or not</li>
	<li><strong>description</strong> - The URL identifier for the category</li>
	<li><strong>precedence</strong> - The order to display the link. The higher the number the higher on the list (by default)</li>
	<li><strong>published</strong> - Determines whether to display the link</li>
</ul>

<h1>The Link Model Properties</h1>
<p>The link object has the following properties:</p>
<ul>
	<li>id</li>
	<li>name</li>
	<li>url</li>
	<li>target</li>
	<li>description</li>
	<li>precedence</li>
	<li>published (returns 'yes' or 'no')</li>
</ul>

<h1>The Link Model Methods</h1>
<p>The link object model has the following method:</p>

<h2>get_link()</h2>
<p>Returns the link.</p>
