<div id="fuel_main_content_inner">


	<p class="instructions"><?=$this->instructions?></p>

	<form method="post" action="<?=fuel_url($this->module_uri.'/'.$action.'/'.$id)?>" enctype="multipart/form-data" id="form">
	<?=$form?>
	</form>
	
</div>