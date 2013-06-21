<h1>Advanced Modules</h1>
<p>Think of Advanced Modules as another application folder that can contain it's own
controllers, helpers, libraries, language and view files. They can also contain their
own assets folders. FUEL itself is an advanced module as is this <dfn>user_guide</dfn> module.
It is highly recommended that you take a peek at this module's files and any others to get a sense of how
to create one (it's not too tough!).
</p>

<h2>Generating an Advanced Module</h2>
<p>FUEL comes with the ability to automatically <a href="<?=user_guide_url('modules/generate')?>">generate</a> the starter files for an advanced module. Below is an example of how to 
do so:</p>
<pre class="brush:php">
php index.php fuel/generate/advanced examples
</pre>
<p class="important"><a href="<?=user_guide_url('modules/generate')?>">Read more about FUEL's generate capabilities</a>.</p>

<h2>Steps to Create an Advanced Module Manually</h2>
<ol>
	<li>Create a folder to contain your code in the <dfn>fuel/modules/</dfn> folder</li>
	<li>Create your config folder and add a <dfn>{module_name}.php</dfn>, <dfn>{module_name}_constants.php</dfn>, and a <dfn>{module_name}_routes.php</dfn>
		<ul>
			<li>In the <dfn>{module_name}.php</dfn> config file, add your module specific configuration information as well as any FUEL admin interface information like:<br />
				<dfn>$config['nav']['tools']['tools/{module_name}'] = '{module_name}';</dfn><br />
				This will create a menu <dfn>{module_name}</dfn> in the admin left navigation with a FUEL admin link of <dfn>tools/{module_name}</dfn>.<p>

				As another example, let's look at the blogs configuration file found under <dfn>fuel/modules/blog/config/blog.php</dfn>:</p>
				
<pre class="brush: php">
$config['nav']['blog'] = array(
'blog/posts' => 'Posts', 
'blog/categories' => 'Categories', 
'blog/comments' => 'Comments', 
'blog/links' => 'Links', 
'blog/users' => 'Authors', 
'blog/settings' => 'Settings'
);
</pre>


				<p>This will create the following in the admin left navigation.</p>
<pre class="brush: php">
&lt;li&gt;&lt;a href=&quot;http://localhost/fuelcmsdemo/index.php/fuel/blog/posts&quot; class=&quot;ico ico_blog_posts&quot;&gt;Posts&lt;/a&gt;&lt;/li&gt;
&lt;li&gt;&lt;a href=&quot;http://localhost/fuelcmsdemo/index.php/fuel/blog/categories&quot; class=&quot;ico ico_blog_categories&quot;&gt;Categories&lt;/a&gt;&lt;/li&gt;
&lt;li&gt;&lt;a href=&quot;http://localhost/fuelcmsdemo/index.php/fuel/blog/comments&quot; class=&quot;ico ico_blog_comments&quot;&gt;Comments&lt;/a&gt;&lt;/li&gt;
&lt;li&gt;&lt;a href=&quot;http://localhost/fuelcmsdemo/index.php/fuel/blog/links&quot; class=&quot;ico ico_blog_links&quot;&gt;Links&lt;/a&gt;&lt;/li&gt;
&lt;li&gt;&lt;a href=&quot;http://localhost/fuelcmsdemo/index.php/fuel/blog/users&quot; class=&quot;ico ico_blog_users&quot;&gt;Authors&lt;/a&gt;&lt;/li&gt;
&lt;li&gt;&lt;a href=&quot;http://localhost/fuelcmsdemo/index.php/fuel/blog/settings&quot; class=&quot;ico ico_blog_settings&quot;&gt;Settings&lt;/a&gt;&lt;/li&gt;
</pre>	

			<p class="important">You can overwrite the configuration values by adding your own <dfn>application/config/{module}.php</dfn> file with the specific values you want to overwrite.</p>

			</li>
			<li>In the {module_name}_constants.php file its a good idea to create the following constants (although not required):
				<ul>
					<li><strong>{MODULE_NAME}_VERSION</strong> - the version number of the module</li>
					<li><strong>{MODULE_NAME}_FOLDER</strong> - the folder name of the module</li>
					<li><strong>{MODULE_NAME}_PATH</strong> - the full directory path to the module folder</li>
				</ul>
			</li>
			<li>In the routes file, add the fuel routes you want to use. For example, this <strong>user_guide</strong> module has the following routes:
				<ul>
					<li>$route[FUEL_ROUTE.'tools/user_guide'] = 'user_guide';</li>
					<li>$route[FUEL_ROUTE.'tools/user_guide/(:any)'] = 'user_guide/$1';</li>
				</ul>
			</li>
		</ul>
	
	</li>
	<li>Create your controller files. 
		<ul>
			<li><strong>Admin Controllers</strong> - Pages that need to be displayed in the admin interface should inherit from the <dfn>fuel/modules/fuel/libraries/Fuel_base_controller.php</dfn> OR <dfn>fuel/modules/fuel/controllers/module.php</dfn> (which inherits from Fuel_base_controller) and can use the <dfn>_validate_user()</dfn>, protected controller method.</li>
			<li><strong>Dashboard Controller</strong> - If you add a controller with the name of <dfn>Dashboard</dfn>, then it can get pulled in to the FUEL admin (if the module is in the fuel $config['dashboards'] configuration)</li>
		</ul>
	</li>
	<li>Create your assets folder and add any specific styles you want to use for that module's admin interface in a <dfn>css/{module_name}.css</dfn> file including your menu item icons.</li>
	<li>If you have the <a href="<?=user_guide_url('tools/user_guide/modules/user_guide')?>">Tester</a> module, you can add your tests to a <dfn>tests</dfn> folder.</li>
	<li>If you have this <dfn>user_guide</dfn> module installed, you can add documentation to the <dfn>views/_docs/</dfn> folder. There needs to be at least an <dfn>index.php</dfn> file before it will appear in the <dfn>user_guide</dfn> module.</li>
	<li>Last but not least, in order to see your module you must add the folder name of your module to the <dfn>$config['modules_allowed']</dfn> array in your <dfn>fuel/application/config/MY_fuel.php</dfn> file.</li>
</ol>


<h2>Loading Module Assets</h2>
<p>Loading module assets can be done one of two ways in your module controllers:</p>
<ol>
	<li><dfn>$this->load->library('my_module/my_lib');</dfn></li>
	<li><dfn>$this->load->module_library('my_module', 'my_lib');</dfn></li>
</ol>
