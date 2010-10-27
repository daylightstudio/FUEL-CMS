<div id="main_top_panel">
	<h2><?=lang('h2_manage')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

<div id="main_content_inner">
	
	<div class="boxbuttons">
		<ul>
			<?php if ($this->fuel_auth->has_permission('Manage users')) : ?><li><a href="<?=fuel_url('users')?>" class="ico_users">Fuel Users</a></li><?php endif; ?> 
			<?php if ($this->fuel_auth->has_permission('Manage permissions')) : ?><li><a href="<?=fuel_url('permissions')?>" class="ico_permissions">Permissions</a></li><?php endif; ?> 
			<?php if ($this->fuel_auth->has_permission('Clear cache')) : ?><li><a href="<?=fuel_url('manage/cache')?>" class="ico_manage_cache">Clear Page Cache</a></li><?php endif; ?> 
			<?php if ($this->fuel_auth->has_permission('View logs')) : ?><li><a href="<?=fuel_url('manage/activity')?>" class="ico_manage_activity">View Activity Log</a></li><?php endif; ?> 
		</ul>
	</div>
	
	<div class="clear"></div>
	
	
</div>


