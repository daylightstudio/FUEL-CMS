<h1>Authors</h1>

<div class="author">
<?php foreach($authors as $author) { ?>
	<?php if (!empty($author->avatar_image)) : ?>
	<?=$author->get_avatar_img_tag(array('class' => 'img_left'))?>
	<?php endif; ?>
	<?=anchor($author->url, $author->name)?>
	<div class="clear"></div>
<?php } ?>
<div class="clear"></div>
</div>
