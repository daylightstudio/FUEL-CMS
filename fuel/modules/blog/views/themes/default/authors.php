<h1>Authors</h1>

<ul>
<?php foreach($authors as $author) { ?>
	<li><img src="<?=$author->avatar_path?>" style="float: left;">
		<?=anchor($author->url, $author->name)?>
		<div class="clear"></div>
	</li>
<?php } ?>
</ul>
