<?php

$settings = array();
$modules = $this->fuel->config('modules_allowed');
foreach ($modules as $module)
{
	$module_settings = $this->fuel->modules->get_advanced_module_settings($module);
	if ($module_settings) {
		$settings[$module] = $module_settings;
	}
}

?>
<div id="fuel_main_content_inner">
<?php if (empty($settings)) : ?>
	<p>There are no settings for any advanced modules to manage.</p>
<?php else : ?>
	<p class="instructions">Manage the settings for the following advanced modules:<p>
	
	<ul>
		<?php foreach ($settings as $module => $module_settings) : ?>
		<li><a href="<?=site_url("fuel/settings/manage/{$module}")?>"><?=humanize($module)?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
</div>