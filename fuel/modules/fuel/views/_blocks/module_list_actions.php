<div id="filters">
	<table border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<?php if (empty($this->advanced_search)) : ?>
				<td>
					<?php if (!empty($more_filters)) : ?>
					<?=$more_filters?>
					<?php endif; ?>
				</td>
				<?php endif; ?>

				<?php if ( $this->advanced_search !== 'collapse') : ?>
				<td><a href="<?=fuel_url($this->module_uri.'/reset_page_state', FALSE)?>" class="reset"></a></td>
				<td>
					<div class="search_input<?php if ( $this->advanced_search === TRUE OR $this->advanced_search === 'popover') : ?> advanced<?php endif; ?>">
						<?=$this->form->search('search_term', $params['search_term'], 'placeholder="'.lang('label_search').'"')?>
						<?php if ( $this->advanced_search === TRUE OR $this->advanced_search === 'popover') : ?>
						<a href="#" id="adv-search-btn" title="<?=lang('adv_search')?>"><img src="<?=img_path('th_arrow_desc.png', FUEL_FOLDER)?>" /></a>
						<div class="adv_search">
							<p><strong><?=lang('adv_search')?></strong></p>
							<?php $this->load->module_view(FUEL_FOLDER, '_blocks/search_filters'); ?>
							<p><?=$this->form->submit(lang('btn_search'), 'search')?> &nbsp;&nbsp; <a href="#" id="adv-search-close"><?=lang('viewpage_close')?></a> &nbsp;&nbsp; <a href="<?=fuel_url($this->module_uri.'/reset_page_state', FALSE)?>"><?=lang('reset_search')?></a></p>

						</div>
					</div>
					<?php endif ?>
				</td>
				<td class="search"><?=$this->form->submit(lang('btn_search'), 'search')?></td>
				<?php endif; ?>
				
				<td class="show"><label for="limit"><?=lang('label_show')?></label> <?=$this->form->select('limit', $this->limit_options, $params['limit'])?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class="buttonbar" id="action_btns">

	<ul>
		<?php 
		$create_url = (!empty($this->model->filters['group_id'])) ? $this->module_uri.'/create/?group_id='.$this->model->filters['group_id'] : $this->module_uri.'/create';
		if (!empty($tree)) : ?>
		<li class="active"><a href="#" id="toggle_list" class="ico ico_table" title="<?=$keyboard_shortcuts['toggle_view']?> to toggle view"><?=lang('btn_list')?></a></li>
		<li><a href="#" id="toggle_tree" class="ico ico_tree" title="<?=$keyboard_shortcuts['toggle_view']?> to toggle view"><?=lang('btn_tree')?></a></li>
		<?php endif; ?>
		<?php if (!empty($this->list_actions)) : ?>
			<?php 
			foreach($this->list_actions as $action => $label) : 
				$ico_key = trim(preg_replace('#(.+)\{.+\}(.*)#', '$1', $action), '/');
				$action_parts = explode('/', $ico_key);
				$action_permissions = end($action_parts);
				$action_permissions_arr = explode('?', $action_permissions);
				$action_perm = current($action_permissions_arr);
				$lang_key = str_replace('/', '_', $action);

				// $action_arr = explode('?', $action);
				// $action = current($action_arr);
			?>
			<?php if ($this->fuel->auth->has_permission($this->permission, $action_perm)) : ?>
			<li><?=anchor(fuel_url($action), $label, array('class' => 'ico ico_'.$lang_key))?></li>
			<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<li><a href="#" class="ico ico_select_all"><?=lang('btn_select_all')?></a></li>

		<li><a href="#" class="ico ico_precedence" id="rearrange"><?=lang('btn_rearrange')?></a></li>

		<?php if ($this->fuel->auth->module_has_action('delete') && $this->fuel->auth->has_permission($this->permission, 'delete')) : ?>
		<li><a href="#" class="ico ico_delete" id="multi_delete"><?=lang('btn_delete_multiple')?></a></li>
		<?php endif; ?>
		
		<?php if ($this->exportable AND $this->fuel->auth->has_permission($this->permission, 'export')) : ?>
			<li><a href="<?=fuel_url($this->module_uri.'/export')?>" class="ico ico_export" id="export_data"><?=lang('btn_export_data')?></a></li>
		<?php endif; ?>
		
		<?php if ($this->fuel->auth->module_has_action('create') AND $this->fuel->auth->has_permission($this->permission, 'create')) : ?>
		<li class="end"><a href="<?=fuel_url($create_url)?>" class="ico ico_create"><?=$this->create_action_name?></a></li>
		<?php endif; ?>
	</ul>
	
	
</div>
<?php if (!empty($params['view_type'])) : ?>
<?=$this->form->hidden('view_type', $params['view_type'])?>
<?php endif; ?>
