<div id="main_top_panel">
	<h2 class="ico ico_blog_settings">Blog Settings</h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">
	<div id="main_content_inner">
	
		<div id="notification_extra" class="notification">
			<?php if ($warn_using_config) : ?>
					<div class="warning ico ico_warn">The blog is currently using a configuration file to control the settings. 
					You may save settings but they will not effect the site until the configuration file reliquishes control by changing the
					<strong>blog_use_db_table_settings</strong> configuration parameter to TRUE.</div>
			<?php endif; ?>
		</div>

		<p class="instructions">
			Configure the blog settings below:
		</p>

		<form method="post" action="" id="form">
		<?=$form?>
		</form>
	</div>
</div>