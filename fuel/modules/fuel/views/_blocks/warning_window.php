<?php if (!empty($warning_window)) : ?>
	<div class="warning jqmWindow jqmWindowShow" id="warning_window">
		<p><?=$warning_window?></p>
	
		<div class="buttonbar" id="yes_no_modal">
			<ul>
				<li class="unattached"><a href="#" class="ico ico_no" id="no_modal"><?=lang('btn_no')?></a></li>
				<li class="unattached"><a href="#" class="ico ico_yes" id="yes_modal"><?=lang('btn_yes')?></a></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>
<?php endif; ?>