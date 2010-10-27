<h1>Categories</h1>
<?php foreach($categories as $category) :
		$posts = $category->posts;
		if (!empty($posts)) :
	 ?>
<h2><?=anchor($category->url, $category->name)?></h2>
	<ul class="bullets">
	<?php foreach($posts as $post) : ?>
		<li><?=anchor($post->url, $post->title)?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php endforeach; ?>