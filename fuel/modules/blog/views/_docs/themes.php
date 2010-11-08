<h1>Creating Themes</h1>
<p>Themes are a set of view files used to render your blog. Because you actually
have access to the CodeIgniter super object, your theme files can act like
any other CodeIgniter view file. The <dfn>$blog</dfn> variable, which is an instance of the 
<a href="<?=user_guide_url('modules/blog/fuel_blog')?>">Fuel_blog class</a>, is passed to 
each main view file as well. You could even load in directly any of the blog's models 
(<a href="<?=user_guide_url('modules/blog/posts')?>">posts</a>, 
	<a href="<?=user_guide_url('modules/blog/categories')?>">categories</a>, 
	<a href="<?=user_guide_url('modules/blog/comments')?>">comments</a>, 
	<a href="<?=user_guide_url('modules/blog/links')?>">links</a> and 
	<a href="<?=user_guide_url('modules/blog/authors')?>">author</a>).
</p>

<p>Below are steps to creating a new theme:</p>
<ol>
	<li>Duplicate the default theme and rename the folder</li>
	<li>In the <a href="<?=user_guide_url('modules/blog/settings')?>">settings</a> module, change the <dfn>Theme location</dfn> parameter to the path to your new theme folder</li>
	<li>Edit the files listed below to your liking</li>
</ol>

<h2>View Files</h2>
<p>The following files can be found in your renamed theme folder for you to begin editing to your liking:</p>

<h3>Main View Files</h3>
<ul>
	<li><strong>index.php</strong> - the main homepage of the blog. Displays post excerpts (similar to posts.php below)</li>
	<li><strong>archives.php</strong> - displays a list of older posts grouped by month</li>
	<li><strong>author.php</strong> - displays the authors bio information</li>
	<li><strong>authors.php</strong> - displays a list of authors for the blog</li>
	<li><strong>categories.php</strong> - displays a list of categories</li>
	<li><strong>category.php</strong> - displays all the posts for a given category</li>
	<li><strong>post.php</strong> - displays the contents of a single post</li>
	<li><strong>posts.php</strong> - displays post excerpts</li>
	<li><strong>search.php</strong> - displays the search results</li>
</ul>

<h3>Blocks View Files</h3>
<ul>
	<li><strong>about.php</strong> - displays the description found in the settings</li>
	<li><strong>archives.php</strong> - displays side menu links for older blog posts</li>
	<li><strong>author.php</strong> - displays the various authors of the blogs and number of posts associated with them</li>
	<li><strong>categories.php</strong> - displays side menu links of blog categories</li>
	<li><strong>comment_form.php</strong> - displays the comment form for a post</li>
	<li><strong>comment_thanks.php</strong> - displays the thanks information after a successful comment has been made on a post</li>
	<li><strong>header.php</strong> - the HTML header content which normally includes the RSS feed and css links</li>
	<li><strong>post_unpublished.php</strong> - displayed when a blog post is not currently published (and you are logged in to FUEL)</li>
	<li><strong>posts.php</strong> - used to render an post excerpts and is used for both the <dfn>posts</dfn> and <dfn>category</dfn> main views</li>
	<li><strong>search.php</strong> - displays the side menu search box</li>
	<li><strong>sidemenu.php</strong> - displays all the contents for the side menu</li>
</ul>
