<div id="fuel_main_content_inner">
	
	<div class="boxbuttons">

	<ul>
	<?php foreach($modules as $key => $module) : ?>
		<?php if ($this->fuel->auth->has_permission($key)) : ?>
		<li><a href="<?=$module->fuel_url()?>" class="ico <?=$module->icon()?>"><?=$module->friendly_name()?></a></li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
	</div>
	
	<div class="clear"></div>
	
	
</div>
