<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 	<title><?=$page_title?></title>

	<?=css('datepicker, jqmodal, markitup, jquery.tooltip, jquery.supercomboselect, jquery.treeview, fuel', 'fuel')?>

	<?php foreach($css as $c) : echo css($c); endforeach; ?>
	
	<script type="text/javascript">
		<?=$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header_jqx', array(), TRUE)?>
	</script>
	<?=js('jquery/jquery', 'fuel')?>
	<?=js('jqx/jqx', 'fuel')?>
	<?=js($this->config->item('fuel_javascript', 'fuel'), 'fuel')?>
	<?php foreach($js as $m => $j) : echo js(array($m => $j)); endforeach; ?>

	<?php if (!empty($this->js_controller)) : ?> 
	<script type="text/javascript">
		<?php if ($this->js_controller != 'BaseFuelController') : ?>
		jqx.addPreload('fuel.controller.BaseFuelController');
		<?php endif; ?>
		jqx.init('<?=$this->js_controller?>', <?=json_encode($this->js_controller_params)?>, '<?=$this->js_controller_path?>');
	</script>
	<?php endif; ?>

</head>
<body>

<div id="fuel_header">
	<h1 id="site_name"><a href="<?=site_url()?>"><?=$this->config->item('site_name', 'fuel')?></a></h1>
	<div id="login_logout">
			<?=lang('logged_in_as')?>
			<a href="<?=fuel_url('my_profile/edit/')?>"><strong><?=$user['user_name']?></strong></a>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?=fuel_url('logout')?>"><?=lang('logout')?></a>
	</div>
</div>
<div id="fuel_body">
	<div id="fuel_left_panel">
		<div id="fuel_left_panel_inner">
			
<?php 
	// Get all modules
	$modules = $this->fuel_modules->get_modules();
	$mods = array();
        
	foreach($modules as $mod)
	{
		if(isset($mod['module_uri']))
		{
			// Index modules by their uri so we know which module belongs to a specific nav item
			$mods[$mod['module_uri']] = isset($mod['permission']) ? $mod['permission'] : '';
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
				$key = isset($mods[$key]) ? $mods[$key] : $key;
				
				// Convert wild-cards to RegEx
				$nav_selected = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $this->nav_selected));

				if (($this->fuel_auth->has_permission($key)) || $key == 'dashboard')
				{
					if  (!$header_written)
					{
						$section_hdr = lang('section_'.$section);
						if (empty($section_hdr))
						{
							$section_hdr = ucfirst(str_replace('_', ' ', $section));
						}
						echo "<div class=\"left_nav_section\" id=\"leftnav_".$section."\">\n";
						echo "\t<h3>".$section_hdr."</h3>\n";
						echo "\t<ul>\n";
					}
					echo "\t\t<li";
					if (preg_match('#^'.$nav_selected.'$#', $url))
					{
						echo ' class="active"';
					}
					echo "><a href=\"".fuel_url($url)."\" class=\"ico ico_".url_title(str_replace('/', '_', $key),'_', TRUE)."\">".$val."</a></li>\n";
					$header_written = TRUE;
				} 
			}
		}
		if  ($header_written)
		{
			echo "\t</ul>\n";
			echo "</div>\n";
		}
		
	}
?>
				
			<?php 
				$user_data = $this->fuel_auth->user_data();
				if (isset($user_data['recent'])) : ?>
			<div class="left_nav_section" id="leftnav_recent">
				<h3><?=lang('section_recently_viewed')?></h3>
				<ul>
					<?php foreach($user_data['recent'] as $val) : ?>
					<li><a href="<?=site_url($val['link'])?>" class="ico ico_<?=$val['type']?>" title="<?=$val['name']?>"><?=$val['name']?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

		</div>
	</div>
	<div id="main_panel">

