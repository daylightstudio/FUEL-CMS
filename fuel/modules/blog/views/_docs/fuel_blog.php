<h1>Fuel_blog Class</h1>

<p>The main class used to marshal information to the blog.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->module_library(BLOG_FOLDER, 'fuel_blog');
</pre>

<p>The following function is available:</p>

<h2>$this->fuel_blog->title()</h2>
<p>Returns the title of the blog specified in the settings.</p>


<h2>$this->fuel_blog->description()</h2>
<p>Returns the descripton of the blog specified in the settings.</p>


<h2>$this->fuel_blog->language(<var>'[code]'</var>)</h2>
<p>Returns the language code based on the language currently set in CodeIgniter config. If <dfn>code</dfn> is set to TRUE, then it will return the full language code.</p>


<h2>$this->fuel_blog->domain()</h2>
<p>Returns the domain to be used for the blog based on the FUEL configuration. If empty it will return whatever $_SERVER['SERVER_NAME']. Needed for Atom feeds.</p>


<h2>$this->fuel_blog->url(<var>'uri'</var>)</h2>
<p>Returns the blog specific URL.</p>


<h2>$this->fuel_blog->feed(<var>'[type]'</var>, <var>'[category]'</var>)</h2>
<p>Returns the blog specific RSS feed URL.</p>


<h2>$this->fuel_blog->feed_header()</h2>
<p>Sets the HTTP headers needed for the RSS feed.</p>


<h2>$this->fuel_blog->feed_output(<var>'[type]'</var>, <var>'[category]'</var>)</h2>
<p>Returns the output for the RSS feed.</p>


<h2>$this->fuel_blog->feed_data(<var>[limit]</var>, <var>[category]</var>)</h2>
<p>Returns the data need for the blog feed.</p>


<h2>$this->fuel_blog->last_updated()</h2>
<p>Returns last updated blog post.</p>


<h2>$this->fuel_blog->theme_path()</h2>
<p>Returns the path to the theme view files.</p>


<h2>$this->fuel_blog->layout()</h2>
<p>Returns name of the theme layout file to use.</p>


<h2>$this->fuel_blog->settings(<var>[key]</var>)</h2>
<p>Returns the setting(s) information.</p>


<h2>$this->fuel_blog->header(<var>[vars]</var>, <var>[return]</var>)</h2>
<p>Returns header of the blog.
The <dfn>vars</dfn> parameter is an array of variables to pass to the header view file.
The <dfn>return</dfn> parameter determines whether to echo the output or just return a string
</p>


<h2>$this->fuel_blog->view(<var>'view'</var>, <var>[vars]</var>, <var>[return]</var>)</h2>
<p>Returns a view for the blog.
The <dfn>vars</dfn> parameter is an array of variables to pass to the header view file.
The <dfn>return</dfn> parameter determines whether to echo the output or just return a string
</p>


<h2>$this->fuel_blog->block(<var>'block'</var>, <var>[vars]</var>, <var>[return]</var>)</h2>
<p>Returns a block view file for the blog. Block files must be located in the themes <dfn>_blocks</dfn> view folder.
The <dfn>vars</dfn> parameter is an array of variables to pass to the header view file.
The <dfn>return</dfn> parameter determines whether to echo the output or just return a string
</p>


<h2>$this->fuel_blog->sidemenu(<var>'[blocks]'</var>)</h2>
<p>Returns the sidemenu for the blog. The blocks parameter is an array of names to block files to include. The default is the search and categories block files.</p>


<h2>$this->fuel_blog->get_post_count(<var>'[category]'</var>)</h2>
<p>Returns the number of posts. If the <dfn>category</dfn> parameter is provided, then it will limit the number to be specific to the category</p>


<h2>$this->fuel_blog->get_recent_posts(<var>[limit]</var>)</h2>
<p>Returns the most recent posts. The default limit is 5.</p>


