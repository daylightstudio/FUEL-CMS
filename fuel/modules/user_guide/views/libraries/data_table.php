<h1>Data Table Class</h1>
<p>This class allows you to easily create tables of data by passing in a multi-dimensional array of data. 
This class provides methods to set the sorting, headers and data of the table. This is the class used by the FUEL admin for the table view of data.</p>

<h2>Initializing the Class</h2>

<p>The Data_table class is initialized using the <dfn>$this->load->library</dfn> function:</p>

<pre class="brush: php">$this->load->library('data_table');</pre>

<p>Alternatively, you can pass initialization parameters as the second parameter:</p>

<pre class="brush: php">$this->load->library('data_table', array('id'=>'data_table', 'header_on_class' => 'active'));</pre>

<h2>Configuring Data Table Information</h2>
<p>The <dfn>Data_table</dfn> class has the following configuration parameters:</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>id</strong></td>
			<td>data_table</td>
			<td>None</td>
			<td>ID to be used for the form</td>
		</tr>
		<tr>
			<td><strong>css_class</strong></td>
			<td>data</td>
			<td>None</td>
			<td>CSS class to be used with the form</td>
		</tr>
		<tr>
			<td><strong>headers</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>The table headers</td>
		</tr>
		<tr>
			<td><strong>table_attrs</strong></td>
			<td>array()</td>
			<td>Can be string or an array of attributes</td>
			<td>The table attributes</td>
		</tr>
		<tr>
			<td><strong>header_on_class</strong></td>
			<td>on</td>
			<td>None</td>
			<td>Table header on class</td>
		</tr>
		<tr>
			<td><strong>header_asc_class</strong></td>
			<td>asc</td>
			<td>None</td>
			<td>Table header asc class</td>
		</tr>
		<tr>
			<td><strong>header_desc_class</strong></td>
			<td>desc</td>
			<td>None</td>
			<td>Table header desc class</td>
		</tr>
		<tr>
			<td><strong>row_alt_class</strong></td>
			<td>alt</td>
			<td>None</td>
			<td>Row alternating class</td>
		</tr>
		<tr>
			<td><strong>sort_js_func</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>Javascript sorting function to be used</td>
		</tr>
		<tr>
			<td><strong>body_attrs</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>tbody attributes</td>
		</tr>
		<tr>
			<td><strong>action_delimiter</strong></td>
			<td>|</td>
			<td>None</td>
			<td>The text delimiter between each action</td>
		</tr>
		<tr>
			<td><strong>actions_field</strong></td>
			<td>first</td>
			<td>first, last</td>
			<td>Which column the actions are in</td>
		</tr>
		<tr>
			<td><strong>auto_sort</strong></td>
			<td>TRUE/FALSE (boolean)</td>
			<td>None</td>
			<td>Should sorting be done automatically?</td>
		</tr>
		<tr>
			<td><strong>only_data_fields</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Data columns that won't be displayed</td>
		</tr>
		<tr>
			<td><strong>default_field_action</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The default column action</td>
		</tr>
		<tr>
			<td><strong>row_id_key</strong></td>
			<td>id</td>
			<td>None</td>
			<td>The &lt;tr&gt; id prefix</td>
		</tr>
		<tr>
			<td><strong>row_action</strong></td>
			<td>TRUE/FALSE (boolean)</td>
			<td>None</td>
			<td>Does the row have actions?</td>
		</tr>
		<tr>
			<td><strong>data</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>The data applied to the table</td>
		</tr>
		<tr>
			<td><strong>rows</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>An array of table rows</td>
		</tr>
		<tr>
			<td><strong>inner_td_class</strong></td>
			<td>empty string ''</td>
			<td>None</td>
			<td>The css class to be used for the span inside the td</td>
		</tr>
		<tr>
			<td><strong>no_data_str</strong></td>
			<td>No data to display.</td>
			<td>None</td>
			<td>The default string to display when no data exists</td>
		</tr>
		<tr>
			<td><strong>lang_prefix</strong></td>
			<td>table_header_</td>
			<td>None</td>
			<td>The language prefix to associate with table headers</td>
		</tr>
		<tr>
			<td><strong>field_styles</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Styles to apply to the data columns. Index is the column and the value is the style</td>
		</tr>
	</tbody>
</table>

<br />

<h1>Function Reference</h1>

<h2>$this->data_table->assign_data(<var>data</var>, <var>[headers]</var>, <var>[attrs]</var>)</h2>
<p>Assigns the data to be used for the table. Accepts a multi-deminsional array like so:</p>

<pre class="brush: php">
$data[] = array(
	'name' => 'Darth Vader',
	'weapon' => 'light saber',
	'darkside' => TRUE,
	'active' => 'yes'
);
$data[] = array(
	'name' => 'Luke Skywalker',
	'weapon' => 'light saber',
	'darkside' => FALSE,
	'active' => 'yes'
);

$data[] = array(
	'name' => 'Han Solo',
	'weapon' => 'blaster',
	'darkside' => FALSE,
	'active' => 'yes',
	'__field__' => array('name' => array('class' => 'highlight'))
);

