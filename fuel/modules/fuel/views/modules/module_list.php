<?php if (!empty($description)) : ?>
<div id="module_description">
	<p><?=$description?></p>
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