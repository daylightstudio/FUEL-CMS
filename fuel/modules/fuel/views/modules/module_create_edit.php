<!-- NOTIFICATION EXTRA -->
<div id="notification_extra" class="notification">
	<?php if (!empty($data['published']) && !is_true_val($data['published'])) {?>
			<div class="warning ico ico_warn"><?=lang('warn_not_published')?></div>
	<?php } else if (!empty($data['active']) && !is_true_val($data['active'])) {?>
		<div class="warning ico ico_warn"><?=lang('warn_not_active', strtolower(substr($this->module_name, 0, -1)))?></div>
	<?php } ?>
</div>

<div id="fuel_main_content_inner">

	<?php if (!empty($instructions)) : ?>
	<p class="instructions"><?=$instructions?></p>
	<?php endif; ?>

	<?=$form?>

</div>