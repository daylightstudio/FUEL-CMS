<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<?php

		echo '
		<title>'.$page_title.'</title>'.
		css('fuel.min', FUEL_FOLDER);

		if ( ! empty($css)) echo $css;

		echo '
		<script type="text/javascript">'.
			$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header_jqx', array(), TRUE).'
		</script>'.
		js('jquery/jquery', FUEL_FOLDER).
		js('jqx/jqx', FUEL_FOLDER);

		?>
		<script type="text/javascript">
			jqx.addPreload("fuel.controller.BaseFuelController");
			jqx.init("fuel.controller.LoginController", {});
		</script>
	</head>
	<body>
		<div id="login">
			<div class="login_logo">
				<span class="hidden">FUEL CMS</span>
			</div>
			<div id="login_notification" class="notification">
				<?=$notifications?>
			</div>
			<?php

			if ( ! empty($instructions)) echo '<p>'.$instructions.'</p>';

			echo $form;

			if ($display_forgotten_pwd) echo '<a href="'.fuel_url('login/pwd_reset').'" id="forgotten_pwd">'.lang('login_forgot_pwd').'</a>';

			?>
			</div>
		</div>
	</body>
</html>