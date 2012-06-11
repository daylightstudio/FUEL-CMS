<h1>Installing FUEL CMS</h1>
<p>FUEL CMS uses CodeIgniter. If you are familiar with installing CodeIgniter, then many of the following steps will seem familiar to you.</p>
<p>The following are steps to installing FUEL:</p>
<ol>
	<li>Download the latest version from <a href="http://www.getfuelcms.com" target="_blank">getfuelcms.com</a></li>
	<li>Place the downloaded folder onto your webserver. Note that the <dfn>fuel/data_backup</dfn>, <dfn>fuel/install</dfn> and <dfn>fuel/scripts</dfn> folders should be a folders inaccessible from the web if using .htaccess.</li>
	<li>Browse to the index page. You should see a page of similar instructions as below.</li>
	<li>Alter your Apache .htaccess file to the proper RewriteBase directory. The default is your web servers root directory. <strong>If you do not have mod_rewrite enabled you will need to change the $config['index_page'] from blank to 'index.php' in <strong>fuel/application/config.php</strong></strong></li>
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

<p class="important">Certain modules may require their own configuration and database SQL files to be run. Please reference their own documentation in the user guide for additional
install information.</p>