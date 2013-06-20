<?php  if (!empty($related_items) AND ((is_array($related_items) AND current($related_items)) OR is_string($related_items)) OR !empty($versions) AND !$this->fuel->admin->is_inline()) : ?>


<div id="related_items">

	<?php if (isset($action) AND $action == 'edit') : ?>
		<?php if (!empty($versions)) : ?>
			<div class="versions"><?=$this->form->select('fuel_restore_version', $versions, '', '', lang('label_restore_from_prev'))?></div>
			<?=$this->form->hidden('fuel_restore_ref_id', $id)?>
		<?php endif; ?>
	<?php endif; ?>


	<?php if (is_array($related_items)) : ?>

	<?php $this->load->module_view(FUEL_FOLDER, '_blocks/related_items_array'); ?>
	
	<?php elseif (is_string($related_items)) : ?>
		<?=$related_items?>
	<?php endif; ?>
	
</div>
<?php endif; ?>