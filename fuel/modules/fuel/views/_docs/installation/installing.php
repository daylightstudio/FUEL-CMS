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
If you have a current installation and are wanting to upgrade, there are a few things to be aware of. FUEL 1.4 uses CodeIgniter 3.x which includes a number of changes, the most prominent being the capitalization of controller and model names. Additionally it is more strict on reporting errors. FUEL 1.4 includes a script to help automate most (and maybe all) of the updates that may be required in your own fuel/application and installed advanced module code. It is recommended you run the following command using a different branch to test:
<pre class="brush: php">
php index.php fuel/installer/update
</pre>

<p class="important">Certain modules may require their own configuration and database SQL files to be run. Please reference their own documentation in the user guide for additional
install information.</p>