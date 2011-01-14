<h1>Archives</h1>
<?php if (!empty($archives_by_month)) : ?>
<?php foreach($archives_by_month as $month => $archives) : 
	$month_str = date('F Y', strtotime(str_replace('/', '-', $month).'-01'));
?>

<h2><?=$month_str?></h2>
<ul>
	<?php foreach($archives as $post) : ?>
	<li><?=fuel_edit($post->id, 'Edit Post: '.$post->title, 'blog/posts')?>
		<a href="<?=$post->url?>"><?=$post->title?></a> 
		<em><?=$post->author_name?></em>
	</li>
	<?php endforeach; ?>
</ul>
<?php endforeach; ?>
<?php else: ?>
<p>There is currently nothing in the archives.</p>
<?php endif; ?>