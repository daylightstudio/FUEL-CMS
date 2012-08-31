<div id="fuel_main_content_inner">
	
	<div class="boxbuttons">
		<ul>
			<?php if ($this->fuel->auth->has_permission('users')) : ?><li><a href="<?=fuel_url('users')?>" class="ico_users"><?=lang('module_users')?></a></li><?php endif; ?> 
			<?php if ($this->fuel->auth->has_permission('permissions')) : ?><li><a href="<?=fuel_url('permissions')?>" class="ico_permissions"><?=lang('module_permissions')?></a></li><?php endif; ?> 
			<?php if ($this->fuel->auth->has_permission('cache')) : ?><li><a href="<?=fuel_url('manage/cache')?>" class="ico_manage_cache"><?=lang('module_manage_cache')?></a></li><?php endif; ?> 
			<?php if ($this->fuel->auth->has_permission('logs')) : ?><li><a href="<?=fuel_url('logs')?>" class="ico_logs"><?=lang('module_manage_activity')?></a></li><?php endif; ?> 
			<?php if ($this->fuel->auth->has_permission('settings')) : ?><li><a href="<?=fuel_url('settings')?>" class="ico_settings"><?=lang('module_manage_settings')?></a></li><?php endif; ?> 
		</ul>
	</div>
	
	<div class="clear"></div>
	
	
</div>


