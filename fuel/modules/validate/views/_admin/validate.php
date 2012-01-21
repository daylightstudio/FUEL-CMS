<?=js('ValidateController', 'validate')?>
<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('validate_instructions')?></p>

	<?=$form?>
	
	<div style="text-align: center; margin: 0 0 30px 135px;" class="buttonbar">
		<ul>
			<li class="end"><a href="#" class="ico ico_validate_links" id="submit_links"><?=lang('btn_validate_links')?></a></li>
			<li class="end"><a href="#" class="ico ico_tools_validate" id="submit_html"><?=lang('btn_validate_html')?></a></li>
			<li class="end"><a href="#" class="ico ico_validate_size" id="submit_size_report"><?=lang('btn_view_size_report')?></a></li>
		</ul>
	</div>
</div>