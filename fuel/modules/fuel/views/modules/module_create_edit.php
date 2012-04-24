<?php /* TODO... and for related items ?>
<?php $this->load->view('_blocks/related_items'); ?>
<?php */ ?>



<!-- NOTIFICATION EXTRA -->
<?php $this->load->view('_blocks/notification_extra'); ?>

<!-- WARNING WINDOW -->
<?php $this->load->view('_blocks/warning_window'); ?>


<div id="fuel_main_content_inner">

	<?php if (!empty($instructions)) : ?>
	<p class="instructions"><?=$instructions?></p>
	<?php endif; ?>

	<?=$form?>

</div>