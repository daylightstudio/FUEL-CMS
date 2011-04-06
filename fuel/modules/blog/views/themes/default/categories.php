<h1>Categories</h1>
<?php foreach($categories as $category) :
		$posts = $category->posts;
		if (!empty($posts)) :
	 ?>
<h2><?=fuel_edit($category->id, 'Edit Category', 'blog/categories')?><?=anchor($category->url, $category->name)?></h2>
	<ul class="bullets">
	<?php foreach($posts as $post) : ?>
		<li><?=fuel_edit($post->post_id, 'Edit Post', 'blog/posts')?><?=anchor($this->fuel_blog->url('id/'.$post->post_id), $post->title)?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php endforeach; ?>