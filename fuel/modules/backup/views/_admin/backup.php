<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('data_backup_instructions')?>
	
	<?php if ($is_writable) : ?>
		<?=lang('data_backup_instructions_writable')?><br />
			<strong><em><?=$download_path?></em></strong></p>
	<?php else: ?>
		<?=lang('data_backup_instructions_not_writable')?><br />
			<strong><em><span class="error"><?=$download_path?></span></em>  <?=lang('data_backup_not_writable')?></strong></p>
	<?php endif; ?>
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	
	<div style="padding: 10px 0;"><?=$this->form->checkbox('include_assets', '1')?> <label for="include_assets"><?=lang('data_backup_include_assets')?></label></div>
	
	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url('recent')?>" class="ico ico_no"><?=lang('data_backup_no_backup')?></a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit"><?=lang('data_backup_yes_backup')?></a></li>
		</ul>
	</div>
	<?=$this->form->hidden('action', 'backup')?>
	<?=$this->form->close()?>
</div>