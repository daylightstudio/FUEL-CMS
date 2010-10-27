<div id="main_top_panel">
	<h2><a href="<?=fuel_url('manage')?>"><?=lang('h2_manage')?></a> &gt; <?=lang('h2_page_cache')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>

<div id="main_content" class="noaction">
	<div id="main_content_inner">

	<p class="instructions"><?=lang('page_cache_instructions')?></p>
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	
	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url('manage/')?>" class="ico ico_no">No, don't clear cache</a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit">Yes, clear cache</a></li>
		</ul>
	</div>
	
	<?=$this->form->hidden('action', 'cache')?>
	<?=$this->form->close()?>
	</div>
</div>