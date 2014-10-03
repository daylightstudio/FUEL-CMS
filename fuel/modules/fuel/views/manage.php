<div id="fuel_main_content_inner">
	<div class="boxbuttons">
		<ul>
			<?php

			if ($this->fuel->auth->has_permission('users')) echo '<li><a href="'.fuel_url('users').'"><i class="ico ico_users"></i>'.lang('module_users').'</a></li>';
			if ($this->fuel->auth->has_permission('permissions')) echo '<li><a href="'.fuel_url('permissions').'"><i class="ico ico_permissions"></i>'.lang('module_permissions').'</a></li>';
			if ($this->fuel->auth->has_permission('manage/cache')) echo '<li><a href="'.fuel_url('manage/cache').'"><i class="ico ico_manage_cache"></i>'.lang('module_manage_cache').'</a></li>';
			if ($this->fuel->auth->has_permission('logs')) echo '<li><a href="'.fuel_url('logs').'"><i class="ico ico_logs"></i>'.lang('module_manage_activity').'</a></li>';
			if ($this->fuel->auth->has_permission('settings')) echo '<li><a href="'.fuel_url('settings').'"><i class="ico ico_settings"></i>'.lang('module_manage_settings').'</a></li>';

			?>
		</ul>
	</div>
	<div class="clear"></div>
</div>