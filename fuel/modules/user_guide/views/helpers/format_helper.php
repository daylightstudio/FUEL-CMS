<h1>Format Helper</h1>

<p>Contains function is used in formatting string values.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('format');
</pre>

<p>The following function is available:</p>

<h2>dollar(<var>'value'</var>)</h2>
<p>Returns a dollar formatted string</p>
<pre class="brush: php">
$str = capture(false, 'all');
echo dollar('100000');
// $100,000
</pre>