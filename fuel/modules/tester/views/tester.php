<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('tester_instructions')?></p>

	<form action="<?=fuel_url('tools/tester/run')?>" method="post" id="form">
		<?=$this->form->select('tests[]', $test_list, $this->input->post('tests'), array('multiple' => 'multiple'))?>
		<div class="clear"></div>
		
		<div style="text-align: center; margin: 0 0 30px 280px;" class="buttonbar">
			<ul>
				<li class="end"><a href="#" class="ico ico_tools_tester" id="run_tests"><?=lang('btn_run_tests')?></a></li>
			</ul>
		</div>
	</form>
	<?=js('')?>
	<script type="text/javascript">
	//<![CDATA[
		$(function(){
			$('#tests').supercomboselect();
			$('#run_tests').click(function(){
				$('.csadd').click();
				$('#form').submit();
				return false;
			});
		})
	//]]>
	</script>
</div>