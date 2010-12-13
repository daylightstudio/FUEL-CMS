<h1>Advanced Modules</h1>
<p>Think of Advanced Modules as another application folder that can contain it's own
controllers, helpers, libraries, language and view files. They can also contain their
own assets folders. FUEL itself is an advanced module as us this <dfn>user_guide</dfn> module.
It is highly recommended that you take a peek at this module's files and any others to get a sense of how
to create one (it's not too tough!).
</p>

<h2>Steps to Create an Advanced Module</h2>
<ol>
	<li>Create a folder to contain your code in the <dfn>fuel/modules/</dfn> folder</li>
	<li>Create your config folder and add a <dfn>{module_name}.php</dfn>, <dfn>{module_name}_constants.php</dfn>, and a <dfn>{module_name}_routes.php</dfn>
		<ul>
			<li>In the config file, add your module specific configuration information as well as and FUEL admin interface information like:<br />
				<dfn>$config['nav']['tools']['tools/{module_name}'] = '{module_name}';</dfn><br />
				This line will add the menu item in the admin.
			</li>
			<li>In the constants folder its a good idea to create the following constants (although not required)
				<ul>
					<li><strong>{MODULE_NAME}_VERSION</strong> - the version number of the module</li>
					<li><strong>{MODULE_NAME}_FOLDER</strong> - the folder name of the module</li>
					<li><strong>{MODULE_NAME}_PATH</strong> - the full directory path to the module folder</li>
				</ul>
			</li>
			<li>In the routes file, add the fuel routes you want to use. For example, this <strong>user_guide</strong> module has the following routes:
				<ul>
					<li>$route[FUEL_ROUTE.'tools/user_guide'] = 'user_guide';</li>
					<li>$route[FUEL_ROUTE.'tools/user_guide/:any'] = 'user_guide/$1';</li>
				</ul>
			</li>
		</ul>
	
	</li>
	<li>Create your controller files. 
		<ul>
			<li><strong>Admin Controllers</strong> - Pages that need to be displayed in the admin interface should inherit from the <dfn>fuel/libraries/Fuel_base_controller.php</dfn> and can use the <dfn>_validate_user()</dfn>, protected controller method.</li>
			<li><strong>Dashboard Controller</strong> - If you add a controller with the name of <dfn>Dashboard</dfn>, then it can get pulled in to the FUEL admin (if the module is in the fuel $config['dashboards'] configuration)</li>
		</ul>
	</li>
	<li>Create your assets folder and add any specific styles you want to use for that module's admin interface in a <dfn>css/{module_name}.css</dfn> file including your menu item icons.</li>
	<li>If you have the <a href="<?=user_guide_url('modules/tester')?>">Tester</a> module, you can add your tests to a <dfn>tests</dfn> folder.</li>
	<li>If you have this <dfn>user_guide</dfn> module installed, you can add documentation to the <dfn>views/_docs/</dfn> folder. There needs to be at least an <dfn>index.php</dfn> file before it will appear in the <dfn>user_guide</dfn> module.</li>
</ol>