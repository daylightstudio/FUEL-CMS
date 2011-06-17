<div id="main_top_panel">
	<h2 class="ico ico_<?=strtolower($this->module_name)?>"><a href="<?=fuel_url($this->module_uri)?>"><?=$this->module_name?></a> &gt; <?=lang('assets_upload_action')?></h2>
</div>
<div class="clear"></div>

<div id="action">

	<?=$actions?>

</div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content">

<div id="main_content_inner">


	<p class="instructions"><?=$this->instructions?></p>

	<form method="post" action="<?=fuel_url($this->module_uri.'/'.$action.'/'.$id)?>" enctype="multipart/form-data" id="form">
	<?=$form?>
	</form>
	
</div>