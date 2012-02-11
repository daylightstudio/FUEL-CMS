<?php if (!empty($error)) : ?>
<span class="error" id="total_invalid"><span id="total_invalid_num">404</span> Error</span>
<?php else : ?>
<?php if (!empty($edit_url)) : ?><div id="edit_url"><?=$edit_url?></div><?php endif; ?>
<div class="summary">
<h1><a href="<?=$link?>"><?=$link?></a></h1>
<span class="success" id="total"><?=lang('validate_total_valid')?> <?=($total - count($invalid))?></span>
<span class="error" id="total_invalid"><?=lang('validate_total_invalid')?> <span id="total_invalid_num"><?=count($invalid)?></span></span>
</div>

<?php if (count($invalid)) : ?>
<h2>Invalid Links</h2>
<ul class="nobullets">
	<?php foreach($invalid as $link) : ?>
	<li class="invalid"><a href="<?=$link?>" target="_blank"><?=$link?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endif; ?>


