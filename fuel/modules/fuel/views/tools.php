<div id="fuel_main_content_inner">
	<div class="boxbuttons">
		<ul>
		<?php

		foreach ($nav['tools'] as $key => $val)
		{
			if ($this->fuel->auth->has_permission($key) && $val != 'View All...')
			{
				echo '
				<li'.($this->nav_selected == $key ? ' class="active"' : '').'>
					<a href="'.fuel_url($key).'">
						<i class="ico ico_'.url_title(str_replace('/', '_', $key),'_', TRUE).'"></i>'.
						$val.'
					</a>
				</li>';
			}
		}

		?>
		</ul>
	</div>
	<div class="clear"></div>
</div>