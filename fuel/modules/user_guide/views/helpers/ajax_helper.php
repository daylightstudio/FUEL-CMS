<h1>Ajax Helper</h1>

<p>Contains a function for detecting AJAX requests.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('ajax');
</pre>

<p>The following function is available:</p>

<h2>is_ajax()</h2>
<p>Returns a boolean value based on whether the page was requested via AJAX or not.</p>
<pre class="brush: php">
if (is_ajax())
{
	echo 'html code requested via AJAX';
}
else
{
	echo '&lt;html&gt;&lt;body&gt;This page was NOT an AJAX request&lt;/body&gt;&lt;/html&gt;';
}
</pre>