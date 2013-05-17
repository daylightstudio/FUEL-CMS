<h1>Generate</h1>
<p>FUEL CMS provides a command line tool to help you auto generate starter files for your simple and advanced modules. To do so, navigate 
to the folder (e.g. using "cd") where the main bootstrap index.php file exists. Then you can type the commands below which use the
<a href="http://codeigniter.com/user_guide/general/cli.html" target="_blank">CodeIgniter CLI</a> to generate files automatically.</p>

<h2 id="configuration">Configuration and Templates</h2>
<p>The default generated template files exist in the <span class="file">fuel/modules/fuel/views/_generated/{type}</span> folder where <dfn>{type}</dfn>
is either "advanced", "model" or "simple". You can overwrite these defaults by creating a <span class="file">fuel/application/views/_generated/{type}</span> 
folder with files that correspond to the files specified in the <a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration file</a> under the "generated" parameter.
Additionally, you can set additional module folders to look in by adding to the "search" parameter array shown in the configuration array below:
</p>
<pre class="brush:php">
$config['generate'] = array(
							'search'   => array('app', 'fuel'),
							'advanced' => array(
										'assets/css/{module}.css',
										'assets/images/ico_cog.png',
										'assets/js/{ModuleName}Controller.js',
										'assets/cache/',
										'config/{module}.php',
										'config/{module}_constants.php',
										'config/{module}_routes.php',
										'controllers/{module}_module.php',
										'helpers/{module}_helper.php',
										'install/install.php',
										'libraries/Fuel_{module}.php',
										'language/english/{module}_lang.php',
										'models/',
										'tests/sql/',
										'views/_admin/{module}.php',
										'views/_blocks/',
										'views/_docs/index.php',
										'views/_layouts/',
							),
							'simple' => 'MY_fuel_modules.php',
							'model'  => array(
											'{model}_model.php',
											'sql/{table}.sql',
											),
										);

</pre>

<h2 id="models">Models</h2>
<p>The following will create a model named "examples_model.php", generate a placeholder table (you'll need to modify):</p>
<pre class="brush:php">
php index.php fuel/generate/model examples

// generates multiple models at once
php index.php fuel/generate/model examples:examples2

// generates a model in an advanced module named examples
php index.php fuel/generate/model my_model examples
</pre>

<p class="important">If you are on a MAC and having trouble where the script is outputting nothing, you may need to make sure you are calling the right php binary. In my case, I needed to call to a /Applications/MAMP/bin/php5/bin/php. Here is a thread that talks about it more: http://codeigniter.com/forums/viewthread/130383/ Hopefully it saves you some time too!</p>
<p class="important">Separating module names with a colon will generate multiple models.</p>

<h2 id="simple">Simple Modules</h2>
<p>The following will create a model named "examples_model.php", generate a placeholder table (you'll need to modify), add the permisions to manage it in the CMS,  and will add it to the <span class="file">fuel/application/config/MY_fuel_modules.php</span>:</p>
<pre class="brush:php">
php index.php fuel/generate/simple examples
</pre>

<h2 id="advaned">Advanced Modules</h2>
<p>The following will create a directory named "test" in the <span class="file">fuel/modules/</span> folder, as well as create a permission and add it to the "modules_allowed" FUEL configuration.
It will generate by default the files specified in the
<a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration file</a> under the <dfn>generated</dfn> parameter:</p>
<pre class="brush:php">
php index.php fuel/generate/advanced examples
</pre>

<p>After creating the advanced module, you will need to still setup your simple modules within that advanced module. 
	To do that, you can pass a second parameter to the "simple" generate CLI command to tell which advanced module folder to generate the simple module:
</p>
<pre class="brush:php">
php index.php fuel/generate/simple my_model examples
</pre>
<h2 id="placeholder">Placeholder Variables</h2>
<p>The following variables are available to be used in your generation templates:</p>
<ul>
	<li><strong>{module}</strong>: the module name (lowercased)</li>
	<li><strong>{model}</strong>: the model name without the "_model" suffix (lowercased)</li>
	<li><strong>{table}</strong>: the table name</li>
	<li><strong>{module_name}</strong>: the name of the module with the first letter of each word upper cased (e.g. My Module)</li>
	<li><strong>{model_name}</strong>: the model name with the first letter upper cased and without the suffix (e.g. News)</li>
	<li><strong>{model_record}</strong>: The model records name which will remove any pluralization</li>
	<li><strong>{ModuleName}</strong>: A camel-cased version of the module name</li>
	<li><strong>{MODULE_NAME}</strong>: An all upper cased version of the module's name (used for constants)</li>
</ul>

<h2 id="samples">Samples</h2>
<p>By default, the FUEL generate command comes with several models that we thought were pretty common to many website projects and so we included them as samples&mdash;they are <dfn>news</dfn>, <dfn>events</dfn>, <dfn>careers</dfn>.
When creating a model or simple module with those names, you will get those models generated instead as a starting point.
</p>

