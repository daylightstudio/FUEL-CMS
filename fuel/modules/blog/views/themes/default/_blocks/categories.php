<?php $posts_to_categories = $CI->fuel_blog->get_posts_to_categories(); ?>
<?php if (!empty($posts_to_categories)) : ?>
<h3>Categories</h3>
<div class="leftmenu">
	<ul>
		<?php foreach($posts_to_categories as $post_to_category) : ?>
		<li>
			<?=fuel_edit($post_to_category->category_id, 'Edit Category: '.$post_to_category->category_name, 'blog/categories')?>
			<a href="<?=$post_to_category->category_url?>"><?=$post_to_category->category_name?> (<?=$post_to_category->posts_count?>)</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>