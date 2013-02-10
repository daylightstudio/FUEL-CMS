<div id="fuel_main_top_panel">
	<h2 class="ico <?=$titlebar_icon?>">
	<?php if (!empty($titlebar)) : ?>
	<?php if (is_array($titlebar)) : ?>
	<?php $last_key = array_pop($titlebar);
		foreach($titlebar as $url => $crumb) : ?>
			<?php if (!$this->fuel->admin->is_inline()) : ?><a href="<?=fuel_url($url)?>"><?php endif; ?><?=$crumb?><?php if (!$this->fuel->admin->is_inline()) : ?></a><?php endif; ?> &gt;
		<?php endforeach; ?>
		<?=$last_key?>
		<?php else: ?>
		<?=$titlebar?>
	<?php endif; ?>
	<?php endif; ?>
	</h2>

	<?php if (!$this->fuel->admin->is_inline() AND !empty($user)) : ?>
	<div id="fuel_login_logout">
		<?=lang('logged_in_as')?>
		<a href="<?=fuel_url('my_profile/edit/')?>"><strong><?=$user['user_name']?></strong></a>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?=fuel_url('logout')?>"><?=lang('logout')?></a>
	</div>
	<?php endif; ?>

</div>

<div class="clear"></div>