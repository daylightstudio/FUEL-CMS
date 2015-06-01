<?php 
/*
Example view that can be used to display a single module post

$config['modules']['articles'] = array(
	'preview_path' => 'articles/{year}/{month}/{day}/{slug}', // put in the preview path on the site e.g products/{slug}
	'model_location' => '', // put in the advanced module name here
	'pages' => array(
		'base_uri' => 'articles',
		'list' => '_posts/posts',
		'post' => '_posts/post', // <-- THIS POINTS TO THE VIEW
		// CAN ALSO BE WRITTEN LIKE THE FOLLOWING:
		'post' => array('view' => '_posts/post'), 
	)
);
*/
?>
<?php 
// add in redirect if no content and there is either a link or a PDF
if (!$post->has_content() AND ($post->has_link() OR $post->has_pdf()))
{
	redirect($post->url, 'location', 301);
}
?>
<div class="media post">
	<?=fuel_edit($post)?>
	
	<?=fuel_block(array('view' => 'posts/post_unpublished', 'vars' => array('post' => $post)))?>
	
	<h1><?=$post->title?> </h1>
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

		<?=$post->content_formatted?>

		<?php if($post->has_pdf() OR $post->has_link()) : ?>
		<p><a href="<?=$post->url?>"<?=link_target($post->url, array('pdf'))?>>View More</p>
		<?php endif; ?>

		<div class="bottom_of_post">

			<div class="post_navigation_container">

				<?php $prev = $post->prev; ?>
				<?php if (isset($prev->id)) : ?>
				<div class="post_navigation prev">
					<a href="<?=$prev->url?>" class="cta">Previous</a>
				</div>
				<?php else: ?>
				<div class="post_navigation prev">
					<a href="#" class="cta disabled">Previous</a>
				</div>
				<?php endif; ?>

				<div class="post_navigation all">
					<a href="<?=site_url('media')?>" class="cta">Back to <?=$module->model()->friendly_name()?></a>
				</div>


				<?php $next = $post->next; ?>
				<?php if (isset($next->id)) : ?>
				<div class="post_navigation next">
					<a href="<?=$next->url?>" class="cta">Next</a>
				</div>
				<?php else: ?>
				<div class="post_navigation next">
					<a href="#" class="cta disabled">Next</a>
				</div>
				<?php endif; ?>

			</div>

			<?=fuel_block(array('view' => 'posts/share', 'vars' => array('post' => $post)))?>
		</div>


	</div>

</div>