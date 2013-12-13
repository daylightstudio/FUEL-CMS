<h1>Redirects</h1>
 
<p>FUEL has added a way to deal with page redirects that doesn't require the need for .htaccess. 
Redirects are configured in the <dfn>fuel/application/config/redirects.php</dfn> file.
To add a redirect, add a URI location as a key and the redirect location as the value 
to either the <dfn>passive_redirects</dfn> (recommended) or <dfn>aggressive_redirects</dfn> config parameters.
<dfn>passive_redirects</dfn> will only work if there are no pages detected by FUEL. 
This means that it will only incur the expense of looping through the redirects list if no pages are found. 
Alternatively, <dfn>aggressive_redirects</dfn> will redirect no matter what but will incur the cost of looping the the redirects list on every request.
</p>

<p>You can also pass additional parameters such as the <dfn>case_sensitive</dfn>, <dfn>http_code</dfn> and <dfn>max_redirects</dfn> with your redirect if you use an array syntax like so:</p>
<pre class="brush:php">
// non key array
$config['passive_redirects'] = array(
									'news/my_old_news_item' => array('news/my_brand_new_news_item', TRUE, 302)
									);
 
// keyed array
$config['passive_redirects'] = array(
									'news/my_old_news_item' => array('url' => 'news/my_brand_new_news_item', 'case_sensitive' => TRUE, 'http_code' => 302)
									);
</pre>

<p class="important">Key values can use regular expression syntax similar to <a href="http://codeigniter.com/user_guide/general/routing.html" target="_blank">routes</a>.</p>

<br />

<p>Programmatically, you can access the instantiated <a href="<?=user_guide_url('libraries/fuel_redirects')?>">Fuel_redirects</a> object like so:</p>
<pre class="brush:php">
$this->fuel->redirects->execute();
</pre>


<p>You can also set global configuration values from within the <dfn>fuel/application/config/redirects.php</dfn> file like so:</p>
<pre class="brush:php">
$config['http_code'] = 302;
$config['case_sensitive'] = FALSE;
$config['max_redirects'] = 2;
</pre>
<p class="important">The <dfn>max_redirects</dfn> is used to prevent scenarios where it can cause an infinite redirect loop.</p>

<h3>SSL</h3>
<p>The <dfn>ssl</dfn> configuration provides an easy way to force SSL encryption (https) for your page URLs. The following example would 
restrict any url under "store" to use SSL encription when in the "production" environment:</p>
<pre class="brush:php">
$config['ssl'] = array('production' => array(
	'^store|store/:any'
)); 
</pre>

<h3>Enforce Host</h3>
<p>The <dfn>host</dfn> configuration provides an alternative to .htaccess for redirecting your page if the current $_SERVER['HTTP_HOST'] PHP variable doesn't match what is specified
	as the "host" value in the redirect configuration. The following example would redirect any requests to "mysite.com" if the HTTP_HOST value doesn't match in the "production" environment (e.g. www.mysite.com):</p>
<pre class="brush:php">
$config['host'] = array('production' => 'mysite.com'); 
</pre>

<h3>Testing Redirects</h3>
<p>If you are needing to check the redirects, you can utilize <a href="<?=user_guide_url('libraries/fuel_redirects')?>">Fuel_redirects::test method</a> and create a simple view file at <span class="file">fuel/applications/views/redirects_test.php</span> with the following:</p>
<pre class="brush:php">
&lt;?php 
echo '<pre>';
print_r($CI->fuel->redirects->test());
echo '</pre>';
?&gt;
</pre>
<p class="important">Regular expressions and shorthand :any will not be properly translated and will return errors.</p>

<h3>Redirect Hooks</h3>
<ul>
	<li><strong>pre_redirect</strong>: Called right before a page is redirected</li>
	<li><strong>pre_404</strong>: Called right before a 404 error page is displayed. You must use the <a href="<?=user_guide_url('helpers/my_url_helper#func_redirect_404')?>">redirect_404()</a> function instead of the show_404() function for this hook to be executed.</li>
</ul>
 
<h3>Custom 404 Page</h3>
<p>You can create a custom 404 page by either creating a page in the CMS or a view file named <dfn>404_error</dfn>.</p>
 
<p class="important">If you are having trouble with a redirect, check that you don't have any routes, or pages with assigned views at the URI path in question.
Additionally, if you have the FUEL configuration setting <dfn>$config['max_page_params']</dfn> with a value greater then 0, then you may run into issues where 
the rendered page is passing a segment as a parameter instead of redirecting. In that case, you may want to change the <dfn>$config['max_page_params']</dfn> 
value to a key value pair with the keys being the URI paths and the values being the number of segments to accept at those URI paths (e.g. $config['max_page_params'] = array('about/news' => 1);)
</p>