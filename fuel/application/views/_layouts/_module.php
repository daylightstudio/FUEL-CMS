<?php 
/*
This is an example of a Fuel_module_layout where you have a list view and an item view
*/

$param = uri_segment($segment);

// check if there is a uri segment
if ($param)
{
	// if the uri segment is numeric, then search by key
	if (is_numeric($param))
	{
		$item = fuel_model($model, array('find' => 'key', 'where' => $param));
	}
	else
	{
		// otherwise we search by find one
		if (empty($item_where))
		{
			$item_where = array($key_field => $param);
		}
		$item = fuel_model($model, array('find' => 'one', 'where' => $item_where));
	}
	
	// if no item is found, then do a redirect or 404 if no redirect exists
	if (empty($item))
	{
		redirect_404();
	}
}
else
{
	// if no uri segment, then we do a query on the model to get a list of data
	if (empty($list_where))
	{
		$list_where = array();
	}
	$data = fuel_model($model, array('find' => 'all', 'where' => $list_where));
}
?>

<?php if (!empty($item)) : ?>

	<?=fuel_block(array('view' => $item_block, 'data' => $item));  ?>

<?php else: ?>
	
	<?=fuel_block(array('view' => $list_block, 'data' => $data)); ?>

<?php endif; ?>