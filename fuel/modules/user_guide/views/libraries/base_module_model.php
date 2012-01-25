<h1>Base_module_model</h1>
<p>The <strong>Base_module_model</strong> is the base abstract class that should be extended when creating modules. The class should be required at the top of your module like so:</p>

<pre class="brush:php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class My_super_model extends Base_module_model {
...
</pre>

<h2>Configuring Base_module_model Information</h2>
<p>There are several public properties you can use to configure the Base_module_model Class:</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>filters</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Field names of where to search for items</td>
		</tr>
		<tr>
			<td><strong>filter_value</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The values of the filters. Set automatically when filters are selected</td>
		</tr>
		<tr>
			<td><strong>filter_join</strong></td>
			<td>or</td>
			<td>and, or</td>
			<td>How to combine filters in the list_items method query</td>
		</tr>
		<tr>
			<td><strong>parsed_fields</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Fields to automatically do template parsing</td>
		</tr>
		<tr>
			<td><strong>upload_data</strong></td>
			<td>array()</td>
			<td>None</td>
			<td>Data about all uploaded files. Set after uploads</td>
		</tr>
	</tbody>
</table>


<br /><br />

<h1>Base_module_model Function Reference</h1>
<p>The <dfn>Base_module_model</dfn> is a table class and returns <dfn>Base_module_record</dfn> objects (see reference below).</p>

<h2>$this->module->get_table(<var>table</var>)</h2>
<p>Gets the table name based on the configuration.</p>


<h2>$this->module->add_filter(<var>filter</var>, <var>[key]</var>)</h2>
<p>Adds a filter for searching.</p>


<h2>$this->module->add_filters(<var>filters</var>)</h2>
<p>Adds multiple filters for searching.</p>


<h2>$this->module->list_items(<var>[limit]</var>, <var>[offset]</var>, <var>[col]</var>, <var>[order]</var>)</h2>
<p>Returns an associative array of the table records and is used by the <a href="<?=user_guide_url('libraries/data_table')?>">Data_table</a> class to display the module's items in the list view. 
By default it will list all columns. To filter out columns, simply override this method and use CodeIgniter's Active Record
select statement like so:</p>

<pre class="brush:php">
...
function list_items($limit=NULL, $offset=NULL, $col = "name", $order = "asc")
{
	$this->db->select('id, name', published');
	parent::list_items($limit, $offset, $col, $order);
}
...
</pre>

<p class="important">You should always include the primary key id in this select so the <a href="<?=user_guide_url('libraries/data_table')?>">Data_table</a> object can use it in the table row's edit and delete actions.</p>

<h2>$this->module->list_items_total()</h2>
<p>Lists the total number of module items. Used in calculating the number of pages when paginating.</p>

<h2>$this->module->archive(<var>ref_id</var>, <var>data</var>)</h2>
<p>Saves data to the archive.</p>

<h2>$this->module->get_last_archive(<var>ref_id</var>, <var>[all_data]</var>)</h2>
<p>Retrieves the last archived value.</p>

<h2>$this->module->get_archive(<var>ref_id</var>, <var>[version]</var>, <var>[all_data]</var>)</h2>
<p>Retrieves an archived value.</p>

<h2>$this->module->restore(<var>ref_id</var>, <var>[version]</var>)</h2>
<p>Restores module item from an archived value.</p>

<h2>$this->module->get_others(<var>'display_field'</var>, <var>id</var>, <var>[val_field]</var>)</h2>
<p>Get other listed module items excluding the currently displayed.</p>

<h2>$this->module->form_fields(<var>[values]</var>, <var>[related]</var>)</h2>
<p>Overwrites MY_Model's form_field() method and adds some additional features including automatically creating 
image upload fields for table column names that end with <dfn>image</dfn> or <dfn>img</dfn>.</p>

<br />

<h1>Base_module_record Function Reference</h1>
<p>The <dfn>Base_module_record</dfn> class is what is returned by module table classes and extends the <a href="<?=user_guide_url('libraries/my_model/data_record_class_functions')?>">Data_record class</a>.</p>

<h2>set_parsed_fields()</h2>
<p>Sets the fields to parse.</p>

<h2>get_parsed_fields()</h2>
<p>Returns the fields to parse.</p>

<h2>after_get()</h2>
<p>A hook method that gets called after a value is returned by the class. This method will parse any fields that have been set to be parsed.</p>