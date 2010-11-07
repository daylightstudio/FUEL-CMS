<?php $authors = $CI->fuel_blog->get_users()?>
<?php if (!empty($authors)) : ?>
<div class="blog_block">
	<h3>Authors</h3>
	<ul>
		<?php foreach($authors as $author) : ?>
		<li>
			<?=fuel_edit($author->id, 'Edit Author: '.$author->name, 'blog/users')?>
			<a href="<?=$author->url?>"><?=$author->name?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>