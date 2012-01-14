<div id="fuel_main_top_panel">
	<h2 class="ico <?=$titlebar_icon?>">
	<?php if (!empty($titlebar)) : ?>
	<?php if (is_array($titlebar)) : ?>
	<?php $last_key = array_pop($titlebar);
		foreach($titlebar as $url => $crumb) : ?>
			<?php if (!$this->fuel->admin->is_inline()) : ?><a href="<?=fuel_url($url)?>"><?php endif; ?><?=$crumb?><?php if (!$this->fuel->admin->is_inline()) : ?></a><?php endif; ?> &gt;
		<?php endforeach; ?>
		<em><?=$last_key?></em>
		<?php else: ?>
		<em><?=$titlebar?></em>
	<?php endif; ?>
	<?php endif; ?>
	</h2>
	
</div>

<div class="clear"></div>