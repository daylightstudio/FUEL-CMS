<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
 	<title><?php echo fuel_var('page_title', 'FUEL CMS : A Rapid Development CodeIgniter CMS');?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en-us" />
	<meta name="ROBOTS" content="ALL" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="MSSmartTagsPreventParsing" content="true" />

	<meta name="keywords" content="<?php echo fuel_var('meta_keywords'); ?>" />
	<meta name="description" content="<?php echo fuel_var('meta_description'); ?>" />

	<?php echo css('main'); ?>
	
	<?php echo js('jquery, main'); ?>
	<?php echo js($js); ?>
	
	
	<script type="text/javascript" charset="utf-8">
	//<![CDATA[
		var basePath = '<?php echo site_url();?>';
		var imgPath = '<?php echo img_path(); ?>';
	//]]>
	</script>
</head>

<body class="<?php echo fuel_var('body_class', 'Body Class');?>">
<div id="container">
	<div id="container_inner">
		<div id="header">
			<div id="topnav">
				<ul>
					<li><a href="http://www.thedaylightstudio.com">About Daylight</a></li>
					<li class="last"><?php echo mailto('info@thedaylightstudio.com', 'Contact');?></li>
				</ul>
			</div>
			<a href="http://www.thedaylightstudio.com" id="daylight_logo"></a>
		
	
			<a href="<?php echo site_url()?>" id="fuel_logo">FUEL CMS</a>

			<div id="fuel_intro">
				<h1 id="fuel_cms">FUEL CMS</h1>
				<div id="fuel_tagline"></div>
				<div id="fuel_text">
					<p class="intro"><strong>An easy, flexible, empowering Content Management System for rapid development</strong>
						that transforms your CodeIgniter projects into client manageable brilliance. 
					</p>
				</div>
			</div>
		
		
		</div>
	
		<div id="main">