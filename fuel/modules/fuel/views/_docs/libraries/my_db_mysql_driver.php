<h1>MY_DB_mysql_driver Class</h1>
<p>The <dfn>MY_DB_mysql_driver Class</dfn> extends <dfn>CI_DB_mysql_driver</dfn> driver class.
It adds several methods listed below:
</p>

<h2>$this->db->debug_query(<var>[hidden]</var>, <var>[exit]</var>)</h2>
<p>Echos out the last query ran to the screen.
The <dfn>hidden</dfn> parameter will wrap the echoed output into an HTML comment.
The <dfn>exit</dfn> parameter will exit the script upon debugging.
</p>

<pre class="brush:php">
$this->examples_model->db->debug_query();
</pre>


<h2>$this->db->safe_select(<var>'table'</var>, <var>[fields]</var>, <var>[prefix]</var>)</h2>
<p>Appends the table name to fields in a select that don't have it to prevent ambiguity.
The <dfn>fields</dfn> parameter is an array of fields to create the select statement. By default it will do all the table fields.
The <dfn>prefix</dfn> parameter will prefix the selected columns with string.
</p>

<pre class="brush:php">
$select = $this->examples_model->db->safe_select('example');
$this->db->select($select);
</pre>


<h2>$this->db->field_info(<var>'table'</var>, <var>fields</var>)</h2>
<p>Gets an array of information about a particular table's field.
The <dfn>fields</dfn> parameter is the field name to get information from.
</p>

<pre class="brush:php">
$field_info = $this->examples_model->db->field_info('example', 'my_field');
</pre>


<h2>$this->db->table_info(<var>'table'</var>, <var>[set_field_key]</var>)</h2>
<p>Gets an array of information about a particular table's field.
The <dfn>set_field_key</dfn> parameter sets the array returned key to the column name. The default is <dfn>TRUE</dfn>
</p>

<pre class="brush:php">
$table_info = $this->examples_model->db->table_info('example');
foreach($table_info as $field => $info)
{
	echo $info['name'].'<br />';
	echo $info['type'].'<br />';
}
</pre>


<h2>$this->db->insert_ignore(<var>'table'</var>, <var>values</var>, <var>[primary_key]</var>)</h2>
<p>Save's information to the database using INSERT IGNORE syntax
The <dfn>values</dfn> parameter are the values to save to the database.
The <dfn>primary_key</dfn> parameter is column to be used as the primary key. The default is <dfn>id</dfn>.
</p>

<pre class="brush:php">
$field_info = $this->examples_model->db->field_info('example', 'my_field');
</pre>

<h2>$this->db->get_query_string([<var>$clear</var>])</h2>
<p>Allows you to get the compiled active record string without running the query</dfn>. The <dfn>clear</dfn> parameter will by default clear the
active record for subsequent queries. This method is an alias to active records <dfn>_compile_select()</dfn> method.</p>

<pre class="brush:php">
$this->db->select('my_field');
$this->db->from('my_table');
$this->db->where(array('active' => 'yes'));
$sql = $this->examples_model->db->get_query_string();
echo $sql; // SELECT my_field FROM my_table WHERE active = 'yes'
</pre>

<h2>$this->db->clear_query_string()</h2>
<p>Clears the compiled query string</dfn>. This method is an alias to active records <dfn>_reset_select()</dfn> method.</p>

<pre class="brush:php">
$this->examples_model->db->select('my_field');
$this->examples_model->db->from('my_table');
$this->examples_model->db->where(array('active' => 'yes'));
$sql = $this->examples_model->db->get_query_string();
echo $sql; // SELECT my_field FROM my_table WHERE active = 'yes'
$this->examples_model->db->clear_query_string();
$sql = $this->examples_model->db->get_query_string();
echo $sql; // ""
</pre>