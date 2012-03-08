<?php $products = fuel_model('products', array('find' => 'all')); ?>

<h1>Products</h1>

<?php if ( ! empty($products)) : ?>
<?php foreach ($products as $product) : ?>
	
	<div class="product">
		<h2><?php echo $product->name; ?></h2>
		
		<?php if ( ! empty($product->description)): ?>
		<div class="product_description">
			<?php echo $product->description; ?>
		</div>
		<?php endif; ?>
		
		<h3>Product Widgets</h3>
	</div>

<?php endforeach; ?>
<?php else : ?>
<p>These aren't the products you're looking for... move along.</p>
<?php endif; ?>