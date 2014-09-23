<?php

foreach ($related_items as $group => $group_related)
{
	if ( ! is_array($group_related)) $group_related = array($group_related);

	$mod = $this->fuel->modules->get($group);

	if ($this->fuel->modules->is_advanced($mod))
	{
		$icon = $mod->icon();
		$name = $mod->friendly_name();
	}
	else
	{
		$icon = $mod->info('icon_class');
		$name = $mod->info('module_name');
	}
	
	if ( ! empty($group_related))
	{
		echo '
		<h3 class="ico '.$icon.'">'.$name.'</h3>
		<ul>';

		foreach ($group_related as $url => $label)
		{
			if ($this->fuel->modules->is_advanced($mod))
			{
				$url = $mod->fuel_url($url);
			}
			else
			{
				$url = fuel_url($mod->info('module_uri').'/'.$url);
			}

			echo '<li><a href="'.$url.'">'.$label.'</a></li>';
		}

		echo '</ul>';
	}
}