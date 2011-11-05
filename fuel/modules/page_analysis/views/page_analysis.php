<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('page_analysis_instructions')?></p>
	<?=site_url()?><?=$this->form->select('page', $pages_select, $this->input->post('page'))?>
	<div style="text-align: center; margin-top: 10px;" class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url('tools/page_analysis')?>" class="ico ico_tools_page_analysis submit_action" id="submit_page_analysis"><?=lang('btn_analyze')?></a></li>
		</ul>
	</div>
	
	<div class="clear"></div>
	
	<?=$report?>

	
</div>