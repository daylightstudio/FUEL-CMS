<ul>
	<?php foreach($features as $feature) {?>
	<li class="<?=$feature->icon_class?>">
		
		<h3><?=$feature->title?>
			<?=fuel_edit($feature->id, 'Edit Feature '.$feature->title, 'features')?>
		</h3>
		<p><?=$feature->copy?></p>
	</li>
	<?php } ?>
</ul>