<!DOCTYPE html>

<html lang="en-US">
<head>
	<meta charset="utf-8">

	<title>
		<?php 
			if (!empty($is_blog))
			{
				echo $CI->fuel_blog->page_title($page_title, ' : ', 'right');
			}
			else
			{
				echo fuel_var('page_title', '');
			}
		?>
	</title>

	<meta name="keywords" content="<?php echo fuel_var('meta_keywords')?>">
	<meta name="description" content="<?php echo fuel_var('meta_description')?>">

	<?php
		echo css('main').css($css);

		if (!empty($is_blog))
		{
			echo $CI->fuel_blog->header();
		}
	?>

	<?php echo jquery('1.8.3');	?>
</head>

<body class="<?php echo fuel_var('body_class', ''); ?>">
<div id="container">
	<div id="container_inner">
		<div id="header">
			<div id="topnav">
				<ul>
					<li><a href="http://www.thedaylightstudio.com">About Daylight</a></li>
					<li class="last"><?php echo mailto('info@thedaylightstudio.com', 'Contact'); ?></li>
				</ul>
			</div>
			<a href="http://www.thedaylightstudio.com" id="daylight_logo"></a>

			<a href="<?php echo site_url(); ?>" id="fuel_logo">FUEL CMS</a>

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
