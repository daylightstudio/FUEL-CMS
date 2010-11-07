<h1>Authors</h1>

<ul>
<?php foreach($authors as $author) { ?>
	<li>
		<?php if (!empty($author->avatar_path)) : ?>
		<img src="<?=$author->avatar_path?>" style="float: left;">
		<?php endif; ?>
		<?=anchor($author->url, $author->name)?>
		<div class="clear"></div>
	</li>
<?php } ?>
</ul>
