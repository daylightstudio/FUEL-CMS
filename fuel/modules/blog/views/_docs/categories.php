<h1>Categories</h1>
<p>Categories allow you to group your posts together. A post can belong to more then one category. 
To create a category, you can either add it directly within the post, or you can create them under 
the categories menu item. Categories are powered by a <dfn>blog_categories_model</dfn> and have a grandparent class of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>.
</p>

<h2>Category Fields</h2>
<p>Below are the fields to fill out for a category:</p>
<ul>
	<li><strong>Name</strong> - The display name of the category</li>
	<li><strong>Permalink</strong> - The URL identifier for the category</li>
	<li><strong>Published</strong> - Determines whether to display the category</li>
</ul>

<br />
<h1>The Category Model Properties</h1>
<p>The category object has the following properties:</p>
<ul>
	<li>id</li>
	<li>name</li>
	<li>permalink</li>
	<li>published (returns 'yes' or 'no')</li>
</ul>

<br />
<h1>The Category Model Methods</h1>
<p>The category object model has the following specific methods:</p>

<h2>get_posts()</h2>
<p>Returns an array of posts associated with a category.</p>


<h2>get_posts_count()</h2>
<p>Returns the number of posts associated with the category.</p>


<h2>get_url()</h2>
<p>Returns a url to that category.</p>

