<?php 
// set some render variables based on what is in the panels array
$no_menu = (empty($panels['nav'])) ? TRUE : FALSE;
$no_breadcrumb = (empty($panels['breadcrumb'])) ? TRUE : FALSE;
$no_actions = (empty($panels['actions'])) ? TRUE : FALSE;
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


		<?php if (!empty($panels['breadcrumb'])) : ?>
		<!-- BREADCRUMB PANEL -->
		<?php $this->load->module_view(FUEL_FOLDER, '_blocks/breadcrumb')?>
		<?php endif; ?>
		
		
		<?php if (!empty($panels['actions'])) : ?>
		<!-- ACTION PANEL -->
		<?=$this->form->open(array('action' => fuel_url($this->module_uri.'/items'), 'method' => 'post', 'id' => 'form_actions'))?>
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
		if($no_actions AND $no_breadcrumb) :
			$main_content_class = 'noactions_nocrumb';
		elseif ($no_breadcrumb) :
			$main_content_class = 'nocrumb';
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
		<?=$this->form->close()?>
	</div>
</div>


<?php if (!empty($panels['bottom'])) : ?>
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_bottom'); ?>
<?php endif; ?>


</body>
</html>