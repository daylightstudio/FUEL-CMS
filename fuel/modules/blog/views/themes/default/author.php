<?=fuel_edit($author->id, 'Edit Author: '.$author->name, 'blog/users')?>
<h1><?=$author->name?></h1>
<?php if (!empty($author->avatar_image)){ ?>
<?=$author->get_avatar_img_tag(array('class' => 'img_right'))?>
<?php } ?>
<?=$author->about_formatted?>

<ul>
	<?php if (!empty($author->email)) : ?>
	<li><?=safe_mailto($author->email)?></li>
	<?php endif; ?>
	
	<?php if (!empty($author->website)) : ?>
	<li><?=$author->website_link?></li>
	<?php endif; ?>
</ul>

<?php $posts = $author->posts; ?>
<?php if (!empty($posts)) : ?>
<h2>Posts By <?=$author->name?></h2>
<ul>
	<?php foreach($posts as $post) : ?>
	<li><a href="<?=$post->url ?>"><?=$post->title?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
