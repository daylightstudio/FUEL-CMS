<h1>Convert Helper</h1>

<p>A simple collection if helper functions designed for conversion and decoding of string values.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('convert');
</pre>

<p>The following functions are available:</p>

<h2>ascii_to_hex(<var>ascii</var>)</h2>
<p>Convert ascii string to hex format. </p>


<h2>hex_to_ascii(<var>hex</var>)</h2>
<p>Convert hex string to ascii string.</p>

<h2>uri_safe_encode(<var>str</var>, <var>[hexify]</var>)</h2>
<p>Convert a string into a safe, encoded uri value. The <strong>hexify</strong> option will remove common non uri-safe characters like the equals (<dfn>=</dfn>) sign.</p>


<h2>uri_safe_decode(<var>str</var>, <var>[hexify]</var>)</h2>
<p>Decode a base64 encoded string value encoded with <dfn>uri_safe_encode</dfn>.  The <strong>hexify</strong> option will remove common non uri-safe characters like the equals (<dfn>=</dfn>) sign.</p>


<h2>uri_safe_batch_encode(<var>uri</var>, <var>[delimiter]</var>, <var>[hexify]</var>)</h2>
<p>Encode a key/value array or string into a URI safe value. The default delimiter is a pipe.</p>
<pre class="brush: php">
$params['first_name'] = 'Darth';
$params['last_name'] = 'Vader';
$seg_params = uri_safe_batch_encode($params, '|', TRUE);
echo $seg_params; // 5a6d6c7963335266626d46745a53394559584a306148787359584e305832356862575576566d466b5a584a38
</pre>


<h2>uri_safe_batch_decode(<var>uri</var>, <var>[delimiter]</var>, <var>[hexify]</var>)</h2>
<p>Decode a key/value array or string into a URI safe value. The default delimiter is a pipe.</p>

<pre class="brush: php">
$params = uri_safe_batch_decode($this->uri->segment(3), '|', TRUE);
echo $params['first_name']; // Darth
echo $params['last_name']; // Vader
</pre>
