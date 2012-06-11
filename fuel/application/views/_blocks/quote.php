<?php $quote = fuel_model('quotes', array('find' => 'one', 'order' => 'RAND()')); ?>
<?php if (!empty($quote)) : ?>
<div id="block_quote">
	<?php echo quote($quote->content, $quote->name, $quote->title); ?>
</div>
<?php endif; ?>