<h1>Data Set Class Function Reference</h1>
<p>This class is instantiated by the Table Class (MY_Model) when a result set is needed.
The Data_set class has a few methods to retrieve information about the data set.
</p>

<h2>$this->examples_model->result()</h2>
<p>If a single result set is needed (e.g. $this->examples_model->find_one(), or $this->examples_model->get(FALSE)), then the result method will return a single object or 
array like so:
</p>

<pre class="brush: php">
$single_result_set = $this->examples_model->get(FALSE); 
$example = $single_result_set->result(); 
echo $example->name; 
</pre>

<p>If multiple result sets are needed, then the result method will return multiple result objects/arrays like so: </p>

<pre class="brush: php">
$multiple_result_set = $this->examples_model->get(TRUE); 
foreach($multiple_result_set as $example) 
{ 
    echo $example->name; 
} 
</pre>


<h2>$this->examples_model->num_records()</h2>
<p>This method will return the number of records in the result set.</p>
<pre class="brush: php">
$multiple_result_set = $this->examples_model->get(TRUE); 
echo $multiple_result_set->num_records(); // 
</pre>

<h2>$this->examples_model->debug()</h2>
<p>Prints debugging information about the result set to the screen like so</p>
<pre class="brush: php">
$multiple_result_set = $this->examples_model->get(TRUE); 
$multiple_result_set->debug(); // 
</pre>