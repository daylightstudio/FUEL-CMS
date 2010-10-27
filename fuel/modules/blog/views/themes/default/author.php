<?=fuel_edit($author->id, 'Edit Author: '.$author->name, 'blog/users')?>
<h1><?=$author->name?></h1>
<?php if (!empty($author->avatar_image)){ ?>
<img src="<?=$author->avatar_image_path?>" style="float: right;" />
<?php } ?>
<?=$author->about_formatted?>

<ul>
	<li><?=safe_mailto($author->email)?></li>
	<?php if (!empty($author->website)) {?><li><a href="<?=$author->website?>"><?=$author->website?></a></li><?php } ?>
</ul>