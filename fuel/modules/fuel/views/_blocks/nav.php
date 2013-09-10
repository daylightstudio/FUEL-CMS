	<div id="fuel_left_panel">
		<div id="fuel_left_panel_inner">
<?php 
	// // Get all modules
	$modules = $this->fuel->modules->get();
	$mods = $icons = array();
	        
	foreach($modules as $mod)
	{
		$info = $mod->info();
	    if(isset($info['module_uri']))
	    {
	        // Index modules by their uri so we know which module belongs to a specific nav item
	        $mods[$info['module_uri']] = isset($info['permission']) ? $info['permission'] : '';
	        // Use custom icon classes
	        $icons[$info['module_uri']] =isset($info['icon_class']) ?$info['icon_class'] : "ico_".url_title(str_replace('/', '_', $info['module_uri']),'_', TRUE);
	    }
	}
	
	foreach($nav as $section => $nav_items)
	{
		if (is_array($nav_items))
		{
			$header_written = FALSE;
			foreach($nav_items as $key => $val)
			{
				$segments = explode('/', $key);
				$url = $key;
				
				// Check for a specific module's permission                                
				$perm = (isset($mods[$key]) AND !is_array($mods[$key])) ? $mods[$key] : $key;
				
				if (($this->fuel->auth->has_permission($perm)) || $perm == 'dashboard')
				{
					if  (!$header_written)
					{
						$section_hdr = lang('section_'.$section);
						if (empty($section_hdr))
						{
							$section_hdr = ucfirst(str_replace('_', ' ', $section));
						}
						echo "<div class=\"left_nav_section\" id=\"leftnav_".str_replace('/', '_', $section)."\">\n";
						echo "\t<h3>".$section_hdr."</h3>\n";
						echo "\t<ul>\n";
					}
					echo "\t\t<li";
					if (preg_match('#^'.$nav_selected.'$#', $url))
					{
						echo ' class="active"';
					}
					// Use custom icons or default to key as class
					$icon = isset($icons[$key]) ? $icons[$key] :  "ico_".url_title(str_replace('/', '_', $key),'_', TRUE);
					echo "><a href=\"".fuel_url($url)."\" class=\"ico ".$icon."\">".$val."</a></li>\n";
					$header_written = TRUE;
				} 
			}
		}
		else
		{
			$header_written = FALSE;
		}
		
		if  ($header_written)
		{
			echo "\t</ul>\n";
			echo "</div>\n";
		}
		
	}
?>
				
			<?php 
				$user_data = $this->fuel->auth->user_data();
				if (!empty($user_data['recent'])) : ?>
			<div class="left_nav_section" id="leftnav_recent">
				<h3><?=lang('section_recently_viewed')?></h3>
				<ul>
					<?php foreach($user_data['recent'] as $val) : ?>
					<li><a href="<?=site_url($val['l'])?>" class="ico ico_<?=$val['t']?>" title="<?=$val['n']?>"><?=$val['n']?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

		</div>
	</div>
