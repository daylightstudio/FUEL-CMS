<h1>Posts</h1>
<p>Posts are where you create articles for your blog.  
Posts are powered by a <dfn>blog_posts_model</dfn> and have a grandparent class of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>.</p>

<h2>Post Fields</h2>
<p>Below are the fields to fill out for a post:</p>
<ul>
	<li><strong>Title</strong> - The title of the post</li>
	<li><strong>Content</strong> - The HTML content for the post</li>
	<li><strong>Formatting</strong> - Formatting to apply to the content of the post. Options are Automatic (which uses CodeIgniter's <a href="http://codeigniter.com/user_guide/helpers/typography_helper.html" target="_blank">auto_typography</a> function), <a href="<?=user_guide_url('helpers/markdown')?>">Markdown</a>  or None</li>
	<li><strong>Excerpt</strong> - The excerpt of the post content to be displayed</li>
	<li><strong>Permalink</strong> - The friendly URL to apply to the post</li>
	<li><strong>Author</strong> - The author of the post</li>
	<li><strong>Sticky</strong> - Determines whether to keep the article on the top pages. Gives a higher sorting precedence.</li>
	<li><strong>Allow_comments</strong> - Allow comments for the post. Global setting for comments, <dfn>allow_comments</dfn>, must be set to 1</li>
	<li><strong>Published</strong> - Determines whether to display the content</li>
	<li><strong>Date added</strong> - The publish date the post</li>
</ul>

<br />

<h1>The Post Model Properties</h1>
<p>The post model has the following properties:</p>
<ul>
	<li>id</li>
	<li>title</li>
	<li>content</li>
	<li>content_filtered (stripped html)</li>
	<li>formatting</li>
	<li>excerpt</li>
	<li>permalink</li>
	<li>author_id</li>
	<li>sticky</li>
	<li>allow_comments</li>
	<li>date_added</li>
	<li>last_modified</li>
	<li>published (returns 'yes' or 'no')</li>
</ul>

<br />

<h1>The Post Model Methods</h1>
<p>The post model has the following specific methods:</p>

<h2>get_content_formatted(<var>strip_images</var>)</h2>
<p>Gets the post content with the selected formatting settings applied. You can optionally strip all HTML tags from the excerpt.</p>

<h2>get_excerpt_formatted(<var>char_limit</var>, <var>'[readmore]'</var>)</h2>
<p>Gets the post excerpt.
The <dfn>char_limit</dfn> parameter will return only a certain character limit (if so, strip_tags is applied).
The <dfn>readmore</dfn> parameter will be appended to the end of the excerpt with a link to the full post.
</p>

<h2>is_published()</h2>
<p>Returns a boolean of whether the post is published</p>

<h2>get_comments(<var>'[order]'</var>, <var>[limit]</var>)</h2>
<p>
Returns an array of comment objects.
Default order is date added in ascending order.
</p>

<h2>get_comments_count(<var>'[order]'</var>, <var>[limit]</var>)</h2>
<p>
Returns the number of comments for the post
Default order is <dfn>date added</dfn> in ascending order.
</p>

<h2>get_categories(<var>'[order]'</var>)</h2>
<p>
Returns an array of category objects associated with the post
Default ordering is by <dfn>name</dfn> in ascending order.
</p>

<h2>get_categories_linked(<var>'[order]'</var>, <var>'[join]'</var>)</h2>
<p>
Returns a string of category links.
Default ordering is by <dfn>name</dfn> in ascending order.
Default join parameter is ', '.
</p>

<h2>get_author()</h2>
<p>Returns the author object.</p>

<h2>get_url(<var>'full_path'</var>)</h2>
<p>
Returns the url of the post.
The default for <dfn>full_path</dfn> is set to TRUE.
</p>

<h2>get_rss_date()</h2>
<p>Returns an rss formatted date for the post.</p>

<h2>get_atom_date()</h2>
<p>Returns an atom formatted date for the post.</p>

<h2>get_date_formatted(<var>'[format]'</var>)</h2>
<p>
Returns the date of the post.
Takes a <a href="http://www.php.net/date">date</a> format. Default is 'M d, Y'.
</p>

<h2>get_allow_comments()</h2>
<p>Returns a boolean as to whether comments are allowed.</p>

<h2>is_within_comment_time_limit()</h2>
<p>Returns a boolean as to whether the current time is within the time limits specified by the post to comment.</p>

<h2>get_social_bookmarking_links()</h2>
<p>Gets a string of bookmarking links.</p>

<h2>get_facebook_recommend()</h2>
<p>Returns a Facebook Recommend button.</p>

<h2>get_digg()</h2>
<p>Returns a digg button.</p>

<h2>get_tweetme()</h2>
<p>Returns a TweetMe button.</p>


