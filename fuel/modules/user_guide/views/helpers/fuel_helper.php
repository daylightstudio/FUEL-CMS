<h1>FUEL helper</h1>

<p>Contains FUEL specific functions. This helper actually exists in the <dfn>fuel</dfn> module and
should be loaded the following way:
</p>

<pre class="brush: php">
$this->load->module_helper(FUEL_FOLDER, 'fuel');
</pre>

<p>The following functions are available:</p>

<h2>fuel_block(<var>params</var>)</h2>
<p>Allows you to load a view and pass data to it.
The <dfn>params</dfn> parameter can either be string value (in which case it will assume it is the name of the block view file) or an associative array that can have the following values:
</p>
<ul>
	<li><strong>view</strong> - a view file located in the <dfn>views/_blocks</dfn></li>
	<li><strong>view_string</strong> - a string value to be used for the view</li>
	<li><strong>model</strong> - the model to load and be available for the view</li>
	<li><strong>find</strong> - the find method on the model to use (e.g. 'one', 'all' or 'key')</li>
	<li><strong>select</strong> - the select condition to filter the results of the find query</li>
	<li><strong>where</strong> - the where condition on the model to be used in the find query</li>
	<li><strong>order</strong> - order the data results returned from the model and sort them </li>
	<li><strong>limit</strong> - limit the number of data results returned by the model</li>
	<li><strong>offset</strong> - offset the data results returned by the model</li>
	<li><strong>return_method</strong> - the return method to use which can be an object or an array</li>
	<li><strong>assoc_key</strong> - the field to be used as an associative key for the data results</li>
	<li><strong>data</strong> - data to be passed to the view if a model isn't provided. This information can be accessed in the block from the variable <dfn>$data</dfn></li>
	<li><strong>editable</strong> - insert in inline editing</li>
	<li><strong>parse</strong> - parse the contents of the page. Default is set to 'auto' which will NOT try and parse if your <dfn>fuel_mode</dfn> value in the fuel config file is set to "views".</li>
	<li><strong>vars</strong> - additional variables to pass to the block</li>
	<li><strong>cache</strong> - will cache the block</li>
</ul>


<h2>fuel_model(<var>module</var>, <var>[params]</var>)</h2>
<p>Loads a module's model and creates a variable that you can use to merge data into your view.
The <dfn>params</dfn> parameter is an associative array that can have the following values:
</p>
<ul>
	<li><strong>find</strong> - the find method to use on the module model</li>
	<li><strong>select</strong> - the select condition to filter the results of the find query</li>
	<li><strong>where</strong> - the where condition to be used in the find query</li>
	<li><strong>order</strong> - order the data results and sort them </li>
	<li><strong>limit</strong> - limit the number of data results returned</li>
	<li><strong>offset</strong> - offset the data results</li>
	<li><strong>return_method</strong> - the return method to use which can be an object or an array</li>
	<li><strong>assoc_key</strong> - the field to be used as an associative key for the data results</li>
	<li><strong>var</strong> - the variable name to assign the data returned from the module model query</li>
	<li><strong>module</strong> - specifies the module folder name to find the model</li>
</ul>

<h2>fuel_nav(<var>params</var>)</h2>
<p>Creates a menu structure. 
The <dfn>params</dfn> parameter is an array of options to be used with the <a href="<?=user_guide_url('libraries/menu')?>">Menu class</a>.
If FUEL's configuration mode is set to either <dfn>auto</dfn> or <dfn>cms</dfn>,
then it will first look for data from the FUEL navigation module. Otherwise it will by default look for the file <dfn>views/_variables/nav.php</dfn>
(you can change the name of the file it looks for in the <dfn>file</dfn> parameter passed). That file should contain an array of menu information (see <a href="<?=user_guide_url('libraries/menu')?>">Menu class</a> for more information on the required
data structure). The parameter values are very similar to the <a href="<?=user_guide_url('libraries/menu')?>">Menu class</a>, with a few additions shown below:
</p>
<ul>
	<li><strong>file</strong> - the name of the file containing the navigation information</li>
	<li><strong>var</strong> - the variable name in the file to use</li>
	<li><strong>parent</strong> - the parent id you would like to start rendering from</li>
	<li><strong>root</strong> - the equivalent to the root_value attribute in the Menu class. It states what the root value of the menu structure should be. Normally you don't need to worry about this.</li>
	<li><strong>group_id</strong> - the group ID in the database to use. The default is <dfn>1</dfn>. Only applies to navigation items saved in the admin.</li>
	<li><strong>exclude</strong> - nav items to exclude from the menu</li>
	<li><strong>return_normalized</strong> - returns the raw normalized array that gets used to generate the menu</li>
</ul>

