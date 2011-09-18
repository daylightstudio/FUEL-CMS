<?php $this->load->view('_blocks/header')?>
	
	<?php /* RIGHT SIDE WITH SIDE MENU AND BLCOKS */ ?>
	<?php if (!empty($blocks) OR !empty($sidemenu)) : ?>
	<div id="right">
		
		<?php if (!empty($sidemenu)) : ?>
		<?php echo $sidemenu; ?>
		<?php endif ?>
		
		<?php if (!empty($blocks)) : ?>
		<div id="blocks">
			<?php foreach($blocks as $block) : ?>

				<div class="block">
				<?php echo fuel_block($block); ?>
				</div>

			<?php endforeach; ?>
		</div>
		<?php endif; ?>

	</div>
	<?php endif; ?>


	<div id="main_inner">
		<?php echo fuel_var('body', ''); ?>
	</div>
	
	
	<div class="clear"></div>
	
<?php $this->load->view('_blocks/footer')?>
