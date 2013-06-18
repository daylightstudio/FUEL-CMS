<?php fuel_set_var('layout', '')?>

<!DOCTYPE html>

<html lang="en-US">
<head>
	<meta charset="utf-8">
 	<title>FUEL CMS</title>

	<style type="text/css">
		/***************************************************************
		RESET BROWSER VALUES SO EVERYONE IS THE SAME
		***************************************************************/
		* { outline-style: none; -moz-outline-style: none; -webkit-text-size-adjust: none; }
		h1,h2,h3,h4,h5,h6,pre,code { font-size:1em; }
		ul,ol,li,dl,dt,dd,h1,h2,h3,h4,h5,h6,pre,form,body,html,p,blockquote,fieldset { margin:0; padding:0; }
		input { margin: 0; }
		a img,:link img,:visited img { border: none; }
		ol,ul{list-style:none;}
		th{text-align:left;}
		h1,h2,h3,h4{font-size:100%;}
		q:before,q:after{content:'';}
		pre,code{font:115% monospace; font-size:100%;}
		th{text-align:left;}
		cite,code,th,address{font-style:normal;font-weight:normal;}
		body { font-size:62.5%; }

		/***************************************************************
		TAG STYLES
		***************************************************************/
		body { background: #000 url(http://www.getfuelcms.com/assets/images/bg_waves.jpg) no-repeat top center; font-family: 'Lucida Grande', Arial, sans-serif; color: #fff; font-size: 12px; }
		h1 { font-size: 40px; margin-top: 30px; text-shadow: #000 0px 1px 3px; line-height: 40px; margin-left: -4px; }
		h2 { font-size: 25px; color: #fff; color: #c8d1d9; margin-bottom: 5px; text-shadow: #000 0px 1px 3px; line-height: 25px; }
		h3 { font-size: 22px; color: #ea5b17; margin-bottom: 15px; font-weight: normal; }
		h4 { font-size: 16px; margin-bottom: 2px; }
		a { color: #dc461c; }
		p { margin-bottom: 10px; }
		ul { margin: 10px 0 20px 0; }
		ol { list-style: decimal; margin: 0 0 20px 20px; }
		ol>li { margin-bottom: 20px;  }
		li { margin-bottom: 3px; }
		ul.bullets { list-style: disc; }
		ul.bullets li { margin-left: 20px; }
		strong, code { color: #8eb8d2; }

		/***************************************************************
		GENERIC CLASSES 
		***************************************************************/
		.clear { clear: both; height: 0; line-height: 0; font-size: 0; }
		.success { background-color: #6ec461; background-repeat: no-repeat; padding: 2px; }
		.warning { background-color: #ff1; background-repeat: no-repeat; padding: 2px; }
		.error { background-color: #c30; background-repeat: no-repeat; padding: 2px; }
		.important { font-size: 16px; font-weight: bold; }
		/***************************************************************
		MAIN AREAS 
		***************************************************************/
		#container { 
			-webkit-box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.8);
			-moz-box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.8);
			width: 700px; margin: 70px auto; border: 1px solid #1e466a; padding: 20px; -moz-border-radius: 10px; -webkit-border-radius: 10px;
			background: -webkit-gradient(linear, left top, left bottom, from(rgba(42, 96, 147, 0.35)), to(#000));
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e466a', endColorstr='#000'); /* for IE */
		}
		#heading { margin-left: 160px; }
		#logo {  background: transparent url(http://www.getfuelcms.com/assets/images/fuel_logo_sm.png) no-repeat; width: 150px; height: 150px; float: left; }
		#content { padding: 10px 20px; }
		.writable li { padding: 5px 8px; font-size: 15px; text-shadow: 1px 1px 2px rgba(10, 10, 10, .75); }
		.writable li span { font-size: 13px; text-shadow: 1px 1px 1px rgba(20, 20, 20, .45) }
	</style>
</head>
<body>

	<div id="container">
		<div id="logo"></div>
		<div id="heading">
			<h1>Welcome to FUEL CMS</h1>
			<h2>Inject More Into Your Web Projects</h2>
			<h4>Version 1.0 Beta</h4>
		</div>
		<div class="clear"></div>
		<div id="content">
			<h3>Getting Started</h3>
			<ol>
				<li><h4>Change the .htaccess file</h4>
					<p>Change the Apache .htaccess found at the root of the installation folder to the proper RewriteBase directory. The default is your web server's root directory (e.g "/"). 
						You may need to enable viewing hidden files on your system to see the .htaccess file. Also, if you do not have mod_rewrite enabled you will need to change 
						the $config['index_page'] from blank to 'index.php'	in your <strong>fuel/application/config/config.php</strong> file. 
						Visit the <a href="http://httpd.apache.org/docs/2.0/misc/rewriteguide.html" target="_blank">Apache website</a> for more on <strong>mod_rewrite</strong>.
					</p>
					<p>In some server environments, you may need to add a "?" after index.php in the .htaccess like so:
						<code>RewriteRule .* index.php?/$0 [L]</code>
					</p>
					<p>NOTE: This is the only step needed if you want to use FUEL <em>without</em> the CMS.</p>
				</li>
				<li>
					<h4>Install the Database</h4>
					<p>Install the FUEL CMS database by first creating the database in MySQL and then importing the <strong>fuel/install/fuel_schema.sql</strong> file.
						After creating the database, change the database configuration found in <strong>fuel/application/config/database.php</strong> 
						to include your <code>hostname</code> (e.g. localhost), <code>username</code>, <code>password</code> and the <code>database</code> to match the new database you created.</p>
				</li>

				<li>
					<h4>Make Folders Writable</h4>
					<p>Make the following folders writable:</p>
					<ul class="writable">
						<li class="<?=(is_really_writable(APPPATH.'cache/')) ? 'success' : 'error'; ?>">
							<?=APPPATH.'cache/'?><br><span>(folder for holding cache files)</span>
						</li>
						<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/')) ? 'success' : 'error'; ?>">
							<?=APPPATH.'cache/dwoo/'?><br><span>(folder for holding Dwoo cache files)</span>
						</li>
						<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/compiled')) ? 'success' : 'error'; ?>">
							<?=APPPATH.'cache/dwoo/compiled'?><br><span>(for writing Dwoo compiled template files)</span>
						</li>
						<li class="<?=(is_really_writable(assets_server_path('', 'images'))) ? 'success' : 'error'; ?>">
							<?=WEB_ROOT.'assets/images'?><br><span>(for managing image assets in the CMS)</span>
						</li>
					</ul>
				</li>

				<?php  if ($this->config->item('encryption_key') == '' OR
							$this->config->item('fuel_mode', 'fuel') == 'views' OR
							!$this->config->item('admin_enabled', 'fuel')

				) : ?>
				<li>
					<h4>Make Configuration Changes</h4>
					<ul class="bullets">
						<?php if ($this->config->item('encryption_key') == '') : ?>
						<li>In the <strong>fuel/application/config/config.php</strong>, change the <code>$config['encryption_key']</code> to your own unique key.</li></li>
						<?php endif; ?>
						<?php if (!$this->config->item('admin_enabled', 'fuel')) : ?>
						<li>In the <strong>fuel/application/config/MY_fuel.php</strong> file, change the <code>$config['admin_enabled']</code> configuration property to <code>TRUE</code>. If you do not want the CMS accessible, leave it as <strong>FALSE</strong>.</li>
						<?php endif; ?>
						<?php if ($this->config->item('fuel_mode', 'fuel') == 'views') : ?>
						<li>In the <strong>fuel/application/config/MY_fuel.php</strong> file, change the <code>$config['fuel_mode']</code> configuration property to <code>AUTO</code>. This must be done only if you want to view pages created in the CMS.</li>
						<?php endif; ?>
					</ul>
				</li>
				<?php endif; ?>
			</ol>

			<p class="important">That&rsquo;s it!</p>
			<p>To access the FUEL admin, go to: <br>
			<a href="<?=site_url('fuel')?>"><?=site_url('fuel')?></a><br>
			User name: <strong>admin</strong> <br>
			Password: <strong>admin</strong> (you can and should change this password and admin user information after logging in)<br>
			</p>
			<br>

			<h3>What's Next?</h3>
			<ul class="bullets">
				<li>Visit the <a href="http://docs.getfuelcms.com" target="_blank">1.0 user guide</a> online.</li>
				<li>To change the contents of this homepage, edit the <strong>fuel/application/views/home.php</strong> file. Also, the header and footer files can be found in the <strong>fuel/application/_blocks/</strong> folder and the 
					CSS is located at <strong>assets/css/main.css</strong>.</li>
				<li>Need help? Visit the <a href="http://www.getfuelcms.com/forums/" target="_blank">FUEL CMS Forums</a>.</li>
				<li>Found a bug? <a href="https://github.com/daylightstudio/FUEL-CMS/issues" target="_blank">Report it on GitHub</a> (please specify 1.0 version in bug report).</li>
				<li>Subscribe to our <a href="http://twitter.com/fuelcms">Twitter Feed</a> for tips and news.</li>
				<li>Visit our <a href="https://github.com/daylightstudio?tab=repositories" target="_blank">GitHub page</a> for additional modules including:
					<ul class="bullets">
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Backup-Module" target="_blank">Backup</a></li>
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Blog-Module" target="_blank">Blog</a></li>
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Cronjobs-Module" target="_blank">Cronjobs</a></li>
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Google-Keywords-Module" target="_blank">Google Keywords</a></li>
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Page-Analysis-Module" target="_blank">Page Analysis</a></li>
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Tester-Module" target="_blank">Tester</a></li>
						<li><a href="https://github.com/daylightstudio/FUEL-CMS-Validate-Module" target="_blank">Validate</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>

</body>
</html>