<div id="fuel_main_top_panel">
	<h2 class="ico <?=$breadcrumb_icon?>">
	<?php if (!empty($breadcrumb)) : ?>
	<?php 
	$last_key = array_pop($breadcrumb);
	foreach($breadcrumb as $url => $crumb) : ?>
		<a href="<?=fuel_url($url)?>"><?=$crumb?></a> &gt;
	<?php endforeach; ?>
	<em><?=$last_key?></em>
	<?php endif; ?>
	</h2>
	
</div>

<div class="clear"></div>