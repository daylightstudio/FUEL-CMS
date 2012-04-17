<div id="fuel_main_content_inner">

<p class="instructions"><?=lang('cache_instructions')?></p>
<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>

<div class="buttonbar">
	<ul>
		<li class="unattached"><a href="<?=fuel_url('manage/')?>" class="ico ico_no"><?=lang('cache_no_clear')?></a></li>
		<li class="unattached"><a href="#" class="ico ico_yes" id="submit"><?=lang('cache_yes_clear')?></a></li>
	</ul>
</div>

<?=$this->form->hidden('action', 'cache')?>
<?=$this->form->close()?>