<p class="important">For more information see the <a href="<?=user_guide_url('libraries/menu')?>">Menu class</a>.</p>

<h2>fuel_set_var(<var>key</var>, <var>val</var>)</h2>
<p>Sets a variable for all views to use no matter what view it is declared in.</p>


<h2>fuel_var(<var>key</var>, <var>[default]</var>, <var>[edit_module]</var>, <var>[evaluate]</var>)</h2>
<p>Returns a variable and allows for a default value. Also creates inline editing marker.
The <dfn>default</dfn> parameter will be used if the variable does not exist.
The <dfn>edit_module</dfn> parameter specifies the module to include for inline editing.
The <dfn>evaluate</dfn> parameter specifies whether to evaluate any php in the variables.
</p>
<p class="important">You should not use this function inside of another function because you may get unexepected results. This is
because it returns inline editing markers that later get parsed out by FUEL. For example, you <kbd>shouldn't</kbd> do:</p>

<pre class="brush:php">
// NO
&lt;a href="&lt;?=site_url(fuel_var('my_url'))?&gt;"&gt;my link&lt;/a&gt;
</pre>

<p>Instead you should use <dfn>fuel_edit()</dfn> like so:</p>

<pre class="brush:php">
// YES
&lt;?=fuel_edit('my_url', 'Edit Link')?&gt; &lt;a href="&lt;?=site_url($my_url)?&gt;"&gt;my link&lt;/a&gt;
</pre>

<h2>fuel_edit(<var>id</var>, <var>[label]</var>, <var>[module]</var>, <var>[xOffset]</var>, <var>[yOffset]</var>)</h2>
<p>Sets a variable marker (pencil icon) in a page which can be used for inline editing.
The <dfn>id</dfn> parameter is the unique id that will be used to query the module. You can also pass an id value
and a field like so <dfn>id|field</dfn>. This will display only a certain field instead of the entire module form.
The <dfn>label</dfn> parameter specifies the label to display next to the pencil icon.
The <dfn>xOffset</dfn> and <dfn>yOffset</dfn> are pixel values to offset the pencil icon.</p>

<h2>fuel_var_append(<var>key</var>, <var>value</var>)</h2>
<p>Appends a value to an array variable. This function makes it convenient if you are in a view file and want to say add a javascript
file to your <dfn>$js</dfn> array variable for example. The <dfn>$js</dfn> variable can then be passed to the asset helper's <dfn>js($js)</dfn> function with the appended values.
The <dfn>key</dfn> value is the name of the array variable you want to append to.
The <dfn>value</dfn> can be either an array or a string. An array will merge the values.
</p>

<pre class="brush:php">
// EXAMPLE HEADER FILE
...
&lt;meta name=&quot;keywords&quot; content=&quot;&lt;?php echo fuel_var(&#x27;meta_keywords&#x27;)?&gt;&quot; /&gt;
&lt;meta name=&quot;description&quot; content=&quot;&lt;?php echo fuel_var(&#x27;meta_description&#x27;)?&gt;&quot; /&gt;

&lt;?php echo css(&#x27;main&#x27;); ?&gt;
&lt;?php echo css($css); ?&gt;

&lt;?php echo js(&#x27;jquery, main&#x27;); ?&gt;
&lt;?php echo js($js); ?&gt;
...


// Then in your view file
...
&lt;php
fuel_var_append('css', 'my_css_file.css');
fuel_var_append('js', 'my_js_file.js');
?&gt;
<h1>About our company</h1>
...
</pre>


<h2>fuel_cache_id(<var>[location]</var>)</h2>
<p>Creates the cache ID for the fuel page based on the URI. 
If no <dfn>location</dfn> value is passed, it will default to the current <a href="<?=user_guide_url('my_url_helper')?>">uri_path</a>.
</p>


<h2>fuel_url(<var>[uri]</var>)</h2>
<p>Creates the admin URL for FUEL (e.g. http://localhost/MY_PROJECT/fuel/admin).</p>


<h2>fuel_uri(<var>[uri]</var>)</h2>
<p>Returns the FUEL admin URI path.</p>


<h2>fuel_uri_segment(<var>[seg_index]</var>, <var>[rerouted]</var>)</h2>
<p>Returns the uri segment based on the FUEL admin path.</p>


<h2>fuel_uri_index(<var>[seg_index]</var>)</h2>
<p>Returns the uri index number based on the FUEL admin path.</p>


<h2>fuel_uri_string(<var>[from]</var>, <var>[to]</var>, <var>[rerouted]</var>)</h2>
<p>Returns the uri string based on the FUEL admin path.</p>


<h2>is_fuelified()</h2>
<p>Check to see if you are logged in and can use inline editing. Is an alias to the Fuel_auth::is_fuelified().</p>