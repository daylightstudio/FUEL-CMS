<?php 
function svg_icon($id, $width, $height, $viewbox = "0 0 126.962 115.395")
{
    return '<svg width="' . $width . 'px" height="' . $height . 'px" viewBox="'.$viewbox.'" preserveAspectRatio="xMidYMid"><use xlink:href="#'.$id.'"></use></svg>';
}
?>
<?php fuel_set_var('layout', '')?>

<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
 	<title>Welcome to FUEL CMS</title>
	<link href='http://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'>
	<?php echo css('main')?>
	<?=jquery()?>
</head>
<body>

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		 width="126.962px" height="115.395px" viewBox="0 0 126.962 115.395" enable-background="new 0 0 126.962 115.395"
		 xml:space="preserve">

		<g id="fuel" class="icon">
				<path fill="#3DBFD9" d="M97.607,46.492C96.816,66.475,80.357,82.5,60.183,82.5c-17.978,0-33.03-12.719-36.659-29.626l27.841-4.995l-6.153,17.314
			l76.037-35.357c0,0-7.743,1.839-17.449,4.145C98.871,14.481,81.194,0,60.183,0c-24.682,0-44.779,19.977-44.99,44.61
			c2.518-1.054,5.084-2.13,7.672-3.214C24.686,22.404,40.722,7.5,60.183,7.5c17.472,0,32.163,12.02,36.307,28.218
			c-14.32,3.403-29.729,7.066-30.79,7.327c-2.342,0.575,7.665-19.018,7.665-19.018S1.171,57.061,0,57.094l16.141-2.896
			C20.401,74.613,38.522,90,60.183,90c24.812,0,45-20.186,45-45c0-0.586-0.021-1.168-0.045-1.75L97.607,46.492z"/>
		</g>

		<g id="fuelwhite" class="icon">
			<path fill="#FFFFFF" d="M124.351,42.451c0,0-9.949,2.053-21.453,4.432c-3.959-18.029-20.016-31.526-39.233-31.526
				c-22.19,0-40.179,17.988-40.179,40.178c0,0.705,0.019,1.407,0.055,2.104c-11.617,4.843-20.635,8.549-21.04,8.549l21.677-3.251
				c3.476,18.652,19.826,32.777,39.487,32.777c22.188,0,40.177-17.989,40.177-40.179c0-1.406-0.073-2.794-0.214-4.163L124.351,42.451z
				 M63.664,21.962c16.029,0,29.426,11.236,32.764,26.26c-13.383,2.771-26.779,5.552-27.925,5.822c-2.345,0.55,8.2-18.769,8.2-18.769
				s-25.492,10.79-46.597,19.621C30.449,36.649,45.336,21.962,63.664,21.962z M97.237,55.536C97.236,74.078,82.205,89.11,63.664,89.11
				c-16.347,0-29.956-11.683-32.954-27.153l23.339-3.5l-6.638,17.116l49.79-21.434C97.221,54.603,97.237,55.067,97.237,55.536z"/>
		</g>
		<g id="daylight" class="icon">
			<path fill="#FFFFFF" d="M21.47,56.842c0-23.765,19.272-43.038,43.046-43.038c23.772,0,43.037,19.273,43.037,43.038
				c0,17.501-10.447,32.521-25.429,39.25c-3.905-5.512-10.338-9.108-17.608-9.108c-7.273,0-13.693,3.603-17.598,9.115
				C31.928,89.369,21.47,74.349,21.47,56.842z M41.711,34.049C51.895,23.868,65.142,19.3,76.629,20.807
				c-3.812-1.279-7.872-1.995-12.112-1.995c-21.01,0-38.044,17.028-38.044,38.03c0,4.235,0.718,8.297,1.995,12.095
				C26.975,57.459,31.534,44.23,41.711,34.049z"/>
			<path fill="#FFFFFF" d="M118.547,56.842c0-29.797-24.238-54.041-54.031-54.041
				c-29.798,0-54.043,24.244-54.043,54.041c0,29.803,24.244,54.049,54.043,54.049C94.31,110.891,118.547,86.645,118.547,56.842
				L118.547,56.842z M114.201,56.842c0,27.406-22.289,49.702-49.685,49.702c-27.401,0-49.697-22.296-49.697-49.702
				c0-27.403,22.295-49.695,49.697-49.695C91.912,7.147,114.201,29.439,114.201,56.842L114.201,56.842z"/>
		</g>
		<g id="rightarrow" class="icon">
			<path fill="#FFFFFF" d="M63.498,110.932c-29.777,0-54.01-24.232-54.01-54.01s24.232-54.01,54.01-54.01s54.01,24.232,54.01,54.01
				S93.275,110.932,63.498,110.932z M59.641,85.856l34.72-28.933l-34.72-28.933v21.218H36.492v15.431h23.148V85.856z"/>
		</g>
		<g id="map" class="icon">
			<path fill="#FFFFFF" d="M10.025,3.943l34.595,15.376v92.254L10.025,93.915V3.943z M79.217,97.759l-30.751,12.252V17.757
				L79.217,5.505V97.759z M83.06,96.198V3.943l34.595,15.376v92.254L83.06,96.198z"/>
		</g>
		<g id="signpost" class="icon">
			<path fill="#3DBFD9" d="M25.511,38.797L12,25.286l13.511-13.51h36.671v-7.72h7.72v7.72h34.741v27.021H69.902v7.72h-7.72v-7.72
				H25.511z M120.084,63.887l-13.511,13.511H69.902v34.741h-7.72V77.398H23.58V50.377h82.993L120.084,63.887z"/>
		</g>
		<g id="clipboard" class="icon">
			<path fill="#3DBFD9" d="M81.86,32.222v77.193H12.019V32.222h10.683c-0.918,2.297-1.493,4.824-1.493,7.352v3.676H72.67v-3.676
				c0-2.527-0.574-5.054-1.493-7.352H81.86z M68.995,39.574h-44.11c0-5.628,3.101-10.798,7.926-14.129
				c-0.345-1.378-0.574-2.757-0.574-4.25c0-8.156,6.548-14.703,14.703-14.703c8.155,0,14.703,6.548,14.703,14.703
				c0,1.493-0.23,2.872-0.575,4.25C65.893,28.776,68.995,33.945,68.995,39.574z M54.292,21.195c0-4.021-3.332-7.352-7.352-7.352
				c-4.021,0-7.352,3.331-7.352,7.352s3.331,7.352,7.352,7.352C50.96,28.546,54.292,25.215,54.292,21.195z M114.943,13.843v7.352
				H89.212v-7.352c0-4.71,5.743-7.352,12.865-7.352C109.2,6.491,114.943,9.133,114.943,13.843z M114.943,24.87v58.814H89.212V24.87
				H114.943z M114.943,87.36l-12.866,22.055L89.212,87.36H114.943z"/>
		</g>
		<g id="github" class="icon" fill="#FFFFFF">
			<path d="M45.2456548,0.435054373 C20.5116217,0.435054373 0.455416507,20.4867899 0.455416507,45.2252926 C0.455416507,65.0138103 13.2890239,81.802638 31.0850322,87.7250391 C33.3238737,88.1397314 34.145312,86.7536164 34.145312,85.570626 C34.145312,84.5028556 34.1040911,80.9737502 34.0847222,77.2310906 C21.6245862,79.941241 18.9948911,71.9468686 18.9948911,71.9468686 C16.9576844,66.7694235 14.0220607,65.3927446 14.0220607,65.3927446 C9.95857338,62.6125684 14.328486,62.670675 14.328486,62.670675 C18.826531,62.9865364 21.1944982,67.2864231 21.1944982,67.2864231 C25.1894495,74.1335631 31.6725543,72.1539664 34.2287471,71.0097138 C34.6305267,68.1153109 35.791665,66.138694 37.0724929,65.0197699 C27.1243489,63.8879333 16.6661583,60.0469395 16.6661583,42.8846405 C16.6661583,37.9957418 18.4153152,33.9993006 21.2809131,30.8625387 C20.8160605,29.7346751 19.2829408,25.1790201 21.7149742,19.0092936 C21.7149742,19.0092936 25.4774992,17.8064378 34.0355551,23.6012031 C37.607868,22.6089215 41.4394256,22.1112908 45.246648,22.0934119 C49.0528772,22.1112908 52.8879113,22.6089215 56.4671771,23.6012031 C65.0157968,17.8064378 68.7718656,19.0092936 68.7718656,19.0092936 C71.2098586,25.1785235 69.6757457,29.7346751 69.2113897,30.8625387 C72.0829472,33.9993006 73.8201849,37.9957418 73.8201849,42.8846405 C73.8201849,60.0886571 63.3416321,63.876014 53.3681596,64.9845087 C54.9747817,66.3750934 56.4065873,69.1006396 56.4065873,73.2788501 C56.4065873,79.2712771 56.354937,84.0951163 56.354937,85.570626 C56.354937,86.7625558 57.1604829,88.1591002 59.4311092,87.7190795 C77.2171848,81.7902221 90.0353964,65.007354 90.0353964,45.2252926 C90.0353964,20.4867899 69.9816743,0.435054373 45.2456548,0.435054373"></path>
			<path d="M17.4205505,64.7421496 C17.321223,64.9651398 16.9715903,65.0316892 16.6527491,64.8792216 C16.3269549,64.7327135 16.1451856,64.4297647 16.2509694,64.2067745 C16.347317,63.977328 16.6969498,63.9137584 17.0222473,64.0667228 C17.3465515,64.2132308 17.5322939,64.5196561 17.4205505,64.7421496"></path>
			<path d="M19.234767,66.7664437 C19.0212129,66.9641054 18.6025475,66.8722275 18.3199608,66.5598426 C18.0259515,66.246961 17.9723147,65.8287923 18.1888486,65.6286474 C18.4088589,65.4314824 18.8141151,65.5238569 19.1066345,65.8357452 C19.4006439,66.15111 19.4582538,66.5658022 19.234767,66.7664437"></path>
			<path d="M21.000313,69.3459784 C20.7266657,69.5371838 20.2767122,69.3578977 19.9990919,68.9595945 C19.7249481,68.5612913 19.7249481,68.0830295 20.0055482,67.8923207 C20.2831685,67.7011153 20.7261691,67.8739452 21.0072659,68.2682753 C21.2814097,68.6725381 21.2814097,69.1503033 21.000313,69.3459784"></path>
			<path d="M23.4194338,71.8376083 C23.1745915,72.1087724 22.6516323,72.0357667 22.2687249,71.6662684 C21.8773746,71.305213 21.7681144,70.7921866 22.0134533,70.5215192 C22.2627653,70.2503552 22.7887043,70.3263407 23.1745915,70.6933557 C23.562962,71.0544111 23.6816583,71.5699208 23.4194338,71.8376083"></path>
			<path d="M26.7568372,73.2848098 C26.6490669,73.6354358 26.1454765,73.7938631 25.6379131,73.6458651 C25.1318396,73.4919076 24.7995892,73.081685 24.9023931,72.7270859 C25.0081769,72.3734801 25.5132571,72.2076032 26.0247936,72.3675205 C26.5303705,72.5199881 26.862621,72.9282241 26.7568372,73.2848098"></path>
			<path d="M30.4235112,73.5534906 C30.4354305,73.9224922 30.0053425,74.2289175 29.4729472,74.2358704 C28.9370754,74.2477897 28.503511,73.948814 28.4975513,73.5847787 C28.4975513,73.2118041 28.9186998,72.9093519 29.4535783,72.8999158 C29.9859736,72.8889897 30.4235112,73.1864755 30.4235112,73.5534906"></path>
			<path d="M33.8334237,72.9729215 C33.8979865,73.3334802 33.5279917,73.7034751 32.9985762,73.8018093 C32.4790935,73.8971637 31.9973552,73.6741735 31.931799,73.3170912 C31.8672362,72.9475929 32.242694,72.5775981 32.7631701,72.4817471 C33.2925855,72.3898691 33.7663776,72.606403 33.8334237,72.9729215"></path>
		</g>
	</svg>


	<div class="page">
		<div class="wrapper">
			<header class="page_header">
				<div class="logo"><?=svg_icon('fuel', 140, 165)?></div>
				
				<h1>Welcome to Fuel CMS</h1>
				<h2>Version <?=FUEL_VERSION?></h2>
			</header>
			<section>
				<header>
					<div class="icon_block">
						<?=svg_icon('clipboard', 70, 68)?>
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
								!$this->config->item('admin_enabled', 'fuel') OR
								!$this->config->item('sess_save_path')

					) : ?>
					<li>
						<div class="icon_block">
							<div class="circle">4</div>
						</div>
						<div class="content_block">
							<h4>Make configuration changes</h4>
							<ul class="writable">
								<li>In the <strong>fuel/application/config/config.php</strong> file, change the <code>$config['base_url']</code> configuration property from the <code>BASE_URL</code> constant to your site's base URL with trailing slash (e.g. https://mysite.test/).</li>
								<?php if ($this->config->item('encryption_key') == '') : ?>
								<li>In the <strong>fuel/application/config/config.php</strong>, change the <code>$config['encryption_key']</code> to your own unique key.</li>
								<?php endif; ?>
								<?php if (!$this->config->item('admin_enabled', 'fuel')) : ?>
								<li>In the <strong>fuel/application/config/MY_fuel.php</strong> file, change the <code>$config['admin_enabled']</code> configuration property to <code>TRUE</code>. If you do not want the CMS accessible, leave it as <strong>FALSE</strong>.</li>
								<?php endif; ?>
								<?php if ($this->config->item('fuel_mode', 'fuel') == 'views') : ?>
								<li>In the <strong>fuel/application/config/MY_fuel.php</strong> file, change the <code>$config['fuel_mode']</code> configuration property to <code>auto</code>. This must be done only if you want to view pages created in the CMS.</li>
								<?php endif; ?>
								<?php if (!$this->config->item('sess_save_path')) : ?>
								<li>In the <strong>fuel/application/config/config.php</strong> file, change the <code>$config['sess_save_path']</code> configuration property to a writable folder above the web root to save session files OR leave it set to <strong>NULL</strong> to use the default PHP setting.</li>
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
						<div class="logo">
							<?=svg_icon('signpost', 74, 68)?>
						</div>
					</div>
					<div class="content_block">
						<h3>What's Next?</h3>
					</div>
				</header>
				<ol>
					<li>
						<div class="icon_block">
							<div class="circle">
								<?=svg_icon('map', 30, 27)?>
							</div>
						</div>
						<div class="content_block">
							<a class="cta" href="http://docs.getfuelcms.com" target="_blank" style="margin-top: 20px;">User Guide 
								<?=svg_icon('rightarrow', 30, 27)?>
							</a>
							<h4>Visit the <a href="http://docs.getfuelcms.com" target="_blank">1.0 user guide</a> online</h4>
						</div>
					</li>
					<li>
						<div class="icon_block">
							<div class="circle">
								<?=svg_icon('github', 40, 37)?>
							</div>
						</div>
						<div class="content_block">
							<h4>Get rolling</h4>
							<p>FUEL CMS is open source and on GitHub for a good reason. The communities involvement is an important part of its success.
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
		<p class="colophon">FUEL CMS is developed with love by <a href="http://thedaylightstudio.com" target="_blank">Daylight Studio</a> <?=svg_icon('daylight', 24, 24)?> &copy; <?php echo date("Y"); ?> Daylight Studio LLC, All Rights Reserved.</p>
	</footer>
</div>
</body>
</html>