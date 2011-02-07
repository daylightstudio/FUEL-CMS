<h1>MY String Helper</h1>

<p>Contains functions to be used with string. Extends CI's string helpers and is <strong>autoloaded</strong>.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('string');
</pre>

<p>The following functions are available:</p>

<h2>eval_string(<var>str</var>, <var>[vars]</var>)</h2>
<p>Evaluates a strings PHP code. Used for outputing FUEL page data. 
The optional <dfn>vars</dfn> parameter is an array of variables to be passed to the string.
</p>

<pre class="brush: php">
$str = "This is my &lt;php echo strtoupper('upper case'); ?&gt; string";
eval_string($str); // This is my UPPER CASE string
</pre>

<p class="important">Because this function evaluates PHP code, make sure the data being sent to it is safe.</p>

<h2>lang(<var>str</var>, <var>[vars]</var>)</h2>
<p>Get translated local strings with arguments.</p>

<pre class="brush: php">
// in lang file
$lang['lang_key'] = "This is a lang file entry for %1s.";
...
echo lang('lang_key', 'my_param'); //This is lang file entry for my_param.
</pre>


<h2>pluralize(<var>num</var>, <var>str</var>, <var>[plural]</var>)</h2>
<p>Add an <dfn>s</dfn> to the end of string based on the number. If the count is only one, it will not add the <dfn>s</dfn>.</p>

<pre class="brush: php">
echo 'The search returned '.count($results).' '.pluralize(count($results), 'result');
</pre>


<h2>strip_whitespace(<var>str</var>, <var>[vars]</var>).</h2>
<p>Strips extra whitespace from a string. It will remove all whitespace with more then one space.</p>

<pre class="brush: php">
$str = '&nbsp;&nbsp;my string&nbsp;&nbsp;&nbsp;without &nbsp; whitespace';
echo strip_whitespace($str); // <?=strip_whitespace("  my string   without   whitespace")?>
</pre>


<h2>smart_ucwords(<var>str</var>, <var>[exceptions]</var>)</h2>
<p>Converts words to title case and allows for exceptions. 
The exceptions parameter is an array of strings that will be 
ignored. 
</p>

<pre class="brush: php">
$str = "the return of the jedi";
echo smart_ucwords($str); // <?=smart_ucwords('the return of the jedi')?>
</pre>


<h2>safe_htmlentities(<var>str</var>, <var>protect_amp</var>)</h2>
<p>Safely converts a string's entities without encoding HTML tags and quotes. The <dfn>protect_amp</dfn> parameter will protect ampersands as well and is set to <dfn>TRUE</dfn> by default.</p>

<pre class="brush: php">
$str = '<p>This is a test with special characters : “”‘’™–…&©"</p>';
echo safe_htmlentities($str, ENT_NOQUOTES, 'UTF-8', FALSE); 
</pre>

<h2>php_to_template_syntax(<var>str</var>)</h2>
<p>Convert PHP syntax to <a href="http://dwoo.org" target="_blank">Dwoo templating syntax</a>. Must use the PHP alternative syntax for if and foreach loops to be translated correctly.</p>

<pre class="brush: php">
$str = '&lt;?=$my_var?&gt;';
echo php_to_template_syntax($str);//  {$my_var}
</pre>

<h2>parse_template_syntax(<var>str</var>, <var>vars</var>)</h2>
<p>Parses a strings <a href="http://dwoo.org" target="_blank">Dwoo templating syntax</a>.</p>

<pre class="brush: php">
$vars = array('name' => 'Luke');
$str = '{name}, I am your Father.';
echo parse_template_syntax($str, $vars);//  Luke, I am your Father.
</pre>


