<?php fuel_set_var('layout', '')?>

<?=fuel_block('header')?>
	<div id="features_screenshots">
		
		<div id="features">
			<div id="for_clients">
				<h2><span class="lite">FOR</span> CLIENTS</h2>
				<?=fuel_block(array('model' => 'features', 'where' => array('type' => 'client'), 'view' => 'features_list'))?>
				
			</div>
			<div id="for_developers">
				<h2><span class="lite">FOR</span> DEVELOPERS</h2>
				<?=fuel_block(array('model' => 'features', 'where' => array('type' => 'developer'), 'view' => 'features_list'))?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
		
<?=fuel_block('footer')?>
