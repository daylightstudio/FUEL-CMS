<?php fuel_set_var('layout', '')?>

<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
 	<title>Welcome to FUEL CMS</title>
	<link href='http://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'>
	<?php echo css('main')?>
	
</head>
<body>
	<div class="page">
		<div class="wrapper">
			<header class="page_header">
				<div class="logo"><object type="image/svg+xml" width="160" height="145" data="<?=img_path('_template_icons.svg#fuel') ?>"></object></div>
				<h1>Welcome to Fuel CMS</h1>
				<h2>Version <?=FUEL_VERSION?></h2>
			</header>
			<section>
				<header>
					<div class="icon_block">
						<object type="image/svg+xml" width="74" height="68" data="<?=img_path('_template_icons.svg#clipboard') ?>"></object>
					</div>
					<div class="content_block">
						<h3>Getting Started</h3>
					</div>
				</header>
				<ol>
					<li>
						<div class="icon_block">
							<div class="circle">1</div>
						</div>
						<div class="content_block">
							<h4>Change the Apache .htaccess file</h4>
							<p>Change the Apache .htaccess found at the root of FUEL CMS's installation folder to the proper RewriteBase directory. The default is your web server's root directory (e.g "/"), but if you have FUEL CMS installed in a sub folder, you will need to add the path to line 5.
							If you are using the folder it was zipped up in from GitHub, it would be <strong>RewriteBase /FUEL-CMS-master/</strong>.</p>
							<p>In some server environments, you may need to add a "?" after index.php in the .htaccess like so: <code>RewriteRule .* index.php?/$0 [L]</code></p>
							<p class="callout"><strong>NOTE:</strong> This is the only step needed if you want to use FUEL <em>without</em> the CMS.</p>
						</div>
					</li>
					<li>
						<div class="icon_block">
							<div class="circle">2</div>
						</div>
						<div class="content_block">
							<h4>Install the database</h4>
							<p>Install the FUEL CMS database by first creating the database in MySQL and then importing the <strong>fuel/install/fuel_schema.sql</strong> file. After creating the database, change the database configuration found in <strong>fuel/application/config/database.php</strong> to include your hostname (e.g. localhost), username, password and the database to match the new database you created.</p>
						</div>
					</li>
					<li>
						<div class="icon_block">
							<div class="circle">3</div>
						</div>
						<div class="content_block">
							<h4>Make folders writable</h4>
							<p>Make the following folders writable (666 = rw-rw-rw, 777 = rwxrwxrwx, etc.):</p>
							<ul class="writable">
								<li class="<?=(is_really_writable(APPPATH.'cache/')) ? 'success' : 'error'; ?>">
									<strong><?=APPPATH.'cache/'?></strong><br><span>(folder for holding cache files)</span>
								</li>
								<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/')) ? 'success' : 'error'; ?>">
									<strong><?=APPPATH.'cache/dwoo/'?></strong><br><span>(folder for holding template cache files)</span>
								</li>
								<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/compiled')) ? 'success' : 'error'; ?>">
									<strong><?=APPPATH.'cache/dwoo/compiled'?></strong><br><span>(for writing compiled template files)</span>
								</li>
								<li class="<?=(is_really_writable(assets_server_path('', 'images'))) ? 'success' : 'error'; ?>">
									<strong><?=WEB_ROOT.'assets/images'?></strong><br><span>(for managing image assets in the CMS)</span>
								</li>
							</ul>
						</div>
					</li>

						<?php  if ($this->config->item('encryption_key') == '' OR
								$this->config->item('fuel_mode', 'fuel') == 'views' OR
								!$this->config->item('admin_enabled', 'fuel')

					) : ?>
					<li>
						<div class="icon_block">
							<div class="circle">4</div>
						</div>
						<div class="content_block">
							<h4>Make configuration changes</h4>
							<ul class="writable">
								<?php if ($this->config->item('encryption_key') == '') : ?>
								<li>In the <strong>fuel/application/config/config.php</strong>, <a href="http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/" target="_blank">change the <code>$config['encryption_key']</code> to your own unique key</a>.</li></li>
								<?php endif; ?>
								<?php if (!$this->config->item('admin_enabled', 'fuel')) : ?>
								<li>In the <strong>fuel/application/config/MY_fuel.php</strong> file, change the <code>$config['admin_enabled']</code> configuration property to <code>TRUE</code>. If you do not want the CMS accessible, leave it as <strong>FALSE</strong>.</li>
								<?php endif; ?>
								<?php if ($this->config->item('fuel_mode', 'fuel') == 'views') : ?>
								<li>In the <strong>fuel/application/config/MY_fuel.php</strong> file, change the <code>$config['fuel_mode']</code> configuration property to <code>AUTO</code>. This must be done only if you want to view pages created in the CMS.</li>
								<?php endif; ?>
							</ul>
						</div>
					</li>
					<?php endif; ?>
					
				</ol>
				<div>
					<div class="icon_block"></div>
					<div class="content_block">
						<h4>That's it!</h4>

						<p>To access the FUEL admin, go to:<br/>
						<a href="<?=site_url('fuel')?>"><?=site_url('fuel')?></a><br>
						User name: <strong>admin</strong><br/>
						Password: <strong>admin</strong> (you can and should change this password and admin user information after logging in)</p>
					</div>
				</div>
			</section>

			<section>
				<header>
					<div class="icon_block">
						<div class="logo"><object type="image/svg+xml" width="74" height="68" data="<?=img_path('_template_icons.svg#signpost') ?>"></object></div>
					</div>
					<div class="content_block">
						<h3>What's Next?</h3>
					</div>
				</header>
				<ol>
					<li>
						<div class="icon_block">
							<div class="circle"><object type="image/svg+xml" width="30" height="27" data="<?=img_path('_template_icons.svg#map') ?>"></object></div>
						</div>
						<div class="content_block">
							<a class="cta" href="http://docs.getfuelcms.com" target="_blank" style="margin-top: 20px;">User Guide <object type="image/svg+xml" width="30" height="27" data="<?=img_path('_template_icons.svg#rightarrow') ?>"></object></a>
							<h4>Visit the <a href="http://docs.getfuelcms.com" target="_blank">1.0 user guide</a> online</h4>
						</div>
					</li>
					<li>
						<div class="icon_block">
							<div class="circle"><object type="image/svg+xml" width="40" height="37" data="<?=img_path('_template_icons.svg#github') ?>"></object></div>
						</div>
						<div class="content_block">
							<h4>Get rolling</h4>
							<p>FUEL CMS is open source and on GitHub for a good reason. The communities involvement is an important part of it's success.
							If you have any ideas for improvement, or even better, a <a href="https://help.github.com/articles/creating-a-pull-request" target="_blank">GitHub pull request</a>, please let us know.</p>


							<ul class="bullets">
								<li>Need help? Visit the <a href="http://forum.getfuelcms.com" target="_blank">FUEL CMS Forum</a>.</li>
								<li>Found a bug? <a href="https://github.com/daylightstudio/FUEL-CMS/issues" target="_blank">Report it on GitHub</a>.</li>
								<li>Subscribe to our <a href="http://twitter.com/fuelcms">Twitter feed</a> for FUEL CMS notifications.</li>
								<li>Visit our <a href="http://twitter.com/blog" target="_blank">blog</a> for tips and news.</li>
								<li>Visit our <a href="http://www.getfuelcms.com/developers" target="_blank">developer page</a> for additional modules.</li>
							</ul>

							<p>To change the contents of this homepage, edit the <strong>fuel/application/views/home.php</strong> file.</p>

							<p>Happy coding!</p>
						</div>
					</li>
				</ol>
			</section>
		</div>
	</div>
	<div class="wrapper">
	<footer class="row footer">
		<nav class="mainnav">
			<ul>
				<li class="first active"><a href="http://www.getfuelcms.com" target="_blank">Home</a></li>
				<li><a href="http://getfuelcms.com/features" target="_blank">Features</a></li>
				<li><a href="http://getfuelcms.com/developers" target="_blank">Developers</a></li>
				<li><a href="http://getfuelcms.com/support" target="_blank">Support</a></li>
				<li class="last"><a href="http://getfuelcms.com/blog" target="_blank">Blog</a></li>
			</ul>
		</nav>
		<p class="colophon">FUEL CMS is developed with love by <a href="http://thedaylightstudio.com" target="_blank">Daylight Studio</a> <object type="image/svg+xml" width="25" height="25" data="<?=img_path('_template_icons.svg#daylight') ?>"></object> &copy; <?php echo date("Y"); ?> Run for Daylight LLC, All Rights Reserved.</p>
	</footer>
</div>
</body>
</html>