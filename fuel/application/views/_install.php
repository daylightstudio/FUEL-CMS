<?php fuel_set_var('layout', '')?>

<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
 	<title>Welcome to FUEL CMS</title>
	<link href='http://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'>
	<style type="text/css">
		/* NORMALIZE================================================================ */
		article,aside,details,figcaption,figure,footer,header,hgroup,nav,section,summary{display:block;} audio,canvas,video{display:inline-block;*display:inline;*zoom:1;} audio:not([controls]){display:none;height:0;} [hidden]{display:none;} html{font-size:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100% ;} html,button,input,select,textarea{font-family:sans-serif;} body{margin:0;} a:focus{outline:thin dotted;} a:active,a:hover{outline:0;} h1{font-size:2em;margin:0.67em 0;} h2{font-size:1.5em;margin:0.83em 0;} h3{font-size:1.17em;margin:1em 0;} h4{font-size:1em;margin:1.33em 0;} h5{font-size:.83em;margin:1.67em 0;} h6{font-size:.67em;margin:2.33em 0;} abbr[title]{border-bottom:1px dotted;} b,strong{font-weight:700;} blockquote{margin:1em 40px;} dfn{font-style:italic;} mark{background:#ff0;color:#000000;} p,pre{margin:1em 0;} code,kbd,pre,samp{font-family:monospace,serif;_font-family:'courier new',monospace;font-size:1em;} pre{white-space:pre;white-space:pre-wrap;word-wrap:break-word;} q{quotes:none;} q:before,q:after{content:'';content:none;} small{font-size:80%;} sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline;} sup{top:-0.5em;} sub{bottom:-0.25em;} dl,menu,ol,ul{margin:1em 0;} dd{margin:0 0 0 40px;} menu,ol,ul{padding:0 0 0 20px;} nav ul,nav ol{list-style:none;list-style-image:none;} img{border:0;-ms-interpolation-mode:bicubic ;} svg:not(:root){overflow:hidden;} figure{margin:0;} form{margin:0;} fieldset{border:1px solid silver;margin:0 2px;padding:0.35em 0.625em 0.75em;} legend{border:0;padding:0;white-space:normal;*margin-left:-7px ;} button,input,select,textarea{font-size:100%;margin:0;vertical-align:baseline;*vertical-align:middle ;} button,input{line-height:normal;} button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer;*overflow:visible ;} button[disabled],input[disabled]{cursor:default;} input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0;*height:13px;*width:13px ;} input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;} input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none;} button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0;} textarea{overflow:auto;vertical-align:top ;} table{border-collapse:collapse;border-spacing:0;}
		/* CORE================================================================ */
		html, body {min-height: 100%; padding: 0; margin: 0; display: table; width: 100%; }
		html {font-size: 62.5%; background: #3dbfd9; }
		body {font-size: 16px; line-height: 1.5; color: white; font-family: "Raleway", arial, sans-serif; }
		.wrapper { max-width: 1000px; margin: 0 auto; }
		.page { background: #212326; }
		a:link, a:visited { color: #3dbfd9; text-decoration: none; }
		a:hover, a:active, a:focus { text-decoration: underline; }
		h1 { font-size: 56px; line-height: 1; margin: 0; }
		h2 { font-size: 30px; font-weight: normal; }
		h3 { font-size: 44px; margin: 0.25em 0; }
		h4 { font-size: 24px; font-weight: bold; margin: 1.2em 0; color: #3dbfd9;}
		.page_header { text-align: center; padding-top: 20px; }
		section { padding-bottom: 3em; }
		section header { border-top: 1px solid #3dbfd9; border-bottom: 1px solid #3dbfd9; clear: both; }
		ol { list-style: none; margin: 2em 0; padding:0;  }
		ol li { clear: both; margin: 1em 0; }
		.icon_block { float: left; width: 16%; min-width: 100px; text-align: center; margin-top: 5px; }
		.content_block { overflow: hidden; }
		.circle { margin-top: 0.4em; display: inline-block; font-size: 30px; font-weight: bold; line-height: 55px; background: #3dbfd9; border-radius: 50%; width: 60px; height: 60px; }
		.circle object { margin: 15px; float: left; }
		.bullets li { list-style: disc; margin-bottom: 0.74em; }
		.bullets li ul { padding-left: 0; }
		.bullets li li { list-style: none; margin: 0.5em 0; }
		.small { font-size: 14px; }
		.cta, .cta:link, .cta:visited { font-weight: bold; position: relative; float: right; border: 2px solid white; text-transform: uppercase; white-space: nowrap; color: #ffffff; border-radius: 8px; -webkit-border-radius: 8px; line-height: 2; font-size: 20px; padding: 0.3em 3.5em 0.3em 1em; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out; background: #ff0000; }
		.cta:hover, .cta:active, .cta:focus {text-decoration: none; background: #333333; }
		.cta object { position: absolute; right: 1em; top: 20%; }
		.writable { padding: 0; list-style: none; }
		.writable li { color: #212326; border-radius: 8px; background: #e2f5f9; padding: 0.75em 1em; }
		.writable .error { border: 2px solid red; }
		.writable .success { border: 2px solid green; }
		.footer { padding: 2em 0; position: relative; }
		.footer .mainnav {margin-top: 0.5em; }
		.footer a:link, .footer a:visited { color: white; }
		.mainnav { color: #bcffff; font-size: 14px; font-size: 1.4rem; text-align: center; }
		.mainnav ul { padding: 0; margin: 0 auto; }
		.mainnav li { display: inline-block; float: none; position: relative; }
		.mainnav a { text-transform: uppercase; margin-right: 0.8em; position: relative; }
		.mainnav a:link, .mainnav a:visited { color: #bcffff; }
		.mainnav a:hover, .mainnav a:active, .mainnav a:focus {color: #ffffff; }
		.mainnav a:after {content: '/'; display: inline-block; padding-left: 1em; left: 0; top: 0; font-size: 12px; font-size: 1.2rem; color: #bcffff; }
		.mainnav .first a {padding-left: 0; outline: none; }
		.mainnav .first a:after {content: '/'; }
		.footer .left { position: absolute; margin: 0; }
		.colophon {clear: both; text-align: center; margin-top: 3em; font-size: 12px; }
		.colophon object { margin-bottom: -8px; }
		.small object { margin-bottom: -15px; }
		.callout { color: #f00; }
	</style>
</head>
<body>
	<div class="page">
		<div class="wrapper">
			<header class="page_header">
				<div class="logo"><object type="image/svg+xml" width="160" height="145" data="<?= img_path('_template_icons.svg#fuel') ?>"></object></div>
				<h1>Welcome to Fuel CMS</h1>
				<h2>Version <?=FUEL_VERSION?></h2>
			</header>
			<section>
				<header>
					<div class="icon_block">
						<object type="image/svg+xml" width="74" height="68" data="<?= img_path('_template_icons.svg#clipboard') ?>"></object>
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
							<p>Change the Apache .htaccess found at the root of FUEL CMS's installation folder to the proper RewriteBase directory. The default is your web server's root directory (e.g "/"), but if you have FUEL CMS installed in a sub folder, you will need to add the path to line 5, e.g. <strong>RewriteBase /mysub/folders/</strong>.</p>
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
							<p>Make the following folders writable:</p>
							<ul class="writable">
								<li class="<?=(is_really_writable(APPPATH.'cache/')) ? 'success' : 'error'; ?>">
									<strong><?=APPPATH.'cache/'?></strong><br><span>(folder for holding cache files)</span>
								</li>
								<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/')) ? 'success' : 'error'; ?>">
									<strong><?=APPPATH.'cache/dwoo/'?></strong><br><span>(folder for holding Dwoo cache files)</span>
								</li>
								<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/compiled')) ? 'success' : 'error'; ?>">
									<strong><?=APPPATH.'cache/dwoo/compiled'?></strong><br><span>(for writing Dwoo compiled template files)</span>
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
								<li>In the <strong>fuel/application/config/config.php</strong>, change the <code>$config['encryption_key']</code> to your own unique key.</li></li>
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
						<div class="logo"><object type="image/svg+xml" width="74" height="68" data="<?= img_path('_template_icons.svg#signpost') ?>"></object></div>
					</div>
					<div class="content_block">
						<h3>What's Next?</h3>
					</div>
				</header>
				<ol>
					<li>
						<div class="icon_block">
							<div class="circle"><object type="image/svg+xml" width="30" height="27" data="<?= img_path('_template_icons.svg#map') ?>"></object></div>
						</div>
						<div class="content_block">
							<a class="cta" href="http://docs.getfuelcms.com" target="_blank" style="margin-top: 20px;">User Guide <object type="image/svg+xml" width="30" height="27" data="<?= img_path('_template_icons.svg#rightarrow') ?>"></object></a>
							<h4>Visit the <a href="http://docs.getfuelcms.com" target="_blank">1.0 user guide</a> online</h4>
						</div>
					</li>
					<li>
						<div class="icon_block">
							<div class="circle"><object type="image/svg+xml" width="40" height="37" data="<?= img_path('_template_icons.svg#github') ?>"></object></div>
						</div>
						<div class="content_block">
							<h4>Get rolling</h4>
							<p>FUEL CMS is open source and on GitHub for a good reason. The communities involvement is an important part of it's success.
							If you have any ideas for improvement, or even better, a <a href="https://help.github.com/articles/creating-a-pull-request" target="_blank">GitHub pull request</a>, please let us know.</p>


							<ul class="bullets">
								<li>Need help? Visit the <a href="http://forums.getfuelcms.com" target="_blank">FUEL CMS Forums</a>.</li>
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
		<p class="colophon">FUEL CMS is developed with love by <a href="http://thedaylightstudio.com" target="_blank">Daylight Studio</a> <object type="image/svg+xml" width="25" height="25" data="<?= img_path('_template_icons.svg#daylight') ?>"></object> &copy; <?php echo date("Y"); ?> Run for Daylight LLC, All Rights Reserved.</p>
	</footer>
</div>
</body>
</html>