<h1>Modules</h1>
<p>Modules are a way for you to extend the functionality of FUEL. 
The term <dfn>modules</dfn> is used rather loosely and it is entirely possible to 
nest modules within another module (sometimes called <dfn>sub-modules</dfn>). We refer to these types of modules as <dfn>advanced modules</dfn>.
In fact, FUEL is no more then an advanced module itself.</p>

<p>Most of the time however, modules are simply just data models (<dfn>simple modules</dfn>) with a form interface to change values.</p>
<ul>
	<li><a href="<?=user_guide_url('modules/simple')?>">Simple Modules</a></li>
	<li><a href="<?=user_guide_url('modules/tutorial')?>">Creating Simple Modules Tutorial</a></li>
	<li><a href="<?=user_guide_url('modules/advanced')?>">Advanced Modules</a></li>
	<li><a href="<?=user_guide_url('modules/forms')?>">Module Forms</a></li>
	<li><a href="<?=user_guide_url('modules/hooks')?>">Module Hooks</a></li>
</ul>


<h2>Installation</h2>
<p>One of the bigger changes in FUEL v1.0 was the removal of all the extra bundled advanced modules. The reason was to keep the initial FUEL install
smaller, as well as provide separate GIT repository versioning and issue tracking for each. 
A list of some available modules can currently be found at <a href="https://github.com/daylightstudio" target="_blank">https://github.com/daylightstudio</a>.
Because of this change, it was deemed necessary to make the installation and updating process of advanced modules a little easier going forward. </p>


<h3>Using GIT Submodules</h3>
<p>We like to lean on GIT as much as possible for the updating of advanced modules and so we've added a couple command line tools to make this a little easier. 
The first is to use GIT to add a <a href="http://git-scm.com/book/en/Git-Tools-Submodules" target="_blank">submodule</a> to your installation in your <span class="file">fuel/modules/{module}</span> folder. 
Adding the advanced module as a GIT submodule will allow you to run updates independent of your main FUEL CMS repository.
Adding a submodule can be done one of two ways. The first way uses the native "submodule" command from GIT, and the second uses the FUEL installer "add_get_submodule" controller method. 
To get started, you first need to open up a terminal window and "<a href="http://en.wikipedia.org/wiki/Cd_(command)" target="_blank">cd</a>" (change directory) to the 
installation directory (where the index.php CodeIgniter bootstrap file exists alongside the "fuel" folder). Then run the commands below where "php" references the path to your PHP interpreter.</p>

<pre class="brush: php">
// GIT
&gt;php git submodule add git://github.com/daylightstudio/FUEL-CMS-Blog-Module.git fuel/modules/blog

// FUEL
&gt;php index.php fuel/installer/add_git_submodule git://github.com/daylightstudio/FUEL-CMS-Blog-Module.git blog
</pre>

<p class="important">If your git repo path has a "@" in it, change it to a "-at-" or else you will get a "The URI you submitted has disallowed characters" error.</p>

<p>Phil Sturgeon of PyroCMS fame has a good <a href="http://philsturgeon.co.uk/blog/2011/09/managing-codeigniter-packages-with-git-submodules" target="_blank">article on using submodules</a>.</p>

<h3>Finalizing the Installation</h3>
<p>After adding the folder to your <span class="file">fuel/modules</span> folder either manually or via GIT, you may need to finalize the istallation by running some 
additional SQL statements. These SQL statements can be found in your modules "install" folder. FUEL provides a simpler way to install both the SQL and any necessary permissions 
for the module that can be assigned to other users. To do so, you need to open up a terminal window and "<a href="http://en.wikipedia.org/wiki/Cd_(command)" target="_blank">cd</a>" (change directory) to the 
to the installation directory (similar to above). Then run the following:</p>

<pre class="brush: php">
&gt;php index.php fuel/installer/install blog
</pre>

<h3>Uninstall</h3>
<p>If you need to uninstall the module, you can delete the folder and then run the following command to clean up any data associated with the module:</p>
<pre class="brush: php">
&gt;php index.php fuel/installer/uninstall blog
</pre>

<p class="important">The "ENVIRONMENT" constant defined in your index.php bootstrap file must be set to "development" in order install modules via the command line</p>

<h3>Configuring Your Own Installation</h3>
<p>If you create your own advanced module and would like to share it with the community, you can create your own install configuration to help with the installation process.
To do so, you can create an <span class="file">install/install.php</span> file with the following parameters in it:
<ul>
	<li>name</li>
	<li>version</li>
	<li>author</li>
	<li>company</li>
	<li>license</li>
	<li>copyright</li>
	<li>author_url</li>
	<li>description</li>
	<li>compatibility</li>
	<li>instructions</li>
	<li>permissions</li>
	<li>migration_version</li>
	<li>install_sql</li>
	<li>uninstall_sql</li>
	<li>repo</li>
</ul>
<p>For example, the Blog module has the following install configuration.</p>
<span class="file">fuel/modules/blog/install/install.php</span>
<pre class="brush: php">
$config['name'] = 'Blog Module';
$config['version'] = BLOG_VERSION;
$config['author'] = 'David McReynolds';
$config['company'] = 'Daylight Studio';
$config['license'] = 'Apache 2';
$config['copyright'] = '2012';
$config['author_url'] = 'http://www.thedaylightstudio.com';
$config['description'] = 'The FUEL Blog Module can be used to create posts and allow comments in an organized manner on your site.';
$config['compatibility'] = '1.0';
$config['instructions'] = '';
$config['permissions'] = array('blog/posts', 'blog/comments', 'blog/categories', 'blog/users');
$config['migration_version'] = 0;
$config['install_sql'] = 'fuel_blog_install.sql';
$config['uninstall_sql'] = 'fuel_blog_uninstall.sql';
$config['repo'] = 'git://github.com/daylightstudio/FUEL-CMS-Blog-Module.git';
</pre>

<p class="important">Note that you can use <a href="http://codeigniter.com/user_guide/libraries/migration.html" target="_blank">CodeIgniter's migrations</a> for managing SQL versioning as an
alternative to using install_sql and uninstall_sql parameters.</p>
