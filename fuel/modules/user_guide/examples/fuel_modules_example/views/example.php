<?php 
$category = $CI->uri->segment(2);
if (!empty($category))
{
	$CI->load->model('categories_model');
	
	// remember, all categories that have a published value of 'no' will automatically be excluded
	$category = $CI->categories_model->find_one_by_name($category);
	$articles = $category->articles;
}
else
{
	$CI->load->model('articles_model');

	// remember, all articles that have a published value of 'no' will automatically be excluded
	$articles = $CI->articles_model->find_all();
}
?>

<h1>Articles <?php if (!empty($category)) : ?> : <?=$category->name?> <?php endif; ?></h1>
<?php foreach($articles as $article) : ?>

<h2><?=fuel_edit($article->id, 'Edit: '.$article->title, 'articles')?><?=$article->title?></h2>
<p>
<?=$article->content_formatted?>
</p>
<div class="author"><?=$article->author->name?></div>
<?php endforeach; ?>