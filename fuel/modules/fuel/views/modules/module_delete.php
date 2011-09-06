<div id="fuel_main_content_inner">

	<p class="instructions"><?=lang('delete_item_message')?><br/> <span class="delete"><?=$title?></span></p>
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	<?=$this->form->hidden('id', $id)?>

	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url($this->module_uri.'/')?>" class="ico ico_no"><?=lang('btn_no_dont_delete')?></a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit"><?=lang('btn_yes_dont_delete')?></a></li>
		</ul>
	</div>

	<?=$this->form->close()?>

</div>