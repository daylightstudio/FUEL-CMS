<?php $projects = fuel_model('projects', array('find' => 'all', 'order' => 'precedence desc')); ?>

<h1>Showcase</h1>

<?php echo fuel_edit('create', 'Add Project', 'projects'); ?>

<?php if (!empty($projects)) : ?>
<?php foreach($projects as $project) : ?>
	
	<div class="project">
	
		<?php if (!empty($project->thumb)) : ?>
		<img src="<?php echo $project->thumb; ?>" />
		<?php endif; ?>
		
		<div class="project_info">
			<?php echo fuel_edit($project->id, 'Edit Project: '.$project->name, 'projects'); ?>
			
			<h2><?php echo $project->name; ?></h2>
			<ul>
				<?php if (!empty($project->client)) : ?>
				<li><strong>Client:</strong> <?php echo $project->client; ?></li>
				<?php endif; ?>
				<?php if (!empty($project->website)) : ?>
				<li><strong>Website:</strong> <a href="<?php echo prep_url($project->website); ?>" target="_blank"><?php echo $project->website; ?></a></li>
				<?php endif; ?>
				<?php if ((int) $project->launch_date) : ?>
				<li><strong>Launched:</strong> <?php echo $project->launch_date_formatted; ?></li>
				<?php endif; ?>
			</ul>
			<a href="<?php echo $project->url?>" class="readmore">View More</a>
		</div>
	</div>
	
	
<?php endforeach; ?>
<?php endif; ?>