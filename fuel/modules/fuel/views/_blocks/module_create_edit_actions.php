<div class="buttonbar" id="action_btns">
	<ul>
		<?php if (isset($action) AND $action == 'edit') : ?>
			
			<?php if ($this->fuel->auth->module_has_action('save')) : ?>
				<li><a href="#" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save"><?=lang('btn_save')?></a></li>
			<?php endif; ?>
			
			<?php if (!empty($this->preview_path) AND $this->fuel->auth->module_has_action('view')) : ?>
				<li><a href="<?=site_url($this->preview_path, FALSE)?>" class="ico ico_view key_view_action<?php if (!$this->fuel->config('view_in_new_window')) : ?> view_action<?php endif; ?>" title="<?=$keyboard_shortcuts['view']?> to view" target="_blank"><?=lang('btn_view')?></a></li>
			<?php endif; ?>

			<?php if ($this->fuel->auth->module_has_action('publish') AND $this->fuel->auth->has_permission($this->permission, 'publish')) : ?>
				<?php if (!empty($publish)) : ?>
			<li><a href="#" class="ico ico_<?=strtolower($publish)?> <?=strtolower($publish)?>_action"><?=lang('btn_'.strtolower($publish))?></a></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->fuel->auth->module_has_action('activate') AND $this->fuel->auth->has_permission($this->permission, 'activate')) :  ?>
				<?php if (!empty($activate))  : ?>
			<li><a href="#" class="ico ico_<?=strtolower($activate)?> <?=strtolower($activate)?>_action"><?=lang('btn_'.strtolower($activate))?></a></li>
				<?php endif; ?>
			<?php endif; ?>

		
			<?php if ($this->fuel->auth->module_has_action('delete') AND $this->fuel->auth->has_permission($this->permission, 'delete')) : ?>
				<li><a href="<?=fuel_url($this->module_uri.'/delete/'.$id, TRUE)?>" class="ico ico_delete delete_action"><?=lang('btn_delete')?></a></li>
			<?php endif; ?>
			
			<?php if ($this->fuel->auth->module_has_action('duplicate')) : ?>
				<li><a href="<?=fuel_url($this->module_uri.'/create', TRUE)?>" class="ico ico_duplicate duplicate_action"><?=lang('btn_duplicate')?></a></li>
			<?php endif; ?>
			
			<?php if ($this->fuel->auth->module_has_action('replace') AND !empty($others) AND $this->fuel->auth->has_permission($this->permission, 'edit') AND $this->fuel->auth->has_permission($this->permission, 'delete')) : ?>
				<li><a href="<?=fuel_url($this->module_uri.'/replace/'.$id, TRUE)?>" class="ico ico_replace replace_action"><?=lang('btn_replace')?></a></li>
			<?php endif; ?>
			
			<?php if ($this->fuel->auth->module_has_action('others')) : ?>
			<?php foreach($this->item_actions['others'] as $other_action => $label) : 
				if ($this->fuel->auth->has_permission($this->permission, $other_action)) :
				$ico_key = str_replace('/', '_', $other_action);
				$lang_key = url_title($label, 'underscore', TRUE);
				if ($new_label = lang('btn_'.$lang_key)) $label = $new_label;
			?>
				<li><?=anchor(fuel_url($other_action, TRUE), $label, array('class' => 'submit_action ico ico_'.$ico_key))?></li>
			<?php endif; ?>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php if ($this->fuel->auth->module_has_action('create') AND $this->fuel->auth->has_permission($this->permission, 'create')) : ?>
				<li class="end"><a href="<?=fuel_url($this->module_uri.'/create', TRUE)?>" class="ico ico_create"><?=lang('btn_create')?></a></li>
			<?php endif; ?>
			
			
		<?php elseif ($action == 'create' AND $this->fuel->auth->module_has_action('save')) : ?>
			<li class="end"><a href="#" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save"><?=lang('btn_save')?></a></li>
		<?php endif; ?>
	</ul>
	<?php if (!empty($others) AND !$this->fuel->admin->is_inline()) {?><div id="other_items"><?=$this->form->select('fuel_other_items', $others, '', '', lang('label_select_another'), array($id))?></div><?php } ?>
</div>