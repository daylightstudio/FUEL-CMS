<h1>Redirects</h1>
<p>FUEL has added a way to deal with 301 redirects that don't require the need for .htaccess. 
Redirects are configured in the <dfn>fuel/application/config/redirects.php</dfn> file.
To add a redirect, add a URI location as a key and the redirect location as the value.
Key values can use regular expression syntax similar to <a href="http://codeigniter.com/user_guide/general/routing.html" target="_blank">routes</a>.
A redirect will only work if there are no pages detected by FUEL. It will check the redirects right before
delivering a 404 error.
</p>

<p class="important">Do NOT include beginning or trailing slashes for BOTH keys and values</p>

<pre class="brush:php">
$redirect['news/my_old_news_item'] = 'news/my_brand_new_news_item';
</pre>
