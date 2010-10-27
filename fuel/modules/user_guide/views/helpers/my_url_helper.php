<h1>MY URL Helper</h1>

<p>Contains functions to be used with urls. Extends CI's url helper and is autoloaded.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('url');
</pre>

<p>The following functions are available:</p>

<h2>site_url(<var>uri</var>)</h2>
<p>Overwrites CI's <dfn>site_url()</dfn> function so it can ignore uris with "http" at the beginning.</p>

<h2>https_site_url(<var>uri</var>, <var>[remove_https]</var>)</h2>
<p>Creates https URLs or removes the https from the site_url if the remove_https flag is set to <dfn>TRUE</dfn>.</p>

<h2>uri_path(<var>[rerouted]</var>, <var>[start_index]</var>)</h2>
<p>Returns the uri path and will normalize the slashes.</p>

<h2>uri_segment(<var>[n]</var>, <var>[rerouted]</var>)</h2>
<p>Returns the uri segment. Acts as a convenience function to the $this->uri->segment($n) and $this->uri->rsegment($n).</p>

<h2>is_http_path(<var>path</var>)</h2>
<p>Determines if a path uses http at the beginning.</p>

<h2>is_home()</h2>
<p>Determines the current URI is the homepage or not.</p>

