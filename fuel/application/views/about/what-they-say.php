<?php $quotes = fuel_model('quotes', array('find' => 'all', 'precedence desc')); ?>

<h1>What They Say</h1>
<p>Don't take our word for it. Here what some of our customers have to say:</p>

<div id="quotes">
<?php echo fuel_edit('create', 'Add Quote', 'quotes'); ?>

<?php foreach($quotes as $quote) : ?>
	<?php echo fuel_edit($quote->id, 'Edit Quote', 'quotes'); ?>
	<?php echo quote($quote->content, $quote->name, $quote->title); ?>
<?php endforeach; ?>
</div>