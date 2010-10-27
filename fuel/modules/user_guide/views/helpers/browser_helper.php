<h1>Browser Helper</h1>

<p>Contains a function used in retrieving browser information.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('browser');
</pre>

<p>The following function is available:</p>

<h2>browser_info()</h2>
<p>Returns a key value array with the key being the name of the brower and the value being the version number.</p>

<pre class="brush: php">
$browser_info = browser_info();
echo key($browser_info); // e.g. firefox
echo current($browser_info); // e.g. 3.6
</pre>