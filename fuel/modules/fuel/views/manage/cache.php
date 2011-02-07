<div id="main_top_panel">
	<h2><a href="<?=fuel_url('manage')?>"><?=lang('section_manage')?></a> &gt; <?=lang('module_manage_cache')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>

<div id="main_content" class="noaction">
	<div id="main_content_inner">

	<p class="instructions"><?=lang('cache_instructions')?></p>
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	
	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url('manage/')?>" class="ico ico_no"><?=lang('cache_no_clear')?></a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit"><?=lang('cache_yes_clear')?></a></li>
		</ul>
	</div>
	
	<?=$this->form->hidden('action', 'cache')?>
	<?=$this->form->close()?>
	</div>
</div>