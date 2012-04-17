<h1>FUEL Constants</h1>
<p>In addition to the <a href="http://codeigniter.com/user_guide/general/reserved_names.html" target="_blank">CodeIgniter constants</a>, FUEL CMS provides the following:</p>

<ul>
	<li><strong>FUEL_VERSION</strong>: The current version number of FUEL</li>
	<li><strong>FUEL_FOLDER</strong>: The FUEL module folder name. The value is <dfn>fuel</dfn></li>
	<li><strong>FUEL_PATH</strong>: The full server path to the FUEL module folder</li>
	<li><strong>FUEL_ROUTE</strong>: The route to use to access the FUEL CMS. The default value is <dfn>fuel/</dfn></li>
	<li><strong>USE_FUEL_ROUTES</strong>: A boolean value automatically generated based on the URI location which determines whether to includes the FUEL routes in a request or not</li>
	<li><strong>FUEL_ADMIN</strong>: This is only set in a controller that extends the <a href="<?=user_guide_url('libraries/fuel_base_controller')?>">Fuel_base_controller</a> and can be used to assess if the current page is an admin page or not.</li>

	<li><strong>MODULES_FOLDER</strong>: The folder path relative to the <span class="file">fuel/application</span> directory. The value is <span class="file">'../modules'</span></li>
	<li><strong>MODULES_PATH</strong>: The full server path the module folder</li>
	<li><strong>MODULES_FROM_APPCONTROLLERS</strong>: The modules folder relative to the application controllers directory</li>
	<li><strong>MODULES_WEB_PATH</strong>: The web path to the modules folder</li>

	<li><strong>WEB_FOLDER</strong>: The name of the web folder of the FUEL installation</li>
	<li><strong>WEB_ROOT</strong>: The full server path to the web directory</li>
	<li><strong>WEB_PATH</strong>: The web path to the FUEL installation on the server (if fuel is installed in a subfolder)</li>
	<li><strong>BASE_URL</strong>: The web path in which all site urls are based. Is also used as the <dfn>$config['base_url']</dfn> value in the <span class="file">fuel/application/config/config.php</span> file</li>
</ul>

<h2>Constants for Advanced Modules</h2>
<p>When creating an advanced module, you should also create a constants file at <span class="file">/fuel/modules/{module}/config/{module}_constnats.php</span> 
with at least the following constants:</p>
<pre class="brush:php">
define('{MY_MODULE}_VERSION', '1.0');
define('{MY_MODULE}_FOLDER', 'my_module');
define('{MY_MODULE}_PATH', MODULES_PATH.{MY_MODULE}_FOLDER.'/');
</pre>