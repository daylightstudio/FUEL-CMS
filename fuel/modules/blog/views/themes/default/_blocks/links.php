<?php $links = $CI->fuel_blog->get_links()?>
<?php if (!empty($links)) : ?>
<div class="blog_block">
	<h3>Links</h3>
	<ul>
		<?php foreach($links as $link) : ?>
		<li>
			<?=fuel_edit($link->id, 'Edit Link: '.$link->name, 'blog/links')?>
			<?=$link->link?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>