<?=js('ValidateController', 'validate')?>
<div id="fuel_main_content_inner">
	<a href="<?=fuel_url('tools/validate')?>" id="back_to">&lt; <?=lang('validate_link_back_to_page_selection')?></a>
	<div style="float: right;" class="btn">
		<form action="<?=site_url($this->uri->uri_string())?>" method="post" id="reload_form">
			<a href="javascript:$('#reload_form').submit()" class="ico ico_refresh"><?=lang('btn_reload_all')?></a>
			<input type="hidden" name="pages_serialized" value="<?=$pages_serialized?>" />
		</form>
	</div>
	<div id="validation_status"><h2 id="validation_status_text"></h2>
		<div class="loader" style="postion: absolute;"></div>
	</div>
	<div id="validation_results" class="<?=strtolower($validation_type)?>"></div>
</div>