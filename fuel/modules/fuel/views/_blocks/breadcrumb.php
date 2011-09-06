<div id="fuel_main_top_panel">
	<h2 class="ico <?=$breadcrumb_icon?>">
	<?php if (!empty($breadcrumb)) : ?>
	<?php 
	$last_key = end(array_keys($breadcrumb));
	foreach($breadcrumb as $url => $crumb) : ?>
		<?php if ($last_key != $url) : ?>
			<a href="<?=fuel_url($url)?>"><?=$crumb?></a>
		<?php else: ?>
			<em><?=$crumb?></em>
		<?php endif; ?>
		<?php if ($last_key != $url) : ?> &gt; <?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	</h2>
	
</div>

<div class="clear"></div>