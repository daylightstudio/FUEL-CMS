<h1>Markdown Helper</h1>

<p>This helper has the markdown function originally developed by 
<a href="http://daringfireball.net/projects/markdown/" target="_blank">John Gruber</a> 
and modifed by <a href="http://www.michelf.com/projects/php-markdown/">Michel Fortin</a>
to be used for PHP. It has some similar characteristics to the autotypography function in the
typography helper. 

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('markdown');
</pre>

<h2>markdown(<var>str</var>)</h2>
<p>Converts normal text into html syntax. </p>

<pre class="brush: php">
$str = '
This is a paragraph.

* list item 1
* list item 2

';
echo markdown($str);

// echos out
&lt;p&gt;This is a paragraph.&lt;p&gt;

&lt;ul&gt;
	&lt;li&gt;list item 1&lt;/li&gt;
	&lt;li&gt;list item 2&lt;/li&gt;
&lt;/ul&gt;
</pre>

<p class="important">For complete syntax documentation 
<a href="http://daringfireball.net/projects/markdown/syntax" target="_blank">click here</a>.
</p>