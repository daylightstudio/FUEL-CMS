<?php 
$slug = uri_segment(3);
$project = fuel_model('projects', array('find' => 'one', 'where' => array('slug' => $slug)));
if (!isset($project->id)) redirect('showcase'); // if project doesn't exist then we show 404'
?>
<?php echo fuel_edit($project->id, 'Edit Project: '.$project->name, 'projects'); ?>

<div class="project_detail">
	<?php if (!empty($project->image)) : ?>
	<img src="<?php echo $project->image_path; ?>" />
	<?php endif; ?>
	
	<div class="project_info">
		<h1><?php echo $project->name; ?></h1>
		<ul>
			<?php if (!empty($project->client)) : ?>
			<li><strong>Client:</strong> <?php echo $project->client; ?></li>
			<?php endif; ?>
			<?php if (!empty($project->website)) : ?>
			<li><strong>Website:</strong> <a href="<?php echo prep_url($project->website); ?>" target="_blank"><?php echo $project->website; ?></a></li>
			<?php endif; ?>
			<?php if ((int)$project->launch_date) : ?>
			<li><strong>Launched:</strong> <?php echo $project->launch_date_formatted; ?></li>
			<?php endif; ?>
		</ul>
		<?php echo $project->description_formatted; ?>
	</div>
	<div class="clear"></div>
</div>
<a href="<?php echo site_url('showcase')?>">&lt; Back to Showcase List</a>