<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<?php if (!empty($is_blog)) { ?>
	<title><?=$CI->fuel_blog->page_title($page_title, ' : ', 'right')?></title>
	<?php } else { ?>
	<title><?=fuel_var('page_title', '')?></title>
	<?php } ?>
	<meta charset="UTF-8" />
	<meta name="ROBOTS" content="ALL" />
	<meta name="MSSmartTagsPreventParsing" content="true" />

	<meta name="keywords" content="<?=fuel_var('meta_keywords')?>" />
	<meta name="description" content="<?=fuel_var('meta_description')?>" />

	<?php echo css('main'); ?>
	<?php /* for IE specific ... you can remove these commments?>
		<?php echo css('ie6', '', array('ie_conditional' => 'lte IE 6')); ?>
	<?php */ ?>
	<?php echo css($css); ?>
	
	<?php echo js('jquery, main'); ?>
	<?php echo js($js); ?>
	
	<?php if (!empty($is_blog)) : ?>
	<?=$CI->fuel_blog->header()?>
	<?php endif; ?>
	<base href="<?php echo site_url()?>" />
	
</head>

<body class="<?php echo fuel_var('body_class', '');?>">
<div id="container">

	<div id="header">
		<a href="<?php echo site_url(); ?>" id="logo">WigiCorp</a>

		<?php echo fuel_nav(array('container_tag_id' => 'topmenu', 'item_id_prefix' => 'topmenu_')); ?>
		
	</div>
	<div id="main">