<?php $products = fuel_model('products', array('find' => 'all')); ?>

<h1>Products</h1>

<?php if ( ! empty($products)) : ?>
<?php foreach ($products as $product) : ?>
	<?php $product_widgets = $product->widgets; ?>

	<div class="product">
		<h2><?php echo $product->name; ?></h2>
		
		<?php if ( ! empty($product->description)) : ?>
		<div class="product_description">
			<?php echo $product->description; ?>
		</div>
		<?php endif; ?>
		
		<?php if ( ! empty($product_widgets)) : ?>
		<h3>Product Widgets</h3>
		
		<ul class="product_widgets">
			<?php foreach ($product_widgets as $widget) : ?>
			<li><?php echo $widget->name_formatted; ?> <sup><?php echo $widget->id; ?></sup></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</div>

<?php endforeach; ?>
<?php else : ?>
<p>These aren't the products you're looking for... move along.</p>
<?php endif; ?>