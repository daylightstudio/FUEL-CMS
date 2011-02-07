<div id="main_top_panel">
	<h2 class="ico ico_tools_tester"><a href="<?=fuel_url('tools')?>"><?=lang('section_tools')?></a> &gt; 
	<a href="<?=fuel_url('tools/tester')?>"><?=lang('module_tester')?></a> &gt;
	<?=lang('tester_results')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
	<div id="tester_results">
		
			<div style="float: right;" class="btn">
				<form action="<?=site_url($this->uri->uri_string())?>" method="post" id="reload_form">
					<a href="javascript:$('#reload_form').submit()" class="ico ico_refresh"><?=lang('tester_reload_all')?></a>
					<input type="hidden" name="tests_serialized" value="<?=$tests_serialized?>" />
				</form>
			</div>
			
			<a href="<?=fuel_url('tools/tester')?>" class="back"><?=lang('tester_back')?></a>

			<h2><?=lang('tester_accumulative')?> - <?=lang('ut_passed')?>: <span class="success"><?=$total_passed?></span> <?=lang('ut_failed')?>: <span class="error"><?=$total_failed?></span></h2>
			<?php foreach($results as $key => $result): ?>
			<h3><?=ucfirst($key)?><?php if (count($results) > 1) : ?> - <?=lang('ut_passed')?>: <span class="success"><?=$result['passed']?></span> <?=lang('ut_failed')?>: <span class="error"><?=$result['failed']?></span><?php endif; ?></h3>
				<?=$result['report']?>
			<?php endforeach; ?>
			
	</div>
	
	
</div>