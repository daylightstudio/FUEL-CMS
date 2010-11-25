<?php $posts_to_categories = $CI->fuel_blog->get_posts_to_categories(); ?>
<?php if (!empty($posts_to_categories)) : ?>
<div class="blog_block">
	<h3>Categories</h3>
	<ul>
		<?php foreach($posts_to_categories as $post_to_category) : ?>
		<li>
			<?=fuel_edit($post_to_category->category_id, 'Edit Category: '.$post_to_category->category_name, 'blog/categories')?>
			<a href="<?=$post_to_category->category_url?>"><?=$post_to_category->category_name?></a> (<?=$post_to_category->posts_count?>)
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>