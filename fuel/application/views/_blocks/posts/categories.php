<?php 
/*
Example block that displays a list of categories for a particular module
*/
$categories = $CI->fuel->posts->get_published_categories(); ?>
<?php if ( ! empty($categories)) : ?>
<div class="post_categories">
	<h3>Categories</h3>
	<ul>
		<?php foreach ($categories as $category) : ?>
		<li>
			<?=fuel_edit($category)?>
			<a href="<?=$CI->fuel->posts->url('category/'.$category->slug)?>"><?=$category->name?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>