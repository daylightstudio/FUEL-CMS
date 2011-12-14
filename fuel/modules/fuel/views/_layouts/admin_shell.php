<?php 
// set some render variables based on what is in the panels array
$no_menu = (empty($panels['nav'])) ? TRUE : FALSE;
$no_titlebar = (empty($panels['titlebar'])) ? TRUE : FALSE;
$no_actions = (empty($panels['actions'])) ? TRUE : FALSE;
$no_notification = (empty($panels['notification'])) ? TRUE : FALSE;
?>

<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header');  ?>

<body>


<?php if (!empty($panels['top'])) : ?>
<!-- TOP MENU PANEL -->
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_top'); ?>
<?php endif; ?>


<div id="fuel_body">


	<?php if (!empty($panels['nav'])) : ?>
	<!-- LEFT MENU PANEL -->
	<?php $this->load->module_view(FUEL_FOLDER, '_blocks/nav'); ?>
	<?php endif; ?>

	
	<div id="fuel_main_panel<?=($no_menu) ? '_compact' : ''?>">
	
		<?=$this->form->open('action="'.$form_action.'" method="post" id="form" enctype="multipart/form-data"')?>
	

		<?php if (!empty($panels['titlebar'])) : ?>
		<!-- BREADCRUMB/TITLE BAR PANEL -->
		<?php $this->load->module_view(FUEL_FOLDER, '_blocks/titlebar')?>
		<?php endif; ?>
		
		
		<?php if (!empty($panels['actions'])) : ?>
		<!-- ACTION PANEL -->
		<div id="fuel_actions">
			<?php if (!empty($actions)) : ?>
			<?=$actions?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		
		<?php if (!empty($panels['notification'])) : ?>
		<!-- NOTIFICATION PANEL -->
		<div id="fuel_notification" class="notification">
			<?php if (!empty($notifications)) : ?>
			<?=$notifications?>
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
			<?php if (!empty($warning_window)) : ?>
				<!-- WARNING WINDOW -->
				<div class="warning jqmWindow jqmWindowShow" id="warning_window">
					<p><?=$warning_window?></p>

					<div class="buttonbar" id="yes_no_modal">
						<ul>
							<li class="end"><a href="#" class="ico ico_no" id="no_modal"><?=lang('btn_no')?></a></li>
							<li class="end"><a href="#" class="ico ico_yes" id="yes_modal"><?=lang('btn_yes')?></a></li>
						</ul>
					</div>
					<div class="clear"></div>
				</div>

			<?php endif; ?>


			<!-- BODY -->
			<?=$body?>
		</div>
		<?=$this->form->hidden('fuel_display_mode', $this->fuel->admin->display_mode())?>
		<?=$this->form->close()?>
	</div>
</div>


<?php if (!empty($panels['bottom'])) : ?>
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_bottom'); ?>
<?php endif; ?>


</body>
</html>