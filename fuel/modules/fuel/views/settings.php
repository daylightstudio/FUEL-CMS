<div id="fuel_main_content_inner">
<?php if (empty($settings)) : ?>
	<p class="instructions"><?=lang('settings_none')?></p>
<?php else : ?>
	<p class="instructions"><?=lang('settings_manage')?><p>
	
	<div class="boxbuttons">
		<ul>
			<?php foreach ($settings as $key => $module) : ?>
			<li><a href="<?=site_url("fuel/settings/manage/{$module->folder()}")?>">
				<i class="ico <?=$module->icon()?>"></i>
				<?=$module->friendly_name()?>
			</a></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
</div>