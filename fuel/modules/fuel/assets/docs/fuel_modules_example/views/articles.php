<?php 
$slug = uri_segment(2);

if ($slug) :

	$article = fuel_model('articles', array('find' => 'one', 'where' => array('slug' => $slug)));

	if (empty($article)) :
		redirect_404();
	endif;

else:

	$tags = fuel_model('tags');

endif;

if (!empty($article)) : ?>
	
	<h1><?=fuel_edit($article)?><?=$article->title?></h1>
	<div class="author"><?=$article->author->name?></div>
	<img src="<?=$article->image_path?>" alt="<?=$article->title_entities?>" class="img_right" />
	<article><?=$article->content_formatted?></article>


<?php else: ?>

	<h1>Articles</h1>
	<?=fuel_edit('create?title=xxx', 'Create Article', 'articles')?>
	<?php foreach($tags as $tag) : ?>
	<h2><?=$tag->name?></h2>
	<ul>
		<?php foreach($tag->articles as $article) : ?>
		<li><?=fuel_edit($article)?><a href="<?=$article->url?>"><?=$article->title?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php endforeach; ?>

<?php endif; ?>