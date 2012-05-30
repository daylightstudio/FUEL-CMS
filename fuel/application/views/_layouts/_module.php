<?php 
$param = uri_segment($segment);

if ($param)
{
	if (is_numeric($param))
	{
		$item = fuel_model($model, array('find' => 'key', 'where' => $param));
	}
	else
	{
		if (empty($item_where))
		{
			$item_where = array($key_field => $param);
		}
		$item = fuel_model($model, array('find' => 'one', 'where' => $item_where));
	}
	
	if (empty($item))
	{
		show_404();
	}
}
else
{
	if (empty($list_where))
	{
		$list_where = array();
	}
	$data = fuel_model($model, array('find' => 'all', 'where' => $list_where));
}
?>

<?php if ($item) : ?>

	<?=fuel_block($item_block); ?>

<?php else: ?>
	
	<?=fuel_block($list_block); ?>

<?php endif; ?>