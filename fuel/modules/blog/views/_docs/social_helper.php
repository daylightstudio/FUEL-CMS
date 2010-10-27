<h1>Social Helper</h1>

<p>Contains functions for social networks sites to use in your blog pages. The 
blog module has a <dfn>config/social.php</dfn> file to configure the social links.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->module_helper(BLOG_FOLDER, 'social');
</pre>

<p>The following function is available:</p>

<h2>social_bookmarking_links(<var>'post_url'</var>, <var>'post_title'</var>, <var>'[joiner]'</var>, <var>[only_show]</var>)</h2>
<p>Creates HTML links for the various social sites set in the social configuration. 
The <dfn>joiner</dfn> parameter is the delimiter to use between the links and defaults to a pipe (<strong>|</strong>).
The <dfn>only_show</dfn> parameter specifies which social links you want to display.
</p>


<h2>social_facebook_recommend(<var>'post_url'</var>)</h2>
<p>Creates HTML iframe for facebook recommend.</p>


<h2>social_facebook_share()</h2>
<p>Creates javascript for facebook share.</p>


<h2>social_digg(<var>post_url</var>, <var>post_title</var>, <var>[size]</var>)</h2>
<p>Creates Digg button.</p>


<h2>social_tweetme(<var>post_url</var>)</h2>
<p>Creates a TweetMe button.</p>


<h2>social_stumbleupon()</h2>
<p>Creates stumbleupon button.</p>


<h2>generate_social_js(<var>path</var>, <var>[async]</var>)</h2>
<p>Generates javascript for embedding social media links in a page.</p>


<h2>social_url()</h2>
<p>Creates a social url link.</p>
