<h1>MY_DB_mysql_result Class</h1>
<p>The <dfn>MY_DB_mysql_result Class</dfn> extends <dfn>CI_DB_mysql_result</dfn> result class.
It adds several methods listed below:
</p>

<h2>result_assoc_array(<var>'field_name'</var>)</h2>
<p>Returns an associative array with an array for rows.</p>

<pre class="brush:php">
$query = $this->examples_model->db->query();
$rows = $query->result_assoc_array('id');
</pre>


<h2>result_assoc(<var>'field_name'</var>)</h2>
<p>Returns an associative array with objects for rows
</p>

<pre class="brush:php">
$query = $this->examples_model->db->query();
$rows = $query->result_assoc('id');
</pre>