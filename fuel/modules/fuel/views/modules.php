<div id="main_top_panel">
	<h2 class="ico"><?=lang('section_my_modules')?></h2>
</div>
<div class="clear"></div>

<div id="main_content" class="noaction">

<div id="main_content_inner">
	
	<div class="boxbuttons">

	<ul>
	<?php foreach($nav['modules'] as $key => $val) : ?>
		<?php if ($this->fuel_auth->has_permission('Manage '.$key)) : ?>
		<li ><a href="<?=fuel_url($key)?>" class="ico"><?=$val?></a></li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
	</div>
	
	<div class="clear"></div>
	
	
</div>
