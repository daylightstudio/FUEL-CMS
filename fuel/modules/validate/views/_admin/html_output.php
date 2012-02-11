<?php if (!empty($error)) : ?>
<span class="error" id="total_invalid"><span id="total_invalid_num">404</span> Error</span>
<?php else : ?>
<?php if (!empty($edit_url)) : ?><div id="edit_url"><?=$edit_url?></div><?php endif; ?>
<div class="summary">
<h1><a href="<?=$link?>"><?=$link?></a></h1>
<?php 
/*noticed issue with warn_num showing 1 when no warnings were in list, so I 
opted for just doing a count on the $errors, $warnings to be accurate of what 
is reflected*/
 ?>
<?php if (!empty($errors) OR !empty($warnings)) : ?>
	<?php if (!empty($errors)) : ?>
	<span class="error" id="total_invalid"><span id="total_invalid_num"><?=count($errors)?></span> <?=lang('validate_errors')?></span>
	<?php endif; ?>

	<?php if (!empty($warnings)) : ?>
	<span class="warning" id="total_warning"><span id="total_warning_num"><?=count($warnings)?></span> <?=lang('validate_warnings')?></span>
	<?php endif; ?>
<?php else : ?>
<span class="success" id="valid">Valid</span>
<?php endif; ?>
</div>

<?php if (count($errors)) : ?>
<h2><?=lang('validate_errors')?></h2>
<ul class="nobullets">
	<?php foreach($errors as $error) : ?>
	<li><span  class="error"><em><?=lang('validate_line')?> <?=$error['line']?>, <?=lang('validate_column')?> <?=$error['col']?></em>: <strong><?=$error['message']?></strong></span>
		<pre><code><?=htmlspecialchars($error['explanation'], ENT_NOQUOTES, 'UTF-8', FALSE)?></code></pre>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (count($warnings)) : ?>
<h2><?=lang('validate_warnings')?></h2>
<ul class="nobullets">
	<?php foreach($warnings as $warning) : ?>
	<li><span class="warning"><strong><?=$warning['message']?></strong></span> <br /><br /></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php endif; ?>

