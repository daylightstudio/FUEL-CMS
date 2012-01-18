<style type="text/css" media="screen">
	.search_highlight { background-color: #00ff00; }
</style>
<?php if (!empty($results)) : ?>
	<?php foreach($results as $result): ?>
	<h4><a href="<?=$result->url?>"><?=highlight_phrase($result->title, $q, '<span class="search_highlight">', '</span>')?></a></h4>
	<p><?=highlight_phrase($result->content_excerpt, $q, '<span class="search_highlight">', '</span>')?></p>
	<?php endforeach; ?>
<?php else : ?>

<p>No Data available.</p>
<?php endif; ?>