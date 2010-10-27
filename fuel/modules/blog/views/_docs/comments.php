<h1>Comments</h1>
<p>Blog comments are created from people commenting on your posts. After a person has commented, the author is sent an email notification.
He can then login to FUEL and publish the comment (assuming the <dfn>monitor_comments</dfn> setting is set) and reply to the comment.
Comments are powered by a <dfn>blog_comments_model</dfn> and have a grandparent class of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>.
</p>

<h2>Comment Fields</h2>
<p>Below are the fields to fill out to submit a comment from the blog (note that author in this case refers to the comment and not the post):</p>
<ul>
	<li><name>author_name</name> - the commentor's name</li>
	<li><name>author_email</name> - the commentor's email address</li>
	<li><name>author_wesite</name> - the commentor's website address</li>
</ul>

<h2>Comment Security</h2>
<p>The Blog Settings module has several security options for comments:</p>
<ul>
	<li><strong>Akismet key</strong> - The <a href="http://akismet.com/personal/" target="_blank">Akismet</a> antispam key to use for checking the validity of comments</li>
	<li><strong>Allow comments</strong> - You have the option to allow comments at both a <a href="<?=user_guide_url('modules/blog/settings')?>">global settings</a> level and at a per post level.</li>
	<li><strong>Use captchas</strong> - Adds a captcha to the comment form</li>
	<li><strong>Monitor comments</strong> - Sends an email to the author to publish the comments.</li>
	<li><strong>Save spam</strong> - Whether to save comments marked as spam by Akismet or not</li>
	<li><strong>Comment submission time limit</strong> - The time limit allow for multiple comment submission</li>
	<li><strong>Comments time_limit</strong> - How the comment form for a post remains active after the creation date</li>
</ul>

<br />

<h1>The Comment Model Properties</h1>
<p>The comment object model has the following properties:</p>
<ul>
	<li>id</li>
	<li>post_id</li>
	<li>parent_id (parent comment id if it exists)</li>
	<li>author_id</li>
	<li>author_name</li>
	<li>author_email</li>
	<li>author_website</li>
	<li>author_ip</li>
	<li>is_spam</li>
	<li>content</li>
	<li>published (returns 'yes' or 'no')</li>
	<li>date_added</li>
	<li>last_modified</li>
</ul>

<br />

<h1>The Comment Model Methods</h1>
<p>The comment object model has the following specific methods:</p>

<h2>content_formatted()</h2>
<p>Returns the content of the comment.</p>

<h2>get_post()</h2>
<p>Returns the post associate with the comment.</p>

<h2>is_duplicate()</h2>
<p>Returns boolean value whether the comment is considered a duplicate of an existing.</p>

<h2>is_by_post_author()</h2>
<p>Returns a boolean as to whether the comment is by the post's author or not.</p>

<h2>is_child()</h2>
<p>Returns a boolean as to whether the comment is a child of another comment.</p>

<h2>get_author_and_link()</h2>
<p>Returns the comments author and link if a website was specified in the comment.</p>

<h2>get_date_formatted(<var>'[format]'</var>)</h2>
<p>Returns a formatted version of the date of the comment.
Takes a <a href="http://www.php.net/date">date</a> format. Default is 'M d, Y'.
</p>


