<h1>Caching</h1>
<p>CodeIgniter, is well-known for it's fast performance as a framework. However, there are some instances where it makes sense to cache a page or file to prevent 
excessive database queries and HTTP requests for javascript and CSS. To help with this, FUEL provides several method to cache resources and thus speed up page speed.</p>

<p>Programmatically, you can access the FUEL's cache methods through the instantiated <a href="<?=user_guide_url('libraries/fuel_layouts')?>">Fuel_cache</a> object like so:</p>
<pre class="brush:php">
$this->fuel->cache->clear_all();
$this->fuel->cache->save('my_cache_id', $data);
</pre>

<h2>Page Caching</h2>
<p></p>

<h2>Block Caching</h2>

<h2>Asset Optimization Caching</h2>