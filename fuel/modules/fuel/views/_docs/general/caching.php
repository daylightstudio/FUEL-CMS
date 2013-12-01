<h1>Caching</h1>
<p>CodeIgniter, is well-known for it's fast performance as a framework. However, there are some instances where you may want to cache a page or file to prevent 
excessive database queries and HTTP requests for javascript and CSS. To help with this, FUEL provides several method to cache resources and thus speed up page speed.</p>

<p>Programmatically, you can access the FUEL's cache methods through the instantiated <a href="<?=user_guide_url('libraries/fuel_cache')?>">Fuel_cache</a> object like so:</p>
<pre class="brush:php">
$this->fuel->cache->clear_all();
$this->fuel->cache->save('my_cache_id', $data);
</pre>

<h2>Page Caching</h2>
<p>FUEL provides several options for caching your pages. The default setting, which can be altered in your <span class="file">fuel/application/config/MY_fuel.php</span> 
file by changing the <a href="<?=user_guide_url('installation/configuring')?>">$config['use_page_cache']</a> parameter, 
is to cache all the pages created in the CMS. In some cases, you may not want to cache the page (e.g. you have a section that you want to be random on each page),
and in those cases you can set the page's CMS <dfn>cache</dfn> setting to no.</p>

<h2>Block Caching</h2>
<p>Similarly, <a href="<?=user_guide_url('libraries/fuel_blocks')?>">blocks</a> can also be cached as shown in the example below:</p>
<pre class="brush:php">
echo fuel_block(array('view' => 'my_block', 'cache' => TRUE));
</pre>

<h2>Template Compiling</h2>
<p>Both pages and blocks can take advantage of the <a href="<?=user_guide_url('general/template-parsing')?>">template parsing library</a> to allow users to safely
use limited PHP funcationality. These template files get compiled and are stored in the <span class="file">fuel/application/cache/dwoo/compiled</span> folder.</p>

<h2>Asset Optimization Caching</h2>
<p>The <a href="<?=user_guide_url('libraries/asset')?>">Asset class</a> provides several options to speed up your CSS and javascript files of your pages by changing the <dfn>assets_output</dfn>
property which has the following options:</p>

<ul>
	<li><strong>FALSE</strong> - no optimization</li>
	<li><strong>TRUE</strong> - will combine files, strip whitespace, and gzip</li>
	<li><strong>inline</strong> - will render the files inline</li>
	<li><strong>gzip</strong> - will combine files (if multiple) and gzip without stripping whitespace</li>
	<li><strong>whitespace</strong> - will combine files (if multiple) and strip out whitespace without gzipping</li>
	<li><strong>combine</strong> - will combine files (if multiple) but will not strip out whitespace or gzip</li>
</ul>

<p class="important">A writable <span class="file">assets/cache</span> folder must exist for asset caching to work. Also, cached asset files must be deleted either manually or by the CMS's <dfn>Clear Cache</dfn> utility.</p>

<h2>Cache Clearing</h2>
<p>Sometimes you may make changes and not see them reflected on the site. If so, you may need to clear your site's cache.
To do that, click on the <strong>Page Cache</strong> menu item under manage and then click the <strong>Yes, clear cache</strong> button to clear your sites cache files.</p>

<p>Alternatively, you can use the command line to clear the cache:</p>
<pre class="brush:php">
php index.php fuel/manage/clear_cache
</pre>

<h2>Web Hooks</h2>
<p>The <a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration's <dfn>webhook_remote_ip</dfn> parameter</a> gives you the ability to set one or more IP addresses
that can be used to remotely call the fuel/manage/clear_cache. For example, say you use <a href="http://beanstalkapp.com" target="_blank">Beanstalk</a> to manage your GIT repositories and you would like to automatically clear the cache upon 
commit. You can set this configuration value to the IP address ranges <a href="http://support.beanstalkapp.com/customer/portal/articles/68153-ip-addresses-for-access-to-beanstalk" target="_blank">provided here</a>.
Then, in Beanstalk you can set up your <a href="http://support.beanstalkapp.com/customer/portal/articles/68163-web-hooks-for-deployments" target="_blank">web deployment post hook</a>. In this case you would set it in Beanstalk to be the full URL path:</p>
<pre class="brush:php">
http://www.mysite.com/fuel/manage/clear_cache
</pre>