<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 	<title><?=$page_title?></title>
	<?=css('fuel.min', FUEL_FOLDER)?>
	<?php if (!empty($css)) : ?>
	<?=$css?>
	<?php endif; ?>
	<script type="text/javascript">
	<?=$this->load->module_view('fuel', '_blocks/fuel_header_jqx', array(), TRUE)?>
	</script>
	<?=js('jquery/jquery', FUEL_FOLDER)?>
	<?=js('jqx/jqx', FUEL_FOLDER)?>
	<script type="text/javascript">
		jqx.addPreload('fuel.controller.BaseFuelController');
		jqx.init('fuel.controller.LoginController', {});
	</script>
</head>
<body>
<div id="login">
	<div id="login_inner">
		<img src="<?=img_path('fuel_logo.jpg')?>" width="400" height="100" alt="FUEL CMS" border="0" id="login_logo" />
		<div id="login_notification" class="notification">
			<?=$notifications?>
		</div>
		<?php if (!empty($instructions)) : ?>
		<p><?=$instructions?></p>
		<?php endif; ?>
		<?=$form?>
		<?php if ($display_forgotten_pwd) : ?>
			<a href="<?=fuel_url('login/pwd_reset')?>" id="forgotten_pwd"><?=lang('login_forgot_pwd')?></a>
		<?php endif; ?>
	</div>
	<div id="login_footer"><?=$this->load->module_view('fuel', '_blocks/fuel_footer_copyright', array(), TRUE) ?></div>
</div>
</body>
</html>