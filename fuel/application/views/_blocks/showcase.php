<?php $project = fuel_model('projects', array('find' => 'one', 'where' => array('featured' => 'yes'), 'order' => 'RAND()')); ?>

<?php if (!empty($project)) : ?>
<div id="block_showcase">
	<h3><?php echo $project->name ?></h3>
	<img src="<?php echo $project->image_path; ?>" />
</div>
<?php endif; ?>