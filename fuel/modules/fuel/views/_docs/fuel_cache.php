<h1>Fuel Cache Class</h1>
<p>This class provides various methods for saving, retrieving and deleting cached files from FUEL.</p>

<h2>Initializing the Class</h2>

<p>The Fuel_cache class is a child of the <a href="<?=user_guide_url('fuel/library/fuel_base_library')?>">Fuel_base_library</a> automatically loaded by the Fuel class and can be accessed as follows from your controller:</p>

<pre class="brush: php">$this->fuel->cache->{method};</pre>

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


<h2 id="get">$this->fuel->cache->get(<var>cache_id</var>, <var>cache_group</var>, <var>[skip_checking]</var>)</h2>
<p>Gets and returns an item from the cache.</p>
<pre>
* @param	string	Cache ID
* @param	string	Cache group ID (optinal)
* @param	boolean	Skip checking if it is in the cache or not (optional)
* @return	object	The object or NULL if not available
</pre>

<h3>Example:</h3>
<pre class="brush: php">
$cache_id = $this->fuel->cache->cache_id();
$file = $this->fuel->cache->get($cache_id, 'pages', FALSE);
</pre>


<h2 id="save">$this->fuel->cache->save(<var>cache_id</var>, <var>data</var>, <var>[group]</var>, <var>[ttl]</var>)</h2>
<p>Saves an item to the cache.</p>
<pre>
* @param	string	Cache Id
* @param	mixed	Data to save to the cache
* @param	string	Cache group Id (optional)
* @param	int		Time to live for cache (optional)
* @return	void
</pre>

<h3>Example:</h3>
<pre class="brush: php">
$cache_id = $this->fuel->cache->cache_id();
$data = 'These are not the droids you are looking for.';

// sets the cached item with a TTL of one hour
$file = $this->fuel->cache->save($cache_id, $data, NULL, 3600);
</pre>


<h2 id="is_cached">$this->fuel->cache->is_cached(<var>cache_id</var>, <var>[group]</var>)</h2>
<p>Checks if the file is cached based on the cache_id passed.</p>
<pre>
* @param	string	Cache Id
* @param	string	Cache group Id (optinal)
* @return	boolean
</pre>

<h3>Example:</h3>
<pre class="brush: php">
$cache_id = $this->fuel->cache->cache_id();
if ($this->fuel->cache->is_cached($cache_id)){
	echo 'cached';
} else {
	echo 'not cached';
}
</pre>


<h2 id="clear">$this->fuel->cache->clear(<var>[types]</var>)</h2>
<p>Clears the various types of caches.
Value passed can be either a string or an array. 
If a string,the value must "compiled", "pages" or "assets".
If an array,the array must contain one or more of the values (e.g. array("compiled", "pages", "assets"))
If no parameters are passed, then all caches are cleared
</p>

<pre>
* @param	mixed	Value can be either a string of one value or an array of multiple values. Valid values are compiled, pages and assets. (optinal)
* @return	void
</pre>

<h3>Example:</h3>
<pre class="brush: php">
// as an array
$this->fuel->cache->clear(array('compiled', 'pages'));

// as string
$this->fuel->cache->clear('assets');

</pre>