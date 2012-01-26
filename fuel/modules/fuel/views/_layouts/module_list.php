<div id="main_top_panel">
	<h2 class="ico ico_<?=url_title(str_replace('/', '_', $this->module_uri),'_', TRUE)?>"><?=$this->module_name?></h2>
</div>

<div class="clear"></div>

<?=$this->form->open(array('action' => fuel_url($this->module_uri.'/items'), 'method' => 'post', 'id' => 'form_table'))?>

<div id="action">


	<div id="filters">
		<table border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td><a href="<?=fuel_url($this->module_uri.'/reset_page_state')?>" class="reset"></a></td>
					<td><?=$this->form->search('search_term', $params['search_term'])?> </td>
					<td class="search"><?=$this->form->submit(lang('btn_search'), 'search')?></td>
					<td class="show"><?=lang('label_show')?> <?=$this->form->select('limit', $this->table_row_limits, $params['limit'])?></td>
					<td>
						<?=$more_filters?>
					</td>
				</tr>
			</tbody>
		</table>
		
	</div>

	<div class="buttonbar" id="actions">

		<ul>
		<?php if ($this->fuel_auth->module_has_action('create')) : ?>
			<?php 
			$create_url = (!empty($this->model->filters['group_id'])) ? $this->module_uri.'/create/'.$this->model->filters['group_id'] : $this->module_uri.'/create';
			if (!empty($tree)) : ?>
			<li class="active"><a href="#" id="toggle_list" class="ico ico_table" title="<?=$keyboard_shortcuts['toggle_view']?> to toggle view"><?=lang('btn_list')?></a></li>
			<li class="end"><a href="#" id="toggle_tree" class="ico ico_tree" title="<?=$keyboard_shortcuts['toggle_view']?> to toggle view"><?=lang('btn_tree')?></a></li>
			<li class="spacer end"><a href="<?=fuel_url($create_url)?>" class="ico ico_create"><?=$this->create_action_name?></a></li>
			<?php else : ?>
			<li class="end"><a href="<?=fuel_url($create_url)?>" class="ico ico_create" id="create_item"><?=$this->create_action_name?></a></li>
			<?php endif; ?>
			<?php if ($this->fuel_auth->module_has_action('delete') && $this->fuel_auth->has_permission($this->permission, 'delete')) : ?>
			<li class="spacer end"><a href="#" class="ico ico_delete" id="multi_delete"><?=lang('btn_delete_multiple')?></a></li>
			<?php endif; ?>
			<li class="spacer end"><a href="#" class="ico ico_precedence" id="rearrange"><?=lang('btn_rearrange')?></a></li>
		<?php endif; ?>
		<?php if (!empty($this->list_actions)) : ?>
			<?php 
			foreach($this->list_actions as $action => $label) : 
			$lang_key = str_replace('/', '_', $action);
			?>
			<li class="spacer end"><?=anchor(fuel_url($action), $label, array('class' => 'ico ico_'.$lang_key))?></li>
			<?php endforeach; ?>
		<?php endif; ?>
		</ul>
		
		
	</div>
	
</div>
<div id="notification" class="notification">
	<?=$notifications?>
	<div id="pagination"><?=$pagination?></div>
</div>

<div id="main_content">

	<div id="list_container">


	<!-- list view -->
		<div id="data_table_container">
			<?=$table?>
		</div>
		<div class="loader" id="table_loader"></div>
	</div>

	<?php if (!empty($tree)) : ?>
	<!-- tree view -->
	<div id="tree_container">
		<div id="tree">
			<?=$tree?>
		</div>
		<div class="loader hidden" id="tree_loader"></div>
	</div>
	<?php endif; ?>

	<div class="clear"></div>

</div>
<?=$this->form->hidden('view_type', $params['view_type'])?>
<?=$this->form->close()?>
