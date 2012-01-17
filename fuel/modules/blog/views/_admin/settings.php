<div id="fuel_notification_extra" class="notification">
	<?php if ($warn_using_config) : ?>
			<div class="warning ico ico_warn">The blog is currently using a configuration file to control the settings. 
			You may save settings but they will not effect the site until the configuration file reliquishes control by changing the
			<strong>blog_use_db_table_settings</strong> configuration parameter to TRUE.</div>
	<?php endif; ?>
</div>

<div id="fuel_main_content_inner">


	<p class="instructions">
		Configure the blog settings below:
	</p>

	<form method="post" action="" id="form">
	<?=$form?>
	</form>
</div>
