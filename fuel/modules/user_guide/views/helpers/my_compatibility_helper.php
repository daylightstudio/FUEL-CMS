<h1>MY Compatibility Helper</h1>

<p>Contains functions to bridge the gap with older versions of PHP. Extends CI's compatability helpers and is <strong>autoloaded</strong>.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('compatibility');
</pre>

<p>The following functions are available:</p>

<h2>json_encode(<var>data</var>)</h2>
<p>Used for older versions of PHP that don't support <a href="http://php.net/manual/en/function.json-encode.php">json_encode</a>.</p>

<pre class="brush: php">
$my_arr[0] = array('first_name' => 'Darth', 'last_name' => 'Vader');
$my_arr[1] = array('first_name' => 'Luke', 'last_name' => 'Skywalker');
$my_arr[2] = array('first_name' => 'Han', 'last_name' => 'Solo');

$new_arr = array_sorter($my_arr, 'first_name', 'asc');

echo $new_arr[0]['first_name'].' '.$new_arr[0]['last_name']; // Luke Skywalker
</pre>


<h2>json_decode(<var>data</var>)</h2>
<p>Used for older versions of PHP that don't support <a href="http://php.net/manual/en/function.json-decode.php">json_decode</a>.</p>

<pre class="brush: php">
echo $my_obj[0]->first_name.' '.$my_obj[0]->last_name; // Luke Skywalker
</pre>


<h2>str_getcsv(<var>input</var>, <var>[delimiter]</var>, <var>[enclosure]</var>)</h2>
<p>Used for older versions of PHP that don't support <a href="http://php.net/manual/en/function.str-getcsv.php">str_getcsv</a>.</p>


<h2>str_putcsv(<var>input</var>, <var>[delimiter]</var>, <var>[enclosure]</var>)</h2>
<p>Not really a compatibility function since it doesn't exist natively in PHP. However it probably should so we provide it here.</p>
