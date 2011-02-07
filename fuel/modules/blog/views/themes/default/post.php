<div class="post">
	<?=fuel_edit($post->id, 'Edit Post', 'blog/posts')?>
	
	<?=blog_block('post_unpublished', array('post' => $post))?>
	
	<h1><?=$post->title?> </h1>
	<div class="post_author_date">
		Posted on <span class="post_content_date"><?=$post->get_date_formatted()?></span> by <span class="post_author_name"><?=$post->author_name?></span>
	</div>
	
	<div class="post_content">
		<?=$post->content_formatted?>
	</div>
	
</div>

<a name="comments"></a>

	<?php if ($post->comments_count > 0) : ?>
		<h3>Comments</h3>
		<div class="comments">

			<?php foreach($post->comments as $comment) : ?>

				<div class="<?=($comment->is_child()) ? 'comment child' : 'comment'?>">

					<a name="comment<?=$comment->id?>"></a>
					<div class="comment_content">
						<?php if ($comment->is_by_post_author()) :?>
						<?=$comment->post->author->get_avatar_img_tag(array('class' => 'img_left'))?>
						<?php endif; ?>
						<?=$comment->content_formatted?>
					</div>


					<div class="comment_meta">
						<cite><?=$comment->author_and_link?>, <?=$comment->get_date_formatted('h:iA / M d, Y')?></cite>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

<?php if ($post->allow_comments) : ?>
	<div class="comment_form">
	<a name="comments_form"></a>

	<?php if ($post->is_within_comment_time_limit()) : ?>
		<?php if (empty($thanks)) : ?>
		<h3>Leave a Comment</h3>
		<?php else: ?>
		<?=$thanks?>
		<?php endif;
		 ?>
		<?=$comment_form?>
	<?php else: ?>
		<p>Comments have been turned off for this post.</p>
	<?php endif; ?>
	</div>

<?php else: ?>
	<p>Comments have been closed.</p>
<?php endif; ?>
