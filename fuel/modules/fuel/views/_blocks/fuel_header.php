<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
 	<title><?=$page_title?></title>

 	<meta name="viewport" content="width=device-width">

	<?=css('fuel.min', 'fuel')?>

	<?php foreach($css as $m => $c) : echo css(array($m => $c))."\n\t"; endforeach; ?>
	<?=js('jquery/jquery', 'fuel')?>

	<script type="text/javascript">
		<?=$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header_jqx', array(), TRUE)?>
	</script>

</head>