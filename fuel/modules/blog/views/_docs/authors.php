<h1>Authors</h1>
<p>Blog Authors (Users) must have general access to the FUEL admin and permissions for the blog module.
Blog Authors are powered by a <dfn>blog_users_model</dfn> and have a grandparent class of <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model class</a>
</p>

<h2>Author Fields</h2>
<p>Below are the fields to fill out for an author:</p>
<ul>
	<li><strong>Display name</strong> - The display name of the user</li>
	<li><strong>Website</strong> - The author's website</li>
	<li><strong>About</strong> - Bio information about the author</li>
	<li><strong>Avatar Image</strong> - The image to associate with the author</li>
	<li><strong>Active</strong> - Determines whether the author page can be displayed</li>
</ul>

<br />

<h1>The Authors (User) Model Properties</h1>
<p>The author object has the following properties:</p>
<ul>
	<li>id</li>
	<li>first_name</li>
	<li>last_name</li>
	<li>name (virtual property combined first_name and last_name)</li>
	<li>email</li>
	<li>user_name</li>
	<li>display_name</li>
	<li>website</li>
	<li>about</li>
	<li>avatar_image</li>
	<li>date_added</li>
	<li>posts_count (virtual property)</li>
	<li>active (returns 'yes' or 'no')</li>
</ul>

<br />

<h1>The Authors (User) Model Methods</h1>
<p>The author object model has the following specific methods:</p>

<h2>get_url()</h2>
<p>Returns the link.</p>

<h2>get_posts_url('[full_path]')</h2>
<p>Returns posts the link.</p>

<h2>get_avatar_image_path()</h2>
<p>Returns image path to the avatar.</p>

<h2>get_avatar_img_tag()</h2>
<p>Returns image tag to the avatar.</p>