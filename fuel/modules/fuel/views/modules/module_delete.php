<div id="fuel_main_content_inner">
	<?php if (!empty($success)) : ?>

	<p class="instructions"><?=$success?></p>

	<?php else : ?>
	
	<p class="instructions"><?=lang('delete_item_message')?><br/> <span class="delete"><?=$title?></span></p>
	<?=$this->form->hidden('id', $id)?>

	<div class="buttonbar clearfix">
		<ul>
			<li class="unattached end"><a href="<?=$back_action?>" class="ico ico_no"><?=lang('btn_no_dont_delete')?></a></li>
			<li class="unattached"><a href="#" class="ico ico_yes" id="submit"><?=lang('btn_yes_dont_delete')?></a></li>
		</ul>
	</div>

	<?php endif; ?>
</div>