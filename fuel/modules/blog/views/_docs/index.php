<h1>Blog Documentation</h1>
<p>This Blog documentation is for version the Blog module version <?=BLOG_VERSION?>.</p>

<h2>Overview</h2>
<p>The FUEL Blog is a module that allows you to have blog functionality without the need
of installing a 3rd party application like Wordpress. It allows you to create posts, categorize and search them as well as allow others to comment on them.
There are various <a href="<?=user_guide_url('modules/blog/settings')?>">setting options</a> to taylor the blog to your liking. Read below for more information.
</p>

<h2>Installation</h2>
<p>The following are the steps to installing the FUEL Blog module:</p>
<ol>
	<li>Download the latest version of the FUEL Blog module from <a href="http://www.getfuelcms.com" target="_blank">getfuelcms.com</a></li>
	<li>Move the downloaded folder into the <dfn>fuel/modules</dfn> folder</li>
	<li>Run the sql file in the FUEL database</li>
	<li>Create a new theme for your blog (see <a href="<?=user_guide_url('modules/blog/themes')?>">Creating Themes</a>)</li>
	<li>The blog can be found at <dfn>/blog</dfn> of your website (you can use routes to change that though)</li>
</ol>

<h2>Sub-Modules</h2>
<ul>
	<li><a href="<?=user_guide_url('modules/blog/posts')?>">Posts</a></li>
	<li><a href="<?=user_guide_url('modules/blog/categories')?>">Categories</a></li>
	<li><a href="<?=user_guide_url('modules/blog/comments')?>">Comments</a></li>
	<li><a href="<?=user_guide_url('modules/blog/links')?>">Links</a></li>
	<li><a href="<?=user_guide_url('modules/blog/authors')?>">Authors</a></li>
	<li><a href="<?=user_guide_url('modules/blog/settings')?>">Settings</a></li>
</ul>

<h2>Libraries and Helpers</h2>
<ul>
	<li><a href="<?=user_guide_url('modules/blog/fuel_blog')?>">Fuel_blog Class</a></li>
	<li><a href="<?=user_guide_url('modules/blog/blog_helper')?>">blog_helper</a></li>
	<li><a href="<?=user_guide_url('modules/blog/social_helper')?>">social_helper</a></li>
</ul>

<h2>Misc.</h2>
<ul>
	<li><a href="<?=user_guide_url('modules/blog/themes')?>">Creating Themes</a></li>
	<li><a href="<?=user_guide_url('modules/blog/configuration')?>">Other Configuration</a></li>
</ul>
