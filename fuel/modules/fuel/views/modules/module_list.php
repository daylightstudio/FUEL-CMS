<?php if (!empty($description)) : ?>
<div id="module_description">
	<p><?=$description?></p>
</div>
<?php endif; ?>

<?php if ($this->advanced_search === 'collapse' OR $this->advanced_search === 'collapse_with_searchbox') : ?>
<div id="filters_container">
	<div id="" class="filters clearfix">
		<?php $this->load->module_view(FUEL_FOLDER, '_blocks/search_filters'); ?>
		<p><?=$this->form->submit(lang('btn_search'), 'search')?> &nbsp;&nbsp; <a href="<?=fuel_url($this->module_uri.'/reset_page_state', FALSE)?>"><?=lang('reset_search')?></a></p>
	</div>
	<a href="#" class="btn filters_toggle"><?=lang('filters_close')?></a>
</div>
<?php endif; ?>

<!-- list view -->
<div id="list_container">
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