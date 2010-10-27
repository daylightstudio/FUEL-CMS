<h1>Blog Helper</h1>

<p>Contains blog related functions to use in your blog's theme pages.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->module_helper(BLOG_FOLDER, 'blog');
</pre>

<p>The following function is available:</p>


<h2>blog_url(<var>uri</var>)</h2>
<p>Returns a blog specific URI. Convience function that maps to <a href="<?=user_guide_url('modules/blog/fuel_blog')?>">Fuel_blog->url()</a> method.</p>


<h2>blog_block(<var>view</var>, <var>[vars]</var>, <var>[return]</var>)</h2>
<p>Returns an HTML block from the specified theme's _block.</p>