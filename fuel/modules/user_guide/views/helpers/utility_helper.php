<h1>Utility Helper</h1>

<p>A simple collection if helper functions designed for debugging content and echoing it out to the screen.
This helper is <strong>autoloaded</strong>.</p>
</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('utility');
</pre>

<p>The following functions are available:</p>

<h2>capture(<var>'on'</var>, <var>'clean'</var>)</h2>
<p>Capture content a buffer into a string and output it as needed.
The <dfn>clean</dfn> parameter can either have the value of <dfn>all</dfn>,
which will end and clean the output buffering, or <dfn>TRUE</dfn>, which will
just clean the output buffering.
</p>
<pre class="brush: php">
capture(true);

...code goes here

$str = capture(false, 'all');

echo $str;
</pre>

<h2>is_true_val(<var>'val'</var>)</h2>
<p>Determine if a string is a true value and format it as a standard true value.</p>
<pre class="brush: php">
$val = true;

if(($bool = is_true_val($val)) == true)
{
    ...code goes here
}
</pre>

<h2>is_serialized_str(<var>'data'</var>)</h2>
<p>Determine if a string is a serialized array</p>
<pre class="brush: php">
$data = serialize(array(0=>'val1', 1=>'val2'))

if(is_serialized_str($data))
{
    ...code goes here
}
</pre>

<h2>print_obj(<var>'obj'</var>, <var>'return'</var>)</h2>
<p>Pass an object variable and print or return the contents in human-readible format.</p>
<pre class="brush: php">
$obj = new Object();

$str = print_obj($object, true);

echo $str;
</pre>


<h2>CI()</h2>
<p>Returns the global CodeIgniter super object</p>
<pre class="brush: php">
CI()->load->model('my_model');
</pre>