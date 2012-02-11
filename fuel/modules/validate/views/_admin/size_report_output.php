<?php if (!empty($edit_url)) : ?><div id="edit_url"><?=$edit_url?></div><?php endif; ?>
<?php if (isset($total_kb)) : ?>

<div class="summary">
	<h1><a href="<?=$link?>"><?=$link?></a></h1>
	<h2 id="total"><?=lang('validate_approx_file_size')?>
		<?php if (($total_kb/1000) < 0) :?>
			<span class="error" id="total_error"><?=byte_format($total_kb)?></span>
		<?php elseif (($total_kb/1000) >= $config_limit AND ($total_kb/1000) > 0) : ?>
			<span class="warning" id="total_warn"><?=byte_format($total_kb)?></span>
		<?php else: ?>
			<span class="success" id="total_ok"><?=byte_format($total_kb)?></span>
		<?php endif; ?>
	</h2>
	<div id="summary_key">
		<span class="error"><span class="num"><?=count($invalid)?></span> <?=lang('validate_invalid')?></span>
		<span class="warning"><?=lang('validate_file_size_greater')?> <?=$config_limit?>KB</span>
		<span class="success"><?=lang('validate_file_size_less')?> <?=$config_limit?>KB</span>
	</div>
</div>

<div class="clear"></div>

<h2><?=lang('validate_resources_file_sizes')?></h2>
<ul class="nobullets">
	<?php foreach($invalid as $link) : ?>
	<li><a href="<?=$link?>" target="_blank"><?=$link?></a> <span class="error"><?=lang('validate_invalid')?></span></li>
	<?php endforeach; ?>
	
	<?php foreach($filesize_range['warn'] as $link => $filesize) : ?>
	<li><a href="<?=$link?>" target="_blank"><?=$link?></a> <span class="warning"><?=byte_format($filesize)?></span></li>
	<?php endforeach; ?>
	
	<?php foreach($filesize_range['ok'] as $link => $filesize) : ?>
	<li><a href="<?=$link?>" target="_blank"><?=$link?></a> <span class="success"><?=byte_format($filesize)?></span></li>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<?=lang('error_calculating_page_weight')?>
<?php endif; ?>