<?php if ($change_pwd){ ?>
<div class="jqmWindow jqmWindowShow warning" id="change_pwd_notification">
	<div class="modal_content_inner">
		<p><?=lang('warn_change_default_pwd', $this->config->item('default_pwd', 'fuel'))?></p>
		<div class="buttonbar" id="yes_no_modal" style="width: 364px;">
			<ul>
				<li class="unattached"><a href="#" class="ico ico_no jqmClose" id="change_pwd_cancel"><?=lang('dashboard_change_pwd_later')?></a></li>
				<li class="unattached"><a href="<?=fuel_url('my_profile/edit/')?>" class="ico ico_yes" id="change_pwd_go"><?=lang('dashboard_change_pwd')?></a></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>
</div>
<?php } ?>

<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('dashboard_intro')?></p>
	
	<?php foreach($dashboards as $dashboard) : ?>
		<div id="dashboard_<?=$dashboard?>" class="dashboard_module">
			<div class="loader"></div>
		</div>
	<?php endforeach; ?>
	
	<div class="clear"></div>
</div>