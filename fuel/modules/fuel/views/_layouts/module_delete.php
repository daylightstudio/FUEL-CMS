<div id="main_top_panel">
	<h2 class="ico ico_<?=url_title(str_replace('/', '_', $this->module_uri),'_', TRUE)?>"><a href="<?=fuel_url($this->module_uri)?>"><?=$this->module_name?></a> &gt; <?=lang('action_delete')?></h2>
</div>
<div class="clear"></div>


<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

<div id="main_content_inner">


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

