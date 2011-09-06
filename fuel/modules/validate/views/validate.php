<?=js('ValidateController', 'validate')?>
<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('validate_instructions')?></p>
	<form action="<?=fuel_url('tools/validate/html')?>" method="post" id="form">
		<?=$this->form->select('pages[]', $pages_select, $this->input->post('pages'), array('multiple' => 'multiple'))?>
		<div class="clear"></div>
		
		<?=$this->form->textarea('pages_input', (!empty($default_page_input) ? $default_page_input : lang('validate_pages_input')), 'cols="5" rows="100" class="fillin"')?>
		
		<div style="text-align: center; margin: 0 0 30px 135px;" class="buttonbar">
			<ul>
				<li class="end"><a href="#" class="ico ico_validate_links" id="submit_links"><?=lang('btn_validate_links')?></a></li>
				<li class="end"><a href="#" class="ico ico_tools_validate" id="submit_html"><?=lang('btn_validate_html')?></a></li>
				<li class="end"><a href="#" class="ico ico_validate_size" id="submit_size_report"><?=lang('btn_view_size_report')?></a></li>
			</ul>
		</div>
	</form>
</div>