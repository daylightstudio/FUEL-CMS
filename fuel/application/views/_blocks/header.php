<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
 	<title><?=fuel_var('page_title', 'FUEL CMS : A Rapid Development CodeIgniter CMS')?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en-us" />
	<meta name="ROBOTS" content="ALL" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="MSSmartTagsPreventParsing" content="true" />

	<meta name="keywords" content="<?=fuel_var('meta_keywords')?>" />
	<meta name="description" content="<?=fuel_var('meta_description')?>" />

	<?=css('main')?>
	<?=css('ie6', '', array('ie_conditional' => 'lte IE 6'))?>
	<?=css('ie7', '', array('ie_conditional' => 'lte IE 7'))?>
	
	<?=js('jquery')?>
	<?=js('jquery.inputlabel, jquery.viewer, jquery.shadow, jquery.ifixpng.js, cufon_yui, geometric_400-geometric_800.font, jquery.fancyzoom, main')?>
	<?=js($js)?>
	
	
	<script type="text/javascript" charset="utf-8">
	//<![CDATA[
		var basePath = '<?=site_url()?>';
		var imgPath = '<?=img_path()?>';
	//]]>
	</script>
</head>

<body class="<?=fuel_var('body_class', 'Body Class')?>">
<div id="container">
	<div id="container_inner">
		<div id="header">
			<div id="topnav">
				<ul>
					<li><a href="http://www.thedaylightstudio.com">About Daylight</a></li>
					<?php /* ?><li><a href="<?=site_url('features/screenshots')?>">Screenshots</a></li>
					<li><a href="<?=site_url('faq')?>">FAQs</a></li>
					<li><a href="<?=site_url('play_demo')?>">Play Demo</a></li>
					<?php */?>
					<li class="last"><?=safe_mailto('info@thedaylightstudio.com', 'Contact');?></li>
				</ul>
			</div>
			<a href="http://www.thedaylightstudio.com" id="daylight_logo"></a>
		
	
			<a href="<?=site_url()?>" id="fuel_logo">FUEL CMS</a>

			<div id="fuel_intro">
				<h1 id="fuel_cms">FUEL CMS</h1>
				<div id="fuel_tagline"></div>
				<?php if (!uri_path(FALSE) || uri_path(FALSE) == 'home') : ?>
				<div id="fuel_text">
					<p class="intro"><strong>An easy, flexible, empowering Content Management System for rapid development</strong>
						that transforms your CodeIgniter projects into client manageable brilliance. 
					</p>
					<a href="#" id="btn_screenshots" class="btn btn_big btn_double_arrow" >SCREENSHOTS <span class="double_arrow">&raquo;</span></a>
				</div>
				<?php endif; ?>
			</div>
		
		
		</div>
	
		<div id="main">