<?php echo str_replace(';', '', '<?xml version="1.0" encoding="UTF-8"?>'); ?> 
<feed xmlns="http://www.w3.org/2005/Atom">
	<title><?php echo $title; ?></title>
	<subtitle><?php echo $description; ?></subtitle>
	<link href="<?php echo $link; ?>"/>
	<link rel="alternate" type="text/html" href="<?php echo $link; ?>" />
	<link rel="self" type="application/atom+xml" href="<?php echo $this->fuel_blog->feed('atom')?>" />
	<id><?php echo $this->fuel_blog->feed('atom')?></id>
	<updated><?php echo standard_date('DATE_ATOM', strtotime($last_updated)); ?></updated> 

	<?php if ($posts){ ?> 

	<?php foreach ($posts as $post){ ?> 
	<entry>
		<title><?php echo $post->title; ?></title>
	    <link rel="alternate" type="text/html" href="<?php echo $post->url; ?>" />
		<id>tag:<?php echo $this->fuel_blog->domain() ?>,<?php echo date('Y-m-d', strtotime($post->date_added)); ?>:article/<?php echo $post->permalink; ?></id>
	
		<published><?php echo standard_date('DATE_ATOM', strtotime($post->date_added)); ?></published>
		<summary><![CDATA[<?php echo html_entity_decode(strip_tags(word_limiter($post->excerpt, 100, '...')), ENT_COMPAT, 'UTF-8'); ?>]]></summary>
		<author>
			<name><?php echo $post->author_name; ?></name>
		</author>
		<content type="html" xml:lang="<?php echo $this->fuel_blog->language(TRUE)?>" xml:base="<?php echo $link; ?>/article">
			<![CDATA[<?php echo $post->excerpt_formatted; ?>]]> 
		</content>
		<updated><?php echo standard_date('DATE_ATOM', strtotime($post->last_modified)); ?></updated>
	</entry> 
	<?php } ?>
	
	<?php } ?>

</feed>