<?php $products = fuel_model('products', array('find' => 'all')); ?>

<h1>Products</h1>

<?php if ( ! empty($products)) : ?>
<?php foreach ($products as $product) : ?>
	<div class="product">
		<h2><?php echo $product->name; ?></h2>
		
		<?php if ( ! empty($product->description)) : ?>
		<div class="product_description">
			<?php echo $product->description; ?>
		</div>
		<?php endif; ?>
		
		<?php $product_widgets = $product->widgets; ?>
		<?php if ( ! empty($product_widgets)) : ?>
		<h3>Product Widgets</h3>
		
		<ul class="product_widgets">
			<?php foreach ($product_widgets as $widget) : ?>
			<?php $widget_armaments = $widget->armaments; ?>
			<li>
				<?php echo $widget->name_formatted; ?> <sup><?php echo $widget->id; ?></sup>
				<?php if ( ! empty($widget_armaments)) : ?>
				<ul>
					<?php foreach ($widget_armaments as $armament) : ?>
					<li><?php echo $armament->name_formatted; ?> <sup><?php echo $armament->id; ?></sup></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</div>

<?php endforeach; ?>
<?php else : ?>
<p>These aren't the products you're looking for... move along.</p>
<?php endif; ?>