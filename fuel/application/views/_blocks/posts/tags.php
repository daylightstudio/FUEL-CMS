<?php 
/*
Example block that displays a list of tags for a particular module
*/
$tags = $CI->fuel->posts->get_published_tags(); ?>
<?php if ( ! empty($tags)) : ?>
<div class="post_tags">
	<h3>Tags</h3>
	<ul>
		<?php foreach ($tags as $tag) : ?>
		<li>
			<?=fuel_edit($tag)?>
			<a href="<?=$CI->fuel->posts->url('tag/'.$tag->slug)?>"><?=$tag->name?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>