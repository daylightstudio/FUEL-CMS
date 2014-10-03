<script type="text/javascript">
//<![CDATA[
	var CKEDITOR_BASEPATH = '<?=js_path('', 'fuel')?>editors/ckeditor/';
	//var __FUEL_INLINE_EDITING = true;
	var __FUEL_INIT_PARAMS__ = <?=json_encode($init_params)?>; 
	var __FUEL_LOCALIZED__ = <?=$js_localized?>; 
	var __FUEL_PATH__ = '<?=site_url($this->config->item('fuel_path', 'fuel'))?>'; // for preview in markitup settings
	var __FUEL_LINKED_FIELDS = null;

	// to prevent some issues with loading jquery twice on the page
	if (typeof jQuery == 'undefined'){
		document.write('<script type="text/javascript" charset="utf-8" src="<?=js_path('jquery/jquery', 'fuel')?>"><\/script>');
	}

	// must be less then version 1.9 or we will load campatability helper
	var __jq_version__ = jQuery.fn.jquery.split('.');
	if (parseInt(__jq_version__[0]) > 1 || (parseInt(__jq_version__[0]) == 1 && parseInt(__jq_version__[1]) >= 9)){
		jQuery.migrateMute = true;
		document.write('<script type="text/javascript" charset="utf-8" src="<?=js_path('jquery/plugins/jquery-migrate-1.1.1.js', 'fuel')?>"><\/script>');
	}
//]]>
</script>

<?php

echo js('fuel/fuel_inline.min.js', 'fuel', array('ignore_if_loaded' => TRUE, 'output' => $this->fuel->config('fuel_assets_output')));

echo '
<div class="__fuel__" id="__fuel_edit_bar__">'.
	$this->form->open(array('action' => fuel_url('pages/ajax_page_edit/'), 'method' => 'post', 'id' => '__fuel_edit_bar_form__')).'
	<div class="buttonbar buttonbar_notop">
		<ul>';

		if ( ! isset($page['id']) && $is_fuelified)
		{
			echo '<li class="'.((isset($page['published']) && ! is_true_val($page['published'])) ? 'exposed' : 'start round exposed').'"><a href="#" id="__fuel_page_toolbar_toggle__" class="ico ico_fuel" title="'.lang('inline_edit_toggle_toolbar').'"></a></li>';

			if ($can_edit_pages) echo '<li class="'.( ! empty($_COOKIE['fuel_show_editable_areas']) && $_COOKIE['fuel_show_editable_areas'] == 1) ? 'active' : '').'"><a href="#" id="__fuel_page_edit_toggle__" class="ico ico_edit" title="'.lang('inline_edit_toggle_editable').'"></a></li>';

			if (count($tools) > 0) echo '<li> &nbsp;'.$this->form->select('tools', $tools, '', 'id="__fuel_page_tools__"', lang('inline_edit_tools')).'</li>';

			echo '
			<li class="txt"><a href="'.fuel_url('recent').'">'.lang('inline_edit_back_to_admin').'</a></li>
			<li class="txt"><a href="'.fuel_url('logout/'.$last_page).'" class="" title="'.lang('inline_edit_logout_title').'">'.lang('inline_edit_logout').'</a></li>';
		}
		else
		{
			if (isset($page['published']) && ! is_true_val($page['published'])) echo '<li class="start unpublished round exposed">'.lang('inline_edit_page_not_published').'</li>';

			echo '<li class="'.((isset($page['published']) && ! is_true_val($page['published'])) ? 'exposed' : 'start round exposed').'"><a href="#" id="__fuel_page_toolbar_toggle__" class="ico ico_fuel" title="'.lang('inline_edit_toggle_toolbar').'"></a></li>';

			if ($is_fuelified && $can_edit_pages)
			{
				echo '<li '.((!empty($_COOKIE['fuel_show_editable_areas']) && $_COOKIE['fuel_show_editable_areas'] == 1) ? 'class="active"' : '').'><a href="#" id="__fuel_page_edit_toggle__" class="ico ico_edit" title="'.lang('inline_edit_toggle_editable').'"></a></li>';

				if (isset($page['published']))
				{
					$publish = ( ! is_true_val($page['published'])) ? 'unpublish' : 'publish';

					echo '<li '.((is_true_val($page['published'])) ? 'class="active"' : '').'><a href="#" class="ico ico_'.$publish.'" id="__fuel_page_publish_toggle__" title="'.lang('inline_edit_toggle_publish').'"></a></li>';

					echo $this->form->hidden('published', $page['published'], 'id="__fuel_page_published__"');
				}

				if (isset($page['cache']))
				{
					echo '
					<li '.((is_true_val($page['cache'])) ? 'class="active"' : '').'>
						<a href="#" class="ico ico_cache" id="__fuel_page_cache_toggle__" title="'.lang('inline_edit_toggle_cache').'"></a>
					</li>'.
					$this->form->hidden('cache', $page['cache'], 'id="__fuel_page_cached__"');
				}

				if ($this->fuel->language->has_multiple())
				{
					echo '
					<li> &nbsp;'.$this->form->select($this->fuel->language->query_str_param, $this->fuel->language->options(), $language, 'id="__fuel_language__"').
						$this->form->hidden('language_mode', $language_mode, 'id="__fuel_language_mode__"') .
						$this->form->hidden('language_default', $language_default, 'id="__fuel_language_default__"').'
					</li>';
				}
			}

			if (count($tools) > 0) echo '<li> &nbsp;'.$this->form->select('tools', $tools, '', 'id="__fuel_page_tools__"', lang('inline_edit_tools')).'</li>';

			if (count($others) > 0) echo '<li> &nbsp;'.$this->form->select('others', $others, '', 'id="__fuel_page_others__"', lang('inline_edit_other_pages')).'</li>';

			if (count($layouts) > 1 && $can_edit_pages) echo '<li><label for="layout">'.lang('inline_edit_layout').'</label> '.$this->form->select('layout', $layouts, $page['layout'], 'id="__fuel_page_layout__"').'</li>';

			if ( ! empty($page['id']))
			{
				echo '<li class="txt"><a href="'.fuel_url('pages/edit/'.$page['id'].'?lang='.$language).'" title="'.lang('inline_edit_back_to_admin').'">'.lang('inline_edit_back_to_admin').'</a></li>';
			}

			if ($is_fuelified)
			{
				echo '
				<li class="txt">
					<a href="'.fuel_url('logout/'.$last_page).'" title="'.lang('inline_edit_logout_title').'">'.lang('inline_edit_logout').'</a>
				</li>';
			}
			else
			{
				$uri = uri_string();

				if ($uri == '') $uri = 'home';

				echo '
				<li class="txt">
					<a href="'.fuel_url('login/'.uri_safe_encode($uri)).'" title="'.lang('inline_edit_login_title').'">'.lang('inline_edit_login').'</a>
				</li>';
			}
		}

	echo '
		</ul>
		<div class="clear"></div>
	</div>';

	if (isset($page['id']))
	{
		echo
		$this->form->hidden('id', $page['id'], 'id=""').
		$this->form->hidden('location', $page['location'], 'id=""');
	}

	echo
	$this->form->close().'
</div>';