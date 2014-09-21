<?php if (!empty($error)) :
	$error = str_replace(array('{script}', '{/script}'), array('<script>', '</script>'), $error); // convert script tags
	echo display_errors($error, 'ico error ico_error');
?>
<?php elseif ($this->session->flashdata('error') AND $this->session->flashdata('error') !== TRUE AND $this->session->flashdata('success') !== '1') : 
	$error = str_replace(array('{script}', '{/script}'), array('<script>', '</script>'), $this->session->flashdata('error')); // convert script tags
	echo display_errors($this->session->flashdata('error'), 'ico error ico_error');
?>
<?php elseif ($this->session->flashdata('success') AND $this->session->flashdata('success') !== TRUE AND $this->session->flashdata('success') !== '1') : 
$success = str_replace(array('{script}', '{/script}'), array('<script>', '</script>'), $this->session->flashdata('success')); // convert script tags

?>
	<div class="success ico ico_success"><?=$success;?></div>
<?php else: ?>
	<?php echo display_errors(NULL, 'ico error ico_error')?>
<?php endif; ?>

<?php if (!empty($last_updated)) : ?>
<p class="ico ico_info last_updated">
	<?=$last_updated?>
</p>
<?php elseif ($this->session->flashdata('info')) : ?>
<p class="ico ico_info">
	<?=$this->session->flashdata('info');?>
</p>
<?php elseif (!empty($info)) : ?>
<p class="ico ico_info"><?=$info?></p>
<?php endif; ?>