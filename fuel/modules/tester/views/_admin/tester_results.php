<div id="fuel_main_content_inner">
	<div id="tester_results">
	
			<div style="float: right;" class="btn">
				<a href="javascript:$('#form').submit()" class="ico ico_refresh"><?=lang('tester_reload_all')?></a>
				<input type="hidden" name="tests_serialized" value="<?=$tests_serialized?>" />
			</div>
		
			<a href="<?=fuel_url('tools/tester')?>" class="back"><?=lang('tester_back')?></a>

			<h2><?=lang('tester_accumulative')?> - <?=lang('ut_passed')?>: <span class="success"><?=$results['total_passed']?></span> <?=lang('ut_failed')?>: <span class="error"><?=$results['total_failed']?></span></h2>
			<?php foreach($results as $key => $result): ?>
			<?php if (is_array($result)) : ?>
			<h3><?=ucfirst($key)?><?php if (count($results) > 1) : ?> - <?=lang('ut_passed')?>: <span class="success"><?=$result['passed']?></span> <?=lang('ut_failed')?>: <span class="error"><?=$result['failed']?></span><?php endif; ?></h3>
				<?=$result['report']?>
			<?php endif; ?>
			<?php endforeach; ?>
		
	</div>

</div>