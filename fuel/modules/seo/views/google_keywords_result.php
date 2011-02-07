<?=js('SeoController', 'seo')?>
<?php if (!empty($results)){ ?>
	<h3><?=lang('seo_google_keywords_results_text', $num_results)?></h3>
	<ul class="nobullets">
	<?php foreach($results as $keyword => $found){ ?>
			<li><a href="http://www.google.com/search?q=<?=rawurlencode($keyword)?>&num=<?=$this->config->item('keyword_google_num_results', FUEL_FOLDER)?>" target="_blank"><strong class="success"><?=$keyword?></strong></a> 
				<?=lang('seo_google_keywords_ranking')?><?php if (count($found) != 1) {?>s<?php } ?>: <?=implode(', ', $found) ?>
			</li>
	<?php } ?>
	</ul>
<?php } else {?>
	<span class="warning"><?=lang('seo_google_keywords_results_not_found', $domain, $num_results, $keywords)?></span>
<?php } ?>