<h1>MY HTML Helper</h1>

<p>Contains functions to be used with arrays. Extends CI's array helpers and is <strong>autoloaded</strong>.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('html');
</pre>

<p>The following functions are available:</p>

<h2>tag(<var>open</var>, <var>vals</var>)</h2>
<p>Wrap a string or array of values in an opening and closing tag.</p>

<pre class="brush: php">
$p[0] = 'This is paragraph 1';
$p[1] = 'This is paragraph 2';
$p[2] = 'This is paragraph 3';
echo tag('p', $p);

// echos out
&lt;p&gt;This is paragraph 1&lt;/p&gt;
&lt;p&gt;This is paragraph 2&lt;/p&gt;
&lt;p&gt;This is paragraph 3&lt;/p&gt;
</pre>


<h2>quote(<var>str</var>, <var>[cite]</var>, <var>[title]</var>, <var>[class_prefix]</var>)</h2>
<p>Wrap a string into an HTML blockquote with quotes and cite added.</p>

<pre class="brush: php">
echo quote('Use the force.', 'Obi Wan', 'Jedi', 'quote');

// echos out
	&lt;blockquote class="quote"&gt;
		&lt;span class="quote_left"&gt;&amp;#8220;&lt;/span&gt;Use the force&lt;span class="quote_right"&gt;&amp;#8221;&lt;/span&gt;<br />
	&lt;/blockquote&gt;
	&lt;cite&gt;
		Obi Wan, &lt;span class="cite_title"&gt;Jedi&lt;/span&gt;
	&lt;/cite&gt;
&lt;/div&gt;
</pre>


<h2>html_attrs(<var>attrs</var>)</h2>
<p>Creates HTML attributes from an array.</p>

<pre class="brush: php">
echo $attrs = array('height' => 100, 'width' => 100); // height="100" width="100"
</pre>
