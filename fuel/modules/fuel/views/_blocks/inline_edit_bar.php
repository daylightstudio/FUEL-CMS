<script type="text/javascript">
//<![CDATA[
	var __FUEL_INIT_PARAMS__ = <?=json_encode($init_params)?>;
	var __FUEL_PATH__ = '<?=site_url($this->config->item('fuel_path', 'fuel'))?>'; // for preview in markitup settings
//]]>
</script>
<?=js('jquery/jquery, jquery/plugins/jquery.form, jquery/plugins/jqModal, jquery/plugins/date, jquery/plugins/jquery.datePicker, jquery/plugins/jquery.tooltip, jquery/plugins/jquery.markitup.pack, jquery/plugins/jquery.markitup.set, jquery/plugins/jquery.serialize, jquery/plugins/jquery.cookie, jquery/plugins/jquery.supercookie, jquery/plugins/jquery-ui-1.8.4.custom.min, jquery/plugins/jquery.disable.text.select.pack, jquery/plugins/jquery.selso, jquery/plugins/jquery.fillin, jquery/plugins/jquery.supercomboselect, jquery/plugins/jquery.MultiFile.pack, jquery/plugins/jquery.scrollTo-min, jquery/plugins/jquery.ba-resize.min, fuel/edit_mode', 'fuel')?>

<div  class="__fuel__ notification" id="__fuel_notification__">
	<?=$this->load->module_view(FUEL_FOLDER, '_blocks/notifications')?>
</div>
<div class="__fuel__" id="__fuel_edit_bar__">
	<?=$this->form->open(array('action' => fuel_url('pages/ajax_page_edit/'), 'method' => 'post', 'id' => '__fuel_edit_bar_form__'))?>
	<div class="buttonbar buttonbar_notop">
		<ul>
			<?php if (!isset($page['id'])) : ?>
				<li class="<?=(isset($page['published']) && !is_true_val($page['published']))? 'exposed' : 'start round exposed'; ?>"><a href="#" id="__fuel_page_toolbar_toggle__" class="ico ico_fuel" title="Toggle toolbar display"></a></li>
				<li class="<?php if (!empty($_COOKIE['fuel_show_editable_areas']) && $_COOKIE['fuel_show_editable_areas'] == 1) : ?>active<?php endif; ?>"><a href="#" id="__fuel_page_edit_toggle__" class="ico ico_edit" title="toggle editable areas"></a></li>
				<li class="txt"><a href="<?=fuel_url('recent')?>" title="Go back to the FUEL admin">Back to Admin</a></li>
				<li class="txt"><a href="<?=fuel_url('logout')?>" class="" title="Logout of FUEL admin">Logout</a></li>
			<?php else: ?>
		
				<?php if (isset($page['published']) && !is_true_val($page['published'])) : ?>
						<li class="start unpublished round exposed">The page is not published</li>
				<?php endif; ?>
				
				<li class="<?=(isset($page['published']) && !is_true_val($page['published']))? 'exposed' : 'start round exposed'; ?>"><a href="#" id="__fuel_page_toolbar_toggle__" class="ico ico_fuel" title="Toggle toolbar display"></a></li>
				
				<li<?php if (!empty($_COOKIE['fuel_show_editable_areas']) && $_COOKIE['fuel_show_editable_areas'] == 1) : ?> class="active"<?php endif; ?>><a href="#" id="__fuel_page_edit_toggle__" class="ico ico_edit" title="toggle editable areas"></a></li>
			
				<?php if (isset($page['published'])) : ?>
					<?php $publish = (!is_true_val($page['published'])) ? 'unpublish' : 'publish';?>
					<li<?php if (is_true_val($page['published'])) : ?> class="active"<?php endif; ?>><a href="#" class="ico ico_<?=$publish?>" id="__fuel_page_publish_toggle__" title="Toggle the pages publish status"></a></li>
					<?=$this->form->hidden('published', $page['published'], 'id="__fuel_page_published__"')?>
				<?php endif; ?>

				<?php if (isset($page['cache'])) : ?>
					<li<?php if (is_true_val($page['cache'])) : ?> class="active"<?php endif; ?>><a href="#" class="ico ico_cache" id="__fuel_page_cache_toggle__" title="Toggle page cache settings"></a></li>
					<?=$this->form->hidden('cache', $page['cache'], 'id="__fuel_page_cached__"')?>
				<?php endif; ?>
			
				
				<?php if (count($others) > 0) : ?><li> &nbsp;<?=$this->form->select('others', $others, '', 'id="__fuel_page_others__"', 'Other pages...')?> </li><?php endif; ?>
				<?php if (count($layouts) > 1) : ?><li><label for="layout">Layout</label> <?=$this->form->select('layout', $layouts, $page['layout'], 'id="__fuel_page_layout__"')?></li><?php endif; ?>
				<li class="txt"><a href="<?=fuel_url('pages/edit/'.$page['id'])?>" title="Go back to the FUEL admin">Back to Admin</a></li>
				<li class="txt"><a href="<?=fuel_url('logout')?>" title="Logout of FUEL admin">Logout</a></li>
				
			<?php endif; ?>
		</ul>
		<div class="clear"></div>
	</div>
	<?php if (isset($page['id'])) : ?>
	<?=$this->form->hidden('id', $page['id'], 'id=""')?>
	<?php endif; ?>
	<?=$this->form->close()?>
	
</div>