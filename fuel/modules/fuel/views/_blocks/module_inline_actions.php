<div class="buttonbar" id="action_btns">
	<ul>
		<?php if (isset($action) AND $action == 'edit') : ?>
			
			<?php if ($this->fuel->auth->module_has_action('save')) : ?>
				<li><a href="<?=fuel_url($this->module_uri.'/inline_edit')?>" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save"><?=lang('btn_save')?></a></li>
			<?php endif; ?>
			
			<?php if ($this->fuel->auth->module_has_action('publish') AND $this->fuel->auth->has_permission($this->permission, 'publish')) : ?>
				<?php if (!empty($publish)) : ?>
			<li><a href="<?=fuel_url($this->module_uri.'/inline_edit')?>" class="ico ico_<?=strtolower($publish)?> <?=strtolower($publish)?>_action"><?=lang('btn_'.strtolower($publish))?></a></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->fuel->auth->module_has_action('activate') AND $this->fuel->auth->has_permission($this->permission, 'activate')) :  ?>
				<?php if (!empty($activate))  : ?>
			<li><a href="#" class="ico ico_<?=strtolower($activate)?> <?=strtolower($activate)?>_action"><?=lang('btn_'.strtolower($activate))?></a></li>
				<?php endif; ?>
			<?php endif; ?>

		
			<?php if ($this->fuel->auth->module_has_action('delete') AND $this->fuel->auth->has_permission($this->permission, 'delete')) :?>
				<li><a href="<?=fuel_url($this->module_uri.'/inline_delete/'.$id)?>" class="ico ico_delete delete_action"><?=lang('btn_delete')?></a></li>
			<?php endif; ?>
			
			<?php if ($this->fuel->auth->module_has_action('duplicate')) : ?>
				<li><a href="<?=fuel_url($this->module_uri.'/inline_create')?>" class="ico ico_duplicate duplicate_action"><?=lang('btn_duplicate')?></a></li>
			<?php endif; ?>
			<?php if ($this->fuel->auth->module_has_action('create')) : ?>
				<li class="end"><a href="<?=fuel_url($this->module_uri.'/inline_create')?>" class="ico ico_create"><?=lang('btn_create')?></a></li>
			<?php endif; ?>
			
		<?php elseif ($action == 'create' AND $this->fuel->auth->module_has_action('save')) : ?>
			<li class="end"><a href="<?=fuel_url($this->module_uri.'/inline_create')?>" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save"><?=lang('btn_save')?></a></li>
		<?php endif; ?>
	</ul>
</div>

<?php if (isset($action) AND $action == 'edit') : ?>
<div id="filters">
	<?php if (!empty($versions)) : ?>
	<form method="post" action="<?=fuel_url($this->module_uri.'/restore')?>" id="restore_form">
		<div class="versions"><?=$this->form->select('version', $versions, '', '', lang('label_restore_from_prev'))?></div>
		<?=$this->form->hidden('ref_id', $id)?>
		
	</form>
	<?php endif; ?>
</div>
<?php endif; ?>
