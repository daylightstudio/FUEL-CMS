<div class="buttonbar" id="actions">
	<ul>
		<?php if (isset($action) AND $action == 'edit') : ?>
			
			<?php if ($this->fuel_auth->module_has_action('save')) : ?>
				<li><a href="#" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save">Save</a></li>
			<?php endif; ?>
			
			<?php if (!empty($this->preview_path) AND $this->fuel_auth->module_has_action('view')) : ?>
				<li><a href="<?=site_url($this->preview_path)?>" class="ico ico_view view_action" title="<?=$keyboard_shortcuts['view']?> to view">View</a></li>
			<?php endif; ?>

			<?php if ($this->fuel_auth->module_has_action('publish') AND $this->fuel_auth->has_permission($this->permission, 'publish')) : ?>
				<?php if (!empty($publish)) : ?>
			<li><a href="#" class="ico ico_<?=strtolower($publish)?> <?=strtolower($publish)?>_action"><?=$publish?></a></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->fuel_auth->module_has_action('activate') AND $this->fuel_auth->has_permission($this->permission, 'activate')) :  ?>
				<?php if (!empty($activate))  : ?>
			<li><a href="#" class="ico ico_<?=strtolower($activate)?> <?=strtolower($activate)?>_action"><?=$activate?></a></li>
				<?php endif; ?>
			<?php endif; ?>

		
			<?php if ($this->fuel_auth->module_has_action('delete') AND $this->fuel_auth->has_permission($this->permission, 'delete')) :?>
				<li><a href="<?=fuel_url($this->module_uri.'/delete/'.$id)?>" class="ico ico_delete delete_action">Delete</a></li>
			<?php endif; ?>
			
			<?php if ($this->fuel_auth->module_has_action('duplicate')) : ?>
				<li><a href="<?=fuel_url($this->module_uri.'/create')?>" class="ico ico_duplicate duplicate_action">Duplicate</a></li>
			<?php endif; ?>
			<?php if ($this->fuel_auth->module_has_action('create')) : ?>
				<li class="end"><a href="<?=fuel_url($this->module_uri.'/create')?>" class="ico ico_create">Create</a></li>
			<?php endif; ?>
		<?php elseif ($action == 'create' AND $this->fuel_auth->module_has_action('save')) : ?>
			<li class="end"><a href="#" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save">Save</a></li>
		<?php endif; ?>
	</ul>
	<?php if (!empty($others)) {?><div id="other_items"><?=$this->form->select('others', $others, '', '', 'Select another...')?></div><?php } ?>
</div>

<?php if (isset($action) AND $action == 'edit') : ?>
<div id="filters">
	<?php if (!empty($versions)) : ?>
	<form method="post" action="<?=fuel_url($this->module_uri.'/restore')?>" id="restore_form">
		<div class="versions"><?=$this->form->select('version', $versions, '', '', 'Restore from previous version...')?></div>
		<?=$this->form->hidden('ref_id', $id)?>
		
	</form>
	<?php endif; ?>
</div>
<?php endif; ?>
