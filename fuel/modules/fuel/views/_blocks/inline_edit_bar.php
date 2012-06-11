<script type="text/javascript">
//<![CDATA[
	var CKEDITOR_BASEPATH = '<?=js_path('', 'fuel')?>editors/ckeditor/';
	//var __FUEL_INLINE_EDITING = true;
	var __FUEL_INIT_PARAMS__ = <?=json_encode($init_params)?>; 
	var __FUEL_LOCALIZED__ = <?=$js_localized?>; 
	var __FUEL_PATH__ = '<?=site_url($this->config->item('fuel_path', 'fuel'))?>'; // for preview in markitup settings
	var __FUEL_LINKED_FIELDS = null;
	
	// to prevent some issues with loading jquery twice on the page
	if (jQuery == undefined){
		//document.write('<script type="text/javascript" charset="utf-8" src="<?=js_path('jquery/jquery', 'fuel')?>"><\/script>');
	}
//]]>
</script>

<?=js('jquery/plugins/jquery.form, jquery/plugins/jqModal, jquery/plugins/jquery.serialize, jquery/plugins/jquery.cookie, jquery/plugins/jquery.supercookie, jquery/plugins/jquery.ba-resize.min, fuel/global, fuel/edit_mode', 'fuel', array('ignore_if_loaded' => TRUE, 'output' => $this->fuel->config('fuel_assets_output')))?>

<?php /* ?>
<div  class="__fuel__ notification" id="__fuel_notification__">
	<?=$this->load->module_view(FUEL_FOLDER, '_blocks/notifications')?>
</div>
<?php */ ?>
<div class="__fuel__" id="__fuel_edit_bar__">
	<?=$this->form->open(array('action' => fuel_url('pages/ajax_page_edit/'), 'method' => 'post', 'id' => '__fuel_edit_bar_form__'))?>
	<div class="buttonbar buttonbar_notop">
		<ul>
			<?php if (!isset($page['id'])) : ?>
				<li class="<?=(isset($page['published']) && !is_true_val($page['published']))? 'exposed' : 'start round exposed'; ?>"><a href="#" id="__fuel_page_toolbar_toggle__" class="ico ico_fuel" title="<?=lang('inline_edit_toggle_toolbar')?>"></a></li>
				<li class="<?php if (!empty($_COOKIE['fuel_show_editable_areas']) && $_COOKIE['fuel_show_editable_areas'] == 1) : ?>active<?php endif; ?>"><a href="#" id="__fuel_page_edit_toggle__" class="ico ico_edit" title="<?=lang('inline_edit_toggle_editable')?>"></a></li>
				<?php if (count($tools) > 0) : ?><li> &nbsp;<?=$this->form->select('tools', $tools, '', 'id="__fuel_page_tools__"', lang('inline_edit_tools'))?> </li><?php endif; ?>
				<li class="txt"><a href="<?=fuel_url('recent')?>"><?=lang('inline_edit_back_to_admin')?></a></li>
				<li class="txt"><a href="<?=fuel_url('logout/'.$last_page)?>" class="" title="<?=lang('inline_edit_logout_title')?>"><?=lang('inline_edit_logout')?></a></li>
			<?php else: ?>
		
				<?php if (isset($page['published']) && !is_true_val($page['published'])) : ?>
						<li class="start unpublished round exposed"><?=lang('inline_edit_page_not_published')?></li>
				<?php endif; ?>
				
				<li class="<?=(isset($page['published']) && !is_true_val($page['published']))? 'exposed' : 'start round exposed'; ?>"><a href="#" id="__fuel_page_toolbar_toggle__" class="ico ico_fuel" title="<?=lang('inline_edit_toggle_toolbar')?>"></a></li>
				
				<li<?php if (!empty($_COOKIE['fuel_show_editable_areas']) && $_COOKIE['fuel_show_editable_areas'] == 1) : ?> class="active"<?php endif; ?>><a href="#" id="__fuel_page_edit_toggle__" class="ico ico_edit" title="<?=lang('inline_edit_toggle_editable')?>"></a></li>
			
				<?php if (isset($page['published'])) : ?>
					<?php $publish = (!is_true_val($page['published'])) ? 'unpublish' : 'publish';?>
					<li<?php if (is_true_val($page['published'])) : ?> class="active"<?php endif; ?>><a href="#" class="ico ico_<?=$publish?>" id="__fuel_page_publish_toggle__" title="<?=lang('inline_edit_toggle_publish')?>"></a></li>
					<?=$this->form->hidden('published', $page['published'], 'id="__fuel_page_published__"')?>
				<?php endif; ?>

				<?php if (isset($page['cache'])) : ?>
					<li<?php if (is_true_val($page['cache'])) : ?> class="active"<?php endif; ?>><a href="#" class="ico ico_cache" id="__fuel_page_cache_toggle__" title="<?=lang('inline_edit_toggle_cache')?>"></a></li>
					<?=$this->form->hidden('cache', $page['cache'], 'id="__fuel_page_cached__"')?>
				<?php endif; ?>
			
				
				<?php if (count($tools) > 0) : ?><li> &nbsp;<?=$this->form->select('tools', $tools, '', 'id="__fuel_page_tools__"', lang('inline_edit_tools'))?> </li><?php endif; ?>
				<?php if (count($others) > 0) : ?><li> &nbsp;<?=$this->form->select('others', $others, '', 'id="__fuel_page_others__"', lang('inline_edit_other_pages'))?> </li><?php endif; ?>
				<?php if (count($layouts) > 1) : ?><li><label for="layout"><?=lang('inline_edit_layout')?></label> <?=$this->form->select('layout', $layouts, $page['layout'], 'id="__fuel_page_layout__"')?></li><?php endif; ?>
				<li class="txt"><a href="<?=fuel_url('pages/edit/'.$page['id'])?>" title="<?=lang('inline_edit_back_to_admin')?>"><?=lang('inline_edit_back_to_admin')?></a></li>
				<li class="txt"><a href="<?=fuel_url('logout/'.$last_page)?>" title="<?=lang('inline_edit_logout_title')?>"><?=lang('inline_edit_logout')?></a></li>
				
			<?php endif; ?>
		</ul>
		<div class="clear"></div>
	</div>
	<?php if (isset($page['id'])) : ?>
	<?=$this->form->hidden('id', $page['id'], 'id=""')?>
	<?php endif; ?>
	<?=$this->form->close()?>
	
</div>