<h2>$this->fuel_blog->get_category_posts(<var>'[category]'</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns the most recent posts for a given category.</p>


<h2>$this->fuel_blog->get_posts_by_date(<var>[year]</var>, <var>[month]</var>, <var>[day]</var>, <var>[permalink]</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[order_by]'</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns posts by providing a given date.</p>


<h2>$this->fuel_blog->get_posts(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns posts based on specific query parameters.</p>


<h2>$this->fuel_blog->get_posts_by_page(<var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns posts to be displayed for a specific page. Used for pagination mostly.</p>


<h2>$this->fuel_blog->get_post_archives(<var>[limit]</var>, <var>[offset]</var>)</h2>
<p>Returns posts grouped by the year/month.</p>


<h2>$this->fuel_blog->get_post(<var>[post]</var>, <var>'[order_by]'</var>, <var>'[return_method]'</var>)</h2>
<p>Returns a single post. The post parameter can be either the permalink value or the post's id.</p>


<h2>$this->fuel_blog->get_next_post(<var>post</var>, <var>'[return_method]'</var>)</h2>
<p>Returns the next post (if any) from a given date.</p>


<h2>$this->fuel_blog->get_prev_post(<var>post</var>, <var>'[return_method]'</var>)</h2>
<p>Returns the previous post (if any) from a given date.</p>


<h2>$this->fuel_blog->get_categories(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns a list of blog categories.</p>


<h2>$this->fuel_blog->get_category(<var>[category]</var> <var>'[order_by]'</var>, <var>'[return_method]'</var>)</h2>
<p>Returns a single blog category.</p>


<h2>$this->fuel_blog->get_posts_to_categories(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns a the posts associated with categories.</p>


<h2>$this->fuel_blog->search_posts(<var>term</var> <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>)</h2>
<p>Searches posts for a specific term.</p>


<h2>$this->fuel_blog->get_comments(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns a comments from posts based on the where condition.</p>

<h2>$this->fuel_blog->get_comments(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns a single comment.</p>

<h2>$this->fuel_blog->get_links(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns links.</p>


<h2>$this->fuel_blog->get_user(<var>id</var>)</h2>
<p>Returns a FUEL author/user.</p>


<h2>$this->fuel_blog->get_users(<var>[where]</var>, <var>'[order_by]'</var>, <var>[limit]</var>, <var>[offset]</var>, <var>'[return_method]'</var>, <var>'[assoc_key]'</var>)</h2>
<p>Returns FUEL users/authors.</p>


<h2>$this->fuel_blog->logged_in_user()</h2>
<p>Returns the logged in information array of the currently logged in FUEL user.</p>


<h2>$this->fuel_blog->is_logged_in()</h2>
<p>Returns whether you are logged into FUEL or not.</p>


<h2>$this->fuel_blog->use_cache()</h2>
<p>Returns whether cache should be used based on the blog settings.</p>


<h2>$this->fuel_blog->get_cache(<var>'cache_id'</var>)</h2>
<p>Returns a cached file if it exists.</p>


<h2>$this->fuel_blog->get_cache(<var>'cache_id'</var>)</h2>
<p>Returns a cached file if it exists.</p>


<h2>$this->fuel_blog->save_cache(<var>cache_id</var>, <var>'output'</var>)</h2>
<p>Saves output to the cache.</p>


<h2>$this->fuel_blog->remove_cache(<var>'[cache_id]'</var>)</h2>
<p>Removes page from cache. If no <dfn>cache_id</dfn> is provided, then all the blog cache is removed.</p>


<h2>$this->fuel_blog->page_title(<var>'[title]'</var>, <var>'[sep]'</var>, <var>'[order]'</var>)</h2>
<p>Returns the page title.
The <dfn>title</dfn> parameter can be either a string or an array.
The <dfn>sep</dfn> parameter is the delimiter to use between parts of the title. The default <dfn>sep</dfn> value is &laquo;.
The <dfn>order</dfn> parameter determines whether the page title should go in ascending or descending order (right to left or left to right).
 Values can be <dfn>right</dfn> or <dfn>left</dfn>. Default is <dfn>right</dfn>.
</p>
