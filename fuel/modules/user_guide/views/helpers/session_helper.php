<h1>Session Helper</h1>

<p>Contains convenience functions for grabbing session information. Is parsed by page templates.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('session');
</pre>

<p>The following function is available:</p>

<h2>session_userdata(<var>'key'</var>)</h2>
<p>Returns a session variable.
Shortcut for commonly used CI <a href="http://codeigniter.com/user_guide/libraries/sessions.html" target="_blank">Session class</a> method.</p>


<h2>session_flash(<var>'key'</var>)</h2>
<p>Returns a session flash variable. 
Shortcut for commonly used CI <a href="http://codeigniter.com/user_guide/libraries/sessions.html" target="_blank">Session class</a> method.</p>
