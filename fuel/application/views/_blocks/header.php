<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<?php if (!empty($is_blog)) : ?>
	<title><?php echo $CI->fuel->blog->page_title($page_title, ' : ', 'right')?></title>
	<?php else : ?>
	<title><?php echo fuel_var('page_title', '')?></title>
	<?php endif ?>
	<meta charset="UTF-8" />
	<meta name="ROBOTS" content="ALL" />

	<meta name="keywords" content="<?php echo fuel_var('meta_keywords')?>" />
	<meta name="description" content="<?php echo fuel_var('meta_description')?>" />

	<?php echo js('jquery'); ?>
	
	<?php echo css('main'); ?>
	<?php echo css($css); ?>
	
	<!-- Javascript in footer for performance -->

	<?php if (!empty($is_blog)) : ?>
	<?php echo $CI->fuel->blog->header()?>
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
