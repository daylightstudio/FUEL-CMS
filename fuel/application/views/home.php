<?php fuel_set_var('layout', '')?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
 	<title>FUEL CMS</title>
	<meta charset="UTF-8" />
	<meta name="ROBOTS" content="ALL" />
	<meta name="MSSmartTagsPreventParsing" content="true" />

	<meta name="keywords" content="<?=fuel_var('meta_keywords')?>" />
	<meta name="description" content="<?=fuel_var('meta_description')?>" />

	<base href="<?=site_url()?>" />
	<style type="text/css" media="screen">
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
		h1 { font-size: 30px; margin-top: 50px; text-shadow: #000 0px 1px 3px; padding-left: 160px; line-height: 20px; }
		h2 { font-size: 16px; color: #fff; color: #c8d1d9; margin-bottom: 15px; text-shadow: #000 0px 1px 3px; padding-left: 164px; line-height: 28px; }
		h3 { font-size: 16px; color: #ea5b17; margin-bottom: 5px; font-weight: normal; }
		h4 { font-size: 12px; margin-bottom: 5px; }
		a { color: #dc461c; }
		p { margin-bottom: 20px; }
		ul { margin: 0 0 20px 0; }
		ol { list-style: decimal; margin: 0 0 20px 20px; }
		li { margin-bottom: 3px; }

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
		div#container { 
			-webkit-box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.8);
			-moz-box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.8);
			width: 600px; margin: 70px auto; border: 1px solid #1e466a; padding: 20px; -moz-border-radius: 10px; -webkit-border-radius: 10px;
			background: -webkit-gradient(linear, left top, left bottom, from(rgba(42, 96, 147, 0.35)), to(#000));
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e466a', endColorstr='#000'); /* for IE */
		 }
		div#logo {  background: transparent url(http://www.getfuelcms.com/assets/images/fuel_logo_sm.png) no-repeat; width: 150px; height: 150px; float: left; }
		div#content { padding: 10px 20px; }
	</style>
</head>
<body>

	<div id="container">
		<div id="logo"></div>
		<h1>Welcome to FUEL CMS</h1>
		<h2>Inject More Into Your Web Projects</h2>
		
		<div class="clear"></div>
		<div id="content">
			<h3>Getting Started</h3>
			<p>FUEL CMS uses CodeIgniter 2.0. If you are familiar with installing CodeIgniter, then many of the following steps will seem familiar to you.</p>

			<ol>
				<li>Alter your Apache .htaccess file to the proper RewriteBase directory. The default is your web server's root directory. <strong>If you do not have mod_rewrite enabled, you will need to change the $config['index_page'] from blank to 'index.php'
				in your <strong>fuel/application/config/config.php</strong> file</strong></li>
				<li>Install the database by first creating the database in MySQL and then running either <strong>fuel/install/fuel_schema.sql</strong> OR the <strong>fuel/install/widgicorp.sql</strong> with the latter if you want the demo site to run.
					If you have installed a previous version of FUEL CMS, you can run the <strong>fuel/install/fuel_0.9.2_upgrade.sql</strong> file.
					After installing the database, change the database configuration found in <strong>fuel/application/config/database.php</strong> 
				</li>
				<li>Make the following folders writable:
					<ul>
						<li class="<?=(is_really_writable(APPPATH.'cache/')) ? 'success' : 'error'; ?>">
							<?=APPPATH.'cache/'?>
						</li>
						<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/')) ? 'success' : 'error'; ?>">
							<?=APPPATH.'cache/dwoo/'?>
						</li>
						<li class="<?=(is_really_writable(APPPATH.'cache/dwoo/compiled')) ? 'success' : 'error'; ?>">
							<?=APPPATH.'cache/dwoo/compiled'?>
						</li>
						<li class="<?=(is_really_writable(assets_server_path('', 'images'))) ? 'success' : 'error'; ?>">
							<?=WEB_ROOT.'assets/images'?>
						</li>
					</ul>
				</li>
				<?php if (!$this->config->item('admin_enabled', 'fuel')) : ?>
				<li>If you are wanting to use the FUEL admin, change the $config['admin_enabled'] configuration property to <strong>TRUE</strong> in the fuel/application/config/MY_fuel.php file</li>
				<?php endif; ?>
				<?php if ($this->config->item('fuel_mode', 'fuel') == 'views') : ?>
				<li>If you are wanting to edit pages in FUEL, change the $config['fuel_mode'] configuration property to <strong>AUTO</strong> in the fuel/application/config/MY_fuel.php file. <strong>This must be done to view the demo site.</strong></li>
				<?php endif; ?>
				<li>WARNING: Make sure the <strong>fuel/data_backup</strong> folder and <strong>fuel/crons</strong> folder is inaccessible to the web. The .htaccess file that comes with FUEL, blocks the fuel/data_backup and fuel/cron folders by default</li>
				<li>To access the FUEL admin, go to: <br />
				<a href="<?=site_url('fuel')?>"><?=site_url('fuel')?></a><br />
				uid: <strong>admin</strong> <br />
				pwd: <strong>admin</strong> <br />
				</li>
				
			</ol>

			<p class="important">That&rsquo;s it!</p>

			<h3>What's Next?</h3>
			<ul>
				<li><a href="http://www.getfuelcms.com/blog/2010/12/09/learning-fuel-cms-blog-series" target="_blank">Read our Learn FUEL CMS blog series</a></li>
				<li><a href="<?=fuel_url('tools/user_guide/general/quickstart')?>">Review the Quick Start Guide</a></li>
				<li><a href="<?=fuel_url('tools/user_guide')?>">Review the User Guide</a></li>
			</ul>
		</div>
	</div>

</body>
</html>