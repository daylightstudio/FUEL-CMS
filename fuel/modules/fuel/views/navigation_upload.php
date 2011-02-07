<div id="main_top_panel">
	<h2 class="ico ico_navigation"><a href="<?=fuel_url($this->module_uri)?>"><?=$this->module_name?></a> &gt; <?=lang('navigation_import')?></h2>
</div>
<div class="clear"></div>


<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

<div id="main_content_inner">

	<?=$this->form->open(array('id' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'))?>
	
	<p class="instructions"><?=lang('navigation_import_instructions')?></p>
	<?=$form?>

	<br />
	<br />

	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url($this->module_uri.'/')?>" class="ico ico_no"><?=lang('btn_no_upload')?></a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit"><?=lang('btn_yes_upload')?></a></li>
		</ul>
	</div>

	<?=$this->form->close()?>
	
	
</div>

