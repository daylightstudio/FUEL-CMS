<!-- NOTIFICATION EXTRA -->
<div id="notification_extra" class="notification">
	<?php if (isset($data['published']) && !is_true_val($data['published'])) {?>
			<div class="warning ico ico_warn"><?=lang('warn_not_published')?></div>
	<?php } else if (isset($data['active']) && !is_true_val($data['active'])) {?>
		<div class="warning ico ico_warn"><?=lang('warn_not_active', strtolower(substr($this->module_name, 0, -1)))?></div>
	<?php } ?>
</div>
	
<?php if (!empty($warning_window)) : ?>
	<div class="warning jqmWindow jqmWindowShow" id="warning_window">
		<p><?=$warning_window?></p>
	
		<div class="buttonbar" id="yes_no_modal">
			<ul>
				<li class="unattached"><a href="#" class="ico ico_no" id="no_modal"><?=lang('btn_no')?></a></li>
				<li class="unattached"><a href="#" class="ico ico_yes" id="yes_modal"><?=lang('btn_yes')?></a></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>

<?php endif; ?>

<div id="fuel_main_content_inner">

	<?php if (!empty($instructions)) : ?>
	<p class="instructions"><?=$instructions?></p>
	<?php endif; ?>

	<?=$form?>

</div>