$this->data_table->assign_data($data);
</pre>
<p class="important">Note the <dfn>'__field__'</dfn> field (that's 2 underscores on each side). 
This is a special field that allows you to add attributes to the parent &lt;td&gt; tag. In the above example, the CSS class of "highlight" 
is applied to the Han Solo named column.</p>



<h2>$this->data_table->render()</h2>
<p>Renders the table. Example:</p>

<pre class="brush: php">
$this->data_table->render();
</pre>

<h2>$this->data_table->render_headers()</h2>
<p>Renders just the headers. Example:</p>

<pre class="brush: php">
$this->data_table->render_headers();
</pre>

<h2>$this->data_table->render_rows()</h2>
<p>Renders just the table rows. Example:</p>

<pre class="brush: php">
$this->data_table->render();
</pre>



<h2>$this->data_table->clear()</h2>
<p>Clears the data assigned to the table. Example:</p>

<pre class="brush: php">
$this->data_table->clear();
</pre>


<h2>$this->data_table->set_sorting(<var>'col'</var>, <var>'ordering'</var>)</h2>
<p>Sets the sorting information of which column and which direction. Example:</p>

<pre class="brush: php">
$this->data_table->set_sorting('name', 'asc');
</pre>


<h2>$this->data_table->get_sorted_ordering()</h2>
<p>Returns the sorting ordering of either <dfn>asc</dfn> or <dfn>desc</dfn>. Example:</p>

<pre class="brush: php">
echo $this->data_table->get_sorted_ordering(); // echos asc or desc
</pre>


<h2>$this->data_table->get_sorted_field()</h2>
<p>Returns the field that the data is currently being sorted by. Example:</p>

<pre class="brush: php">
echo $this->data_table->get_sorted_field(); // name of a field
</pre>

<h2>$this->data_table->add_action(<var>'field'</var>, <var>'action'</var>, <var>['type']</var>)</h2>
<p>Adds actions to each row in the table. The <dfn>type</dfn> attribute can be either <dfn>url</dfn> or <dfn>func</dfn>.
Type <dfn>url</dfn> is a string value that can have placeholder with <dfn>{}</dfn> surrounding column values that need to be substituted in.
Type <dfn>func</dfn> is a string value of a function that will have the rows field values passed to it as the only parameter. 

The default is <dfn>url</dfn>. Example:</p>

<pre class="brush: php">
// with just a url
$action = array('EDIT' => 'example/edit/{id}');
$this->data_table->add_action($field, $action);
</pre>

<pre class="brush: php">
// with a function
$delete_func = '
$CI =& get_instance();
$link = "";
if ($CI->auth->has_permission("delete"))
{
	$url = site_url("example/delete/".$cols["id"]);
	$link = "<a href=\"".$url."\">DELETE</a>";
}
return $link;';

$delete_func = create_function('$cols', $delete_func);
$this->data_table->add_action($field, $action);
</pre>


<h2>$this->data_table->add_field_formatter(<var>'field'</var>, <var>'func'</var>)</h2>
<p>Adds a formatter to a field. The formatter should be a function that accepts an array of field values. Example:</p>

<pre class="brush: php">
function is_darkside($fields)
{
	return ($fields['darkside']) ? 'yes' : 'no';
}

$this->data_table->add_field_formatter('darkside', 'is_darkside'); // will echo out either yes or no for the field value
</pre>


<h2>$this->data_table->set_table_attributes(<var>attrs</var>)</h2>
<p>Sets the table attributes. Example:</p>

<pre class="brush: php">
$this->data_table->set_table_attributes($attrs);
</pre>
<p class="important"><kbd>$attrs</kbd> must be a key value array.</p>


<h2>$this->data_table->set_body_attributes(<var>attrs</var>)</h2>
<p>Sets the tbody attributes. Example:</p>

<pre class="brush: php">
$this->data_table->set_body_attributes($attrs);
</pre>
<p class="important"><kbd>$attrs</kbd> must be a key value array.</p>


<h2>$this->data_table->add_header(<var>'key'</var>, <var>'name'</var>, <var>['sorting_param']</var>, <var>[attrs]</var>, <var>[index]</var>)</h2>
<p>Adds a single header to the table. The <dfn>$sorting_param</dfn> is the column to sort on if it is different then the $key. Example:</p>

<pre class="brush: php">
$this->data_table->add_header('name', 'Name', 'name', '', 1);
</pre>

<h2>$this->data_table->add_headers(<var>'headers'</var>, <var>[sorting_params]</var>, <var>[attrs]</var>)</h2>
<p>Adds multiple headers to the table. Example:</p>

<pre class="brush: php">
$headers = array(
	'name' => 'Name',
	'weapon' => 'Weapon',
	'active' => 'Active'
);
$sorting_cols = array('name', 'weapon', 'active');

$this->data_table->add_headers($headers, $sorting_cols, '');
</pre>


<h2>$this->data_table->add_row(<var>'columns'</var>, <var>[attrs]</var>, <var>[index]</var>, <var>[action]</var>)</h2>
<p>Adds a single row to the table. Example:</p>

<pre class="brush: php">
$row = array(
	'id' => 1,
	'name' => 'Luke Skywalker',
	'weapon' => 'light saber',
	'active' => 'yes'
);
$action = array('EDIT' => 'example/edit/{id}');

$this->data_table->add_row($row, '', 1, $action);
</pre>

<h2>$this->data_table->add_rows(<var>'data'</var>, <var>[attrs]</var>, <var>['action']</var>)</h2>
<p>Adds multiple rows to the table. Example:</p>

<pre class="brush: php">
$rows[] = array(
	'id' => 1,
	'name' => 'Luke Skywalker',
	'weapon' => 'light saber',
	'active' => 'yes'
);

$rows[] = array(
	'id' => 2,
	'name' => 'Han Solo',
	'weapon' => 'blaster',
	'active' => 'yes'
);
$action = array('EDIT' => 'example/edit/{id}');

$this->data_table->add_rows($rows, '', $action);
</pre>

