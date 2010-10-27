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
					<td class="search"><?=$this->form->submit('Search', 'search')?></td>
					<td class="show">Show: <?=$this->form->select('limit', array('25' => '25', '50' => '50', '100' => '100'), $params['limit'])?></td>
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
			<li class="active"><a href="#" id="toggle_list" class="ico ico_table" title="<?=$keyboard_shortcuts['toggle_view']?> to toggle view">List</a></li>
			<li class="end"><a href="#" id="toggle_tree" class="ico ico_tree" title="<?=$keyboard_shortcuts['toggle_view']?> to toggle view">Tree</a></li>
			<li class="spacer end"><a href="<?=fuel_url($create_url)?>" class="ico ico_create"><?=$this->create_action_name?></a></li>
			<?php else : ?>
			<li class="end"><a href="<?=fuel_url($create_url)?>" class="ico ico_create" id="create_item"><?=$this->create_action_name?></a></li>
			<?php endif; ?>
			<?php if ($this->fuel_auth->module_has_action('delete') && $this->fuel_auth->has_permission($this->permission, 'delete')) : ?>
				<li class="spacer end"><a href="#" class="ico ico_delete" id="multi_delete">Delete Multiple</a></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (!empty($this->list_actions)) : ?>
			<?php foreach($this->list_actions as $action => $label) : ?>
			<li class="spacer end"><?=anchor(fuel_url($action), $label, array('class' => 'ico ico_'.$this->module.'_'.url_title($label, 'underscore', TRUE)))?></li>
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
