<div id="main_top_panel">
	<h2 class="ico ico_pages"><a href="<?=fuel_url($this->module)?>"><?=$this->module_name?></a> &gt; 
	<?=lang('action_'.$action)?><?php if (!empty($data[$this->display_field])) { ?>: <em><?=$data[$this->display_field]?></em><?php } ?></h2>
</div>
<div class="clear"></div>

<div id="action">

	<?=$actions?>

</div>

<div id="notification" class="notification">
	<?=$notifications?>
	<?php if (!empty($data['last_modified'])) : ?>
	<p class="ico ico_info last_updated"><?=lang('pages_last_updated_by', english_date($data['last_modified'], true), $data['email'])?></p>
	<?php else: ?>

	<?php endif; ?>
</div>
<div id="main_content">

	<?php  if (!empty($page_navs)) : ?>
	<div id="page_navs">
		<h3 class="ico ico_navigation"><?=lang('pages_associated_navigation')?></h3>
		<ul>
			<?php foreach($page_navs as $nav) : ?>
			<li><a href="<?=fuel_url('navigation/edit/'.$nav['id'])?>"><?=$nav['label']?> 
			<?php if (!empty($nav['group_name'])){ ?>(<?=$nav['group_name']?>)<?php } ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>

	<div id="notification_extra" class="notification">
		<?php if (!empty($data['published']) && !is_true_val($data['published'])) : ?>
				<div class="warning ico ico_warn"><?=lang('pages_not_published')?></div>
		<?php endif; ?>
		
	
		<?php if (!empty($routes)) : ?>
		<div class="warning ico ico_warn">
			<?=lang('page_route_warning', APPPATH.'config/routes.php')?>
			<?php foreach($routes as $val) : ?>
				<ul>
					<li><?=$val?></li>
				</ul>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	
		<?php if ($uses_controller) : ?>
			<div class="warning ico ico_warn">
				<?=lang('page_controller_assigned', $view_twin)?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ($import_view) : ?>
		<div class="warning jqmWindow jqmWindowShow" id="view_twin_notification">
			<p><?=lang('page_updated_view', $view_twin)?></p>
		
			<div class="buttonbar" id="yes_no_modal">
				<ul>
					<li class="end"><a href="#" class="ico ico_no" id="view_twin_cancel"><?=lang('page_no_import')?></a></li>
					<li class="end"><a href="#" class="ico ico_yes" id="view_twin_import"><?=lang('page_yes_import')?></a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	
	<?php endif; ?>


	<div id="main_content_inner" style="margin-right: 180px;">

		<p class="instructions"><?=$this->instructions?></p>

		<form method="post" action="<?=fuel_url('pages/'.$action.'/'.$id)?>" enctype="multipart/form-data" id="form">

			<div id="tab_page_variables">
				<h3><?=lang('page_information')?></h3>
				<?=$form?>

				<h3><?=lang('page_layout_vars')?></h3>
				<div id="layout_vars"><?=$layout_fields?></div>
			</div>
	
		</form>
	</div>
</div>