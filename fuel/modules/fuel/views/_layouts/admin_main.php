<?php 
// set some render variables based on what is in the panels array
$no_menu = (!$this->fuel->admin->has_panel('nav')) ? TRUE : FALSE;
$no_titlebar = (!$this->fuel->admin->has_panel('titlebar')) ? TRUE : FALSE;
$no_actions = (!$this->fuel->admin->has_panel('actions') OR empty($actions)) ? TRUE : FALSE;
$no_notification = (!$this->fuel->admin->has_panel('notification')) ? TRUE : FALSE;
?>

<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header');  ?>

<body>

<div id="fuel_body"<?=($this->fuel->admin->ui_cookie('leftnav_hide') === '1') ? ' class="nav_hide"' : ''; ?>>


	<?php if ($this->fuel->admin->has_panel('top')) : ?>
	<!-- TOP MENU PANEL -->
	<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_top'); ?>
	<?php endif; ?>

	<?php if ($this->fuel->admin->has_panel('nav')) : ?>
	<!-- LEFT MENU PANEL -->
	<?php $this->load->module_view(FUEL_FOLDER, '_blocks/nav'); ?>
	<?php endif; ?>

	
	<div id="fuel_main_panel<?=($no_menu) ? '_compact' : ''?>">
	
		<?php if ($this->fuel->admin->has_panel('titlebar')) : ?>
		<!-- BREADCRUMB/TITLE BAR PANEL -->
		<?php $this->load->module_view(FUEL_FOLDER, '_blocks/titlebar')?>
		<?php endif; ?>
		
		<?=$this->form->open('action="'.$form_action.'" method="'.((!empty($form_method)) ? $form_method : 'post').'" id="form" enctype="multipart/form-data"')?>
		
		<?php if ($this->fuel->admin->has_panel('actions') AND !empty($actions)) : ?>
		<!-- ACTION PANEL -->
		<div id="fuel_actions">

			<?php /* ?><?=$this->form->open('action="'.$form_action.'" method="post" id="form_actions" enctype="multipart/form-data"')?><?php */ ?>
			<?php if (!empty($actions)) : ?>
			<?=$actions?>
			<?php endif; ?>
			<?php /* ?><?=$this->form->close()?><?php */ ?>
			
		</div>
		<?php endif; ?>
		
		
		<?php if ($this->fuel->admin->has_panel('notification')) : ?>
		<!-- NOTIFICATION PANEL -->
		<div id="fuel_notification" class="notification">
			<?php if (!empty($notifications)) : ?>
			<?=$notifications?>
			<?php endif; ?>
			<?php if (!empty($pagination)): ?>
			<div id="pagination"><?=$pagination?></div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		
		<?php 
		$main_content_class = '';
		if($no_actions AND $no_notification) :
			$main_content_class = 'noactions_nonotification';
		elseif($no_actions AND $no_titlebar) :
			$main_content_class = 'noactions_notitlebar';
		elseif ($no_titlebar) :
			$main_content_class = 'notitlebar';
		elseif ($no_actions) :
			$main_content_class = 'noactions';
		endif;
		 ?>
		
		
		<div id="fuel_main_content<?=($no_menu) ? '_compact' : ''?>"<?=(!empty($main_content_class)) ? ' class="'.$main_content_class.'"' : ''?>>
			
			<?php /* ?><?=$this->form->open('action="'.$form_action.'" method="post" id="form" enctype="multipart/form-data"')?><?php */ ?>

			<!-- BODY -->
			<?=$body?>
			
			<?=$this->form->hidden('fuel_inline', (int)$this->fuel->admin->is_inline())?>
		</div>
		<?=$this->form->close()?>
			
	</div>
</div>

<div id="fuel_modal" class="jqmWindow"></div>

<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_footer');  ?>

</body>
</html>