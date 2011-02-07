<div>
	<?php if (!empty($description)) {?><p><?=$description?></p><?php } ?>
	<?=$this->form->open(array('method' => 'post'))?>
	<a name="inline_errors"></a>
	<div class="notification inline_errors"></div>
	
		<?=$form?>
		<?=$this->form->hidden('var_id', $var_id, 'id=""')?>
		<?=$this->form->hidden('model', 'fuel/pagevariable', 'id=""')?>
	<div class="buttonbar save_cancel">
		<ul>
			<li class="start end"><a href="#" class="ico ico_cancel modal_cancel"><?=lang('btn_cancel')?></a></li>
			<li class="start end"><a href="#" class="ico ico_save"><?=lang('btn_save')?></a></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<?=$this->form->close()?>
</div>