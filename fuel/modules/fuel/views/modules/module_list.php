<?php

if ( ! empty($description))
{
	echo '
	<div id="module_description">
		<p>'.$description.'</p>
	</div>';
}

// List view
echo '
<div id="list_container">
	<div id="data_table_container">'.
		$table.'
	</div>
	<div class="loader" id="table_loader"></div>
</div>';

if ( ! empty($tree))
{
	// Tree view
	echo '
	<div id="tree_container">
		<div id="tree">'.$tree.'</div>
		<div class="loader hidden" id="tree_loader"></div>
	</div>';
}

echo '<div class="clear"></div>';