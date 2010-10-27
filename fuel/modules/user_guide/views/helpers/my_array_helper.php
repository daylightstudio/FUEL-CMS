<h1>MY Array Helper</h1>

<p>Contains functions to be used with arrays. Extends CI's array helpers.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('array');
</pre>

<p>The following functions are available:</p>

<h2>array_sorter(<var>array</var>, <var>index</var>, <var>['order']</var>, <var>[nat_sort]</var>, <var>[case_sensitive]</var>)</h2>
<p>Array sorter that will sort on an array key and allows for asc/desc order. It returns an array.

</p>

<pre class="brush: php">
$my_arr[0] = array('first_name' => 'Darth', 'last_name' => 'Vader');
$my_arr[1] = array('first_name' => 'Luke', 'last_name' => 'Skywalker');
$my_arr[2] = array('first_name' => 'Han', 'last_name' => 'Solo');

$new_arr = array_sorter($my_arr, 'first_name', 'asc');

echo $new_arr[0]['first_name'].' '.$new_arr[0]['last_name']; // Luke Skywalker
</pre>


<h2>object_sorter(<var>data</var>, <var>key</var>, <var>[order]</var>)</h2>
<p>Array sorter that will sort an array of objects based on an objects property and allows for asc/desc order. Default order is <dfn>'asc'</dfn>. Does not return anything but changes the original object by reference.</p>

<pre class="brush: php">
$obj1 = new stdClass();
$obj1->first_name = 'Darth';
$obj1->last_name = 'Vader';

$obj2 = new stdClass();
$obj2->first_name = 'Luke';
$obj2->last_name = 'Skywalker';

$obj3 = new stdClass();
$obj2->first_name = 'Han';
$obj2->last_name = 'Solo';

$my_obj[0] = $obj1;
$my_obj[1] = $obj2;
$my_obj[2] = $obj3;

object_sorter($my_obj, 'first_name', 'asc');

echo $my_obj[0]->first_name.' '.$my_obj[0]->last_name; // Luke Skywalker
</pre>


<h2>options_list(<var>data</var>, <var>[value]</var>, <var>[label]</var>)</h2>
<p>Creates a key/value array based on an original array. 
Can be used in conjunction with the Form library class (e.g. $this->form->select('characters, option_list($options)))</p>

<pre class="brush: php">
$my_obj[0] = array('id' => 1, 'first_name' => 'Darth', 'last_name' => 'Vader');
$my_obj[1] = array('id' => 2, 'first_name' => 'Luke', 'last_name' => 'Skywalker');
$my_obj[2] = array('id' => 3, 'first_name' => 'Han', 'last_name' => 'Solo');

$options = options_list($my_arr, 'id', 'last_name');
echo $options[1]; // Vader
echo $options[2]; // Skywalker
echo $options[3]; // Solo
</pre>


<h2>parse_string_to_array(<var>str</var>)</h2>
<p>Parses a string in the format of key1="val1" key2="val2" into an array.</p>

<pre class="brush: php">
$str = 'first_name="Darth" last_name="Vader"';
$options = parse_string_to_array($str);
echo $options['first_name']; // Darth
echo $options['last_name']; // Vader
</pre>

