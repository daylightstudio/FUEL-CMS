<h1>Installing FUEL CMS</h1>
<p>FUEL CMS uses CodeIgniter 2.0. If you are familiar with installing CodeIgniter, then many of the following steps will seem familiar to you.</p>
<p>The following are steps to installing FUEL:</p>
<ol>
	<li>Download the latest version from <a href="http://www.getfuelcms.com" target="_blank">getfuelcms.com</a></li>
	<li>Place the downloaded folder onto your webserver and make sure your <dfn>fuel/data_backup</dfn>, <dfn>fuel/install</dfn> and <dfn>fuel/crons</dfn> folders are in a folder inaccessible to the web <strong>(the .htaccess file has these blocked by default so you may not need to do anything)</strong>.</li>
	<li>Browse to the index page. You should see a page of similar instructions as below.</li>
	<li>Alter your Apache .htaccess file to the proper RewriteBase directory. The default is your web servers root directory. <strong>If you do not have mod_rewrite enabled you will need to change the $config['index_page'] from blank to 'index.php' in <strong>fuel/application/config.php</strong></strong></li>
	<li>Install the database by first creating the database in MySQL and then running the <dfn>fuel/install/fuel_schema.sql</dfn> file</li>
	<li>Configure the <dfn>fuel/application/config/database.php</dfn> file with the proper database connection settings (like with any other CodeIgniter database application)</li>
	<li>Make the following folders writable (note that <dfn>/var/www/httpdocs/</dfn> is the path to your web server):
		<ul>
			<li class="<?=(is_really_writable(BASEPATH.'cache/')) ? 'success' : 'error'; ?>">
				/var/www/httpdocs/cache/
			</li>
			<li class="<?=(is_really_writable(BASEPATH.'cache/dwoo/')) ? 'success' : 'error'; ?>">
				/var/www/httpdocs/cache/dwoo/
			</li>
			<li class="<?=(is_really_writable(BASEPATH.'cache/dwoo/compiled')) ? 'success' : 'error'; ?>">
				/var/www/httpdocs/cache/dwoo/compiled
			</li>
			<li class="<?=(is_really_writable(assets_server_path('', 'images'))) ? 'success' : 'error'; ?>">
				/var/www/httpdocs/assets/images
			</li>
		</ul>
	</li>
</ol>

<p>That's it!</p>

<p class="important">Certain modules may require their own configuration and database SQL files to be run. Please reference their own documentation in the user guide for additional
install information.</p>