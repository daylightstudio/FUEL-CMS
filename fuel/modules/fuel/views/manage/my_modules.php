<div id="fuel_main_content_inner">
	<div class="boxbuttons">
		<ul>
		<?php

		foreach ($modules as $key => $module)
		{
			if ($this->fuel->auth->has_permission($key))
			{
				echo '<li><a href="'.$module->fuel_url().'" class="ico '.$module->icon().'">'.$module->friendly_name().'</a></li>';
			}
		}

		?>
		</ul>
	</div>
	<div class="clear"></div>
</div>