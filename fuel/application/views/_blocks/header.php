<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<?php if (!empty($is_blog)) : ?>
	<title><?php echo $CI->fuel_blog->page_title($page_title, ' : ', 'right')?></title>
	<?php else : ?>
	<title><?php echo fuel_var('page_title', '')?></title>
	<?php endif ?>
	<meta charset="UTF-8" />
	<meta name="ROBOTS" content="ALL" />
	<meta name="MSSmartTagsPreventParsing" content="true" />

	<meta name="keywords" content="<?php echo fuel_var('meta_keywords')?>" />
	<meta name="description" content="<?php echo fuel_var('meta_description')?>" />

	<?php echo css('main'); ?>
	<?php echo css($css); ?>
	
	<?php echo js('jquery, main'); ?>
	<?php echo js($js); ?>
	
	<?php if (!empty($is_blog)) : ?>
	<?php echo $CI->fuel_blog->header()?>
	<?php endif; ?>
	<base href="<?php echo site_url()?>" />
	
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