<!-- RELATED ITEMS -->
<?php echo $related = $this->load->module_view(FUEL_FOLDER, '_blocks/related_items', array(), TRUE); ?>

<!-- NOTIFICATION EXTRA -->
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/notification_extra'); ?>

<!-- WARNING WINDOW -->
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/warning_window'); ?>


<div id="fuel_main_content_inner"<?php if (!empty($related)) : ?> class="has_related"<?php endif; ?>>

	<?php if (!empty($instructions)) : ?>
	<p class="instructions"><?=$instructions?></p>
	<?php endif; ?>

	<?=$form?>

</div>