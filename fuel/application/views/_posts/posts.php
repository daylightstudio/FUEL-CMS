<?php 
/*
Example view that can be used to display module archives. 

$config['modules']['articles'] = array(
	'preview_path' => 'articles/{year}/{month}/{day}/{slug}', // put in the preview path on the site e.g products/{slug}
	'model_location' => '', // put in the advanced module name here
	'pages' => array(
		'base_uri' => 'articles',
		'list' => '_posts/posts', // <-- THIS POINTS TO THE VIEW
		// CAN ALSO BE WRITTEN LIKE THE FOLLOWING:
		'list' => array('view' => '_posts/posts'), 
	)
);
*/
?>
<div class="posts left">

	<?=fuel_edit('create', 'Create Post', $module->info('module_uri'))?>
	
	<?php if (!empty($posts)) : ?>
		<?php foreach($posts as $post) : ?>

		

		<div class="post_excerpt_image">

			<?php if ($post->has_image()) : ?>
			<p><a href="<?=$post->url?>"><img src="<?=$post->image_path?>" alt="<?=$post->title_entities?>" /></a></p>
			<?php endif; ?>
			
		</div>

		
		<div class="post_excerpt">
			

			<?=fuel_block(array('view' => 'posts/post_unpublished', 'vars' => array('post' => $post)))?>
		
			<h2><?=fuel_edit($post)?>
				<a href="<?=$post->url?>"><?=$post->title?></a>
			</h2> 

			<div class="featured_meta">
				<span class="date_span"><?=$post->publish_date_formatted('n/j/y')?></span>
				<?php $tags_linked = $post->tags_linked; ?>
				<span>
				<?php if (!empty($tags_linked)) : ?>
				<?=$tags_linked?>
				<?php endif; ?>
				</span>
			</div>

			<div class="post_content">
				<?=$post->excerpt_formatted?> 
			</div>
			<p><a class="cta" href="<?=$post->url?>"<?=link_target($post->url, array('pdf'))?>>Read More</a></p>

		</div>
		<div class="clear"></div>
		<?php endforeach; ?>
		
		<?php if (!empty($pagination)) : ?>
		<div class="pagination"><?=$pagination?></div>
		<?php endif; ?>

	<?php else: ?>
	<div class="no_posts">
		<p>There are no posts available.</p>
	</div>
	<?php endif; ?> 
</div>


