<?php foreach($results as $key => $result): ?>
<?php if (is_array($result)) : ?>
-------------------------------------------------- 
<?=ucfirst(strip_tags($key))?> 
-------------------------------------------------- 
<?php if (count($results) > 1) : ?>
- <?=lang('ut_passed')?>: <?=$result['passed']?> 
- <?=lang('ut_failed')?>: <?=$result['failed']?> 
<?php endif; ?> 
<?=$result['report']?><?php endif; ?><?php endforeach; ?>
-------------------------------------------------- 
<?=lang('tester_accumulative')?> 
-------------------------------------------------- 
- <?=lang('ut_passed')?>: <?=$results['total_passed']?> 
- <?=lang('ut_failed')?>: <?=$results['total_failed']?> 

