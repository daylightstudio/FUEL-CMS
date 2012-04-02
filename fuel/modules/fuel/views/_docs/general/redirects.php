<h1>Redirects</h1>

<p>FUEL has added a way to deal with 301 redirects that don't require the need for .htaccess. 
Redirects are configured in the <dfn>fuel/application/config/redirects.php</dfn> file.
To add a redirect, add a URI location as a key and the redirect location as the value.
Key values can use regular expression syntax similar to <a href="http://codeigniter.com/user_guide/general/routing.html" target="_blank">routes</a>.
A redirect will only work if there are no pages detected by FUEL. It will check the redirects right before
delivering a 404 error.</p>

<pre class="brush:php">
$redirect['news/my_old_news_item'] = 'news/my_brand_new_news_item';
</pre>

<p>Programmatically, you can access the instantiated <a href="<?=user_guide_url('libraries/fuel_redirects')?>">Fuel_redirects</a> object like so:</p>
<pre class="brush:php">
$this->fuel->redirects->execute();
</pre>


<p class="important">If you are having trouble with a redirect, check that you don't have any routes, or pages with assigned views at the URI path in question.
Additionally, if you have the FUEL configuration setting <dfn>$config['max_page_params']</dfn> with a value greater then 0, then you may run into issues where 
the rendered page is passing a segment as a parameter instead of redirecting. In that case, you may want to change the <dfn>$config['max_page_params']</dfn> 
value to key value pair with the keys being the URI paths and the values being the number of segments to accept at those URI paths (e.g. $config['max_page_params'] = array('about/news' => 1);)
</p>