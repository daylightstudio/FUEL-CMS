<h1>Google Helper</h1>

<p>Contains a function for inserting Google Analytics code.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('google');
</pre>

<p>The following function is available:</p>

<h2>google_analytics(<var>[uacct]</var>)</h2>
<p>Returns Google Analytics code. If no <dfn>uacct</dfn> is provided, it will look for a config value of <dfn>google_uacct</dfn>.</p>
<pre class="brush: php">
echo google_analytics('abc123');

// echos out
&lt;script type="text/javascript"&gt;
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'abc123']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
&lt;/script&gt;
</pre>