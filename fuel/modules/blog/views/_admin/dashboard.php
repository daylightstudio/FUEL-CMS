	<div id="dashboard_blog" class="dashboard_module">
		<h3>Recent Blog Posts</h3>
		<ul class="nobullets">
		<?php foreach($posts as $post) : ?>
		<li><a href="<?=$post->url?>" target="_blank"><?=$post->title?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>