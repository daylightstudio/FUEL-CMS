<h1>MY_URI Class</h1>
<p>The <dfn>MY_URI Class</dfn> extends <a href="http://codeigniter.com/user_guide/libraries/uri.html" target="_blank">CI_URI</a> result class.
It adds a couple methods listed below:
</p>

<h2>$this->uri->assoc_to_uri(<var>'array'</var>, <var>[noemptys]</var>)</h2>
<p>Overwrites existing CI method that generates a URI string from an associative array but with the added <dfn>noemptys</dfn> parameter.
This will not add empty array values to the URI string
</p>

<pre class="brush:php">
$uri = array('id' => 1, 'param1' => '', 'param2' => 'value2');
$this->uri->assoc_to_uri($uri, TRUE);
echo $uri; // id/1/param2/value2
</pre>


<h2>$this->uri->init_get_params()</h2>
<p>Sets the $_GET parameters.</p>

<pre class="brush:php">
...
// a page with a query string of ?id=1234
...
$this->uri->init_get_params();
echo $this->input->get('id'); // 1234
</pre>