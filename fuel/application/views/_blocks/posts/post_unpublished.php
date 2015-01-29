<?php 
/*
This can be used in your post detail and listing pages to notify a user that's logged in that a particular post is not published
*/
if (!$post->is_published()) : ?>
	<div class="post_unpublished_wrapper">
		<div class="post_unpublished">
			This post is currently not published and is only viewable to you because you are currently logged into FUEL.
		</div>
	</div>
<?php endif; ?>