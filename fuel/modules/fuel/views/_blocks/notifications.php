<?php if (!empty($error)) :
	echo display_errors($error, 'error ico_error');
?>
<?php elseif ($this->session->flashdata('error') AND $this->session->flashdata('error') !== TRUE AND $this->session->flashdata('success') !== '1') : 
	echo display_errors($this->session->flashdata('error'), 'error ico_error');
?>
<?php elseif ($this->session->flashdata('success') AND $this->session->flashdata('success') !== TRUE AND $this->session->flashdata('success') !== '1') : ?>
	<div class="success ico_success"><?=$this->session->flashdata('success');?></div>
<?php else: ?>
	<?php echo display_errors(NULL, 'error ico_error')?>
<?php endif; ?>

<?php if (!empty($last_updated)) : ?>
<p class="ico ico_info last_updated">
	<?=$last_updated?>
</p>
<?php endif; ?>

