<?php if ($change_pwd){ ?>
<div class="jqmWindow jqmWindowShow warning" id="change_pwd_notification">
	<p><?=lang('warn_change_default_pwd', $this->config->item('default_pwd', 'fuel'))?></p>

	<div class="buttonbar" id="yes_no_modal" style="width: 400px;">
		<ul>
			<li class="end"><a href="#" class="ico ico_no jqmClose" id="change_pwd_cancel"><?=lang('dashboard_change_pwd_later')?></a></li>
			<li class="end"><a href="<?=fuel_url('my_profile/edit/')?>" class="ico ico_yes" id="change_pwd_go"><?=lang('dashboard_change_pwd')?></a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>

<div id="main_top_panel">
	<h2><?=lang('section_dashboard')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
</div>

<div id="main_content" class="noaction">
	<div id="main_content_inner">
		<p class="instructions"><?=lang('dashboard_intro')?></p>
		
		<?php foreach($dashboards as $dashboard) : ?>
			<div id="dashboard_<?=$dashboard?>" class="dashboard_module">
				<div class="loader"></div>
			</div>
		<?php endforeach; ?>
		
		<div class="clear"></div>
		

	</div>
	
	<div>
</div>