<?=generate_docs('Fuel_base_library')?>

<h1>Fuel Base Library Class</h1>
<p>This class provides various methods for saving, retrieving and deleting cached files from FUEL.</p>

<h2>Initializing the Class</h2>

<p>The Fuel_cache class is a child of the <a href="<?=user_guide_url('fuel/library/fuel_base_library')?>">Fuel_base_library</a> automatically loaded by the Fuel class and can be accessed as follows from your controller:</p>

<pre class="brush: php">$this->fuel->cache->{method}();</pre>

<p>The Fuel_cache class uses the more generic <a href="<?=user_guide_url('libraries/cache')?>">Cache</a> library.</p>

<h2>Configuring Fuel_cache Information</h2>
<p>The <dfn>Fuel_cache</dfn> class has the following configuration parameter:</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>ignore</strong></td>
			<td>#^(\..+)|(index\.html)#</td>
			<td>None</td>
			<td>The regular expression to use to ignore files from deletion from the cach</td>
		</tr>
	</tbody>
</table>

<br />

<h1>Function Reference</h1>

<h2 id="cache_id">$this->fuel->cache->cache_id(<var>[location]</var>)</h2>
<p>If no location value is provided, then the ID will be based on the current URI segments.</p>
<pre>
* @access	public
* @param	string	location used in creating the ID (optional)
* @return	string
</pre>
<h3>Example:</h3>
<pre class="brush: php">
$cache_id = $this->fuel->cache->cache_id(); // create a cache id... this will be based on the current URI location if no parameters are passed
</pre>


