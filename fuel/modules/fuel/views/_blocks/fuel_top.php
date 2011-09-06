<div id="fuel_top">
	<h1 id="fuel_site_name"><a href="<?=site_url()?>"><?=$this->config->item('site_name', 'fuel')?></a></h1>
	<div id="fuel_login_logout">
			<?=lang('logged_in_as')?>
			<a href="<?=fuel_url('my_profile/edit/')?>"><strong><?=$user['user_name']?></strong></a>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?=fuel_url('logout')?>"><?=lang('logout')?></a>
	</div>
</div>