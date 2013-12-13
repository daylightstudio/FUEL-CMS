<h1>The FUEL Object Structure</h1>
<p>FUEL CMS version 1.0 provides a powerful object oriented structure which allows you to easily access FUEL functionality within your own code.
There is a <a href="<?=user_guide_url('libraries/fuel')?>">fuel</a> object set on the main CI super object (controller object accessed using the get_instance() function).
This object allows access to your FUEL configuration as well as attaches other useful FUEL objects as shown below:
</p>

<ul>
	<li><strong>$this->fuel</strong> (<a href="<?=user_guide_url('libraries/fuel')?>">Fuel</a>)
	
		<ul>
			<li><strong>$this->fuel->admin</strong> (<a href="<?=user_guide_url('libraries/fuel_admin')?>">Fuel_admin</a>)</li>
			<li><strong>$this->fuel->assets</strong> (<a href="<?=user_guide_url('libraries/fuel_assets')?>">Fuel_assets</a>)</li>
			<li><strong>$this->fuel->auth</strong> (<a href="<?=user_guide_url('libraries/fuel_auth')?>">Fuel_auth</a>)</li>
			<li><strong>$this->fuel->blocks</strong> (<a href="<?=user_guide_url('libraries/fuel_blocks')?>">Fuel_blocks</a>)</li>
			<li><strong>$this->fuel->cache</strong> (<a href="<?=user_guide_url('libraries/fuel_cache')?>">Fuel_cache</a>)</li>
			<li><strong>$this->fuel->categories</strong> (<a href="<?=user_guide_url('libraries/Fuel_categories')?>">Fuel_categories</a>)</li>
			<li><strong>$this->fuel->language</strong> (<a href="<?=user_guide_url('libraries/fuel_language')?>">Fuel_language</a>)</li>
			<li><strong>$this->fuel->layouts</strong> (<a href="<?=user_guide_url('libraries/fuel_layouts')?>">Fuel_layouts</a>)</li>
			<li><strong>$this->fuel->logs</strong> (<a href="<?=user_guide_url('libraries/fuel_logs')?>">Fuel_logs</a>)</li>
			<li><strong>$this->fuel->modules</strong> (<a href="<?=user_guide_url('libraries/fuel_modules')?>">Fuel_modules</a>)</li>
			<li><strong>$this->fuel->navigation</strong> (<a href="<?=user_guide_url('libraries/fuel_navigation')?>">Fuel_navigation</a>)</li>
			<li><strong>$this->fuel->notification</strong> (<a href="<?=user_guide_url('libraries/fuel_notification')?>">Fuel_notification</a>)</li>
			<li><strong>$this->fuel->pages</strong> (<a href="<?=user_guide_url('libraries/fuel_pages')?>">Fuel_pages</a>)</li>
			<li><strong>$this->fuel->pagevars</strong> (<a href="<?=user_guide_url('libraries/fuel_pagevars')?>">Fuel_pagevars</a>)</li>
			<li><strong>$this->fuel->permissions</strong> (<a href="<?=user_guide_url('libraries/fuel_permissions')?>">Fuel_permissions</a>)</li>
			<li><strong>$this->fuel->redirects</strong> (<a href="<?=user_guide_url('libraries/fuel_redirects')?>">Fuel_redirects</a>)</li>
			<li><strong>$this->fuel->settings</strong> (<a href="<?=user_guide_url('libraries/fuel_settings')?>">Fuel_settings</a>)</li>
			<li><strong>$this->fuel->sitevars</strong> (<a href="<?=user_guide_url('libraries/fuel_sitevars')?>">Fuel_sitevars</a>)</li>
			<li><strong>$this->fuel->tags</strong> (<a href="<?=user_guide_url('libraries/fuel_tags')?>">Fuel_tags</a>)</li>
			<li><strong>$this->fuel->users</strong> (<a href="<?=user_guide_url('libraries/fuel_users')?>">Fuel_users</a>)</li>
		</ul>
	</li>
</ul>

<h3>Basic Examples</h3>
<pre class="brush:php">
// EXAMPLES if used in your controller
$page = $this->fuel->pages->create();
$nav = $this->fuel->navigation->render();
$this->fuel->cache->clear();
$this->fuel->assets->upload();
</pre>

<p class="important">All the above classes inherit from the <a href="<?=user_guide_url('libraries/fuel_base_library')?>">Fuel_base_library</a> class.</p>

<h2>Advanced Module Objects</h2>
<p>Additionally, you have access to advanced module objects if a class exists in the advanced module's libraries folder
with a naming convention of <span class="file">Fuel_{module}.php</span>. The following examples assume you have the advanced modules installed:</p>

<h3>Advanced Module Examples</h3>
<pre class="brush:php">
$posts = $this->fuel->blog->get_posts();
$this->fuel->backup->do_backup();
$this->fuel->validate->html('mypage');
$this->fuel->tester->run();
</pre>


<p class="important">The main <strong>fuel</strong> object is an Advanced Module object. Review the Advanced Module Reference in this user guide for a list of methods available for your installed advanced modules.</p>

<p class="important">The above advanced module classes inherit from the <a href="<?=user_guide_url('libraries/fuel_advanced_module')?>">Fuel_advanced_module</a> class (which itself inherits from the <a href="<?=user_guide_url('libraries/fuel_base_library')?>">Fuel_base_library</a> class).</p>
<br />

