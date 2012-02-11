<?php fuel_set_var('layout', 'none'); ?>

<div class="sidebar_layout clearfix">
	<header>
		<h1>Search Results</h1>
		<p><?=$count?> <?=pluralize($count, 'Result')?> for &ldquo;<?=$q?>&rdquo;</p>
	</header>

	<article id="main">
		<ul id="search_results" class="row">
		
		<?php if (!empty($results)) : ?>
		
			<?php foreach($results as $result): ?>
			<li>
				<h3><a href="<?=$result->url?>"><?=highlight_phrase($result->title, $q, '<span class="search_highlight">', '</span>')?></a></h3>
				<p><?=highlight_phrase($result->content_excerpt, $q, '<span class="search_highlight">', '</span>')?></p>
				<a href="<?=$result->url?>" class="page_link"><?=$result->url?></a>
			</li>
			<?php endforeach; ?>
		
		<?php else : ?>

		<li>
			<p>No search results found.</p>
		</li>

		<?php endif; ?>


		<ul id="pagination" class="row">
			<li class="next_prev disabled">Prev</li>
			<li class="active"><a href="">1</a></li>
			<li><a href="">2</a></li>
			<li><a href="">3</a></li>
			<li><a href="">4</a></li>
			<li><a href="">5</a></li>
			<li class="next_prev"><a href="">Next</a></li>
		</ul>
	</article>
	
	<aside id="sidebar">
		<?=$this->form->open(array('action' => site_url('search'), 'method' => 'get'))?>
		<div class="searchbox">
			<h3>Search again</h3>
			<input type="text" name="q" placeholder="Type here..." />
			<input type="submit" class="ir" />
		</div>
		<?=$this->form->close()?>
	</aside>
</div>
	

