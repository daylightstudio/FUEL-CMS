<?php $authors = $CI->fuel_blog->get_users()?>
<?php if (!empty($authors)) : ?>
<div class="blog_block">
	<h3>Authors</h3>
	<ul>
		<?php foreach($authors as $author) : ?>
		<li>
			<?=fuel_edit($author->id, 'Edit Author: '.$author->name, 'blog/users')?>
			<a href="<?=$author->url?>"><?=$author->name?></a>
			<?php if (!empty($author->posts_count)) : ?>(<?=$author->posts_count?>)<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>