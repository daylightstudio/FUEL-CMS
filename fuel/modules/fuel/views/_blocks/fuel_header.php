<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
 	<title><?=$page_title?></title>

	<?=css('screen.min', 'fuel')?>

	<?php foreach($css as $m => $c) : echo css(array($m => $c))."\n\t"; endforeach; ?>
	<script>
		<?=$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header_jqx', array(), TRUE)?>
	</script>
	<?=js('jquery/jquery', 'fuel')?>
	<?=js('jqx/jqx', 'fuel')?>
	<?=js($this->fuel->config('fuel_javascript'), 'fuel')?>
	<?php foreach($js as $m => $j) : echo js(array($m => $j))."\n\t"; endforeach; ?>

	<?php if (!empty($this->js_controller)) : ?> 
	<script>
		<?php if ($this->js_controller != 'fuel.controller.BaseFuelController') : ?>
		jqx.addPreload('fuel.controller.BaseFuelController');
		<?php endif; ?>
		jqx.init('<?=$this->js_controller?>', <?=json_encode($this->js_controller_params)?>, '<?=$this->js_controller_path?>');
	</script>
	<?php endif; ?>

</head>
