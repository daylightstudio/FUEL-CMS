<div class="posts left">
	<?=fuel_edit('create', 'Create Post', 'blog/posts')?>
	<?php if (!empty($posts)) : ?>
		<?php foreach($posts as $post) : ?>
		<div class="post">
			<?=fuel_edit($post->id, 'Edit Post: '.$post->title, 'blog/posts')?>

			<?=blog_block('post_unpublished', array('post' => $post))?>
		
			<h2><a href="<?=$post->url?>"><?=$post->title?></a></h2> 

			<div class="post_date">
				Published <?=$post->get_date_formatted('F')?> <?=$post->get_date_formatted('d')?>, <?=$post->get_date_formatted('Y')?> 
				by <strong><span class="post_author_name"><?=$post->author_name?></span></strong>
			</div>

			<div class="post_content">
				<?=$post->excerpt_formatted?> 
			</div>
			<div class="post_meta">
				<div class="post_categories">
					Posted in: <?=$post->categories_linked ?> 
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<?php endforeach; ?>
		
		<div class="view_archives">
			<?php if (!empty($pagination)) : ?><?=$pagination?>  &nbsp;<?php endif; ?>
			Looking for older posts? <a href="<?=blog_url('archives')?>">View our Archives</a>
		</div>
		
	<?php else: ?>
	<div class="no_posts">
		<p>There are no posts available.</p>
	</div>
	<?php endif; ?> 
</div>