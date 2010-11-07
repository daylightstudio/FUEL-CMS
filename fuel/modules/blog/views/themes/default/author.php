<?=fuel_edit($author->id, 'Edit Author: '.$author->name, 'blog/users')?>
<h1><?=$author->name?></h1>
<?php if (!empty($author->avatar_image)){ ?>
<img src="<?=$author->avatar_image_path?>" style="float: right;" />
<?php } ?>
<?=$author->about_formatted?>

<ul>
	<?php if (!empty($author->email)) : ?>
	<li><?=safe_mailto($author->email)?></li>
	<?php endif; ?>
	
	<?php if (!empty($author->website)) : ?>
	<li><a href="<?=$author->website?>"><?=$author->website?></a></li>
	<?php endif; ?>
</ul>

<h2>Posts By <?=$author->name?></h2>
<?php $posts = $author->posts; ?>
<?php if (!empty($posts)) : ?>
<ul>
	<?php foreach($posts as $post) : ?>
	<li><a href="<?=$post->url ?>"><?=$post->title?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
