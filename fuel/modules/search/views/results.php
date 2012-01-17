<style type="text/css" media="screen">
	.search_highlight { background-color: #00ff00; }
</style>
<?php foreach($results as $result): ?>
<h4><?=highlight_phrase($result->title, $q, '<span class="search_highlight">', '</span>')?></h4>
<p><?=highlight_phrase($result->content_excerpt, $q, '<span class="search_highlight">', '</span>')?></p>
<?php endforeach; ?>