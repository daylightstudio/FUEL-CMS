<?php 
/*
Example view that can be used to display search results for a particular module's posts. 

$config['modules']['articles'] = array(
	'preview_path' => 'articles/{year}/{month}/{day}/{slug}', // put in the preview path on the site e.g products/{slug}
	'model_location' => '', // put in the advanced module name here
	'pages' => array(
		'base_uri' => 'articles',
		'list' => '_posts/posts',

		'search' => '_posts/search', // <-- THIS POINTS TO THE VIEW
		// CAN ALSO BE WRITTEN LIKE THE FOLLOWING:
		'search' => array('view' => '_posts/search'), 
	)
);
*/
?><h1><?=count($posts)?> Search Results</h1>
<?php if (!empty($posts)) : ?>

	<?php foreach($posts as $post) : ?>
		<h2><a href="<?=$post->url?>"><?=highlight_phrase($post->title, $q, '<span class="search_highlight">', '</span>')?></a></h2>
		<?=highlight_phrase(($post->get_excerpt_formatted(50, '', TRUE)), $q, '<span class="search_highlight">', '</span>')?>
	<?php endforeach; ?>

<?php else : ?>
	<p>There were no search results returned.</p>
<?php endif; ?>