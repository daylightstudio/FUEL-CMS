<h1>Installing FUEL CMS</h1>
<p>FUEL CMS uses CodeIgniter. If you are familiar with installing CodeIgniter, then many of the following steps will seem familiar to you.</p>
<p>The following are steps to installing FUEL:</p>
<ol>
	<li>Download the latest version from <a href="http://www.getfuelcms.com" target="_blank">getfuelcms.com</a></li>
	<li>Place the downloaded folder onto your webserver. Note that the <dfn>fuel/data_backup</dfn>, <dfn>fuel/install</dfn> and <dfn>fuel/scripts</dfn> folders should be a folders inaccessible from the web if using .htaccess.</li>
	<li>Browse to the index page. You should see a page of similar instructions as below.</li>
	<li>Alter your Apache .htaccess file to the proper RewriteBase directory. The default is your web servers root directory. <strong>If you do not have mod_rewrite enabled you will need to change the $config['index_page'] from blank to 'index.php' in <strong>fuel/application/config/config.php</strong></strong></li>
	<li>Install the database by first creating the database in MySQL and then running the <dfn>fuel/install/fuel_schema.sql</dfn> file</li>
	<li>Configure the <dfn>fuel/application/config/database.php</dfn> file with the <a href="<?=user_guide_url('installation/db-setup')?>">proper database connection settings</a> (like with any other CodeIgniter database application)</li>
	<li>Change the <dfn>$config['encryption_key']</dfn> found in the <dfn>fuel/application/config/config.php</dfn> file</li>
	<li>Make the following folders writable:
		<ul>
			<li>
				<strong>fuel/application/cache/</strong>
			</li>
			<li>
				<strong>fuel/application/cache/dwoo/</strong>
			</li>
			<li>
				<strong>fuel/application/cache/dwoo/compiled/</strong>
			</li>
			<li>
				<strong>assets/images/</strong>
			</li>
		</ul>
	</li>
</ol>

<p>That's it!</p>

<h2>Upgrading to 1.4</h2>
<p>If you have a current installation of 1.3x or less and are wanting to upgrade, there are a few things to be aware of. 
FUEL 1.4 uses CodeIgniter 3.x which includes a number of changes, the most prominent being the capitalization of controller and model names. 
Additionally it is more strict on reporting errors. </p>

<p>Upgrading to 1.4 will be a bit of a manual process and different depending on how your projects were developed. In general, FUEL CMS gives developers the fuel/application folder
as their coding playground. So one option would be to start with a fresh 1.4 installation and copy over the following files (mileage may vary depending on your project):</p>

<ul>
	<li>fuel/application/controllers/*</li>
	<li>fuel/application/helpers/*</li>
	<li>fuel/application/libraries/ (that are specific to your project and not FUEL related overwrites)</li>
	<li>fuel/application/models/*</li>
	<li>fuel/application/views/*</li>
	<li>fuel/application/config/MY_fuel.php</li>
	<li>fuel/application/config/MY_fuel_modules.php</li>
	<li>fuel/application/config/MY_fuel_layouts.php</li>
	<li>fuel/modules/ (any module folders that aren't fuel... leave that one alone)</li>
</ul>

<p>Another option would be to create a separate branch using Git and add the following as a remote repository in which to pull and merge from:</p>
<pre class="brush: php">https://github.com/daylightstudio/FUEL-CMS.git</pre>

<p>It will indicate potential merge conflicts and some of them you may need to fix if they are your own code changes in the fuel/application folder. However, most will just need to be accepted.</p>

<p>Additionally, FUEL 1.4 includes a script to help automate most (and maybe all) of the updates that may be required for your own actual code to address capitalization issues, method parameter errors and more in your own fuel/application and installed advanced module code.
It is recommended you run the following command from the folder you installed FUEL CMS using a different Git branch to test out. 
Mac OSX or a Unix flavor operating system and Git are required to run the script:</p>
<pre class="brush: php">
php index.php fuel/installer/update
</pre>

<p>Lastly, there is a SQL update file at <span class="file">fuel/install/upgrades/fuel_1.4_schema_changes.sql</span> that should be run and performs some minor updates to the schema.</p>

<p class="important">Certain modules may require their own configuration and database SQL files to be run. Please reference their own documentation in the user guide for additional
install information.</p>