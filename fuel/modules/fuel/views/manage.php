<div id="main_top_panel">
	<h2><?=lang('section_manage')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

<div id="main_content_inner">
	
	<div class="boxbuttons">
		<ul>
			<?php if ($this->fuel_auth->has_permission('users')) : ?><li><a href="<?=fuel_url('users')?>" class="ico_users"><?=lang('module_users')?></a></li><?php endif; ?> 
			<?php if ($this->fuel_auth->has_permission('manage/permissions')) : ?><li><a href="<?=fuel_url('permissions')?>" class="ico_permissions"><?=lang('module_permissions')?></a></li><?php endif; ?> 
			<?php if ($this->fuel_auth->has_permission('manage/cache')) : ?><li><a href="<?=fuel_url('manage/cache')?>" class="ico_manage_cache"><?=lang('module_manage_cache')?></a></li><?php endif; ?> 
			<?php if ($this->fuel_auth->has_permission('activity')) : ?><li><a href="<?=fuel_url('manage/activity')?>" class="ico_manage_activity"><?=lang('module_manage_activity')?></a></li><?php endif; ?> 
		</ul>
	</div>
	
	<div class="clear"></div>
	
	
</div>


