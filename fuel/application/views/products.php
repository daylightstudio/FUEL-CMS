<?php

$products = fuel_model('products', array('find' => 'all'));
$widgets = fuel_model('product_widgets', array('find' => 'all', 'order' => 'name'));

?>

<style type="text/css">
#page { overflow: hidden; }
#page .col { float: left; width: 300px; margin-right: 10px; }
.product ul li ul { margin-bottom: 0; }
</style>

<div id="page">
	<div class="col">
		<h1>Products</h1>
		
		<?php if ( ! empty($products)) : ?>
			<?php foreach ($products as $product) : ?>
			<div class="product">
				<h2><?=$product->name?><sup><?=$product->id?></sup></h2>
				
				<?php if ( ! empty($product->description)) : ?>
				<div class="product_description">
					<?=$product->description?>
				</div>
				<?php endif; ?>
				
				<?php $product_widgets = $product->widgets; ?>
				<?php if ( ! empty($product_widgets)) : ?>
				<h3>Product Widgets</h3>
				
				<ul class="product_widgets">
					<?php foreach ($product_widgets as $widget) : ?>
					<?php $widget_armaments = $widget->armaments; ?>
					<li>
						<?=$widget->name_formatted?><sup><?=$widget->id?></sup>
						<?php if ( ! empty($widget_armaments)) : ?>
						<ul>
							<?php foreach ($widget_armaments as $armament) : ?>
							<li><?=$armament->name_formatted?><sup><?=$armament->id?></sup></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php else : ?>
				<p><em>No product widgets.</em></p>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		<?php else : ?>
		<p>These aren't the products you're looking for... move along.</p>
		<?php endif; ?>
	</div>
	
	<div class="col">
		<h1>Product Widgets</h1>
		
		<?php if ( ! empty($widgets)) : ?>
			<?php foreach ($widgets as $widget) : ?>
			<div class="widget">
				<h2><?=$widget->name?><sup><?=$widget->id?></sup></h2>
				
				<?php $widget_products = $widget->products; ?>
				<?php if ( ! empty($widget_products)) : ?>
				<h3>Widget Products</h3>
				
				<ul class="widget_products">
					<?php foreach ($widget_products as $product) : ?>
					<li><?=$product->name?> <sup><?=$product->id?></sup></li>
					<?php endforeach; ?>
				</ul>
				<?php else: ?>
				<p><em>Doesn't belong to any products.</em></p>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>